<?php
require "include/header.inc.php";
?>

<?php

// FIXME: ---

if(isloggedin())
	{
	$query="select id, name, version, summary, DATE_FORMAT(updated, '%e %b %Y') as upd from ${DB_TABLE}, ${DB_TABLE}_subscription";
	$query.=" where approved=1";
	$query.=" and appid=id";
	$query.=" and email=".quote(loginname());
	$query.=" order by name asc, updated desc";
	listapps($query, "<b>Subscriptions for:</b> ".htmlentities(loginname()));
	}
else
	echo "Not logged in.";
?>		

<?php
include "include/footer.inc.php";
?>
