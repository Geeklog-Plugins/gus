<?php

// +---------------------------------------------------------------------------+
// | GUS Plugin                                                                |
// +---------------------------------------------------------------------------+
// | country.php                                                               |
// |                                                                           |
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

require_once './include/security.inc';

if (!GUS_HasAccess()) {
	exit;
}

require_once './include/sql.inc';
require_once './include/util.inc';

$_COUNTRIES = array(
	'AD' => 'Andorra',
	'AE' => 'United Arab Emirates',
	'AF' => 'Afghanistan',
	'AG' => 'Antigua and Barbuda',
	'AI' => 'Anguilla',
	'AL' => 'Albania',
	'AM' => 'Armenia',
	'AN' => 'Netherlands Antilles',
	'AO' => 'Angola',
	'AQ' => 'Antarctica',
	'AR' => 'Argentina',
	'AS' => 'American Samoa',
	'AT' => 'Austria',
	'AU' => 'Australia',
	'AW' => 'Aruba',
	'AZ' => 'Azerbaijan',
	'BA' => 'Bosnia and Herzegovina',
	'BB' => 'Barbados',
	'BD' => 'Bangladesh',
	'BE' => 'Belgium',
	'BF' => 'Burkina Faso',
	'BG' => 'Bulgaria',
	'BH' => 'Bahrain',
	'BI' => 'Burundi',
	'BJ' => 'Benin',
	'BM' => 'Bermuda',
	'BN' => 'Brunei Darussalam',
	'BO' => 'Bolivia',
	'BR' => 'Brazil',
	'BS' => 'Bahamas',
	'BT' => 'Bhutan',
	'BV' => 'Bouvet Island',
	'BW' => 'Botswana',
	'BY' => 'Belarus',
	'BZ' => 'Belize',
	'CA' => 'Canada',
	'CC' => 'Cocos (Keeling) Islands',
	'CF' => 'Central African Republic',
	'CG' => 'Congo',
	'CH' => 'Switzerland',
	'CI' => "Cote D'Ivoire (Ivory Coast)",
	'CK' => 'Cook Islands',
	'CL' => 'Chile',
	'CM' => 'Cameroon',
	'CN' => 'China',
	'CO' => 'Colombia',
	'CR' => 'Costa Rica',
	'CS' => 'Czechoslovakia (former)',
	'CU' => 'Cuba',
	'CV' => 'Cape Verde',
	'CX' => 'Christmas Island',
	'CY' => 'Cyprus',
	'CZ' => 'Czech Republic',
	'DE' => 'Germany',
	'DJ' => 'Djibouti',
	'DK' => 'Denmark',
	'DM' => 'Dominica',
	'DO' => 'Dominican Republic',
	'DZ' => 'Algeria',
	'EC' => 'Ecuador',
	'EE' => 'Estonia',
	'EG' => 'Egypt',
	'EH' => 'Western Sahara',
	'ER' => 'Eritrea',
	'ES' => 'Spain',
	'ET' => 'Ethiopia',
	'FI' => 'Finland',
	'FJ' => 'Fiji',
	'FK' => 'Falkland Islands (Malvinas)',
	'FM' => 'Micronesia',
	'FO' => 'Faroe Islands',
	'FR' => 'France',
	'FX' => 'France, Metropolitan',
	'GA' => 'Gabon',
	'GB' => 'Great Britain (UK)',
	'GD' => 'Grenada',
	'GE' => 'Georgia',
	'GF' => 'French Guiana',
	'GH' => 'Ghana',
	'GI' => 'Gibraltar',
	'GL' => 'Greenland',
	'GM' => 'Gambia',
	'GN' => 'Guinea',
	'GP' => 'Guadeloupe',
	'GQ' => 'Equatorial Guinea',
	'GR' => 'Greece',
	'GS' => 'S. Georgia and S. Sandwich Isls.',
	'GT' => 'Guatemala',
	'GU' => 'Guam',
	'GW' => 'Guinea-Bissau',
	'GY' => 'Guyana',
	'HK' => 'Hong Kong',
	'HM' => 'Heard and McDonald Islands',
	'HN' => 'Honduras',
	'HR' => 'Croatia (Hrvatska)',
	'HT' => 'Haiti',
	'HU' => 'Hungary',
	'ID' => 'Indonesia',
	'IE' => 'Ireland',
	'IL' => 'Israel',
	'IN' => 'India',
	'IO' => 'British Indian Ocean Territory',
	'IQ' => 'Iraq',
	'IR' => 'Iran',
	'IS' => 'Iceland',
	'IT' => 'Italy',
	'JM' => 'Jamaica',
	'JO' => 'Jordan',
	'JP' => 'Japan',
	'KE' => 'Kenya',
	'KG' => 'Kyrgyzstan',
	'KH' => 'Cambodia',
	'KI' => 'Kiribati',
	'KM' => 'Comoros',
	'KN' => 'Saint Kitts and Nevis',
	'KP' => 'Korea (North)',
	'KR' => 'Korea (South)',
	'KW' => 'Kuwait',
	'KY' => 'Cayman Islands',
	'KZ' => 'Kazakhstan',
	'LA' => 'Laos',
	'LB' => 'Lebanon',
	'LC' => 'Saint Lucia',
	'LI' => 'Liechtenstein',
	'LK' => 'Sri Lanka',
	'LR' => 'Liberia',
	'LS' => 'Lesotho',
	'LT' => 'Lithuania',
	'LU' => 'Luxembourg',
	'LV' => 'Latvia',
	'LY' => 'Libya',
	'MA' => 'Morocco',
	'MC' => 'Monaco',
	'MD' => 'Moldova',
	'MG' => 'Madagascar',
	'MH' => 'Marshall Islands',
	'MK' => 'Macedonia',
	'ML' => 'Mali',
	'MM' => 'Myanmar',
	'MN' => 'Mongolia',
	'MO' => 'Macau',
	'MP' => 'Northern Mariana Islands',
	'MQ' => 'Martinique',
	'MR' => 'Mauritania',
	'MS' => 'Montserrat',
	'MT' => 'Malta',
	'MU' => 'Mauritius',
	'MV' => 'Maldives',
	'MW' => 'Malawi',
	'MX' => 'Mexico',
	'MY' => 'Malaysia',
	'MZ' => 'Mozambique',
	'NA' => 'Namibia',
	'NC' => 'New Caledonia',
	'NE' => 'Niger',
	'NF' => 'Norfolk Island',
	'NG' => 'Nigeria',
	'NI' => 'Nicaragua',
	'NL' => 'Netherlands',
	'NO' => 'Norway',
	'NP' => 'Nepal',
	'NR' => 'Nauru',
	'NT' => 'Neutral Zone',
	'NU' => 'Niue',
	'NZ' => 'New Zealand (Aotearoa)',
	'OM' => 'Oman',
	'PA' => 'Panama',
	'PE' => 'Peru',
	'PF' => 'French Polynesia',
	'PG' => 'Papua New Guinea',
	'PH' => 'Philippines',
	'PK' => 'Pakistan',
	'PL' => 'Poland',
	'PM' => 'St. Pierre et Miquelon',
	'PN' => 'Pitcairn',
	'PR' => 'Puerto Rico',
	'PT' => 'Portugal',
	'PW' => 'Palau',
	'PY' => 'Paraguay',
	'QA' => 'Qatar',
	'RE' => 'Reunion',
	'RO' => 'Romania',
	'RU' => 'Russian Federation',
	'RW' => 'Rwanda',
	'SA' => 'Saudi Arabia',
	'SB' => 'Solomon Islands',
	'SC' => 'Seychelles',
	'SD' => 'Sudan',
	'SE' => 'Sweden',
	'SG' => 'Singapore',
	'SH' => 'St. Helena',
	'SI' => 'Slovenia',
	'SJ' => 'Svalbard and Jan Mayen Islands',
	'SK' => 'Slovak Republic',
	'SL' => 'Sierra Leone',
	'SM' => 'San Marino',
	'SN' => 'Senegal',
	'SO' => 'Somalia',
	'SR' => 'Suriname',
	'ST' => 'Sao Tome and Principe',
	'SU' => 'USSR (former)',
	'SV' => 'El Salvador',
	'SY' => 'Syria',
	'SZ' => 'Swaziland',
	'TC' => 'Turks and Caicos Islands',
	'TD' => 'Chad',
	'TF' => 'French Southern Territories',
	'TG' => 'Togo',
	'TH' => 'Thailand',
	'TJ' => 'Tajikistan',
	'TK' => 'Tokelau',
	'TM' => 'Turkmenistan',
	'TN' => 'Tunisia',
	'TO' => 'Tonga',
	'TP' => 'East Timor',
	'TR' => 'Turkey',
	'TT' => 'Trinidad and Tobago',
	'TV' => 'Tuvalu',
	'TW' => 'Taiwan',
	'TZ' => 'Tanzania',
	'UA' => 'Ukraine',
	'UG' => 'Uganda',
	'UK' => 'United Kingdom',
	'UM' => 'US Minor Outlying Islands',
	'US' => 'United States',
	'UY' => 'Uruguay',
	'UZ' => 'Uzbekistan',
	'VA' => 'Vatican City State (Holy See)',
	'VC' => 'Saint Vincent and the Grenadines',
	'VE' => 'Venezuela',
	'VG' => 'Virgin Islands (British)',
	'VI' => 'Virgin Islands (US)',
	'VN' => 'Viet Nam',
	'VU' => 'Vanuatu',
	'WF' => 'Wallis and Futuna Islands',
	'WS' => 'Samoa',
	'YE' => 'Yemen',
	'YT' => 'Mayotte',
	'YU' => 'Yugoslavia',
	'ZA' => 'South Africa',
	'ZM' => 'Zambia',
	'ZR' => 'Zaire',
	'ZW' => 'Zimbabwe',
	'COM' => 'Commercial',
	'EDU' => 'US Educational',
	'GOV' => 'US Government',
	'INT' => 'International',
	'MIL' => 'US Military',
	'NET' => 'Network',
	'ORG' => 'Non-Profit Organization',
	'ARPA' => 'Arpanet',
	'NATO' => 'Nato field',
	'BIZ' => 'Business',
	'INFO' => 'Commercial or Personal',
	'NAME' => 'Individual',
	'PRO' => 'Credentialed Professional',
	'AERO' => 'Air Transport Industry',
	'COOP' => 'Co-operative Association',
	'MUSEUM' => 'Museum',
	'UNKNOWN' => 'Unresolved/Unknown',
);

/* 
* Main Function
*/

// check for cached file
$today = date('MY');

if (file_exists(GUS_cachefile()) AND ($today !== $month . $year)) {
	$display = GUS_getcache();
} else {
	// no cached version
	$T = GUS_template_start();

	$T->set_block('page', 'COLUMN', 'CBlock');
	$T->set_block('page', 'ROW', 'BBlock');
	$T->set_block('page', 'TABLE', 'ABlock');

	$T->set_var(array(
		'colclass' => 'col_right',
		'data'     => $LANG_GUS00['unique_visitors']
	));
	$T->parse('CBlock', 'COLUMN', FALSE);
	$T->set_var(array(
		'colclass' => 'col_left',
		'data'     => $LANG_GUS00['code']
	));
	$T->parse('CBlock', 'COLUMN', TRUE);

	$T->set_var('data', $LANG_GUS00['country_title']);
	$T->parse('CBlock', 'COLUMN', TRUE);

	$T->set_var('rowclass', 'header');
	$T->parse('BBlock', 'ROW', TRUE);

	$date_compare = GUS_get_date_comparison('date', $year, $month);

	$sql = "SELECT RIGHT( host, INSTR( REVERSE( host ), '.' ) - 1 ) AS country
			FROM {$_TABLES['gus_userstats']}
			WHERE {$date_compare} AND host != 'localhost' 
			GROUP BY ip, date";
	$result = DB_query($sql);
	$country_list = array('Unknown' => 0);

	while ($row = DB_fetchArray($result)) {
		if (is_numeric($row['country']) OR ($row['country'] === '')) {
			$country_list['Unknown'] = $country_list['Unknown'] + 1;
		} else {
			$country = strtolower($row['country']);
			
			if (!isset($country_list[$country])) {
				$country_list[$country] = 1;
			} else {
				$country_list[$country]++;
			}
		}
	}

	arsort($country_list);
	$rowNum = 1;

	foreach ($country_list as $ctry => $cnt) {
		if ($rowNum % 2) {
			$T->set_var('rowclass', 'row1');
		} else {
			$T->set_var('rowclass', 'row2');
		}
		
		$rowNum++;
		$T->set_var(array(
			'colclass' => 'col_right',
			'data'     => $cnt
		));
		$T->parse('CBlock', 'COLUMN', FALSE);
		
		$T->set_var(array(
			'colclass' => 'col_left',
			'data'     => $ctry
		));
		$T->parse('CBlock', 'COLUMN', TRUE);
		
		$T->set_var(array(
			'colclass' => 'col_left',
			'data'     => $_COUNTRIES[strtoupper($ctry)]
		));
		$T->parse('CBlock', 'COLUMN', TRUE);
		
		$T->parse('BBlock','ROW',TRUE);
	}
	
	$T->Parse('ABlock', 'TABLE', TRUE);
	$title = Date('F Y - ', mktime(0, 0, 0, $month, 1, $year))
		   . $LANG_GUS00['countries'];
	$display = GUS_template_finish($T, $title);

	if ($_GUS_cache AND ($today !== $month . $year)) {
		GUS_writecache($display);
	}
}

echo COM_siteHeader($_GUS_CONF['show_left_blocks']);
echo $display;
echo COM_siteFooter($_GUS_CONF['show_right_blocks']);
