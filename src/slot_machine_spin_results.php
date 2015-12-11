<?php
include("includes/constants.php");
require_once(DBCONNECT);

import("models.Player");
import("utilities.Logger");
import("utilities.Utils");

$res = "";

//process player spin data
try {
	//retrieve data from POST
	$hash = isset($_POST['hash']) ? Utils::sanitizeInput($_POST['hash']) : "";
	$coinsWon = isset($_POST['coins_won']) && is_numeric($_POST['coins_won']) ? intval(Utils::sanitizeInput($_POST['coins_won'])) : 0;
	$coinsBet = isset($_POST['coins_bet']) && is_numeric($_POST['coins_bet']) ? intval(Utils::sanitizeInput($_POST['coins_bet'])) : 0;
	$playerId = isset($_POST['player_id']) && is_numeric($_POST['player_id']) ? intval(Utils::sanitizeInput($_POST['player_id'])) : 0;
	
	//validate data
	$isDataValid = true;
	$isDataValid &= $hash !== ""; //make sure hash is non-empty
	$isDataValid &= $coinsWon >= 0; //coins won must be an integer greater than or equal to 0
	$isDataValid &= $coinsBet >= 0; //coins bet must be an integer greater than or equal to 0
	$isDataValid &= $playerId > 0; //player ID must be an integer greater than 0
	
	if($isDataValid) {
		//get current player data
		$player = new Player($playerId);
		
		//only update if the specified hash is authenticated
		if($hash == $player->hash) {
			$netCredits = $coinsWon - $coinsBet;
			$player->credits += $netCredits;
			$player->lifetimeSpins += 1;
			$avgReturn = number_format(($player->credits / $player->lifetimeSpins), 2);
			
			//update player data
			$player->Save();
			
			//set return data
			$res = array(
				"ResponseCode" => 200,
				"PlayerID" => $player->id,
				"Name" => $player->name,
				"Credits" => $player->credits,
				"LifetimeSpins" => $player->lifetimeSpins,
				"LifetimeAverageReturn" => $avgReturn
			);
		} //end if
		else {
			$res = array(
				"ResponseCode" => 400,
				"ErrorMessage" => "Invalid player spin data specified."
			);
		} //end else
	} //end if
	else {
		$res = array(
			"ResponseCode" => 400,
			"ErrorMessage" => "Invalid player spin data specified."
		);
	} //end else
} //end try
catch(RecordNotFoundException $e) {
	Logger::write(Logger::ERROR, "Error saving player spin data.", $e);
	Logger::write(Logger::DEBUG, $e->getTraceAsString());
	$res = array(
		"ResponseCode" => 400,
		"ErrorMessage" => "Invalid player spin data specified."
	);
} //end catch
catch(Exception $e) {
	Logger::write(Logger::ERROR, "Error saving player spin data.", $e);
	Logger::write(Logger::DEBUG, $e->getTraceAsString());
	$res = array(
		"ResponseCode" => 500,
		"ErrorMessage" => "Error occurred on the server."
	);
} //end catch

//echo back JSON data
header("Content-type: application/json");
echo json_encode($res);
?>