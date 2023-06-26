<?php
/*
* Change the value of $password if you have set a password on the root userid
* Change NULL to port number to use DBMS other than the default using port 3306
*
*/
define('baseURL', "http://localhost/ecommerceP3");
define('baseDIR', "C:\wamp64\www\\ecommerceP3");

$user = 'root';
$password = ''; //To be completed if you have set a password to root
$database = 'ecommerceP3'; //To be completed to connect to a database. The database must exist.
$port = NULL; //Default must be NULL to use default port
// GLOBALS $mysqli;
$mysqli = new mysqli('127.0.0.1', $user, $password, $database, $port);

if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
    // echo "error";
}
else{
    // echo "no error";
    // $sql = "SELECT slno, email FROM registered_users WHERE email = '".$_REQUEST['email']."' ";
    // $mysqli->connect();
}

?>