<?php 

class DBConn {    
    
    var $credentials;
    
    function __construct( $servername, $database, $username, $password ) {
        
        $this->credentials = array(
                            'server' => $servername,
                            'database' => $database,
                            'username' => $username,
                            'password' => $password
                            );
        
        return $this->connect();
    }
    
    function connect() {
    
        // Create connection
        $conn = new mysqli( $this->credentials['server'], 
                            $this->credentials['username'], 
                            $this->credentials['password'],
                            $this->credentials['database'] );

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }

}