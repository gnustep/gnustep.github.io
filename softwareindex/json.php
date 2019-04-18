<?php
	
	require "include/connect.inc.php";
	
	/*
	 
	 JSON schreiben so dass man sie lesen kann
	 
	 arguments:
	 all=x						show all (even if not yet approved)
	 since="2005-12-12"		filter entries older than date
	 rom=xxx					filter by ROM
	 appid=xxx				specified app only
	 limit=xxx				specify limit (default = 100)
	 
	 result is an NSArray filled with NSDictionaries where database columns are the keys
	 
	 */
	header("Content-Encoding: text/json");
	header("Cache-Control: no-transform", false);
	echo "[\n";	// outer array
	$query="select *, DATE_FORMAT(updated, '%e %b %Y') as upd from ${DB_TABLE}";
	if($_GET['all'])
		$query.=" where approved=approved";	// any...
	else
		$query.=" where approved=1";
	if($_GET['rom'])
		$query.=" and rom=".quote($_GET['rom']);
	if($_GET['appid'])
		$query.=" and id=".quote($_GET['appid']);
	if($_GET['since'])
		$query.=" and updated >= ".quote($_GET['since']);
	$query.=" order by updated desc";
	if($_GET['limit'])
		$query.=" limit ".quote($_GET['limit']);
	else
		$query.=" limit 100";
	// echo $query;
	$result=query($query);
	

	while($row=mysql_fetch_array($result))
	{
		echo "  {\n";
		reset($row);
		while(list($key, $value) = each($row)) 
				{
					if(is_numeric($key))
						continue;	// skip 1,2,3 etc.
					if(!$value)
						continue;	// omit empty fields
					if($translate[$key])
						$key=$translate[$key];
				// CHECKME: does this correctly generate quotes?
					echo "  \"".htmlspecialchars($key, ENT_NOQUOTES)."\" :";
					echo " \"".htmlspecialchars($value, ENT_NOQUOTES)."\",\n";
				} 
		echo "  },\n";
	}
	mysql_free_result($result);
	
	echo "]\n";
	?>
