<?php
/**
 * This file belongs to the S2 Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

// Exit if accessed directly
if ( ! defined ( 'ABSPATH' ) ) {
    exit;
}

/**
 * Abstract Settings API Class
 *
 * Admin Settings API
 *
 * @package  S2 Plugin\Abstracts
 */

/**
 * S2_Settings_API class.
 */
if ( ! class_exists( 'S2_Settings_API' ) ) {

	abstract class S2_Settings_API {

		/**
		 * The plugin ID. Used for option names.
		 *
		 * @var string
		 */
		public $plugin_id = '';

		/**
		 * ID of the class extending the settings API. Used in option names.
		 *
		 * @var string
		 */
		public $id = '';

		/**
		 * Validation errors.
		 *
		 * @var array of strings
		 */
		public $errors = [];

		/**
		 * Setting values.
		 *
		 * @var array
		 */
		public $settings = [];

		/**
		 * Form option fields.
		 *
		 * @var array
		 */
		public $form_fields = [];

		/**
		 * The posted settings data. When empty, $_POST data will be used.
		 *
		 * @var array
		 */
		protected $data = [];

		/**
		 * Get the form fields after they are initialized.
		 *
		 * @since  1.0.0
		 * @return array of options
		 */
		public function get_form_fields() {
			return apply_filters( 's2_settings_api_form_fields_' . $this->id, array_map( [ $this, 'set_defaults' ], $this->form_fields ) );
		}

		/**
		 * Set default required properties for each field.
		 *
		 * @param array $field Setting field array.
		 * @since  1.0.0
		 * @return array
		 */
		protected function set_defaults( $field ) {
			if ( ! isset( $field['default'] ) ) {
				$field['default'] = '';
			}
			return $field;
		}

		/**
		 * Output the admin options table.
		 */
		public function admin_options() {
			echo '<table class="form-table">' . $this->generate_settings_html( $this->get_form_fields(), false ) . '</table>';
		}

		/**
		 * Initialise settings form fields.
		 *
		 * Add an array of fields to be displayed on the gateway's settings screen.
		 *
		 * @since  1.0.0
		 */
		public function init_form_fields() {}

		/**
		 * Return the name of the option in the WP DB.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_option_key() {
			return $this->plugin_id . $this->id . '_settings';
		}

		/**
		 * Get a fields type. Defaults to "text" if not set.
		 *
		 * @param  array $field Field key.
		 * @since  1.0.0
		 * @return string
		 */
		public function get_field_type( $field ) {
			return empty( $field['type'] ) ? 'text' : $field['type'];
		}

		/**
		 * Get a fields default value. Defaults to "" if not set.
		 *
		 * @param  array $field Field key.
		 * @since  1.0.0
		 * @return string
		 */
		public function get_field_default( $field ) {
			return empty( $field['default'] ) ? '' : $field['default'];
		}

		/**
		 * Get a field's posted and validated value.
		 *
		 * @param string $key Field key.
		 * @param array  $field Field array.
		 * @param array  $post_data Posted data.
		 * @since  1.0.0
		 * @return string
		 */
		public function get_field_value( $key, $field, $post_data = [] ) {
			$type      = $this->get_field_type( $field );
			$field_key = $this->get_field_key( $key );
			$post_data = empty( $post_data ) ? $_POST : $post_data; // WPCS: CSRF ok, input var ok.
			$value     = isset( $post_data[ $field_key ] ) ? $post_data[ $field_key ] : null;

			if ( isset( $field['sanitize_callback'] ) && is_callable( $field['sanitize_callback'] ) ) {
				return call_user_func( $field['sanitize_callback'], $value );
			}

			// Look for a validate_FIELDID_field method for special handling.
			if ( is_callable( [ $this, 'validate_' . $key . '_field' ] ) ) {
				return $this->{'validate_' . $key . '_field'}( $key, $value );
			}

			// Look for a validate_FIELDTYPE_field method.
			if ( is_callable( [ $this, 'validate_' . $type . '_field' ] ) ) {
				return $this->{'validate_' . $type . '_field'}( $key, $value );
			}

			// Fallback to text.
			return $this->validate_text_field( $key, $value );
		}

		/**
		 * Sets the POSTed data. This method can be used to set specific data, instead of taking it from the $_POST array.
		 *
		 * @param array $data Posted data.
		 * @since  1.0.0
		 */
		public function set_post_data( $data = [] ) {
			$this->data = $data;
		}

		/**
		 * Returns the POSTed data, to be used to save the settings.
		 *
		 * @since  1.0.0
		 * @return array
		 */
		public function get_post_data() {
			if ( ! empty( $this->data ) && is_array( $this->data ) ) {
				return $this->data;
			}
			return $_POST;
		}

		/**
		 * Update a single option.
		 *
		 * @param string $key Option key.
		 * @param mixed  $value Value to set.
		 * @since  1.0.0
		 * @return bool was anything saved?
		 */
		public function update_option( $key, $value = '' ) {
			if ( empty( $this->settings ) ) {
				$this->init_settings();
			}

			$this->settings[ $key ] = $value;

			return update_option( $this->get_option_key(), apply_filters( 's2_settings_api_sanitized_fields_' . $this->id, $this->settings ), 'yes' );
		}

		/**
		 * Processes and saves options.
		 * If there is an error thrown, will continue to save and validate fields, but will leave the erroring field out.
		 *
		 * @since  1.0.0
		 * @return bool was anything saved?
		 */
		public function process_admin_options() {
			$this->init_settings();

			$posted_data = $this->get_post_data();
			$posted_data = wp_unslash( $posted_data );
			$posted_data = $this->sanitize_posted_data( $posted_data );

			foreach ( $this->get_form_fields() as $key => $field ) {
				if ( 'title' !== $this->get_field_type( $field ) ) {
					try {
						$this->settings[ $key ] = $this->get_field_value( $key, $field, $posted_data );
					} catch ( Exception $e ) {
						$this->add_error( $e->getMessage() );
					}
				}
			}

			return update_option( $this->get_option_key(), apply_filters( 's2_settings_api_sanitized_fields_' . $this->id, $this->settings ), 'yes' );
		}

		/**
		 * Make sure the data is escaped correctly, etc.
		 *
		 * @param  string $value Posted Value.
		 * @since  1.0.3
		 */
		private function sanitize_posted_data( $value ) {

			if ( is_array( $value ) ) {
		
				$value = array_map( [ $this, 'sanitize_posted_data' ], $value );
		
			} elseif ( is_string( $value ) ) {
		
				$value = wp_check_invalid_utf8( $value );
				$value = wp_kses_no_null( $value );
		
			}

			return $value;
		
		}

		/**
		 * Add an error message for display in admin on save.
		 *
		 * @since  1.0.0
		 * @param string $error Error message.
		 */
		public function add_error( $error ) {
			$this->errors[] = $error;
		}

		/**
		 * Get admin error messages.
		 *
		 * @since  1.0.0
		 */
		public function get_errors() {
			return $this->errors;
		}

		/**
		 * Display admin error messages.
		 *
		 * @since  1.0.0
		 */
		public function display_errors() {
			if ( $this->get_errors() ) {
				echo '<div id="s2_errors" class="error notice is-dismissible">';
				foreach ( $this->get_errors() as $error ) {
					echo '<p>' . wp_kses_post( $error ) . '</p>';
				}
				echo '</div>';
			}
		}

		/**
		 * Initialise Settings.
		 *
		 * Store all settings in a single database entry
		 * and make sure the $settings array is either the default
		 * or the settings stored in the database.
		 *
		 * @since 1.0.0
		 * @uses get_option(), add_option()
		 */
		public function init_settings() {
			$this->settings = get_option( $this->get_option_key(), null );

			// If there are no settings defined, use defaults.
			if ( ! is_array( $this->settings ) ) {
				$form_fields    = $this->get_form_fields();
				$this->settings = array_merge( array_fill_keys( array_keys( $form_fields ), '' ), wp_list_pluck( $form_fields, 'default' ) );
			}
		}

		/**
		 * Get option from DB.
		 *
		 * Gets an option from the settings API, using defaults if necessary to prevent undefined notices.
		 *
		 * @param  string $key Option key.
		 * @param  mixed  $empty_value Value when empty.
		 * @since  1.0.0
		 * @return string The value specified for the option or a default value for the option.
		 */
		public function get_option( $key, $empty_value = null ) {
			if ( empty( $this->settings ) ) {
				$this->init_settings();
			}

			// Get option default if unset.
			if ( ! isset( $this->settings[ $key ] ) ) {
				$form_fields            = $this->get_form_fields();
				$this->settings[ $key ] = isset( $form_fields[ $key ] ) ? $this->get_field_default( $form_fields[ $key ] ) : '';
			}

			if ( ! is_null( $empty_value ) && '' === $this->settings[ $key ] ) {
				$this->settings[ $key ] = $empty_value;
			}

			return $this->settings[ $key ];
		}

		/**
		 * Prefix key for settings.
		 *
		 * @param  string $key Field key.
		 * @since  1.0.0
		 * @return string
		 */
		public function get_field_key( $key ) {
			return $this->plugin_id . $this->id . '_' . $key;
		}

		/**
		 * Generate Settings HTML.
		 *
		 * Generate the HTML for the fields on the "settings" screen.
		 *
		 * @param array $form_fields (default: []) Array of form fields.
		 * @param bool  $echo Echo or return.
		 * @return string the html for the settings
		 * @since  1.0.0
		 * @uses   method_exists()
		 */
		public function generate_settings_html( $form_fields = [], $echo = true ) {
			if ( empty( $form_fields ) ) {
				$form_fields = $this->get_form_fields();
			}

			$html = '';
			foreach ( $form_fields as $k => $v ) {
				$type = $this->get_field_type( $v );

				if ( method_exists( $this, 'generate_' . $type . '_html' ) ) {
					$html .= $this->{'generate_' . $type . '_html'}( $k, $v );
				} else {
					$html .= $this->generate_text_html( $k, $v );
				}
			}

			if ( $echo ) {
				echo $html; // WPCS: XSS ok.
			} else {
				return $html;
			}
		}

		/**
		 * Get HTML for tooltips.
		 *
		 * @param  array $data Data for the tooltip.
		 * @since  1.0.0
		 * @return string
		 */
		public function get_tooltip_html( $data ) {
			if ( true === $data['desc_tip'] ) {
				$tip = $data['description'];
			} elseif ( ! empty( $data['desc_tip'] ) ) {
				$tip = $data['desc_tip'];
			} else {
				$tip = '';
			}

			return $tip ? s2_help_tip( $tip, true ) : '';
		}

		/**
		 * Get HTML for descriptions.
		 *
		 * @param  array $data Data for the description
		 * @since  1.0.0
		 * @return string
		 */
		public function get_description_html( $data ) {
			if ( true === $data['desc_tip'] ) {
				$description = '';
			} elseif ( ! empty( $data['desc_tip'] ) ) {
				$description = $data['description'];
			} elseif ( ! empty( $data['description'] ) ) {
				$description = $data['description'];
			} else {
				$description = '';
			}

			return $description ? '<p class="description">' . wp_kses_post( $description ) . '</p>' . "\n" : '';
		}

		/**
		 * Get custom attributes.
		 *
		 * @param  array $data Field data
		 * @since  1.0.0
		 * @return string
		 */
		public function get_custom_attribute_html( $data ) {
			$custom_attributes = [];

			if ( ! empty( $data['custom_attributes'] ) && is_array( $data['custom_attributes'] ) ) {
				foreach ( $data['custom_attributes'] as $attribute => $attribute_value ) {
					$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
				}
			}

			return implode( ' ', $custom_attributes );
		}

		/**
		 * Generate Text Input HTML.
		 *
		 * @param string $key Field key.
		 * @param array  $data Field data.
		 * @since  1.0.0
		 * @return string
		 */
		public function generate_text_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );
			$defaults  = [
				'title'             => '',
				'disabled'          => false,
				'class'             => '',
				'css'               => '',
				'placeholder'       => '',
				'type'              => 'text',
				'desc_tip'          => false,
				'description'       => '',
				'custom_attributes' => [],
			];

			$data = wp_parse_args( $data, $defaults );

			ob_start();
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html( $data ); // WPCS: XSS ok. ?></label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
						<input class="regular-text <?php echo esc_attr( $data['class'] ); ?>" type="<?php echo esc_attr( $data['type'] ); ?>" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="<?php echo esc_attr( $this->get_option( $key ) ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); // WPCS: XSS ok. ?> />
						<?php echo $this->get_description_html( $data ); // WPCS: XSS ok. ?>
					</fieldset>
				</td>
			</tr>
			<?php

			return ob_get_clean();
		}

		/**
		 * Generate Price Input HTML.
		 *
		 * @param string $key Field key.
		 * @param array  $data Field data.
		 * @since  1.0.0
		 * @return string
		 */
		public function generate_price_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );
			$defaults  = [
				'title'             => '',
				'disabled'          => false,
				'class'             => '',
				'css'               => '',
				'placeholder'       => '',
				'type'              => 'text',
				'desc_tip'          => false,
				'description'       => '',
				'custom_attributes' => [],
			];

			$data = wp_parse_args( $data, $defaults );

			ob_start();
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html( $data ); // WPCS: XSS ok. ?></label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
						<input class="regular-text <?php echo esc_attr( $data['class'] ); ?>" type="number" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="<?php echo esc_attr( s2_format_localized_price( $this->get_option( $key ) ) ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); // WPCS: XSS ok. ?> />
						<?php echo $this->get_description_html( $data ); // WPCS: XSS ok. ?>
					</fieldset>
				</td>
			</tr>
			<?php

			return ob_get_clean();
		}

		/**
		 * Generate Decimal Input HTML.
		 *
		 * @param string $key Field key.
		 * @param array  $data Field data.
		 * @since  1.0.0
		 * @return string
		 */
		public function generate_decimal_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );
			$defaults  = [
				'title'             => '',
				'disabled'          => false,
				'class'             => '',
				'css'               => '',
				'placeholder'       => '',
				'type'              => 'text',
				'desc_tip'          => false,
				'description'       => '',
				'custom_attributes' => [],
			];

			$data = wp_parse_args( $data, $defaults );

			ob_start();
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html( $data ); // WPCS: XSS ok. ?></label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
						<input class="regular-text <?php echo esc_attr( $data['class'] ); ?>" type="text" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="<?php echo esc_attr( s2_format_localized_decimal( $this->get_option( $key ) ) ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); // WPCS: XSS ok. ?> />
						<?php echo $this->get_description_html( $data ); // WPCS: XSS ok. ?>
					</fieldset>
				</td>
			</tr>
			<?php

			return ob_get_clean();
		}

		/**
		 * Generate Password Input HTML.
		 *
		 * @param string $key Field key.
		 * @param array  $data Field data.
		 * @since  1.0.0
		 * @return string
		 */
		public function generate_password_html( $key, $data ) {
			$data['type'] = 'password';
			return $this->generate_text_html( $key, $data );
		}

		/**
		 * Generate Textarea HTML.
		 *
		 * @param string $key Field key.
		 * @param array  $data Field data.
		 * @since  1.0.0
		 * @return string
		 */
		public function generate_textarea_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );
			$defaults  = [
				'title'             => '',
				'disabled'          => false,
				'class'             => '',
				'css'               => '',
				'placeholder'       => '',
				'type'              => 'text',
				'desc_tip'          => false,
				'description'       => '',
				'custom_attributes' => [],
			];

			$data = wp_parse_args( $data, $defaults );

			ob_start();
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html( $data ); // WPCS: XSS ok. ?></label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
						<textarea class="regular-text <?php echo esc_attr( $data['class'] ); ?>" type="<?php echo esc_attr( $data['type'] ); ?>" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); // WPCS: XSS ok. ?> rows="3" cols="20"><?php echo esc_textarea( $this->get_option( $key ) ); ?></textarea>
						<?php echo $this->get_description_html( $data ); // WPCS: XSS ok. ?>
					</fieldset>
				</td>
			</tr>
			<?php

			return ob_get_clean();
		}

		/**
		 * Generate Checkbox HTML.
		 *
		 * @param string $key Field key.
		 * @param array  $data Field data.
		 * @since  1.0.0
		 * @return string
		 */
		public function generate_checkbox_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );
			$defaults  = [
				'title'             => '',
				'label'             => '',
				'disabled'          => false,
				'class'             => '',
				'css'               => '',
				'type'              => 'text',
				'desc_tip'          => false,
				'description'       => '',
				'custom_attributes' => [],
			];

			$data = wp_parse_args( $data, $defaults );

			if ( ! $data['label'] ) {
				$data['label'] = $data['title'];
			}

			ob_start();
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html( $data ); // WPCS: XSS ok. ?></label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
						<label for="<?php echo esc_attr( $field_key ); ?>">
						<input <?php disabled( $data['disabled'], true ); ?> class="<?php echo esc_attr( $data['class'] ); ?>" type="checkbox" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="1" <?php checked( $this->get_option( $key ), 'yes' ); ?> <?php echo $this->get_custom_attribute_html( $data ); // WPCS: XSS ok. ?> /> <?php echo wp_kses_post( $data['label'] ); ?></label><br/>
						<?php echo $this->get_description_html( $data ); // WPCS: XSS ok. ?>
					</fieldset>
				</td>
			</tr>
			<?php

			return ob_get_clean();
		}

		/**
		 * Generate Select HTML.
		 *
		 * @param string $key Field key.
		 * @param array  $data Field data.
		 * @since  1.0.0
		 * @return string
		 */
		public function generate_select_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );
			$defaults  = [
				'title'             => '',
				'disabled'          => false,
				'class'             => '',
				'css'               => '',
				'placeholder'       => '',
				'type'              => 'text',
				'desc_tip'          => false,
				'description'       => '',
				'custom_attributes' => [],
				'options'           => [],
			];

			$data = wp_parse_args( $data, $defaults );

			ob_start();
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html( $data ); // WPCS: XSS ok. ?></label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
						<select class="regular-text select <?php echo esc_attr( $data['class'] ); ?>" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); // WPCS: XSS ok. ?>>
							<?php foreach ( (array) $data['options'] as $option_key => $option_value ) : ?>
								<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( (string) $option_key, esc_attr( $this->get_option( $key ) ) ); ?>><?php echo esc_html( $option_value ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php echo $this->get_description_html( $data ); // WPCS: XSS ok. ?>
					</fieldset>
				</td>
			</tr>
			<?php

			return ob_get_clean();
		}

		/**
		 * Generate Multiselect HTML.
		 *
		 * @param string $key Field key.
		 * @param array  $data Field data.
		 * @since  1.0.0
		 * @return string
		 */
		public function generate_multiselect_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );
			$defaults  = [
				'title'             => '',
				'disabled'          => false,
				'class'             => '',
				'css'               => '',
				'placeholder'       => '',
				'type'              => 'text',
				'desc_tip'          => false,
				'description'       => '',
				'custom_attributes' => [],
				'select_buttons'    => false,
				'options'           => [],
			];

			$data  = wp_parse_args( $data, $defaults );
			$value = (array) $this->get_option( $key, [] );

			ob_start();
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html( $data ); // WPCS: XSS ok. ?></label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
						<select multiple="multiple" class="multiselect <?php echo esc_attr( $data['class'] ); ?>" name="<?php echo esc_attr( $field_key ); ?>[]" id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); // WPCS: XSS ok. ?>>
							<?php foreach ( (array) $data['options'] as $option_key => $option_value ) : ?>
								<?php if ( is_array( $option_value ) ) : ?>
									<optgroup label="<?php echo esc_attr( $option_key ); ?>">
										<?php foreach ( $option_value as $option_key_inner => $option_value_inner ) : ?>
											<option value="<?php echo esc_attr( $option_key_inner ); ?>" <?php selected( in_array( (string) $option_key_inner, $value, true ), true ); ?>><?php echo esc_html( $option_value_inner ); ?></option>
										<?php endforeach; ?>
									</optgroup>
								<?php else : ?>
									<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( in_array( (string) $option_key, $value, true ), true ); ?>><?php echo esc_html( $option_value ); ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
						</select>
						<?php echo $this->get_description_html( $data ); // WPCS: XSS ok. ?>
						<?php if ( $data['select_buttons'] ) : ?>
							<br/><a class="select_all button" href="#"><?php esc_html_e( 'Select all', 's2-plugin' ); ?></a> <a class="select_none button" href="#"><?php esc_html_e( 'Select none', 's2-plugin' ); ?></a>
						<?php endif; ?>
					</fieldset>
				</td>
			</tr>
			<?php

			return ob_get_clean();
		}

		/**
		 * Generate Title HTML.
		 *
		 * @param string $key Field key.
		 * @param array  $data Field data.
		 * @since  1.0.0
		 * @return string
		 */
		public function generate_title_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );
			$defaults  = [
				'title' => '',
				'class' => '',
			];

			$data = wp_parse_args( $data, $defaults );

			ob_start();
			?>
				</table>
				<h3 class="<?php echo esc_attr( $data['class'] ); ?>" id="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></h3>
				<?php if ( ! empty( $data['description'] ) ) : ?>
					<p><?php echo wp_kses_post( $data['description'] ); ?></p>
				<?php endif; ?>
				<table class="form-table">
			<?php

			return ob_get_clean();
		}

		/**
		 * Generate Readonly HTML.
		 *
		 * @param string $key Field key.
		 * @param array  $data Field data.
		 * @since  1.0.0
		 * @return string
		 */
		public function generate_readonly_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );
			$defaults  = [
				'title' => '',
				'class' => '',
			];

			$data = wp_parse_args( $data, $defaults );

			ob_start();
			?>
				</table>
				<span class="wp-ui-highlight">
					<input type="text" onfocus="this.select();" readonly="readonly" class="large-text code" value="<?php echo wp_kses_post( $data['description'] ); ?>">
				</span>
				<table class="form-table">
			<?php

			return ob_get_clean();
		}

		/**
		 * Validate Text Field.
		 *
		 * Make sure the data is escaped correctly, etc.
		 *
		 * @param  string $key Field key.
		 * @param  string $value Posted Value.
		 * @since  1.0.0
		 * @return string
		 */
		public function validate_text_field( $key, $value ) {
			$value = is_null( $value ) ? '' : $value;
			// return wp_kses_post( trim( stripslashes( $value ) ) );
			return $value;
		}

		/**
		 * Validate Price Field.
		 *
		 * Make sure the data is escaped correctly, etc.
		 *
		 * @param  string $key Field key.
		 * @param  string $value Posted Value
		 * @since  1.0.0.
		 * @return string
		 */
		public function validate_price_field( $key, $value ) {
			$value = is_null( $value ) ? '' : $value;
			return ( '' === $value ) ? '' : s2_format_decimal( trim( stripslashes( $value ) ) );
		}

		/**
		 * Validate Decimal Field.
		 *
		 * Make sure the data is escaped correctly, etc.
		 *
		 * @param  string $key Field key.
		 * @param  string $value Posted Value.
		 * @since  1.0.0
		 * @return string
		 */
		public function validate_decimal_field( $key, $value ) {
			$value = is_null( $value ) ? '' : $value;
			return ( '' === $value ) ? '' : s2_format_decimal( trim( stripslashes( $value ) ) );
		}

		/**
		 * Validate Password Field. No input sanitization is used to avoid corrupting passwords.
		 *
		 * @param  string $key Field key.
		 * @param  string $value Posted Value.
		 * @since  1.0.0
		 * @return string
		 */
		public function validate_password_field( $key, $value ) {
			$value = is_null( $value ) ? '' : $value;
			return trim( stripslashes( $value ) );
		}

		/**
		 * Validate Textarea Field.
		 *
		 * @param  string $key Field key.
		 * @param  string $value Posted Value.
		 * @since  1.0.0
		 * @return string
		 */
		public function validate_textarea_field( $key, $value ) {
			$value = is_null( $value ) ? '' : $value;
			return wp_kses(
				trim( stripslashes( $value ) ),
				array_merge(
					[
						'iframe' => [
							'src'   => true,
							'style' => true,
							'id'    => true,
							'class' => true,
						],
					],
					wp_kses_allowed_html( 'post' )
				)
			);
		}

		/**
		 * Validate Checkbox Field.
		 *
		 * If not set, return "no", otherwise return "yes".
		 *
		 * @param  string $key Field key.
		 * @param  string $value Posted Value.
		 * @since  1.0.0
		 * @return string
		 */
		public function validate_checkbox_field( $key, $value ) {
			return ! is_null( $value ) ? 'yes' : 'no';
		}

		/**
		 * Validate Select Field.
		 *
		 * @param  string $key Field key.
		 * @param  string $value Posted Value.
		 * @since  1.0.0
		 * @return string
		 */
		public function validate_select_field( $key, $value ) {
			$value = is_null( $value ) ? '' : $value;
			return s2_clean( stripslashes( $value ) );
		}

		/**
		 * Validate Multiselect Field.
		 *
		 * @param  string $key Field key.
		 * @param  string $value Posted Value.
		 * @since  1.0.0
		 * @return string|array
		 */
		public function validate_multiselect_field( $key, $value ) {
			return is_array( $value ) ? array_map( 's2_clean', array_map( 'stripslashes', $value ) ) : '';
		}

	}

}
