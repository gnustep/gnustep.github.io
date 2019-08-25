<?php

$SERVER="softwareindex";	// link where SWI resides on the host server

// database connection

$DB_HOST="mysql.gnustep.org";
$DB_DATABASE="gnustep";
$DB_USER="gso_mya";
$DB_PASSWORD="wjtcxr3b";
$DB_TABLE="swi_gs";

// general config

$URL="http://www.gnustep.org/softwareindex";		// URL where SWI resides
$TITLE="GNUstep Software Index";		// the TITLE
$FROM="swi@gnustep.org";				// where mails originate
$MAIN="<h1><img src=\"http://www.gnustep.org/images/AlternateGNUstepLogo.png\"><br/>Software<br/>Index</h1>";
$CONTENT="<b>The GNUstep Software Index</b>";
$KEYWORDS="GNUstep";

$MAX_FILE_SIZE=100*1024;

// bottom line

$CONTACT="Comments: <a href=\"mailto:$FROM\">$FROM</a>";
$IMPRESSUM="<a href=\"http://www.gnustep.org/index.html\">GNUstep Home</a>";
$HINT="<a href=\"http://wiki.gnustep.org\">GNUstep Wiki</a>";

	$ROM_VARIANTS=array("Any"
											//"---",
											//"Other",
											);
	
	$MODEL_VARIANTS=array("Any"
												//"---",
												//"Other",
												);

	$CATEGORIES_MENU=array("Application",
						   "Commercial",
						   "Developer Tool",
						   "Distribution",
						   "Framework",
						   "Game",
						   "Preference",
						   "System Tool",
						   "Tool"
						  );
	
	$LICENSE_MENU=array("none/unknown",
						"GPL",
						"LGPL", 
						"BSD", 
						"X11",
						"Commercial",
						"Shareware", 
						"Public", 
						"Other"
						);

?>
