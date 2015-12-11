<?php
import("data.MySQLConn");
import("lib.Logger");
import("exceptions.DatabaseException");

/**********************************************/
/* - create database connection               */
/**********************************************/

//attempt to create connection to database if necessary
try {
	$retval = MySQLConn::connect('sg_problem_2', null, 'root', 'c00kie');
	if(!$retval) Logger::write(Logger::FATAL, "Error - database failed to connect.");
} //end TRY
catch(DatabaseException $e) {
	Logger::write(Logger::ERROR, "Error connecting to database.", $e);
} //end CATCH
?>