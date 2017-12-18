<?php
###############################################################################
# japanese_utf-8.php
# This is the Japense UTF-8 language page for GUS
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
    'main_menu_title'     => '来訪者の閲覧統計',
    'priv_pol'      => 'プライバシーポリシー',
    'links_followed'=> 'リンク別アクセス状況',
    'link'          => 'リンク',
    'type'          => '種類',
    'ptu'           => 'ページ/タイトル/URL',
    'browsers'      => 'ブラウザ別アクセス状況',
    'browser'       => 'ブラウザ',
    'version'       => 'バージョン',
    'platforms'     => 'プラットフォーム別アクセス状況',
    'platform'      => 'プラットフォーム',
    'new_comments'  => 'コメント別アクセス状況',
    'comment_title' => 'コメント',
    'datetime'      => '日時',
    'countries'     => '国別アクセス状況',
    'code'          => 'コード',
    'referer'       => 'リファラー',
    'referers'      => 'リファラー別アクセス状況',
    'count'         => '数',
    'new_stories'   => '記事別閲覧状況',
    'story_title'   => '記事',
    'hits'          => '閲覧回数',
    'user'          => 'ユーザー',
    'page'          => 'ページ名',
    'pages'         => 'ページ総数',
    'page_views'    => '閲覧回数',
    'views_per_page'=> 'ページ別アクセス状況',
    'views_per_hour'=> '1時間あたりの閲覧回数',
    'hour'          => '時刻',
    'ip'            => 'IP',
    'host'			=> 'ホスト',
    'hostname'      => 'ホスト名',
    'anon_users'    => 'ゲストユーザー',
	'reg_users'     => '登録ユーザー',
    'unique_visitors' => 'ユニークビジター',
    'views'         => '1訪問あたりの閲覧回数',
    'total'         => '合計',
    'daily_title'   => '1日単位のアクセス状況',
    'monthly_title' => '1月単位のアクセス状況',
    'day_title'     => '日',
    'month_title'   => '月',
    'anon_title'    => 'ゲストユーザー',
    'reg_title'     => '登録ユーザー',
    'page_title'    => '閲覧ページ数',
    'comm_title'    => 'コメント',
    'link_title'    => 'リンククリック数',
    'hour_title'    => '時間',
    'referer_title' => 'リファラー',
    'country_title' => '国',
    'browser_title' => 'ブラウザ',
    'platform_title' => 'プラットフォーム',
	'access_denied' => 'アクセス拒否',
	'access_denied_msg' => 'このページを見る権限がありません。あなたの名前とIPを記録しました。',
	'install_header'	=> 'GUSをインストールしました',
	'sortDESC'			=> 'ソート(降順)',
	'sortASC'			=> 'ソート(昇順)',
	'import_header'     => 'データのインポート'
);

// Admin and user block entries
$LANG_GUS_blocks = array(
	'admin_menu_title'	=> '訪問者統計',
	
	'user_menu_latest'	=> '訪問者統計',
	'user_menu_today'	=> '今日'
);
	
// Who's Online
$LANG_GUS_wo = array(
    'title'				=> "オンラインユーザー",
    
	'bots'				=> 'ロボット',
	'stats'				=> '統計',
	'reg_users'     	=> '登録ユーザー',
	'referers'      	=> 'リファラー',
	'new_users'         => '新規ユーザー',
	'page_title'    	=> 'ページ閲覧',
	'unique_visitors'	=> '人の訪問者'	/* unique visitors*/
);

// Builtin stats
$LANG_GUS_builtin_stats = array(
	'unique_visitors'	=> 'ユニークビジター',
	'users'				=> '登録ユーザー'
);

// Admin Page
$LANG_GUS_admin = array(
	'admin'		=> 'GUS管理',
	
	'capture'		=> 'データ取得',
	'captureon'		=> 'データ取得中です',
	'captureoff'	=> 'データを取得していません',
	'turnon'		=> 'データ取得開始',
	'turnoff'		=> 'データ取得中断',

    'instructions'	=> 'The Geeklog Usage Stats [GUS] plugin collects statistics on who visits your site, what browser and operating system they are using, which pages they view, and which links they are clicking to get there. It allows the administrator to browse these stats through a series of tables, getting right down into the data.',
    
	// Ignore section
	'ignore'    => '無視する項目',
	
	'tip'		=> 'ヒント:',
	'example'	=> '例:',
	
	'wildcard_tip'	=> '% をワイルドカードとして使えます。文字列比較にはMySQLの <a href="http://dev.mysql.com/doc/mysql/en/string-comparison-functions.html">LIKE</a> 構文を使います。',
	
	'irreversible'	=> '<b>この操作は取り消せない</b>ので、十分に注意してください。',
	
	'clean_msg1'		=> '上記のフィルタを適用した結果、無視されるデータがデータベース中に見つかりました。',
	'clean_msg2'		=> 'このデータを削除してもよいですか?',
	'clean_num_entries'	=> '一致した項目数',
	'clean_up'			=> '削除',
	'star'				=> '* この項目には、あなたがデータベースから削除したいと思われるかもしれないデータがあります。',
	
	'add'		=> '追加',
	'remove'    => '削除',
	
	// IP
	'ip_title'		=> 'IPアドレス',
	'ip_tip'		=> 'あなたのIPアドレスは',
	'ip_example'	=> '123.0.1.% と指定すれば、123.0.1.0 から 123.0.1.255 までの全てのIPアドレスが無視されます。123.0.1% と指定すれば、上記のものに<i>加えて</i> 123.0.10.% から 123.0.199.% まで全てのIPアドレスが無視されます。ピリオドに注意してください！',
	'ip_num_ip'		=> '一致するIPアドレス数',
	
	// User
	'user_title'	=> 'ユーザー',
	'user_num_user'	=> '一致するユーザー数',
	
	// Page
	'page_title'	=> 'ページ',
	'page_num_page'	=> '一致するページ数',
	
	// User Agent
	'ua_title'		=> 'ユーザーエージェント',
	'ua_example'	=> '%Googlebot% と指定すれば、Googlebotという文字を含む全てのユーザーエージェントが無視されます。',
	'ua_num_ua'		=> '一致するユーザーエージェント数',

	// Host
	'host_title'	=> 'ホスト名',
	'host_tip'		=> 'あなたのホスト名は次のようです：',
	'host_example'	=> '%.googlebot.com と指定すれば、google botが無視されます。',
	'host_num_host'	=> '一致するホスト数',

	// Referrer
	'referrer_title'	    => 'リファラー',
	'referrer_example'	    => '%images.google.% と指定すれば、google image sitesからたどってきた全てのページは無視されます。',
	'referrer_num_referrer'	=> '一致するリファラー数',

	// Remove Data
	'remove_data'	=> 'データ削除',
	
	// Import data
	'import_data'	=> 'データインポート'
);

?>
