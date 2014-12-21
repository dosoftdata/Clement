<?php

/**
 * @author : CMU
 * @tutorial:Handle all database activities(CRUD) based on ZF2 database adaptor'  
 */

namespace Php247\Db;

// Class providing generic data access functionality

use Zend\Db\Adapter\Adapter as AgDbConnection,
    Zend\Db\ResultSet\ResultSet as agCurrencyRowset;

// Class providing generic data access functionality
class DatabaseHandler {

    // Hold an instance of the PDO class
    private static $_mHandler;
    private static $_rOwset;

    // Private constructor to prevent direct creation of object
    /**
     * DatabaseHandler::__construct()
     * 
     * @return
     */
    private function __construct() {
        
    }

    /**
     * DatabaseHandler::GetRowSet()
     * 
     * @return
     */
    final private static function GetRowSet() {
        // Create PDO Fetch mode if not exist
        if (!isset(self::$_rOwset)) {
            // Execute code catching potential exceptions
            try {
                // Create a new Fetch instance
                self::$_rOwset = new agCurrencyRowset();
            } catch (\Zend_Exception $e) {
                $e->getMessage();
            }
        }
        // Return fetch mode
        return self::$_rOwset;
    }

    // Return an initialized database handler
    /**
     * DatabaseHandler::GetHandler()
     * 
     * @return
     */
    private static function GetHandler() {
        // Create a database connection only if one doesn’t already exist
        if (!isset(self::$_mHandler)) {
            // Execute code catching potential exceptions
            try {
                // Create a new PDO class instance
                self::$_mHandler = new AgDbConnection(array(
                    'driver' => DB_DRIVER,
                    'hostname' => DB_SERVER,
                    'database' => DB_DATABASE,
                    'username' => DB_USERNAME,
                    'password' => DB_PASSWORD,
                        //'driver_options'=>array( new \Zend\Db\Adapter\Driver\Pdo::MYSQL_ATTR_USE_BUFFERED_QUERY=>true)
                ));
                //self::$_mHandler->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
                //self::$_mHandler->getConnection();
            } catch (\Zend_Exception $e) {
                // Close the database handler and trigger an error
                self::Close();
                $e->getMessage();
            }
        }
        // Return the database handler
        return self::$_mHandler;
    }

    // Clear the PDO class instance
    /**
     * DatabaseHandler::Close()
     * 
     * @return
     */
    public static function Close() {
        self::$_mHandler = null;
    }

    // Wrapper method for PDOStatement::fetchAll()
    /**
     * DatabaseHandler::GetAll()
     * 
     * @return
     */
    final public static function GetAll($sqlQuery, $params = null) {
        // Initialize the return value to null
        $result = null;
        // Try to execute an SQL query or a stored procedure
        try {
            // Get the database handler
            $database_handler = self::GetHandler();
            //Create Zend db statement
            $createStatment = $database_handler->createStatement($sqlQuery, $params);
            // Prepare the query for execution
            $statement_execute = $createStatment->execute();
            $rowset = self::GetRowSet();
            $result = $rowset->initialize($statement_execute)
                    ->toArray();
            //->closeCursor();
        }
        // Trigger an error if an exception was thrown when executing the SQL query
        catch (\Zend_Exception $e) {
            // Close the database handler and trigger an error
            self::Close();
            $e->getMessage();
        }
        // Return the query results
        return $result;
    }

    // Return the number of all rows value in the table
    /**
     * DatabaseHandler::RowNum()
     * 
     * @return
     */
    final public static function RowNum($sqlQuery, $params = null) {
        // Initialize the return value to null
        $result = null;
        // Try to execute an SQL query or a stored procedure
        try {
            // Get the database handler
            $database_handler = self::GetHandler();
            //Create Zend db statement
            $createStatment = $database_handler->createStatement($sqlQuery, $params);
            //  the query execution
            $statement_execute = $createStatment->execute();
            $result = $statement_execute->count();
        }
        // Trigger an error if an exception was thrown when executing the SQL query
        catch (\Zend_Exception $e) {
            // Close the database handler and trigger an error
            self::Close();
            $e->getMessage();
        }
        // Return the query results
        return $result;
    }

    // Wrapper method for PDOStatement::execute()
    /**
     * DatabaseHandler::Execute()
     * 
     * @return
     */
    final public static function Execute($sqlQuery, $params = null) {
        // Try to execute an SQL query or a stored procedure
        try {
            // Get the database handler
            $database_handler = self::GetHandler();
            //Create Zend db statement
            $createStatment = $database_handler->createStatement($sqlQuery, $params);
            //  the query execution
            $statement_execute = $createStatment->execute();
        }
        // Trigger an error if an exception was thrown when executing the SQL query
        catch (\Zend_Exception $e) {
            // Close the database handler and trigger an error
            self::Close();
            $e->getMessage();
        }
    }

}
