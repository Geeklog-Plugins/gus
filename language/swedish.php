<?php
###############################################################################
# lang.php
# This is the Swedish language page for GUS
#
# Copyright (C) 2002, 2003, 2005
# Andy Maloney - asmaloney@users.sf.net
# Tom Willett  - twillett@users.sourceforge.net
# Markus Berg  - markus@kelvin.nu
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
	'GUS_title'			=> 'Statistik',
    'main_menu_title'     => 'Besökarstatistik',
    'priv_pol'      => 'Sekretesspolicy',
    'links_followed'=> 'Följda länkar',
    'link'          => 'Länk',
    'type'          => 'Typ',
    'ptu'           => 'Sida/Titel/URL',
    'browsers'      => 'Webbläsare',
    'browser'       => 'Webbläsare',
    'version'       => 'Version',
    'platforms'     => 'Operativsystem',
    'platform'      => 'Operativsystem',
    'new_comments'  => 'Nya kommentarer',
    'comment_title' => 'Kommentarstitel',
    'datetime'      => 'Datum/Tid',
    'countries'     => 'Länder',
    'code'          => 'Kod',
    'referer'       => 'Hänvisning',
    'referers'      => 'Hänvisningar',
    'count'         => 'Antal',
    'new_stories'   => 'Nya artiklar',
    'story_title'   => 'Artikeltitel',
    'hits'          => 'Träffar',
    'user'          => 'Användare',
    'page'          => 'Sida',
    'pages'         => 'Sidor',
    'page_views'    => 'Visningar',
    'views_per_page'=> 'Visningar/sida',
    'views_per_hour'=> 'Visningar/timme',
    'hour'          => 'Timme',
    'ip'            => 'IP',
    'hostname'      => 'Datornamn',
    'anon_users'    => 'Anonyma',
    'unique_visitors' => 'Unika besök',
    'views'         => 'Visningar/besök',
    'total'         => 'Totalt',
    'daily_title'   => 'Daglig besökarstatistik',
    'monthly_title' => 'Månatlig besökarstatistik',
    'day_title'     => 'Dag',
    'month_title'   => 'Månad',
    'anon_title'    => 'Anonyma besökare',
    'reg_title'     => 'Inloggade besökare',
    'page_title'    => 'Antal visade sidor',
    'comm_title'    => 'Kommentarer',
    'link_title'    => 'Följda länkar',
    'hour_title'    => 'Per timme',
    'referer_title' => 'Hänvisning',
    'country_title' => 'Land',
    'browser_title' => 'Webbläsare',
    'platform_title' => 'Operativsystem',
	'access_denied' => 'Åtkomst nekas',
	'access_denied_msg' => 'Bara vissa användare har tillgång till den här sidan.  Ditt namn och IP har loggats.',
	'install_header'	=> 'Installera GUS',
	'sortDESC'			=> 'Sortera fallande',
	'sortASC'			=> 'Sortera stigande',
	'import_header'     => 'GUS Importera Data'
);

// Admin and user block entries
$LANG_GUS_blocks = array(
	'admin_menu_title'	=> 'GUS',
	
	'user_menu_latest'	=> 'GUS',
	'user_menu_today'	=> 'idag'
);
	
// Who's Online
$LANG_GUS_wo = array(
    'title'				=> "Vem är här",
    
	'stats'				=> 'Statistik',
	'reg_users'     	=> 'Inloggade',
	'referers'      	=> 'Hänvisningar',
	'new_users'         => 'Nya användare',
	'page_title'    	=> 'Antal visade sidor',
	'unique_visitors'	=> 'Unika besök'
);

// Builtin stats
$LANG_GUS_builtin_stats = array(
	'unique_visitors'	=> 'Unika besökare',
	'users'				=> 'Inloggade användare'
);

// Admin Page
$LANG_GUS_admin = array(
	'admin'		=> 'GUS Admin',
	
	'capture'		=> 'Statistikmodulen',
    'captureon'		=> 'Statistikmodulen är PÅ',
    'captureoff'	=> 'Statistikmodulen är AV',
    'turnon'		=> 'Aktivera statistikinsamling',
    'turnoff'		=> 'Avaktivera statistikinsamling',
    
    'instructions'	=> 'The Geeklog Usage Stats [GUS] plugin collects statistics on who visits your site, what browser and operating system they are using, which pages they view, and which links they are clicking to get there. It allows the administrator to browse these stats through a series of tables, getting right down into the data.',

	// Ignore section
	'ignore'    => 'Ignorera',
	
	'tip'		=> 'Tip:',
	'example'	=> 'Example:',
	
	'wildcard_tip'	=> 'Use % as a wildcard.  Matching uses the MySQL <a href="http://dev.mysql.com/doc/mysql/en/string-comparison-functions.html">LIKE</a> syntax.',
	
	'irreversible'	=> '<b>This is irreversible</b>, so make sure you really want to do this.',
	
	'clean_msg1'		=> 'Based on these filters, I took a quick look and have found entries in your database which match.',
	'clean_msg2'		=> 'Would you like me to clean these up?',
	'clean_num_entries'	=> 'Number of matching entries',
	'clean_up'			=> 'Clean Up',
	'star'				=> '* This section has some data which you may want to clean out of the database.',
	
	'add'		=> 'Lägg till',
	'remove'    => 'Radera',
	
	// IP
	'ip_title'		=> "IP-addresser",
	'ip_tip'		=> 'Your IP address is',
	'ip_example'	=> 'Using 123.0.1.% will ignore all addresses in the range 123.0.1.0 - 123.0.1.255.  Using 123.0.1% will ignore all of those <i>and</i> 123.0.10.% - 123.0.199.% - be careful of the period!',
	'ip_num_ip'		=> 'Number of matching IP addresses',
	
	// User
	'user_title'	=> "Användaren",
	'user_num_user'	=> 'Number of matching users',
	
	// Page
	'page_title'	=> "Sidor",
	'page_num_page'	=> 'Number of matching pages',
	
	// User Agent
	'ua_title'		=> "User Agents",
	'ua_example'	=> 'Using %Googlebot% will ignore any user agent containing the term Googlebot.',
	'ua_num_ua'		=> 'Number of matching user agents',

	// Host
	'host_title'	=> "Host Names",
	'host_tip'		=> 'Your host name appears to be',
	'host_example'	=> 'Using %.googlebot.com will ignore the google bot.',
	'host_num_host'	=> 'Number of matching hosts',

	// Referrer
	'referrer_title'	    => 'Referrers',
	'referrer_example'	    => 'Using %images.google.% will ignore any pages referred from any of the google image sites.',
	'referrer_num_referrer'	=> 'Number of matching referrers',

	// Remove Data
	'remove_data'	=> 'Radera Data',
	
	// Import data
	'import_data'	=> 'Importera Data',
);

?>