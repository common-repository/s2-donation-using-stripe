<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_DN_VERSION' ) ) {
	exit;
}

/**
 * Donation Report Page
 *
 * @package S2 Donation\Templates
 * @version 1.0.4
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
?>

<div class="wrap">
	<div id="poststuff">
		<div id="post-body" class="metabox-holder">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<form method="get">
						<input type="hidden" name="page" value="s2-donation" />
						<input type="hidden" name="tab" value="report" />
						<input type="hidden" name="section" value="<?php esc_attr_e( $_REQUEST['section'] ); ?>" />
						<?php $report_list->search_box( 'search', 'search_id' ); ?>
					</form>
					<form method="get">
						<input type="hidden" name="page" value="s2-donation" />
						<input type="hidden" name="tab" value="report" />
						<input type="hidden" name="section" value="<?php esc_attr_e( $_REQUEST['section'] ); ?>" />
						<?php
						$report_list->prepare_items();
						$report_list->display();
						?>
					</form>
				</div>
			</div>
		</div>
		<br class="clear" />
	</div>
</div>
