<?php

// +---------------------------------------------------------------------------+
// | GUS Plugin                                                                |
// +---------------------------------------------------------------------------+
// | browser.php                                                               |
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

if (!GUS_HasAccess())
	exit;

require_once './include/sql.inc';
require_once './include/util.inc';

/* 
* Main Function
*/

// check for cached file
$today = date('MY');
if (file_exists(GUS_cachefile()) AND ($today !== $month . $year)) {
	$display = GUS_getcache();
} else {
	// no cached version
	$T = GUS_template_start();
	$T->set_block('page','COLUMN','CBlock');
	$T->set_block('page','ROW','BBlock');
	$T->set_block('page','TABLE','ABlock');

	$T->set_var(array(
		'colclass' => 'col_left',
		'data'     => $LANG_GUS00['browser']
	));
	$T->parse('CBlock', 'COLUMN', FALSE);

	$T->set_var('data', $LANG_GUS00['version']);
	$T->parse('CBlock', 'COLUMN', TRUE);

	$T->set_var(array(
		'colclass' => 'col_right',
		'data'     => $LANG_GUS00['page_views']
	));
	$T->parse('CBlock', 'COLUMN', TRUE);

	$T->set_var(array(
		'colclass' => 'col_right',
		'data'     => '%'
	));
	$T->parse('CBlock', 'COLUMN', TRUE);

	$T->set_var('rowclass', 'header');
	$T->parse('BBlock', 'ROW', TRUE);

	$date_compare = GUS_get_date_comparison('date', $year, $month);
	$tmp_name     = $_GUS_table_prefix . 'tempBrowser_' . $year . $month;
	$temp_table   = GUS_create_temp_table($tmp_name, "SELECT COUNT( browser ) AS count, browser, version
		FROM {$_TABLES['gus_userstats']}, {$_TABLES['gus_user_agents']}
		WHERE {$_TABLES['gus_userstats']}.ua_id = {$_TABLES['gus_user_agents']}.ua_id AND {$date_compare}
		GROUP BY browser, version ORDER BY count DESC");

	DB_query("SET @totalHits=0");
	DB_query("SELECT @totalHits := SUM(count) FROM {$temp_table['name']}");

	$sql = "SELECT SUM( count ) AS subtotal, FORMAT( (SUM( count ) / @totalHits) * 100.0, 2 ) as percent, browser
		FROM {$temp_table['name']}
		GROUP BY browser ORDER BY subtotal DESC";
	$result = DB_query($sql);

	while ($row = DB_fetchArray($result)) {
		$T->set_var(array(
			'rowclass' => 'row2',
			'colclass' => 'col_left'
		));
		$browser = $row['browser'];
		$T->set_var('data', $row['browser']);
		$T->parse('CBlock', 'COLUMN', FALSE);

		$T->set_var('data', '&nbsp;');
		$T->parse('CBlock', 'COLUMN', TRUE);

		$T->set_var(array(
			'colclass' => 'col_right',
			'data'     => $row['subtotal']
		));
		$T->parse('CBlock', 'COLUMN', TRUE);

		$T->set_var('data', $row['percent']);
		$T->parse('CBlock', 'COLUMN', TRUE);
		$T->parse( 'BBlock', 'ROW', true );

		$sql = "SELECT count, version
					FROM {$temp_table['name']}
					WHERE browser = '{$browser}'
					GROUP BY version ORDER BY count DESC";
		$browser_result = DB_query($sql);
	
		while ($browser_row = DB_fetchArray($browser_result)) {
			$T->set_var(array(
				'rowclass' => 'row1',
				'colclass' => 'col_left',
				'data'     => '&nbsp;',
			));
			
			$T->parse('CBlock', 'COLUMN', FALSE);
			
			$T->set_var('data', $browser_row['version']);
			$T->parse('CBlock', 'COLUMN', TRUE);

			$T->set_var(array(
				'colclass' => 'col_right',
				'data'     => $browser_row['count']
			));
			$T->parse('CBlock', 'COLUMN', TRUE);

			$T->set_var('data', '&nbsp;');
			$T->parse('CBlock', 'COLUMN', TRUE);
			
			$T->parse('BBlock', 'ROW', TRUE);
		}
	}
	
	$T->parse('ABlock', 'TABLE', TRUE);

	$title = date('F Y - ', mktime(0, 0, 0, $month, 1, $year))
		   . $LANG_GUS00['browsers'];
	$display = GUS_template_finish($T, $title);

	if ($_GUS_cache AND ($today !== $month . $year)) {
		GUS_writecache($display);
	}

	GUS_remove_temp_table($temp_table);
}

echo COM_siteHeader($_GUS_CONF['show_left_blocks']);
echo $display;
echo COM_siteFooter($_GUS_CONF['show_right_blocks']);
