<?php
	
	include "include/version.inc.php";
	
	
	global $ROM_VARIANTS;
	global $MODEL_VARIANTS;
	global $LICENSE_MENU;
	global $CATEGORY_MENU;

	// load machine specific configuration

	require "config/".$_SERVER['SERVER_NAME']."-".$_SERVER['SERVER_PORT'].".php";
	
	// set defaults
	
	if(!$ROM_VARIANTS)
		$ROM_VARIANTS=array("Any");
	
	if(!$MODEL_VARIANTS)
		$MODEL_VARIANTS=array("Any");
	
	if(!$CATEGORIES_MENU)
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
	
	if(!$LICENSE_MENU)
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
