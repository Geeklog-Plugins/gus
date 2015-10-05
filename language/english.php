<?php
###############################################################################
# english.php
# This is the English language page for GUS
#
# Copyright (C) 2002, 2003, 2005
# Andy Maloney - asmaloney@users.sf.net
# Tom Willett  - twillett@users.sourceforge.net
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
#
###############################################################################

/**
* General language
*/

$LANG_GUS00 = array (
	'GUS_title'			=> 'GUS',
    'main_menu_title'     => 'Visitor Statistics',
    'priv_pol'      => 'Privacy Policy',
    'links_followed'=> 'Links Followed',
    'link'          => 'Link',
    'type'          => 'Type',
    'ptu'           => 'Page/Title/URL',
    'browsers'      => 'Browsers',
    'browser'       => 'Browser',
    'version'       => 'Version',
    'platforms'     => 'Platforms',
    'platform'      => 'Platform',
    'new_comments'  => 'New Comments',
    'comment_title' => 'Comment Title',
    'datetime'      => 'Date/Time',
    'time'			=> 'Time',
    'countries'     => 'Countries',
    'code'          => 'Code',
    'referer'       => 'Referrer',
    'referers'      => 'Referrers',
    'count'         => 'Count',
    'new_stories'   => 'New Stories',
    'story_title'   => 'Story Title',
    'hits'          => 'Hits',
    'user'          => 'User',
    'page'          => 'Page',
    'pages'         => 'Pages',
    'page_views'    => 'Page Views',
    'views_per_page'=> 'Views/Page',
    'views_per_hour'=> 'Views/Hour',
    'hour'          => 'Hour',
    'ip'            => 'IP',
    'host'			=> 'Host',
    'hostname'      => 'Host Name',
    'anon_users'    => 'Anon Users',
	'reg_users'     => 'Reg Users',
    'unique_visitors' => 'Unique Visits',
    'views'         => 'Views/Visit',
    'total'         => 'Total',
    'daily_title'   => 'Visitor Statistics by Day',
    'monthly_title' => 'Visitor Statistics by Month',
    'day_title'     => 'Day',
    'month_title'   => 'Month',
    'anon_title'    => 'Anonymous Visitors',
    'reg_title'     => 'Registered Visitors',
    'page_title'    => 'Pages Viewed',
    'comm_title'    => 'Comments',
    'link_title'    => 'Links Followed',
    'hour_title'    => 'By Hour',
    'referer_title' => 'Referer',
    'country_title' => 'Country',
    'browser_title' => 'Browser',
    'platform_title' => 'Platform',
	'access_denied' => 'Access Denied',
	'access_denied_msg' => 'Only certain users have access to this page.  Your name and IP have been recorded.',
	'install_header'	=> 'Install GUS',
	'sortDESC'			=> 'Sort descending order',
	'sortASC'			=> 'Sort ascending order',
	'import_header'     => 'GUS Import Data'
);

// Admin and user block entries
$LANG_GUS_blocks = array(
	'admin_menu_title'	=> 'GUS',
	
	'user_menu_title'	=> 'GUS',
	'today'				=> 'today'
);
	
// Who's Online
$LANG_GUS_wo = array(
    'title'				=> "Who's Online",
    
	'bots'				=> 'Bots',
	'stats'				=> 'Stats',
	'reg_users'     	=> 'Reg Users',
	'referers'      	=> 'Referrers',
	'new_users'         => 'New Users',
	'page_title'    	=> 'Pages Viewed',
	'unique_visitors'	=> 'Unique Visits'
);

// Builtin stats
$LANG_GUS_builtin_stats = array(
	'unique_visitors'	=> 'Unique Visitors',
	'users'				=> 'Registered Users'
);

// Admin Page
$LANG_GUS_admin = array(
	'admin'		=> 'GUS Admin',
	
	'capture'		=> 'Capture',
	'captureon'		=> 'Statistics capture is ON',
	'captureoff'	=> 'Statistics capture is OFF',
	'turnon'		=> 'Turn Capture On',
	'turnoff'		=> 'Turn Capture Off',
    
    'instructions'	=> 'The Geeklog Usage Stats [GUS] plugin collects statistics on who visits your site, what browser and operating system they are using, which pages they view, and which links they are clicking to get there. It allows the administrator to browse these stats through a series of tables, getting right down into the data.',

	// Ignore section
	'ignore'    => 'Ignore',
	
	'tip'		=> 'Tip:',
	'example'	=> 'Example:',
	
	'wildcard_tip'	=> 'Use % as a wildcard.  Matching uses the MySQL <a href="http://dev.mysql.com/doc/mysql/en/string-comparison-functions.html">LIKE</a> syntax.',
	
	'irreversible'	=> '<b>This is irreversible</b>, so make sure you really want to do this.',
	
	'clean_msg1'		=> 'Based on these filters, I took a quick look and have found entries in your database which match.',
	'clean_msg2'		=> 'Would you like me to clean these up?',
	'clean_num_entries'	=> 'Number of matching entries',
	'clean_up'			=> 'Clean Up',
	'star'				=> '* This section has some data which you may want to clean out of the database.',
	
	'add'		=> 'Add',
	'remove'    => 'Remove',
	
	// IP
	'ip_title'		=> 'IP Addresses',
	'ip_tip'		=> 'Your IP address is',
	'ip_example'	=> 'Using 123.0.1.% will ignore all addresses in the range 123.0.1.0 - 123.0.1.255.  Using 123.0.1% will ignore all of those <i>and</i> 123.0.10.% - 123.0.199.% - be careful of the period!',
	'ip_num_ip'		=> 'Number of matching IP addresses',
	
	// User
	'user_title'	=> 'Users',
	'user_num_user'	=> 'Number of matching users',
	
	// Page
	'page_title'	=> 'Pages',
	'page_num_page'	=> 'Number of matching pages',
	
	// User Agent
	'ua_title'		=> 'User Agents',
	'ua_example'	=> 'Using %Googlebot% will ignore any user agent containing the term Googlebot.',
	'ua_num_ua'		=> 'Number of matching user agents',

	// Host
	'host_title'	=> 'Host Names',
	'host_tip'		=> 'Your host name appears to be',
	'host_example'	=> 'Using %.googlebot.com will ignore the google bot.',
	'host_num_host'	=> 'Number of matching hosts',

	// Referrer
	'referrer_title'	    => 'Referrers',
	'referrer_example'	    => 'Using %images.google.% will ignore any pages referred from any of the google image sites.',
	'referrer_num_referrer'	=> 'Number of matching referrers',

	// Remove Data
	'remove_data'	=> 'Remove Data',
	
	// Import data
	'import_data'	=> 'Import Data',
);

?>
