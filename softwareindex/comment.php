<?php
require "include/header.inc.php";
?>

<?php

// FIXME: ---

global $_GET;
global $_POST;

if(!isloggedin())
{
	echo "<b>Please <a href=\"login.php\">log in</a> before you can submit or view comments.</b>\n";
}
else
{
	if($_GET['cmd'] == "all")
		{ // show all comments
			echo "Please click on subject to show full message.<p>";
			$query="select *, DATE_FORMAT(date, '%e %b %Y') as posted from ${DB_TABLE}_comment";
			$query.=" order by date desc";
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
			echo "<td width=10%>";
			// make entries clickable -> link to specific comment
			echo htmlentities($row['posted']);
			echo "</td>";
			echo "<td width=10%>";
			echo htmlentities($row['appid']);
			echo " ";
			echo htmlentities($row['version']);
			echo "</td>";
			echo "<td width=15%>";
			// protect against simple harvesting by replacing @ and . with &#64; etc.
			echo htmlentities($row['from']);
			echo "</td>";
			echo "<td width>";
			echo htmlentities($row['subject']);
			echo "</td>";
			echo "</tr>\n";
			}
		mysql_free_result($result);
		echo "</table>\n";
		include "include/footer.inc.php";
		exit;
		}

	// echo " [ <a href=\"comment.php?cmd=delete&appid=$id&date=".rawurlencode($row['date'])."&from=".rawurlencode($row['from'])."\">Delete</a> ]";
	if($_GET['cmd'] == "delete")
		{
			echo "Delete Comment for App: ".$_GET['appid']."<br>";
			echo "From ".$_GET['from']."<br>";
			echo "Date ".$_GET['date']."<br>";
			// look up record specified
			// check if($row['from'] == loginname() || manage())
			include "include/footer.inc.php";
			exit;
		}
	
	if($_GET['cmd'] == "save")
		{
		if(($_GET['app']+0) && $_POST['subject'] && $_POST['body'])
			{ // store comment
			$query="insert into ${DB_TABLE}_comment (appid, version, `from`, subject, body, `date`) values(";
			$query.=($_GET['app']+0).", '?', ".quote(loginname()).", ".quote($_POST['subject']);
			$query.=", ".quote($_POST['body']).", now())";
			query($query);
			echo "<b>Comment saved. Go <a href=\"showdetail.php?app=".($_GET['app']+0)."\">back</a>.\n";
			include "include/footer.inc.php";
			exit;
			}
		echo "Please provide Subject and Body!";
		}
	$query="select *, DATE_FORMAT(updated, '%e %b %Y') as upd, DATE_FORMAT(added, '%e %b %Y') as ad from ${DB_TABLE}";
	$query.=" where id=".($_GET['app']+0);
	$result=query($query);
	$row=mysql_fetch_array($result);
	mysql_free_result($result);
	echo "Please add comments for <b>".$row['name']." - ".$row['version']."</b>\n";
	?>
	<form method="POST" action="comment.php?cmd=save&app=<?php echo $_GET['app'];?>" accept-charset="iso-8859-1">
	<table align=left>
	<tbody>
	<tr>
	<td><B>Subject:</B></TD>
	<td><INPUT size="80" value="<?php echo htmlentities($_POST['subject']);?>" name="subject"></td>
	</tr>
	<tr>
	<td><B>Body:</B></TD>
	<td><textarea rows="12" cols="55" name="body"><?php echo $_POST['body'];?></textarea></td>
	</tr>
	<tr>
	<td colSpan=2><INPUT type="submit" value="Add" name="submit"> </td>
	</tr>
	</form>

	<?php
}
?>

<?php
include "include/footer.inc.php";
?>
