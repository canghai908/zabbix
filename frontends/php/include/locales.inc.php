<?php
/*
** Zabbix
** Copyright (C) 2001-2015 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/


function init_mbstrings() {
	$res = true;
	$res &= extension_loaded('mbstring');

	if (version_compare(PHP_VERSION, '5.6', '<')) {
		ini_set('mbstring.internal_encoding', 'UTF-8');
		$res &= (ini_get('mbstring.internal_encoding') === 'UTF-8');
	}
	else {
		ini_set('default_charset', 'UTF-8');
		$res &= (ini_get('default_charset') === 'UTF-8');
	}

	ini_set('mbstring.detect_order', 'UTF-8, ISO-8859-1, JIS, SJIS');
	$res &= (ini_get('mbstring.detect_order') === 'UTF-8, ISO-8859-1, JIS, SJIS');

	return $res;
}

/**
 * Returns a list of all used locales.
 *
 * Each locale has the following properties:
 * - name       - the full name of the locale
 * - display    - whether to display the locale in the frontend
 *
 * @return array    an array of locales with locale codes as keys and arrays as values
 */
function getLocales() {
	return array(
		'en_GB' => array('name' => _('English (en_GB)'),	'display' => true),
		'en_US' => array('name' => _('English (en_US)'),	'display' => true),
		'bg_BG' => array('name' => _('Bulgarian (bg_BG)'),	'display' => false),
		'zh_CN' => array('name' => _('Chinese (zh_CN)'),	'display' => true),
		'zh_TW' => array('name' => _('Chinese (zh_TW)'),	'display' => false),
		'cs_CZ' => array('name' => _('Czech (cs_CZ)'),		'display' => true),
		'nl_NL' => array('name' => _('Dutch (nl_NL)'),		'display' => false),
		'fi_FI' => array('name' => _('Finnish (fi_FI)'),	'display' => false),
		'fr_FR' => array('name' => _('French (fr_FR)'),		'display' => false),
		'de_DE' => array('name' => _('German (de_DE)'),		'display' => false),
		'el_GR' => array('name' => _('Greek (el_GR)'),		'display' => false),
		'hu_HU' => array('name' => _('Hungarian (hu_HU)'),	'display' => false),
		'id_ID' => array('name' => _('Indonesian (id_ID)'),	'display' => false),
		'it_IT' => array('name' => _('Italian (it_IT)'),	'display' => true),
		'ko_KR' => array('name' => _('Korean (ko_KR)'),		'display' => false),
		'ja_JP' => array('name' => _('Japanese (ja_JP)'),	'display' => true),
		'lv_LV' => array('name' => _('Latvian (lv_LV)'),	'display' => false),
		'lt_LT' => array('name' => _('Lithuanian (lt_LT)'),	'display' => false),
		'fa_IR' => array('name' => _('Persian (fa_IR)'),	'display' => false),
		'pl_PL' => array('name' => _('Polish (pl_PL)'),		'display' => true),
		'pt_BR' => array('name' => _('Portuguese (pt_BR)'),	'display' => true),
		'pt_PT' => array('name' => _('Portuguese (pt_PT)'),	'display' => false),
		'ro_RO' => array('name' => _('Romanian (ro_RO)'),	'display' => false),
		'ru_RU' => array('name' => _('Russian (ru_RU)'),	'display' => true),
		'sk_SK' => array('name' => _('Slovak (sk_SK)'),		'display' => true),
		'es_ES' => array('name' => _('Spanish (es_ES)'),	'display' => false),
		'sv_SE' => array('name' => _('Swedish (sv_SE)'),	'display' => false),
		'tr_TR' => array('name' => _('Turkish (tr_TR)'),	'display' => false),
		'uk_UA' => array('name' => _('Ukrainian (uk_UA)'),	'display' => false),
		'vi_VN' => array('name' => _('Vietnamese (vi_VN)'),	'display' => false)
	);
}

/**
 * Return an array of locale name variants based on language.
 *
 * @param string $language in format 'ru_RU', 'en_EN' and so on
 * @return array a list of possible locale names
 */
function zbx_locale_variants($language) {
	if ((stristr($_SERVER['SERVER_SOFTWARE'], 'win32') !== false) || (stristr($_SERVER['SERVER_SOFTWARE'], 'win64') !== false)) {
		return zbx_locale_variants_win($language);
	}
	else {
		return zbx_locale_variants_unix($language);
	}
}

function zbx_locale_variants_unix($language) {
	$postfixes = array(
		'',
		'.utf8',
		'.UTF-8',
		'.iso885915',
		'.ISO8859-1',
		'.ISO8859-2',
		'.ISO8859-4',
		'.ISO8859-5',
		'.ISO8859-15',
		'.ISO8859-13',
		'.CP1131',
		'.CP1251',
		'.CP1251',
		'.CP949',
		'.KOI8-U',
		'.US-ASCII',
		'.eucKR',
		'.eucJP',
		'.SJIS',
		'.GB18030',
		'.GB2312',
		'.GBK',
		'.eucCN',
		'.Big5HKSCS',
		'.Big5',
		'.armscii8',
		'.cp1251',
		'.eucjp',
		'.euckr',
		'.euctw',
		'.gb18030',
		'.gbk',
		'.koi8r',
		'.tcvn'
	);
	$result = array();
	foreach ($postfixes as $postfix) {
		$result[] = $language.$postfix;
	}
	return $result;
}

function zbx_locale_variants_win($language) {
	// windows locales are written like language[_country[.charset]]
	// for a list of supported languages see:
	// http://msdn.microsoft.com/en-us/library/39cwe7zf(vs.71).aspx
	// http://docs.moodle.org/dev/Table_of_locales#Table
	$winLanguageName = array(
		'en_gb' => array('English_United Kingdom.1252', 'english-uk'),
		'en_us' => array('English_United States.1252', 'english-usa'),
		'bg_bg' => array('Bulgarian_Bulgaria.1251'),
		'zh_cn' => array('Chinese (Simplified)_People\'s Republic of China.936', 'chinese'),
		'zh_tw' => array('Chinese_Taiwan.950', 'chinese'),
		'cs_cz' => array('Czech_Czech Republic.1250', 'czech'),
		'nl_nl' => array('Dutch_Netherlands.1252', 'dutch'),
		'fi_fi' => array('Finnish_Finland.1252', 'finnish'),
		'fr_fr' => array('French_France.1252', 'french'),
		'de_de' => array('German_Germany.1252', 'german'),
		'el_gr' => array('Greek_Greece.1253', 'greek'),
		'hu_hu' => array('Hungarian_Hungary.1250', 'hungarian'),
		'id_id' => array('Indonesian_indonesia.1252', 'indonesian'),
		'it_it' => array('Italian_Italy.1252', 'italian'),
		'ko_kr' => array('Korean_Korea.949', 'korean'),
		'ja_jp' => array('Japanese_Japan.932', 'japanese'),
		'lv_lv' => array('Latvian_Latvia.1257', 'latvian'),
		'lt_lt' => array('Lithuanian_Lithuania.1257', 'lithuanian'),
		'fa_ir' => array('Farsi_Iran.1256', 'farsi'),
		'pl_pl' => array('Polish_Poland.1250', 'polish'),
		'pt_br' => array('Portuguese_Brazil.1252', 'portuguese-brazil'),
		'pt_pt' => array('Portuguese_Portugal.1252', 'portuguese'),
		'ro_ro' => array('Romanian_Romania.1250', 'romanian'),
		'ru_ru' => array('Russian_Russia.1251', 'russian'),
		'sk_sk' => array('Slovak_Slovakia.1250', 'slovak'),
		'es_es' => array('Spanish_Spain.1252', 'spanish'),
		'sv_se' => array('Swedish_Sweden.1252', 'swedish'),
		'tr_tr' => array('Turkish_Turkey.1254', 'turkish'),
		'uk_ua' => array('Ukrainian_Ukraine.1251', 'ukrainian'),
		'vi_vn' => array('Vietnamese_Viet Nam.1258', 'vietnamese')
	);
	return $winLanguageName[strtolower($language)];
}
