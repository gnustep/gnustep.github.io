<?php
include "include/header.inc.php";
?>

<?php

/*
 FIXME:
 - 
 */
?>

<TABLE width="650" border=0>
<TR>
<TD>
<TABLE colspan=2 rowspan=2 align=center border=0>

<?php
$query="select license, count(license) as cnt from ${DB_TABLE}";
$query.=" where approved=1 and license <> ''";
$query.=" group by license";
$query.=" order by license asc";
$result=query($query);
$total=0;
while($row=mysql_fetch_array($result))
{
	echo "<tr><td width=80%>";
	echo "<a href='showapps.php?license=".rawurlencode($row['license'])."'>";
	echo htmlentities($row['license']);
	echo "</a>\n";
	echo "</td><td align=right>".$row['cnt']."</td></tr>\n";
	$total=$total+$row['cnt'];
}
mysql_free_result($result);
?>
<TR><TD colspan=2><HR></TR>
<TR><TH align=left>Overall</TH><TH align=right><?php echo $total;?></TH></TR>
<TR><TD colspan=2><HR></TR>
<?php
$query="select DATE_FORMAT(added, '%b %Y') as since from ${DB_TABLE} order by approved desc limit 1";	// month of oldest record
$result=query($query);
$row=mysql_fetch_array($result);
mysql_free_result($result);
echo "<tr><td>Detail-Views since ".$row['since'].": </td>";

$query="select sum(views) as sum from ${DB_TABLE}";
$query.=" where approved=1";
$result=query($query);
$row=mysql_fetch_array($result);
mysql_free_result($result);
echo "<th align=right>".$row['sum']."</td></tr>\n";
?>
<TR><TD colspan=2><HR></TR>
<?php
$query="select count(*) as sum from ${DB_TABLE}_comment";
$result=query($query);
$row=mysql_fetch_array($result);
mysql_free_result($result);
echo "<tr><td>Total comments: </td><th align=right>".$row['sum']."</b></tr>\n";

?>
<TR><TD colspan=2><HR></TR>
<?php
$query="select count(email) as sum from ${DB_TABLE}_users";
$query.=" where enabled=1";
$result=query($query);
$row=mysql_fetch_array($result);
mysql_free_result($result);
echo "<tr><td>Total users: </td><th align=right>".$row['sum']."</b></tr>\n";

?>
</table>
</tr>
<tr><td>
<?php

echo "<b>Pending changes/approvals</b><p>\n";
$query="select distinct DATE_FORMAT(`when`, '%e %b %Y %H:%i:%s') as upd, ${DB_TABLE}.id as app, name, version from ${DB_TABLE}_changerequest, ${DB_TABLE} where appid=id order by `when` asc";
$result=query($query);
$rownum=0;
echo "<table width=\"95%\" cellspacing=0>";
while($row=mysql_fetch_array($result))
{
	$rownum++;
	if($rownum % 2 == 0)
		echo "<tr class=\"listing\">";
	else
		echo "<tr class=\"mlisting\">";
	echo "<td align=left width=30%>";
	echo "<a href='showchanges.php?app=";
	echo rawurlencode($row['app']);
	echo "'>";
	echo htmlentities($row['name']);
	if($row['version'])
		echo " - ".htmlentities($row['version']);
	echo "</a>";
	echo "</td>";
	echo "<td><small>";
	if($row['upd'])
		echo "(".htmlentities($row['upd']).")";
	echo "</small></td>";
	echo "</tr>\n";
}
mysql_free_result($result);
if($rownum == 0)
	echo "Nothing found.";

?>
</td>
</tr>
</table>

<?php
include "include/footer.inc.php";
?>
