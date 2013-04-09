<?php

// +---------------------------------------------------------------------------+
// | GUS Plugin                                                                |
// +---------------------------------------------------------------------------+
// | day.php                                                                   |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2002, 2003, 2005 by the following authors:                  |
// |                                                                           |
// | Authors: Andy Maloney      - asmaloney@users.sf.net                       |
// |          Tom Willett       - twillett@users.sourceforge.net               |
// |          John Hughes       - jlhughes@users.sf.net                        |
// |          Danny Ledger      - danny@squatty.com                            |
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

/*
* Main Function
*/

// IF nothing is set THEN use today
if (($day == 0) AND ($month == 0) AND ($year == 0)) {
	$year  = date('Y');
	$month = date('n');
	$day   = date('j');
	
	$sort_sep = '?';
} else {	
	$sort_sep = '&amp;';
}

$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$anon = isset($_GET['anon']) ? $_GET['anon'] : '';

// check for cached file
if (file_exists(GUS_cachefile()) AND (date('dMY') !== $day . $month . $year)) {
	$display = GUS_getcache();
} else {
	// no cached version

	//main sql option
	$date_compare = GUS_get_date_comparison('date', $year, $month, $day);
	$date_format = ($day == 0)
				 ? 'CONCAT( DATE_FORMAT( date, \'%d %b - \' ), TIME_FORMAT( time, \'%H:%i\' ) )'
				 : 'TIME_FORMAT( time, \'%H:%i\' )';
	$order_by = GUS_get_order_by($sort);
	$sql = "SELECT COUNT(*) AS views, uid, username, ip, host, referer, date, time, 
			{$date_format} AS date_formatted 
			FROM {$_TABLES['gus_userstats']}
			WHERE {$date_compare} ";

	// anon or uid
	if ($anon == '1') {
		$sql .= " AND uid = '1'";
	} else if ($anon == '2') {
		$sql .= " AND uid > '1'";
	}

	// main sql grouping
	$sql .= " GROUP BY date, uid, ip {$order_by}";

	// create navlinks AND set urls
	$totalrec  = DB_query($sql);
	$totalrows = DB_numRows($totalrec);

	$num_pages = ceil($totalrows / $_GUS_limit);

	if (!isset($_GET['page']) OR empty($_GET['page'])) {
		$curpage = 1; 
	} else {
		$curpage = (int) $_GET['page'];
	}

	$header_url = GUS_create_url('sort') . $sort_sep;
	$base_url   = GUS_create_url('page');
	$navlinks   = COM_printPageNavigation($base_url, $curpage, $num_pages);

	// limit to the sql
	$offset = ($curpage - 1) * $_GUS_limit;
	$sql .= " LIMIT " . $offset . ', ' . $_GUS_limit;

	$rec   = DB_query($sql);
	$nrows = DB_numRows($rec);

	// template calls
	$T = GUS_template_start();
	$T->set_var('additional_nav', GUS_make_nav($day, $month, $year));

	$T->set_block('page', 'COLUMN', 'CBlock');
	$T->set_block('page', 'ROW', 'BBlock');
	$T->set_block('page', 'TABLE', 'ABlock');

	$nav_down = "<br><img src=\"{$_CONF['site_url']}/gus/images/nav_down.gif\" width=\"13\" height=\"11\" alt=\"{$LANG_GUS00['sortDESC']}\" border=\"0\">";
	$nav_up = "<img src=\"{$_CONF['site_url']}/gus/images/nav_up.gif\" width=\"13\" height=\"11\" alt=\"{$LANG_GUS00['sortASC']}\" border=\"0\">";

	$T->set_var(array(
		'colclass' => 'col_right',
		'data'     => '<div align="center">' . $LANG_GUS00['page_views'] . '&nbsp;<a href="' . $header_url . 'sort=viewsDESC">' . $nav_down .'</a><a href="' . $header_url . 'sort=viewsASC">' . $nav_up . '</a></div>'
	));
	$T->parse('CBlock', 'COLUMN', FALSE);
	$T->set_var(array(
		'colclass' => 'col_left',
		'data'     => '<div align="center">' . $LANG_GUS00['user'] . '&nbsp;<a href="' . $header_url . 'sort=usernameDESC">' . $nav_down .'</a><a href="' . $header_url . 'sort=usernameASC">' . $nav_up . '</a></div>',
	));
	$T->parse('CBlock', 'COLUMN', TRUE);

	$T->set_var('data','<div align="center">' . $LANG_GUS00['host'] . '&nbsp;<a href="' . $header_url . 'sort=hostDESC">' . $nav_down .'</a><a href="' . $header_url . 'sort=hostASC">' . $nav_up . '</a></div>');
	$T->parse('CBlock', 'COLUMN', TRUE);

	$T->set_var('data','<div align="center">' . (($day == 0) ? $LANG_GUS00['datetime'] : $LANG_GUS00['time']) . '&nbsp;<a href="' . $header_url . 'sort=dateDESC">' . $nav_down .'</a><a href="' . $header_url . 'sort=dateASC">' . $nav_up . '</a></div>');
	$T->parse('CBlock', 'COLUMN', TRUE);

	$T->set_var('data','<div align="center">' . $LANG_GUS00['referer'] . '&nbsp;<a href="' . $header_url . 'sort=refererDESC">' . $nav_down .'</a><a href="' . $header_url . 'sort=refererASC">' . $nav_up . '</a></div>');
	$T->parse('CBlock', 'COLUMN', TRUE);

	$T->set_var('rowclass', 'header');
	$T->parse('BBlock', 'ROW', TRUE);

	//process sql results
	for ($i = 0; $i < $nrows; $i++) {
		if (($i + 1) % 2) {
			$T->set_var('rowclass', 'row1');
		} else {
			$T->set_var('rowclass', 'row2');
		}

		$row = DB_fetchArray($rec);
		$T->set_var(array(
			'colclass' => 'col_right',
			'data'     => '<a href="' . $_CONF['site_url'] . '/gus/page.php?year=' . $year . '&amp;month=' . $month . '&amp;day=' . $day . '&amp;ip=' . $row['ip'] . '&amp;uid=' . $row['uid'] . '">' . $row['views'] . '</a>'
		));
		$T->parse('CBlock', 'COLUMN', FALSE);
		$T->set_var('colclass', 'col_left');

		$the_data = GUS_template_get_user_data($row['uid'], $row['username']);
		$T->set_var('data', $the_data);
		$T->parse('CBlock', 'COLUMN', TRUE);
		
		$ip = '';
	 	//   if ( $row['ip'] != $row['host'] )
	 	//   	$ip = ' [' . $row['ip'] . ']';

		$T->set_var('data','<a href="' . $_CONF['site_url'] . '/gus/ip.php?year=' . $year . '&amp;month=' . $month . '&amp;day=' . $day . '&amp;ip_addr=' . $row['ip'] . '&amp;uid=' . $row['uid'] . '">' . $row['host'] . '</a>' . $ip);
		$T->parse('CBlock', 'COLUMN', TRUE);
		
		$T->set_var('data', $row['date_formatted']);
		$T->parse('CBlock', 'COLUMN', TRUE);
		
		$the_data = GUS_template_get_referrer_data($row['referer'], TRUE);
		$T->set_var('data', $the_data);
		$T->parse('CBlock', 'COLUMN', TRUE);
   		
		$T->parse('BBlock', 'ROW', TRUE);
	}

	$T->parse('ABlock', 'TABLE', TRUE);
	$T->set_var('google_paging', $navlinks);

	if ($day != '') {
		$title = date('l, j F, Y - ', mktime(0, 0, 0, $month, $day, $year))
			   . $LANG_GUS00['views'];
	} else if ($month != '') {
		$title = date('F Y - ', mktime(0, 0, 0, $month, 1, $year))
			   . $LANG_GUS00['views'];
	} else {
		$title = date('Y ', mktime(0, 0, 0, 1, 1, $year)) . $LANG_GUS00['views'];
	}

	$display = GUS_template_finish($T, $title);

	if ($_GUS_cache AND (date('dMY') !== $day . $month . $year)) {
		GUS_writecache($display);
	}
}

echo COM_siteHeader($_GUS_CONF['show_left_blocks']);
echo $display;
echo COM_siteFooter($_GUS_CONF['show_right_blocks']);
