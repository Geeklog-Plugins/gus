<?php

// +---------------------------------------------------------------------------+
// | GUS Plugin                                                                |
// +---------------------------------------------------------------------------+
// | sql.inc                                                                   |
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

function GUS_create_temp_userstats_table($year, $month) {
	global	$_GUS_VARS, $_TABLES, $_GUS_table_prefix;
	
	$tmp_name = $_GUS_table_prefix . 'temp_' . $year . $month;
	$Ym_date_format = "$year-" . ($month < 10 ? '0' : '' ) . $month;
	$table = GUS_create_temp_table($tmp_name, "SELECT * FROM {$_TABLES['gus_userstats']}
		WHERE date LIKE '{$Ym_date_format}%'");
	
	return $table;
}

//	$table_name		table to create
//	$sql			SQL to create the table
//
//	Returns array:
//		'name'		table name created
//		'used_temp'	did we successfully use TEMPORARY?
function GUS_create_temp_table($table_name, $sql) {
	global $_GUS_CONF;
		
	$temporary = $_GUS_CONF['SQL_use_TEMPORARY'] ? 'TEMPORARY' : '';
	DB_query("CREATE $temporary TABLE $table_name $sql", 1);

	// IF there was a problem creating the TEMPORARY TABLE
	//	THEN use a regular table
	if ($_GUS_CONF['SQL_use_TEMPORARY'] AND DB_error()) {
		DB_query("DROP TABLE IF EXISTS {$table_name}");
		DB_query("CREATE TABLE {$table_name} {$sql}", 1);
		$table = array('name' => $table_name, 'used_temp' => FALSE);
	} else {
		$table = array('name' => $table_name, 'used_temp' => $_GUS_CONF['SQL_use_TEMPORARY']);
	}
	
	return $table;
}

// $table is an array created by the function GUS_create_temp_table()
function GUS_remove_temp_table($table) {
	if (!$table['used_temp']) {
		DB_query("DROP TABLE IF EXISTS {$table['name']}");
	}
}

function GUS_get_date_comparison($field, $year, $month, $day = '') {
	global $_GUS_VARS;
  
	$date_formatted = "$year-";
	
	if ($month AND ($month != 0)) {
		$date_formatted .= ($month < 10 ? '0' : '') . $month;
	}
	
	if ($day AND ($day != 0)) {
		$date_formatted .= '-' . ($day < 10 ? '0' : '') . $day;
	}

	return "{$field} LIKE '{$date_formatted}%'";
}

function GUS_get_order_by($sort) {
	$sort_str = '';
	
	// sort options
	switch ($sort) {
		case 'ipASC':
		   $sort_str .= "ORDER BY ip ASC, date ASC, time ASC, username ASC";
		   break;
		   
		case 'ipDESC':
		   $sort_str .= "ORDER BY ip DESC, date DESC, time DESC, username ASC";
		   break;
		   
		case 'viewsASC':
		   $sort_str .= "ORDER BY views ASC, date ASC, time ASC, username ASC";
		   break;
		   
		case 'viewsDESC':
		   $sort_str .= "ORDER BY views DESC, date DESC, time DESC, username ASC";
		   break;

		case 'typeASC':
		   $sort_str .= "ORDER BY request ASC, date ASC, time ASC, username ASC";
		   break;
		   
		case 'typeDESC':
		   $sort_str .= "ORDER BY request DESC, date DESC, time DESC, username ASC";
		   break;
		   
		case 'ptuASC':
		   $sort_str .= "ORDER BY page ASC, date ASC, time ASC, username ASC";
		   break;
		   
		case 'ptuDESC':
		   $sort_str .= "ORDER BY page DESC, date DESC, time DESC, username ASC";
		   break;
		   
		case 'usernameASC':
		   $sort_str .= "ORDER BY username ASC, date ASC, time ASC";
		   break;
		   
		case 'usernameDESC':
		   $sort_str .= "ORDER BY username DESC, date DESC, time DESC";
		   break;
		   
		case 'hostASC':
		   $sort_str .= "ORDER BY host ASC, date ASC, time ASC, username ASC";
		   break;
		   
		case 'hostDESC':
		   $sort_str .= "ORDER BY host DESC, date DESC, time DESC, username ASC";
		   break;
		   
		case 'refererASC':
		   $sort_str .= "ORDER BY referer ASC, date ASC, time ASC, username ASC";
		   break;
		   
		case 'refererDESC':
		   $sort_str .= "ORDER BY referer DESC, date DESC, time DESC, username ASC";
		   break;
		   
		case 'dateASC':
		   $sort_str .= "ORDER BY date ASC, time ASC, username ASC";
		   break;
		   
		default: // same as date DESC
		   $sort_str .= "ORDER BY date DESC, time DESC, username ASC";
		   break;
	}
	
	return $sort_str;
}
