<?php
require "include/header.inc.php";
?>

<?php

/*
 FIXME: 
 - layout
*/

$id=($_GET['app']+0);
if($id == 0)
{ // try by application name
	$name=($_GET['appname']);
	$query="select * from ${DB_TABLE} where name=".quote($name)." limit 1";
	$result=query($query);
	if($row=mysql_fetch_array($result))
		$id=$row['id'];	// found
	mysql_free_result($result);
}

function title($row)
{ // show title and changed
	global $DB_TABLE;
	?>
	<td colspan="2">
	<table name="title" width="100%" border="0">
	<tr>
	<td>
			<span class="hline">
				<?php 
				echo htmlentities($row['name']);
				$v=$row['version'];	// found
				if(!$v)
					$v=$row['added'];	// substitute publishing date as version
					// format as yyyy.mm.dd
					//				if($row['version'])
//					echo " - ".htmlentities($row['version']);
				$query="select * from ${DB_TABLE} where name=".quote($row['name'])." and approved=1 order by added desc, updated desc, version desc";
				$values=array();
				$ids=array();
				$result=query($query);
				while($vrow=mysql_fetch_array($result))
					{
					  $vv=$vrow['version'];	// found
						if(!$vv)
							$vv=$vrow['added'];	// substitute publishing date as version
							// format as yyyy.mm.dd
						$values[] = $vv;	// add
						$ids[] = $vrow['id'];	// add id
					}
				echo " - ";
				selectionlist("version", $values, $v, "showdetail.php?app=", $ids);
				mysql_free_result($result);
				if(count($values) > 0 && $v != $values[0])
					{
						if($v == $values[1])
							echo " <font color=\"red\">update available</font>";	// second newest
						else
							echo " <font color=\"red\">updates available</font>";	// current is not first
					}
				?>
			</span>
	</td>
	<td width="20%" align="right">
				<?php
				if($row['upd'])
					echo "published: ".htmlentities($row['upd']);
				?>
	</td>
	</tr>
	</table name="title">
	</td>
	<?php
}

function checklink($url)
{
	global $DB_TABLE;
//	return "";	// experimental

	// we should wipe out older records so that the table does not grow infinitely
	 $query="select * from ${DB_TABLE}_links where url=".quote($url)." limit 1";
	 $result=query($query);
	 $row=mysql_fetch_array($result);
	 mysql_free_result($result);
	 if($row && $row['timestamp'] > time()-1*24*3600)
		return $row['result'];	// return cached result
	
	$parsed=parse_url($url);
	if(!$parsed["scheme"] && !$parsed["host"] && $parsed["path"])
		{ // just a plain address
		$parsed["host"]=$parsed["path"];
		$parsed["path"]="";
		}

	if(!$parsed['scheme']) $parsed['scheme']="http";	// default
	
//	print_r($parsed);
	
//	$fullpath="$parsed[scheme]://$parsed[host]$parsed[path]";
	$result=" <font color=orange>[can't check URL]</font>";
	if(!$parsed["host"])
		return "";	// not valid at all -- don't cache
	
	if((checkdnsrr($parsed["host"], "A")) != true)
		$result=" <font color=orange>[host unreachable]</font>";	// host not found
	else
		{
		/*
		 
		 $ch = curl_init ($url) ;
		 curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1) ;
		 // set some option to check existence only
		 // set some option to get error status
		 $res = curl_exec ($ch) ;
		 curl_close ($ch) ;
		 return ($res) ;
		 
		 */
		$port=$parsed["port"];
		if(!$port)
			{
			if($parsed['scheme'] == "https")
				$port=443;
			else
				$port=80;	// default for HTTP
			}
		$path=$parsed["path"];
		if(!$path)
			$path="/";	// root
		if($parsed["query"])
			$path.="?".$parsed["query"];
	
		if($parsed['scheme'] == "http")
			{
			$fp=fsockopen($parsed["host"], $port, $errno, $errstr, 1.0);
			if($fp)
				{
				stream_set_timeout($fp, 2.0);
//			if($path[0] == "/")
//				$path=substr($path, 1);
		
//			echo " GET $path HTTP/1.1\n\n";
			// FIXME: this does not work for https
				// we may need to do a curl call
				fputs($fp,"HEAD $path HTTP/1.1\nHost: ".$parsed['host']."\n\n");	// ask for header only (large files!)
				$lines=fgets($fp, 1024);	// get first line(s)
		
//			echo $lines;
			
				strtok($lines, " ");	// HTTP...
				$code=strtok(" ");
				$message=trim(strtok("\n"));
				if($code == 200)
					$result=" <font color=green>[$code $message]</font>";
			// handle server redirect!
				else if($message)
					$result=" <font color=red>[$code $message]</font>";	// path not found
				else
					$result=" <font color=orange>[no response]</font>";
				fclose($fp);
				}
			else
				$result=" <font color=orange>[no connection]</font>";
			}
		
		}
	if($row)
		$query="update ${DB_TABLE}_links set timestamp=".quote(time()).", result=".quote($result)." where url=".quote($url);
	else
		$query="insert into ${DB_TABLE}_links set timestamp=".quote(time()).", result=".quote($result).", url=".quote($url);
//	echo $query;
	query($query);

	return $result;
}

function field($row, $title, $field, $right="", $left="")
{
	if($left || $right || $row[$field])
		return "<tr valign=top><td><b>".htmlentities($title)."</b></td><td valign=top>".$left.htmlentities($row[$field]).$right."</td></tr>";
	return "";
}

function urlfield($row, $title, $field)
{
	if($row[$field])
		return field($row, $title, $field, "</a>".checklink($row[$field]), "<a href=\"".$row[$field]."\">");
	return "";
}

function details($id, $row)
{ // show description
	global $DB_TABLE;
	echo "<tr><td colspan='2'>";
				$desc=$row['description'];
				if(preg_match("/<a href=/", $desc) ||
				   preg_match("/<br\/>/", $desc) ||
				   preg_match("/<br>/", $desc) ||
				   preg_match("/<li>/", $desc) ||
				   preg_match("/<p>/", $desc))
					echo $desc;	// contains <br> or <br/> or <p>, don't convert \n and assume plain HTML
				else
					{
					$desc=htmlentities($desc);	// convert to HTML
					$desc=preg_replace("/\n\n/", "<p>", $desc);	// add simple formatting
					$desc=preg_replace("/\n/", "<br/>\n", $desc);
					echo $desc;
					}
				?>
					</td>
					</tr>
					<tr>
					<td colspan=2>
					<p/>
					</td>
					</tr>
					<?php
					echo field($row, "Visits", "views");
					echo field($row, "Version", "version");
					echo field($row, "Operating System", "rom");
					echo field($row, "Device Models", "model");
					echo field($row, "Maturity Level", "maturity");
//					echo field($row, "Permission", "permission");
					echo field($row, "License", "license", "</a>", "<a href='showapps.php?license=".rawurlencode($row['license'])."'>");
					echo field($row, "Price (US$)", "price", "&nbsp;US$&nbsp;&nbsp;<i>This is informative and approximate only. Not an offer. No liability for inaccuracies.</i>");
					?>
					<tr>
					<td>
					<b>Category</b>
										</td>
										<td>
										<?php
										$first=true;
										for($i=1; $i<=4; $i++)
										{
											$query="select category$i from ${DB_TABLE} where id=$id";
											$result=query($query);
											$r=mysql_fetch_array($result);
											mysql_free_result($result);
											$cat=$r["category$i"];
											if(!$cat)
												continue;	// empty
											if(!$first)
												echo ", ";
											echo "<a href=\"showapps.php?cat=".rawurlencode($cat)."\">";
											echo htmlentities($cat);
											echo "</a>";
											$first=false;
										}
										if(!$first)
											; // no category specified...
									?>
										</td>
										</tr>
										<?php
							   echo field($row, "Released", "ad");
							   echo urlfield($row, "Homepage", "homepageurl");
							   echo urlfield($row, "Feed", "feedurl");
							   echo urlfield($row, "Download", "downloadurl");
							   echo urlfield($row, "Source", "sourceurl");
}

function description($id, $row)
{ // show description
	global $DB_TABLE;
	echo "<td align='left' valign='top'>";
	?>
		<table name="description" width="100%">
		<tr><?php title($row);?></tr>
		<tr><td colspan="2"><p/></td></tr>
		<tr>
			<td colspan="2">
			<?php 
				echo htmlentities($row['summary']);
			if($row['author'])
				echo " - by "."<a href=\"search.php?q=".rawurlencode($row['author'])."\">".htmlentities($row['author'])."<a>";
			?>
			</td>
		</tr>
		<tr><td colspan="2"><p/></td></tr>
		<?php details($id, $row);?>
		</table name="description">
	<?php
	echo "</td>";
}

function commands($id)
{ // show commands
	global $DB_TABLE;
	?>
	<td colspan="2" align="right">
	[ <a href="comment.php?app=<?php echo $id;?>">add comment</a> |
						  <a href="submit.php?change=<?php echo $id;?>">edit entry</a> |
		<?php
		$query="select * from ${DB_TABLE}_changerequest where appid=$id limit 1";
		$result=query($query);
		if(mysql_fetch_array($result))
		{ // has pending changes
			echo " <a href=\"showchanges.php?app=$id\"?>pending changes</a> | ";
		}
		mysql_free_result($result);

		if(isloggedin())
		{
			$query="select * from ${DB_TABLE}_subscription where appid=$id and email=".quote(loginname());
			$result=query($query);
			if(mysql_fetch_array($result))
				{ // has subscribed
				echo " <a href=\"showdetail.php?app=".$id."&cmd=unsubscribe\">unsubscribe</a>";
				}
			else
				echo " <a href=\"showdetail.php?app=".$id."&cmd=subscribe\">subscribe</a>";
			mysql_free_result($result);
			echo " |";
		}
		//		if(manage())
		//		{
		//			if($row['approved'] == 0)
		//				echo " | <a href=\"showdetail.php?app=".$id."&cmd=approve\">approve</a>";
		//			else
		echo " <a href=\"showdetail.php?app=".$id."&cmd=disapprove\">disapprove</a>";
		//		}
		?>
		]</td>
	<?php
}

function screenshot($id)
{ // show screen shot if available
	global $DB_TABLE;
	$filename=getscreenshot($id);	// get first screen shot
	if($filename)
		{ // scale image proportionally to max width of 240 and height of 320
		$info=getimagesize($filename);
		$w=$info[0];
		$h=$info[1];
		$wfactor=240/$w;
		$hfactor=320/$h;
		$factor=min($hfactor, $wfactor);
		if($factor > 3)
			$factor=3.0;	// don't enlarge too much
		$h*=$factor;
		$w*=$factor;
		?>
		<td align="left" valign="top" width="260">
		<a href='<?php echo $filename;?>'>
			<img src='<?php echo $filename;?>' width='<?php echo $w;?>' height='<?php echo $h;?>' alt='Screenshot'/>&nbsp;
			</a> 	
				</td>
				<?php
		}
	else
		echo "<td width='10%'><a href=\"submit.php?change=$id#Screenshots\">submit<br>screenshot</a></td>";
	echo "</td>";
}

function morescreens($id, $row)
{ // show more screen shots if available
	global $DB_TABLE;
	$first=1;
	for($num=1; $num <= 4; $num++)
		{
		$file=getscreenshot($id, $num);	// get file name
		if($file == "")
			continue;	// not found
		if($first)
			{
			?>
			<tr>
			<td colspan="2" align="left">
			<b>More Screenshots:</b><p>
			<table name="screenshots">
			<tr>
			<td>
			<?php
			$first=0;
			}
		$info=getimagesize($file);
		$w=$info[0];
		$h=$info[1];
		$wfactor=120/$w;
		$hfactor=160/$h;
		$factor=min($hfactor, $wfactor);
		if($factor > 3)
			$factor=3.0;	// don't enlarge too much
		$h*=$factor;
		$w*=$factor;
		?>
			<a href='<?php echo $file;?>'>
			<img src='<?php echo $file;?>' width='<?php echo $w;?>' height='<?php echo $h;?>' border='0'
				alt='Screenshot <?php echo htmlentities($num+1);?>'/>
			</a>
		<?php
		}	// for loop
	if(!$first)
		{ // there was any
		?>
		</td>
		</tr>
		</table>
		<tr>
		<?php commands($id); ?>
		</tr>
		</td>
		</tr>
		<?php
		}
}

function comments($id)
{ // show comments - if available
	global $DB_TABLE;
	// FIXME: show comments by same appname not id
	// fetch version number belonging to comment
	$query="select *, DATE_FORMAT(`date`, '%e %b %Y %H:%i:%s') as d from ${DB_TABLE}_comment where appid=$id order by `date` desc";
	$result=query($query);
	$first=1;
	while(($row=mysql_fetch_array($result)))
		{
		if($first)
			{
			?>
			<tr>
			<td colspan="2" align="left">
			<b>Comments:</b><p>
			<table name="comments">
			<?php
			$first=0;
			}
		echo "<tr><td>";
		echo "<b>From: ".htmlentities($row['from'])."</b>";
		echo " Date: ".htmlentities($row['d']);
		if($row['version'])
			echo " Version: ".htmlentities($row['version']);
		if($row['from'] == loginname() || manage())
			echo " [ <a href=\"comment.php?cmd=delete&appid=$id&date=".rawurlencode($row['date'])."&from=".rawurlencode($row['from'])."\">Delete</a> ]";
		echo "<br/>\n";
		echo "<b>Subject: ".htmlentities($row['subject'])."</b>";
		echo "<p/>\n";
		echo htmlentities($row['body']);
		echo "<p/>\n";
		}	// while loop
	if(!$first)
		{ // there was any
		?>
		</table>
		<tr>
		<?php commands($id); ?>
		</tr>
		</td>
		</tr>
		<?php
		}
	mysql_free_result($result);
}

function fixit($id)
{ // show more screen shots if available
	global $DB_TABLE;
		?>
	<td colspan="2" align="center">
		<font color=red>
		Anything wrong with this entry? <a href="submit.php?change=<?php echo $id;?>"><font color=red>Click here to fix it!</font></a>
			<p>
			Want to add a comment? <a href="comment.php?app=<?php echo $id;?>"><font color=red>Click here!</font></a>
		</font>
		<td>
			<?php
}

function master($id, $row)
{ // master layout
	global $DB_TABLE;
	?>
	<table name="master" width="100%" align="left">
	<tr>
		<?php screenshot($id); ?>
		<?php description($id, $row); ?>
	</tr>
			<tr>
			<?php commands($id); ?>
			</tr>
	<?php morescreens($id, $row); ?>
	<?php comments($id); ?>
				<tr>
			<?php fixit($id); ?>
			</tr>
	</table name="master">
	<?php
}

if($_GET['cmd'] == "disapprove")
{
	echo "<b>This function is to request to delete the application. Please note that this request will be reviewed.</b>";
	?>
		<form method="POST" enctype="multipart/form-data" action="showdetail.php?app=<?php echo $id;?>&cmd=disapprove-ok" accept-charset="iso-8859-1">
		<table>
		<tr>
		<td>Reason:</td><td><INPUT size="30" value="" name="comment"></input></td>
		</tr>
		<TR>
		<TD>
		<?php
			$number=341517;
			$key=imagecode($number);
			$imagepath="images/submit-number.png?no=$key";
			// create image for number and store
			echo "<Input value=\"$key\" name=\"cone\" type=\"hidden\">";
			echo "<IMG src=\"$imagepath\">";
		?>
		</td><td>
		<INPUT size="7" name="image"> (Please retype the number you see on the left after subtracting two)</TD></TR>
		</TABLE>
		<INPUT type="submit" value="Submit" name="submit"> 
		</form>
	<?php
}
else if($_GET['cmd'] == "disapprove-ok")
{
	if(!$_POST['comment'])
		{
		echo "<b>Please specify a comment.</b>";
		}
	else if(imagecode($_POST['image']) != "".$_POST['cone']."")
		{
		echo "<b>Please type in the correct code.</b>";
		}
	else
		{
		requestchange($id, 'approved', 0, $_POST['comment']);
		echo "<b>Disapproval requested.</b>";
		}
}
else if(manage() && $_GET['cmd'] == "approve")
{
	requestchange($id, 'approved', 1, $_POST['comment']);
	echo "<b>Approval requested.</b>";
}
else if(isloggedin() && $_GET['cmd'] == "subscribe")
{
	query("replace ${DB_TABLE}_subscription set email=".quote(loginname()).", appid=$id");
}
else if(isloggedin() && $_GET['cmd'] == "unsubscribe")
{
	query("delete from ${DB_TABLE}_subscription where email=".quote(loginname())." and appid=$id");
}

$query="select *, DATE_FORMAT(updated, '%e %b %Y') as upd, DATE_FORMAT(added, '%e %b %Y') as ad from ${DB_TABLE}";
$query.=" where id=".$id;
$result=query($query);
$row=mysql_fetch_array($result);
mysql_free_result($result);

if(!$row)
	{
	if($_GET['appname'])
		$id=$_GET['appname'];
	echo "Application $id not found.";
	include "include/footer.inc.php";
	exit;
	}

if(!manage())
	{ // update visit count
	query("update ${DB_TABLE} set views=views+1 where id=$id");
	$query="insert into ${DB_TABLE}_visitors values($id, now(), ".quote($_SERVER['REMOTE_ADDR']).")";
	query($query);
	query("delete from ${DB_TABLE}_visitors where `when` < date_sub(now(), interval 1 day)");	// remove older entries
	}

master($id, $row);
include "include/footer.inc.php";
exit;

?>