/**
 * frontend.js
 *
 * @package S2 Donation
 * @since   1.0.0
 * @author Shuban Studio <shuban.studio@gmail.com>
 * @version 1.0.1
 */

/* global s2_donation_frontend */
jQuery( document ).ready( function( $ ) {
    'use strict';

    if( typeof s2_donation_frontend === "undefined" || ! s2_donation_frontend.publishable_key ) return;

	$( document ).on( 'click', '#s2-donation-submit', function ( event ) {

        // Create an instance of the Stripe object with publishable API key
        var stripe = Stripe( s2_donation_frontend.publishable_key );

		event.preventDefault();

        $( '#s2-donation-submit' ).attr( 'disabled', 'disabled' );
        $( '#s2-donation-form .s2-ajax-loader' ).css( 'visibility', 'visible' );

        const formData = new FormData( $( '#s2-donation-form' ).get( 0 ) );
		formData.append( 'action', 'create_stripe_session' );
		formData.append( 'security', s2_donation_frontend.donation_nonce );

      	fetch( s2_donation_frontend.ajaxurl, {

      		method: 'POST',
        	body: formData,

      	} )
        .then( function ( response ) {

          	return response.json();

        } )
        .then( function ( session ) {

        	$( '#s2-donation-form input' ).attr( 'aria-invalid', 'false' );
			$( '#s2-donation-form .error' ).text( '' );
			$( '#s2-donation-form .error' ).removeClass( 'active' );

    		if( session.error ) {

        		var element, errorElement;
    			$.each( session.error, function( key, val ) {
					element = $( '#' + key );
					element.attr( 'aria-invalid', 'true' );

					errorElement = element.siblings( '.error' );
					errorElement.addClass( 'active' );
					errorElement.text( val );
				} );

                $( '#s2-donation-submit' ).removeAttr( 'disabled' );
                $( '#s2-donation-form .s2-ajax-loader' ).css( 'visibility', 'hidden' );

				throw new Error( session.error.s2_donation_error_message );

    		} else {

          		return stripe.redirectToCheckout( { sessionId: session.id } );

          	}

        } )
        .then( function ( result ) {

          	if ( result.error ) {

            	alert( result.error.message );

          	}

        } )
        .catch( function ( error ) {

          	console.error( "Error:", error );

        } );

	} );

    // on recurring donation option change show donation info
    $( document ).on( 'change', '#s2-recurring-donation', function() {

        var recurring_donation = $( this ).val(),
            recurring_donation_info = $( '.s2-recurring-donation-info' ),
            info = '';

        if( recurring_donation != '' ) {
            info = 'You will be donate amount ' + recurring_donation;
        }

        recurring_donation_info.text( info );

    } );

} );

function send_mail( email ) {

    var data = { action : 'send_mail', email : email, security : s2_donation_frontend.donation_nonce };

    jQuery.ajax( {
        method: 'POST',
        url: s2_donation_frontend.ajaxurl,
        data: data
    } );

}
