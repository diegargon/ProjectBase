<?php


function db_connect($dbcfg) {
    global $dblink;
    $dblink = mysqli_connect("$dbcfg[database_host]","$dbcfg[database_user]","$dbcfg[database_password]", "$dbcfg[database]");

    db_query("SET NAMES '$dbcfg[dbcharset]'");
    
    if (mysqli_connect_errno($dblink)) {
        echo "Failed to connect to MySQL: " . db_connect_error();
    }
    return true;
}

function db_query($string) {
	global $dblink;
	$query = mysqli_query($dblink, $string) or db_die($query);
	return $query;
}

function db_fetch($query) {
	$row = mysqli_fetch_assoc($query);
	return $row;
}

function db_escape_string($var) {
	global $dblink;
	return mysqli_real_escape_string($dblink, $var);
}



function db_connect_error() {
	mysqli_connect_error();
}

function db_num_rows($query) {
	return mysqli_num_rows($query);
}

function db_close() {
    global $dblink;
	if (!$dblink) {
	    die('Could not connect: ' . db_error());
	}
	mysqli_close($dblink);
}

function db_die($query) {

   global $dblink;
   echo "\n<b>Error: Unable to retrieve information.</b>";
   echo "\n<br>$query";
   echo "\n<br>reported: ".mysqli_error($dblink);
   exit;
}

function db_insert_id() {
    global $dblink;
	if (!$dblink) {
	    die('Could not connect: ' . mysql_error());
	}
	return mysqli_insert_id($dblink);
}

function db_free_result($query) {
    mysqli_free_result($query);
}

