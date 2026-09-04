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
							$extended = unserialize( $extended );
						}
					}

					$dateFormat = isset( $extended['date_format'] ) && ! empty( $extended['date_format'] ) ? esc_attr( $extended['date_format'] ) : 'm-d-Y';

					$mode = isset( $extended['date_mode'] ) && ! empty( $extended['date_mode'] ) ? esc_attr( $extended['date_mode'] ) : 'single';

					$enableTime = isset( $extended['enable_time'] ) && ! empty( $extended['enable_time'] ) ? esc_attr( $extended['enable_time'] ) : false;

					$time_24hr = isset( $extended['time_24hr'] ) && ! empty( $extended['time_24hr'] ) ? esc_attr( $extended['time_24hr'] ) : false;

					$input .= '<input type="text" ' . fed_get_data(
							'is_required',
							$attr
						) . ' data-date-format="F j, Y h:i K" data-alt-format="' . $dateFormat . '" data-alt-input="true" data-mode="' . $mode . '" placeholder="' . $dateFormat . '" data-enable-time="' . $enableTime . '" data-time_24hr="' . $time_24hr . '" type="text" name="' . $attr['input_meta'] . '"    class="flatpickr ' . fed_get_data(
						          'class_name',
						          $attr
					          ) . '"  id="' . fed_get_data( 'id_name', $attr ) . '" value="' . fed_get_data(
						          'user_value',
						          $attr
					          ) . '" >';
					break;

				case 'wp_editor':
					$input .= fed_e_form_wpeditor( $attr );
					break;

				case 'color':
					$user_value = fed_get_data( 'user_value', $attr, '#000000' );
					$input      .= '<input ' . fed_get_data( 'is_required', $attr ) . ' ' . fed_get_data(
							'disabled',
							$attr
						) . '  type="text" name="' . $attr['input_meta'] . '"    class="form-control jscolor {hash:true} ' . fed_get_data(
						               'class_name',
						               $attr
					               ) . '"  id="' . fed_get_data( 'id_name',
							$attr ) . '"  value="' . $user_value . '" >';
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
												'value'   => isset( $row['extended']['date_format'] ) ? $row['extended']['date_format'] : '',
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
												'value'   => isset( $row['extended']['enable_time'] ) ? $row['extended']['enable_time'] : '',
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
												'value'   => isset( $row['extended']['date_mode'] ) ? $row['extended']['date_mode'] : '',
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
												'value'   => isset( $row['extended']['time_24hr'] ) ? $row['extended']['time_24hr'] : '',
												'options' => array(
													'true'  => __( '24 Hours', 'frontend-dashboard' ),
													'false' => __( '12 Hours', 'frontend-dashboard' ),
												),
											),
											'select'
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
			$is_active = ( isset( $row['input_type'] ) && 'table' === $row['input_type'] );
			$table_val = isset( $row['input_value'] ) ? $row['input_value'] : 'Column 1,Column 2,Column 3|2';
			?>
			<div class="fed_input_type_container fed_input_table_container space-y-7 <?php echo $is_active ? '' : 'hide hidden'; ?>" data-field-type="table">
				<form method="post"
						class="fed_admin_menu fed_ajax space-y-7"
						action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_up_form' ) ); ?>">

					<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
					<?php echo fed_loader(); ?>

					<!-- Card: Basic Field Settings -->
					<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6 sm:space-y-7">
						<div class="flex items-center gap-3.5 pb-5 border-b border-slate-100">
							<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
								<i class="fas fa-table"></i>
							</div>
							<div>
								<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php esc_html_e( 'Table Grid Field', 'frontend-dashboard' ); ?></h3>
								<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Dynamic table grid input for tabular data entry.', 'frontend-dashboard' ); ?></p>
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

					<!-- Card: Table Structure & Columns -->
					<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-5">
						<div class="flex items-center gap-3.5 pb-4 border-b border-slate-100">
							<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
								<i class="fas fa-columns"></i>
							</div>
							<div>
								<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php esc_html_e( 'Table Structure & Rows', 'frontend-dashboard' ); ?></h3>
								<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Define table headers and number of default rows (Format: Col1,Col2,Col3|NumberOfRows)', 'frontend-dashboard' ); ?></p>
							</div>
						</div>

						<div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
							<div class="space-y-2">
								<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Table Schema (Format: Headers|Rows)', 'frontend-dashboard' ); ?></label>
								<?php
								echo fed_input_box(
									'input_value',
									array(
										'placeholder' => 'column1,column2,column3|2',
										'rows'        => 4,
										'value'       => $table_val,
									),
									'multi_line'
								);
								?>
								<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Example: Item Name,Quantity,Price|3', 'frontend-dashboard' ); ?></p>
							</div>

							<div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-2 text-xs text-slate-600">
								<div class="font-bold text-slate-800 flex items-center gap-1.5">
									<i class="fas fa-info-circle text-indigo-500"></i>
									<span><?php esc_html_e( 'Formatting Guide', 'frontend-dashboard' ); ?></span>
								</div>
								<p class="m-0 text-[11px] leading-relaxed">
									Separate column header titles with commas (<code>,</code>), then append a pipe (<code>|</code>) followed by the row count:
								</p>
								<div class="p-2.5 bg-white rounded-xl border border-slate-200 font-mono text-[11px] text-indigo-600">
									Subject,Grade,Score|2
								</div>
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
								<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Enter the HTML or description text that will be shown to users.', 'frontend-dashboard' ); ?></p>
							</div>
						</div>

						<div class="space-y-2">
							<textarea name="input_value" rows="6" class="w-full rounded-2xl border-slate-200 bg-slate-50 text-xs text-slate-800 p-3.5 outline-none focus:border-indigo-500 focus:bg-white font-mono transition-all" placeholder="<?php esc_attr_e( 'Enter HTML content or text here...', 'frontend-dashboard' ); ?>"><?php echo esc_textarea( $label_content ); ?></textarea>
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
