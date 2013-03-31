<?php

// +---------------------------------------------------------------------------+
// | GUS Plugin                                                                |
// +---------------------------------------------------------------------------+
// | gus.php                                                                   |
// | Contains all the SQL necessary to install the GUS plugin                  |
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

$_SQL = array();

$_SQL[] = "
CREATE TABLE IF NOT EXISTS {$_TABLES['gus_userstats']} (
  uid INT(10) NOT NULL DEFAULT '1',
  ip VARCHAR(20) DEFAULT '0.0.0.0',
  host VARCHAR(75) DEFAULT '',
  page VARCHAR(50) DEFAULT '',
  username VARCHAR(16) DEFAULT '',
  referer VARCHAR(255) DEFAULT '',
  request VARCHAR(10) DEFAULT '',
  query_string VARCHAR(255) DEFAULT '',
  `date` DATE DEFAULT NULL,
  `time` TIME NOT NULL DEFAULT '00:00:00',
  ua_id SMALLINT(5) unsigned NOT NULL DEFAULT '0',
  KEY uid (uid),
  KEY ip (ip),
  KEY page (page),
  KEY refer (referer),
  KEY ua_id (ua_id),
  KEY `date` (`date`)
)";

$_SQL[] = "
CREATE TABLE IF NOT EXISTS {$_TABLES['gus_user_agents']} (
  ua_id SMALLINT(5) unsigned NOT NULL auto_increment,
  user_agent VARCHAR(128) DEFAULT NULL,
  browser VARCHAR(20) DEFAULT NULL,
  version VARCHAR(10) DEFAULT NULL,
  platform VARCHAR(30) DEFAULT NULL,
  PRIMARY KEY (ua_id),
  UNIQUE KEY user_agent (user_agent)
)";

$_SQL[] = "
CREATE TABLE IF NOT EXISTS {$_TABLES['gus_ignore_ip']} (
  ip VARCHAR(20) NOT NULL DEFAULT '0.0.0.0',
  PRIMARY KEY (ip)
)";

$_SQL[] = "
CREATE TABLE IF NOT EXISTS {$_TABLES['gus_ignore_user']} (
  username VARCHAR(16) NOT NULL DEFAULT '',
  PRIMARY KEY (username)
)";

$_SQL[] = "
CREATE TABLE IF NOT EXISTS {$_TABLES['gus_ignore_page']} (
  page VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (page)
)";

$_SQL[] = "
CREATE TABLE IF NOT EXISTS {$_TABLES['gus_ignore_ua']} (
  ua VARCHAR(128) NOT NULL DEFAULT '',
  PRIMARY KEY (ua)
)";

$_SQL[] = "
CREATE TABLE IF NOT EXISTS {$_TABLES['gus_ignore_host']} (
  host VARCHAR(128) NOT NULL DEFAULT '',
  PRIMARY KEY (host)
)";

$_SQL[] = "
CREATE TABLE IF NOT EXISTS {$_TABLES['gus_ignore_referrer']} (
  referrer VARCHAR(128) NOT NULL DEFAULT '',
  PRIMARY KEY (referrer)
)";

$_SQL[] = "
CREATE TABLE IF NOT EXISTS {$_TABLES['gus_vars']} (
  name VARCHAR(10) NOT NULL DEFAULT '',
  `value` VARCHAR(32) DEFAULT '',
  PRIMARY KEY (name)
)";

$_DATA = array();

// By DEFAULT ignore current IP
$_DATA[] = "INSERT IGNORE INTO {$_TABLES['gus_ignore_ip']} VALUES ('" . $_GUS_VARS['remote_ip'] . "')";

// By DEFAULT ignore current user
$_DATA[] = "INSERT IGNORE INTO {$_TABLES['gus_ignore_user']} VALUES ('" . $_USER['username'] . "')";

// Var that controls capture of info
$_DATA[] = "INSERT IGNORE INTO {$_TABLES['gus_vars']} VALUES ( 'capture', '1' )";
$_DATA[] = "INSERT IGNORE INTO {$_TABLES['gus_vars']} VALUES ( 'imported', '0' )";
