<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_DN_VERSION' ) ) {
	exit;
}

/**
 * Donation form
 *
 * @package S2 Donation\Templates
 * @version 1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */

$options = get_option( 's2dn_settings' );
$recurring_frequency_options = s2_get_recurring_frequency_options();
?>

<?php if( ! empty( $options['donation_title'] ) ) : ?>
	<h3 class="s2-donation-title"><?php esc_html_e( $options['donation_title'] ); ?></h3>
<?php endif; ?>

<?php if( ! empty( $options['donation_description'] ) ) : ?>
	<p>
		<small><?php esc_html_e( $options['donation_description'] ); ?></small>
	</p>
<?php endif; ?>

<form class="s2-donation-form" id="s2-donation-form" method="post">
	<p>
		<label>
			<span>Your email</span>
			<input id="s2-donation-email" type="email" name="s2-donation-email" value="" size="40" aria-required="true" aria-invalid="false" />
			<span class="error" aria-hidden="true"></span>
		</label>
	</p>
	<p>
		<label>
			<span>
				Amount in <?php ! empty( $options['donation_currency'] ) ? esc_html_e( $options['donation_currency'] . '(' .s2_get_currency_symbols( $options['donation_currency'] ) .')' ) : ''; ?>
			</span>
			<?php if( empty( $options['fixed_donation_amount'] ) || $options['fixed_donation_amount'] == 'no' ) : ?>
				<input id="s2-donation-amount" type="number" name="s2-donation-amount" value="<?php echo esc_attr( $options['donation_amount'] ); ?>" size="40" min="1" step="any" aria-required="true" aria-invalid="false" />
			<?php else: ?>
				<span> : <?php esc_html_e( $options['donation_amount'] ); ?></span>
			<?php endif; ?>
			<span class="error" aria-hidden="true"></span>
		</label>
	</p>
	<?php if( ! empty( $options['recurring_donation'] ) && $options['recurring_donation'] != 'no' ) : ?>
		<p>
			<label>
				<span>Recurring Donation : </span>
	    		<select id="s2-recurring-donation" name="s2-recurring-donation" aria-invalid="false">
	    			<option value="">None</option>;
	    			<?php
	    			foreach ( $recurring_frequency_options as $key => $frequency ) {
	    				echo "<option value='" . esc_attr( $key ) . "'>" . esc_html( $frequency['name'] ) . "</option>";
	    			}
	    			?>
	    		</select>
	    		<span class="s2-recurring-donation-info" aria-hidden="true"></span>
	    	</label>
		</p>
	<?php endif; ?>
	<p>
		<input id="s2-donation-submit" type="submit" value="Submit" />
		<span class="s2-ajax-loader"></span>
	</p>
</form>
