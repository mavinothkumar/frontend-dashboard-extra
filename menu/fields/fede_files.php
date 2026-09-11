<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'fed_form_files' ) ) {
	function fed_form_files( $options ) {
		return \FED\Services\Fields\FieldFactory::render( 'file', $options );
	}
}