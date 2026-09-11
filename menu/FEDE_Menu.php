<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FEDE_Menu' ) ) {
	/**
	 * Class FEDE_Menu
	 */
	class FEDE_Menu {

		/**
		 * FEDE_Menu constructor.
		 */
		public function __construct() {
			if ( function_exists( 'fed_form_date' ) ) {
				// Core natively handles Extra fields.
				return;
			}
			add_action( 'fed_admin_input_item_options', array( $this, 'fed_extra_admin_input_item_options' ) );
			add_action(
				'fed_admin_input_fields_container_extra', array(
				$this,
				'fed_extra_admin_input_fields_container_extra_date',
			), 10, 3
			);
			add_action(
				'fed_admin_input_fields_container_extra', array(
				$this,
				'fed_extra_admin_input_fields_container_extra_wp_editor',
			), 13, 3
			);
			add_action(
				'fed_admin_input_fields_container_extra', array(
				$this,
				'fed_extra_admin_input_fields_container_extra_file',
			), 12, 3
			);
			add_action(
				'fed_admin_input_fields_container_extra', array(
				$this,
				'fed_extra_admin_input_fields_container_extra_color',
			), 12, 3
			);
			add_action(
				'fed_admin_input_fields_container_extra', array(
				$this,
				'fed_extra_admin_input_fields_container_extra_label',
			), 12, 3
			);
            add_action(
				'fed_admin_input_fields_container_extra', array(
				$this,
				'fed_extra_admin_input_fields_container_extra_table',
			), 12, 3
			);

			add_filter( 'fed_custom_input_fields', array( $this, 'fed_extra_custom_input_fields' ), 10, 2 );
		}

		/**
		 * @param $input
		 * @param $values
		 * @param $attr
		 *
		 * @return string
		 */
		public function fed_extra_custom_input_fields( $input, $attr ) {
			switch ( $attr['input_type'] ) {
				case 'date':
					$extended = array();
					if ( isset( $attr['extended'] ) ) {
						$extended = $attr['extended'];
						if ( is_string( $extended ) ) {
							$extended = maybe_unserialize( $extended );
						}
					}

					$dateFormat    = isset( $extended['date_format'] ) && ! empty( $extended['date_format'] ) ? esc_attr( $extended['date_format'] ) : 'd-m-Y';
					$mode          = isset( $extended['date_mode'] ) && ! empty( $extended['date_mode'] ) ? esc_attr( $extended['date_mode'] ) : 'single';
					$enableTime    = isset( $extended['enable_time'] ) && 'true' === (string) $extended['enable_time'] ? 'true' : 'false';
					$time_24hr     = isset( $extended['time_24hr'] ) && 'true' === (string) $extended['time_24hr'] ? 'true' : 'false';
					$enableSeconds = isset( $extended['enable_seconds'] ) && 'true' === (string) $extended['enable_seconds'] ? 'true' : 'false';
					$minDate       = isset( $extended['min_date'] ) && ! empty( $extended['min_date'] ) ? esc_attr( $extended['min_date'] ) : '';
					$maxDate       = isset( $extended['max_date'] ) && ! empty( $extended['max_date'] ) ? esc_attr( $extended['max_date'] ) : '';

					if ( 'true' === $enableTime ) {
						if ( 'true' === $time_24hr ) {
							$timePart = 'true' === $enableSeconds ? ' H:i:S' : ' H:i';
						} else {
							$timePart = 'true' === $enableSeconds ? ' h:i:S K' : ' h:i K';
						}
						$fullFormat = $dateFormat . $timePart;
					} else {
						$fullFormat = $dateFormat;
					}

					$placeholder = isset( $attr['placeholder'] ) && ! empty( $attr['placeholder'] ) ? esc_attr( $attr['placeholder'] ) : $fullFormat;

					$extra_attrs = '';
					if ( '' !== $minDate ) {
						$extra_attrs .= ' data-min-date="' . $minDate . '"';
					}
					if ( '' !== $maxDate ) {
						$extra_attrs .= ' data-max-date="' . $maxDate . '"';
					}
					if ( 'true' === $enableSeconds ) {
						$extra_attrs .= ' data-enable-seconds="true"';
					}

					$input .= '<input type="text" ' . fed_get_data( 'is_required', $attr ) . ' data-date-format="' . esc_attr( $fullFormat ) . '" data-alt-format="' . esc_attr( $fullFormat ) . '" data-alt-input="true" data-mode="' . esc_attr( $mode ) . '" placeholder="' . esc_attr( $placeholder ) . '" data-enable-time="' . esc_attr( $enableTime ) . '" data-time_24hr="' . esc_attr( $time_24hr ) . '"' . $extra_attrs . ' name="' . esc_attr( $attr['input_meta'] ) . '" class="flatpickr ' . esc_attr( fed_get_data( 'class_name', $attr ) ) . '" id="' . esc_attr( fed_get_data( 'id_name', $attr ) ) . '" value="' . esc_attr( fed_get_data( 'user_value', $attr ) ) . '" >';
					break;

				case 'wp_editor':
					$input .= fed_e_form_wpeditor( $attr );
					break;

				case 'color':
					$input .= fed_form_color( $attr );
					break;

				case 'file':
					$input .= fed_form_files( $attr );
					break;
				case 'label':
					$input .= fed_form_label( $attr );
					break;
                case 'table':
					$input .= fed_form_table( $attr );
					break;
			}

			return $input;

		}

		/**
		 * Append Dropdown Item
		 *
		 * @param  array $items
		 *
		 * @return array
		 */
		public function fed_extra_admin_input_item_options( $items ) {
			return array_merge(
				$items, array(
					'date'      => array(
						'name'  => 'Date',
						'image' => plugins_url( 'assets/images/inputs/date.png', BC_FED_EXTRA_PLUGIN ),
					),
					'file'      => array(
						'name'  => 'File',
						'image' => plugins_url( 'assets/images/inputs/file.png', BC_FED_EXTRA_PLUGIN ),
					),
					'color'     => array(
						'name'  => 'Color',
						'image' => plugins_url( 'assets/images/inputs/color.png', BC_FED_EXTRA_PLUGIN ),
					),
					'wp_editor' => array(
						'name'  => 'WP Editor(Beta)',
						'image' => plugins_url( 'assets/images/inputs/wp_editor.png', BC_FED_EXTRA_PLUGIN ),
					),
					'label'     => array(
						'name'  => 'Label',
						'image' => plugins_url( 'assets/images/inputs/label.png', BC_FED_EXTRA_PLUGIN ),
					),
                    'table'     => array(
						'name'  => 'Table',
						'image' => plugins_url( 'assets/images/inputs/table.png', BC_FED_EXTRA_PLUGIN ),
					),
				)
			);
		}

		/**
		 * Date Field
		 *
		 * @param  array  $row
		 * @param  string $action
		 * @param  array  $menu_options
		 */
		public function fed_extra_admin_input_fields_container_extra_date( $row, $action, $menu_options ) {
			$is_active = ( isset( $row['input_type'] ) && 'date' === $row['input_type'] );
			$extended  = isset( $row['extended'] ) ? ( is_string( $row['extended'] ) ? maybe_unserialize( $row['extended'] ) : $row['extended'] ) : array();
			if ( ! is_array( $extended ) ) {
				$extended = array();
			}
			?>
			<div class="fed_input_type_container fed_input_date_container space-y-7 <?php echo $is_active ? '' : 'hide hidden'; ?>" data-field-type="date">
				<form method="post"
						class="fed_admin_menu fed_ajax space-y-7"
						action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_up_form' ) ); ?>">

					<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
					<?php echo fed_loader(); ?>

					<!-- Card: Basic Field Settings -->
					<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6 sm:space-y-7">
						<div class="flex items-center gap-3.5 pb-5 border-b border-slate-100">
							<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
								<i class="fas fa-calendar-alt"></i>
							</div>
							<div>
								<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php esc_html_e( 'Date & Time Field', 'frontend-dashboard' ); ?></h3>
								<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Date & time picker with customizable format, mode, and time options.', 'frontend-dashboard' ); ?></p>
							</div>
						</div>

						<div class="space-y-5">
							<?php fed_get_admin_up_label_input_order( $row ); ?>
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
								<?php fed_get_admin_up_input_meta( $row ); ?>
								<?php fed_get_placeholder_field( $row ); ?>
							</div>
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
								<?php fed_get_class_field( $row ); ?>
								<?php fed_get_id_field( $row ); ?>
							</div>

							<!-- Date & Time Options -->
							<div class="pt-4 border-t border-slate-100 space-y-3">
								<h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2"><?php esc_html_e( 'Date & Time Configurations', 'frontend-dashboard' ); ?></h4>
								<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
									<div class="space-y-1.5">
										<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Date Format', 'frontend-dashboard' ); ?></label>
										<?php
										echo fed_input_box(
											'date_format',
											array(
												'name'    => 'extended[date_format]',
												'value'   => isset( $extended['date_format'] ) ? $extended['date_format'] : '',
												'options' => fed_get_date_formats(),
											),
											'select'
										);
										?>
									</div>
									<div class="space-y-1.5">
										<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Enable Time', 'frontend-dashboard' ); ?></label>
										<?php
										echo fed_input_box(
											'enable_time',
											array(
												'name'    => 'extended[enable_time]',
												'value'   => isset( $extended['enable_time'] ) ? $extended['enable_time'] : '',
												'options' => array(
													'false' => __( 'False', 'frontend-dashboard' ),
													'true'  => __( 'True', 'frontend-dashboard' ),
												),
											),
											'select'
										);
										?>
									</div>
									<div class="space-y-1.5">
										<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Date Mode', 'frontend-dashboard' ); ?></label>
										<?php
										echo fed_input_box(
											'date_mode',
											array(
												'name'    => 'extended[date_mode]',
												'value'   => isset( $extended['date_mode'] ) ? $extended['date_mode'] : '',
												'options' => fed_get_date_mode(),
											),
											'select'
										);
										?>
									</div>
									<div class="space-y-1.5">
										<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Time Format', 'frontend-dashboard' ); ?></label>
										<?php
										echo fed_input_box(
											'time_24hr',
											array(
												'name'    => 'extended[time_24hr]',
												'value'   => isset( $extended['time_24hr'] ) ? $extended['time_24hr'] : '',
												'options' => array(
													'true'  => __( '24 Hours', 'frontend-dashboard' ),
													'false' => __( '12 Hours (AM/PM)', 'frontend-dashboard' ),
												),
											),
											'select'
										);
										?>
									</div>
									<div class="space-y-1.5">
										<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Enable Seconds', 'frontend-dashboard' ); ?></label>
										<?php
										echo fed_input_box(
											'enable_seconds',
											array(
												'name'    => 'extended[enable_seconds]',
												'value'   => isset( $extended['enable_seconds'] ) ? $extended['enable_seconds'] : '',
												'options' => array(
													'false' => __( 'False', 'frontend-dashboard' ),
													'true'  => __( 'True', 'frontend-dashboard' ),
												),
											),
											'select'
										);
										?>
									</div>
									<div class="space-y-1.5">
										<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Min Date', 'frontend-dashboard' ); ?></label>
										<input
											type="text"
											name="extended[min_date]"
											value="<?php echo esc_attr( isset( $extended['min_date'] ) ? $extended['min_date'] : '' ); ?>"
											placeholder="<?php esc_attr_e( 'e.g. today or 2026-01-01', 'frontend-dashboard' ); ?>"
											class="w-full text-xs text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all"
											style="min-height:38px;"
										/>
									</div>
									<div class="space-y-1.5">
										<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Max Date', 'frontend-dashboard' ); ?></label>
										<input
											type="text"
											name="extended[max_date]"
											value="<?php echo esc_attr( isset( $extended['max_date'] ) ? $extended['max_date'] : '' ); ?>"
											placeholder="<?php esc_attr_e( 'e.g. today or 2026-12-31', 'frontend-dashboard' ); ?>"
											class="w-full text-xs text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all"
											style="min-height:38px;"
										/>
									</div>
								</div>
							</div>
						</div>
					</div>

					<?php
					fed_get_admin_up_display_permission( $row, $action );
					fed_get_admin_up_role_based( $row, $action, $menu_options );
					fed_get_input_type_and_submit_btn( 'date', $action );
					?>
				</form>
			</div>
			<?php
		}

		/**
		 * WP Editor Field
		 *
		 * @param array  $row
		 * @param string $action
		 * @param array  $menu_options
		 */
		public function fed_extra_admin_input_fields_container_extra_wp_editor( $row, $action, $menu_options ) {
			$is_active = ( isset( $row['input_type'] ) && 'wp_editor' === $row['input_type'] );
			?>
			<div class="fed_input_type_container fed_input_wp_editor_container space-y-7 <?php echo $is_active ? '' : 'hide hidden'; ?>" data-field-type="wp_editor">
				<form method="post"
						class="fed_admin_menu fed_ajax space-y-7"
						action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_up_form' ) ); ?>">

					<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
					<?php echo fed_loader(); ?>

					<!-- Card: Basic Field Settings -->
					<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6 sm:space-y-7">
						<div class="flex items-center gap-3.5 pb-5 border-b border-slate-100">
							<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
								<i class="fas fa-edit"></i>
							</div>
							<div>
								<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php esc_html_e( 'WP Editor (Rich Text)', 'frontend-dashboard' ); ?></h3>
								<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'WordPress visual and HTML rich text WYSIWYG editor.', 'frontend-dashboard' ); ?></p>
							</div>
						</div>

						<div class="space-y-5">
							<?php fed_get_admin_up_label_input_order( $row ); ?>
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
								<?php fed_get_admin_up_input_meta( $row ); ?>
								<?php fed_get_placeholder_field( $row ); ?>
							</div>
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
								<?php fed_get_class_field( $row ); ?>
								<?php fed_get_id_field( $row ); ?>
							</div>

							<!-- Editor Configurations -->
							<div class="pt-4 border-t border-slate-100 space-y-3">
								<h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2"><?php esc_html_e( 'Editor Configurations', 'frontend-dashboard' ); ?></h4>
								<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
									<div class="p-4 bg-slate-50/80 border border-slate-200/80 rounded-2xl flex items-center justify-between gap-3">
										<div>
											<span class="block text-xs font-bold text-slate-800"><?php esc_html_e( 'Media Upload', 'frontend-dashboard' ); ?></span>
											<span class="text-[11px] text-slate-400"><?php esc_html_e( 'Add media button', 'frontend-dashboard' ); ?></span>
										</div>
										<?php
										echo fed_input_box(
											'extended[settings][media_buttons]',
											array(
												'default_value' => 'true',
												'value'         => fed_get_data( 'extended.settings.media_buttons', $row, 'true' ),
											),
											'checkbox'
										);
										?>
									</div>

									<div class="p-4 bg-slate-50/80 border border-slate-200/80 rounded-2xl flex items-center justify-between gap-3">
										<div>
											<span class="block text-xs font-bold text-slate-800"><?php esc_html_e( 'Quicktags', 'frontend-dashboard' ); ?></span>
											<span class="text-[11px] text-slate-400"><?php esc_html_e( 'HTML formatting tags', 'frontend-dashboard' ); ?></span>
										</div>
										<?php
										echo fed_input_box(
											'extended[settings][quicktags]',
											array(
												'default_value' => 'true',
												'value'         => fed_get_data( 'extended.settings.quicktags', $row, 'true' ),
											),
											'checkbox'
										);
										?>
									</div>

									<div class="space-y-1.5">
										<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Textarea Rows', 'frontend-dashboard' ); ?></label>
										<?php
										echo fed_input_box(
											'extended[settings][textarea_rows]',
											array(
												'value' => fed_get_data( 'extended.settings.textarea_rows', $row, 10 ),
											),
											'number'
										);
										?>
									</div>

									<div class="space-y-1.5">
										<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Editor Height (px)', 'frontend-dashboard' ); ?></label>
										<?php
										echo fed_input_box(
											'extended[settings][editor_height]',
											array(
												'value' => fed_get_data( 'extended.settings.editor_height', $row, 250 ),
											),
											'number'
										);
										?>
									</div>
								</div>
							</div>
						</div>
					</div>

					<?php
					fed_get_admin_up_display_permission( $row, $action );
					fed_get_admin_up_role_based( $row, $action, $menu_options );
					fed_get_input_type_and_submit_btn( 'wp_editor', $action );
					?>
				</form>
			</div>
			<?php
		}

		/**
		 * Table Field
		 *
		 * @param array  $row
		 * @param string $action
		 * @param array  $menu_options
		 */
		public function fed_extra_admin_input_fields_container_extra_table( $row, $action, $menu_options ) {
			$is_active    = ( isset( $row['input_type'] ) && 'table' === $row['input_type'] );
			$table_val    = isset( $row['input_value'] ) ? $row['input_value'] : 'Column 1,Column 2,Column 3|2';
			$parts        = explode( '|', $table_val );
			$col_string   = isset( $parts[0] ) ? trim( $parts[0] ) : 'Column 1,Column 2,Column 3';
			$default_rows = isset( $parts[1] ) ? max( 1, (int) $parts[1] ) : 2;
			$columns      = array_values( array_filter( array_map( 'trim', explode( ',', $col_string ) ) ) );
			if ( empty( $columns ) ) {
				$columns = array( 'Column 1', 'Column 2', 'Column 3' );
			}

			$extended = isset( $row['extended'] ) ? ( is_string( $row['extended'] ) ? maybe_unserialize( $row['extended'] ) : $row['extended'] ) : array();
			if ( ! is_array( $extended ) ) {
				$extended = array();
			}
			$table_mode     = isset( $extended['table_mode'] ) && 'readonly' === $extended['table_mode'] ? 'readonly' : 'editable';
			$table_template = isset( $extended['table_template'] ) && ! empty( $extended['table_template'] ) ? $extended['table_template'] : 'bordered';
			?>
			<div class="fed_input_type_container fed_input_table_container space-y-7 <?php echo $is_active ? '' : 'hide hidden'; ?>" data-field-type="table">
				<form method="post"
					  class="fed_admin_menu fed_ajax space-y-7"
					  action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_up_form' ) ); ?>">

					<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
					<?php echo fed_loader(); ?>

					<!-- Hidden serialised value that the save handler reads -->
					<input type="hidden" name="input_value" id="fed_table_schema_hidden" value="<?php echo esc_attr( $table_val ); ?>" />

					<!-- Card: Basic Field Settings -->
					<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6 sm:space-y-7">
						<div class="flex items-center gap-3.5 pb-5 border-b border-slate-100">
							<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
								<i class="fas fa-table"></i>
							</div>
							<div>
								<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php esc_html_e( 'Table Grid Field', 'frontend-dashboard' ); ?></h3>
								<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Dynamic tabular data field with support for user data entry or static read-only presentation.', 'frontend-dashboard' ); ?></p>
							</div>
						</div>

						<div class="space-y-5">
							<?php fed_get_admin_up_label_input_order( $row ); ?>
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
								<?php fed_get_admin_up_input_meta( $row ); ?>
								<?php fed_get_placeholder_field( $row ); ?>
							</div>
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
								<?php fed_get_class_field( $row ); ?>
								<?php fed_get_id_field( $row ); ?>
							</div>

							<!-- Table Behavior & Template Configurations -->
							<div class="pt-5 border-t border-slate-100 space-y-4">
								<h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-2">
									<i class="fas fa-sliders-h text-indigo-500"></i>
									<?php esc_html_e( 'Table Behavior & Template', 'frontend-dashboard' ); ?>
								</h4>
								<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
									<div class="space-y-1.5">
										<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Table Mode', 'frontend-dashboard' ); ?></label>
										<select name="extended[table_mode]" id="fed_table_mode_select" class="w-full text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all" style="min-height:38px;">
											<option value="editable" <?php selected( $table_mode, 'editable' ); ?>><?php esc_html_e( 'User Input (Editable by User)', 'frontend-dashboard' ); ?></option>
											<option value="readonly" <?php selected( $table_mode, 'readonly' ); ?>><?php esc_html_e( 'Read-Only (Display Table Data)', 'frontend-dashboard' ); ?></option>
										</select>
										<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Read-only mode displays fixed data without inputs. User Input mode saves responses per user.', 'frontend-dashboard' ); ?></p>
									</div>
									<div class="space-y-1.5">
										<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Table Template / Style', 'frontend-dashboard' ); ?></label>
										<select name="extended[table_template]" id="fed_table_template_select" class="w-full text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all" style="min-height:38px;">
											<option value="bordered" <?php selected( $table_template, 'bordered' ); ?>><?php esc_html_e( 'Bordered Grid (Standard with Borders)', 'frontend-dashboard' ); ?></option>
											<option value="borderless" <?php selected( $table_template, 'borderless' ); ?>><?php esc_html_e( 'Clean Borderless (Minimal Dividers)', 'frontend-dashboard' ); ?></option>
											<option value="striped" <?php selected( $table_template, 'striped' ); ?>><?php esc_html_e( 'Zebra Striped (Dark Header & Tinted Rows)', 'frontend-dashboard' ); ?></option>
											<option value="compact" <?php selected( $table_template, 'compact' ); ?>><?php esc_html_e( 'Modern Compact (Tight Spacing)', 'frontend-dashboard' ); ?></option>
										</select>
										<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Choose visual layout and styling template for the frontend table.', 'frontend-dashboard' ); ?></p>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Card: Visual Column Builder -->
					<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6">
						<div class="flex items-center gap-3.5 pb-4 border-b border-slate-100">
							<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
								<i class="fas fa-columns"></i>
							</div>
							<div>
								<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php esc_html_e( 'Column & Row Builder', 'frontend-dashboard' ); ?></h3>
								<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Add columns with their headers, then set how many default rows appear on the frontend.', 'frontend-dashboard' ); ?></p>
							</div>
						</div>

						<!-- Column list -->
						<div>
							<label class="block text-xs font-bold text-slate-700 mb-3"><?php esc_html_e( 'Column Headers', 'frontend-dashboard' ); ?></label>
							<div id="fed_table_columns_list" class="space-y-2.5 mb-4">
									<?php foreach ( $columns as $idx => $col_name ) : ?>
									<div class="fed-table-col-row flex items-center gap-2.5">
										<span class="flex items-center justify-center w-6 h-6 rounded-lg bg-slate-100 text-slate-400 text-xs font-bold shrink-0 fed-table-col-num"><?php echo (int) $idx + 1; ?></span>
										<input
											type="text"
											class="fed-table-col-input flex-1 text-xs text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all"
											style="min-height:38px;"
											placeholder="<?php esc_attr_e( 'Column name', 'frontend-dashboard' ); ?>"
											value="<?php echo esc_attr( $col_name ); ?>"
											data-col-index="<?php echo (int) $idx; ?>"
										/>
										<button type="button" class="fed-table-col-remove w-8 h-8 rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-100 flex items-center justify-center shrink-0 transition-all" title="<?php esc_attr_e( 'Remove', 'frontend-dashboard' ); ?>">
											<i class="fas fa-times text-xs"></i>
										</button>
									</div>
								<?php endforeach; ?>
							</div>
							<button type="button" id="fed_table_add_column" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 text-xs font-bold transition-all">
								<i class="fas fa-plus"></i>
								<?php esc_html_e( 'Add Column', 'frontend-dashboard' ); ?>
							</button>
						</div>

						<!-- Default Rows -->
						<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 pt-5 border-t border-slate-100 items-start">
							<div class="space-y-1.5">
								<label for="fed_table_default_rows" class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Default Rows', 'frontend-dashboard' ); ?></label>
								<input
									type="number"
									id="fed_table_default_rows"
									min="1"
									max="50"
									value="<?php echo (int) $default_rows; ?>"
									class="w-full text-xs text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all"
									style="min-height:38px;"
								/>
								<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Number of rows to generate for data entry/display.', 'frontend-dashboard' ); ?></p>
							</div>
							<div class="sm:col-span-2 pt-1">
								<div id="fed_table_mode_help_banner" class="p-3.5 bg-indigo-50/70 rounded-2xl border border-indigo-100 text-xs text-indigo-700 flex items-start gap-2.5">
									<i class="fas fa-info-circle mt-0.5 shrink-0 text-indigo-500"></i>
									<span id="fed_table_mode_help_text"><?php esc_html_e( 'Fill cell data in the live preview below. In Read-Only mode, users see this exact data. In User Input mode, these serve as initial defaults.', 'frontend-dashboard' ); ?></span>
								</div>
							</div>
						</div>

						<!-- Live Preview -->
						<div class="pt-5 border-t border-slate-100">
							<div class="flex items-center justify-between gap-3 mb-3">
								<label class="block text-xs font-bold text-slate-700 flex items-center gap-2 m-0">
									<i class="fas fa-eye text-indigo-500"></i>
									<?php esc_html_e( 'Live Preview & Default Data Entry', 'frontend-dashboard' ); ?>
								</label>
								<span id="fed_table_preview_mode_badge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[11px] font-bold"></span>
							</div>

							<div id="fed_table_preview_wrapper" class="overflow-x-auto rounded-2xl border border-slate-200 bg-white transition-all">
								<table class="w-full text-xs border-collapse" id="fed_table_preview">
									<thead>
										<tr class="bg-slate-100 border-b border-slate-200">
											<?php foreach ( $columns as $col_name ) : ?>
												<th class="px-4 py-2.5 text-left text-[11px] font-bold text-slate-700 uppercase tracking-wider whitespace-nowrap"><?php echo esc_html( $col_name ); ?></th>
											<?php endforeach; ?>
										</tr>
									</thead>
									<tbody>
										<?php for ( $r = 0; $r < min( $default_rows, 4 ); $r++ ) : ?>
											<tr class="border-b border-slate-100 last:border-0">
												<?php foreach ( $columns as $col_name ) : ?>
													<td class="px-3 py-2">
														<div class="h-7 bg-slate-50 border border-slate-200 rounded-lg"></div>
													</td>
												<?php endforeach; ?>
											</tr>
										<?php endfor; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<?php
					fed_get_admin_up_display_permission( $row, $action );
					fed_get_admin_up_role_based( $row, $action, $menu_options );
					fed_get_input_type_and_submit_btn( 'table', $action );
					?>
				</form>
			</div>

			<script>
			(function($) {
				'use strict';
				var $c = $('.fed_input_table_container');

				// Parse current schema string to get existing cell values
				function getExistingCellValues() {
					var raw = $c.find('#fed_table_schema_hidden').val();
					var parts = raw ? raw.split('|') : [];
					if (parts.length > 2 && parts[2]) {
						try { return JSON.parse(parts[2]); } catch (e) {}
					}
					return {};
				}

				function fedTblSchema() {
					var cols = [];
					$c.find('.fed-table-col-input').each(function() {
						var v = $.trim($(this).val());
						if (v !== '') cols.push(v);
					});
					var rows = parseInt($c.find('#fed_table_default_rows').val(), 10) || 1;
					
					// Collect cell values from preview inputs
					var cellValues = getExistingCellValues(); // keep old values just in case
					$c.find('.fed-table-cell-input').each(function() {
						var r = $(this).data('row');
						var col = $(this).data('col');
						var val = $(this).val();
						if (val !== '') {
							cellValues['row_' + r + '_' + col] = val;
						} else {
							delete cellValues['row_' + r + '_' + col];
						}
					});

					var schema = cols.join(',') + '|' + rows;
					if (Object.keys(cellValues).length > 0) {
						schema += '|' + JSON.stringify(cellValues);
					}
					
					$c.find('#fed_table_schema_hidden').val(schema);
					fedTblPreview(cols, rows, cellValues);
					fedTblRenum();
				}

				function fedTblPreview(cols, rows, cellValues) {
					var $t = $c.find('#fed_table_preview');
					var $wrap = $c.find('#fed_table_preview_wrapper');
					var mode = $c.find('#fed_table_mode_select').val() || 'editable';
					var tpl = $c.find('#fed_table_template_select').val() || 'bordered';

					// Update Mode Badge
					var $badge = $c.find('#fed_table_preview_mode_badge');
					var $helpText = $c.find('#fed_table_mode_help_text');
					if (mode === 'readonly') {
						$badge.removeClass('bg-indigo-50 text-indigo-700 border-indigo-200/80')
							  .addClass('bg-amber-50 text-amber-700 border border-amber-200/80')
							  .html('<i class="fas fa-lock text-[10px]"></i> <?php esc_html_e( 'Read-Only Mode', 'frontend-dashboard' ); ?>');
						$helpText.text('<?php esc_html_e( 'Read-Only Mode: Users cannot edit this table on the frontend. The data you enter in the preview cells below will be shown as static table text.', 'frontend-dashboard' ); ?>');
					} else {
						$badge.removeClass('bg-amber-50 text-amber-700 border-amber-200/80')
							  .addClass('bg-indigo-50 text-indigo-700 border border-indigo-200/80')
							  .html('<i class="fas fa-pen text-[10px]"></i> <?php esc_html_e( 'User Input Mode', 'frontend-dashboard' ); ?>');
						$helpText.text('<?php esc_html_e( 'User Input Mode: Users can edit cells on the frontend. Any data entered below will serve as initial default values.', 'frontend-dashboard' ); ?>');
					}

					// Update wrapper styles according to template
					$wrap.removeClass('border-0 shadow-none border-slate-200');
					if (tpl === 'borderless') {
						$wrap.addClass('border-0 shadow-none');
					} else {
						$wrap.addClass('border border-slate-200');
					}

					// Build thead
					var thRowClass = 'bg-slate-100 border-b border-slate-200';
					var thCellClass = 'px-4 py-2.5 text-left text-[11px] font-bold text-slate-700 uppercase tracking-wider whitespace-nowrap';
					
					if (tpl === 'striped') {
						thRowClass = 'bg-slate-800 text-white';
						thCellClass = 'px-4 py-2.5 text-left text-[11px] font-bold text-white uppercase tracking-wider whitespace-nowrap border-r border-slate-700 last:border-r-0';
					} else if (tpl === 'borderless') {
						thRowClass = 'bg-transparent border-b-2 border-slate-200';
						thCellClass = 'px-4 py-2.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap';
					} else if (tpl === 'compact') {
						thRowClass = 'bg-slate-100/90 border-b border-slate-200';
						thCellClass = 'px-3 py-2 text-left text-[10px] font-bold text-slate-600 uppercase tracking-wider whitespace-nowrap border-r border-slate-200/60 last:border-r-0';
					} else { // bordered
						thRowClass = 'bg-slate-50/90 border-b border-slate-200';
						thCellClass = 'px-4 py-2.5 text-left text-[11px] font-bold text-slate-700 uppercase tracking-wider whitespace-nowrap border-r border-slate-200/60 last:border-r-0';
					}

					var th = '<tr class="' + thRowClass + '">';
					if (cols.length === 0) {
						th += '<th class="px-4 py-2.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider"><?php esc_html_e( 'Add columns above…', 'frontend-dashboard' ); ?></th>';
					} else {
						$.each(cols, function(i, c) {
							th += '<th class="' + thCellClass + '">' + $('<span>').text(c).html() + '</th>';
						});
					}
					th += '</tr>';
					$t.find('thead').html(th);
					
					// Build tbody
					var tb = '';
					for (var r = 0; r < rows; r++) {
						var trClass = 'border-b border-slate-100 last:border-0 hover:bg-slate-50';
						if (tpl === 'striped') {
							trClass = (r % 2 === 1 ? 'bg-slate-50/70 ' : 'bg-white ') + 'border-b border-slate-100 last:border-0 hover:bg-indigo-50/30';
						}
						tb += '<tr class="' + trClass + '">';
						if (cols.length === 0) {
							tb += '<td class="px-3 py-2"><div class="h-7 bg-slate-50 border border-slate-200 rounded-lg opacity-50"></div></td>';
						} else {
							$.each(cols, function(cIdx, c) {
								var cellKey = 'row_' + r + '_' + cIdx;
								var val = cellValues && cellValues[cellKey] ? cellValues[cellKey] : '';
								var tdPad = (tpl === 'compact') ? 'px-2 py-1.5' : 'px-3 py-2';
								var borderClass = (tpl === 'borderless') ? '' : ' border-r border-slate-100 last:border-r-0';
								tb += '<td class="' + tdPad + borderClass + '"><input type="text" data-row="' + r + '" data-col="' + cIdx + '" class="fed-table-cell-input w-full text-xs text-slate-700 bg-white border border-slate-200 rounded-lg px-2.5 outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 transition-all" style="min-height:30px;" placeholder="' + $('<span>').text(c).html() + '" value="' + $('<span>').text(val).html() + '" /></td>';
							});
						}
						tb += '</tr>';
					}
					$t.find('tbody').html(tb);
				}

				function fedTblRenum() {
					$c.find('.fed-table-col-row').each(function(i) {
						$(this).find('.fed-table-col-num').text(i + 1);
						$(this).find('.fed-table-col-input').attr('data-col-index', i);
					});
				}

				$c.on('click', '#fed_table_add_column', function() {
					var tpl = '<div class="fed-table-col-row flex items-center gap-2.5">' +
						'<span class="flex items-center justify-center w-6 h-6 rounded-lg bg-slate-100 text-slate-400 text-xs font-bold shrink-0 fed-table-col-num">+</span>' +
						'<input type="text" class="fed-table-col-input flex-1 text-xs text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all" style="min-height:38px;" placeholder="<?php esc_attr_e( 'Column name', 'frontend-dashboard' ); ?>" value="" />' +
						'<button type="button" class="fed-table-col-remove w-8 h-8 rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-100 flex items-center justify-center shrink-0 transition-all" title="<?php esc_attr_e( 'Remove', 'frontend-dashboard' ); ?>"><i class="fas fa-times text-xs"></i></button>' +
						'</div>';
					$c.find('#fed_table_columns_list').append(tpl);
					$c.find('#fed_table_columns_list .fed-table-col-row:last .fed-table-col-input').focus();
					fedTblSchema();
				});

				$c.on('click', '.fed-table-col-remove', function() {
					if ($c.find('.fed-table-col-row').length <= 1) {
						$c.find('.fed-table-col-input').first().val('').focus();
					} else {
						$(this).closest('.fed-table-col-row').remove();
					}
					fedTblSchema();
				});

				$c.on('input change', '.fed-table-col-input, #fed_table_default_rows, #fed_table_mode_select, #fed_table_template_select', function() {
					fedTblSchema();
				});
				
				// Listen for cell data changes
				$c.on('input change', '.fed-table-cell-input', function() {
					var raw = $c.find('#fed_table_schema_hidden').val();
					var parts = raw ? raw.split('|') : [];
					var cellValues = {};
					if (parts.length > 2 && parts[2]) {
						try { cellValues = JSON.parse(parts[2]); } catch (e) {}
					}
					
					var r = $(this).data('row');
					var col = $(this).data('col');
					var val = $(this).val();
					if (val !== '') {
						cellValues['row_' + r + '_' + col] = val;
					} else {
						delete cellValues['row_' + r + '_' + col];
					}
					
					var schema = (parts[0] || '') + '|' + (parts[1] || 1);
					if (Object.keys(cellValues).length > 0) {
						schema += '|' + JSON.stringify(cellValues);
					}
					$c.find('#fed_table_schema_hidden').val(schema);
				});

				// Init without rewriting existing values in hidden field
				var initialRaw = $c.find('#fed_table_schema_hidden').val();
				var initialParts = initialRaw ? initialRaw.split('|') : [];
				var initialCellValues = {};
				if (initialParts.length > 2 && initialParts[2]) {
					try { initialCellValues = JSON.parse(initialParts[2]); } catch (e) {}
				}
				var initialCols = [];
				$c.find('.fed-table-col-input').each(function() {
					var v = $.trim($(this).val());
					if (v !== '') initialCols.push(v);
				});
				var initialRows = parseInt($c.find('#fed_table_default_rows').val(), 10) || 1;
				fedTblPreview(initialCols, initialRows, initialCellValues);

			})(jQuery);
			</script>
			<?php
		}

		/**
		 * File Field
		 *
		 * @param array  $row
		 * @param string $action
		 * @param array  $menu_options
		 */
		public function fed_extra_admin_input_fields_container_extra_file( $row, $action, $menu_options ) {
			$is_active = ( isset( $row['input_type'] ) && 'file' === $row['input_type'] );
			?>
			<div class="fed_input_type_container fed_input_file_container space-y-7 <?php echo $is_active ? '' : 'hide hidden'; ?>" data-field-type="file">
				<form method="post"
						class="fed_admin_menu fed_ajax space-y-7"
						action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_up_form' ) ); ?>">

					<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
					<?php echo fed_loader(); ?>

					<!-- Card: Basic Field Settings -->
					<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6 sm:space-y-7">
						<div class="flex items-center gap-3.5 pb-5 border-b border-slate-100">
							<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
								<i class="fas fa-cloud-upload-alt"></i>
							</div>
							<div>
								<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php esc_html_e( 'File Upload Field', 'frontend-dashboard' ); ?></h3>
								<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Upload documents, images, audio, or video files with preview.', 'frontend-dashboard' ); ?></p>
							</div>
						</div>

						<div class="space-y-5">
							<?php fed_get_admin_up_label_input_order( $row ); ?>
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
								<?php fed_get_admin_up_input_meta( $row ); ?>
								<?php fed_get_placeholder_field( $row ); ?>
							</div>
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
								<?php fed_get_class_field( $row ); ?>
								<?php fed_get_id_field( $row ); ?>
							</div>
						</div>
					</div>

					<?php
					fed_get_admin_up_display_permission( $row, $action, 'file' );
					fed_get_admin_up_role_based( $row, $action, $menu_options );
					fed_get_input_type_and_submit_btn( 'file', $action );
					?>
				</form>
			</div>
			<?php
		}

		/**
		 * Color Field
		 *
		 * @param array  $row
		 * @param string $action
		 * @param array  $menu_options
		 */
		public function fed_extra_admin_input_fields_container_extra_color( $row, $action, $menu_options ) {
			$is_active = ( isset( $row['input_type'] ) && 'color' === $row['input_type'] );
			?>
			<div class="fed_input_type_container fed_input_color_container space-y-7 <?php echo $is_active ? '' : 'hide hidden'; ?>" data-field-type="color">
				<form method="post"
						class="fed_admin_menu fed_ajax space-y-7"
						action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_up_form' ) ); ?>">

					<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
					<?php echo fed_loader(); ?>

					<!-- Card: Basic Field Settings -->
					<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6 sm:space-y-7">
						<div class="flex items-center gap-3.5 pb-5 border-b border-slate-100">
							<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
								<i class="fas fa-palette"></i>
							</div>
							<div>
								<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php esc_html_e( 'Color Picker Field', 'frontend-dashboard' ); ?></h3>
								<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Color picker with HEX code selection.', 'frontend-dashboard' ); ?></p>
							</div>
						</div>

						<div class="space-y-5">
							<?php fed_get_admin_up_label_input_order( $row ); ?>
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
								<?php fed_get_admin_up_input_meta( $row ); ?>
								<?php fed_get_placeholder_field( $row ); ?>
							</div>
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
								<?php fed_get_class_field( $row ); ?>
								<?php fed_get_id_field( $row ); ?>
							</div>
						</div>
					</div>

					<?php
					fed_get_admin_up_display_permission( $row, $action );
					fed_get_admin_up_role_based( $row, $action, $menu_options );
					fed_get_input_type_and_submit_btn( 'color', $action );
					?>
				</form>
			</div>
			<?php
		}

		/**
		 * Label Field
		 *
		 * @param array  $row
		 * @param string $action
		 * @param array  $menu_options
		 */
		public function fed_extra_admin_input_fields_container_extra_label( $row, $action, $menu_options ) {
			$is_active     = ( isset( $row['input_type'] ) && 'label' === $row['input_type'] );
			$label_content = isset( $row['input_value'] ) ? $row['input_value'] : '';
			?>
			<div class="fed_input_type_container fed_input_label_container space-y-7 <?php echo $is_active ? '' : 'hide hidden'; ?>" data-field-type="label">
				<form method="post"
						class="fed_admin_menu fed_ajax space-y-7"
						action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_up_form' ) ); ?>">

					<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
					<?php echo fed_loader(); ?>

					<!-- Card: Basic Field Settings -->
					<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6 sm:space-y-7">
						<div class="flex items-center gap-3.5 pb-5 border-b border-slate-100">
							<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
								<i class="fas fa-tag"></i>
							</div>
							<div>
								<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php esc_html_e( 'Custom Label / Static HTML', 'frontend-dashboard' ); ?></h3>
								<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Display custom formatted HTML or instructional text in the form.', 'frontend-dashboard' ); ?></p>
							</div>
						</div>

						<div class="space-y-5">
							<?php fed_get_admin_up_label_input_order( $row ); ?>
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
								<?php fed_get_admin_up_input_meta( $row ); ?>
								<?php fed_get_class_field( $row ); ?>
							</div>
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
								<?php fed_get_id_field( $row ); ?>
							</div>
						</div>
					</div>

					<!-- Card: Label Content -->
					<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-5">
						<div class="flex items-center gap-3.5 pb-4 border-b border-slate-100">
							<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
								<i class="fas fa-code"></i>
							</div>
							<div>
								<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php esc_html_e( 'Label / HTML Content', 'frontend-dashboard' ); ?></h3>
								<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Enter rich HTML or description text that will be shown to users.', 'frontend-dashboard' ); ?></p>
							</div>
						</div>

						<div class="fed_wp_editor_wrapper rounded-2xl overflow-hidden border border-slate-200/90 shadow-2xs bg-white">
							<textarea id="fed_label_html_content_editor" name="input_value" rows="10" class="w-full p-4 border-0 outline-none font-mono text-xs text-slate-800 bg-white" placeholder="<?php esc_attr_e( 'Enter HTML content or text here...', 'frontend-dashboard' ); ?>"><?php echo esc_textarea( $label_content ); ?></textarea>
						</div>
					</div>

					<?php
					fed_get_admin_up_display_permission( $row, $action );
					fed_get_admin_up_role_based( $row, $action, $menu_options );
					fed_get_input_type_and_submit_btn( 'label', $action );
					?>
				</form>
			</div>
			<?php
		}


		/**
		 * @param $values
		 *
		 * @return string
		 */
		private function get_image_by_type( $values ) {
			$mime_type = get_post_mime_type( $values['user_value'] );
			$default   = fed_image_mime_types();
			if ( strpos( $mime_type, 'image' ) !== false ) {
				return wp_get_attachment_image( $values['user_value'], array( 100, 100 ) );
			}

			if ( isset( $default[ $mime_type ] ) ) {
				return '<img src="' . $default[ $mime_type ] . '" />';
			}

			return '<img src="' . site_url() . '/wp-includes/images/media/default.png" />';
		}


		/**
		 * @param $menu
		 */
		public function fed_admin_dashboard_settings_menu_header_extra( $menu ) {
			$menu['general'] = array(
				'icon_class' => 'fas fa-tachometer-alt',
				'name'       => __( 'General', 'frontend-dashboard-extra' ),
				'callable'   => array(
					'object' => new FEDE_Menu(),
					'method' => 'fed_admin_general_tab',
				),
			);

			return $menu;

		}

	}

	new FEDE_Menu();
}
