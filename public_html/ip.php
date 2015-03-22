<?php

// +---------------------------------------------------------------------------+
// | GUS Plugin                                                                |
// +---------------------------------------------------------------------------+
// | page.php                                                                  |
// |                                                                           |
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

require_once './include/security.inc';

if (!GUS_HasAccess(FALSE)) {
	exit;
}

require_once './include/sql.inc';
require_once './include/util.inc';

$gus_ip_collect_data = isset($_POST['gus_ip_collect_data'])
					 ? COM_applyFilter($_POST['gus_ip_collect_data'])
					 : '';
$gus_ip_ban = isset($_POST['gus_ip_ban'])
		    ? COM_applyFilter($_POST['gus_ip_ban'])
			: '';
$sort = isset($_GET['sort']) ? COM_applyFilter($_GET['sort']) : '';
$ip_addr = isset($_GET['ip_addr']) ? COM_applyFilter($_GET['ip_addr']) : '';

// handle our forms
if ($gus_ip_collect_data == '0') {
	DB_query("INSERT IGNORE INTO {$_TABLES['gus_ignore_ip']} VALUES ( '$ip_addr' )", 1);
} else if ($gus_ip_collect_data == '1') {
	DB_query("DELETE FROM {$_TABLES['gus_ignore_ip']} WHERE ip = '$ip_addr' LIMIT 1", 1);
}

// check for the Ban plugin and which table structure to use
if (function_exists('BAN_for_plugins_check_access') AND BAN_for_plugins_check_access()) {
	if ($gus_ip_ban == '0') { // Delete
		BAN_for_plugins_ban_ip($ip_addr, 'gus', false);
	} else if ($gus_ip_ban == '1') { // Insert
		BAN_for_plugins_ban_ip($ip_addr, 'gus');
	}
}

// main SQL query
$date_compare = GUS_get_date_comparison('date', $year, $month, $day);
$date_format = ($day == 0)
			 ? 'CONCAT( DATE_FORMAT( date, \'%d %b - \' ), TIME_FORMAT( time, \'%H:%i\' ) )'
			 : 'TIME_FORMAT( time, \'%H:%i\' )';
$order_by = GUS_get_order_by($sort);
$sql = "SELECT page, uid, username, ip, host, referer, query_string, date, time, request,
		{$date_format} AS date_formatted 
		FROM {$_TABLES['gus_userstats']} 
		WHERE {$date_compare} AND ip='$ip_addr' {$order_by}";

// create navigation_URLs AND set urls
$totalrec  = DB_query($sql);
$totalrows = DB_numRows($totalrec);
$num_pages = ceil($totalrows / $_GUS_limit);

if (!isset($_GET['page']) OR empty($_GET['page'])) {
	$curpage = 1; 
} else {
	$curpage = (int) $_GET['page'];
}

$header_url = GUS_create_url('sort');
$base_url   = GUS_create_url('');
$navigation_URLs = COM_printPageNavigation($base_url, $curpage, $num_pages);

// limit to the sql
$offset = ($curpage - 1) * $_GUS_limit;
$sql .= " LIMIT " . $offset . ', ' . $_GUS_limit;
$rec = DB_query($sql);
$nrows = DB_numRows($rec);

// set template
$T = GUS_template_start();
$T->set_var('additional_nav', GUS_make_nav($day, $month, $year, "ip_addr=$ip_addr"));

//----------------------
// IP info block

$block_title = "$ip_addr [" . $_GUS_Whois_URL_start . $ip_addr . $_GUS_Whois_URL_end . 'whois</a>]';

// Check to see if collect stats on this IP
$result = DB_query("SELECT COUNT(*) AS ignored
						FROM {$_TABLES['gus_ignore_ip']}
						WHERE '{$ip_addr}' LIKE ip
						LIMIT 1", 1);
$row = DB_fetchArray($result, FALSE);
$actionURL = $_SERVER['PHP_SELF'] . '?' . htmlentities($_SERVER['QUERY_STRING']);
$data = "<table width=\"100%\" cellspacing=0 cellpadding=0><tr class=header><td>$block_title</td></tr><tr><td>"
	  . '<table cellspacing=0 cellpadding=0  style="border: 0px;"><tr>'
	  . '<td class=col_right>Stats collection:</td>';

if ($row['ignored'] == '1') {
	$data .= '<td><span style="font-weight: bold;">off</span></td>'
		  .  "<td><form method='POST' action='" . $actionURL . "'>"
		  .  "<input type=submit value='Turn On'>"
		  .  "<input type='hidden' value='1' name='gus_ip_collect_data'>"
		  .  '</form></td>';
} else {
	$data .= '<td><span style="font-weight: bold;">on</span></td>'
		  .  "<td><form method='POST' action='" . $actionURL . "'>"
		  .  "<input type=submit value='Turn Off'>"
		  .  "<input type='hidden' value='0' name='gus_ip_collect_data'>"
		  .  '</form></td>';
}

$data .= '</tr><tr>'
	  .  '<td class=col_right>Ban IP:</td>';

// check for the Ban plugin
if (function_exists('BAN_for_plugins_check_access') AND BAN_for_plugins_check_access()) {
	if (BAN_for_plugins_ban_found($ip_addr)) {
		$data .= '<td><span style="font-weight: bold;">on</span></td>'
			  .  "<td><form method='POST' action='" . $actionURL . "'>"
			  .  "<input type=submit value='Turn Off'>"
			  .  "<input type='hidden' value='0' name='gus_ip_ban'>"
			  .  '</form></td>';
	} else {
		$data .= '<td><span style="font-weight: bold;">off</span></td>'
			  .  "<td><form method='POST' action='" . $actionURL . "'>"
			  .  "<input type=submit value='Turn On'>"
			  .  "<input type='hidden' value='1' name='gus_ip_ban'>"
			  .  '</form></td>';
	}
} else {
	$data .= '<td colspan=2>[A compatible version of the <a href="http://code.google.com/p/geeklog/" target="_blank">ban plugin</a> is not installed or you do not have access]</td>';
}

$data .= '</tr></table></td><tr><td>';

// list the host names associated with this IP address
$sql = "SELECT DISTINCT( host ) 
		FROM {$_TABLES['gus_userstats']} 
		WHERE ip='$ip_addr' AND host <> '$ip_addr'";
$result = DB_query($sql);
$num_results = DB_numRows($result);

if ($num_results == 0) {
	$data .= "There are <b>no</b> host names associated with this IP address.";
} else {
	if ($num_results == 1) {
		$data .= "There is <b>one</b> host name associated with this IP address:";
	} else {
		$data .= "There are <b>{$num_results}</b> host names associated with this IP address:";
	}
	
	$data .= '<ul>';
	
	while ($row = DB_fetchArray($result)) {
		$data .= '<li><a href="http://' . $row['host'] . '" target="_blank">'
			  .  $row['host'] . '</a>&nbsp;&nbsp;'
			  .  ' [' . $_GUS_Whois_URL_start . $row['host'] . $_GUS_Whois_URL_end
			  .  'whois</a>]'
			  .  ' [<a href="http://toolbar.netcraft.com/site_report?url=http://'
			  .  $row['host'] . '" target="_blank">netcraft</a>]'
			  .  '</li>';
	}
	
	$data .= '</ul>';
}

$data .= '</td></tr><tr><td>';

// list the user agents associated with this IP address
$sql = "SELECT DISTINCT( a.user_agent ) 
		FROM {$_TABLES['gus_userstats']} s
		JOIN {$_TABLES['gus_user_agents']} a
		WHERE s.ua_id = a.ua_id AND s.ip = '$ip_addr' AND a.user_agent <> ''";
$result = DB_query($sql);
$num_results = DB_numRows($result);

if ($num_results == 0) {
	$data .= "There are <b>no</b> user agents associated with this IP address.";
} else {
	if ($num_results == 1) {
		$data .= "There is <b>one</b> user agent associated with this IP address:";
	} else {
		$data .= "There are <b>{$num_results}</b> user agents associated with this IP address:";
	}
	
	$data .= '<ul>';
	
	while ($row = DB_fetchArray($result)) {
		$data .= '<li>' . htmlentities($row['user_agent']) . '</li>';
	}
	
	$data .= '</ul>';
}

$data .= '</td></tr></table><br>';
$T->set_var('ip_info', $data);

// IP info block
//----------------------
// main table

$T->set_block('page', 'COLUMN', 'CBlock');
$T->set_block('page', 'ROW', 'BBlock');
$T->set_block('page', 'TABLE', 'ABlock');

$nav_down = "<br><img src=\"{$_CONF['site_url']}/gus/images/nav_down.gif\" width=\"13\" height=\"11\" alt=\"{$LANG_GUS00['sortDESC']}\" border=\"0\">";
$nav_up = "<img src=\"{$_CONF['site_url']}/gus/images/nav_up.gif\" width=\"13\" height=\"11\" alt=\"{$LANG_GUS00['sortASC']}\" border=\"0\">";

$T->set_var('data', '<div align="center">' . $LANG_GUS00['type'] . '&nbsp;<a href="' . $header_url . '&amp;sort=typeDESC">' . $nav_down .'</a><a href="' . $header_url . '&amp;sort=typeASC">' . $nav_up . '</a></div>');
$T->parse('CBlock', 'COLUMN', FALSE);

$T->set_var('data', '<div align="center">' . $LANG_GUS00['ptu'] . '&nbsp;<a href="' . $header_url . '&amp;sort=ptuDESC">' . $nav_down .'</a><a href="' . $header_url . '&amp;sort=ptuASC">' . $nav_up . '</a></div>');
$T->parse('CBlock', 'COLUMN', TRUE);

$T->set_var('data', '<div align="center">' . $LANG_GUS00['user'] . '&nbsp;<a href="' . $header_url . '&amp;sort=usernameDESC">' . $nav_down .'</a><a href="' . $header_url . '&amp;sort=usernameASC">' . $nav_up . '</a></div>');
$T->parse('CBlock', 'COLUMN', TRUE);

$T->set_var('data', '<div align="center">' . $LANG_GUS00['hostname'] . '&nbsp;<a href="' . $header_url . '&amp;sort=hostDESC">' . $nav_down .'</a><a href="' . $header_url . '&amp;sort=hostASC">' . $nav_up . '</a></div>');
$T->parse('CBlock', 'COLUMN', TRUE);

$T->set_var('data', '<div align="center">' . (($day == 0) ? $LANG_GUS00['datetime'] : $LANG_GUS00['time']) . '&nbsp;<a href="' . $header_url . '&amp;sort=dateDESC">' . $nav_down .'</a><a href="' . $header_url . '&amp;sort=dateASC">' . $nav_up . '</a></div>');
$T->parse('CBlock', 'COLUMN', TRUE);

$T->set_var('data', '<div align="center">' . $LANG_GUS00['referer'] . '&nbsp;<a href="' . $header_url . '&amp;sort=refererDESC">' . $nav_down .'</a><a href="' . $header_url . '&amp;sort=refererASC">' . $nav_up . '</a></div>');
$T->parse('CBlock', 'COLUMN', TRUE);

$T->set_var('rowclass', 'header');
$T->parse('BBlock', 'ROW' , TRUE);

for ($i = 0; $i < $nrows; $i++) {
	if (($i + 1) % 2) {
		$T->set_var('rowclass', 'row1');
	} else {
		$T->set_var('rowclass', 'row2');
	}

	$A = DB_fetchArray($rec);
	$T->set_var('data', $A['request']);
	$T->parse('CBlock', 'COLUMN', FALSE);
	
	$query_string = urldecode($A['query_string']);
	$the_data = GUS_template_get_page_data($A['page'], $query_string);
	$T->set_var('data', $the_data);
	$T->parse('CBlock', 'COLUMN', TRUE);

	$the_data = GUS_template_get_user_data($A['uid'], $A['username']);
	$T->set_var('data', $the_data);
	$T->parse('CBlock', 'COLUMN', TRUE);

	$T->set_var('data','<a href="http://' . $A['host'] . '" target="_blank">' . $A['host'] . '</a>');
	$T->parse('CBlock', 'COLUMN', TRUE);
	
	$T->set_var('data', $A['date_formatted']);
	$T->parse('CBlock', 'COLUMN', TRUE);

	$the_data = GUS_template_get_referrer_data($A['referer']);
	$T->set_var('data', $the_data);
	$T->parse('CBlock', 'COLUMN', TRUE);
	
	$T->parse('BBlock', 'ROW', TRUE);
}

$T->parse('ABlock', 'TABLE', TRUE);
$T->set_var('google_paging', $navigation_URLs);

if ($day != '') {
	$title = "$ip_addr - " . date('l, j F, Y ', mktime(0, 0, 0, $month, $day, $year));
} else if ($month != '') {
	$title = "$ip_addr - " . date('F Y ', mktime(0, 0, 0, $month, 1, $year));
} else {
	$title = "$ip_addr - " . date('Y ', mktime(0, 0, 0, 1, 1, $year));
}

$T->set_var('header_text', $title);
$T->parse('page_header', 'header');
$T->parse('table', 'page');
$T->parse('page_footer', 'footer');

$display = '<div class="gus">'
		 . $T->finish($T->get_var('page_header'))
		 . $T->finish($T->get_var('ip_info'))
		 . $T->finish($T->get_var('table'))
		 . '<br>'
		 . $T->finish($T->get_var('page_footer'))
		 . '</div>';
echo COM_siteHeader($_GUS_CONF['show_left_blocks']);
echo $display;
echo COM_siteFooter($_GUS_CONF['show_right_blocks']);
