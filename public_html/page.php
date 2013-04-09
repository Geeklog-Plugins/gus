<?php

// +---------------------------------------------------------------------------+
// | GUS Plugin                                                                |
// +---------------------------------------------------------------------------+
// | page.php                                                                  |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2002, 2003, 2005 by the following authors:                  |
// |                                                                           |
// | Authors: Andy Maloney      - asmaloney@users.sf.net                       |
// |          Tom Willett       - twillett@users.sourceforge.net               |
// |          John Hughes       - jlhughes@users.sf.net                        |
// |          Danny Ledger      - squatty@users.sourceforge.net                |            
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

// limit rows to display in reports
// this should be set in config.php
if (empty($_GUS_limit)) {
	$_GUS_limit = 25;
}

$ip  = isset($_GET['ip']) ? $_GET['ip'] : '';
$uid = isset( $_GET['uid'] ) ? $_GET['uid'] : '';
$uid = (int) $uid;
$visited_page = isset($_GET['visited_page']) ? $_GET['visited_page'] : '';
$anon = isset($_GET['anon']) ? $_GET['anon'] : '';
$anon = (int) $anon;
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

/*
* Main Function
*/

// check for cached file
if ($day == 0) {
	$today = date('MY');
} else {
	$today = date('dMY');
}

if (file_exists(GUS_cachefile()) AND ($today != $day . $month . $year)) {
	$display = GUS_getcache();
} else {
	// no cached version

	// basic sql
	$date_compare = GUS_get_date_comparison('date', $year, $month, $day);
	$date_format = ($day == 0)
				 ? 'CONCAT( DATE_FORMAT( date, \'%d %b - \' ), TIME_FORMAT( time, \'%H:%i\' ) )'
				 : 'TIME_FORMAT( time, \'%H:%i\' )';
	$sql = "SELECT page, uid, username, ip, host, referer, query_string, date, time, request,
			{$date_format} AS date_formatted 
			FROM {$_TABLES['gus_userstats']} 
			WHERE {$date_compare}";

	if ($ip != '') {
		$sql .= " AND ip='$ip' ";
	}

	if ($uid >  1) {
		$sql .= " AND uid='$uid' ";
	}

	if ($visited_page != '') {
		$sql .= " AND page='$visited_page'";
	}

	//anonymous or logged in
	if ($anon == '1') {
		$sql .= " AND uid='1'";
	} else if ($anon == '2') {
		$sql .= " AND uid>'1'";
	}

	$sql .= GUS_get_order_by($sort);

	// create navlinks AND set urls
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
	$navlinks   = COM_printPageNavigation($base_url, $curpage, $num_pages);

	// limit to the sql
	$offset = ($curpage - 1) * $_GUS_limit;
	$sql .= " LIMIT " . $offset . ', ' . $_GUS_limit;
	$rec   = DB_query($sql);
	$nrows = DB_numRows($rec);

	// set template
	$T = GUS_template_start();
	$T->set_block('page', 'COLUMN', 'CBlock');
	$T->set_block('page', 'ROW', 'BBlock');
	$T->set_block('page', 'TABLE', 'ABlock');
	$nav_down = "<br><img src=\"{$_CONF['site_url']}/gus/images/nav_down.gif\" width=\"13\" height=\"11\" alt=\"{$LANG_GUS00['sortDESC']}\" border=\"0\">";
	$nav_up = "<img src=\"{$_CONF['site_url']}/gus/images/nav_up.gif\" width=\"13\" height=\"11\" alt=\"{$LANG_GUS00['sortASC']}\" border=\"0\">";
	$T->set_var(array(
		'colclass' => 'col_left',
		'data'     => '<div align="center">' . $LANG_GUS00['type'] . '&nbsp;<a href="' . $header_url . '&amp;sort=typeDESC">' . $nav_down . '</a><a href="' . $header_url . '&amp;sort=typeASC">' . $nav_up . '</a></div>',
	));
	$T->parse('CBlock', 'COLUMN', FALSE);

	$T->set_var('data', '<div align="center">' . $LANG_GUS00['ptu'] . '&nbsp;<a href="' . $header_url . '&amp;sort=ptuDESC">' . $nav_down . '</a><a href="' . $header_url . '&amp;sort=ptuASC">' . $nav_up . '</a></div>');
	$T->parse('CBlock', 'COLUMN', TRUE);

	$T->set_var('data', '<div align="center">' . $LANG_GUS00['user'] . '&nbsp;<a href="' . $header_url . '&amp;sort=usernameDESC">' . $nav_down . '</a><a href="' . $header_url . '&amp;sort=usernameASC">' . $nav_up . '</a></div>');
	$T->parse('CBlock', 'COLUMN', TRUE);

	$T->set_var('data', '<div align="center">' . $LANG_GUS00['ip'] . '&nbsp;<a href="' . $header_url . '&amp;sort=ipDESC">' . $nav_down . '</a><a href="' . $header_url . '&amp;sort=ipASC">' . $nav_up . '</a></div>');
	$T->parse('CBlock', 'COLUMN', TRUE);

	$T->set_var('data', '<div align="center">' . $LANG_GUS00['hostname'] . '&nbsp;<a href="' . $header_url . '&amp;sort=hostDESC">' . $nav_down . '</a><a href="' . $header_url . '&amp;sort=hostASC">' . $nav_up . '</a></div>');
	$T->parse('CBlock', 'COLUMN', TRUE);

	$T->set_var('data', '<div align="center">' . (($day == 0) ? $LANG_GUS00['datetime'] : $LANG_GUS00['time']) . '&nbsp;<a href="' . $header_url . '&amp;sort=dateDESC">' . $nav_down . '</a><a href="' . $header_url . '&amp;sort=dateASC">' . $nav_up . '</a></div>');
	$T->parse('CBlock', 'COLUMN', TRUE);

	$T->set_var('data', '<div align="center">' . $LANG_GUS00['referer'] . '&nbsp;<a href="' . $header_url . '&amp;sort=refererDESC">' . $nav_down . '</a><a href="' . $header_url . '&amp;sort=refererASC">' . $nav_up . '</a></div>');
	$T->parse('CBlock', 'COLUMN', TRUE);

	$T->set_var('rowclass', 'header');
	$T->parse('BBlock', 'ROW', TRUE);

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
		
		$T->set_var('data', '<a href="' . $_CONF['site_url'] . '/gus/ip.php?year=' . $year . '&amp;month=' . $month . '&amp;day=' . $day . '&amp;ip_addr=' . $A['ip'] . '&amp;uid=' . $A['uid'] . '">' . $A['ip'] . '</a>');
		$T->parse('CBlock', 'COLUMN', TRUE);
		
		$T->set_var('data', '<a href="http://' . $A['host'] . '" target="_blank">' . $A['host'] . '</a>');
		$T->parse('CBlock', 'COLUMN', TRUE);
	
		$T->set_var('data', $A['date_formatted']);
		$T->parse('CBlock', 'COLUMN', TRUE);
		
		$the_data = GUS_template_get_referrer_data($A['referer']);
		$T->set_var('data', $the_data);
		$T->parse('CBlock', 'COLUMN', TRUE);
		$T->parse('BBlock', 'ROW', TRUE);
	}
	
	$T->parse('ABlock', 'TABLE', TRUE);
	$T->set_var('google_paging', $navlinks);

	if ($day != '') {
		$date_formatted = date('l, j F, Y - ', mktime(0, 0, 0, $month, $day, $year));
	} else {
		$date_formatted = date('F Y - ', mktime(0, 0, 0, $month, 1, $year));
	}

	$ip_str = ($ip != '') ? "$ip,&nbsp;" : '';
	$title = $date_formatted . $ip_str . $visited_page . '&nbsp;'
		   . $LANG_GUS00['page_views'];
	$display = GUS_template_finish($T, $title);

	if ($_GUS_cache AND ($today != $day . $month . $year)) {
		GUS_writecache($display);
	}
}

echo COM_siteHeader($_GUS_CONF['show_left_blocks']);
echo $display;
echo COM_siteFooter($_GUS_CONF['show_right_blocks']);
