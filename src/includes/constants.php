<?php 
// Debugging
define("DEBUG", 0);

// Site Root, Doc Root
define('SITEROOT','/');
define('DOCROOT', $_SERVER['DOCUMENT_ROOT'] . SITEROOT);
define("LIBPATH", DOCROOT . "lib/");

// File part includes
define("DBCONNECT", DOCROOT . "includes/dbconnect.php");

/**
 * Name: import
 * Purpose: Parse a string as a library path for including files in other PHP files
 * Arguments:
 *  - $libString: string to parse as import library
 * Returns: void
 */
$_IMPORT_ARRAY = array();
function import($libString = "") {
	if(!is_string($libString) || $libString == "") {
		error_log('FATAL ERROR: Specified class file path to must be a non-empty string!');
		printf("%s", "An unrecoverable error has occurred.  Please try again.  If problem persists, please contact the webmaster.");
		exit;
	} //end if
	
	global $_IMPORT_ARRAY;
	if(!isset($_IMPORT_ARRAY[$libString]) || !$_IMPORT_ARRAY[$libString]) {
		$_IMPORT_ARRAY[$libString] = 1;
		
		$importPathArray = explode(".", $libString);
		$filePath = LIBPATH . implode("/", $importPathArray) . ".php";
		if(file_exists($filePath))
			require_once($filePath);
	} //end if
	
	return;
} //end function import()
?>