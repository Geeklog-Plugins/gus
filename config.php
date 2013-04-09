<?php

// +---------------------------------------------------------------------------+
// | GUS Plugin                                                                |
// +---------------------------------------------------------------------------+
// | config.php                                                                |
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
//

// GUS Who's Online Block
// Executes the function phpblock_gusstats()
$_GUS_CONF['block_enable'] = true; // True or false
$_GUS_CONF['block_isleft'] = 0; // Whether to display the block on the left (when set to 1) or right (when set to 0).
$_GUS_CONF['block_order'] = 0; // The order the block appears in the column.
$_GUS_CONF['block_topic_option'] = TOPIC_HOMEONLY_OPTION; // TOPIC_ALL_OPTION, TOPIC_HOMEONLY_OPTION, TOPIC_SELECTED_OPTION - Set to 'All' for block to appear on all pages. Set to 'Homepage Only' for block to appear on just the homepage. Set to 'Selected Topics' for block to appear only in Topics selected in the Topic setting.
$_GUS_CONF['block_topic'] = array(); // The topics the block will appear for if the Topic Options is set to 'Select Topics'.
$_GUS_CONF['block_permissions'] = array (2, 2, 0, 0); // 0 = No Access, 2 = Read-Only for (Owner, Group, Member, Anonymous)

// The method to use for host name lookup - can be 'host', 'nslookup', or 'gethostbyaddr'
// If your system is set up such that you cannot execute shell commands, use 'gethostbyaddr'.
$_GUS_CONF['host_lookup'] = 'host';

// sets the timeout for host name lookup using 'host' or 'nslookup' [minimum 1 second]
$_GUS_CONF['host_lookup_timeout'] = 1;

// Set this to FALSE if you know you cannot use temporary tables in your MySQL setup.
// If you aren't sure, then leave it set to TRUE - everything will still work properly.
//	This is only used as a slight optimization - it doesn't have to try to use TEMPORARY tables first.
$_GUS_CONF['SQL_use_TEMPORARY'] = TRUE;

// Set these to show or hide the left and right blocks
$_GUS_CONF['show_left_blocks'] = TRUE;
$_GUS_CONF['show_right_blocks'] = FALSE;

// Set this to TRUE if you want to be able to ignore the user 'Anonymous'
//	Leaving it FALSE allows a slight optimisation by eliminating a db lookup
$_GUS_CONF['allow_ignore_anonymous'] = FALSE;

// Set this to the referrers you DO NOT want to show up in the day summary.
//	Note that the data is still collected, it is just not visible in the day summary.
$_GUS_CONF['hide_in_day_summary'] = array(
		$_CONF['site_url']
	//	, 'http://images.google.com'	// hide all images.google.com referrers
	//	, 'http://images.google.'		// hide all images.google.* referrers
		 );

// This the URL for the Whois lookup. This uses www.whois.sc
// If you have an alternate source of this information supply it here.
$_GUS_Whois_URL_start = '<a href="http://www.whois.sc/';
$_GUS_Whois_URL_end = '" target="_blank">';

// The icon to use for the GUS pages - located in the public_html/gus/images/ directory
$_GUS_IMG_name = "GUS48.png";
$_GUS_IMG_small_name = "GUS24.png";

// Enable anononymous access to summary stats
$_GUS_anon_access = 0;

// Show a link in the main menu if user has permission to access the stats
$_GUS_enable_main_menu_GUS = 0;

// Show a link to the privacy policy in the main menu
$_GUS_enable_main_menu_privacy_policy = 0;

// Set to 1 to enable user stats menu option in the 'User Functions' block
$_GUS_user = 1;

// Set to 1 to extend the regular Geeklog stats page to include 'Unique Visitors' and 'Registered Users'
$_GUS_stats = 1;

// Set to 1 to enable phplinks integration
$_GUS_phplinks = 0;

// Limit on number of lines to display on certain stats reports
$_GUS_limit = 25;

// Number of months displayed on the index page
$_GUS_months = 4;

// Number of days on each page
$_GUS_days = 16;

// Enable the caching of stats
$_GUS_cache = FALSE;

// The following variables allow custom configuration of WhosOnline Block.

// If set to TRUE then the full name is displayed if available instead of username
$_GUS_CONF['wo_fullname'] = FALSE;

// If set to TRUE, show only a count of users for Who's Online, Registered Users, and New Users to anon users
$_GUS_CONF['wo_users_anonymous'] = TRUE;

// If set to TRUE, show a list of who's online
$_GUS_CONF['wo_online'] = TRUE;

// If set to TRUE, then show bots as they access your site
$_GUS_CONF['wo_show_bots'] = TRUE;

// If set to TRUE, show Registered users
$_GUS_CONF['wo_registered'] = TRUE;

// If set to TRUE, show New users
$_GUS_CONF['wo_new'] = TRUE;

// If set to TRUE, show daily usage stats
$_GUS_CONF['wo_daily'] = TRUE;

// If set to TRUE, show referrers
$_GUS_CONF['wo_refs'] = TRUE;

// Set this to the referrers you DO NOT want to show up in the Who's Online block.
//	Note that the data is still collected, it is just not visible in the block.
$_GUS_CONF['wo_hide_referrers'] = array(
		$_CONF['site_url']
	//	, 'http://images.google.com'	// hide all images.google.com referrers
	//	, 'http://images.google.'		// hide all images.google.* referrers
		 );

// Maximum number of referrers to show
$_GUS_CONF['wo_max_referrers'] = 100;


// DO NOT CHANGE THE STUFF BELOW UNLESS YOU KNOW WHAT YOU ARE DOING
$_GUS_table_prefix = $_DB_table_prefix . 'gus_';

$_TABLES['gus_userstats']       = $_GUS_table_prefix . 'userstats';
$_TABLES['gus_user_agents']     = $_GUS_table_prefix . 'user_agents';
$_TABLES['gus_ignore_ip']       = $_GUS_table_prefix . 'ignore_ip';
$_TABLES['gus_ignore_user']     = $_GUS_table_prefix . 'ignore_user';
$_TABLES['gus_ignore_page']     = $_GUS_table_prefix . 'ignore_page';
$_TABLES['gus_ignore_ua']       = $_GUS_table_prefix . 'ignore_ua';
$_TABLES['gus_ignore_host']     = $_GUS_table_prefix . 'ignore_host';
$_TABLES['gus_ignore_referrer'] = $_GUS_table_prefix . 'ignore_referrer';
$_TABLES['gus_vars']            = $_GUS_table_prefix . 'vars';
