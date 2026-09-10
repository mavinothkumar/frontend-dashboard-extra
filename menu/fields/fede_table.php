<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Files.
 *
 * @param  array  $options  Options.
 *
 * @return string
 */
function fed_form_table( $options ) {
	$name                = fed_get_data( 'input_meta', $options );
	$value               = fed_get_data( 'user_value', $options );
	$class               = fed_get_data( 'class_name', $options );
	$fa_icon             = fed_get_data( 'fa_icon', $options, ' fa-2x fa fa-upload ' );
	$required            = fed_get_data( 'is_required', $options ) == 'true' ? 'required="required"' : null;
	$id                  = fed_get_data( 'id_name', $options ) != '' ? 'id="' . esc_attr( $options['id_name'] ) . '"' : null;
	$extended            = fed_get_data( 'extended', $options );
	$unseralize          = $extended ? maybe_unserialize( $extended ) : null;
	$readonly            = '';
	$disabled            = '';
	$disable_user_access = $unseralize ? fed_get_data( 'disable_user_access', $unseralize ) : null;
	if ( 'Disable' === $disable_user_access && ! fed_is_admin() ) {
		$name     = '';
		$readonly = ' readonly=readonly ';
		$disabled = ' disabled=disabled ';
	}

	if ( ! empty( $value ) ) {
		$value = maybe_unserialize( $value );
	} else {
		$value = array();
	}

	$table = fed_get_data( 'input_value', $options );
	$table = explode( '|', $table );
	//Table Header
	$table_header = $table[0] ?? '';
	$table_header = '' !== $table_header ? explode( ',', $table_header ) : array();
	$th           = '';
	foreach ( $table_header as $header ) {
		$th .= '<th>' . $header . '</th>';
	}

	//Table Default Cell Values
	$default_cell_values = isset( $table[2] ) ? json_decode( $table[2], true ) : array();
	if ( ! is_array( $default_cell_values ) ) {
		$default_cell_values = array();
	}

	//Table Rows
	$table_rows = isset( $table[1] ) ? (int) $table[1] : 0;
	$td         = '';
	for ( $row = 0; $row < $table_rows; $row ++ ) {
		$td .= '<tr>';
		foreach ( $table_header as $key => $header ) {
			// Fallback to default cell values set by admin if user hasn't provided a value yet
			$user_val_key = 'row_' . $row . '_' . $key;
			if ( isset( $value[ $user_val_key ] ) && '' !== $value[ $user_val_key ] ) {
				$user_value = $value[ $user_val_key ];
			} else {
				$user_value = isset( $default_cell_values[ $user_val_key ] ) ? $default_cell_values[ $user_val_key ] : '';
			}
			$_name       = '' !== $name ? $name . '[' . $user_val_key . ']' : '';
			$td         .= '<td><input type="text" '.$readonly. $disabled .' name="' . $_name . '" value="' . esc_attr( $user_value ) . '" class="form-control" /></td>';
		}
		$td .= '</tr>';
	}

	return sprintf(
		'<div class="fed_table_wrapper %s" %s>
					<table class="table table-bordered">
						<thead>
							<tr>
							%s
							</tr>	
						</thead>
						<tbody>
							%s
						</tbody>
					</table>
				</div>',
		$class,
		$id,
		$th,
		$td
	);
}
