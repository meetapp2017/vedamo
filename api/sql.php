<?php
class Sql
{
    protected $conn;

    function __construct()
    {
        $this->init();
    }

    private function init()
    {

        $db_host = "localhost"; // Database Host 
        $db_user = "root"; // Database User 
        $db_password = ""; // Database password 
        $db_name = "vedamo"; // Database name 

        $this->conn = @mysqli_connect($db_host, $db_user, $db_password, $db_name) or die("Fatal MySQL Error");

        $this->conn->set_charset("utf8mb4");

        if ($this->conn === false) {
            return mysqli_connect_error();
        }
    }
}
