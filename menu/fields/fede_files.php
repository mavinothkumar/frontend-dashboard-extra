<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Files.
 *
 * @param  array $options  Options.
 *
 * @return string
 */
function fed_form_files( $options ) {
	$name     = fed_get_data( 'input_meta', $options );
	$value    = fed_get_data( 'user_value', $options );
	$class    = fed_get_data( 'class_name', $options );
	$fa_icon  = fed_get_data( 'fa_icon', $options, ' fa-2x fa fa-upload ' );
	$required = fed_get_data( 'is_required', $options ) == 'true' ? 'required="required"' : null;
	$id       = fed_get_data( 'id_name', $options ) != '' ? 'id="' . esc_attr( $options['id_name'] ) . '"' : null;
	$img      = '';
	if ( ! empty( $value ) ) {
		$options['user_value'] = (int) $value;
		$img                   = fede_get_image_by_type( $options );
		$is_image              = true;
		if ( empty( $img ) ) {
			$is_image = false;
		}
	} else {
		$options['user_value'] = '';
		$is_image              = false;
	}


	return sprintf(
		'<div class="fed_upload_wrapper %s" %s>
					<div class="fed_upload_container text-center">	
						<div class="fed_upload_image_container">
							<div class="fed_upload_image_dummy %s">
								<span class="fed_upload_icon %s"></span>
							</div>
							<div class="fed_upload_image_actual %s">
								<img src="%s" />
							</div>
						</div>
							<input type="hidden" name="%s" class="fed_upload_input" value="%s"  />
					</div>
					%s
				</div>',
		$class,
		$id,
		$is_image ? 'fed_hide' : '',
		$fa_icon,
		! $is_image ? 'fed_hide' : '',
		$img,
		$name,
		$options['user_value'],
		$is_image ? '<span class="fed_remove_image">X</span>' : '<span class="fed_remove_image fed_hide">X</span>'
	);
}

/**
 * Get Image by Type.
 *
 * @param  array $options  Options.
 *
 * @return string
 */
function fede_get_image_by_type( $options ) {
	$mime_type = get_post_mime_type( $options['user_value'] );
	FED_Log::writeLog( $options['user_value']);
	FED_Log::writeLog( $mime_type);
	$default   = fed_image_mime_types();
	if ( strpos( $mime_type, 'image' ) !== false ) {
		return wp_get_attachment_image_url( $options['user_value'], array( 100, 100 ) );
	}

	if ( isset( $default[ $mime_type ] ) ) {
		return $default[ $mime_type ];
	}

	return site_url() . '/wp-includes/images/media/default.png';
}
