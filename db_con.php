<?php
define("HOSTNAME", "localhost");
define("USERNAME", "root");
define("PASSWORD", "");
define("DATABASE", "lab_project");
$connection = mysqli_connect(HOSTNAME, USERNAME, PASSWORD, DATABASE);
if(!$connection)
{
	die("connection failed". mysqli_connect_error());
}

?>


