<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_DN_VERSION' ) ) {
	exit;
}

/**
 * Implements helper functions for S2 Donation
 *
 * @package S2 Donation
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */

if ( ! function_exists( 's2_get_currencies' ) ) {
	/**
	 * Get full list of currency codes.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	function s2_get_currencies() {
		static $currencies;

		if ( ! isset( $currencies ) ) {
			$currencies = array_unique(
				apply_filters(
					's2_currencies',
					[
						'AED' => __( 'United Arab Emirates dirham', 's2-donation' ),
						'AFN' => __( 'Afghan afghani', 's2-donation' ),
						'ALL' => __( 'Albanian lek', 's2-donation' ),
						'AMD' => __( 'Armenian dram', 's2-donation' ),
						'ANG' => __( 'Netherlands Antillean guilder', 's2-donation' ),
						'AOA' => __( 'Angolan kwanza', 's2-donation' ),
						'ARS' => __( 'Argentine peso', 's2-donation' ),
						'AUD' => __( 'Australian dollar', 's2-donation' ),
						'AWG' => __( 'Aruban florin', 's2-donation' ),
						'AZN' => __( 'Azerbaijani manat', 's2-donation' ),
						'BAM' => __( 'Bosnia and Herzegovina convertible mark', 's2-donation' ),
						'BBD' => __( 'Barbadian dollar', 's2-donation' ),
						'BDT' => __( 'Bangladeshi taka', 's2-donation' ),
						'BGN' => __( 'Bulgarian lev', 's2-donation' ),
						'BHD' => __( 'Bahraini dinar', 's2-donation' ),
						'BIF' => __( 'Burundian franc', 's2-donation' ),
						'BMD' => __( 'Bermudian dollar', 's2-donation' ),
						'BND' => __( 'Brunei dollar', 's2-donation' ),
						'BOB' => __( 'Bolivian boliviano', 's2-donation' ),
						'BRL' => __( 'Brazilian real', 's2-donation' ),
						'BSD' => __( 'Bahamian dollar', 's2-donation' ),
						'BTC' => __( 'Bitcoin', 's2-donation' ),
						'BTN' => __( 'Bhutanese ngultrum', 's2-donation' ),
						'BWP' => __( 'Botswana pula', 's2-donation' ),
						'BYR' => __( 'Belarusian ruble (old)', 's2-donation' ),
						'BYN' => __( 'Belarusian ruble', 's2-donation' ),
						'BZD' => __( 'Belize dollar', 's2-donation' ),
						'CAD' => __( 'Canadian dollar', 's2-donation' ),
						'CDF' => __( 'Congolese franc', 's2-donation' ),
						'CHF' => __( 'Swiss franc', 's2-donation' ),
						'CLP' => __( 'Chilean peso', 's2-donation' ),
						'CNY' => __( 'Chinese yuan', 's2-donation' ),
						'COP' => __( 'Colombian peso', 's2-donation' ),
						'CRC' => __( 'Costa Rican col&oacute;n', 's2-donation' ),
						'CUC' => __( 'Cuban convertible peso', 's2-donation' ),
						'CUP' => __( 'Cuban peso', 's2-donation' ),
						'CVE' => __( 'Cape Verdean escudo', 's2-donation' ),
						'CZK' => __( 'Czech koruna', 's2-donation' ),
						'DJF' => __( 'Djiboutian franc', 's2-donation' ),
						'DKK' => __( 'Danish krone', 's2-donation' ),
						'DOP' => __( 'Dominican peso', 's2-donation' ),
						'DZD' => __( 'Algerian dinar', 's2-donation' ),
						'EGP' => __( 'Egyptian pound', 's2-donation' ),
						'ERN' => __( 'Eritrean nakfa', 's2-donation' ),
						'ETB' => __( 'Ethiopian birr', 's2-donation' ),
						'EUR' => __( 'Euro', 's2-donation' ),
						'FJD' => __( 'Fijian dollar', 's2-donation' ),
						'FKP' => __( 'Falkland Islands pound', 's2-donation' ),
						'GBP' => __( 'Pound sterling', 's2-donation' ),
						'GEL' => __( 'Georgian lari', 's2-donation' ),
						'GGP' => __( 'Guernsey pound', 's2-donation' ),
						'GHS' => __( 'Ghana cedi', 's2-donation' ),
						'GIP' => __( 'Gibraltar pound', 's2-donation' ),
						'GMD' => __( 'Gambian dalasi', 's2-donation' ),
						'GNF' => __( 'Guinean franc', 's2-donation' ),
						'GTQ' => __( 'Guatemalan quetzal', 's2-donation' ),
						'GYD' => __( 'Guyanese dollar', 's2-donation' ),
						'HKD' => __( 'Hong Kong dollar', 's2-donation' ),
						'HNL' => __( 'Honduran lempira', 's2-donation' ),
						'HRK' => __( 'Croatian kuna', 's2-donation' ),
						'HTG' => __( 'Haitian gourde', 's2-donation' ),
						'HUF' => __( 'Hungarian forint', 's2-donation' ),
						'IDR' => __( 'Indonesian rupiah', 's2-donation' ),
						'ILS' => __( 'Israeli new shekel', 's2-donation' ),
						'IMP' => __( 'Manx pound', 's2-donation' ),
						'INR' => __( 'Indian rupee', 's2-donation' ),
						'IQD' => __( 'Iraqi dinar', 's2-donation' ),
						'IRR' => __( 'Iranian rial', 's2-donation' ),
						'IRT' => __( 'Iranian toman', 's2-donation' ),
						'ISK' => __( 'Icelandic kr&oacute;na', 's2-donation' ),
						'JEP' => __( 'Jersey pound', 's2-donation' ),
						'JMD' => __( 'Jamaican dollar', 's2-donation' ),
						'JOD' => __( 'Jordanian dinar', 's2-donation' ),
						'JPY' => __( 'Japanese yen', 's2-donation' ),
						'KES' => __( 'Kenyan shilling', 's2-donation' ),
						'KGS' => __( 'Kyrgyzstani som', 's2-donation' ),
						'KHR' => __( 'Cambodian riel', 's2-donation' ),
						'KMF' => __( 'Comorian franc', 's2-donation' ),
						'KPW' => __( 'North Korean won', 's2-donation' ),
						'KRW' => __( 'South Korean won', 's2-donation' ),
						'KWD' => __( 'Kuwaiti dinar', 's2-donation' ),
						'KYD' => __( 'Cayman Islands dollar', 's2-donation' ),
						'KZT' => __( 'Kazakhstani tenge', 's2-donation' ),
						'LAK' => __( 'Lao kip', 's2-donation' ),
						'LBP' => __( 'Lebanese pound', 's2-donation' ),
						'LKR' => __( 'Sri Lankan rupee', 's2-donation' ),
						'LRD' => __( 'Liberian dollar', 's2-donation' ),
						'LSL' => __( 'Lesotho loti', 's2-donation' ),
						'LYD' => __( 'Libyan dinar', 's2-donation' ),
						'MAD' => __( 'Moroccan dirham', 's2-donation' ),
						'MDL' => __( 'Moldovan leu', 's2-donation' ),
						'MGA' => __( 'Malagasy ariary', 's2-donation' ),
						'MKD' => __( 'Macedonian denar', 's2-donation' ),
						'MMK' => __( 'Burmese kyat', 's2-donation' ),
						'MNT' => __( 'Mongolian t&ouml;gr&ouml;g', 's2-donation' ),
						'MOP' => __( 'Macanese pataca', 's2-donation' ),
						'MRU' => __( 'Mauritanian ouguiya', 's2-donation' ),
						'MUR' => __( 'Mauritian rupee', 's2-donation' ),
						'MVR' => __( 'Maldivian rufiyaa', 's2-donation' ),
						'MWK' => __( 'Malawian kwacha', 's2-donation' ),
						'MXN' => __( 'Mexican peso', 's2-donation' ),
						'MYR' => __( 'Malaysian ringgit', 's2-donation' ),
						'MZN' => __( 'Mozambican metical', 's2-donation' ),
						'NAD' => __( 'Namibian dollar', 's2-donation' ),
						'NGN' => __( 'Nigerian naira', 's2-donation' ),
						'NIO' => __( 'Nicaraguan c&oacute;rdoba', 's2-donation' ),
						'NOK' => __( 'Norwegian krone', 's2-donation' ),
						'NPR' => __( 'Nepalese rupee', 's2-donation' ),
						'NZD' => __( 'New Zealand dollar', 's2-donation' ),
						'OMR' => __( 'Omani rial', 's2-donation' ),
						'PAB' => __( 'Panamanian balboa', 's2-donation' ),
						'PEN' => __( 'Sol', 's2-donation' ),
						'PGK' => __( 'Papua New Guinean kina', 's2-donation' ),
						'PHP' => __( 'Philippine peso', 's2-donation' ),
						'PKR' => __( 'Pakistani rupee', 's2-donation' ),
						'PLN' => __( 'Polish z&#x142;oty', 's2-donation' ),
						'PRB' => __( 'Transnistrian ruble', 's2-donation' ),
						'PYG' => __( 'Paraguayan guaran&iacute;', 's2-donation' ),
						'QAR' => __( 'Qatari riyal', 's2-donation' ),
						'RON' => __( 'Romanian leu', 's2-donation' ),
						'RSD' => __( 'Serbian dinar', 's2-donation' ),
						'RUB' => __( 'Russian ruble', 's2-donation' ),
						'RWF' => __( 'Rwandan franc', 's2-donation' ),
						'SAR' => __( 'Saudi riyal', 's2-donation' ),
						'SBD' => __( 'Solomon Islands dollar', 's2-donation' ),
						'SCR' => __( 'Seychellois rupee', 's2-donation' ),
						'SDG' => __( 'Sudanese pound', 's2-donation' ),
						'SEK' => __( 'Swedish krona', 's2-donation' ),
						'SGD' => __( 'Singapore dollar', 's2-donation' ),
						'SHP' => __( 'Saint Helena pound', 's2-donation' ),
						'SLL' => __( 'Sierra Leonean leone', 's2-donation' ),
						'SOS' => __( 'Somali shilling', 's2-donation' ),
						'SRD' => __( 'Surinamese dollar', 's2-donation' ),
						'SSP' => __( 'South Sudanese pound', 's2-donation' ),
						'STN' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 's2-donation' ),
						'SYP' => __( 'Syrian pound', 's2-donation' ),
						'SZL' => __( 'Swazi lilangeni', 's2-donation' ),
						'THB' => __( 'Thai baht', 's2-donation' ),
						'TJS' => __( 'Tajikistani somoni', 's2-donation' ),
						'TMT' => __( 'Turkmenistan manat', 's2-donation' ),
						'TND' => __( 'Tunisian dinar', 's2-donation' ),
						'TOP' => __( 'Tongan pa&#x2bb;anga', 's2-donation' ),
						'TRY' => __( 'Turkish lira', 's2-donation' ),
						'TTD' => __( 'Trinidad and Tobago dollar', 's2-donation' ),
						'TWD' => __( 'New Taiwan dollar', 's2-donation' ),
						'TZS' => __( 'Tanzanian shilling', 's2-donation' ),
						'UAH' => __( 'Ukrainian hryvnia', 's2-donation' ),
						'UGX' => __( 'Ugandan shilling', 's2-donation' ),
						'USD' => __( 'United States (US) dollar', 's2-donation' ),
						'UYU' => __( 'Uruguayan peso', 's2-donation' ),
						'UZS' => __( 'Uzbekistani som', 's2-donation' ),
						'VEF' => __( 'Venezuelan bol&iacute;var', 's2-donation' ),
						'VES' => __( 'Bol&iacute;var soberano', 's2-donation' ),
						'VND' => __( 'Vietnamese &#x111;&#x1ed3;ng', 's2-donation' ),
						'VUV' => __( 'Vanuatu vatu', 's2-donation' ),
						'WST' => __( 'Samoan t&#x101;l&#x101;', 's2-donation' ),
						'XAF' => __( 'Central African CFA franc', 's2-donation' ),
						'XCD' => __( 'East Caribbean dollar', 's2-donation' ),
						'XOF' => __( 'West African CFA franc', 's2-donation' ),
						'XPF' => __( 'CFP franc', 's2-donation' ),
						'YER' => __( 'Yemeni rial', 's2-donation' ),
						'ZAR' => __( 'South African rand', 's2-donation' ),
						'ZMW' => __( 'Zambian kwacha', 's2-donation' ),
					]
				)
			);
		}

		return $currencies;
	}
}

if ( ! function_exists( 's2_get_currency_symbols' ) ) {
	/**
	 * Get all available Currency symbols.
	 *
	 * Currency symbols and names should follow the Unicode CLDR recommendation (http://cldr.unicode.org/translation/currency-names)
	 *
	 * @param string $currency
	 * @since 1.0.0
	 * @return array|string
	 */
	function s2_get_currency_symbols( $currency = '' ) {

		$symbols = apply_filters(
			's2_currency_symbols',
			[
				'AED' => '&#x62f;.&#x625;',
				'AFN' => '&#x60b;',
				'ALL' => 'L',
				'AMD' => 'AMD',
				'ANG' => '&fnof;',
				'AOA' => 'Kz',
				'ARS' => '&#36;',
				'AUD' => '&#36;',
				'AWG' => 'Afl.',
				'AZN' => 'AZN',
				'BAM' => 'KM',
				'BBD' => '&#36;',
				'BDT' => '&#2547;&nbsp;',
				'BGN' => '&#1083;&#1074;.',
				'BHD' => '.&#x62f;.&#x628;',
				'BIF' => 'Fr',
				'BMD' => '&#36;',
				'BND' => '&#36;',
				'BOB' => 'Bs.',
				'BRL' => '&#82;&#36;',
				'BSD' => '&#36;',
				'BTC' => '&#3647;',
				'BTN' => 'Nu.',
				'BWP' => 'P',
				'BYR' => 'Br',
				'BYN' => 'Br',
				'BZD' => '&#36;',
				'CAD' => '&#36;',
				'CDF' => 'Fr',
				'CHF' => '&#67;&#72;&#70;',
				'CLP' => '&#36;',
				'CNY' => '&yen;',
				'COP' => '&#36;',
				'CRC' => '&#x20a1;',
				'CUC' => '&#36;',
				'CUP' => '&#36;',
				'CVE' => '&#36;',
				'CZK' => '&#75;&#269;',
				'DJF' => 'Fr',
				'DKK' => 'DKK',
				'DOP' => 'RD&#36;',
				'DZD' => '&#x62f;.&#x62c;',
				'EGP' => 'EGP',
				'ERN' => 'Nfk',
				'ETB' => 'Br',
				'EUR' => '&euro;',
				'FJD' => '&#36;',
				'FKP' => '&pound;',
				'GBP' => '&pound;',
				'GEL' => '&#x20be;',
				'GGP' => '&pound;',
				'GHS' => '&#x20b5;',
				'GIP' => '&pound;',
				'GMD' => 'D',
				'GNF' => 'Fr',
				'GTQ' => 'Q',
				'GYD' => '&#36;',
				'HKD' => '&#36;',
				'HNL' => 'L',
				'HRK' => 'kn',
				'HTG' => 'G',
				'HUF' => '&#70;&#116;',
				'IDR' => 'Rp',
				'ILS' => '&#8362;',
				'IMP' => '&pound;',
				'INR' => '&#8377;',
				'IQD' => '&#x639;.&#x62f;',
				'IRR' => '&#xfdfc;',
				'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
				'ISK' => 'kr.',
				'JEP' => '&pound;',
				'JMD' => '&#36;',
				'JOD' => '&#x62f;.&#x627;',
				'JPY' => '&yen;',
				'KES' => 'KSh',
				'KGS' => '&#x441;&#x43e;&#x43c;',
				'KHR' => '&#x17db;',
				'KMF' => 'Fr',
				'KPW' => '&#x20a9;',
				'KRW' => '&#8361;',
				'KWD' => '&#x62f;.&#x643;',
				'KYD' => '&#36;',
				'KZT' => '&#8376;',
				'LAK' => '&#8365;',
				'LBP' => '&#x644;.&#x644;',
				'LKR' => '&#xdbb;&#xdd4;',
				'LRD' => '&#36;',
				'LSL' => 'L',
				'LYD' => '&#x644;.&#x62f;',
				'MAD' => '&#x62f;.&#x645;.',
				'MDL' => 'MDL',
				'MGA' => 'Ar',
				'MKD' => '&#x434;&#x435;&#x43d;',
				'MMK' => 'Ks',
				'MNT' => '&#x20ae;',
				'MOP' => 'P',
				'MRU' => 'UM',
				'MUR' => '&#x20a8;',
				'MVR' => '.&#x783;',
				'MWK' => 'MK',
				'MXN' => '&#36;',
				'MYR' => '&#82;&#77;',
				'MZN' => 'MT',
				'NAD' => 'N&#36;',
				'NGN' => '&#8358;',
				'NIO' => 'C&#36;',
				'NOK' => '&#107;&#114;',
				'NPR' => '&#8360;',
				'NZD' => '&#36;',
				'OMR' => '&#x631;.&#x639;.',
				'PAB' => 'B/.',
				'PEN' => 'S/',
				'PGK' => 'K',
				'PHP' => '&#8369;',
				'PKR' => '&#8360;',
				'PLN' => '&#122;&#322;',
				'PRB' => '&#x440;.',
				'PYG' => '&#8370;',
				'QAR' => '&#x631;.&#x642;',
				'RMB' => '&yen;',
				'RON' => 'lei',
				'RSD' => '&#1088;&#1089;&#1076;',
				'RUB' => '&#8381;',
				'RWF' => 'Fr',
				'SAR' => '&#x631;.&#x633;',
				'SBD' => '&#36;',
				'SCR' => '&#x20a8;',
				'SDG' => '&#x62c;.&#x633;.',
				'SEK' => '&#107;&#114;',
				'SGD' => '&#36;',
				'SHP' => '&pound;',
				'SLL' => 'Le',
				'SOS' => 'Sh',
				'SRD' => '&#36;',
				'SSP' => '&pound;',
				'STN' => 'Db',
				'SYP' => '&#x644;.&#x633;',
				'SZL' => 'L',
				'THB' => '&#3647;',
				'TJS' => '&#x405;&#x41c;',
				'TMT' => 'm',
				'TND' => '&#x62f;.&#x62a;',
				'TOP' => 'T&#36;',
				'TRY' => '&#8378;',
				'TTD' => '&#36;',
				'TWD' => '&#78;&#84;&#36;',
				'TZS' => 'Sh',
				'UAH' => '&#8372;',
				'UGX' => 'UGX',
				'USD' => '&#36;',
				'UYU' => '&#36;',
				'UZS' => 'UZS',
				'VEF' => 'Bs F',
				'VES' => 'Bs.S',
				'VND' => '&#8363;',
				'VUV' => 'Vt',
				'WST' => 'T',
				'XAF' => 'CFA',
				'XCD' => '&#36;',
				'XOF' => 'CFA',
				'XPF' => 'Fr',
				'YER' => '&#xfdfc;',
				'ZAR' => '&#82;',
				'ZMW' => 'ZK',
			]
		);

		if( ! empty( $currency ) ) $symbols = $symbols[ $currency ];

		return $symbols;
	}
}

if ( ! function_exists( 's2_no_decimal_currencies' ) ) {
	/**
	 * List of currencies supported by Stripe that has no decimals
	 * https://stripe.com/docs/currencies#zero-decimal from https://stripe.com/docs/currencies#presentment-currencies
	 *
	 * @return array $currencies
	 */
	function s2_no_decimal_currencies() {
		return [
			'bif', // Burundian Franc
			'clp', // Chilean Peso
			'djf', // Djiboutian Franc
			'gnf', // Guinean Franc
			'jpy', // Japanese Yen
			'kmf', // Comorian Franc
			'krw', // South Korean Won
			'mga', // Malagasy Ariary
			'pyg', // Paraguayan Guaraní
			'rwf', // Rwandan Franc
			'ugx', // Ugandan Shilling
			'vnd', // Vietnamese Đồng
			'vuv', // Vanuatu Vatu
			'xaf', // Central African Cfa Franc
			'xof', // West African Cfa Franc
			'xpf', // Cfp Franc
		];
	}
}

if ( ! function_exists( 's2_get_stripe_amount' ) ) {
	/**
	 * Get Stripe amount to pay
	 *
	 * @param float  $total Amount due.
	 * @param string $currency Accepted currency.
	 *
	 * @return float|int
	 */
	function s2_get_stripe_amount( $total, $currency ) {
		if ( in_array( strtolower( $currency ), s2_no_decimal_currencies() ) ) {
			return absint( $total );
		} else {
			// plugin-fw function s2_format_decimal
			return absint( s2_format_decimal( ( (float) $total * 100 ), 2 ) ); // In cents.
		}
	}
}

if ( ! function_exists( 's2_get_pages' ) ) {
	/**
	 * Get all pages.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	function s2_get_pages() {
		$pages = [];

		$page_ids = get_all_page_ids();
		foreach($page_ids as $page_id) {
		    $pages[ $page_id ] = get_the_title( $page_id );
		}

		return $pages;
	}
}

if ( ! function_exists( 's2_get_recurring_frequency_options' ) ) {

	/**
	 * Return the list of recurring frequency otption.
	 *
	 * @return array
	 * @since  1.0.1
	 */
	function s2_get_recurring_frequency_options( $frequency = '' ) {
		$options = [
			'annually'       => [
									'name'   => __( 'Annually', 's2-donation' ),
									'period' => '1',
									'time'   => 'year',
								],
			'every 6 months' => [
									'name'   => __( 'Every 6 Months', 's2-donation' ),
									'period' => '6',
									'time'   => 'month',
								],
			'quarterly'     => [
									'name'   => __( 'Quarterly', 's2-donation' ),
									'period' => '3',
									'time'   => 'month',
								],
			'monthly'       => [
									'name'   => __( 'Monthly', 's2-donation' ),
									'period' => '1',
									'time'   => 'month',
								],
			'every 2 weeks' => [
									'name'   => __( 'Every 2 Weeks', 's2-donation' ),
									'period' => '2',
									'time'   => 'week',
								],
			'weekly'        => [
									'name'   => __( 'Weekly', 's2-donation' ),
									'period' => '1',
									'time'   => 'week',
								],
			'daily'      	=> [
									'name'   => __( 'Daily', 's2-donation' ),
									'period' => '1',
									'time'   => 'day',
								],
		];

		if ( ! empty( $frequency ) ) {
			$options = $options[ $frequency ];
		}

		return $options;
	}
}
