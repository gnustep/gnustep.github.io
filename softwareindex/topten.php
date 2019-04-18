<?php
require "include/header.inc.php";
?>

<?php

// FIXME: ---

$hours=24;

$count=($_GET['count']+0);
if(!$count)
	$count=10;

query("delete from ${DB_TABLE}_visitors where `when` < date_sub(now(), interval $hours hour)");	// remove older entries

$query="select id, name, version, summary, appid, count(appid)/$hours as upd from ${DB_TABLE}_visitors, ${DB_TABLE}";
$query.=" where approved=1";
$query.=" and id=appid";
$query.=" group by appid";
$query.=" order by upd desc, ${DB_TABLE}.name asc";
$query.=" limit $count";

listapps($query, "<b>Top $count:</b> (Top <a href=\"?count=10\">10</a> | <a href=\"?count=20\">20</a> | <a href=\"?count=50\">50</a>)", true);

?>

<?php
include "include/footer.inc.php";
?>
