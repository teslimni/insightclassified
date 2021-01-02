<?php
/**
 * Admin Listings Metaboxes.
 *
 * @package ClassiPress\Admin\Metaboxes\Listings
 * @author  AppThemes
 * @since   ClassiPress 3.4
 */


add_action( 'admin_init', 'cp_setup_meta_box' );
add_filter( 'media_upload_tabs', 'cp_remove_media_from_url_tab' );
add_action( 'post_submitbox_misc_actions', 'cp_sticky_option_submit_box' );
// update Search Index
add_action( 'save_post', array( 'APP_Search_Index', 'save_post' ), 100, 2 );


/**
 * Removes unnecessary metaboxes.
 *
 * @return void
 */
function cp_setup_meta_box() {
	$remove_boxes = array( 'authordiv', 'postexcerpt', 'revisionsdiv', 'trackbacksdiv' );

	foreach ( $remove_boxes as $id ) {
		remove_meta_box( $id, APP_POST_TYPE, 'normal' );
	}

}


/**
 * Removes 'From URL' tab in media uploader, need local image for ads.
 *
 * @param array $tabs
 *
 * @return array
 */
function cp_remove_media_from_url_tab( $tabs ) {
	if ( isset( $_REQUEST['post_id'] ) ) {
		$post_type = get_post_type( $_REQUEST['post_id'] );
		if ( APP_POST_TYPE == $post_type ) {
			unset( $tabs['type_url'] );
		}
	}

	return $tabs;
}


/**
 * Adds a sticky option to the edit ad listing submit metabox.
 *
 * @return void
 */
function cp_sticky_option_submit_box() {
	global $post;

	if ( $post->post_type != APP_POST_TYPE ) {
		return;
	}

	if ( current_user_can( 'edit_others_posts' ) ) {
?>
		<div class="misc-pub-section misc-pub-section-last sticky-listing">
			<span id="sticky"><input id="sticky" name="sticky" type="checkbox" value="sticky" <?php checked( is_sticky( $post->ID ) ); ?> tabindex="4" />
			<label for="sticky" class="selectit"><?php _e( 'Featured Ad (sticky)', APP_TD ); ?></label><br /></span>
		</div>
<?php
	} elseif ( is_sticky( $post->ID ) ) {
		echo html( 'input', array( 'name' => 'sticky', 'type' => 'hidden', 'value' => 'sticky' ) );
	}
}


/**
 * Listing Info Metabox.
 */
class CP_Listing_Info_Metabox extends APP_Meta_Box {

	/**
	 * Setups metabox.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct( 'listing-info', __( 'Listing Info', APP_TD ), APP_POST_TYPE, 'normal', 'high' );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	public function form() {
		$form_fields = array(
			array(
				'title' => __( 'Reference ID', APP_TD ),
				'type' => 'text',
				'name' => 'cp_sys_ad_conf_id',
				'default' => cp_generate_id(),
				'extra' => array( 'readonly' => 'readonly' ),
			),
			array(
				'title' => __( 'Submitted from IP', APP_TD ),
				'type' => 'text',
				'name' => 'cp_sys_userIP',
				'default' => appthemes_get_ip(),
				'extra' => array( 'readonly' => 'readonly' ),
			),
			array(
				'title' => __( 'Marked as Sold', APP_TD ),
				'type' => 'select',
				'name' => 'cp_ad_sold',
				'values' => array(
					'yes' => __( 'Yes', APP_TD ),
					'no'  => __( 'No', APP_TD ),
				),
				'default' => 'no',
			),
		);

		if ( current_theme_supports( 'app-stats' ) ) {
			$form_fields[] = array(
				'title' => __( 'Views Today', APP_TD ),
				'type' => 'text',
				'name' => 'cp_daily_count',
				'sanitize' => 'absint',
				'default' => '0',
				'extra' => array( 'readonly' => 'readonly' ),
			);
			$form_fields[] = array(
				'title' => __( 'Views Total', APP_TD ),
				'type' => 'text',
				'name' => 'cp_total_count',
				'sanitize' => 'absint',
				'default' => '0',
				'extra' => array( 'readonly' => 'readonly' ),
			);
		}

		if ( cp_payments_is_enabled() ) {
			$form_fields[] = array(
				'title' => __( 'Last Payment', APP_TD ),
				'type' => 'text',
				'name' => 'cp_sys_total_ad_cost',
				'default' => '0',
				'desc' => APP_Currencies::get_current_symbol(),
				'extra' => array( 'readonly' => 'readonly' ),
			);
		}

		return $form_fields;
	}

}


/**
 * Listing Custom Forms Metabox.
 */
class CP_Listing_Custom_Forms_Metabox extends APP_Meta_Box {

	/**
	 * Setups metabox.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct( 'listing-custom-forms', __( 'Listing Details', APP_TD ), APP_POST_TYPE, 'normal', 'high' );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	public function form() {
		global $cp_options;

		$i = 0;
		$form_fields = array();
		$custom_form_fields = $this->get_custom_form_fields();
		// field types transition
		$field_types = array(
			'checkbox' => 'checkbox',
			'drop-down' => 'select',
			'radio' => 'radio',
			'text area' => 'textarea',
			'text box' => 'text',
		);

		foreach ( $custom_form_fields as $field ) {
			if ( ! isset( $field_types[ $field->field_type ] ) ) {
				continue;
			}

			// omit fields handled by other metaboxes
			if ( in_array( $field->field_name, array( 'post_title', 'post_content', 'tags_input' ) ) ) {
				continue;
			}

			$form_fields[ $i ] = array(
				'title' => translate( $field->field_label, APP_TD ),
				'type' => $field_types[ $field->field_type ],
				'name' => $field->field_name,
			);

			if ( $field->field_tooltip ) {
				$form_fields[ $i ]['tip'] = translate( $field->field_tooltip, APP_TD );
			}

			if ( $field->field_req ) {
				$form_fields[ $i ]['extra'] = array( 'class' => 'required' );
			}

			if ( in_array( $field->field_type, array( 'checkbox', 'radio', 'drop-down' ) ) ) {
				$choices = array();

				$options = cp_explode( ',', $field->field_values );
				if ( $field->field_type == 'drop-down' ) {
					$choices[''] = __( '-- Select --', APP_TD );
				}

				foreach ( $options as $option ) {
					$choices[ $option ] = translate( $option, APP_TD );
				}
				$form_fields[ $i ]['choices'] = $choices;
			}

			if ( 'text area' === $field->field_type && $cp_options->allow_html ) {
					$form_fields[ $i ]['type']     = 'custom';
					$form_fields[ $i ]['render']   = array( 'CP_Theme_Settings_General', 'render_editor' );
					$form_fields[ $i ]['sanitize'] = 'wp_kses_post';
			}

			$i++;
		}

		// display message when no form fields available
		if ( empty( $form_fields ) ) {
			$form_fields[] = array(
				'title' => '',
				'type' => 'text',
				'name' => '_blank',
				'desc' => __( 'No form fields found.', APP_TD ),
				'extra' => array( 'style' => 'display:none;' ),
			);
		}

		return $form_fields;
	}

	/**
	 * Filter data before display.
	 *
	 * @param array $form_data
	 * @param object $post
	 *
	 * @return array
	 */
	public function before_display( $form_data, $post ) {
		$custom_form_fields = $this->get_custom_form_fields();

		// pass multiple checkbox meta fields as array
		foreach ( $custom_form_fields as $field ) {
			if ( 'checkbox' != $field->field_type ) {
				continue;
			}
			$checkboxes = get_post_meta( $post->ID, $field->field_name, false );
			$form_data[ $field->field_name ] = $checkboxes;
		}

		return $form_data;
	}

	/**
	 * Filter data before save.
	 *
	 * @param array $post_data
	 * @param int $post_id
	 *
	 * @return array
	 */
	protected function before_save( $post_data, $post_id ) {
		$custom_form_fields = $this->get_custom_form_fields();

		$address_comps = array_intersect_key( $post_data, array(
			'cp_street'  => '',
			'cp_city'    => '',
			'cp_state'   => '',
			'cp_zipcode' => '',
			'cp_country' => '',
		) );

		cp_maybe_geocode_address( $post_id, $address_comps );

		foreach ( $custom_form_fields as $field ) {
			if ( 'checkbox' != $field->field_type ) {
				continue;
			}

			// delete old values
			delete_post_meta( $post_id, $field->field_name );

			// save checkboxes as multiple meta fields
			if ( isset( $post_data[ $field->field_name ] ) && is_array( $post_data[ $field->field_name ] ) ) {
				foreach ( $post_data[ $field->field_name ] as $checkbox_value ) {
					if ( ! is_array( $checkbox_value ) ) {
						add_post_meta( $post_id, $field->field_name, $checkbox_value );
					}
				}
				// remove checkboxes from $post_data
				unset( $post_data[ $field->field_name ] );
			}
		}

		return $post_data;
	}

	/**
	 * Returns custom form fields.
	 *
	 * @return array
	 */
	public function get_custom_form_fields() {

		// get the ad category ID
		$category_id = appthemes_get_custom_taxonomy( $this->get_post_id(), APP_TAX_CAT, 'term_id' );

		// get the form id based on category ID
		$form_id = ! empty( $category_id ) ? cp_get_form_id( $category_id ) : false;

		$form_fields = cp_get_custom_form_fields( $form_id );

		return $form_fields;
	}

	/**
	 * Returns current post ID.
	 *
	 * @return int
	 */
	public function get_post_id() {
		if ( ! empty( $_GET['post'] ) ) {
			return $_GET['post'];
		} else if ( ! empty( $_POST['ID'] ) ) {
			return $_POST['ID'];
		} else {
			return 0;
		}
	}

}


/**
 * Listing Pricing Information Metabox.
 */
class CP_Listing_Pricing_Metabox extends APP_Meta_Box {

	/**
	 * Setups metabox.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct( 'listing-pricing', __( 'Pricing Information', APP_TD ), APP_POST_TYPE, 'normal', 'high' );
	}

	/**
	 * Enqueues admin scripts.
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		// Minimize prod or show expanded in dev.
		$min = cp_get_enqueue_suffix();

		// Set the assets path so we don't repeat ourselves.
		$assets_path = get_template_directory_uri() . '/assets';

		wp_enqueue_style( 'timepicker', $assets_path . "/js/lib/timepicker/jquery-ui-timepicker-addon{$min}.css", array(), '1.6.3' );
		wp_enqueue_script( 'timepicker', $assets_path . "/js/lib/timepicker/jquery-ui-timepicker-addon{$min}.js", array( 'jquery-ui-core', 'jquery-ui-datepicker' ), '1.6.3' );
	}

	/**
	 * Displays extra HTML before the form.
	 *
	 * @param object $post
	 *
	 * @return void
	 */
	public function before_form( $post ) {
?>
		<script type="text/javascript">
			//<![CDATA[
			/* initialize the datepicker feature */
			jQuery(document).ready(function($) {
				$('table input.datepicker').datetimepicker({
					showSecond: true,
					timeFormat: 'hh:mm:ss',
					showOn: 'button',
					dateFormat: 'yy-mm-dd',
					minDate: 0,
					buttonText: '',
				});

				$('.ui-datepicker-trigger').addClass('dashicons-before');

				/* update expiration date when duration field has changed */
				$("input[name=cp_sys_ad_duration]").change(function(){
					$("input[name=cp_sys_expire_date]").datetimepicker( 'setDate', '+' + parseInt( $(this).val() ) );
				});
				/* set expiration date when empty and post saved */
				$("input[type=submit]").click( function(){
					var expire_date = $("input[name=cp_sys_expire_date]").val();
					if ( ! expire_date ) {
						$("input[name=cp_sys_expire_date]").datetimepicker( 'setDate', '+' + parseInt( $("input[name=cp_sys_ad_duration]").val() ) );
					}
				});

			});
			//]]>
		</script>
<?php
		echo html( 'p', __( 'These settings allow you to override the defaults that have been applied to the listings based on the package the owner chose. They will apply until the listing expires.', APP_TD ) );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	public function form() {
		global $cp_options;

		$form_fields = array(
			 array(
				'title' => __( 'Listing Duration', APP_TD ),
				'type' => 'number',
				'name' => 'cp_sys_ad_duration',
				'sanitize' => 'absint',
				'default' => $cp_options->prun_period,
				'desc' => __( 'days', APP_TD ),
				'extra' => array(
					'size' => '3'
				),
			),
			array(
				'title' => __( 'Expires on', APP_TD ),
				'type' => 'text',
				'name' => 'cp_sys_expire_date',
				'extra' => array(
					'readonly' => 'readonly',
					'class' => 'datepicker',
				),
			),
		);

		return $form_fields;
	}

}


/**
 * Listing Attachments Metabox.
 */
class CP_Listing_Media extends APP_Media_Manager_Metabox {

	/**
	 * Setups metabox.
	 *
	 * @return void
	 */
	public function __construct( $id, $title, $post_type, $context = 'normal', $priority = 'default' ) {
		add_action( 'cp_before_output_attachment', array( $this, '_output_attachment' ), 10, 2 );

		parent::__construct( $id, $title, $post_type, $context, $priority );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	public function form() {
		return array(
			array(
				'type' => 'text',
				'name' => '_cp_banner_image',
			),
		);
	}

	/**
	 * Displays content.
	 *
	 * @param object $post
	 *
	 * @return void
	 */
	public function display( $post ) {
		global $cp_options;

		$attachment_ids = get_post_meta( $post->ID, '_app_media', true );

		// check for any media uploaded before 3.5 and upgrade it to the new media manager
		if ( ! $attachment_ids ) {
			$attachments = get_posts( array( 'post_parent' => $post->ID, 'post_type' => 'attachment', 'nopaging' => true, 'fields' => 'ids', 'order' => 'ASC' ) );

			if ( count( $attachments ) ) {
				update_post_meta( $post->ID, '_app_media', $attachments );

				foreach ( $attachments as $attach_id ) {
					update_post_meta( $attach_id, '_app_attachment_type', 'file' );
				}
			}
		}

		// output the media manager browser
		cp_media_manager( $post->ID, array( 'id' => self::$id ), array(
			'file_limit'  => (int) $cp_options->num_images,
			'embed_limit' => 0,
			'file_size'   => (int) $cp_options->max_image_size * 1024,
		) );
	}

	/**
	 * Add Banner option to the Listing Attachments metabox
	 *
	 * @param int $attachment_id Attachment ID.
	 */
	public function _output_attachment( $attachment_id ) {

		if ( '_app_media-metabox' !== $this->box_id ) {
			return;
		}

		$parent = get_post( $this->get_post_id() );

		if ( apply_filters( 'cp_allow_listing_banner_image', true ) && $parent ) {
			?>
			<span class="media-attachment-option">
				<input type="radio" name="_cp_banner_image" value="<?php echo esc_attr( $attachment_id ); ?>" <?php checked( $attachment_id, $parent->_cp_banner_image ); ?>/>
				<span><?php esc_html_e( 'Set as a Banner Image', APP_TD ); ?></span>
			</span>
			<?php
		}
	}
}


/**
 * Listing Author Metabox.
 */
class CP_Listing_Author_Metabox extends APP_Meta_Box {

	/**
	 * Setups metabox.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct( 'listingauthordiv', __( 'Author', APP_TD ), APP_POST_TYPE, 'side', 'low' );
	}

	/**
	 * Displays content.
	 *
	 * @param object $post
	 *
	 * @return void
	 */
	public function display( $post ) {
		global $user_ID;
		?>
		<label class="screen-reader-text" for="post_author_override"><?php _e( 'Author', APP_TD ); ?></label>
		<?php
		wp_dropdown_users( array(
			/* 'who' => 'authors', */
			'name' => 'post_author_override',
			'selected' => empty( $post->ID ) ? $user_ID : $post->post_author,
			'include_selected' => true
		) );
	}
}

