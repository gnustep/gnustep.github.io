<?php

require "include/connect.inc.php";

header("Content-Type: application/rss+xml; charset=utf-8");

// RSS in Datei schreiben
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
echo "<rss version=\"2.0\">";

?>
<channel>
<title><?php echo htmlentities($TITLE);?></title>
<link><?php echo $URL;?></link>
<description><?php echo htmlentities($CONTENT);?></description>
<language>en-en</language>
<copyright>hns@dsitri.de</copyright>
<pubDate><?php echo date("r", time());?></pubDate>
<image>
<url><?php echo $URL."/images/rss.png";?></url>
<title>SWI</title>
<link><?php echo $URL;?></link>
</image>

<?php
$query="select * from ${DB_TABLE}";
$query.=" where approved=1";
if($_GET['rom'])
	$query.=" and rom=".quote($_GET['rom']);
$query.=" order by updated desc";
$query.=" limit 30";
$result=query($query);
while($row=mysql_fetch_array($result))
{
//	$guid=$row['id'];
//	echo "<item guid=\"".$guid."\">\n";
	echo "<item>";
	echo "<title><![CDATA[".htmlentities($row['name']);
	if($row['version'])
		echo " - ".htmlentities($row['version']);
	echo "]]></title>\n";
	echo "<pubDate>".htmlentities(date("r", strtotime($row["updated"])))."</pubDate>\n";
	echo "<description><![CDATA[".htmlentities($row['summary'])."]]></description>\n";
	if($row['author'])
		echo "<author><![CDATA[".htmlentities($row['author'])."@unknown.org]]></author>\n";
	echo "<link>".$URL."/showdetail.php?app=".$row['id']."</link>\n";
//	echo "</item guid=\"".$guid."\">\n";
	echo "</item>";
	/*
		<item>
	 <title>Titel des zweiten Artikels</title>
	 <description>
	 <![CDATA[
		 <h1>Hier kann auch der vollständige HTML-Inhalt
		 des Artikels stehen</h1>
		 <p>…</p>
		 ]]>
	 </description>
	 <author>Autor des Artikels</author>
	 </item>
	 */
}
mysql_free_result($result);
?>	
</channel>
</rss>