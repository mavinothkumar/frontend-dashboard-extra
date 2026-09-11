<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'fed_form_label' ) ) {
	function fed_form_label( $options ) {
		return \FED\Services\Fields\FieldFactory::render( 'label', $options );
	}
}