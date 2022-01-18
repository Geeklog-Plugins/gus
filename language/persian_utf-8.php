<?php
###############################################################################
# persian_utf-8.php
#
# This is the Persian language file for Geeklog gus plugin
# Special thanks to Mahdi Montazeri for his work on this project
#
# Copyright (C) 2018 geeklog.ir
# info AT mahdimontazeri DOT com
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
	'GUS_title'			=> 'آمار کاربردی',
    'main_menu_title'     => 'آمار بازدید کننده',
    'priv_pol'      => 'سیاست حریم خصوصی',
    'links_followed'=> 'پیوند های دنبال شده',
    'link'          => 'پیوند',
    'type'          => 'نوع',
    'ptu'           => 'صفحه/عنوان/نشانی اینترنتی',
    'browsers'      => 'مرورگر ها',
    'browser'       => 'مرورگر',
    'version'       => 'نسخه',
    'platforms'     => 'سیستم عامل ها',
    'platform'      => 'سیستم عامل',
    'new_comments'  => 'نظرات جدید',
    'comment_title' => 'عنوان نظر',
    'datetime'      => 'تاریخ/زمان',
    'time'			=> 'زمان',
    'countries'     => 'کشور ها',
    'code'          => 'کد',
    'referer'       => 'رجوعی',
    'referers'      => 'رجوعی ها',
    'count'         => 'دفعه',
    'new_stories'   => 'داستان های جدید',
    'story_title'   => 'عنوان داستان',
    'hits'          => 'بازدید',
    'user'          => 'کاربر',
    'page'          => 'صفحه',
    'pages'         => 'صفحات',
    'page_views'    => 'مشاهدات صفحه',
    'views_per_page'=> 'مشاهدات/صفحه',
    'views_per_hour'=> 'مشاهدات/ساعت',
    'hour'          => 'ساعت',
    'ip'            => 'آیپی',
    'host'			=> 'میزبان',
    'hostname'      => 'نام میزبان',
    'anon_users'    => 'کاربران ناشناس',
	'reg_users'     => 'کاربران ثبت نام شده',
    'unique_visitors' => 'بازدید های منحصر به فرد',
    'views'         => 'مشاهدات/بازدید',
    'total'         => 'جمع',
    'daily_title'   => 'آمار بازدید کننده بر اساس روز',
    'monthly_title' => 'آمار بازدید کننده بر اساس ماه',
    'day_title'     => 'روز',
    'month_title'   => 'ماه',
    'anon_title'    => 'بازدید کنندگان ناشناس',
    'reg_title'     => 'بازدید کنندگان ثبت نام شده',
    'page_title'    => 'صفحات دیده شده',
    'comm_title'    => 'نظرات',
    'link_title'    => 'پیوند های دنبال شده',
    'hour_title'    => 'بر اساس ساعت',
    'referer_title' => 'رجوعی',
    'country_title' => 'کشور',
    'browser_title' => 'مرورگر',
    'platform_title' => 'سیستم عامل',
	'access_denied' => 'دسترسی ممنوع است',
	'access_denied_msg' => 'فقط برخی از کاربران به این صفحه دسترسی دارند. نام و آیپی شما ضبط شده است.',
	'install_header'	=> 'نصب آمار کاربردی',
	'sortDESC'			=> 'سفارش ترتیب نزولی',
	'sortASC'			=> 'سفارش ترتیب صعودی',
	'import_header'     => 'واردات داده آمار کاربردی'
);

// Admin and user block entries
$LANG_GUS_blocks = array(
	'admin_menu_title'	=> 'آمار کاربردی',
	
	'user_menu_latest'	=> 'آمار بازدید کننده',
	'user_menu_today'	=> "آمار امروز"
);
	
// Who's Online
$LANG_GUS_wo = array(
    'title'				=> "کاربران آنلاین",
    
	'bots'				=> 'ربات ها',
	'stats'				=> 'آمار',
	'reg_users'     	=> 'کاربران ثبت نام شده',
	'referers'      	=> 'رجوعی ها',
	'new_users'         => 'کاربران جدید',
	'page_title'    	=> 'صفحات دیده شده',
	'unique_visitors'	=> 'بازدید های منحصر به فرد'
);

// Builtin stats
$LANG_GUS_builtin_stats = array(
	'unique_visitors'	=> 'بازدید کنندگان منحصر به فرد',
	'users'				=> 'کاربران ثبت نام شده'
);

// Admin Page
$LANG_GUS_admin = array(
	'admin'		=> 'مدیریت آمار کاربردی',
	
	'capture'		=> 'گرفتن',
	'captureon'		=> 'گرفتن آمار روشن است',
	'captureoff'	=> 'گرفتن آمار خاموش است',
	'turnon'		=> 'روشن کردن گرفتن',
	'turnoff'		=> 'خاموش کردن گرفتن',
    
    'instructions'	=> 'افزونه آمار کاربردی جمع آوری آمار می کند درباره اینکه چه کسی از سایت شما بازدید می کند، چه مرورگر و سیستم عامل استفاده می کنند، چه صفحاتی را که می بینند و چه پیوند هایی برای رسیدن به آنجا کلیک می کنند. به مدیران این اجازه را می دهد تا این آمار را از طریق یک سلسله از جداول، برای گرفتن داده به درستی مرور کنند.',

	// Ignore section
	'ignore'    => 'چشم پوشی',
	
	'tip'		=> 'نکته:',
	'example'	=> 'مثال:',
	
	'wildcard_tip'	=> 'Use % as a wildcard.  Matching uses the MySQL <a href="http://dev.mysql.com/doc/mysql/en/string-comparison-functions.html">LIKE</a> syntax.',
	
	'irreversible'	=> '<b>این برنگشتنی است</b>، پس مطمئن شوید که واقعا میخواهید این کار را انجام دهید.',
	
	'clean_msg1'		=> 'بر اساس این فیلتر ها، نگاهی سریع انداختم و در پایگاه داده شما ورودی هایی را یافته ام که تطابق دارند.',
	'clean_msg2'		=> 'آیا مایلید به پاکسازی اینها؟',
	'clean_num_entries'	=> 'تعداد ورودی های مطابق',
	'clean_up'			=> 'پاکسازی',
	'star'				=> '* این بخش برخی از داده ها را دارد که ممکن است بخواهید از پایگاه داده پاک کنید.',
	
	'add'		=> 'افزودن',
	'remove'    => 'برداشتن',
	
	// IP
	'ip_title'		=> 'نشانی های آیپی',
	'ip_tip'		=> 'نشانی آیپی شما هست',
	'ip_example'	=> 'Using 123.0.1.% will ignore all addresses in the range 123.0.1.0 - 123.0.1.255.  Using 123.0.1% will ignore all of those <i>and</i> 123.0.10.% - 123.0.199.% - be careful of the period!',
	'ip_num_ip'		=> 'تعداد نشانی های آیپی مطابق',
	
	// User
	'user_title'	=> 'کاربران',
	'user_num_user'	=> 'تعداد کاربران مطابق',
	
	// Page
	'page_title'	=> 'صفحات',
	'page_num_page'	=> 'تعداد صفحات مطابق',
	
	// User Agent
	'ua_title'		=> 'عاملان کاربر',
	'ua_example'	=> 'Using %Googlebot% will ignore any user agent containing the term Googlebot.',
	'ua_num_ua'		=> 'تعداد عاملان کاربر مطابق',

	// Host
	'host_title'	=> 'نام های میزبان',
	'host_tip'		=> 'نام میزبان شما به نظر می رسد',
	'host_example'	=> 'Using %.googlebot.com will ignore the google bot.',
	'host_num_host'	=> 'تعداد نام های میزبان مطابق',

	// Referrer
	'referrer_title'	    => 'رجوعی ها',
	'referrer_example'	    => 'Using %images.google.% will ignore any pages referred from any of the google image sites.',
	'referrer_num_referrer'	=> 'تعداد رجوعی های مطابق',

	// Remove Data
	'remove_data'	=> 'برداشتن داده',
	
	// Import data
	'import_data'	=> 'واردات داده',
);
