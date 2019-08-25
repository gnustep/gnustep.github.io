<?php
	
	// FIXME:  --
	
	require "include/connect.inc.php";		// load database connection
	require "include/login.inc.php";		// login manager
	
	// FIXME: toggle between list and grid view! Cookie? additional paramter?
	
	function listapps($query, $info="<p>", $showall=false)
	{
	global $_GET;
	global $DB_TABLE;
	$grid=($_GET['grid']=="grid");
	echo "$info<p>\n";
	// echo $query;
	$result=query($query);
	$rownum=0;
	$colnum=0;
	$lastname="";
	if($grid)
		{
		echo "<table width=\"95%\" cellspacing=0>";
		$columns=4;
		}
	else
		echo "<table width=\"95%\" cellspacing=0>";
	while($row=mysql_fetch_array($result))
		{
		if(!$showall && $row['name'] == $lastname)
			continue;	// show first entry only (must be ordered by version/date descending)
		$lastname=$row['name'];
		if($grid)
			{ // show in grid mode
				if($colnum == 0)
					echo "<tr>";	// start new row
				echo "<td align='center' valign='top' width='".(100/$columns)."%'>";
				echo "<a href='showdetail.php?app=".$row['id']."'>";	
				$screen=getscreenshot($row['id']);
				if(!$screen)
					$screen="images/image_32.png";
				echo "<img src='".$screen."' width='64' height='64' border='0'>";
				echo "<center>";
				echo htmlentities($row['name']);
				if($row['version'])
					echo " - ".htmlentities($row['version']);
				echo "<br>";
				if($row['upd'])
					echo "(".htmlentities($row['upd']).")";
				echo "<br>";
				// break into lines
				echo htmlentities(wordwrap($row['summary'], 30));
				echo "</center>";
				echo "<br>";
				echo "<br>";
				echo "</a>";
				echo "</td>";
				if(++$colnum == $columns)
					{ // start over with next column
					$colnum=0;
					$rownum++;
					echo "</tr>\n";
					}
				continue;
			}
		$rownum++;
		if($rownum % 2 == 0)
			echo "<tr class=\"listing\">";
		else
			echo "<tr class=\"mlisting\">";
		echo "<td width=5%>";
		if(getscreenshot($row['id']))
			{ // screen0 is enabled and exists
				echo "<a href='showdetail.php?app=".$row['id']."'>";	
				echo "<img src='images/image_32.png' width='18' height='18' border='0'>";
				echo "</a>";
			}
		else
			echo "<img src='images/transparent_32.gif' width='18' height='18' border='0'>";
		echo "</td>";
		echo "<td align=left width=30%>";
		echo "<a href='showdetail.php?app=";
		echo rawurlencode($row['id']);
		if($row['updated'])
			echo "&updated=".rawurlencode($row['updated']);
		if($_GET['rom'])
			echo "&rom=".rawurlencode($_GET['rom']);
		if($_GET['model'])
			echo "&model=".rawurlencode($_GET['model']);
		echo "'>";
		echo htmlentities($row['name']);
		if($row['version'])
			echo " - ".htmlentities($row['version']);
		echo "</a>";
		echo "</td>";
		echo "<td>";
		echo htmlentities($row['summary']);
		echo "<td align=right width=10% nowrap><small>";
		if($row['upd'])
			echo "(".htmlentities($row['upd']).")";
		echo "</small></td>";
		echo "</tr>\n";
		}
	if($grid && $colnum != 0)
		{ // close final cell
		echo "</tr>\n";
		}
	mysql_free_result($result);
	echo "</table>";
	
	if($rownum == 0)
		echo "Nothing found.";
	}
	
	function getscreenshot($id, $number=0, $slot=-1)
	{ // get full file name either by screen$number or $slot
		global $DB_TABLE;
		if($id)
			{
			if($number >= 0)
				{
				$query="select * from ${DB_TABLE} where id=".($id+0);
				$result=query($query);
				$row=mysql_fetch_array($result);
				mysql_free_result($result);
				return getscreenshot($id, -1, $row["screen$number"]-1);
				}
			if($slot >= 0)
				{ // exists (i.e. sequence number is defined)
					$basename=sprintf("screenshots/%05d_%d", $id, $slot);
					if(file_exists($basename.".png"))
						return $basename.".png";
					if(file_exists($basename.".jpg"))
						return $basename.".jpg";
					if(file_exists($basename.".gif"))
						return $basename.".gif";
				}
			}
		return "";
	}
	
	function getscreenshots($id)
	{ // get array of sequence numbers where a screendhot file exists
		global $DB_TABLE;
		$result=array();
		$handle=opendir('screenshots');
		while(false !== ($file = readdir($handle)))
			{
			preg_match("/^(.*)_(.*)\.(.*)/i", $file, $treffer);
			//		echo $file." -> ".$treffer;
			if($treffer[1] == $id)
				$result[]=$treffer[2];
			}
		closedir($handle); 
		return $result;
	}
	
	function requestchange($app, $field, $value="", $reason="")
	{ // queue change request
		global $DB_TABLE, $TITLE, $URL;
		$result=query("select max(number) as next from ${DB_TABLE}_changerequest");
		$row=mysql_fetch_array($result);
		$next=$row['next']+1;
		mysql_free_result($result);
		query("insert into ${DB_TABLE}_changerequest (number, `when`, appid, field, newvalue, requestor, reason) values ($next, now(), $app, ".quote($field).", ".quote($value).", ".quote(loginname()).", ".quote($reason).")");
		// FIXME: collect change rquests and/or use a timer to send all existing CR in a single mail - e.g. daily or store the last sent date in the DB
		$subject="New SWI change request for $TITLE";
		$body="$app, $field, -> $value, $reason <$URL/showchanges.php?app=$app&change=$next&field=$field>";
		$query="select * from ${DB_TABLE}_users where permissions like '%manage%' and enabled=1";
		//	$query="select * from ${DB_TABLE}_users";
		$result=query($query);
		while($row=mysql_fetch_array($result))
			{ // notify all managers
				//			echo $row['email']." -- ".$row['permissions']." -- $subject -- $body<br>";
				sendmail($row['email'], $subject, $body);
			}
		mysql_free_result($result);
	}
	
	function imagecode($number)
	{
	return md5("iws".$number."image");
	}
	
	include "include/header2.inc.php";
	
	?>