<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'fed_form_table' ) ) {
	function fed_form_table( $options ) {
		return \FED\Services\Fields\FieldFactory::render( 'table', $options );
	}
}