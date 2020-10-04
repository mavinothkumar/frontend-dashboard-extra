<?php
/**
 * FED Label
 *
 * @package frontend-dashboard-extra
 */

function fed_form_label( $options ) {
	$input_value = fed_get_data( 'input_value', $options );
	$class       = fed_get_data( 'class_name', $options );
	$id          = fed_get_data( 'id_name', $options ) != '' ? 'id="' . esc_attr( $options['id_name'] ) . '"' : null;

	return sprintf(
		'<div class="%s" id="%s" >%s</div>',
		$class,
		$id,
		wp_kses_post( $input_value )
	);
}
