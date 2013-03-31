<?php

// +---------------------------------------------------------------------------+
// | GUS Plugin                                                                |
// +---------------------------------------------------------------------------+
// | security.inc                                                              |
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

require_once( '../lib-common.php' );

function GUS_HasAccess($allow_anon = TRUE) {
	global $_GUS_anon_access, $LANG_GUS00, $_USER;
	
	if (($allow_anon AND ($_GUS_anon_access == 1)) OR SEC_hasRights('gus.view' )) {
		return TRUE;
	}

	COM_errorLog("Someone has tried to illegally access a GUS stats page.  "
		. "User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: {$_SERVER['REMOTE_ADDR']}", 1);

	echo COM_siteHeader();
	echo COM_startBlock($LANG_GUS00['access_denied']);
	echo $LANG_GUS00['access_denied_msg'];
	echo COM_endBlock();
	echo COM_siteFooter(TRUE);

	return FALSE;
}
