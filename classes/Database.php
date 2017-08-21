<?php
 
class Database {
    private $_config;       // Stores the configuration provided by user.
    private $_query;        // Stores the current query.
    private $_error;        // Stores an error based on $_verbose.
    public $_verbose;       // Stores a boolean determining output / storage of errors.

    private $_buildQuery;   // Stores the current "in progress" query build.

    
    public function __construct($config) 
    {
        $this->_config = $config;
    }

    
    // Initializes the database. Checks the configuration, connects, selects database.
    public function init() 
    {
        if (!$this->__check_config()) {
            return false;
        }
        if (!$this->__connect()) {
            return false;
        }

        if (!$this->__select_db()) {
            return false;
        }

        return true;
    }


    private function __check_config() 
    {
        $config = $this->_config;

        if (empty($config["server"]) || empty($config["username"]) || empty($config["database"])) {
            $this->_error = "Configuration details were blank.";
            return false;
        }

        $this->_verbose = ($config["verbose"]) ? true : false;

        return true;
    }


    private function __connect() 
    {
    	
        $connection = mysql_connect($this->_config["server"], $this->_config["username"], $this->_config["password"]);
    	
        if (!$connection) {
            $this->_error = ($this->_verbose) ? mysql_error() : "Could not connect to database.";
            return false;
        }
        
        return true;
    }

    
    private function __select_db() {
        $database = mysql_select_db($this->_config["database"]);

        if(!$database) {
            $this->_error = ($this->_verbose) ? mysql_error() : "Could not select database.";
            return false;
        }

        return true;
    }
}

?>