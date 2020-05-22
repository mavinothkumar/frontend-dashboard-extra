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
	$required = fed_get_data( 'is_required', $options ) == 'true' ? 'required="required"' : null;
	$id       = fed_get_data( 'id_name', $options ) != '' ? 'id="' . esc_attr( $options['id_name'] ) . '"' : null;

	if ( ! empty( $value ) ) {
		$options['user_value'] = (int) $value;
		$img                   = fede_get_image_by_type( $options );
		if ( empty( $img ) ) {
			$img = '<span class="fed_upload_icon fa fa-2x fa fa fa-upload"></span>';
		}
	} else {
		$options['user_value'] = '';
		$img                   = '<span class="fed_upload_icon fa fa-2x fa fa fa-upload"></span>';
	}


	return sprintf(
		'<div class="fed_upload_wrapper %s" %s>
					<div class="fed_upload_container text-center">	
						<div class="fed_upload_image_container">%s</div>
							<input type="hidden" name="%s" class="fed_upload_input" value="%s"  />
					</div>
					<span class="fed_remove_image">X</span>
				</div>',
		$class,
		$id,
		$img,
		$name,
		$options['user_value']
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
	$default   = fed_image_mime_types();
	if ( strpos( $mime_type, 'image' ) !== false ) {
		return wp_get_attachment_image( $options['user_value'], array( 100, 100 ) );
	}

	if ( isset( $default[ $mime_type ] ) ) {
		return '<img src="' . $default[ $mime_type ] . '" />';
	}

	return '<img src="' . site_url() . '/wp-includes/images/media/default.png" />';
}
