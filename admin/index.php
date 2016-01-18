<?php

// +---------------------------------------------------------------------------+
// | GUS Plugin                                                                |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
// | Administration page.                                                      |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2002, 2003, 2005 by the following authors:                  |
// |                                                                           |
// | Authors: Andy Maloney      - asmaloney@users.sf.net                       |
// |          Tom Willett       - twillett@users.sourceforge.net               |
// |          John Hughes       - jlhughes@users.sf.net                        |
// |                                                                           |
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
require_once $_CONF['path_html'] . '/gus/include/sql.inc';

// Only let admin users access this page
if (!SEC_inGroup('Root') AND !SEC_hasRights('gus.view')) {
	// Someone is trying to illegally access this page
	COM_errorLog("Someone has tried to illegally access the GUS admin page.  "
		. "User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: {$_SERVER['REMOTE_ADDR']}", 1);
	$display = COM_siteHeader()
			 . COM_startBlock($LANG_GUS00['access_denied'])
			 . $LANG_GUS00['access_denied_msg']
			 . COM_endBlock()
			 . COM_siteFooter(TRUE);
	echo $display;
	exit;
}

$action = isset($_POST['action']) ? COM_applyFilter($_POST['action']) : '';
$selected_tab = isset($_POST['selected_tab'])
			  ? COM_applyFilter($_POST['selected_tab'])
			  : 11;
$selected_tab = (int) $selected_tab;
$display = '';

/**
* Main
*/

// GUS_admin_load_ignore_tables
function GUS_admin_load_ignore_tables() {
	global $_TABLES, $_GUS_IP_IGNORE, $_GUS_PAGE_IGNORE, $_GUS_USER_IGNORE,
		   $_GUS_UA_IGNORE, $_GUS_HOST_IGNORE, $_GUS_REFERRER_IGNORE;
	
	$_GUS_IP_IGNORE       = array();
	$_GUS_PAGE_IGNORE     = array();
	$_GUS_USER_IGNORE     = array();
	$_GUS_UA_IGNORE       = array();
	$_GUS_HOST_IGNORE     = array();
	$_GUS_REFERRER_IGNORE = array();
	
	$rec = DB_query("SELECT ip FROM {$_TABLES['gus_ignore_ip']}", 1);
	
	while ($row = DB_fetchArray($rec, FALSE)) {
		$_GUS_IP_IGNORE[] = $row['ip'];
	}
	
	$rec = DB_query("SELECT page FROM {$_TABLES['gus_ignore_page']}", 1);
	
	while ($row = DB_fetchArray($rec, FALSE)) {
		$_GUS_PAGE_IGNORE[] = $row['page'];
	}
	
	$rec = DB_query("SELECT username FROM {$_TABLES['gus_ignore_user']}", 1);
	
	while ($row = DB_fetchArray($rec, FALSE)) {
		$_GUS_USER_IGNORE[] = $row['username'];
	}
	
	$rec = DB_query("SELECT ua FROM {$_TABLES['gus_ignore_ua']}", 1);
	
	while ($row = DB_fetchArray($rec, FALSE)) {
		$_GUS_UA_IGNORE[] = $row['ua'];
	}
	
	$rec = DB_query("SELECT host FROM {$_TABLES['gus_ignore_host']}", 1);
	
	while ($row = DB_fetchArray($rec, false)) {
		$_GUS_HOST_IGNORE[] = $row['host'];
	}
	
	$rec = DB_query("SELECT referrer FROM {$_TABLES['gus_ignore_referrer']}", 1);
	
	while ($row = DB_fetchArray($rec, FALSE)) {
		$_GUS_REFERRER_IGNORE[] = $row['referrer'];
	}
}

// GUS_create_table_from_data
function GUS_create_table_from_data($data, $cols = 4) {
	$table = '<table width="100%" style="border: 1px solid grey; padding: 3px;">';
	$i = 0;
	
	foreach ($data as $item) {
		if ($i % $cols == 0) {
			$table .= '<tr>';
		}
			
		$table .= "<td>{$item}</td>";
		$i++;
		
		if ($i % $cols == 0) {
			$table .= '</tr>';
		}
	}
	
	if ($i === 0) {
		$table .= '<tr><td>&nbsp;</td>';
	}
	
	if ($i < $cols) {
		$table .= '</tr>';
	}

	$table .= '</table>';
	
	return $table;
}

// GUS_create_form
function GUS_create_form($name, $id) {
	global $LANG_GUS_admin;
	
	$form = '<form method=POST action="' . $_SERVER['PHP_SELF'] . '">'
		  . '<input type=submit value="' . $LANG_GUS_admin['add'] . '" name=action>'
		  . '&nbsp;&nbsp;<input type=text size=32 name="' . $name . '">&nbsp;&nbsp;'
		  . '<input type=submit value="' . $LANG_GUS_admin['remove'] . '" name=action>'
		  . '<input type=hidden value=' . $id . ' name=selected_tab>'
		  . '</form>';
	
	return $form;
}

// GUS_create_cleanup_form
function GUS_create_cleanup_form($count_data, $action, $id, $num_msg) {
	global	$LANG_GUS_admin;
	
	$form = '<hr><div class=smaller>'
		  . '<p>' . $LANG_GUS_admin['clean_msg1']
		  . '<form method=POST action="' . $_SERVER['PHP_SELF'] . '">'
		  . $LANG_GUS_admin['clean_msg2']
		  . '<p>&nbsp;&nbsp;&nbsp;&nbsp;<input type=submit value="' . $LANG_GUS_admin['clean_up'] . '">'
		  . '&nbsp;&nbsp;[' . $LANG_GUS_admin['irreversible'] . ']'
		  . '<input type=hidden value=' . $action . ' name=action>'
		  . '<input type=hidden value=' . $id . ' name=selected_tab>'
		  . '</form>'
		  . '<p>' . $LANG_GUS_admin['clean_num_entries'] . ': <b>' . $count_data['entry_count'] . '</b>'
		  . '<br>' . $num_msg . ': <b>' . $count_data['list_len'] . '</b>'
		  . '<div><ul style="list-style-type: circle;">';
	
	foreach ($count_data['list'] as $data) {
		$form .= '<li>' . $data . '</li>';
	}
	
	$form .= '</ul></div>'
		  .  '</div>';
	
	return $form;
}

// GUS_create_item_list_for_count
function GUS_create_item_list_for_count($sql_response, $field_name, $decodeURL = FALSE) {
	$list = array();
	$count = 0;
	
	while ($row = DB_fetchArray($sql_response, FALSE)) {
		$item = $row[$field_name];
		
		if ($decodeURL) {
			$item = urldecode($item);
		}

		$list[] = htmlentities($item);
		$count +=  $row['entries'];
	}
	
	return array('list' => $list, 'list_len' => count($list), 'entry_count' => $count);
}

// GUS_get_user_counts
function GUS_get_user_counts() {
	global $_TABLES, $_GUS_USER_IGNORE, $_GUS_table_prefix;
		
	if (count($_GUS_USER_IGNORE) === 0) {
		return array('list' => array(), 'list_len' => 0, 'entry_count' => 0);
	}

	$result = DB_query("SELECT DISTINCT( us.username ) AS username, COUNT( * ) AS entries
						FROM {$_TABLES['gus_userstats']} us, {$_TABLES['gus_ignore_user']} iu 
						WHERE us.username LIKE iu.username
						GROUP BY us.username");

	return GUS_create_item_list_for_count($result, 'username');
}

// GUS_get_page_counts
function GUS_get_page_counts() {
	global $_TABLES, $_GUS_PAGE_IGNORE, $_GUS_table_prefix;
		
	if (count($_GUS_PAGE_IGNORE) === 0) {
		return array('list' => array(), 'list_len' => 0, 'entry_count' => 0);
	}

	$result = DB_query("SELECT DISTINCT( us.page ) AS page, COUNT( * ) AS entries
						FROM {$_TABLES['gus_userstats']} us, {$_TABLES['gus_ignore_page']} ip 
						WHERE us.page LIKE ip.page
						GROUP BY us.page");

	return GUS_create_item_list_for_count($result, 'page');
}

// GUS_get_ip_counts
function GUS_get_ip_counts() {
	global $_TABLES, $_GUS_IP_IGNORE, $_GUS_table_prefix;
		
	if (count($_GUS_IP_IGNORE) === 0) {
		return array('list' => array(), 'list_len' => 0, 'entry_count' => 0);
	}

	$result = DB_query("SELECT DISTINCT( us.ip ) AS ip, COUNT( * ) AS entries
						FROM {$_TABLES['gus_userstats']} us, {$_TABLES['gus_ignore_ip']} iip 
						WHERE us.ip LIKE iip.ip
						GROUP BY us.ip");

	return GUS_create_item_list_for_count($result, 'ip');
}

// GUS_get_host_counts
function GUS_get_host_counts() {
	global $_TABLES, $_GUS_HOST_IGNORE, $_GUS_table_prefix;
		
	if (count($_GUS_HOST_IGNORE) === 0) {
		return array('list' => array(), 'list_len' => 0, 'entry_count' => 0);
	}

	$result = DB_query("SELECT DISTINCT( us.host ) AS host, COUNT( * ) AS entries
						FROM {$_TABLES['gus_userstats']} us, {$_TABLES['gus_ignore_host']} ih 
						WHERE us.host LIKE ih.host
						GROUP BY us.host");

	return GUS_create_item_list_for_count($result, 'host');
}

// GUS_get_ua_counts
function GUS_get_ua_counts() {
	global $_TABLES, $_GUS_UA_IGNORE, $_GUS_table_prefix;
		
	if (count($_GUS_UA_IGNORE) === 0) {
		return array('list' => array(), 'list_len' => 0, 'entry_count' => 0);
	}

	$tmp_name   = $_GUS_table_prefix . 'temp_ua_table';
	$temp_table = GUS_create_temp_table($tmp_name,
				"SELECT DISTINCT ua_id, user_agent
				FROM {$_TABLES['gus_user_agents']} ua, {$_TABLES['gus_ignore_ua']} iua 
				WHERE ua.user_agent LIKE iua.ua");
	$result = DB_query("SELECT DISTINCT( user_agent ), COUNT( * ) AS entries
						FROM {$_TABLES['gus_userstats']} ua JOIN {$temp_table['name']} tmp ON ua.ua_id = tmp.ua_id
						GROUP BY user_agent");
	$id_list = GUS_create_item_list_for_count($result, 'user_agent');
	GUS_remove_temp_table($temp_table);
		
	return $id_list;
}

// GUS_get_referrer_counts
function GUS_get_referrer_counts() {
	global $_TABLES, $_GUS_REFERRER_IGNORE, $_GUS_table_prefix;
		
	if (count($_GUS_REFERRER_IGNORE) === 0) {
		return array('list' => array(), 'list_len' => 0, 'entry_count' => 0);
	}

	$result = DB_query("SELECT * FROM {$_TABLES['gus_ignore_referrer']}");
	$count  = DB_numRows($result);
	$list = 'WHERE ';
	
	while ($row = DB_fetchArray($result, FALSE)) {
		$list .= 'us.referer LIKE \''
			  .  str_replace('%25', '%', urlencode($row['referrer']))
			  .  '\'';
		
		if ($count > 1) {
			$list .= 'OR ';
		}
		
		$count--;
	}

	$result = DB_query("SELECT DISTINCT( us.referer ) AS referrer, COUNT( * ) AS entries
						FROM {$_TABLES['gus_userstats']} us, {$_TABLES['gus_ignore_referrer']} ir 
						{$list}
						GROUP BY us.referer");

	return GUS_create_item_list_for_count($result, 'referrer', TRUE);
}

// GUS_create_item_list_for_delete
function GUS_create_item_list_for_delete($sql_response, $fieldname) {
	$count = DB_numRows($sql_response);
	$list = '';
	
	while ($row = DB_fetchArray($sql_response, FALSE)) {
		$list .= '\'' . $row[$fieldname] . '\'';
	
		if ($count > 1) {
			$list .= ',';
		}
		
		$count--;
	}
	
	return $list;
}

// begin page -------
if ($action === 'capture_on') {
	DB_query("UPDATE {$_TABLES['gus_vars']} SET value='1' WHERE name='capture' LIMIT 1");
	$_GUS_VARS['capture'] = '1';
} else if ($action === 'capture_off') {
	DB_query("UPDATE {$_TABLES['gus_vars']} SET value='0' WHERE name='capture' LIMIT 1");
	$_GUS_VARS['capture'] = '0';
} else if ($action === 'remove_data') {
	DB_query("UPDATE {$_TABLES['plugins']} SET pi_enabled = '0' WHERE pi_name = 'gus'");
	DB_query("TRUNCATE {$_TABLES['gus_user_agents']}");
	DB_query("TRUNCATE {$_TABLES['gus_userstats']}");
	DB_query("UPDATE {$_TABLES['gus_vars']} SET value='0' WHERE name='imported'");
	DB_query("UPDATE {$_TABLES['plugins']} SET pi_enabled = '1' WHERE pi_name = 'gus'");
	
	// Give the user feedback
	$display .= COM_startBlock('Status', '', COM_getBlockTemplate ('_msg_block', 'header'))
			 .  'Data removed.'
			 .  COM_endBlock(COM_getBlockTemplate('_msg_block', 'footer'))
			 .  '<hr>';
} else if (($action === $LANG_GUS_admin['add']) OR ($action === $LANG_GUS_admin['remove'])) {
	$newip        = isset($_POST['newip']) ? COM_applyFilter($_POST['newip']) : '';
	$newuser      = isset($_POST['newuser']) ? COM_applyFilter($_POST['newuser']) : '';
	$newpage      = isset( $_POST['newpage'] ) ? COM_applyFilter($_POST['newpage']) : '';
	$newuseragent = isset($_POST['newuseragent']) ? COM_applyFilter($_POST['newuseragent']) : '';
	$newhost      = isset($_POST['newhost']) ? COM_applyFilter($_POST['newhost']) : '';
	$newreferrer  = isset($_POST['newreferrer']) ? COM_applyFilter($_POST['newreferrer']) : '';

	if ($newip != '') {
		$table = $_TABLES['gus_ignore_ip'];
		$field = 'ip';
		$data  = substr(trim($newip), 0, 20);
	} else if ($newuser != '') {
		$table = $_TABLES['gus_ignore_user'];
		$field = 'username';
		$data  = substr(trim($newuser), 0, 16);
	} else if ($newpage != '') {
		$table = $_TABLES['gus_ignore_page'];
		$field = 'page';
		$data  = substr(trim($newpage), 0, 255);
	} else if ($newuseragent != '') {
		$table = $_TABLES['gus_ignore_ua'];
		$field = 'ua';
		$data  = substr(trim($newuseragent), 0, 128);
	} else if ($newhost != '') {
		$table = $_TABLES['gus_ignore_host'];
		$field = 'host';
		$data  = substr(trim($newhost), 0, 128);
	} else if ($newreferrer != '') {
		$table = $_TABLES['gus_ignore_referrer'];
		$field = 'referrer';
		$data  = substr(trim( $newreferrer), 0, 128);
	}
	
	$data = addslashes($data);
	
	if ($action === $LANG_GUS_admin['add']) {
		DB_query("INSERT INTO {$table} VALUES ('{$data}')", 1);
	} else {
		DB_query("DELETE FROM {$table} WHERE {$field}='{$data}'", 1);
	}
} else if ($action === 'clean_user') {
	// clean_user
	$result = DB_query("SELECT DISTINCT( us.username ) AS username
							FROM {$_TABLES['gus_userstats']} us, {$_TABLES['gus_ignore_user']} iu 
							WHERE us.username LIKE iu.username");
	$list = GUS_create_item_list_for_delete($result, 'username');
	DB_query("DELETE FROM {$_TABLES['gus_userstats']}
				WHERE username IN ( $list )");
} else if ($action === 'clean_page') {
	// clean_page
	$result = DB_query("SELECT DISTINCT( us.page ) AS page
							FROM {$_TABLES['gus_userstats']} us, {$_TABLES['gus_ignore_page']} ip 
							WHERE us.page LIKE ip.page");
	$list = GUS_create_item_list_for_delete($result, 'page');
	DB_query("DELETE FROM {$_TABLES['gus_userstats']}
				WHERE page IN ( $list )");
} else if ($action === 'clean_host') {
	// clean_host
	$result = DB_query("SELECT DISTINCT( us.host ) AS host
							FROM {$_TABLES['gus_userstats']} us, {$_TABLES['gus_ignore_host']} ih 
							WHERE us.host LIKE ih.host");
	$list = GUS_create_item_list_for_delete($result, 'host');
	DB_query("DELETE FROM {$_TABLES['gus_userstats']}
				WHERE host IN ( $list )");
} else if ($action === 'clean_ip') {
	// clean_ip
	$result = DB_query("SELECT DISTINCT( us.ip ) AS ip
							FROM {$_TABLES['gus_userstats']} us, {$_TABLES['gus_ignore_ip']} iip 
							WHERE us.ip LIKE iip.ip");
	$list = GUS_create_item_list_for_delete($result, 'ip');
	DB_query("DELETE FROM {$_TABLES['gus_userstats']}
				WHERE ip IN ( $list )");
} else if ($action === 'clean_ua') {
	// clean_ua
	$result = DB_query("SELECT DISTINCT( ua_id )
							FROM {$_TABLES['gus_user_agents']} ua, {$_TABLES['gus_ignore_ua']} iua
							WHERE ua.user_agent LIKE iua.ua");
	$list = GUS_create_item_list_for_delete($result, 'ua_id');
	DB_query("DELETE FROM {$_TABLES['gus_userstats']}
				WHERE ua_id IN ( $list )");
} else if ($action === 'clean_referrer') {
	// clean_referrer
	$result = DB_query("SELECT * FROM {$_TABLES['gus_ignore_referrer']}");
	$count = DB_numRows($result);
	$list = 'WHERE ';
	
	while ($row = DB_fetchArray($result, FALSE)) {
		$list .= 'us.referer LIKE \'' . str_replace('%25', '%', urlencode($row['referrer'])) . '\'';
		
		if ($count > 1) {
			$list .= 'OR ';
		}
		
		$count--;
	}

	$result = DB_query("SELECT DISTINCT( us.referer ) AS referrer
						FROM {$_TABLES['gus_userstats']} us, {$_TABLES['gus_ignore_referrer']} ir 
						{$list}
						GROUP BY us.referer");
	$list = GUS_create_item_list_for_delete($result, 'referrer');
	DB_query("DELETE FROM {$_TABLES['gus_userstats']}
				WHERE referer IN ( $list )");
}

GUS_admin_load_ignore_tables();

// Capture
$display .= "<h4>{$LANG_GUS_admin['capture']}</h4>"
		 .  "<form method=POST action=\"{$_CONF['site_admin_url']}/plugins/gus/index.php\">";

if ($_GUS_VARS['capture'] == '1') {
	$display .= $LANG_GUS_admin['captureon'] . '&nbsp;&nbsp;&nbsp;'
			 .  "<input type=submit value='{$LANG_GUS_admin['turnoff']}'>"
			 .  '<input type=hidden value=capture_off name=action>';
} else {
	$display .= $LANG_GUS_admin['captureoff'] . '&nbsp;&nbsp;&nbsp;'
			 .  "<input type=submit value='{$LANG_GUS_admin['turnon']}'>"
			 .  '<input type=hidden value=capture_on name=action>';
}

$display .= '</form>';

// Ignore
$display .= "<hr><h4>{$LANG_GUS_admin['ignore']}</h4>"
		 .  '<div style="margin: 0px 15px; padding: 0px;">';

$titles = array();
$at_least_one_dirty = FALSE;

// User
$i_user = GUS_create_table_from_data($_GUS_USER_IGNORE, 6) . '<br>'
		. GUS_create_form('newuser', 11)
		. '<div class=smaller><b>' . $LANG_GUS_admin['tip'] . '</b> ' . $LANG_GUS_admin['wildcard_tip'] .'</div>';
$titles['user'] = $LANG_GUS_admin['user_title'];
$counts = GUS_get_user_counts();

if ($counts['list_len']) {	
	$i_user .= GUS_create_cleanup_form($counts, 'clean_user', 11, $LANG_GUS_admin['user_num_user']);
	$titles['user'] .= ' *';
	$at_least_one_dirty = TRUE;
}

// Page
$i_page = GUS_create_table_from_data($_GUS_PAGE_IGNORE, 2) . '<br>'
		. GUS_create_form('newpage', 12)
		. '<div class=smaller><b>' . $LANG_GUS_admin['tip'] . '</b> ' . $LANG_GUS_admin['wildcard_tip'] .'</div>'
		. '<div class=smaller><b>' . $LANG_GUS_admin['example'] . '</b> ' . ' admin/plugins/%/index.php, gus/index.php, calendar.php</div>';
$titles['page'] = $LANG_GUS_admin['page_title'];
$counts = GUS_get_page_counts();

if ($counts['list_len']) {	
	$i_page .= GUS_create_cleanup_form($counts, 'clean_page', 12, $LANG_GUS_admin['page_num_page']);

	$titles['page'] .= ' *';
	$at_least_one_dirty = TRUE;
}

// Host
$i_host = GUS_create_table_from_data($_GUS_HOST_IGNORE, 3) . '<br>'
		. GUS_create_form('newhost', 14)
		. '<div class=smaller><b>' . $LANG_GUS_admin['tip'] . '</b> ' . $LANG_GUS_admin['host_tip'] . ' "' . ip_to_hostname( $_GUS_VARS['remote_ip'] ) . '"</div>'
		. '<div class=smaller><b>' . $LANG_GUS_admin['tip'] . '</b> ' . $LANG_GUS_admin['wildcard_tip'] .'</div>'
		. '<div class=smaller><b>' . $LANG_GUS_admin['example'] . '</b> ' . $LANG_GUS_admin['host_example'] . '</div>';
$titles['host'] = $LANG_GUS_admin['host_title'];
$counts = GUS_get_host_counts();

if ($counts['list_len']) {	
	$i_host .= GUS_create_cleanup_form($counts, 'clean_host', 14, $LANG_GUS_admin['host_num_host']);

	$titles['host'] .= ' *';
	$at_least_one_dirty = TRUE;
}

// IP
$i_ip = GUS_create_table_from_data($_GUS_IP_IGNORE) . '<br>'
	  . GUS_create_form('newip', 10)
	  . '<div class=smaller><b>' . $LANG_GUS_admin['tip'] . '</b> ' . $LANG_GUS_admin['ip_tip'] . " {$_GUS_VARS['remote_ip']}</div>"
	  . '<div class=smaller><b>' . $LANG_GUS_admin['tip'] . '</b> ' . $LANG_GUS_admin['wildcard_tip'] . '</div>'
	  . '<div class=smaller><b>' . $LANG_GUS_admin['example'] . '</b> ' . $LANG_GUS_admin['ip_example'] . '</div>';
$titles['ip'] = $LANG_GUS_admin['ip_title'];
$counts = GUS_get_ip_counts();

if ($counts['list_len']) {
	$i_ip .= GUS_create_cleanup_form($counts, 'clean_ip', 10, $LANG_GUS_admin['ip_num_ip']);

	$titles['ip'] .= ' *';
	$at_least_one_dirty = TRUE;
}

// User agent
$i_ua = GUS_create_table_from_data($_GUS_UA_IGNORE, 2) . '<br>'
	  . GUS_create_form('newuseragent', 13)
	  . '<div class=smaller><b>' . $LANG_GUS_admin['tip'] . '</b> ' . $LANG_GUS_admin['wildcard_tip'] .'</div>'
	  . '<div class=smaller><b>' . $LANG_GUS_admin['example'] . '</b> ' . $LANG_GUS_admin['ua_example'] . '</div>';
$titles['ua'] = $LANG_GUS_admin['ua_title'];
$counts = GUS_get_ua_counts();

if ($counts['list_len']) {	
	$i_ua .= GUS_create_cleanup_form($counts, 'clean_ua', 13, $LANG_GUS_admin['ua_num_ua']);
	$titles['ua'] .= ' *';
	$at_least_one_dirty = TRUE;
}

// Referrer
$i_referrer = GUS_create_table_from_data($_GUS_REFERRER_IGNORE, 2) . '<br>'
			. GUS_create_form('newreferrer', 15)
			. '<div class=smaller><b>' . $LANG_GUS_admin['tip'] . '</b> ' . $LANG_GUS_admin['wildcard_tip'] .'</div>'
			. '<div class=smaller><b>' . $LANG_GUS_admin['example'] . '</b> ' . $LANG_GUS_admin['referrer_example'] . '</div>';
$titles['referrer'] = $LANG_GUS_admin['referrer_title'];
$counts = GUS_get_referrer_counts();

if ($counts['list_len']) {	
	$i_referrer .= GUS_create_cleanup_form($counts, 'clean_referrer', 15, $LANG_GUS_admin['referrer_num_referrer']);
	$titles['referrer'] .= ' *';
	$at_least_one_dirty = TRUE;
}

// show our tabbed Ignore pages
$display .= '<script type="text/javascript">'
		 .  "
	var ts = new tabstrip();
	new tab( ts, 11, \"{$titles['user']}\", '" . str_replace( "/", "\/", $i_user ) . "' );
	new tab( ts, 12, \"{$titles['page']}\", '" . str_replace( "/", "\/", $i_page ) . "' );
	new tab( ts, 14, \"{$titles['host']}\", '" . str_replace( "/", "\/", $i_host ) . "' );
	new tab( ts, 10, \"{$titles['ip']}\", '" . str_replace( "/", "\/", $i_ip ) . "' );
	new tab( ts, 13, \"{$titles['ua']}\", '" . str_replace( "/", "\/", $i_ua ) . "' );
	new tab( ts, 15, \"{$titles['referrer']}\", '" . str_replace( "/", "\/", $i_referrer ) . "' );
	ts.write( {$selected_tab} );
"
		 . '</script>';

// handle the case where the user has javascript disabled
$display .= '<noscript>'
		 .  '<div class="noscriptpane"><b>' . $titles['user'] . '</b><br>' . $i_user . '</div>'
		 .  '<div class="noscriptpane"><b>' . $titles['page'] . '</b><br>' . $i_page . '</div>'
		 .  '<div class="noscriptpane"><b>' . $titles['host'] . '</b><br>' . $i_host . '</div>'
		 .  '<div class="noscriptpane"><b>' . $titles['ip'] . '</b><br>' . $i_ip . '</div>'
		 .  '<div class="noscriptpane"><b>' . $titles['ua'] . '</b><br>' . $i_ua . '</div>'
		 .  '<div class="noscriptpane"><b>' . $titles['referrer'] . '</b><br>' . $i_referrer . '</div>'
		 .  '</noscript>';

if ($at_least_one_dirty) {
	$display .= '<p><span class=smaller>' . $LANG_GUS_admin['star'] . '</span>';
}

$display .= '</div><br>';

// let's clear out all the data!
$display .= "<hr><h4>{$LANG_GUS_admin['remove_data']}</h4>"
		 .  'Click the \'' . $LANG_GUS_admin['remove_data'] . '\' button to clear out all the data in your GUS databases. '
		 .  'This will not affect your settings - only the data. '
		 .  'I will disable the plugin, perform the operation, and enable the plugin again. '
		 .  '<p>' . $LANG_GUS_admin['irreversible']
		 .  'You will not get a silly "Are you sure you want to do this?" message.'
		 .  "<form method='POST' action='{$_CONF['site_admin_url']}/plugins/gus/index.php'>
			<input type='submit' value='{$LANG_GUS_admin['remove_data']}' name='Remove'>
			<input type='hidden' value='remove_data' name='action'>
			</form>";

// fetch the 'imported' var since it may have changed
$rec = DB_query("SELECT value FROM {$_TABLES['gus_vars']} WHERE name = 'imported' LIMIT 1", 1);
$row = DB_fetchArray($rec, FALSE);
$_GUS_VARS['imported'] = $row['value'];

// check for old stats to see if we should add an import link
if (GUS_checkStatsInstall() AND ($_ST_plugin_name != '') AND ($_GUS_VARS['imported'] < 3)) {
	$import_url    = $_CONF['site_admin_url'] . '/plugins/gus/import.php';
	$stats_version = DB_getItem( $_TABLES['plugins'], 'pi_version', "pi_name = '{$_ST_plugin_name}'" );
	$display .= "<hr><h4>{$LANG_GUS_admin['import_data']}</h4>"
			 .  "I notice you have the stats plugin version {$stats_version} installed as '{$_ST_plugin_name}'. ";
	
	if ($stats_version !== '1.3') {
		$display .= "<p>If you had version 1.3 installed, I could import its data. 
			If you update this in the future, you can import its data from 
			the <a href=\"{$admin_url}\">admin page</a>.";
	} else {	
		$display .= "<p>You may import its data into GUS using the <a href=\"{$import_url}\">import page</a>.";
	}
}


$readme_url = $_CONF['site_admin_url'] . '/plugins/gus/readme.html#config';

$menu_arr = array (
                array('url' => $_CONF['site_admin_url'],
                      'text' => $LANG_ADMIN['admin_home'])
);

$retval = COM_siteHeader ('menu', $LANG_GUS_admin['admin']);
$retval .= COM_startBlock($LANG_GUS_admin['admin'] . ' [v' . plugin_chkVersion_gus() .']', $readme_url,
                                            COM_getBlockTemplate('_admin_block', 'header'));

require_once $_CONF['path'] . '/system/lib-admin.php';
$retval .= ADMIN_createMenu($menu_arr, $LANG_GUS_admin['instructions'], plugin_geticon_gus());
    
$display = $retval . $display;

$display .= COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));
$display .= COM_siteFooter();

echo $display;

?>


