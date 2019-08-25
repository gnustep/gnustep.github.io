<?php
require "include/header.inc.php";
?>

<?php

// FIXME: should pass ROM/model filter downwards!


$query="select id, name, version, summary, DATE_FORMAT(updated, '%e %b %Y') as upd from ${DB_TABLE}";
$query.=" where approved=1";
if($_GET['rom'])
	$query.=" and rom=".quote($_GET['rom']);
$query.=" order by updated desc";
if($_GET['count'] != 0)
	$query.=" limit ".($_GET['count']+0);
else
	$query.=" limit 32";

listapps($query, "<b>Latest changes/additions:</b>
(<a href=\"index.php?grid=grid\">Grid</a> |
 Last 
 <a href=\"index.php?count=25\">25</a> |
 <a href=\"index.php?count=50\">50</a> |
 <a href=\"index.php?count=100\">100</a>)", true);
?>

<?php
include "include/footer.inc.php";
?>
