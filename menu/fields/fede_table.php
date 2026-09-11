<?php
/**
 * Form Table Field.
 *
 * @package frontend-dashboard-extra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Table Field.
 *
 * @param  array  $options  Options.
 * @return string HTML markup for modern data table.
 */
function fed_form_table( $options ) {
	$name                = fed_get_data( 'input_meta', $options );
	$value               = fed_get_data( 'user_value', $options );
	$class               = fed_get_data( 'class_name', $options );
	$required            = ( 'true' === fed_get_data( 'is_required', $options ) || true === fed_get_data( 'is_required', $options ) || 'Enable' === fed_get_data( 'is_required', $options ) ) ? 'required="required"' : '';
	$id                  = fed_get_data( 'id_name', $options ) != '' ? 'id="' . esc_attr( $options['id_name'] ) . '"' : '';
	$extended            = fed_get_data( 'extended', $options );
	$unserialize         = $extended ? maybe_unserialize( $extended ) : array();
	if ( ! is_array( $unserialize ) ) {
		$unserialize = array();
	}

	$table_mode          = isset( $unserialize['table_mode'] ) && 'readonly' === $unserialize['table_mode'] ? 'readonly' : 'editable';
	$table_template      = isset( $unserialize['table_template'] ) && ! empty( $unserialize['table_template'] ) ? $unserialize['table_template'] : 'bordered';
	$disable_user_access = isset( $unserialize['disable_user_access'] ) ? $unserialize['disable_user_access'] : null;

	$readonly            = '';
	$disabled            = '';

	if ( 'Disable' === $disable_user_access && ! fed_is_admin() ) {
		$name     = '';
		$readonly = 'readonly="readonly"';
		$disabled = 'disabled="disabled"';
	}

	if ( ! empty( $value ) ) {
		$value = maybe_unserialize( $value );
	} else {
		$value = array();
	}

	$table = fed_get_data( 'input_value', $options );
	$table = explode( '|', (string) $table );

	// Table Header Columns
	$table_header = $table[0] ?? '';
	$table_header = '' !== $table_header ? explode( ',', $table_header ) : array();

	// Fallback if no columns configured
	if ( empty( $table_header ) ) {
		$table_header = array( __( 'Column 1', 'frontend-dashboard' ), __( 'Column 2', 'frontend-dashboard' ) );
	}

	// Table Default Cell Values
	$default_cell_values = isset( $table[2] ) ? json_decode( $table[2], true ) : array();
	if ( ! is_array( $default_cell_values ) ) {
		$default_cell_values = array();
	}

	// Table Rows Count
	$table_rows = isset( $table[1] ) ? (int) $table[1] : 0;
	if ( $table_rows <= 0 ) {
		$table_rows = 3;
	}

	// Determine styling classes based on selected template
	$wrapper_classes = 'fed_table_wrapper w-full block overflow-hidden rounded-2xl bg-white shadow-2xs ' . esc_attr( $class );
	$th_classes      = 'px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider select-none whitespace-nowrap';
	$tr_classes      = 'transition-colors';
	$td_classes      = 'p-2.5 sm:p-3 align-middle';

	switch ( $table_template ) {
		case 'borderless':
			$wrapper_classes = 'fed_table_wrapper w-full block overflow-hidden rounded-2xl bg-white shadow-none ' . esc_attr( $class );
			$th_classes     .= ' text-slate-500 bg-transparent border-b-2 border-slate-200';
			$tr_classes     .= ' hover:bg-slate-50/50 border-b border-slate-100 last:border-0';
			$td_classes      = 'px-4 py-3 align-middle';
			break;

		case 'striped':
			$wrapper_classes .= ' border border-slate-200/90';
			$th_classes     .= ' text-white bg-slate-800 border-b border-slate-700 border-r border-slate-700/60 last:border-r-0';
			$tr_classes     .= ' hover:bg-indigo-50/30 even:bg-slate-50/70 odd:bg-white border-b border-slate-100 last:border-0';
			$td_classes     .= ' border-r border-slate-100 last:border-r-0';
			break;

		case 'compact':
			$wrapper_classes = 'fed_table_wrapper w-full block overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xs ' . esc_attr( $class );
			$th_classes      = 'px-3 py-2 text-left text-[11px] font-bold text-slate-600 uppercase tracking-wider select-none whitespace-nowrap bg-slate-100/90 border-b border-slate-200 border-r border-slate-200/60 last:border-r-0';
			$tr_classes     .= ' hover:bg-slate-50/80 border-b border-slate-100 last:border-0';
			$td_classes      = 'p-2 border-r border-slate-100 last:border-r-0 align-middle';
			break;

		case 'bordered':
		default:
			$wrapper_classes .= ' border border-slate-200/90';
			$th_classes     .= ' text-slate-700 bg-slate-50/90 border-b border-slate-200/90 border-r border-slate-200/60 last:border-r-0';
			$tr_classes     .= ' hover:bg-indigo-50/20 even:bg-slate-50/40 border-b border-slate-100 last:border-0';
			$td_classes     .= ' border-r border-slate-100 last:border-r-0';
			break;
	}

	$th = '';
	foreach ( $table_header as $idx => $header ) {
		$header_text = trim( $header );
		$th .= '<th class="' . esc_attr( $th_classes ) . '">';
		$th .= '<div class="flex items-center gap-2">';
		if ( 'striped' !== $table_template ) {
			$th .= '<span class="w-1.5 h-1.5 rounded-full bg-indigo-500 shrink-0"></span>';
		}
		$th .= '<span class="truncate">' . esc_html( $header_text ) . '</span>';
		$th .= '</div>';
		$th .= '</th>';
	}

	$td = '';
	for ( $row = 0; $row < $table_rows; $row++ ) {
		$td .= '<tr class="' . esc_attr( $tr_classes ) . '">';
		foreach ( $table_header as $key => $header ) {
			$header_text  = trim( $header );
			$user_val_key = 'row_' . $row . '_' . $key;

			if ( 'readonly' === $table_mode ) {
				// Read-only static table presentation
				$cell_value = isset( $default_cell_values[ $user_val_key ] ) ? $default_cell_values[ $user_val_key ] : '';
				if ( '' === $cell_value && isset( $value[ $user_val_key ] ) ) {
					$cell_value = $value[ $user_val_key ];
				}

				$text_size = ( 'compact' === $table_template ) ? 'text-[11px]' : 'text-xs';
				$td .= '<td class="' . esc_attr( $td_classes ) . '">';
				$td .= '<div class="px-2 py-1 ' . esc_attr( $text_size ) . ' font-medium text-slate-800 min-h-[30px] flex items-center">';
				if ( '' !== $cell_value ) {
					$td .= esc_html( $cell_value );
				} else {
					$td .= '<span class="text-slate-300 font-normal italic">—</span>';
				}
				$td .= '</div>';
				$td .= '</td>';
			} else {
				// User input editable mode
				if ( isset( $value[ $user_val_key ] ) && '' !== $value[ $user_val_key ] ) {
					$user_value = $value[ $user_val_key ];
				} else {
					$user_value = isset( $default_cell_values[ $user_val_key ] ) ? $default_cell_values[ $user_val_key ] : '';
				}
				$_name = '' !== $name ? $name . '[' . $user_val_key . ']' : '';

				$input_padding = ( 'compact' === $table_template ) ? 'px-2.5 py-1.5 text-[11px]' : 'px-3.5 py-2.5 text-xs';
				$td .= '<td class="' . esc_attr( $td_classes ) . '">';
				$td .= '<input type="text" ' . $readonly . ' ' . $disabled . ' name="' . esc_attr( $_name ) . '" value="' . esc_attr( $user_value ) . '" placeholder="' . esc_attr( $header_text ) . '" class="w-full font-medium text-slate-800 bg-white hover:bg-slate-50/80 focus:bg-white border border-slate-200/90 focus:border-indigo-500 rounded-xl ' . esc_attr( $input_padding ) . ' outline-none focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder:text-slate-300" ' . $required . ' />';
				$td .= '</td>';
			}
		}
		$td .= '</tr>';
	}

	ob_start();
	?>
	<div class="<?php echo esc_attr( $wrapper_classes ); ?>" <?php echo $id; ?>>
		<div class="overflow-x-auto w-full">
			<table class="w-full text-left text-xs border-collapse m-0">
				<thead>
					<tr>
						<?php echo $th; ?>
					</tr>
				</thead>
				<tbody class="divide-y divide-slate-100">
					<?php echo $td; ?>
				</tbody>
			</table>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
