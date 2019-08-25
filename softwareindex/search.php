<?php
require "include/header.inc.php";
?>

<?php

// FIXME: -

$q=$_GET['q'];

if($q)
	{
	$query="select id, name, version, summary, DATE_FORMAT(updated, '%e %b %Y') as upd from ${DB_TABLE}";
	$query.=" where approved=1";
	$query.=" and match(name,summary,description,author) against (".quote($q).")";
	$query.=" order by name asc, updated desc";
	listapps($query, "<b>Search result for:</b> ".htmlentities($q));
	}
else
	echo "No query specified.";
?>

<?php
include "include/footer.inc.php";
?>
