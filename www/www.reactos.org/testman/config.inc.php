<?php
/*
  PROJECT:    ReactOS Web Test Manager
  LICENSE:    GNU GPLv2 or any later version as published by the Free Software Foundation
  PURPOSE:    Configuration settings for the Web Interface
  COPYRIGHT:  Copyright 2008-2009 Colin Finck <colin@reactos.org>
*/

	define("ROOT_PATH", "../");
	define("ROSCMS_PATH", ROOT_PATH . "roscms/");
	define("SHARED_PATH", ROOT_PATH . "shared/");
	define("TESTMAN_PATH", "");
	
	define("DEFAULT_SEARCH_LIMIT", 10);
	define("DEFAULT_SEARCH_USER", "Debug-Buildslave");
	define("MAX_COMPARE_RESULTS", 5);
	define("RESULTS_PER_PAGE", 100);
	define("VIEWVC_TRUNK", "http://svn.reactos.org/svn/reactos/trunk");
?>
