<?php
require "include/header.inc.php";
?>

<?php

/*
 FIXME: 
 - approve all geht nicht richtig (bearbeitet nur 1 Eintrag)
 - zeigt neues Image nicht an
 - Bilder ggf. wieder lšschen!
*/


$app=$_GET['app']+0;	// app we want to change

$number=$_GET['change']+0;	// if we refer to a specific change
$field=$_GET['field'];	// if we refer to a specific field

/*
echo "app=$app<br>";
echo "change=$number<br>";
echo "field=$field<br>";
*/

if(manage() && $_GET['cmd'] == "approve")
{
/*
	echo "Approve<br>";
	echo "change=$number<br>";
	echo "app=$app<br>";
	echo "field=$field<br>";
 */
	$subject="[ANN]: $appname - $version updated in SWI";
	$body="Dear subscriber of the $TITLE,\n\n";
	$body .= "there are updates for the project $appid.";
	
	$body .= "wesentliche Inhalte zitieren";
	
	$body .= "The changes are:\n";
	if($field)
		$query="select * from ${DB_TABLE}_changerequest where number=$number and field=".quote($field); // approve specific change
	else
		$query="select * from ${DB_TABLE}_changerequest where appid=$app order by `when` asc";	// process latest first
	$result=query($query);
	while($record=mysql_fetch_array($result))
		{ // get change request(s)
			$field=$record['field'];
			$new=$record['newvalue'];
		// get old value
		$body .= "$old -> $new\n";
		$query="update ${DB_TABLE} set `$field`=".quote($record['newvalue'])." where id=$app";
//			echo $query;
		query($query);
		query("delete from ${DB_TABLE}_changerequest where appid=$app and field=".quote($field));	// auto-reject all others for same field
		// if change is for screenshot and newvalue == 0 => physically delete screenshot file!
		// if($field is like "screen$d" && newvalue == 0)
		//   $number="select screen$d from ${DB_TABLE} where appid=$app";
		//   delete file(function getscreenshot($app, $number))
		query("update ${DB_TABLE} set updated=now() where id=$app");
		}
	// notify all subscribers of this app
	mysql_free_result($result);
	$query="select email from ${DB_TABLE}_subscription where (appid = 0 or appid=$app)";
	$result=query($query);
	while($record=mysql_fetch_array($result))
		{
			// send mail
		// echo $row['email']." -- $subject -- $body<br>";
		// sendmail($record['email'], $subject, $body);
		}
}

$field=$_GET['field'];

if(manage() && ($_GET['cmd'] == "approve" || $_GET['cmd'] == "reject"))
{ // delete request(s) that have been processed
/*	
	echo "Reject<br>";
	echo "change=$number<br>";
	echo "app=$app<br>";
	echo "field=$field<br>";
*/	 
	if($field)
		{ // reject specific change
		query("delete from ${DB_TABLE}_changerequest where number=$number and field=".quote($field));	// reject specific change for this field
	// wenn field=approved dann auch delete from ${DB_TABLE} where id=appid
		}
	else
		{ // reject all changes for this application
		// if there was a change request for approved - delete application (?)
		query("delete from ${DB_TABLE}_changerequest where appid=$app");	// reject all
		}
	// go to next reasonable app if there are no change left over for this app
	$query="select number from ${DB_TABLE}_changerequest where appid=$app limit 1";	// get another reference - if any
	$result=query($query);
	$record=mysql_fetch_array($result);	// get any other record number
	mysql_free_result($result);
	if(!$record)
		{ // No more change requests for this application left over - try a different one
		$query="select appid from ${DB_TABLE}_changerequest where appid > $app order by number asc limit 1";
		$result=query($query);
		$row=mysql_fetch_array($result);
		mysql_free_result($result);
		if($record)
			$app=$record['appid']+0;	// get next one
		else
			{
			$query="select appid from ${DB_TABLE}_changerequest where appid < $app order by number desc limit 1";
			$result=query($query);
			$row=mysql_fetch_array($result);
			mysql_free_result($result);
			if($record)
				$app=$record['appid']+0;	// get previous one
			}
		}
}

$query="select * from ${DB_TABLE} where id=$app";
$result=query($query);
$record=mysql_fetch_array($result);	// get original data
mysql_free_result($result);

echo "<b>Changes for ";
echo "<a href=\"showdetail.php?app=".rawurlencode($app)."\">";
echo htmlentities($record['name']);
if($record['version'])
	echo " - ".htmlentities($record['version']);
echo "</a>";
echo ":</b>\n";
if(!manage())
 echo "If you want to help to verify and approve these changes, please create an account and apply to become a manager.";
$c="[";

$query="select appid from ${DB_TABLE}_changerequest where appid > $app order by number asc limit 1";
$result=query($query);
$row=mysql_fetch_array($result);
if(($row['appid']+0) > 0)
{
	echo " $c <a href=\"showchanges.php?app=".$row['appid']."\">Next</a>";
	$c="|";
}
mysql_free_result($result);
$query="select appid from ${DB_TABLE}_changerequest where appid < $app order by number desc limit 1";
$result=query($query);
$row=mysql_fetch_array($result);
if(($row['appid']+0) > 0)
{
	echo " $c <a href=\"showchanges.php?app=".rawurlencode($row['appid'])."\">Back</a>";
	$c="|";
}
mysql_free_result($result);
if(manage() && $app)
{
	echo " $c <a href='showchanges.php?";
	echo "change=all";
	echo "&app=".rawurlencode($app);
	echo "&cmd=approve'";
	echo " onclick=\"return confirm('are you sure to approve all changes?')\">";
	echo "Approve latest</a>";
	echo " | <a href='showchanges.php?";
	echo "change=all";
	echo "&app=".rawurlencode($app);
	echo "&cmd=reject'";
	echo " onclick=\"return confirm('are you sure to reject all?')\">";
	echo "Reject all</a>";
	$c="|";
}
if($c != "[")
	echo " ]";	// close
echo "<p>\n";

if($record)
{
$query="select *, DATE_FORMAT(`when`, '%e %b %Y %H:%i:%s') as upd from ${DB_TABLE}_changerequest where appid=$app order by field, `when` asc";	// all changes for this app
$result=query($query);
echo "<table width=\"95%\" cellspacing=0>";
$lastfield="";
while($row=mysql_fetch_array($result))
{
	if($lastfield != $row['field'])
		{ // show next field
		$lastfield=$row['field'];
		echo "<hr>\n";
		echo "<h4>".htmlentities($lastfield)."</h4>";
		// wenn field="category x" dann aus category-Liste
		echo "Current: ".htmlentities($record[$lastfield]);
		if(ereg("screen*", $lastfield) && manage())
			{
			if($record[$lastfield] != 0)
				{ // not empty
				$file=getscreenshot($row['appid'], -1, $record[$lastfield]-1);	// override database contents
				if($file)
					echo ": <a href=\"".$file."\"><img width=32 heigth=48 src=\"".$file."\"/></a>";
				else
					echo ": missing image file";
				}
			else
				echo ": none";
			}
		echo "<br>\n";
		}
	echo "Change: ".htmlentities($row['newvalue']);
	if(ereg("screen*", $lastfield) && manage())
		{
		if($row['newvalue'] != 0)
			{ // not empty
			$file=getscreenshot($row['appid'], -1, $row['newvalue']-1);	// override database contents
			if($file)
				echo ": <a href=\"".$file."\"><img width=32 heigth=48 src=\"".$file."\"/></a>";
			else
				echo ": missing image file";
			}
		else
			echo ": delete";
		echo "<br>\n";
		}
	if($row['upd'])
		{
		echo " By: ".htmlentities($row['upd'])." - ";
		if($row['requestor'])
			echo htmlentities($row['requestor']);
		else
			echo "guest";
		if($row['reason'])
			echo " - ".htmlentities($row['reason']);
		echo "<br>\n";
		}
	if(manage())
		{
		echo " [ <a href='showchanges.php?";
		echo "change=".rawurlencode($row['number']);
		echo "&app=".rawurlencode($row['appid']);
		echo "&field=".rawurlencode($row['field']);
		echo "&cmd=approve'";
		echo " onclick=\"return confirm('are you sure to approve this change?')\">";
		echo "Approve</a>";
		echo " | <a href='showchanges.php?";
		echo "change=".rawurlencode($row['number']);
		echo "&app=".rawurlencode($row['appid']);
		echo "&field=".rawurlencode($row['field']);
		echo "&cmd=reject'";
		echo " onclick=\"return confirm('are you sure to reject this change?')\">";
		echo "Reject</a> ]";
		}
	echo "<br>\n";
}
mysql_free_result($result);
}
else
echo "Change not found.";
?>

<?php
include "include/footer.inc.php";
?>
