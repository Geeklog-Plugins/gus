<?php

// +---------------------------------------------------------------------------+
// | GUS Plugin                                                                |
// +---------------------------------------------------------------------------+
// | stories.php                                                               |
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

if (!GUS_HasAccess()) {
	exit;
}

require_once './include/sql.inc';
require_once './include/util.inc';

/*
* Main Function
*/
// check for cached file
if ($day == '') {
	$today = date('MY');
} else {
	$today = date('dMY');
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
		'colclass' => 'col_right',
		'data'     => $LANG_GUS00['hits'],
	));
	$T->parse('CBlock', 'COLUMN', FALSE);
	$T->set_var(array(
		'colclass' => 'col_left',
		'data'     => $LANG_GUS00['story_title'],
	));
	$T->parse('CBlock', 'COLUMN', TRUE);

	$T->set_var('data', $LANG_GUS00['user']);
	$T->parse('CBlock', 'COLUMN', TRUE);

	$T->set_var('data', $LANG_GUS00['datetime']);
	$T->parse('CBlock', 'COLUMN', TRUE);

	$T->set_var('rowclass', 'header');
	$T->parse('BBlock', 'ROW', TRUE);

	$date_compare = GUS_get_date_comparison('date', $year, $month, $day);
	$sql = "SELECT sid, uid, title, hits, date FROM {$_TABLES['stories']} 
			WHERE {$date_compare}
			ORDER BY hits DESC";
	$rec   = DB_query($sql);
	$nrows = DB_numRows($rec);
	
	for ($i = 0; $i < $nrows; $i++) {
		if (($i + 1) % 2) {
			$T->set_var('rowclass', 'row1');
		} else {
			$T->set_var('rowclass', 'row2');
		}

		$A = DB_fetchArray($rec);
		$T->set_var(array(
			'colclass' => 'col_right',
			'data'     => $A['hits'],
		));
		$T->parse('CBlock', 'COLUMN', FALSE);
		$T->set_var(array(
			'colclass' => 'col_left',
			'data'     => '<a href="' . $_CONF['site_url'] . '/article.php?story=' . $A['sid'] . '">' . $A['title'] . '</a>',
		));
		$T->parse('CBlock', 'COLUMN', TRUE);

		$username = DB_getItem($_TABLES['users'], 'username', "uid='" . $A['uid'] . "' LIMIT 1");
		$the_data = GUS_template_get_user_data($A['uid'], $username);
		$T->set_var('data', $the_data);
		$T->parse('CBlock', 'COLUMN', TRUE);

		$da = COM_getUserDateTimeFormat($A['date']);
		$T->set_var('data', $da[0]);
		$T->parse('CBlock', 'COLUMN', TRUE);
		$T->parse('BBlock', 'ROW', TRUE);
	}
	
	$T->parse('ABlock', 'TABLE', TRUE);

	if ($day != '') {
		$title = date('l, j F, Y - ', mktime(0, 0, 0, $month, $day, $year))
			   . $LANG_GUS00['new_stories'];
	} else {
		$title = date('F Y - ', mktime(0, 0, 0, $month, 1, $year))
			   . $LANG_GUS00['new_stories'];
	}

	$display = GUS_template_finish($T, $title);

	if ($_GUS_cache AND ($today != $day . $month . $year)) {
		GUS_writecache($display);
	}
}

echo COM_siteHeader($_GUS_CONF['show_left_blocks']);
echo $display;
echo COM_siteFooter($_GUS_CONF['show_right_blocks']);
