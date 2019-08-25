<?php

require "include/config.inc.php";

	if (get_magic_quotes_gpc())
	{ // Yes? Strip the added slashes - we will call quote where we need it	
		global $HTTP_REQUEST;
		//	$HTTP_REQUEST = array_map('stripslashes', $HTTP_REQUEST);
		$_GET = array_map('stripslashes', $_GET);
		$_POST = array_map('stripslashes', $_POST);
		$_COOKIE = array_map('stripslashes', $_COOKIE);
	}
	
@mysql_pconnect($DB_HOST, $DB_USER, $DB_PASSWORD);
if(mysql_error())
{
	echo "$URL<hr>We are very sorry, but the database server is currently down. Please try to access us again later.";
	echo "<br>Sincerely,";
	echo "<br>your $TITLE team: $CONTACT";
	exit;
}
mysql_select_db($DB_DATABASE);	
if(mysql_error())
{
	echo "<hr>Invalid config. $DB_DATABASE not found on $DB_HOST";
	exit;
}

function query($query)
{ // run query and notify errors
  //	echo $query."<br>";
	$result=mysql_query($query);
	if(mysql_errno() != 0)
		{
		echo mysql_errno()." ".mysql_error()."<br>";
		echo $query."<br>";
		}
	return $result;
}

function quote($str) 
{ // quote argument for sql queries 
	return "'".addslashes($str)."'"; 
}

?>