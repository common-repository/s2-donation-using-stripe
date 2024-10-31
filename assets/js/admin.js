/**
 * admin.js
 *
 * @package S2 Donation
 * @since   1.0.0
 * @author Shuban Studio <shuban.studio@gmail.com>
 * @version 1.0.3
 */

/* global s2_donation_admin */
( function( $ ) {

    'use strict';

    $( function() {
        
        $( '#s2-donation-admin-form' ).submit( function( e ) {

            e.preventDefault();

            $( '.response-output' ).hide();

            var data = $( this ).serialize();

            var url = s2_donation_admin.api_settings.root;

            url = url.replace( s2_donation_admin.api_settings.namespace, s2_donation_admin.api_settings.namespace + '/admin-settings' );

            $.ajax( {
                method: 'POST',
                url: url,
                beforeSend: function( xhr ) {
                    xhr.setRequestHeader( 'X-WP-Nonce', s2_donation_admin.api_settings.nonce );
                },
                data: data,
            } ).done( function( response ) {
                
                if( response.success == true ) {
                    $( '.response-output' ).show();
                }

            } );

        } );

    } );

    /**
     * Initialize.
     */
    s2_donation_admin.init = function() {
        $( document.body ).on( 'change', '#s2dn_testmode', function() {
            var test_secret_key = $( '#s2dn_test_secret_key' ).parents( 'tr' ).eq( 0 ),
                test_publishable_key = $( '#s2dn_test_publishable_key' ).parents( 'tr' ).eq( 0 ),
                live_secret_key = $( '#s2dn_secret_key' ).parents( 'tr' ).eq( 0 ),
                live_publishable_key = $( '#s2dn_publishable_key' ).parents( 'tr' ).eq( 0 )

            if ( $( this ).is( ':checked' ) ) {
                test_secret_key.show();
                test_publishable_key.show();
                live_secret_key.hide();
                live_publishable_key.hide();
            } else {
                test_secret_key.hide();
                test_publishable_key.hide();
                live_secret_key.show();
                live_publishable_key.show();
            }
        } );

        $( '#s2dn_testmode' ).trigger( 'change' );

    };

    s2_donation_admin.init();

} )( jQuery );
