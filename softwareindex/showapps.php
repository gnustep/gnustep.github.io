<?php
require "include/header.inc.php";
?>

<?php

// FIXME: -

$cat=$_GET['cat'];
$license=$_GET['license'];

$query="select id, name, version, summary, DATE_FORMAT(updated, '%e %b %Y') as upd from ${DB_TABLE}";
$query.=" where approved=1";
if($cat)
{
$query.=" and (";
$query.=" category1=".quote($cat);
$query.=" or category2=".quote($cat);
$query.=" or category3=".quote($cat);
$query.=" or category4=".quote($cat);
$query.=" )";
}
if($license)
	$query.=" and license=".quote($license);
if($_GET['rom'])
	$query.=" and rom=".quote($_GET['rom']);
$query.=" order by name asc, updated desc";

listapps($query, "<b>Category:</b> ".htmlentities($cat));

?>

<?php
include "include/footer.inc.php";
?>
