<?php

// add a table for ignoring user agents
$_SQL[] = "CREATE TABLE IF NOT EXISTS {$_TABLES['gus_ignore_ua']} (
  ua VARCHAR(128) NOT NULL default '',
  PRIMARY KEY (ua)
)";

// add a table for ignoring hosts
$_SQL[] = "
CREATE TABLE IF NOT EXISTS {$_TABLES['gus_ignore_host']} (
  host VARCHAR(128) NOT NULL default '',
  PRIMARY KEY (host)
)";

$_SQL[] = "UPDATE {$_TABLES['gus_ignore_ip']} SET ip = CONCAT( ip, '%' ) WHERE RIGHT(ip, 1) = '.'";

function GUS_UPD_fix_pages() {
	global $_CONF, $_TABLES;

	$num = preg_match("/^http:\/\/[^\/]+\/?(.*)/i", $_CONF['site_url'], $match);
	$extra_path = ($num AND ($match[1] != '')) ? $match[1] . '/' : '';
	
	if ($extra_path != '') {
		$sql = "UPDATE {$_TABLES['gus_userstats']}
					SET page = REPLACE( page, '{$extra_path}', '' )
					WHERE SUBSTRING( page FROM 1 FOR LENGTH('{$extra_path}') ) = '{$extra_path}'";
		$res = DB_query($sql);
	}
}
