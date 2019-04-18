<?php
require "include/header.inc.php";
?>

<?php

/* FIXME:
  - Formatierung für Screenshots im Eingabebereich
*/

if(false)
{
$body="POST:\n"; foreach ($_POST as $key => $value) $body.="$key => $value\n";
$body.="GET:\n"; foreach ($_GET as $key => $value) $body.="$key => $value\n";
$body.="SERVER:\n"; foreach ($_SERVER as $key => $value) $body.="$key => $value\n";
mail("hns@dsitri.de", "submit.php access from ".$_SERVER['REMOTE_ADDR'], $body, "hns@dsitri.de");
}

if($_POST['change'] == "ignore")
{ // store
	$id=($_POST['baseid']+0);
	/*
	echo "Baseid: ".$_POST['baseid']."<br>";
	echo "Change: ".$_POST['change']."<br>";
	echo "Added: ".$_POST['added']."<br>";
	echo "Updated: ".$_POST['updated']."<br>";
	echo "Name: ".$_POST['name']."<br>";
	echo "Summary: ".$_POST['summary']."<br>";
	echo "Category1: ".$_POST['category1']."<br>";
	echo "Category2: ".$_POST['category2']."<br>";
	echo "Category3: ".$_POST['category3']."<br>";
	echo "Category4: ".$_POST['category4']."<br>";
	echo "License: ".$_POST['license']."<br>";
	echo "Version: ".$_POST['version']."<br>";
	echo "Operating System: ".$_POST['rom']."<br>";
	echo "Device Model: ".$_POST['model']."<br>";
	echo "Price: ".$_POST['price']."<br>";
	 echo "Author: ".$_POST['summary']."<br>";
	echo "Homepage: ".$_POST['homepageurl']."<br>";
	 echo "Feed: ".$_POST['feedurl']."<br>";
	 echo "Download: ".$_POST['downloadurl']."<br>";
	echo "Source: ".$_POST['sourceurl']."<br>";
	echo "Description: ".$_POST['description']."<br>";
	echo "Image: ".$_POST['image']."<br>";
	 echo "Reason: ".$_POST['reason']."<br>";
	 */
	$query="select * from ${DB_TABLE} where id=".$id;
	$result=query($query);
	$record=mysql_fetch_array($result);
	mysql_free_result($result);
	if($_POST['ignore'] != 12345 || imagecode($_POST['image']) != "".$_POST['cone']."")
		{
		echo "<b>Please type in the correct code.</b>";
		}
	else if(!$_POST['name'])
		echo "<b>No product name specified.</b>";
	else if($id != 0 && (!$record || $_POST['edit-version'] == "edit" || $_POST['version'] == $record['version']))
		{ // existing version change request
		if(!$record)
			echo "<b>Application $id not found.</b>";
		else
			{ // process fields and generate change requests
			$fields=array("name", "summary", "license", "version", "rom", "model",
						  "price", "downloadurl", "homepageurl", "feedurl", "sourceurl", "description", "author",
						  "maturity",
						  "category1", "category2", "category3", "category4");
			reset($fields);
			while(list($key, $field)=each($fields))
				{ // go through fields
				$val=$_POST[$field];
				if($val == "---" && ($field == "rom" || $field == "model" || ereg("category.*", $field)))
					$val="";	// none
				if($val != $record[$field])
					{ // store change request
					requestchange($id, $field, $val, $_POST['reason']);
					}
				}
			for($num=0; $num <= 4; $num++)
				{ // process uploads and deletes
				/*
				echo "screen$num: ",
				print_r($_FILES["screen$num"]);
				echo " ".$_FILES["screen$num"]['name'];
				echo " ".$_FILES["screen$num"]['size'];
				echo "<br>";
				echo "delete$num: ".$_POST["delete$num"]."<br>";
				 */
				$file=$_FILES["screen$num"];
				if($file['name'])
					{ // file upload (ignore delete checkbox)
					$size=$file['size'];
					if($size == 0 || $size > $MAX_FILE_SIZE)
						{
						echo "File Size of Screen $num too large ($size > "; 
						printf("%.0f kByte", $MAX_FILE_SIZE/1024);
						echo ")<br>";
						continue;	// ignore
						}
					$path=pathinfo("dir/".$file['name']);
					$suffix=strtolower($path['extension']);
					if(!($suffix == "jpg" || $suffix == "png" || $suffix == "gif"))
						{
						echo "File Type of Screen $num unexpected ($suffix <> .jpg, .png, .gif)<br>"; 
						continue;
						}
					$slot=0;	// should find unused number!
					while(getscreenshot($id, -1, $slot))
						$slot++;	// find first free slot
					$destfilename=sprintf("%05d", $id)."_".$slot.".".$suffix;
					$destfilepath=dirname(__FILE__)."/screenshots/".$destfilename;
	//		echo $destfilename;
					if(move_uploaded_file($file['tmp_name'], $destfilepath))
						requestchange($id, "screen$num", $slot+1, $_POST['reason']);
					else
						echo "Upload error for Screen $num: ".$file['error']."<br>";
					}
				else if($_POST["delete$num"] == "on")
					requestchange($id, "screen$num", 0, $_POST['reason']);
				}
			requestchange($id, 'approved', 1, $_POST['reason']);
			echo "<b>Change requests queued for review and approval.</b>\n";
			if(manage())
				echo "<a href=\"showchanges.php?app=$id\">Approve</a>\n";
			}
		}
	else
		{ // store a new record (i.e. different version number)
		$query="select name from ${DB_TABLE} where name=".quote($_POST['name']);
		$result=query($query);
		if(($_POST['baseid']+0) == 0 && $row=mysql_fetch_array($result))
			{ // prohibit duplicates by creating a new record (and not through change request)
			mysql_free_result($result);
			echo "<b>There is already an application named ".htmlentities($_POST['name']).". Please go back and modify.</b>";
			}
		else
			{
			mysql_free_result($result);
			$query="insert into ${DB_TABLE} (added, name, summary, license, version, rom, model, maturity, price,";
			$query.=" downloadurl, homepageurl, feedurl, sourceurl, author, description, creator,";
			$query.=" category1, category2, category3, category4";
			$query.=") values (";
				// FIXME - should be publication date that defaults to now if not specified
			$query.="now(), ";
			$query.=quote($_POST['name']).", ";
			$query.=quote($_POST['summary']).", ";
			$query.=quote($_POST['license']).", ";
			$query.=quote($_POST['version']).", ";
			$query.=quote($_POST['rom']).", ";
			$query.=quote($_POST['model']).", ";
			$query.=quote($_POST['maturity']).", ";
			$query.=quote($_POST['price']).", ";
			$query.=quote($_POST['downloadurl']).", ";
			$query.=quote($_POST['homepageurl']).", ";
			$query.=quote($_POST['feedurl']).", ";
			$query.=quote($_POST['sourceurl']).", ";
			$query.=quote($_POST['author']).", ";
			$query.=quote($_POST['description']).", ";
			$query.=quote($_POST[loginname()]).", ";
			if($_POST['category1'] != '---')
				$query.=quote($_POST['category1']).", ";
			else
				$query.="'', ";
			if($_POST['category2'] != '---')
				$query.=quote($_POST['category2']).", ";
			else
				$query.="'', ";
			if($_POST['category3'] != '---')
				$query.=quote($_POST['category3']).", ";
			else
				$query.="'', ";
			if($_POST['category4'] != '---')
			$query.=quote($_POST['category4']).")";
			else
				$query.="'')";
			query($query);
			$query="select id from ${DB_TABLE} where name=".quote($_POST['name'])." and version=".quote($_POST['version']);	// id of new version
			$result=query($query);
			$row=mysql_fetch_array($result);
			$id=$row['id'];
			mysql_free_result($result);
			requestchange($id, 'approved', 1, $_POST['reason']);
			for($num=0; $num <= 4; $num++)
				{ // process uploads
				/*
				echo "screen$num: ",
				 print_r($_FILES["screen$num"]);
				 echo " ".$_FILES["screen$num"]['name'];
				 echo " ".$_FILES["screen$num"]['size'];
				 echo "<br>";
				 echo "delete$num: ".$_POST["delete$num"]."<br>";
				 */
				$file=$_FILES["screen$num"];
				if($file['name'])
					{ // file upload
					$size=$file['size'];
					if($size == 0 || $size > $MAX_FILE_SIZE)
						{
						echo "File Size of Screen $num too large ($size > "; 
						printf("%.0f kByte", $MAX_FILE_SIZE/1024);
						echo ")<br>";
						continue;	// ignore
						}
					$path=pathinfo("dir/".$file['name']);
					$suffix=strtolower($path['extension']);
					if(!($suffix == "jpg" || $suffix == "png" || $suffix == "gif"))
						{
						echo "File Type of Screen $num unexpected ($suffix <> .jpg, .png, .gif)<br>"; 
						continue;
						}
					$slot=$num;
					$destfilename=sprintf("%05d", $id)."_".$slot.".".$suffix;
					$destfilepath=dirname(__FILE__)."/screenshots/".$destfilename;
		//			echo $destfilename;
					if(move_uploaded_file($file['tmp_name'], $destfilepath))
						requestchange($id, "screen$num", $slot+1, $_POST['reason']);
					else
						echo "Upload error for Screen $num: ".$file['error']."<br>";
					}
				else if(($_POST['baseid']+0) != 0)
					{ // copy existing
					$srcfilepath=getscreenshot(($_POST['baseid']+0), $num);
					if($srcfilepath)
						{ // exists
						$srcfilepath=dirname(__FILE__)."/".$srcfilepath;	// make absolute
						$path=pathinfo($srcfilepath);
//						echo $path["dirname"] . " - ";
//						echo $path["basename"] . " - ";
//						echo $path["extension"] . " - ";
						$suffix=strtolower($path['extension']);
						$slot=$num;
						$destfilename=sprintf("%05d", $id)."_".$slot.".".$suffix;
						$destfilepath=dirname(__FILE__)."/screenshots/".$destfilename;
						if(!copy($srcfilepath, $destfilepath))
							echo "<b>Failed to copy $srcfilepath to $destfilepath.</b>\n";
						requestchange($id, "screen$num", $slot+1, "copied");
						}
					}
				}
			if(($_POST['baseid']+0) != 0)
				{
				echo "<b>New version queued for approval.</b>\n";
				}
			else
				echo "<b>Application queued for approval.</b>\n";
			echo "<a href=\"showdetail.php?app=$id\">Show</a>\n";
			if(manage())
				echo "<a href=\"showchanges.php?app=$id\">Approve</a>\n";
			}
		}
}
else if($_POST['baseid'] && $_POST['change'] == "approve")
{
	// change auf approved setzen
	echo "missing implementation";
}
else
{
	if($_GET['change'])
		{
		$id=($_GET['change']+0);
		$query="select * from ${DB_TABLE}";
		$query.=" where id=".$id;
		$query.=" order by updated";	// latest submitted version
		$query.=" limit 1";				// single record only
		
		$result=query($query);
		$row=mysql_fetch_array($result);
		mysql_free_result($result);
		}
	else
		$row=array();
	?>
		<FORM method="POST" enctype="multipart/form-data" accept-charset="iso-8859-1">
		<TABLE align=center>
        <TBODY>
        <TR>
		<TD vAlign=top><B>Note:</B> </TD>
		<TD><B>Stop submitting anything that is not related to 
		this topic in any way. It will be deleted.</B>
		
		</TD></TR>
        <TR>
		<TD colSpan=2>
		<P></P></TD></TR>
		<?php
		if($_GET['change'])
			{
			?>
			<TR>
			<TD>Reason/Comment</TD>
			<TD><INPUT size="80" value="" name="reason">
			</TR>
			<?php
			}
		?>
        <TR>
		<TD>Name</TD>
		<TD><INPUT size="30" value="<?php echo htmlentities($row['name']);?>" name="name"> (please do not include version number here!)</TD>
		</TR>
        <TR>
		<TD>Short Description</TD>
		<TD><INPUT size="80" value="<?php echo htmlentities($row['summary']);?>" name="summary">

		<!-- Release date should be an input field! -->
		<INPUT type="hidden" value="<?php echo htmlentities($row['added']);?>" name="added">

		<!-- Publication date -->
		<INPUT type="hidden" value="<?php echo htmlentities($row['updated']);?>" name="updated">
		<INPUT value="ignore" name="change" type="hidden">
		<INPUT type="hidden" value="<?php echo htmlentities($id);?>" name="baseid"> </TD></TR>
		<INPUT type="hidden" value="12345" name="ignore">

		<?php
			//	$query="select category from ${DB_TABLE}_categories order by category asc";
			//	$result=query($query);
				$categories=array("---");
				$categories=array_merge($categories, $CATEGORIES_MENU);
				//			print_r($categories);
			//	while($r=mysql_fetch_array($result))
			//		array_push($categories, $r['category']);	// make list of categories
				//			print_r($categories);
			//	mysql_free_result($result);
				
				for($i=1; $i<=4; $i++)
					{ // create 4 fields to store categories 1-4
					echo "<tr>\n";
					echo "<td>Category$i</td>";
					echo "<td>";
						selectionlist("category$i", $categories, $row["category$i"]);
					echo "</td>";
					echo "</tr>\n";
					}
				?>
					<TR>
					<TD>License</TD>
					<TD>
					<?php 
					selectionlist("license", $LICENSE_MENU, $row['license']);
				?>
					</TD>
					</TR>
					<TR>
					<TD>Version # </TD>
					<TD><INPUT size="55" name="version" value="<?php echo htmlentities($row['version']);?>"> 
					<INPUT type="checkbox" name="edit-version" value="edit"> (edit typo in version number without creating a new version) </TD>
					</TR>
					<TR>
					<TD>Operating system</TD>
					<td>
					<?php 
					selectionlist("rom", $ROM_VARIANTS, $row['rom']);
					?>
					</td>
					</tr>
					<TR>
					<TD>Device Models</TD>
					<TD><INPUT size="55" name="model" value="<?php echo htmlentities($row['model']);?>"> </TD></TR>
						<TR>
						<TD>Maturity Level</TD>
						<td>
						<?php 
						selectionlist("maturity", array("",
												   "beta", 
												   "alpha",
												   ), $row['maturity']);
					?>
						</td>
						</tr>
						<tr>
					<TD>Price</TD>
					<TD><INPUT size="15" name="price" value="<?php echo htmlentities($row['price']);?>"> US$ (approx.)  </TD></TR>
						<TR>
						<TD>Author</TD>
						<TD><INPUT size="55" 
						name="author"
						value="<?php echo htmlentities($row['author']);?>"> </TD>
						</TR>
						<TR>
					<TD>Homepage URL</TD>
					<TD><INPUT size="55" 
					name="homepageurl"
					value="<?php echo htmlentities($row['homepageurl']);?>"> </TD>
					</TR>
						<TR>
						<TD>Feed URL</TD>
						<TD><INPUT size="55" 
						name="feedurl"
						value="<?php echo htmlentities($row['feedurl']);?>"> </TD>
						</TR>
						<TR>
					<TD>Download URL </TD>
					<TD><INPUT size="55" value="<?php echo htmlentities($row['downloadurl']);?>" name="downloadurl"> </TD></TR>
					<TR>
					<TD>Source URL </TD>
					<TD><INPUT size="55" name="sourceurl" value="<?php echo htmlentities($row['sourceurl']);?>"> </TD>
					</TR>
					<TR>
					<TD>Long Description </TD>
					<TD>
					Use &lt;br&gt;, &lt;br/&gt;, &lt;li&gt; or &lt;p&gt; to switch to HTML only mode.<br>
					<TEXTAREA name="description" rows="12" cols="55"><?php echo htmlentities($row['description']);?></TEXTAREA> 
					</TD>
					</TR>

						<tr>
						<td>
						<a name="Screenshots">Screenshots
						<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $MAX_FILE_SIZE;?>"/>
						</td>
						<td>
						<table border=1>
					<?php					
					echo "<tr>";
					for($i=0; $i<=4; $i++)
						{ // 5 screen shots
						echo "<td width=20%>";
						$file=getscreenshot($id, $i);
						if($file)
							echo "<img width=64 height=48 src=\"$file\"/>\n";
						else
							echo "no image.";
						echo "</td>";
						}
						?>
							</tr>
							<tr>
						<?php
						for($i=0; $i<=4; $i++)
							{
							echo "<td>";
							echo "<input type='file' name='screen$i' accept='image/gif,image/jpeg,image/png'>";
							$file=getscreenshot($id, $i);
							if($file)
								{ // exists
								echo "<input type='checkbox' name='delete$i'>Delete</input>";
								}
							if($i == 0)
								{
								?>
								<br>Please select screenshot files on your computer.
								<ul>
								<li>Supported formats: png, jpg, or gif
								<li>Max. <?php printf("%.0f kByte", $MAX_FILE_SIZE/1024);?>
								<li>All submissions will be reviewed before they become visible to the public.
								<li>You agree that you take all repsonsibility for the copyright of submissions.
								</ul>
								<?php
								}
							echo "</td>";
							}
						?>

						</tr>
						</table>
						</td>
						</tr>
					
					<TR>
					<TD colSpan=2>
					<TABLE>
					<TBODY>
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
					</TD>
					<TD>
					<INPUT size="7" name="image"> (Please retype the number you see to the left after subtracting two)</TD></TR>
					</TBODY></TABLE>
					</TD></TR>
					<TR>
					<TD colSpan=2><INPUT type="submit" value="Submit Entry" name="submit"> 
					</TD></TR></TBODY></TABLE></FORM></TD></TR></TBODY></TABLE><BR>
					<?php
}
?>

<?php
include "include/footer.inc.php";
?>
