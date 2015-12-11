<?php
import("data.MySQLConn");

/**
 * Class DataObject
 *
 * This is an abstract class from which all of the ORM classes will inherit. It
 * specifies three members and two methods which all ORM classes must
 * implement and use. It also inherits the Db class, so all subclasses will
 * also have the static members of the Db class available. A Clear() method is
 * provided that will reset the defined members to defaults.
 *
 * Note that created and updated are DateTime objects, and will by default be
 * set to default DateTime objects at which point their validity is not well
 * defined.
 */

abstract class DataObject extends MySQLConn {
	
	protected $id = 0;
	public $created = null; // datetime as a string
	public $updated = null; // datetime as a string

	/*************************
	 * PUBLIC MEMBER METHODS *
	 *************************/

	public function __get($var) {
		return $this->$var;
	} //end method __get
	
	/**
	 * Method: Default Constructor
	 * Parameters: None
	 * Visibility: public
	 * Returns: none
	 * Description: Default constructor - no actions for this abstract class
	 * Throws: Exceptions thrown based on actions in derived classes
	*/
	public function __construct() { }
	
	/**
	 * Method: Save
	 * Parameters: None
	 * Visibility: public
	 * Returns: success
	 * Description: Calls and returns value from Insert() if there isn't a valid
	 * 		fileid, else it uses Update() - may be overridden
	 * Throws: DatabaseException: see implementations for Insert()/Update()
	 * 	RecordNotFoundException: see implementations for Insert()/Update()
	*/
	public function Save() {
		if($this->id > 0) {
			return $this->Update();
		} else {
			return $this->Insert();
		}
	}

	/**
	 * Method: Delete
	 * Parameters: None
	 * Visibility: protected
	 * Returns: success
	 * Description: Implementation will remove records in the database associated
	 * 		with this object, as well as reset the object using Clear()
	 * Throws: DatabaseException: error executing SQL commands
	 * 	RecordNotFoundException: the base record for this object was not found
	 * 		or updated
	*/
	public abstract function Delete();
	

	/****************************
	 * PROTECTED MEMBER METHODS *
	 ****************************/

	/**
	 * Method: Clear
	 * Parameters: None
	 * Visibility: protected
	 * Returns: none
	 * Description: Resets all members associated with object
	 * Throws: none
	*/
	protected function Clear() {
		$this->id = 0;
		$this->created = "N/A";
		$this->updated = "N/A";
	}


	/**
	 * Method: Insert
	 * Parameters: None
	 * Visibility: protected
	 * Returns: success
	 * Description: Implementation will create new records in the database to
	 * 		store data for this object
	 * Throws: DatabaseException: error executing SQL commands or assuring that
	 * 		data was stored
	*/
	protected abstract function Insert();


	/**
	 * Method: Update
	 * Parameters: None
	 * Visibility: protected
	 * Returns: success
	 * Description: Implementation will update records in the database to
	 * 		store changes for this object
	 * Throws: DatabaseException: error executing SQL commands
	 * 	RecordNotFoundException: the base record for this object was not found
	 * 		or updated
	*/
	protected abstract function Update();
} //end class DataObject


?>