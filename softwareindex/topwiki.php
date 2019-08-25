<?php
//
// Wiki module to show topten for QuantumSTEP
//
// used as $(module topwiki topten 10)
//
//
// FIXME:
// - allow for count=10
// 


function module_topten($argv)
{
	global $_SERVER, $DATABASE, $USER, $HOST, $PASSWORD;

	$hours=24;

	$count=($argv[3]+0);
	if(!$count)
		$count=10;

	if($_SERVER['SERVER_NAME'] == "localhost")
		{
		$DB_TABLE="swi";
		$DB_DATABASE="killefiz";	// on localhost
		$DB_USER=$USER;
		}
	else
		{
		$DB_TABLE="swi_qs";
		$DB_DATABASE="quantumstep";	// on server
		$DB_USER="root";
		}
//	echo "swi=$DB_DATABASE<br>";
//	echo "wiki=$DATABASE<br>";

	mysql_close();
	@mysql_pconnect($HOST, $DB_USER, $PASSWORD);
	if(mysql_error())
		echo "Could not reconnect to $HOST<br>";
	if(!mysql_select_db($DB_DATABASE))
		echo "Could not select $DB_DATABASE on $HOST<br>";

	query("delete from ${DB_TABLE}_visitors where `when` < date_sub(now(), interval $hours hour)");	// remove older entries

	$query="select id, name, summary, appid, count(appid)/$hours as upd from ${DB_TABLE}_visitors, ${DB_TABLE}";
	$query.=" where approved=1";
	$query.=" and id=appid";
	$query.=" group by appid";
	$query.=" order by upd desc, ${DB_TABLE}.name asc";
	$query.=" limit $count";

	$result=query($query);

	$r="<font size=-1>";
	while($row=mysql_fetch_array($result))
		{
		$r.="<a href=\"swi/showdetail.php?appname=".rawurlencode($row['name'])."\">".
			htmlentities($row['name']).
			"</a> (".$row['upd'].")<br>";
		}

	mysql_free_result($result);
	
	mysql_close();
	@mysql_pconnect($HOST, $USER, $PASSWORD);
	mysql_select_db($DATABASE);	// back to wiki database

	$r.="</font>";
	return output($r, $record);

}
?>