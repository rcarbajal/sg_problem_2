<?php
import("models.base.DataObject");
import("exceptions.DatabaseException");
import("exceptions.RecordNotFoundException");
import("exceptions.DataException");

class Player extends DataObject {
	public $name;
	public $credits;
	public $lifetimeSpins;
	public $salt;
	
	/*************************
	 * PUBLIC MEMBER METHODS *
	 *************************/
	 
	public function __construct($pid = null) {
		$this->Clear();
		
		//if we have a valid ID, load data
		if(isset($pid)) {
			if(is_numeric($pid)) {
				$this->id = $pid;
				$this->Load();
			} //end if
			else throw new InvalidArgumentException(__METHOD__ . "::Invalid player ID specified. Line: " . __LINE__);
		} //end if
	} //end method __construct
	
	public function Delete() {
		//begin transaction
		$internalTrans = false;
		if(!self::InTransaction()) {
			$internalTrans = true;
			self::begin();
		} //end if
		
		//create query string
		$query = "DELETE FROM tblplayerdata WHERE player_id = :pid";
		
		//prepare query
		$sth = self::prepare($query);
		if(!$sth)
			throw new DatabaseException(__METHOD__ . "::Error preparing Player item data delete query. Query: $query; Error: " . self::get_error_str($sth) . "; Line: " . __LINE__);
		
		//bind params
		$sth->bindParam(':pid', $this->id, PDO::PARAM_INT);
		
		//execute query
		if(!$sth->execute()) {
			if($internalTrans) self::rollback();
			throw new DatabaseException(__METHOD__ . "::Error executing Player item data delete query. Query: $query; Error: " . self::get_error_str($sth) . "; Line: " . __LINE__);
		} //end if
		
		//commit transaction
		if($internalTrans) self::commit();
		
		//release DB resources
		$sth->closeCursor();
		unset($sth);
		
		//reset our object
		$this->Clear();
	} //end method Delete
	
	/****************************
	 * PROTECTED MEMBER METHODS *
	 ****************************/
	protected function Clear() {
		parent::Clear();
		$this->name = "";
		$this->credits = "";
		$this->lifetimeSpins = "";
		$this->salt = "";
	} //end method Clear
	
	protected function Insert() {
		//validate given ID
		if($this->id > 0)
			throw new DataException(__METHOD__ . "::Error: object supplied that already has a valid identifier. Line: " . __LINE__);
		
		//begin transaction
		$internalTrans = false;
		if(!self::InTransaction()) {
			$internalTrans = true;
			self::begin();
		} //end if
		
		//create query string
		$query = <<<SQL
INSERT INTO tblplayerdata
	(name, credits, lifetime_spins, salt_val, ts_updated)
VALUES
	(:name, :credits, :spins, :salt, NOW())
SQL;

		//prepare query
		$sth = self::prepare($query);
		if(!$sth) {
			if($internalTrans) self::rollback();
			throw new DatabaseException(__METHOD__ . "::Error preparing Player item data insert query. Query: $query; Error: " . self::get_error_str($sth) . "; Line: " . __LINE__);
		} //end if
		
		//bind params
		$sth->bindParam(':name', $this->name, PDO::PARAM_STR);
		$sth->bindParam(':credits', $this->credits, PDO::PARAM_INT);
		$sth->bindParam(':spins', $this->lifetimeSpins, PDO::PARAM_INT);
		$sth->bindParam(':salt', $this->salt, PDO::PARAM_STR);
		
		//execute query
		if(!$sth->execute()) {
			if($internalTrans) self::rollback();
			throw new DatabaseException(__METHOD__ . "::Error executing Player item data insert query. Query: $query; Error: " . self::get_error_str($sth) . "; Line: " . __LINE__);
		} //end if
		
		//release DB resources
		$sth->closeCursor();
		unset($sth);
		
		//commit transaction
		if($internalTrans) self::commit();
	} //end method Insert
	
	protected function Update() {
		//validate given ID
		if($this->id < 1)
			throw new DataException(__METHOD__ . "::Error: object does not have a valid identifier. Line: " . __LINE__);
		
		//begin transaction
		$internalTrans = false;
		if(!self::InTransaction()) {
			$internalTrans = true;
			self::begin();
		} //end if
		
		//create query string
		$query = <<<SQL
UPDATE tblplayerdata SET
	name = :name,
	credits = :credits,
	lifetime_spins = :spins,
	salt_val = :salt
	ts_updated = NOW()
WHERE player_id = :pid
SQL;

		//prepare query
		$sth = self::prepare($sth);
		if(!$sth) {
			if($internalTrans) self::rollback();
			throw new DatabaseException(__METHOD__ . "::Error preparing Player item data update query. Query: $query; Error: " . self::get_error_str($sth) . "; Line: " . __LINE__);
		} //end if
		
		//bind params
		$sth->bindParam(':name', $this->name, PDO::PARAM_STR);
		$sth->bindParam(':credits', $this->credits, PDO::PARAM_INT);
		$sth->bindParam(':spins', $this->lifetimeSpins, PDO::PARAM_INT);
		$sth->bindParam(':salt', $this->salt, PDO::PARAM_STR);
		$sth->bindParam(':pid', $this->id, PDO::PARAM_INT);
		
		//execute query
		if(!$sth->execute()) {
			if($internalTrans) self::rollback();
			throw new DatabaseException(__METHOD__ . "Error executing Player item data update query. Query: $query; Error: " . self::get_error_str($sth) . "; Line: " . __LINE__);
		} //end if
		
		//release DB resources
		$sth->closeCursor();
		unset($sth);
		
		//commit transaction
		if($internalTrans)
			self::commit();
	} //end method Update
	
	/**************************
	 * PRIVATE MEMBER METHODS *
	 **************************/
	 
	private function Load() {
		//create query string
		$query = "SELECT name, credits, lifetime_spins, salt_val, ts_created, ts_updated FROM tblplayerdata WHERE player_id = :pid";
		
		//prepare query
		$sth = self::prepare($query);
		if(!$sth)
			throw new DatabaseException(__METHOD__ . "::Error preparing Player item data retrieval query. Query: $query; Error: " . self::get_error_str($sth) . "; Line: " . __LINE__);
		
		//bind params
		$sth->bindParam(':pid', $this->id, PDO::PARAM_INT);
		
		//execute query
		if(!$sth->execute())
			throw new DatabaseException(__METHOD__ . "::Error executing Player item data retrieval query. Query: $query; Error: " . self::get_error_str($sth) . "; Line: " . __LINE__);
		
		//bind columns
		$sth->bindColumn('name', $this->name);
		$sth->bindColumn('credits', $this->credits);
		$sth->bindColumn('lifetime_spins', $this->lifetimeSpins);
		$sth->bindColumn('salt_val', $this->salt);
		$sth->bindColumn('ts_created', $this->created);
		$sth->bindColumn('ts_updated', $this->updated);
		
		//fetch bound columns
		$sth->fetch(PDO::FETCH_BOUND);
		if(!isset($this->name) || $this->name === "")
			throw new RecordNotFoundException(__METHOD__ . "::Record not found for given ID: {$this->id}; Line: " . __LINE__);
		
		//release DB resources
		$sth->closeCursor();
		unset($sth);
	} //end method Load
} //end class Player
?>