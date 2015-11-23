<?php

// +---------------------------------------------------------------------------+
// | GUS Plugin                                                                |
// +---------------------------------------------------------------------------+
// | install.php                                                               |
// |                                                                           |
// | This file installs and removes the data structures for the stats          |
// | plugin for Geeklog.                                                       |
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

require_once '../../../lib-common.php';
require_once $_CONF['path'] . 'plugins/gus/functions.inc';

// Only let Root users access this page
if (!SEC_inGroup('Root')) {
	// Someone is trying to illegally access this page
	COM_errorLog("Someone has tried to illegally access the Stats install/uninstall page.  "
		. "User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: {$_SERVER['REMOTE_ADDR']}", 1);

	echo COM_siteHeader();
	echo COM_startBlock($LANG_ST00['access_denied']);
	echo $LANG_ST00['access_denied_msg'];
	echo COM_endBlock();
	echo COM_siteFooter(true);
	exit;
}

$pi_name = 'gus';
$pi_version = plugin_chkVersion_gus();
$gl_version = '2.1.0';
$pi_url = 'https://github.com/Geeklog-Plugins/gus';

$_FEATURE = array(
	'gus.admin' => 'GUS Admin',
	'gus.view'  => 'GUS Viewer',
);
 
/**
* Puts the datastructures for this plugin into the Geeklog database
*
*/
function plugin_install_gus() {
	global $pi_version, $gl_version, $pi_url, $_FEATURE,
		   $_TABLES, $_CONF, $LANG_GUS00, $LANG_GUS_wo, $_GUS_VARS;

	COM_errorLog('Installing the GUS plugin', 1);

//	DB_setdebug( true );

	// Create the Plugin Tables
	GUS_createDatabaseStructures();
	
	// Create the plugin admin security group
	$group_id = DB_getItem($_TABLES['groups'], 'grp_id ', "grp_name = 'GUS Admin'");
	
	if ($group_id == '') {
		COM_errorLog('Creating GUS admin group', 1);
		DB_query("INSERT INTO {$_TABLES['groups']} (grp_name, grp_descr)
					VALUES ('GUS Admin', 'Users in this group can administer the GUS plugin')", 1);
		
		if (DB_error()) {
			return FALSE;
		}
		
		$result = DB_query("SELECT LAST_INSERT_ID() AS group_id");
		
		if (DB_error()) {
			return FALSE;
		}

		$row = DB_fetchArray($result, FALSE);
		$group_id = $row['group_id'];
	} else {
		DB_query("UPDATE {$_TABLES['groups']} SET grp_gl_core = 0 WHERE grp_id = {$group_id}", 1);
	}
	
	COM_errorLog(" GUS group ID is {$group_id}", 1);
   
	// Save the group id for later uninstall
	COM_errorLog('Saving group_id to vars table for use during uninstall', 1);
	$sql = "INSERT INTO {$_TABLES['vars']} VALUES ('gus_group_id', {$group_id})";
	
	// ON DUPLICATE KEY UPDATE only exists on MySQL >= 4.1
	//	See: http://dev.mysql.com/doc/mysql/en/insert.html
	if ($_GUS_VARS['sql_version']['major'] >= 4 && $_GUS_VARS['sql_version']['minor'] >= 1) {
		$sql .= " ON DUPLICATE KEY UPDATE value={$group_id} ";
	}
	
	DB_query($sql, 1);
	
	if (DB_error()) {
		return FALSE;
	}
		
	// Add plugin Features
	foreach ($_FEATURE as $feature => $desc) {
		$feat_id = DB_getItem($_TABLES['features'], 'ft_id ', "ft_name = '{$feature}'");

		if ($feat_id == '') {
			COM_errorLog("Adding {$feature} feature", 1);
			DB_query("INSERT INTO {$_TABLES['features']} (ft_name, ft_descr) 
						VALUES ('{$feature}','{$desc}')", 1);
						
			if (DB_error()) {
				COM_errorLog("Failure adding {$feature} feature", 1);

				return FALSE;
			}
			
			$result = DB_query("SELECT LAST_INSERT_ID() AS feat_id ");
			
			if (DB_error()) {
				return FALSE;
			}
			
			$row = DB_fetchArray($result, FALSE);
			$feat_id = $row['feat_id'];
		} else {
			DB_query("UPDATE {$_TABLES['features']} SET ft_gl_core = 0 WHERE ft_id = {$feat_id}", 1);
		}
		
		COM_errorLog("Feature '{$feature}' has ID {$feat_id}", 1);
		COM_errorLog("Adding {$feature} feature to admin group", 1);
		DB_query("INSERT IGNORE INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id)
			VALUES ({$feat_id}, {$group_id})");
		
		// In case the previous INSERT was IGNORED, we update the group id for the feature
		DB_query("UPDATE {$_TABLES['access']} SET acc_grp_id = {$group_id} WHERE acc_ft_id = {$feat_id}", 1);
	   
		if (DB_error()) {
			COM_errorLog("Failure adding {$feature} feature to admin group", 1);

			return FALSE;
		}
	}
	
	// add the block
	/*
	COM_errorLog('Adding Who\'s Online block', 1);
	$block_id = DB_getItem($_TABLES['blocks'], 'bid ', "phpblockfn = 'phpblock_gusstats'");
	
	if ($block_id == '') {
		$block_title = addslashes($LANG_GUS_wo['title']);
		$sql = "INSERT INTO {$_TABLES['blocks']}
			( is_enabled, name, type, title, blockorder, onleft, phpblockfn, group_id, owner_id )
			VALUES( 1, 'gus_block', 'phpblock', '{$block_title}', 10, 0, 'phpblock_gusstats', {$group_id}, 2 )
			";
		DB_query($sql, 1);
		
		if (DB_error()) {
			return FALSE;
		}
	} else {
		DB_query("UPDATE {$_TABLES['blocks']} SET group_id = {$group_id} WHERE bid = {$block_id} LIMIT 1", 1);
	}
	*/
	
	// OK, now give Root users access to this plugin now! NOTE: Root group should always be 1
	COM_errorLog("Giving all users in Root group access to GUS admin group", 1);
	DB_query("INSERT IGNORE INTO {$_TABLES['group_assignments']} VALUES ({$group_id}, NULL, 1)");
	
	if (DB_error()) {
		return FALSE;
	}

	// Register the plugin with Geeklog
	COM_errorLog( "Registering GUS plugin with Geeklog", 1 );
	DB_query("DELETE FROM {$_TABLES['plugins']} WHERE pi_name = 'gus'");
	DB_query("INSERT INTO {$_TABLES['plugins']} (pi_name, pi_version, pi_gl_version, pi_homepage, pi_enabled)
				VALUES ('gus', '{$pi_version}', '{$gl_version}', '{$pi_url}', 1)");

	if (DB_error()) {
		return FALSE;
	}

	COM_errorLog("Succesfully installed the GUS Plugin!", 1);
	return TRUE;
}

function GUS_createDatabaseStructures() {
	global $_CONF, $_DB, $_TABLES, $_USER, $_GUS_VARS;

	$_DB->setDisplayError(TRUE);

	require_once $_CONF['path'] . 'plugins/gus/sql/gus.php';

	// build tables
	foreach ($_SQL as $sql) {
		DB_query($sql);
	}

	// insert data
	foreach ($_DATA as $data) {
		DB_query($data);
	}
}

/* 
* Main Function
*/
$display = COM_siteHeader()
		 . COM_startBlock( $LANG_GUS00['install_header'] );
$action = isset($_GET['action']) ? COM_applyFilter($_GET['action']) : '';

if ($action === 'install') {
	if (plugin_install_gus()) {
		$img_url = $_CONF['site_url'] . '/gus/images/' . $_GUS_IMG_name;
		$blockManager = $_CONF['site_admin_url'] . '/block.php';
		$admin_url = $_CONF['site_admin_url'] . '/plugins/gus/index.php';
		$import_url = $_CONF['site_admin_url'] . '/plugins/gus/import.php';
		$readme_url = $_CONF['site_admin_url'] . '/plugins/gus/readme.html';
		$display .= "<img align=left src=\"{$img_url}\" alt='GUS Icon' width=48 height=48>"
				 .  '<p>I have created all the necessary tables and activated the Who\'s Online block. '
				 .  "If you do not want to use it, then you may disable it by changing the GUS config.php file located in the plugins/gus directory. "
				 .  "<p>To configure GUS, go to the <a href=\"$admin_url\">admin page</a>.
			Information about the various configuration options	may be found in the 
			<a href=\"{$readme_url}#config\">README file</a>."
				 .  "<p>If you would like to support development of this plugin, there are some suggestions in the  
			<a href=\"{$readme_url}#you\">README file</a>.";

		// check for old stats to see if we should add an import link
		if ($_ST_plugin_name != '') {
			$stats_version = DB_getItem($_TABLES['plugins'], 'pi_version', "pi_name = '{$_ST_plugin_name}'");
			$display .= "<hr>I notice you have the stats plugin version {$stats_version} installed as '{$_ST_plugin_name}'. ";
			
			if ($stats_version !== '1.3') {
				$display .= "<p>If you had version 1.3 installed, I could import its data. 
					If you update this in the future, you can import its data from 
					the <a href=\"$admin_url\">admin page</a>.";
			} else {
				$display .= "<p>You may import its data into GUS using the <a href=\"{$import_url}\">import page</a>.";
			}
		}
	} else {
		plugin_uninstall_gus();
		$display .= 'For some reason, installation failed.  Check your error logs.';
	}
}

$display .= COM_endBlock()
		 .  COM_siteFooter(TRUE);
echo $display;
