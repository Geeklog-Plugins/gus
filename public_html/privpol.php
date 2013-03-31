<?php

// +---------------------------------------------------------------------------+
// | GUS Plugin                                                                |
// +---------------------------------------------------------------------------+
// | privpol.php                                                               |
// | Suggested Site Privacy Policy                                             |
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

require_once '../lib-common.php';

$display = COM_siteHeader()
		 . "
	<div align=center><h1>Privacy Policy</h1></div>
	<i>{$_CONF['site_name']}</i> has created this privacy policy in order to demonstrate our commitment to privacy.
	The following discloses the information gathering and dissemination practices for this website.
"
		 . '
	<h2>Information Logged</h2>
	This site logs the following information for each page access:
	<ul>
		<li>user name</li>
		<li>browser type &amp; version</li>
		<li>operating system type &amp; version</li>
		<li>IP address &amp; host name</li>
		<li>date &amp; time</li>
		<li>page viewed</li>
		<li>referrer [the link which was clicked to bring you to this site]</li>
	</ul>
	We use this information to help diagnose problems with our server and to administer our site.
	<b>This information is not and never will be divulged to a third party.</b>
'
		 . "
	<h2>Cookies</h2>
	<i>{$_CONF['site_name']}</i> uses cookies to deliver content specific to your interests
	and to save your password so you do not have to enter it each time you visit our site.
"
		 . "
	<h2>Registration Forms</h2>
	Our site's registration form requires users to give us contact information (i.e. email address). Contact information
	from the registration forms is used to validate the user's account. This enables the site administrators to
	provide moderation for the various public features of this site, and to get in touch with the user when necessary.
	<b>This information is not and will never be divulged to a third party.</b> We use this data to tailor our visitor's
	experience at our site showing them content that we think they might be interested in, and displaying the content
	according to their preferences. 
"
		 . "
	<h2>External Links</h2>
	This site contains links to other websites. <i>{$_CONF['site_name']}</i> is not responsible for the
	content or privacy practices of such websites.
"
		 . "
	<h2>Public Forums</h2>
	This site makes message boards available to its users. Please remember that any information that is disclosed in
	these areas becomes public information and you should exercise caution when deciding to disclose your personal
	information. We cannot and do not assume responsibility for our user's postings.
	<b>All postings are property of their respective author.</b>
	All submissions are subject to our editorial control and editorial changes will be indicated.
"
		 . "
	<h2>Security </h2>
	This site has security measures in place to protect the loss, misuse, and alteration of the information under our control. 
"
		 . "
	<h2>Data Quality/Access</h2>
	This site gives users control over their user experience and content that they may have provided.  Users may
	freely modify or delete any content they post.  Users have the ability to customise various aspects of the
	look and feel of the site.
"
		 . "
	<h2>Limitation of Liability</h2>
	<i>{$_CONF['site_name']}</i> is not liable for any damages caused by any of the site content, whether
	provided by <i>{$_CONF['site_name']}</i> or not.
"
		 . "
	<h2>Contacting <i>{$_CONF['site_name']}</i></h2>
	If you have any questions about this privacy statement or the practices of this site,
	you may contact the <a href='mailto:{$_CONF['site_mail']}'>webmaster</a>
"
		 . "
	<h2>Changes</h2>
	<i>{$_CONF['site_name']}</i> may change these policies at any time without written notice to users. The changes will become effective upon posting of the changes to the website.
"
		 . COM_siteFooter(true);
echo $display;
