<?php
###############################################################################
# dutch.php
# This is the Dutch language file for GUS
#
# Copyright (C) 2002, 2003, 2005
# Andy Maloney - asmaloney@users.sf.net
# Tom Willett  - twillett@users.sourceforge.net
# Mark van der Heijden
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
    'main_menu_title'     => 'Bezoeker Statistieken',
    'priv_pol'      => 'Privacy Policy',
    'links_followed'=> 'Links Gevolgd',
    'link'          => 'Link',
    'type'          => 'Type',
    'ptu'           => 'Pagina/Titel/URL',
    'browsers'      => 'Browsers',
    'browser'       => 'Browser',
    'version'       => 'Versie',
    'platforms'     => 'Platforms',
    'platform'      => 'Platform',
    'new_comments'  => 'Nieuwe Reacties',
    'comment_title' => 'Reactie Titel',
    'datetime'      => 'Datum/Tijd',
    'countries'     => 'Landen',
    'code'          => 'Code',
    'referer'       => 'Referrer',
    'referers'      => 'Referrers',
    'count'         => 'Aantal',
    'new_stories'   => 'Nieuwe Berichten',
    'story_title'   => 'Bericht Titel',
    'hits'          => 'Hits',
    'user'          => 'Gebruiker',
    'page'          => 'Pagina',
    'pages'         => 'Pagina\'s',
    'page_views'    => 'Bekeken',
    'views_per_page'=> 'Bekeken/Pagina',
    'views_per_hour'=> 'Bekeken/Uur',
    'hour'          => 'Uur',
    'ip'            => 'IP',
    'hostname'      => 'Hostname',
    'anon_users'    => 'Anoniem',
    'unique_visitors' => 'Unieke Bezoekers',
    'views'         => 'Hits/Bezoek',
    'total'         => 'Totaal',
    'daily_title'   => 'Bezoeker Statistieken per Dag',
    'monthly_title' => 'Bezoeker Statistieken per Maand',
    'day_title'     => 'Dag',
    'month_title'   => 'Maand',
    'anon_title'    => 'Anonieme Bezoekers',
    'reg_title'     => 'Geregistreerde Bezoekers',
    'page_title'    => 'Pagina\'s Bekeken',
    'comm_title'    => 'Reacties',
    'link_title'    => 'Links Gevolgd',
    'hour_title'    => 'Per Uur',
    'referer_title' => 'Referer',
    'country_title' => 'Land',
    'browser_title' => 'Browser',
    'platform_title' => 'Platform',
	'access_denied' => 'Geen Toegang',
	'access_denied_msg' => 'Toegang tot deze pagina is gelimiteerd tot bepaalde mensen. Je naam en IP zijn opgeslagen.',
	'install_header'	=> 'Installeer GUS',
	'sortDESC'			=> 'Sorteer aflopen',
	'sortASC'			=> 'Sorteer oplopend',
	'import_header'     => 'GUS Importeer Data'
);

// Admin and user block entries
$LANG_GUS_blocks = array(
	'admin_menu_title'	=> 'GUS',
	
	'user_menu_title'	=> 'GUS',
	'today'				=> 'vandaag'
);
	
// Who's Online
$LANG_GUS_wo = array(
    'title'				=> "Wie is er online",
    
	'stats'				=> 'Stats',
	'reg_users'     	=> 'Geregistreerd',
	'referers'      	=> 'Referrers',
	'new_users'         => 'Nieuwe gebruikers',
	'page_title'    	=> 'Pagina\'s Bekeken',
	'unique_visitors'	=> 'Uniek'
);

// Builtin stats
$LANG_GUS_builtin_stats = array(
	'unique_visitors'	=> 'Unieke Bezoekers',
	'users'				=> 'Geregistreerde Bezoekers'
);

// Admin Page
$LANG_GUS_admin = array(
	'admin'		=> 'GUS Beheer',
	
	'capture'		=> 'Statistieken Monitor',
    'captureon'		=> 'Statistieken monitor is AAN',
    'captureoff'	=> 'Statistieken monitor is UIT',
    'turnon'		=> 'Monitor Inschakelen',
    'turnoff'		=> 'Monitor Uitschakelen',
    
    'instructions'	=> 'The Geeklog Usage Stats [GUS] plugin collects statistics on who visits your site, what browser and operating system they are using, which pages they view, and which links they are clicking to get there. It allows the administrator to browse these stats through a series of tables, getting right down into the data.',

	// Ignore section
	'ignore'    => 'Negeer',
	
	'tip'		=> 'Tip:',
	'example'	=> 'Example:',
	
	'wildcard_tip'	=> 'Use % as a wildcard.  Matching uses the MySQL <a href="http://dev.mysql.com/doc/mysql/en/string-comparison-functions.html">LIKE</a> syntax.',
	
	'irreversible'	=> '<b>This is irreversible</b>, so make sure you really want to do this.',
	
	'clean_msg1'		=> 'Based on these filters, I took a quick look and have found entries in your database which match.',
	'clean_msg2'		=> 'Would you like me to clean these up?',
	'clean_num_entries'	=> 'Number of matching entries',
	'clean_up'			=> 'Clean Up',
	'star'				=> '* This section has some data which you may want to clean out of the database.',
	
	'add'		=> 'Toevoegen',
	'remove'    => 'Verwijderen',
	
	// IP
	'ip_title'		=> "IP Adressen",
	'ip_tip'		=> 'Your IP address is',
	'ip_example'	=> 'Using 123.0.1.% will ignore all addresses in the range 123.0.1.0 - 123.0.1.255.  Using 123.0.1% will ignore all of those <i>and</i> 123.0.10.% - 123.0.199.% - be careful of the period!',
	'ip_num_ip'		=> 'Number of matching IP addresses',
	
	// User
	'user_title'	=> "Gebruikers",
	'user_num_user'	=> 'Number of matching users',
	
	// Page
	'page_title'	=> "Pagina's",
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
	'remove_data'	=> 'Verwijderen Data',
	
	// Import data
	'import_data'	=> 'Importeer Data',
);

?>