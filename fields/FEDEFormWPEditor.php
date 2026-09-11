<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'fed_e_form_wpeditor' ) ) {
	/**
	 * @param $options
	 *
	 * @return string
	 */
	function fed_e_form_wpeditor( $options ) {
		if ( function_exists( 'fed_form_wp_editor' ) ) {
			return fed_form_wp_editor( $options );
		}
		$name     = fed_get_data( 'input_meta', $options );
		$value    = fed_get_data( 'user_value', $options, '', false );
		$class    = 'form-control ' . fed_get_data( 'class_name', $options );
		$id       = isset( $options['id_name'] ) && $options['id_name'] != '' ? esc_attr( $options['id_name'] ) : '';
		$extended = isset( $options['extended'] ) ? ( is_string( $options['extended'] ) ? unserialize( $options['extended'] ) : $options['extended'] ) : array();

		$media_buttons = fed_get_data( 'settings.media_buttons', $extended );
		$quicktags     = fed_get_data( 'settings.quicktags', $extended, false );
		$textarea_rows = fed_get_data( 'settings.textarea_rows', $extended, 10 );
		$editor_height = fed_get_data( 'settings.editor_height', $extended, 30 );

		$label_id_attr = '' !== $id ? ' id="' . $id . '"' : '';
		return '<label' . $label_id_attr . '>' . fed_get_wp_editor(
			$value,
			$name, array(
				'textarea_name' => $name,
				'media_buttons' => $media_buttons,
				'textarea_rows' => $textarea_rows,
				'editor_height' => $editor_height,
				'editor_class'  => $class,
				'quicktags'     => $quicktags,
			)
		) . '</label>';
	}
}