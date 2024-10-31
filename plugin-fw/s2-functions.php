<?php
/**
 * This file belongs to the S2 Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 's2_format_decimal' ) ) {
	/**
     * Format decimal numbers ready for DB storage.
     *
     * Sanitize, remove decimals, and optionally round + trim off zeros.
     *
     * This function does not remove thousands - this should be done before passing a value to the function.
     *
     * @param  float|string $number     Expects either a float or a string with a decimal separator only (no thousands).
     * @param  mixed        $dp number  Number of decimal points to use, blank to use '.', or false to avoid all rounding.
     * @param  bool         $trim_zeros From end of string.
     * @since  1.0.0
     * @return string
     */
    function s2_format_decimal( $number, $dp = false, $trim_zeros = false ) {
        $locale   = localeconv();
        $decimals = [ '.', $locale['decimal_point'], $locale['mon_decimal_point'] ];

        // Remove locale from string.
        if ( ! is_float( $number ) ) {
            $number = str_replace( $decimals, '.', $number );

            // Convert multiple dots to just one.
            $number = preg_replace( '/\.(?![^.]+$)|[^0-9.-]/', '', s2_clean( $number ) );
        }

        if ( false !== $dp ) {
            $dp     = intval( '' === $dp ? 2 : $dp );
            $number = number_format( floatval( $number ), $dp, '.', '' );
        } elseif ( is_float( $number ) ) {
            // DP is false - don't use number format, just return a string using whatever is given. Remove scientific notation using sprintf.
            $number = str_replace( $decimals, '.', sprintf( '%.8f', $number ) );
            // We already had a float, so trailing zeros are not needed.
            $trim_zeros = true;
        }

        if ( $trim_zeros && strstr( $number, '.' ) ) {
            $number = rtrim( rtrim( $number, '0' ), '.' );
        }

        return $number;
    }
}

if ( ! function_exists( 's2_clean' ) ) {
    /**
     * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
     * Non-scalar values are ignored.
     *
     * @param string|array $var Data to sanitize.
     * @since  1.0.0
     * @return string|array
     */
    function s2_clean( $var ) {
        if ( is_array( $var ) ) {
            return array_map( 's2_clean', $var );
        } else {
            return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
        }
    }
}

if ( ! function_exists( 's2_help_tip' ) ) {
    /**
     * Display a help tip.
     *
     * @param  string $tip        Help tip text.
     * @param  bool   $allow_html Allow sanitized HTML if true or escape.
     * @since  1.0.0
     * @return string
     */
    function s2_help_tip( $tip, $allow_html = false ) {
        if ( $allow_html ) {
            $tip = s2_sanitize_tooltip( $tip );
        } else {
            $tip = esc_attr( $tip );
        }

        return '<span class="s2-help-tip" data-tip="' . $tip . '"></span>';
    }
}

if ( ! function_exists( 's2_sanitize_tooltip' ) ) {
    /**
     * Sanitize a string destined to be a tooltip.
     * Tooltips are encoded with htmlspecialchars to prevent XSS. Should not be used in conjunction with esc_attr()
     *
     * @param  string $var Data to sanitize.
     * @since  1.0.0
     * @return string
     */
    function s2_sanitize_tooltip( $var ) {
        return htmlspecialchars(
            wp_kses(
                html_entity_decode( $var ),
                [
                    'br'     => [],
                    'em'     => [],
                    'strong' => [],
                    'small'  => [],
                    'span'   => [],
                    'ul'     => [],
                    'li'     => [],
                    'ol'     => [],
                    'p'      => [],
                ]
            )
        );
    }
}

if ( ! function_exists( 's2_format_localized_price' ) ) {
    /**
     * Format a price with Currency Locale settings.
     *
     * @param  string $value Price to localize.
     * @since  1.0.0
     * @return string
     */
    function s2_format_localized_price( $value ) {
        return apply_filters( 's2_format_localized_price', strval( $value ), $value );
    }
}

if ( ! function_exists( 's2_format_localized_decimal' ) ) {
    /**
     * Format a decimal with PHP Locale settings.
     *
     * @param  string $value Decimal to localize.
     * @since  1.0.0
     * @return string
     */
    function s2_format_localized_decimal( $value ) {
        $locale = localeconv();
        return apply_filters( 's2_format_localized_decimal', str_replace( '.', $locale['decimal_point'], strval( $value ) ), $value );
    }
}
