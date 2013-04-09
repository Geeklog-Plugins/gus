<?php

// +---------------------------------------------------------------------------+
// | GUS Plugin                                                                |
// +---------------------------------------------------------------------------+
// | pages.php                                                                 |
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

require_once './include/security.inc';

if (!GUS_HasAccess(FALSE)) {
	exit;
}

require_once './include/sql.inc';
require_once './include/util.inc';

/*
* Main Function
*/
// check for cached file
if ($day == '') {
	$today = date('jY');
} else {
	$today = date('djY');
}

if (file_exists(GUS_cachefile()) AND ($today != $day . $month . $year)) {
	$display = GUS_getcache();
} else {
	// no cached version
	$T = GUS_template_start();
	$T->set_block('page', 'COLUMN', 'CBlock');
	$T->set_block('page', 'ROW', 'BBlock');
	$T->set_block('page', 'TABLE', 'ABlock');
	$T->set_var(array(
		'colclass' => 'col_left',
		'data'     => $LANG_GUS00['page'],
	));
	$T->parse('CBlock', 'COLUMN', FALSE);

	$T->set_var(array(
		'colclass' => 'col_right',
		'data'     => $LANG_GUS00['page_views'],
	));
	$T->parse('CBlock', 'COLUMN', TRUE);

	$T->set_var('rowclass', 'header');
	$T->parse('BBlock', 'ROW', TRUE);

	$date_compare = GUS_get_date_comparison('date', $year, $month, $day);
	$rec = DB_query("SELECT COUNT(*) AS count, page FROM {$_TABLES['gus_userstats']} 
					WHERE {$date_compare}
					GROUP BY page ORDER BY count DESC");
	$nrows = DB_numRows($rec);
	$pageviews = 0;
	
	for ($i = 0; $i < $nrows; $i++) {
		if (($i + 1) % 2) {
			$T->set_var('rowclass', 'row1');
		} else {
			$T->set_var('rowclass', 'row2');
		}

		$A = DB_fetchArray($rec);
		$T->set_var(array(
			'colclass' => 'col_left',
			'data'     => '<a href="' . $_CONF['site_url'] . '/gus/page.php?year=' . $year . '&amp;month=' . $month . '&amp;day=' . $day . '&amp;visited_page=' . $A['page'] . '">' . $A['page'] . '</a>',
		));
		$T->parse('CBlock', 'COLUMN', FALSE);
		
		$T->set_var(array(
			'colclass' => 'col_right',
			'data'     => $A['count'],
		));
		$pageviews += $A['count'];
		$T->parse('CBlock', 'COLUMN', TRUE);
		$T->parse('BBlock', 'ROW', TRUE);
	}
	
	$T->set_var(array(
		'colclass' => 'col_left',
		'data'     => $LANG_GUS00['total'],
	));
	$T->parse('CBlock', 'COLUMN', FALSE);
	$T->set_var(array(
		'colclass' => 'col_right',
		'data'     => $pageviews,
	));
	$T->parse('CBlock', 'COLUMN', TRUE);

	$T->set_var('rowclass', 'totals');
	$T->parse('BBlock', 'ROW', TRUE);
	$T->parse('ABlock', 'TABLE', TRUE);

	if ($day != '') {
		$date_formatted = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
		$rec = DB_query("SELECT DISTINCT ip FROM {$_TABLES['gus_userstats']}
				WHERE date = '{$date_formatted}'");
	} else {
		$rec = DB_query("SELECT ip FROM {$_TABLES['gus_userstats']}
				WHERE MONTH( date ) = {$month} AND YEAR( date ) = {$year}
				GROUP BY ip, DAYOFMONTH( date )");
	}
	
	$visitors = DB_numRows($rec);
	$T->set_var('summary',"$pageviews {$LANG_GUS00['total']} {$LANG_GUS00['page_title']}<br>$visitors {$LANG_GUS00['unique_visitors']}<br>" . sprintf("%01.2f", $pageviews/$visitors) . " {$LANG_GUS00['views']}.<br>");

	if ($day != '') {
		$title = date('l, j F, Y - ', mktime(0, 0, 0, $month, $day, $year))
			   . $LANG_GUS00['views_per_page'];
	} else {
		$title = date('F Y - ', mktime(0, 0, 0, $month, 1, $year))
			   . $LANG_GUS00['views_per_page'];
	}

	$display = GUS_template_finish($T, $title);

	if ($_GUS_cache AND ($today != $day . $month . $year)) {
		GUS_writecache($display);
	}
}

echo COM_siteHeader($_GUS_CONF['show_left_blocks']);
echo $display;
echo COM_siteFooter($_GUS_CONF['show_right_blocks']);
