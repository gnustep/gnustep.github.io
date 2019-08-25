<?php

// read from cookies - if they are set

global $login_user;
global $login_code;
// echo "cookies: ".$_COOKIE;
$login_user=$_COOKIE['login_user'];
$login_code=$_COOKIE['login_code'];

if($_COOKIE['user'])
{ // try direct login with user=email&code=md5(password)
	$query="select * from ${DB_TABLE}_users where email=".quote($user)." and password=".quote($_COOKIE['code'])." and enabled=1";
	//	echo $query;
	$response=@mysql_query($query);
	if($response)
		{
		$res=mysql_fetch_array($response);
		mysql_free_result($response);
		if($res)
			{
			$duration=3600;
			$login_user=$user;
			$login_code=md5($user.$_COOKIE['code']);
			setcookie("login_user", $login_user, time()+$duration);
			setcookie("login_code", $login_code, time()+$duration);
			}
		}
}

function autologin()
{
	global $DB_TABLE;
	global $login_user;
	$query="select * from ${DB_TABLE}_users where email=".quote($login_user)." and enabled=1";
	$response=@mysql_query($query);
	if($response)
		{
		$res=mysql_fetch_array($response);
		mysql_free_result($response);
		if($res)
			return "user=".rawurlencode($login_user)."&code=".rawurlencode($res['password']);
		}
	return "";
}

function loginname()
{
	global $login_user;
	return $login_user;
}

function dologout()
	{ // delete cookies
	global $login_user;
	global $login_code;
	setcookie("login_user", "", time()-600000);
	setcookie("login_code", "", time()-600000);
	$login_user="";
	$login_code="";
	}

// FIXME: 
// should we save the password in cleartext or md5 in database?
// and cleartext or md5 in cookie?
// login_code should be e.g. md5(email.password)

function dologin($user, $password, $duration=3600)
	{
	global $DB_TABLE;
	global $login_user;
	global $login_code;
	$query="select * from ${DB_TABLE}_users where email=".quote($user)." and password=md5(".quote($password).") and enabled=1";
//	echo $query;
	$response=@mysql_query($query);
	if(!$response)
		return false;	// error
	$res=mysql_fetch_array($response);
	mysql_free_result($response);
	if(!$res)
		return false;	// no result
	$login_user=$user;
	$login_code=md5($user.md5($password));
	setcookie("login_user", $login_user, time()+$duration);
	setcookie("login_code", $login_code, time()+$duration);
	return true;
	}

function createuser($user, $password)
{
	global $DB_TABLE;
	// needs a unique/primary key on the `email` field
	$query="replace into ${DB_TABLE}_users set email=".quote($user).", password=md5(".quote($password)."), registered=now(), enabled=0";
	query($query);
	return md5(md5($password).$user."x");
}

function enableuser($user, $code)
{
	global $DB_TABLE;
	$query="update ${DB_TABLE}_users set enabled=1 where email=".quote($user)." and ".quote($code)."=md5(concat(password, email,'x'))";
//	echo $query;
	query($query);
}

function disableuser($user)
{
	global $DB_TABLE;
	$query="delete from ${DB_TABLE}_users where email=".quote($user);
	//	echo $query;
	query($query);
}
	
function isloggedin()
{
	global $login_user;
	global $login_code;
	return $login_user != "";
}

function checkpermission($permission="")
	{ // check if we have this permission
	global $DB_TABLE;
	global $login_user;
	global $login_code;
	if(!isloggedin())
		return false;
	// query database for user and password
	$query="select permissions from ${DB_TABLE}_users where email=".quote($login_user)." and ".quote($login_code)."=md5(concat(email,password)) and enabled=1";
	$response=@mysql_query($query);
//	echo $response;
	if(!$response)
		return false;	// error
	$res=mysql_fetch_array($response);
	mysql_free_result($response);
//	echo $res;
	foreach(explode(",", $res['permissions']) as $perm)
		{
		if($perm == $permission)
			return true;
		}
	return false;
	}

function manage()
{
	return checkpermission("manage");
}

function sendmail($to, $subject, $msg)
{ // send specific mail
	global $_SERVER;
	global $DEVELOPMENTSERVER;
	if($_SERVER['SERVER_NAME'] == $DEVELOPMENTSERVER)
		{
		echo "<br/><font color=blue>";
		echo "From:".htmlentities($FROM)."<br/>";
		echo "To:".htmlentities($to)."<br/>";
		echo "Subject:".htmlentities($subject)."</font><br/>";
		echo htmlentities($msg)."<p>";
		}
	else
		{
		mail($to, $subject, $msg, $FROM);
		}
}

?>