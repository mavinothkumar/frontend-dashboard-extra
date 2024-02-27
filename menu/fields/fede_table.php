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
	$name     = fed_get_data( 'input_meta', $options );
	$value    = fed_get_data( 'user_value', $options );
	$class    = fed_get_data( 'class_name', $options );
	$fa_icon  = fed_get_data( 'fa_icon', $options, ' fa-2x fa fa-upload ' );
	$required = fed_get_data( 'is_required', $options ) == 'true' ? 'required="required"' : null;
	$id       = fed_get_data( 'id_name', $options ) != '' ? 'id="' . esc_attr( $options['id_name'] ) . '"' : null;

	if ( ! empty( $value ) ) {
		$value = maybe_unserialize( $value );
	} else {
		$value = array();
	}

	//Table Header
	$table_header = fed_get_data( 'input_value', $options );
	$table_header = '' !== $table_header ? implode( ',', $table_header ) : array();
	$th           = '';
	foreach ( $table_header as $header ) {
		$th .= '<th>' . $header . '</th>';
	}

	//Table Rows
	$table_rows = fed_get_data( 'input_row', $options ) != '' ? (int) $options['input_row'] : 0;
	$td         = '';
	for ( $row = 0; $row <= $table_rows; $row ++ ) {
		$td .= '<tr>';
		foreach ( $table_header as $key => $header ) {
			$td .= '<td><input type="text" name="' . $name . '[row_' . $row . '_' . $key . ']" value="" class="form-control" /></td>';
		}
		$td .= '</tr>';
	}

	return sprintf(
		'<div class="fed_table_wrapper %s" %s>
					<table class="table">
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
