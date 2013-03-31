<?php
// +---------------------------------------------------------------------------+
// | GUS Plugin                                                                |
// +---------------------------------------------------------------------------+
// | import.php                                                                |
// |                                                                           |
// | This file will import data from the stats plugin into the DB for GUS.     |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2005 by the following authors:                              |
// |                                                                           |
// | Authors: Andy Maloney      - asmaloney@users.sf.net                       |
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

require_once '../../../lib-common.php';
require_once $_CONF['path'] . 'plugins/gus/functions.inc';

// Only let Root users access this page
if (!SEC_inGroup('Root')) {
    // Someone is trying to illegally access this page
    COM_errorLog("Someone has tried to illegally access the GUS import page.  "
    	. "User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: {$_SERVER['REMOTE_ADDR']}", 1);
    echo COM_siteHeader();
    echo COM_startBlock($LANG_ST00['access_denied']);
    echo $LANG_ST00['access_denied_msg'];
    echo COM_endBlock();
    echo COM_siteFooter(TRUE);
    exit;
}

$img_url = $_CONF['site_url'] . '/gus/images/' . $_GUS_IMG_small_name;
$header = '<img src="' . $img_url .'" width=24 height=24 alt="GUS pic" align=middle>&nbsp;&nbsp;' . $LANG_GUS00['import_header'] . ' [v' . plugin_chkVersion_gus() .']';
$readme_url = $_CONF['site_admin_url'] . '/plugins/gus/readme.html#import';

// IF we can't find the stats plugin, then we can't do much, so abort!
if (GUS_checkStatsInstall() == FALSE) {
	global $_CONF, $header, $readme_url;
	
	echo COM_siteHeader();
	echo COM_startBlock($header, $readme_url);
	echo COM_startBlock('Error', '', COM_getBlockTemplate ('_msg_block', 'header'));
	echo '<img src="' . $_CONF['layout_url'] . '/images/sysmessage.gif" border="0" align="top" alt="">&nbsp;&nbsp;';
	echo "I can't seem to find your stats plugin anywhere.";
	echo COM_endBlock(COM_getBlockTemplate('_msg_block', 'footer'));
	echo COM_endBlock();
	echo COM_siteFooter(TRUE);
	exit;
}

function GUS_save_plugin_enable_states() {
	global $_TABLES, $_ST_plugin_name, $G_plugin_state;
		
	// save the state of our plugins
	$res = DB_query("SELECT pi_name, pi_enabled FROM {$_TABLES['plugins']}
		WHERE (pi_name = 'gus' OR pi_name = '{$_ST_plugin_name}')");
	
	while ($row = DB_fetchArray($res)) {
		$G_plugin_state[$row['pi_name']] = $row['pi_enabled'];
	}
}

function GUS_enable_plugins($enable = true) {
	global $_TABLES, $_ST_plugin_name, $G_plugin_state;
	
	if ($enable) {
		$enable = $G_plugin_state['gus'];
    	DB_query("UPDATE {$_TABLES['plugins']} SET pi_enabled = '{$enable}' WHERE pi_name = 'gus'");
		$enable = $G_plugin_state[$_ST_plugin_name];
		DB_query("UPDATE {$_TABLES['plugins']} SET pi_enabled = '{$enable}' WHERE pi_name = '{$_ST_plugin_name}'");
	} else {
    	DB_query("UPDATE {$_TABLES['plugins']} SET pi_enabled = '0'
    		WHERE pi_name = 'gus' OR pi_name = '{$_ST_plugin_name}'");
    }
}

function GUS_import_ignored() {
	global $_TABLES, $spaces;

	GUS_enable_plugins(FALSE);
	echo COM_startBlock('Step 1 Status', '', COM_getBlockTemplate ('_msg_block', 'header'));
	echo "{$spaces} "; flush();
	echo 'Adding ignored IP addresses...';
	DB_query("INSERT IGNORE INTO {$_TABLES['gus_ignore_ip']} SELECT DISTINCT ip FROM {$_TABLES['ignore_ip']}");

	echo '<br>Adding ignored users...';
	DB_query("INSERT IGNORE INTO {$_TABLES['gus_ignore_user']} SELECT DISTINCT username FROM {$_TABLES['ignore_user']}");

	echo '<br>Adding ignored pages...';
	DB_query("INSERT IGNORE INTO {$_TABLES['gus_ignore_page']} SELECT DISTINCT page FROM {$_TABLES['ignore_page']}");
	
	GUS_enable_plugins();
	
	echo '<br>Done.';
	echo COM_endBlock(COM_getBlockTemplate('_msg_block', 'footer'));

	return TRUE;
}

function GUS_import_user_agents() {
	global $_TABLES, $spaces;

	GUS_enable_plugins(FALSE);
	echo COM_startBlock('Step 2 Status', '', COM_getBlockTemplate('_msg_block', 'header'));
	echo "{$spaces} "; flush();
	
	// populate with the user agents from the original userstats table
	$res = DB_query("SELECT DISTINCT user_agent FROM {$_TABLES['userstats']} ORDER BY user_agent");
	$num_rows = DB_numRows($res);
	
	echo 'Adding ' . $num_rows . ' user agents...<br>';
	echo "{$spaces} "; flush(); 
	
	$i = 0;
	
	while ($row = DB_fetchArray($res)) {
		$user_agent = addslashes(substr($row[0], 0, 128));
		
		$browser = GUS_getBrowser($row[0]);
		$platform = GUS_getComputerType($row[0]);
	
		$query = "INSERT IGNORE INTO {$_TABLES['gus_user_agents']}
			VALUES ( 0, '{$user_agent}', '{$browser['type']}', '{$browser['ver']}', '{$platform}' )";
		
		DB_query($query);
		
		$i++;

		if ($i % 100 === 0) {
			echo '.';
		}
			
		if ($i % 50 === 0) {
			echo "{$spaces} ";
		}
		
		flush();
	}
	
	GUS_enable_plugins();
	echo '<br>Done.';
	echo COM_endBlock(COM_getBlockTemplate('_msg_block', 'footer'));

	return TRUE;
}

function GUS_import_userstats() {
    global $_TABLES;

	GUS_enable_plugins(FALSE);
	echo COM_startBlock('Step 3 Status', '', COM_getBlockTemplate ('_msg_block', 'header'));
	echo "{$spaces} "; flush();
 
 	// Here's what we're doing:
 	//	- try to use faster SELECT...INTO OUTFILE
 	//	- if we can't, then fall back to INSERT
 	
 	$using_file = FALSE;
    $tmpfile = tempnam('', 'GUS');

	// MySQL requires that the file does not exist, so we're really just using the name and path
	unlink($tmpfile); 

	// This monstrosity encodes referrer strings and query strings so that our matching works when we create tables
	$columns = "uid, ip, host, page, username,
		REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
			REPLACE(referer, '%', '%25'), '/', '%2F'), ':', '%3A'), '?', '%3F'), '&', '%26'), '$', '%24'), '#', '%23'), '+', '%2B') AS referer,
		request, 
		REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
			REPLACE(query_string, '%', '%25'), '/', '%2F'), ':', '%3A'), '?', '%3F'), '&', '%26'), '$', '%24'), '#', '%23'), '+', '%2B') AS query_string,
		CONCAT( SUBSTRING( date, 1, 4 ), '-', SUBSTRING( date, 5, 2 ), '-', SUBSTRING( date, 7, 2 ) ) AS date,
		CONCAT( SUBSTRING( hour, 1, 2 ), ':', SUBSTRING( minute, 1, 2 ), ':', '00' ) AS time, ua_id";

	$sql = "SELECT {$columns}
		INTO OUTFILE '{$tmpfile}'
		FROM {$_TABLES['userstats']}, {$_TABLES['gus_user_agents']}
		WHERE {$_TABLES['gus_user_agents']}.user_agent = {$_TABLES['userstats']}.user_agent";

	$result = DB_query($sql, 1);
	$using_file = !DB_error();
	
	if (!$using_file) {
		COM_errorLog("GUS import - Using SELECT...INTO OUTFILE was not successful - falling back on slower INSERT syntax");
		
		$sql = "INSERT INTO {$_TABLES['gus_userstats']} SELECT {$columns}
			FROM {$_TABLES['userstats']}, {$_TABLES['gus_user_agents']}
			WHERE {$_TABLES['gus_user_agents']}.user_agent = {$_TABLES['userstats']}.user_agent";
	
		$result = DB_query($sql);
	}
	
	echo 'Importing ' . DB_numRows($result) . ' rows of data...<br>';
	flush();
	
	if ($using_file) {
		DB_query("LOAD DATA INFILE '{$tmpfile}' INTO TABLE {$_TABLES['gus_userstats']}");
		
		// unlink the one that MySQL created
    	unlink($tmpfile); 
	}
	
	GUS_enable_plugins();
	echo '<br>Done.';
	echo COM_endBlock(COM_getBlockTemplate('_msg_block', 'footer'));
	
	return TRUE;
}

function GUS_import_update() {
	echo COM_startBlock('Step 4 Status', '', COM_getBlockTemplate ('_msg_block', 'header'));
	
	// run all our update scripts to make sure our data is up-to-date
	GUS_doUpgrades('1.3.0');
	echo '<br>Done.';
	echo COM_endBlock(COM_getBlockTemplate('_msg_block', 'footer'));

	return TRUE;
}

function GUS_step($num, $description, $function) {
	global $_CONF, $_GUS_VARS;
	
	$display = '';
	
	if ($_GUS_VARS['imported'] == $num - 1) {
		$display .= "<form method='POST' action='{$_CONF['site_admin_url']}/plugins/gus/import.php'>";
	}
	
	$display .= "<b>Step {$num}</b> {$description} &nbsp;&nbsp;";
	 
	if ($_GUS_VARS['imported'] == $num - 1) {
		$display .= "<input type='submit' value='Go' {$disabled}>
			<input type='hidden' value='{$function}' name='action'>
			</form>";
	} else if ($_GUS_VARS['imported'] > $num - 1) {
		$display .= ' [Done]';
	}

	$display .= '<p>';
	
	return $display;
}

/* 
* Main Function
*/

// we use this trick to keep the connection alive
$spaces = '';

for ($i = 0; $i < 250; $i++) {
	$spaces .= '<!-- bufferme -->';
}

GUS_save_plugin_enable_states();

echo COM_siteHeader();
echo COM_startBlock($header, $readme_url);
echo "{$spaces} "; flush();
$action = isset($_POST['action']) ? COM_applyFilter($_POST['action']) : '';

switch ($action) {
	case 'import_ignored':
		if (GUS_import_ignored()) {
			DB_query("UPDATE {$_TABLES['gus_vars']} SET value = '1' WHERE name = 'imported'");
		}
		
		break;
		
	case 'import_user_agents':
		if (GUS_import_user_agents()) {
			DB_query("UPDATE {$_TABLES['gus_vars']} SET value = '2' WHERE name = 'imported'");
		}
		
		break;
		
	case 'import_userstats':
		if (GUS_import_userstats()) {
			DB_query( "UPDATE {$_TABLES['gus_vars']} SET value = '3' WHERE name = 'imported'" );
		}
		
		break;
		
	case 'import_update':
		if (GUS_import_update()) {
    		DB_query( "UPDATE {$_TABLES['gus_vars']} SET value = '4' WHERE name = 'imported'" );
		}
		
    	break;
}

// fetch the 'imported' var since it may have changed
$rec = DB_query("SELECT value FROM {$_TABLES['gus_vars']} WHERE name = 'imported'", 1);
$row = DB_fetchArray($rec, FALSE);
$_GUS_VARS['imported'] = $row['value'];

// finally, show the main part of the page
$display = "Importing your data from the stats plugin is a four-step process. 
	In the first step, all the IPs, pages, and users which are ignored are imported. 
	In the second step, the user agents are added to the new user agent table. 
	In the third step, the rest of the data is added to the the userstats table. 
	Finally, in the fourth step, the data is updated to the lastest version of GUS. 
	<p>For each of these steps, I will disable the plugins and enable them again if they were enabled originally. 
	<p>If you have a lot of data this may take a long time, so be patient.";

$db_url = $_CONF['site_admin_url'] . '/database.php';
$display .= "<p>You have <a href=\"{$db_url}\">backed up</a> your database, right?"
		 .  '<p><hr>'
		 .  GUS_step(1, "Import ignored lists", "import_ignored")
		 .  GUS_step(2, "Add data to the user agents table", "import_user_agents")
		 .  GUS_step(3, "Import data from the userstats table", "import_userstats")
		 .  GUS_step(4, "Update data to latest version of GUS", "import_update");

if ($_GUS_VARS['imported'] == 4) {
	$display .= '<p>Congratulations!  You have imported all your data into GUS.';
}

echo $display;
echo COM_endBlock();
echo COM_siteFooter(TRUE);
