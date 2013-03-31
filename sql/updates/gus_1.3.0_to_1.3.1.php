<?php

// update user agents table for more accurate reporting

$_SQL[] = "UPDATE {$_TABLES['gus_user_agents']} SET platform='Windows 2000' WHERE platform = 'Windows NT 5.0'";
$_SQL[] = "UPDATE {$_TABLES['gus_user_agents']} SET platform='Windows XP' WHERE platform = 'Windows NT 5.1' OR (platform = 'Unknown' AND `user_agent` LIKE '%Windows XP%')";
$_SQL[] = "UPDATE {$_TABLES['gus_user_agents']} SET platform='Windows 2003' WHERE platform = 'Windows NT 5.2'";
$_SQL[] = "UPDATE {$_TABLES['gus_user_agents']} SET platform='Windows Vista' WHERE platform = 'Windows NT 6.0' OR platform = 'Windows Longhorn'";
$_SQL[] = "UPDATE {$_TABLES['gus_user_agents']} SET platform='Mac OS X' WHERE platform = 'MacOS X'";
$_SQL[] = "UPDATE {$_TABLES['gus_user_agents']} SET platform='SymbianOS' WHERE platform = 'Unknown' AND `user_agent` LIKE '%Symbian%'";
$_SQL[] = "UPDATE {$_TABLES['gus_user_agents']} SET platform='WebTV' WHERE platform = 'Unknown' AND `user_agent` LIKE '%WebTV%'";
$_SQL[] = "UPDATE {$_TABLES['gus_user_agents']} SET platform='[Robot]' WHERE platform = 'Unknown' AND (user_agent LIKE '%larbin%' OR user_agent LIKE '%Jeeves%' OR user_agent LIKE '%Spider%')";
