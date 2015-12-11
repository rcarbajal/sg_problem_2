<?php
import("exceptions.DatabaseException");

/*
 * Purely static class, set up so objects inheriting class can easily
 * access a single database resource. Classes should extend this class.
 * Ex: class PNM_DATA extends Db {}
 */
class MySQLConn {
    // Description: MySQL Connection Resource
    protected static $_conn = null;
	protected static $_transaction = false;
    
    /* Function name: status
     * Availability: public
     * Parameters: none
     * Returns: true/false
     * Description: returns true if the connection is ok, false if not
     */
    public static function status() {
        if(isset(MySQLConn::$_conn) && MySQLConn::$_conn->getAttribute(PDO::ATTR_CONNECTION_STATUS)) {
            return true;
        } else {
            return false;
        }
    }
    
    /* Function name: connect
     * Availability: public
     * Parameters:  $dbname = name of database to connect to
     *              $username = name of account to auth with
     *              $password = password of account to auth with
     *              $host = host to connect to
     * Returns: true/false
     * Description: returns true if connection established successfully.
     *              this function will return false if the connection is live.
     */
    public static function connect($dbname = null,$host = null, $username = null, $password = null) {
        if(MySQLConn::status()) {
            return false;
        }
        if(isset($dbname) && isset($username) && isset($password)) {
            // Attempt to connect with supplied credentials
            try {
                $t_conn_str = "";
                if(isset($host)) {
                    $t_conn_str = "dbname=$dbname;host=$host";
                } else {
                    $t_conn_str = "dbname=$dbname;";
                }

                MySQLConn::$_conn = new PDO("mysql:$t_conn_str", $username, $password, array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
                if(! MySQLConn::$_conn->getAttribute(PDO::ATTR_CONNECTION_STATUS)) {
                    MySQLConn::$_conn = null;
                    throw new DatabaseException("Connection string failed: $t_conn_str");
                }

            } catch (PDOException $e) {
                throw new DatabaseException("Error connecting to database: " . $e->getMessage());
                die();
            }
		    return true;
        } else {
		    return false;
		}
    }
    
    /*
     * Function name: query
     * Availability: public
     * Parameters: string
     * Returns: PDO::PDOStatement
     * Description: Executes the passed query, returns the PDO::PDOStatement
     *              or false if the database handle is unavailable
     */
    // FIXME - needs error handling
    public static function query($str) {
        if(MySQLConn::status()) {
            return MySQLConn::$_conn->query($str);
        } else {
            throw new DatabaseException(__METHOD__."::Database not connected!");
        }
    }
    
    /*
     * Function name: prepare
     * Availability: public
     * Parameters: string
     * Returns: PDO::PDOStatement
     * Description: Prepares the passed query, returns the PDO::PDOStatement
     *              or false if the database handle is unavailable
     */
    // FIXME - needs error handling
    public static function prepare($str) {
        if(MySQLConn::status()) {
            return MySQLConn::$_conn->prepare($str);
        } else {
            throw new DatabaseException(__METHOD__."::Database not connected!");
        }
    }
    
    /*
     * Function name: last_insert_id
     * Availability: public
     * Parameters: none
     * Returns: int
     * Description: Returns last generated id if it exists
     */
    // FIXME - needs error handling
    public static function last_insert_id($sequence = 'id_seq') {
        if(MySQLConn::status()) {
		return MySQLConn::$_conn->lastInsertId();
        } else {
            throw new DatabaseException(__METHOD__."::Database not connected!");
        }
    }
    
    /* Function name: get_error_str
     * Availability: public
     * Parameters: none
     * Returns: string
     * Description: returns the string describing the last error on the PDO object
     */
    public static function get_error_str($stmt = null) {
	if(isset($stmt))
		return implode("|", $stmt->errorInfo());
        return implode("|",MySQLConn::$_conn->errorInfo());
    }
    
    /* Function name: get_error_code
     * Availability: public
     * Parameters: none
     * Returns: int
     * Description: returns the code describing the last error on the PDO object
     */
    public static function get_error_code() {
        return MySQLConn::$_conn->errorCode();
    }

    
    /*
     * Function name: disconnect
     * Availability: public
     * Parameters: none
     * Returns: none
     * Description: closes connection to MySQL
     */
    public static function disconnect() {
        MySQLConn::$_conn = null;
    }
    
    /*
     * Function name: begin
     * Availability: public
     * Parameters: none
     * Returns: none
     * Description: begins a transaction
     */
    public static function begin() {
        if(MySQLConn::status()) {
        	MySQLConn::$_conn->beginTransaction();
			MySQLConn::$_transaction = true;
        } else {
            throw new DatabaseException(__METHOD__."::Database not connected!");
        }
    }
    
    /*
     * Function name: rollback
     * Availability: public
     * Parameters: none
     * Returns: none
     * Description: rolls back a transaction
     */
    public static function rollback() {
		MySQLConn::$_transaction = false;
		
        if(MySQLConn::status()) {
        	MySQLConn::$_conn->rollBack();
        } else {
            throw new DatabaseException(__METHOD__."::Database not connected!");
        }
    }
    
    /*
     * Function name: commit
     * Availability: public
     * Parameters: none
     * Returns: none
     * Description: commits a database transaction
     */
    public static function commit() {
		MySQLConn::$_transaction = false;
		
        if(MySQLConn::status()) {
        	MySQLConn::$_conn->commit();
        } else {
            throw new DatabaseException(__METHOD__."::Database not connected!");
        }
    }
    
    /*
     * Function name: debug
     * Availability: public
     * Parameters: none
     * Returns: none
     * Description: dumps this object, inheritable
     */
    public function debug() {
        var_dump($this);
    }
	
	/**
	 * Function name: InTransaction
	 * Availability: public
	 * Parameters: none
	 * Returns: transaction status
	 * Description: if the database connection is currently in a transaction,
	 * returns true.
	 */
	public static function InTransaction() {
		return MySQLConn::$_transaction;
	}

}
?>
