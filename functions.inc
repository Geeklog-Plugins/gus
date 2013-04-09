<?php

// +---------------------------------------------------------------------------+
// | GUS Plugin                                                                |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// | This file does two things: 1) it implements the necessary Geeklog Plugin  |
// | API method and 2) implements all the common code needed by the stats      |
// | PHP files.                                                                |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2002, 2003, 2005 by the following authors:                  |
// |                                                                           |
// | Authors: Andy Maloney      - asmaloney@users.sf.net                       |
// |          Tom Willett       - twillett@users.sourceforge.net               |
// |          John Hughes       - jlhughes@users.sf.net                        |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU General Public License               |
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+

$langfile = $_CONF['path'] . 'plugins/gus/language/' . $_CONF['language'] . '.php';

if (file_exists($langfile)) {
	require_once $langfile;
} else {
	require_once $_CONF['path'] . 'plugins/gus/language/english.php';
}

require_once $_CONF['path'] . 'plugins/gus/config.php';

///////////////////////////////////////////////////////////////////
///  Set up config variables and call to add stats to database  //
/////////////////////////////////////////////////////////////////

GUS();

function GUS() {
	global $_GUS_VARS, $_GUS_CONF, $_USER, $_TABLES, $_CONF;
	
	$_GUS_VARS = array();
	$rec = DB_query("SELECT * FROM {$_TABLES['gus_vars']}", 1);
	
	if (DB_numRows($rec) > 0) {
		// Get the GUS vars from the DB
		while ($row = DB_fetchArray($rec, FALSE)) {
			$_GUS_VARS[$row['name']] = $row['value'];
		}
		
		// store the MySQL version number in $_GUS_VARS['sql_version']
		$version = DB_getVersion();
		$_GUS_VARS['sql_version'] = array();
		
		if (preg_match("/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/", $version, $match)) {
			$_GUS_VARS['sql_version']['major'] = $match[1];
			$_GUS_VARS['sql_version']['minor'] = $match[2];
			$_GUS_VARS['sql_version']['patch'] = $match[3];
		}
		
		// save our parsed url for path info later on
		$_GUS_VARS['parsed_site_url'] = parse_url($_CONF['site_url']);
		
		if ($_GUS_VARS['capture'] == '1') {
			// First check the IP...
			$_GUS_VARS['remote_ip'] = GetIP($_SERVER['REMOTE_ADDR']);
			$rec = DB_query("SELECT COUNT(*) AS ignored
								FROM {$_TABLES['gus_ignore_ip']}
								WHERE '{$_GUS_VARS['remote_ip']}' LIKE ip
								LIMIT 1", 1);
			$row = DB_fetchArray($rec, FALSE);
			
			if ($row['ignored'] == '1') {
				return;
			}

			// Next check the hostname...
			$host_name = ip_to_hostname($_GUS_VARS['remote_ip']);
			$rec = DB_query("SELECT COUNT(*) AS ignored
								FROM {$_TABLES['gus_ignore_host']}
								WHERE '{$host_name}' LIKE host
								LIMIT 1", 1);
			$row = DB_fetchArray($rec, FALSE);

			if ($row['ignored'] == '1') {
				return;
			}
			
			// Next check the page...
	
			// remove extra path from the page name if necessary
			$page = $_SERVER['SCRIPT_NAME'];
			$len = strlen($_GUS_VARS['parsed_site_url']['path']);
			
			if (strncmp($page, $_GUS_VARS['parsed_site_url']['path'], $len) === 0) {
				$page = substr($page, $len + 1);
			}

			$rec = DB_query("SELECT COUNT(*) AS ignored
								FROM {$_TABLES['gus_ignore_page']}
								WHERE '{$page}' LIKE page
								LIMIT 1", 1);
			$row = DB_fetchArray($rec, false);
			
			if ($row['ignored'] == '1') {
				return;
			}
			
			// check the user...
			$check_user = TRUE;
			
			if (!isset($_USER['uid']) OR ($_USER['uid'] < 2)) {
				$user_id    = 1; 
				$user_name  = 'Anonymous';
				$check_user = $_GUS_CONF['allow_ignore_anonymous'];
			} else {
				$user_id    = $_USER['uid'];
				$user_name  = $_USER['username'];
			}
			
			if ($check_user) {
				$rec = DB_query("SELECT COUNT(*) AS ignored
									FROM {$_TABLES['gus_ignore_user']}
									WHERE '{$user_name}' LIKE username
									LIMIT 1", 1);
				$row = DB_fetchArray($rec, FALSE);
				
				if ($row['ignored'] == '1') {
					return;
				}
			}
			
			// check user agent
			$user_agent = addslashes(substr($_SERVER['HTTP_USER_AGENT'], 0, 128));
			$rec = DB_query("SELECT COUNT(*) AS ignored
								FROM {$_TABLES['gus_ignore_ua']}
								WHERE '{$user_agent}' LIKE ua
								LIMIT 1", 1);
			$row = DB_fetchArray($rec, FALSE);

			if ($row['ignored'] == '1') {
				return;
			}

			// check the referrer
			if (isset($_SERVER['HTTP_REFERER']) AND ($_SERVER['HTTP_REFERER'] != '')) {
				$referrer = $_SERVER['HTTP_REFERER'];
				$rec = DB_query("SELECT COUNT(*) AS ignored
									FROM {$_TABLES['gus_ignore_referrer']}
									WHERE '{$referrer}' LIKE referrer
									LIMIT 1", 1);
				$row = DB_fetchArray($rec, FALSE);
				
				if ($row['ignored'] == '1') {
					return;
				}
			}
			
			// Finally, add stats
			GUS_addStats($page, $user_id, $user_name, $host_name, $user_agent);
		}
	}
}

// adapted from code found here: http://www.php.net/getenv
function GetIP($ip) {
// commented out because it seems to give misleading results sometimes
/*
	if ( $_SERVER["HTTP_CLIENT_IP"] && strcasecmp($_SERVER["HTTP_CLIENT_IP"], "unknown") )
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	else if ( $_SERVER["HTTP_X_FORWARDED_FOR"] && strcasecmp($_SERVER["HTTP_X_FORWARDED_FOR"], "unknown") )
		$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
*/	
	// IF more than one IP, grab the first
	if (strpos($ip, ', ') !== FALSE) {
		$ips = explode(', ', $ip);
		$ip = $ips[0];
	}
	
	return $ip;
}

// adapted from code found here: http://www.php.net/gethostbyaddr
function ip_to_hostname($ip) {
	global $_GUS_CONF;
	
	switch ($_GUS_CONF['host_lookup']) {
		case 'host':
			$timeout = $_GUS_CONF['host_lookup_timeout'];
			$output = `host -W {$timeout} {$ip}`;
			
			if (preg_match('/.*pointer ([A-Za-z0-9.-]+)\..*/', $output, $regs)) {
				return $regs[1];
			}
			
			break;
			
		case 'nslookup':
			$timeout = $_GUS_CONF['host_lookup_timeout'];
			$res = `nslookup -timeout=$timeout $ip`;
			
			if (preg_match('/\nName:(.*)\n/', $res, $out)) {
				return trim($out[1]);
			}
				
			break;
			
		case 'gethostbyaddr':
			return @gethostbyaddr($ip);
			break;
			
		default:
			COM_errorLog("Invalid setting for 'host_lookup' in config.php [{$_GUS_CONF['host_lookup']}]");
			break;
	}

	return $ip;
}

// +---------------------------------------------------------------------------+
// | Geeklog Plugin API Implementations                                        |
// +---------------------------------------------------------------------------+

/**
 * Returns the current plugin code version
 * 
 * @return string    plugin version
*/
function plugin_chkVersion_gus() {
	return '1.7.2';
}

// Add the CSS file if we are browsing a GUS page
function plugin_getheadercode_gus() {
	global $_CONF;
	
	$this_dir = basename(dirname($_SERVER['SCRIPT_NAME']));

	if ($this_dir !== 'gus') {
		return '';
	}
	
	$header_text = '';

	// check for CSS file specific to this theme
	$css_file = 'css/' . $_CONF['theme'] . '.css';
	$css_path = $_CONF['path_html'] . 'gus/' . $css_file;
	
	// IF we don't find one that's readable THEN use the default 	
	if (!is_readable($css_path)) {
		$css_file = 'css/default.css';
		$css_path = $_CONF['path_html'] . 'gus/' . $css_file;
		
		// IF we still don't have anything THEN return
		if (!is_readable($css_path)) {
			return '';
		}
	}
	
	$stylesheet = $_CONF['site_url'] . '/gus/' . $css_file;

	$header_text .= '<link rel="stylesheet" type="text/css" href="' . $stylesheet
				 .  '" title="' . $_CONF['theme'] . '"' . XHTML . '>';
	
	if (strpos($_SERVER['SCRIPT_NAME'], 'admin/plugins/gus/index.php') !== FALSE) {
		$header_text .= '<script type="text/javascript" src="./Tab.js"></script>';
	}
	
	return $header_text;
}

/**
* Returns the items for this plugin that should appear on the main menu
*
* NOTE: this MUST return the url/value pairs in the following format
* $<arrayname>[<label>] = <url>
*
*/
function plugin_getmenuitems_gus() {
	global $_CONF, $LANG_GUS00, $_GUS_anon_access, $_GUS_enable_main_menu_GUS,
		   $_GUS_enable_main_menu_privacy_policy;
	
	$menuitems = array();
	
	if ($_GUS_enable_main_menu_GUS AND ($_GUS_anon_access OR SEC_hasRights('gus.view'))) {
		$tmp = $LANG_GUS00['main_menu_title'];
		$menuitems[$tmp] = $_CONF['site_url'] . '/gus/index.php';
	}
	
	if ($_GUS_enable_main_menu_privacy_policy == 1) {
		$tmp = $LANG_GUS00['priv_pol'];
		$menuitems[$tmp] = $_CONF['site_url'] . '/gus/privpol.php';
	}
	
	return $menuitems;
}

/**
* Shows the statistics for the stats plugin on stats.php.  If
* $showsitestats is 1 then we are to only print the overall stats in the 'site
* statistics' box otherwise we show the detailed stats for the stats
*
* @showsitestate        int         Flag to let us know which stats to get
*/
function plugin_showstats_gus($showsitestats) {
	global $_TABLES, $LANG_GUS_builtin_stats, $_GUS_stats;
	
	if (($showsitestats == 1) AND ($_GUS_stats == 1)) {
		$rec = DB_query("SELECT COUNT( DISTINCT ip ) AS visitors FROM {$_TABLES['gus_userstats']}");
		$row = DB_fetchArray($rec, FALSE);
		$retval = "<table border = '0' width='100%' cellspacing='0' cellpadding='0'>"
				. "<tr><td>" . $LANG_GUS_builtin_stats['unique_visitors'] . "</td>"
				. "<td align='right'>" . $row['visitors'] . "&nbsp;&nbsp </td></tr>";
		
		$total_stats = DB_count($_TABLES['users']);
		$retval .= "<tr><td>" . $LANG_GUS_builtin_stats['users'] . "</td>"
				.  "<td align='right'>" . $total_stats . "&nbsp;&nbsp </td></tr></table>";
	} else {
		$retval = '';
	}
	
	return $retval;
}

/**
* This will put an option for stats in the command and control block on
* moderation.php
*
*/
function plugin_cclabel_gus() {
	global $LANG_GUS00, $_CONF, $_GUS_IMG_name;
	
	$retval = array();
	
	if (SEC_hasRights('gus.admin')) {
		$retval = array(
			$LANG_GUS00['GUS_title'],
			$_CONF['site_admin_url'] . '/plugins/gus/index.php',
			$_CONF['site_url'] . '/gus/images/' . $_GUS_IMG_name
		);
	}
	
	return $retval;
}

/**
* returns the administrative option for this plugin
*
*/
function plugin_getadminoption_gus() {
	global $LANG_GUS_blocks, $_CONF;

	if (SEC_hasRights('gus.admin')) {
		return array($LANG_GUS_blocks['admin_menu_title'], $_CONF['site_admin_url'] . '/plugins/gus/index.php', 0);
	}
}

/**
* returns the user option for this plugin
*
*/
function plugin_getuseroption_gus() {
	global $LANG_GUS_blocks, $_CONF, $_GUS_user;

	if (SEC_hasRights('gus.view') AND ($_GUS_user == 1)) {
		$title = $LANG_GUS_blocks['user_menu_title'];

		// Micro-hack:
		//	We're only supposed to return one thing, but we close the anchor and open another
		//	in order to return two...
		$title .= '</a> <a href="' . $_CONF['site_url'] . '/gus/day.php' . '">['
			   .  $LANG_GUS_blocks['today'] . ']';

		return array($title, $_CONF['site_url'] . '/gus/index.php?page=latest', 0);
	}
}

// upgrade to this version of GUS
function plugin_upgrade_gus() {
	global $_TABLES;
	
	$res = DB_query("SELECT pi_version FROM {$_TABLES['plugins']} WHERE pi_name = 'gus'");
	$row = DB_fetchArray($res, FALSE);
	
	COM_errorLog("Updating GUS plugin from v" . $row['pi_version'] . ' to v' . plugin_chkVersion_gus(), 1);
	
	return GUS_doUpgrades($row['pi_version']);
}

function GUS_doUpgrades($current_gus_version) {
	global $_TABLES, $_CONF, $_DB;

	$_DB->setDisplayError(TRUE);
	$done = FALSE;

	while ($done == FALSE) {
		switch ($current_gus_version) {
			case '1.0.0':
			case '1.1.0':
			case '1.2.0':
				$current_gus_version = '1.3.0';
				break;
				
			case '1.3.0':
				require_once $_CONF['path'] . 'plugins/gus/sql/updates/gus_1.3.0_to_1.3.1.php';

				foreach ($_SQL as $sql) {
					DB_query($sql);
				}

				$current_gus_version = '1.3.1';
				break;
			
			case '1.3.1':
			case '1.3.2':
				$current_gus_version = '1.3.3';
				break;
				
			case '1.3.3':
				$_SQL = array();
				
				require_once $_CONF['path'] . 'plugins/gus/sql/updates/gus_1.3.3_to_1.4.0.php';

				foreach ($_SQL as $sql) {
					DB_query($sql);
				}
				
				GUS_UPD_fix_pages();
				$current_gus_version = '1.4.0';
				break;
				
			case '1.4.0':
				$_SQL = array();
				
				require_once $_CONF['path'] . 'plugins/gus/sql/updates/gus_1.4.0_to_1.5.0.php';

				foreach ($_SQL as $sql) {
					DB_query($sql);
				}
								
				$current_gus_version = '1.5.0';
				break;

			case '1.5.0':
			case '1.6.0':
			case '1.7.0':
				$_SQL = array();
				
				require_once $_CONF['path'] . 'plugins/gus/sql/updates/gus_1.7.0_to_1.7.1.php';

				foreach ($_SQL as $sql) {
					DB_query($sql);
				}
			    
				$current_gus_version = '1.7.1';
				break;

			case '1.7.1':
				$current_gus_version = '1.7.2';
				break;
				
			default:
				$done = TRUE;
				break;
		}
	}

	DB_query("UPDATE {$_TABLES['plugins']} SET pi_version = '{$current_gus_version}' WHERE pi_name = 'gus'");
	
	return TRUE;
}

/**
* Removes the datastructures for this plugin from the Geeklog database
*/  
function plugin_uninstall_gus() {
	global $_TABLES;

	$FEATURES = array('gus.admin', 'gus.view');
	$TABLES   = array('gus_userstats',
						'gus_user_agents',
						'gus_ignore_ip',
						'gus_ignore_user',
						'gus_ignore_page',
						'gus_ignore_ua',
						'gus_ignore_host',
						'gus_vars' 
	);

	COM_errorLog('Removing the GUS plugin...', 1);
	
	// Unregister the plugin with Geeklog
	GUS_log_and_execute("DELETE FROM {$_TABLES['plugins']} WHERE pi_name = 'gus' LIMIT 1");

	// Drop tables we created
	COM_errorLog("Removing tables", 1);
	
	foreach ($TABLES as $table) {
		$t = $_TABLES[$table];
		GUS_log_and_execute("DROP TABLE IF EXISTS {$t}");
	}

	// remove block
	// COM_errorLog("Removing gus block", 1);
	// GUS_log_and_execute("DELETE FROM {$_TABLES['blocks']} WHERE name='gus_block' LIMIT 1");

	// Remove Security for this plugin
	$grp_id = DB_getItem($_TABLES['vars'], 'value', "name = 'gus_group_id'");
	
	COM_errorLog("Removing gus admin group", 1);
	GUS_log_and_execute("DELETE FROM {$_TABLES['groups']} WHERE grp_id = {$grp_id}", 1);
	GUS_log_and_execute("DELETE FROM {$_TABLES['vars']} WHERE name = 'gus_group_id'");
	
	COM_errorLog("Removing root users from admin of gus");
	GUS_log_and_execute("DELETE FROM {$_TABLES['group_assignments']} WHERE ug_main_grp_id = {$grp_id}", 1);
	COM_errorLog("Removing group id in vars");
	GUS_log_and_execute("DELETE FROM {$_TABLES['vars']} WHERE value = {$grp_id}", 1);
	
	// Remove all the associated features -- access rights
	foreach ($FEATURES as $feature) {
		COM_errorLog("Removing feature '{$feature}' and rights", 1);
		$feat_id = DB_getItem($_TABLES['features'], 'ft_id', "ft_name = '$feature'");
		GUS_log_and_execute("DELETE FROM {$_TABLES['access']} WHERE acc_ft_id = {$feat_id}", 1);
		GUS_log_and_execute("DELETE FROM {$_TABLES['features']} WHERE ft_id = {$feat_id}", 1 );
	}

	COM_errorLog("Removing the {$_TABLES['vars']} reference to gus");
	GUS_log_and_execute("DELETE FROM {$_TABLES['vars']} WHERE value = 'gus'", 1);

	COM_errorLog('The GUS plugin has been removed', 1);
	
	return TRUE;
}

//////////////////////////////////////////////////////////////////////////////////
//  STATS Functions                                                             //
//////////////////////////////////////////////////////////////////////////////////

// Check for the 'stats' plugin, which may be installed under a different name
//	return true/false
function GUS_checkStatsInstall() {
	global	$_CONF, $_TABLES, $_DB_table_prefix, $_ST_plugin_name;
	
	if (empty($_ST_plugin_name)) {
		$_ST_plugin_name = DB_getItem($_TABLES['vars'], 'value', "name = 'stats_name'");
		
		if (empty($_ST_plugin_name)) {
			return FALSE;
		}

		// include the config file so we get the $_TABLE vars
		require_once $_CONF['path'] . 'plugins/' . $_ST_plugin_name . '/config.php';
		
		// now that we have the table names, check that the old userstats table actually exists
		$res = DB_query("SELECT uid FROM {$_TABLES['userstats']} LIMIT 1", 1);
		
		return !DB_error();
	} else {
		return TRUE;
	}
}

// Because the referrers are stored encoded, we need our URLs to look similar for SQL comparison
//	- make it look like the referrers by encoding it
//	- escape '%' for the LIKE clause
function GUS_getEncodedURL($url) {
	$encoded_site_url = urlencode(addslashes($url));
	$encoded_site_url = str_replace('%', '\%', $encoded_site_url);
	
	return $encoded_site_url;
}

/*
* Return an array with Browser type and Version
*/
function GUS_getBrowser($UA) {
	$browser = array();
	
	if (@preg_match("@opera[ /]([0-9]{1,2}.[0-9]{1,3}){0,1}@i", $UA, $match)) {
		$browser['type'] = 'Opera'; 
		$browser['ver']  = $match[1];
	} else if (preg_match("@konqueror/([\.\+[:digit:]]+)@i", $UA, $match)) {
		$browser['type'] = 'Konqueror'; 
		$browser['ver']  = $match[1];
	} else if (preg_match("@lynx/([\.\+[:digit:]]+)@i", $UA, $match)) {
		$browser['type'] = 'Lynx'; 
		$browser['ver']  = $match[1];
	} else if (preg_match("@links \(([0-9]{1,2}.[0-9]{1,3})@i", $UA, $match)) {
		$browser['type'] = 'Links'; 
		$browser['ver']  = $match[1];
	} else if (preg_match("@(Safari|Camino|Firefox|Firebird)/([\.\+[:digit:]]+)@i", $UA, $match)) {
		$browser['type'] = $match[1]; 
		$browser['ver']  = $match[2];
	} else if (preg_match("@msie ([\.\+[:digit:]]+)@i", $UA, $match)) {
		$browser['type'] = 'MSIE'; 
		$browser['ver']  = $match[1];
	} else if (preg_match("@^Mozilla/5.0 \(compatible; Yahoo\! Slurp;@i", $UA)) {
		$browser['type'] = 'Yahoo! Slurp';
		$browser['ver']  = '1.0';        
	} else if (preg_match("@netscape6/(6.[0-9]{1,3})@i", $UA, $match)) {
		$browser['type'] = 'Netscape';
		$browser['ver']  = $match[1];
	} else if (preg_match("@netscape/(7.[0-9]{1,3})@i", $UA, $match)) {
		$browser['type'] = 'Netscape'; 
		$browser['ver']  = $match[1];
	} else if (preg_match("@^mozilla/5.+ rv:([^\)]+)\)@i", $UA, $match)) {
		$browser['type'] = 'Mozilla';
		$browser['ver']  = $match[1];
	} else if (preg_match("@^mozilla/([\.\+[:digit:]]+)@i", $UA, $match)) {
		$browser['type'] = 'Netscape';
		$browser['ver']  = $match[1];
	} else if (preg_match("@^Jigsaw/([^ ]+)@i", $UA, $match)) {
		$browser['type'] = 'W3C CSS Validator';
		$browser['ver']  = $match[1];
	} else if (stripos($UA, 'w3m') !== FALSE) {
		$browser['type'] = 'w3m';
		$browser['ver']  = 'Unknown';
	} else if (preg_match("@^([^/]+)/([\.[:digit:]]+)@i", $UA, $match)) {
		$browser['type'] = strtr($match[1], '_', ' '); 
		$browser['ver']  = $match[2];
	} else {
		$browser['type'] = 'Unknown';
		$browser['ver']  = 'Unknown';
	}
	
	return $browser;
}

/*
* Return Computer Type
*/
function GUS_getComputerType($UA) {
	if (stripos($UA, 'linux') !== FALSE) {
		$return = 'Linux';
	} else if (stripos($UA, 'win32') !== FALSE) {
		$return = 'Windows';
	} else if (preg_match("@(win)([0-9]{2})@i", $UA, $match) OR
			preg_match("@(windows) ([0-9]{2})@i", $UA, $match)) {
		$return = "Windows {$match[2]}";
	} else if (preg_match("@win ?9x@i", $UA)) {
		$return = 'Windows 9x';
	} else if (stripos($UA, 'Windows XP') !== FALSE) {
		$return = 'Windows XP';
	} else if (preg_match("@(winnt)([0-9]{1,2}.[0-9]{1,2}){0,1}@i", $UA, $match)) {
		if ($match[2] === '5.1') {
			$return = 'Windows XP';
		} else if ($match[2] === '5.0') {
			$return = 'Windows 2000';
		} else if ($match[2] === '5.2') {
			$return = 'Windows 2003';
		} else if ($match[2] === '6.0') {
			$return = 'Windows Vista';
		} else {
			$return = "Windows NT {$match[2]}";
		}
	} else if (preg_match("@(windows nt) {0,1}([0-9]{1,2}.[0-9]{1,2}){0,1}@i", $UA, $match)) {
		if ($match[2] === '5.1') {
			$return = 'Windows XP';
		} else if ($match[2] === '5.0') {
			$return = 'Windows 2000';
		} else if ($match[2] === '5.2') {
			$return = 'Windows 2003';
		} else if ($match[2] === '6.0') {
			$return = 'Windows Vista';
		} else {
			$return = "Windows NT {$match[2]}";
		}
	} else if (preg_match("@mac os x@i", $UA)) {
		$return = 'Mac OS X';
	} else if (stripos($UA, 'mac') !== FALSE) {
		$return = 'MacOS';
	} else if (stripos($UA, 'darwin') !== FALSE) {
		$return = 'Darwin';
	} else if (preg_match("@(sunos) ([0-9]{1,2}.[0-9]{1,2}){0,1}@i", $UA, $match)) {
		$return = "SunOS {$match[2]}";
	} else if (preg_match("@(beos) r([0-9]{1,2}.[0-9]{1,2}){0,1}@i", $UA, $match)) {
		$return = "BeOS {$match[2]}";
	} else if (stripos($UA, 'freebsd') !== FALSE) {
		$return = 'FreeBSD';
	} else if (stripos($UA, 'openbsd') !== FALSE) {
		$return = 'OpenBSD';
	} else if (stripos($UA, 'irix') !== FALSE) {
		$return = 'IRIX';
	} else if (stripos($UA, 'os/2') !== FALSE) {
		$return = 'OS/2';
	} else if (stripos($UA, 'plan9') !== FALSE) {
		$return = 'Plan9';
	} else if ((stripos($UA, 'unix') !== FALSE) OR (stripos($UA, 'hp-ux') !== FALSE)) {
		$return = 'Unix';
	} else if (stripos($UA, 'osf') !== FALSE) {
		$return = 'OSF';
	} else if (stripos($UA, 'Symbian') !== FALSE) {
		$return = 'SymbianOS';
	} else if (stripos($UA, 'WebTV') !== FALSE) {
		$return = 'WebTV';
	} else if (preg_match("@(bot|Slurp|larbin|Jeeves|Spider)@i", $UA)) {
		$return = '[Robot]';
	} else {
		$return = 'Unknown';
	}
		  
	return $return;
}

/*
* add stats to database
*/
function GUS_addStats($page, $user_id, $user_name, $host_name, $user_agent) {
	global $_TABLES, $_GUS_VARS, $QUERY_STRING;
	
	$sql = "SELECT ua_id FROM {$_TABLES['gus_user_agents']} WHERE user_agent = '{$user_agent}' LIMIT 1";
	$res = DB_query($sql);
	
	if ($row = DB_fetchArray($res)) {
		$ua_id = $row[0];
	} else {
		$browser  = GUS_getBrowser($_SERVER['HTTP_USER_AGENT']);
		$platform = GUS_getComputerType($_SERVER['HTTP_USER_AGENT']);

		$sql = "INSERT INTO {$_TABLES['gus_user_agents']} SET
			user_agent = '{$user_agent}',
			browser = '" . $browser['type'] . "',
			version = '" . $browser['ver'] . "',
			platform = '" . $platform . "'";

		$res = DB_query($sql);
		$ua_id = DB_insertId();
	}
	
	// SPECIAL CASE: 404s fill in the query string witht the URI the user was trying to access
	if (preg_match("/^.+404\.php$/", $_SERVER['SCRIPT_NAME'])) {
		$query_string = $_SERVER['REQUEST_URI'];
	} else {
		$query_string = $_SERVER['QUERY_STRING'];
		
		// This fixes a problem reported by Tom whereby $_SERVER['QUERY_STRING'] and $QUERY_STRING are not
		//	the same [as they should be...]
		if ($query_string == '') {
			$query_string = $QUERY_STRING;
		}
	}

	if ($query_string !== '') {
		$query_string = urlencode(addslashes($query_string));
	}

	$referrer = '';
	
	if (isset($_SERVER['HTTP_REFERER']) AND ($_SERVER['HTTP_REFERER'] !== '')) {
		$referrer = urlencode(addslashes($_SERVER['HTTP_REFERER']));
	}
	
	$sql = "INSERT INTO " . $_TABLES['gus_userstats'] . " SET"
		. " uid='" . $user_id
		. "', username='" . $user_name
		. "', ip='" . $_GUS_VARS['remote_ip']
		. "', host='" . $host_name
		. "', date='" . date( "Y-m-d" )
		. "', time='" . date( "G:i:s" )
		. "', referer='" . $referrer
		. "', query_string='" . $query_string
		. "', request='" . $_SERVER['REQUEST_METHOD']
		. "', page='" . $page
		. "', ua_id='" . $ua_id . "'";
	DB_query($sql);
}

/**
* Caching Functions
*/

/* 
* Function to return cache file name for current page
*/
function GUS_cachefile() {
	global $_CONF;

	$cfile = basename($_SERVER['SCRIPT_NAME']) . '?' . $_SERVER['QUERY_STRING'];
	$cfile = str_replace(
		array('?', '&', '.', '='), '_', $cfile
	);
	$cfile = $_CONF['path_html'] . 'gus/cache/' . $cfile . '.html';
	
	return $cfile;
}

/*
* Function to return current cache file in string variable
*/
function GUS_getcache() {
	$buffer = @file_get_contents(GUS_cachefile());
	
	if ($buffer === FALSE) {
		COM_errorlog('GUS Cache File ' . GUS_cachefile() . ' not found', 1);
		$buffer = '';
	}
	
	return $buffer;
}

/*
* Function to write string to cache file
*/
function GUS_writecache($buf) {
	if (@file_put_contents(GUS_cachefile(), $buf) === FALSE) {
		COM_errorlog('Unable to open GUS Cache file ' . GUS_cachefile() . ' for writing', 1 );
	}
}

function GUS_log_and_execute($sql) {
	COM_errorLog($sql, 1);
	return DB_query($sql, 1);
}

/*
* Stats block to show whos online now, # visitors today, registered today, new today, referers today
*
* @return   string  formatted html for block
*/
function phpblock_gusstats() {
	global $_USER, $_CONF, $_TABLES, $LANG01, $LANG_GUS_wo, $_GUS_CONF;
	
	// Code borrowed from lib-common.php
	if (empty($_USER['uid']) OR ($_USER['uid'] == 1)) {
		// The following code handles anonymous users so they show up properly
		DB_query("DELETE FROM {$_TABLES['sessions']} WHERE remote_ip = '{$_SERVER['REMOTE_ADDR']}' AND uid = 1");
		// Build a useless sess_id (needed for insert to work properly)
		mt_srand(microtime(TRUE) * 1000000);
		$sess_id = mt_rand();
		$curtime = time();
		
		// Insert anonymous user session
		DB_query("INSERT INTO {$_TABLES['sessions']} (sess_id, start_time, remote_ip, uid)
					VALUES ($sess_id,$curtime,'{$_SERVER['REMOTE_ADDR']}',1)");
	}

	$expire_time = time() - $_CONF['whosonline_threshold'];
   
	// Clear out any expired sessions
	DB_query("DELETE FROM {$_TABLES['sessions']} WHERE uid = 1 AND start_time < " . $expire_time);

	// Code adapted from whose online block
	$byname   = $_GUS_CONF['wo_fullname'] ? 'fullname, username' : 'username';
	$num_anon = 0;
	$num_reg  = 0;
	$retval   = '';
	
	if ($_GUS_CONF['wo_online']) {
		$result = DB_query("SELECT DISTINCT {$_TABLES['sessions']}.uid, {$byname}, photo, showonline
				FROM {$_TABLES['sessions']},{$_TABLES['users']},{$_TABLES['userprefs']}
				WHERE {$_TABLES['sessions']}.uid <> 1
					AND {$_TABLES['users']}.uid = {$_TABLES['sessions']}.uid
					AND {$_TABLES['users']}.uid = {$_TABLES['userprefs']}.uid
					AND start_time >= $expire_time 
				ORDER BY {$byname}");

		while ($row = DB_fetchArray($result, FALSE)) {
			if ($row['showonline'] == 1) {
				if ($_GUS_CONF['wo_fullname'] AND !empty($row['fullname'])) {
					$username = $row['fullname'];
				} else {
					$username = $row['username'];
				}

				$retval .= '<a href="' . $_CONF['site_url']
						.  '/users.php?mode=profile&amp;uid=' . $row['uid'] . '">'
						.  $username . '</a>';
				
				if (($_CONF['allow_user_photo'] == 1) AND !empty($row['photo'])) {
					$retval .= '&nbsp;<a href="' . $_CONF['site_url']
							.  '/users.php?mode=profile&amp;uid=' . $row['uid'] . '">'
							.  '<img src="' . $_CONF['site_url']
							. '/gus/images/user_photo.gif" width=16 height=12 border="0" alt=""></a>';
				}
				
				$retval .= '<br>';
				$num_reg++;
			} else {
				// this user does not want to show up in Who's Online
				$num_anon++;
			}
		}

		if ($_GUS_CONF['wo_users_anonymous'] AND (empty($_USER['uid']) OR ($_USER['uid'] == 1))) {
			// note that we're overwriting the contents of $retval here
			$retval = ($num_reg > 0) ? $LANG01[112] . ': ' . $num_reg . '<br>' : '';
		}
		
		$result = DB_query("SELECT DISTINCT uid, remote_ip
								FROM {$_TABLES['sessions']}
								WHERE uid = 1");
	} else {
		$result = DB_query("SELECT DISTINCT uid, remote_ip 
								FROM {$_TABLES['sessions']}");
	}

	$num_anon += DB_numRows($result);
	$bot_text = '';
	
	if ($_GUS_CONF['wo_show_bots']) {
		$bot_list = array();
		
		while ($row = DB_fetchArray($result, FALSE)) {
			$ip = $row['remote_ip'];
			
			if (preg_match("@^216\.239\.46|^64\.68\.8|^66\.249\.(6[4-9]|[78]\d?|9[0-5])@", $ip)) {
				$bot_list[] = 'Google';
				$num_anon--;
			} else if (preg_match("@^68\.142\.(19[2-9]|2[01234]\d?|25[012345])|
						^66\.94\.(22[4-9]|2[3-4]\d?|25[0-5])|
						^216\.109\.(11[2-9]|12[0-7])|
						^206\.190\.(3[2-9]|[4-5]\d?|6[0-3])@", $ip)) {
				$bot_list[] = 'Yahoo!';
				$num_anon--;
			} else if (preg_match("@^65\.(19[2-9]|2[0-1]\d?|22[0-3])@", $ip)) {
				$bot_list[] = 'Ask Jeeves';
				$num_anon--;
			} else if (preg_match("@^72\.30\.|
						^68\.142\.(19[2-9]|2[0-5]\d?)|
						^66\.196\.(6[4-9]|[789]\d?|1[0-1]\d?|12[0-7])@", $ip)) {
				$bot_list[] = 'Inktomi';
				$num_anon--;
			} else if (preg_match("@^64.4.8.9[3-4]|^64.4.8.11[3-7]|^65.5[2-5]\.@", $ip)) {
				$bot_list[] = 'MSN Bot';
				$num_anon--;
			}
		}
		
		if (count($bot_list) > 0) {
			$bot_text .= "<table width='100%'><tr><td width='20%'><hr></td>
				<td nowrap>{$LANG_GUS_wo['bots']}</td>
				<td width='60%'><hr></td></tr></table>";
			$bots = array_unique($bot_list);
			sort($bots);
			
			foreach ($bots as $bot) {
				$bot_text .= $bot . '<br' . XHTML . '>';
			}
		}
	}
	
	if ($num_anon > 0) {
		if ($_GUS_CONF['wo_online']) {
			$retval .= $LANG01[41];
		} else {
			$retval .= $LANG01[17];
		}

		$retval .= ': ' . $num_anon . '<br' . XHTML . '>';
	}
	
	$retval .= $bot_text;

	if ($_GUS_CONF['wo_daily']) {
		$retval .= "<table width='100%'><tr><td width='20%'><hr></td>
					<td nowrap>{$LANG_GUS_wo['stats']}</td>
					<td width='60%'><hr></td></tr></table>";
		$result = DB_query("SELECT COUNT(*) AS pages FROM {$_TABLES['gus_userstats']} WHERE date = CURRENT_DATE()");
		$row = DB_fetchArray($result, FALSE);
		$retval .= $row['pages'] . ' ' . $LANG_GUS_wo['page_title'] . '<br' . XHTML . '>';
		$result = DB_query("SELECT COUNT( DISTINCT ip ) AS visitors FROM {$_TABLES['gus_userstats']} WHERE date = CURRENT_DATE()");
		$row = DB_fetchArray($result, FALSE);
		$retval .= $row['visitors'] . ' ' . $LANG_GUS_wo['unique_visitors'] . '<br' . XHTML . '>';
	}
	
	if ($_GUS_CONF['wo_registered']) {
		$result = DB_query("SELECT DISTINCT uid FROM {$_TABLES['gus_userstats']} WHERE uid > 1 AND date = CURRENT_DATE()");
		$num_reg = DB_numRows($result);

		if ($num_reg > 0) {
			$retval .= "<table width='100%'><tr><td width='20%'><hr></td>
						<td nowrap>{$LANG_GUS_wo['reg_users']}</td>
						<td width='60%'><hr></td></tr></table>";

			if ($_GUS_CONF['wo_users_anonymous'] AND
					(empty($_USER['uid']) OR ($_USER['uid'] == 1))) {
				$retval .= $LANG01[112] . ': ' . $num_reg . '<br' . XHTML . '>';
			} else {
				while ($row = DB_fetchArray($result, FALSE)) {					
					$rec = DB_query("SELECT uid, username, fullname, photo
								FROM {$_TABLES['users']}
								WHERE uid = '" . $row['uid'] . "' LIMIT 1");
					$A = DB_fetchArray($rec, FALSE);
					$retval .= '<a href="' . $_CONF['site_url'] . '/users.php?mode=profile&amp;uid=' . $A['uid'] . '">';
					
					if ($_GUS_CONF['wo_fullname'] AND !empty($A['fullname'])) {
						$retval .= $A['fullname'];
					} else {
						$retval .= $A['username'];
					}
					
					$retval .= '</a>';
					
					if (($_CONF['allow_user_photo'] == 1) AND !empty($A['photo'])) {
						$retval .= '&nbsp;<a href="' . $_CONF['site_url'] . '/users.php?mode=profile&amp;uid=' . $A['uid'] . '">';
						$retval .= '<img src="' . $_CONF['site_url'] . '/gus/images/user_photo.gif" width=16 height=12 border="0" alt=""></a>';
					}
					
					$retval .= '<br' . XHTML . '>';
				}
			}
		}
	}
	
	if ($_GUS_CONF['wo_new']) {
		$result = DB_query("SELECT uid, username, fullname, photo FROM {$_TABLES['users']}
								WHERE regdate >= DATE_SUB( CURRENT_DATE(), INTERVAL 1 DAY )");
		$num_new = DB_numRows($result);

		if ($num_new > 0) {
			$retval .= "<table width='100%'><tr><td width='20%'><hr></td>
						<td nowrap>{$LANG_GUS_wo['new_users']}</td>
						<td width='60%'><hr></td></tr></table>";
		
			if ($_GUS_CONF['wo_users_anonymous'] AND
					(empty($_USER['uid']) OR ($_USER['uid'] == 1))) {
				$retval .= $LANG_GUS_wo['new_users'] . ': ' . $num_new . '<br' . XHTML . '>';
			} else {			
				while ($row = DB_fetchArray($result, FALSE)) {
					$retval .= '<a href="' . $_CONF['site_url'] . '/users.php?mode=profile&amp;uid=' . $row['uid'] . '">';
					
					if ($_GUS_CONF['wo_fullname'] AND !empty($row['fullname'])) {
						$retval .= $row['fullname'];
					} else {
						$retval .= $row['username'];
					}
					
					$retval .= '</a>';
					
					if (($_CONF['allow_user_photo'] == 1) AND !empty($row['photo'])) {
						$retval .= '&nbsp;<a href="' . $_CONF['site_url'] . '/users.php?mode=profile&amp;uid=' . $row['uid'] . '">';
						$retval .= '<img src="' . $_CONF['site_url'] . '/gus/images/user_photo.gif" width=16 height=12 border="0" alt=""></a>';
					}
					
					$retval .= '<br' . XHTML . '>';
				}
			}
		}
	}
	
	if ($_GUS_CONF['wo_refs']) {
		$likeClause = '';

		if (count($_GUS_CONF['wo_hide_referrers']) > 0) {
			foreach ($_GUS_CONF['wo_hide_referrers'] as $referrer) {
				$likeClause .= 'AND referer NOT LIKE \''
							.  GUS_getEncodedURL($referrer) . '%\' ';
			}
		}

		$result = DB_query("SELECT referer FROM {$_TABLES['gus_userstats']}
							WHERE referer <> ''
								AND date = CURRENT_DATE()
								$likeClause");
		$header_output = FALSE;
		
		while ($row = DB_fetchArray($result, FALSE)) {
			if (!$header_output) {
				$retval .= "<table width='100%'><tr><td width='20%'><hr></td>
					<td nowrap>{$LANG_GUS_wo['referers']}</td>
					<td width='60%'><hr></td></tr></table>";
				$header_output = TRUE;
			}
			
			$referrer = urldecode($row['referer']);
			$url = parse_url($referrer);
			
			if ($url['host'] != '') {
				$host = preg_replace('/^www\./', '', $url['host'], 1);
				
				if (($host != '') AND ($host !== $_GUS_VARS['parsed_site_url']['host'])) {
					$ref[$host] += 1;
					$refurl[$host] = htmlentities(preg_replace('/admin\/plugins.*$/', '', $referrer, 1));
				}
			}
		}
		
		if (is_array($ref)) {
			arsort($ref);
			$i = 0;
			
			foreach ($ref as $key => $value) {
				$retval .= '<a href="' . $refurl["$key"] . '" rel="nofollow">' . $key . '</a> - ' . $value . '<br>';
				$i++;
				
				if ($i >= $_GUS_CONF['wo_max_referrers']) {
					break;
				}
			}
		}
	}
	
	return $retval;
}

/*==================================================\
| This does the URLS for page navigation and header |
\==================================================*/

function GUS_create_url($ignore) {
	global $_CONF;
	
	$firstvar = TRUE;
	$qstring = '';
	
	foreach ($_GET as $key => $var) {
		if (($key != $ignore) AND ($key !== 'page')) { // always ignore page 
			if ($firstvar) {// first time through
				$qstring .= '?' . $key . '=' . $var;
				$firstvar = FALSE;
			} else {
				$qstring .= '&' . $key . '=' . $var;
			}
		}
	}
	
	$newurl = parse_url($_CONF['site_url']);
	$retval = $newurl['scheme'] . '://' . $newurl['host'] . $_SERVER['SCRIPT_NAME'] . $qstring;
	
	return htmlentities($retval);
}


/**
* Gets Geeklog blocks from plugins
*
* Returns data for blocks on a given side and, potentially, for
* a given topic.
*
* @param    string  $side   Side to get blocks for (right or left for now)
* @param    string  $topic  Only get blocks for this topic
* @return   array           array of block data
* @link     http://wiki.geeklog.net/index.php/Dynamic_Blocks
*
*/
function plugin_getBlocks_gus($side, $topic='')
{
    global $_TABLES, $_CONF, $_GUS_CONF, $LANG_POLLS, $LANG_GUS_wo;
    
    $retval = array();
    
    $owner_id = SEC_getDefaultRootUser();
    $group_id = DB_getItem($_TABLES['vars'], 'value', "name = 'gus_group_id'");
    
    // Check permissions first
    if ($_GUS_CONF['block_enable'] && SEC_hasAccess($owner_id, $group_id, $_GUS_CONF['block_permissions'][0], $_GUS_CONF['block_permissions'][1], $_GUS_CONF['block_permissions'][2], $_GUS_CONF['block_permissions'][3])) {
        // Check if right topic
        if (($_GUS_CONF['block_topic_option'] == TOPIC_ALL_OPTION) || ($_GUS_CONF['block_topic_option'] == TOPIC_HOMEONLY_OPTION && COM_onFrontpage()) || ($_GUS_CONF['block_topic_option'] == TOPIC_SELECTED_OPTION && in_array($topic, $_GUS_CONF['block_topic']))) {
            if (($side=='left' && $_GUS_CONF['block_isleft'] == 1) || ($side=='right' && $_GUS_CONF['block_isleft'] == 0)) { 
                // Create a block
                $display = phpblock_gusstats();
                
                $retval[] = array('name'           => 'gus_whos_online',
                                  'type'           => 'dynamic',
                                  'onleft'         => $_GUS_CONF['block_isleft'],
                                  'title'          => $LANG_GUS_wo['title'],
                                  'blockorder'     => $_GUS_CONF['block_order'],
                                  'content'        => $display,
                                  'allow_autotags' => false,
                                  'help'           => '');
            }
        }
    }
    
    return $retval;
}

/**
* Gets config information for dynamic blocks from plugins
*
* Returns data for blocks on a given side and, potentially, for
* a given topic.
*
* @param    string  $side   Side to get blocks for (right or left for now)
* @param    string  $topic  Only get blocks for this topic
* @return   array           array of block data
* @link     http://wiki.geeklog.net/index.php/Dynamic_Blocks
*
*/
function plugin_getBlocksConfig_gus($side, $topic='')
{
    global $_TABLES, $_CONF, $_GUS_CONF, $LANG_GUS_wo;
    
    $retval = array();
    
    $owner_id = SEC_getDefaultRootUser();
    $group_id = DB_getItem($_TABLES['vars'], 'value', "name = 'gus_group_id'");
    
    // Check permissions first
    if (SEC_hasAccess($owner_id, $group_id, $_GUS_CONF['block_permissions'][0], $_GUS_CONF['block_permissions'][1], $_GUS_CONF['block_permissions'][2], $_GUS_CONF['block_permissions'][3])) {
        if (($side=='left' && $_GUS_CONF['block_isleft'] == 1) || ($side=='right' && $_GUS_CONF['block_isleft'] == 0)) { 
            $retval[] = array('plugin'         => 'GUS',
                              'name'           => 'gus_whos_online',
                              'title'          => $LANG_GUS_wo['title'],
                              'type'           => 'dynamic',
                              'onleft'         => $_GUS_CONF['block_isleft'],
                              'blockorder'     => $_GUS_CONF['block_order'],
                              'allow_autotags' => false,
                              'help'           => '',
                              'enable'         => $_GUS_CONF['block_enable'],
                              'topic_option'   => $_GUS_CONF['block_topic_option'],
                              'topic'          => $_GUS_CONF['block_topic'],
                              'inherit'        => array()
                              );
        }
    }
    
    return $retval;
}

?>
