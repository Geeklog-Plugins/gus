<?php

// +---------------------------------------------------------------------------+
// | GUS Plugin                                                                |
// +---------------------------------------------------------------------------+
// | util.inc                                                                  |
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

// translate the option to something we can pass directly to COM_siteHeader()
$_GUS_CONF['show_left_blocks'] = $_GUS_CONF['show_left_blocks'] ? '' : 'none';

// some standard GET options
$year  = isset($_GET['year'])  ? COM_applyFilter($_GET['year'])  : '';
$month = isset($_GET['month']) ? COM_applyFilter($_GET['month']) : '';
$day   = isset($_GET['day'])   ? COM_applyFilter($_GET['day'])   : '';

$year  = (int) $year;
$month = (int) $month;
$day   = (int) $day;

function GUS_template_get_user_data($uid, $username) {
	global $_CONF;
	
	if ($uid > 1) {
		$data = '<a href="' . $_CONF['site_url'] . '/users.php?mode=profile&amp;uid=' . $uid . '">' . $username . '</a>';
	} else {
		$data = '-----';
	}

	return $data;
}

function GUS_endsWith($str, $sub) {
   return (substr($str, - strlen($sub)) === $sub);
}

function GUS_template_get_page_data($page, $query_string) {
	global $_TABLES, $_GUS_VARS, $_EF_TABLES;
	
	static $cache = array();
	
	$data = '';
	$html_query = htmlentities($query_string);
	$host = $_GUS_VARS['parsed_site_url']['scheme'] . '://'
		  . $_GUS_VARS['parsed_site_url']['host'];
	
	if ($page[0] !== '/') {
		$host .= '/';
	}
	
	if (($page === 'article.php') AND
			preg_match("/^.*story=([^&]+).*$/", $html_query, $sid)) {
		if (isset($cache['article.php'][$sid[1]])) {
			$title = $cache['article.php'][$sid[1]];
		} else {
			$title = DB_getItem($_TABLES['stories'],'title', "sid = '$sid[1]'");
			$cache['article.php'][$sid[1]] = $title;
		}
		
		if ($title !== '') {
			$data = '<a href="' . $host . $page . '?' . $html_query . '">'
				  . $title . '</a>';
		}
	} else if (($page === 'staticpages/index.php') AND
			preg_match("/^.*page=([^&]+).*$/", $html_query, $page_name)) {
		if (isset($cache['staticpages/index.php'][$page_name[1]])) {
			$title = $cache['staticpages/index.php'][$page_name[1]];
		} else {
			$title = DB_getItem($_TABLES['staticpage'],'sp_title', "sp_id = '$page_name[1]'");
			$cache['staticpages/index.php'][$page_name[1]] = $title;
		}

		if ($title != '') {
			$data = '<a href="' . $host . $page . '?' . $html_query . '">'
				  . $title . '</a>';
		}
	} else if (($page === 'amz/search.php') AND
			preg_match("/^.*keywords?=([^&]+).*$/", $html_query, $keyword)) {
		$data = 'amz search [<a href="' . $host . $page . '?' . $html_query
			  . '">' . $keyword[1] . '</a>]';
	} else if (($page === 'gallery/view_album.php') AND
			preg_match("/^set_albumName=([^&]+).*$/", $html_query, $album_name)) {
		$data = 'gallery [<a href="' . $host . $page . '?' . $html_query . '">'
			  . $album_name[1] . '</a>]';
	} else if (($page === 'gallery/view_photo.php') AND
			preg_match("/^set_albumName=([^&]+)&amp;id=(.+)$/", $html_query, $matches)) {
		$data = 'gallery [<a href="' . $host . $page . '?' . $html_query . '">'
			  . $matches[1] .':' . $matches[2] . '</a>]';
	} else if ($page === '404.php') {
		$data =  $page;
		
		if ($html_query !== '') {
			$data .= ' <span class="a404">[' .  $html_query . ']</span>';
		}
	} else if (($page === 'users.php') AND
			preg_match("/^mode=profile&amp;uid=(.+)$/", $html_query, $matches)) {
		if (isset($cache['users.php'][$matches[1]])) {
			$username = $cache['users.php'][$matches[1]];
		} else {
			$username = DB_getItem($_TABLES['users'], 'username', "uid = '{$matches[1]}'");
			$cache['users.php'][$matches[1]] = $username;
		}
		
		if ($username !== '') {
			$data = 'profile [<a href="' . $host . $page . '?' . $html_query
				  . '">' . $username . '</a>]';
		}
	} else if (($page === 'portal.php') OR ($page === 'download.php')) {
		$query1 = explode('&', $query_string);
		$query  = explode('=', $query1[0]);
		
		if ($query[1] === 'link') {
			$lid   = explode('=', $query1[1]);
			$title = DB_getItem($_TABLES['links'], 'title', "lid = '{$lid[1]}'");
			$data  = 'link: ' . '<a href="' . $host . $page . '?' . $html_query
				   . '">' . $title . '</a>';
		} else {
			$title = urldecode($query[1]);
			
			if (($title !== '') AND ($title !== 'yes')) {
				$data = '<a href="' . $host . $page . '?' . $html_query . '">'
					  . $title . '</a>';
			}
		}
	} else if (defined('PHPBBBRIDGE_INSTALLED') AND
			GUS_endsWith($page, '/viewtopic.php') AND
			preg_match("/^t=(.+)$/", $html_query, $matches)) {
		if (isset($cache['viewtopic.php'][$matches[1]])) {
			$topic_name = $cache['viewtopic.php'][$matches[1]];
		} else {
			$topic_name = DB_getItem(TOPICS_TABLE, 'topic_title', "topic_id = '{$matches[1]}'");
			$cache['viewtopic.php'][$matches[1]] = $topic_name;
		}
		
		if ($topic_name !== '') {
			$data = 'forum topic [<a href="' . $host . $page . '?' . $html_query
				  . '">' . $topic_name . '</a>]';
		}
	} else if (defined('PHPBBBRIDGE_INSTALLED') AND
			GUS_endsWith($page, '/viewforum.php') AND
			preg_match("/^f=(.+)$/", $html_query, $matches)) {
		if (isset( $cache['viewforum.php'][$matches[1]])) {
			$forum_name = $cache['viewforum.php'][$matches[1]];
		} else {
			$forum_name = DB_getItem(FORUMS_TABLE, 'forum_name', "forum_id = '{$matches[1]}'");
			$cache['viewforum.php'][$matches[1]] = $topic_name;
		}
		
		if ($forum_name !== '') {
			$data = 'forum [<a href="' . $host . $page . '?' . $html_query . '">'
				  . $forum_name . '</a>]';
		}
	} else if (($page === 'easyfile/file.php') AND isset($_EF_TABLES['files']) AND
			preg_match("/^download=([0-9]+).*$/", $html_query, $fileid)) {
		if (isset($cache['easyfile'][$fileid[1]])) {
			$title = $cache['easyfile'][$fileid[1]];
		} else {
			$title = DB_getItem($_EF_TABLES['files'], 'title', "fileid = '$fileid[1]'");
			$cache['easyfile'][$fileid[1]] = $title;
		}
		
		if ($title !== '') {
			$data = '<a href="' . $host . $page . '?' . $html_query . '">'
				  . $title . '</a>';
		}
	}
	
	if ($data === '') {
		if ($html_query !== '') {
			$html_query = '?' . $html_query;
		}
			
		$data = '<a href="' . $host . $page . $html_query . '">' . $page . '</a>';
	}

	return $data;
}

function GUS_template_get_referrer_data($referrer, $check_hide = FALSE) {
	global $_GUS_CONF;
	
	if ($referrer === '') {
		return '&nbsp;';
	}
	
	if ($check_hide AND count($_GUS_CONF['hide_in_day_summary'])) {
		static $encodedCheckReferrers = array();
		
		// we only need to encoded these guys once since they aren't going to change
		if (empty($encodedCheckReferrers)) {
			foreach ($_GUS_CONF['hide_in_day_summary'] as $checkReferrer) {
				$checkReferrer = urlencode(addslashes($checkReferrer));
				$encodedCheckReferrers[] = array(
					'referrer' => $checkReferrer,
					'size'     => strlen($checkReferrer)
				);
			}
		}
		
		// check each the encoded referrers against the referrer and return if we find a match
		foreach ($encodedCheckReferrers as $encReferrer) {			
			if (!strncmp($encReferrer['referrer'], $referrer, $encReferrer['size'])) {
				return '&nbsp;';
			}
		}
	}
	
	$referrer = htmlentities(urldecode($referrer));

	return '<a href="' . $referrer . '" target="_blank">' . $referrer . '</a>';
}

function GUS_template_start($page = 'table.thtml') {
	global $LANG_GUS00, $_CONF, $_GUS_IMG_small_name;
	
	$template = new Template($_CONF['path'] . 'plugins/gus/templates');
	$template->set_file(array(
		'header' => 'header.thtml',
		'page'   => $page,
		'footer' => 'footer.thtml',
	));
	
	$template->set_var(array(
		'ref_url' => $_CONF['site_url'] . '/gus/index.php',
		'img_url' => $_CONF['site_url'] . '/gus/images/' . $_GUS_IMG_small_name
	));

	return $template;
}

function GUS_template_finish($template, $title) {
	$template->set_var('header_text', $title);
	$template->parse('page_header', 'header');
	$template->parse('table', 'page');
	$template->parse('page_footer', 'footer');

	$data = '<div class="gus">'
		  . $template->finish($template->get_var('page_header'))
		  . $template->finish($template->get_var('table'))
		  . '<br>'
		  . $template->finish($template->get_var('page_footer'))
		  . '</div>';
	
	return $data;
}

function GUS_make_link_for_date($additional_args, $date, $day = TRUE, $month = TRUE, $year = TRUE) {
	$link = '<a href="' . $_SERVER['PHP_SELF'] . '?';
	
	if ($day) {
		$link .= 'day=' . date('j', $date) . '&amp;';
	}
	
	if ($month) {
		$link .= 'month=' . date('n', $date) . '&amp;';
	}
	
	if ($year) {
		$link .= 'year=' . date('Y', $date) . '&amp;';
	}
	
	$link .= $additional_args . '">';
	
	return $link;
}

function GUS_make_nav($day, $month, $year, $additional_args = '') {
	if ($day != '') {
		$day_before = mktime(0, 0, 0, $month, $day - 1, $year);
		$today      = mktime();
		$day_after  = mktime(0, 0, 0, $month, $day + 1, $year);
		
		$additional_nav = GUS_make_link_for_date($additional_args, $day_before)
						. '&lt;- ' . date('j M Y', $day_before)
						. '</a>';
		
		if ($today > $day_after) {
			$additional_nav .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
							.  GUS_make_link_for_date($additional_args, $day_after)
							.  date('j M Y', $day_after) . ' -&gt;'
							.  '</a>';
		}
	
		$additional_nav .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
						.  GUS_make_link_for_date($additional_args, $today, FALSE)
						.  '[' . date('F', $today) . ']'
						.  '</a>'
						.  '<br><br>';
	} else if ($month != '') {
		$month_before = mktime(0, 0, 0, $month - 1, 1, $year);
		$today        = mktime();
		$month_after  = mktime(0, 0, 0, $month + 1, 1, $year);
		
		$additional_nav = GUS_make_link_for_date($additional_args, $month_before, FALSE)
						. '&lt;- ' . date('M Y', $month_before)
						. '</a>';
		
		if ($today > $month_after) {
			$additional_nav .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
							.  GUS_make_link_for_date($additional_args, $month_after, false)
							.  date('M Y', $month_after) . ' -&gt;'
							.  '</a>';
		}
		
		$additional_nav .= '<br><br>';
	} else if ($year != '') {
		$year_before = mktime(0, 0, 0, 1, 1, $year - 1);
		$today       = mktime();
		$year_after  = mktime(0, 0, 0, 1, 1, $year + 1);
		
		$additional_nav = GUS_make_link_for_date($additional_args, $year_before, FALSE, FALSE)
						. '&lt;- ' . date('Y', $year_before)
						. '</a>';
		
		if ($today > $year_after) {
			$additional_nav .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
							.  GUS_make_link_for_date($additional_args, $year_after, FALSE, FALSE)
							.  date('Y', $year_after) . ' -&gt;'
							.  '</a>';
		}
		
		$additional_nav .= '<br><br>';
	}
	
	return $additional_nav;
}
