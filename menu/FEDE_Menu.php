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
		 */
		public function fed_extra_admin_input_fields_container_extra_date( $row, $action, $menu_options ) {
			?>
			<div class="row fed_input_type_container fed_input_date_container hide">
				<form method="post"
						class="fed_admin_menu fed_ajax"
						action="<?php echo admin_url( 'admin-ajax.php?action=fed_admin_setting_up_form' ); ?>">

					<?php wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>

					<?php echo fed_loader(); ?>

					<div class="col-md-12">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h3 class="panel-title">
									<b>Date</b>
								</h3>
							</div>
							<div class="panel-body">
								<div class="fed_input_text">
									<?php fed_get_admin_up_label_input_order( $row ); ?>
									<div class="row">
										<?php fed_get_admin_up_input_meta( $row ); ?>

										<div class="form-group col-md-3">
											<label for="">Class Name</label>
											<?php
											echo fed_input_box(
												'class_name', array( 'value' => $row['class_name'] ),
												'single_line'
											);
											?>
										</div>

										<div class="form-group col-md-3">
											<label for="">ID Name</label>
											<?php
											echo fed_input_box(
												'id_name', array( 'value' => $row['id_name'] ),
												'single_line'
											);
											?>
										</div>
									</div>

									<div class="row">
										<div class="form-group col-md-3">
											<label for="">Date Format</label>
											<?php
											echo fed_input_box(
												'date_format', array(
												'name'    => 'extended[date_format]',
												'value'   => isset( $row['extended']['date_format'] ) ? $row['extended']['date_format'] : '',
												'options' => fed_get_date_formats(),
											), 'select'
											);
											?>
										</div>
										<div class="form-group col-md-3">
											<label for="">Enable Time</label>
											<?php
											echo fed_input_box(
												'enable_time', array(
												'name'    => 'extended[enable_time]',
												'value'   => isset( $row['extended']['enable_time'] ) ? $row['extended']['enable_time'] : '',
												'options' => array(
													'false' => 'False',
													'true'  => 'True',
												),
											), 'select'
											);
											?>
										</div>
										<div class="form-group col-md-3">
											<label for="date_mode">Date Mode</label>
											<?php
											echo fed_input_box(
												'date_mode', array(
												'name'    => 'extended[date_mode]',
												'value'   => isset( $row['extended']['date_mode'] ) ? $row['extended']['date_mode'] : '',
												'options' => fed_get_date_mode(),
											), 'select'
											);
											?>
										</div>
										<div class="form-group col-md-3">
											<label for="">Time Hours</label>
											<?php
											echo fed_input_box(
												'time_24hr', array(
												'name'    => 'extended[time_24hr]',
												'value'   => isset( $row['extended']['time_24hr'] ) ? $row['extended']['time_24hr'] : '',
												'options' => array(
													'true'  => '24 Hours',
													'false' => '12 Hours',
												),
											), 'select'
											);
											?>
										</div>
									</div>

									<?php
									fed_get_admin_up_display_permission( $row, $action );

									fed_get_admin_up_role_based( $row, $action, $menu_options );

									fed_get_input_type_and_submit_btn( 'date', $action );
									?>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<?php
		}

		/**
		 * WP Editor Field
		 *
		 * @param $row
		 * @param $action
		 * @param $menu_options
		 */
		public function fed_extra_admin_input_fields_container_extra_wp_editor( $row, $action, $menu_options ) {
			?>
			<div class="row fed_input_type_container fed_input_wp_editor_container hide">
				<form method="post"
						class="fed_admin_menu fed_ajax"
						action="<?php echo admin_url( 'admin-ajax.php?action=fed_admin_setting_up_form' ); ?>">

					<?php wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>

					<?php echo fed_loader(); ?>

					<div class="col-md-12">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h3 class="panel-title">
									<b>WP Editor</b>
								</h3>
							</div>
							<div class="panel-body">
								<div class="fed_input_text">
									<?php fed_get_admin_up_label_input_order( $row ); ?>
									<div class="row">
										<?php fed_get_admin_up_input_meta( $row ); ?>

										<div class="form-group col-md-3">
											<label for="">Class Name</label>
											<?php
											echo fed_input_box(
												'class_name', array( 'value' => $row['class_name'] ),
												'single_line'
											);
											?>
										</div>

										<div class="form-group col-md-3">
											<label for="">ID Name</label>
											<?php
											echo fed_input_box(
												'id_name',
												array( 'value' => ! empty( $row['id_name'] ) ? $row['id_name'] : fed_get_random_string( 10 ) ),
												'single_line'
											);
											?>
										</div>
									</div>

									<div class="row fed_admin_up_display_permission">
										<?php
										if ( $action === 'profile' ) {

											$value        = $row['show_register'];
											$others       = '';
											$notification = '';
											?>
											<div class="form-group col-md-4">
												<?php
												echo fed_input_box(
													'show_register', array(
													'default_value' => 'Enable',
													'label'         => __(
														                   'Show in Register Form',
														                   'frontend-dashboard'
													                   ) . ' ' . $notification,
													'value'         => $value,
													'disabled'      => $others,
												), 'checkbox'
												);
												?>
											</div>

											<div class="form-group col-md-4">
												<?php
												echo fed_input_box(
													'show_dashboard', array(
													'default_value' => 'Enable',
													'label'         => __(
														'Show in User Dashboard ',
														'frontend-dashboard'
													),
													'value'         => $row['show_dashboard'],
												), 'checkbox'
												);
												?>
											</div>
										<?php } ?>

										<?php
										if ( $action == 'post' ) {
											?>
											<div class="form-group col-md-4">
												<label><?php _e( 'Post Type', 'frontend-dashboard' ); ?></label>
												<?php
												echo fed_input_box(
													'post_type', array(
													'default_value' => 'Post',
													'value'         => $row['post_type'],
													'options'       => fed_get_public_post_types(),
												), 'select'
												);
												?>
											</div>
										<?php } ?>
									</div>

									<div class="row fed_e_wp_editor_1">
										<div class="form-group col-md-3">
											<?php
											echo fed_input_box(
												'extended[settings][media_buttons]', array(
												'default_value' => 'true',
												'label'         => __(
													'Enable Media',
													'frontend-dashboard'
												),
												'value'         => fed_get_data( 'extended.settings.media_buttons',
													$row ),
											), 'checkbox'
											);
											?>
										</div>
										<div class="form-group col-md-3">
											<?php
											echo fed_input_box(
												'extended[settings][quicktags]', array(
												'default_value' => 'true',
												'label'         => __(
													'Enable Quick Tags',
													'frontend-dashboard'
												),
												'value'         => fed_get_data( 'extended.settings.quicktags', $row ),
											), 'checkbox'
											);
											?>
										</div>
										<div class="form-group col-md-3">
											<label for="">Textarea Rows</label>
											<?php
											echo fed_input_box(
												'extended[settings][textarea_rows]',
												array(
													'value' => fed_get_data( 'extended.settings.textarea_rows', $row ),
												),
												'number'
											);
											?>
										</div>
										<div class="form-group col-md-3">
											<label for="">Editor Height</label>
											<?php
											echo fed_input_box(
												'extended[settings][editor_height]',
												array(
													'value' => fed_get_data( 'extended.settings.editor_height', $row ),
												),
												'number'
											);
											?>
										</div>
									</div>
									<?php
									fed_get_admin_up_role_based( $row, $action, $menu_options );

									fed_get_input_type_and_submit_btn( 'wp_editor', $action );
									?>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<?php
		}
		public function fed_extra_admin_input_fields_container_extra_table( $row, $action, $menu_options ) {
			?>
            <div class="row fed_input_type_container fed_input_table_container hide">
                <form method="post"
                      class="fed_admin_menu fed_ajax"
                      action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_up_form' )) ?>">

					<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ) ?>

					<?php echo fed_loader(); ?>

                    <div class="col-md-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    <b><?php _e( 'Table', 'frontend-dashboard' ) ?></b>
                                </h3>
                            </div>
                            <div class="panel-body">
                                <div class="fed_input_text">
									<?php fed_get_admin_up_label_input_order( $row ); ?>
                                    <div class="row">
										<?php fed_get_admin_up_input_meta( $row ) ?>

                                        <div class="form-group col-md-3">
											<?php fed_get_class_field( $row ) ?>
                                        </div>

                                        <div class="form-group col-md-3">
											<?php fed_get_id_field( $row ) ?>
                                        </div>

                                    </div>
									<?php
									fed_get_admin_up_display_permission( $row, $action );

									fed_get_admin_up_role_based( $row, $action, $menu_options );
									?>
                                    <div class="row fed_key_value_paid">
                                        <div class="col-md-5">
                                            <label for=""><?php _e( 'Values', 'frontend-dashboard' ) ?></label>
											<?php echo fed_input_box( 'input_value', array(
												'placeholder' => __( 'Please enter the value by key,value',
													'frontend-dashboard' ),
												'rows'        => 10,
												'value'       => $row['input_value'],
											), 'multi_line' ); ?>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="row fed_key_value_eg_container">
                                                <div class="col-md-12">
                                                    <label for=""><?php _e( 'Examples:', 'frontend-dashboard' ) ?></label>
                                                    <p>Column Headings with comma separated|Number of rows</p>
                                                    <p>column1,column2,column3,column4,column5|2</p>
                                                </div>
                                                <div class="col-md-12">
                                                    <b><?php _e( 'This will be output as', 'frontend-dashboard' ) ?></b>
                                                    <br><br>
													<table>
                                                        <thead>
                                                        <tr>
                                                            <th>column1</th>
                                                            <th>column2</th>
                                                            <th>column3</th>
                                                            <th>column4</th>
                                                            <th>column5</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td><input style="width:100px"></td>
                                                            <td><input style="width:100px"></td>
                                                            <td><input style="width:100px"></td>
                                                            <td><input style="width:100px"></td>
                                                            <td><input style="width:100px"></td>
                                                        </tr>
                                                        <tr>
                                                            <td><input style="width:100px"></td>
                                                            <td><input style="width:100px"></td>
                                                            <td><input style="width:100px"></td>
                                                            <td><input style="width:100px"></td>
                                                            <td><input style="width:100px"></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

									<?php
									fed_get_input_type_and_submit_btn( 'select', $action );
									?>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
			<?php
		}

		/**
		 * File Field
		 *
		 * @param  array  $row
		 * @param  string $action
		 */
		public function fed_extra_admin_input_fields_container_extra_file( $row, $action, $menu_options ) {
			?>
			<div class="row fed_input_type_container fed_input_file_container hide">
				<form method="post"
						class="fed_admin_menu fed_ajax"
						action="<?php echo admin_url( 'admin-ajax.php?action=fed_admin_setting_up_form' ); ?>">

					<?php wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>

					<?php echo fed_loader(); ?>

					<div class="col-md-12">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h3 class="panel-title">
									<b>File</b>
								</h3>
							</div>
							<div class="panel-body">
								<div class="fed_input_text">
									<?php fed_get_admin_up_label_input_order( $row ); ?>
									<div class="row">
										<?php fed_get_admin_up_input_meta( $row ); ?>
										<div class="form-group col-md-3">
											<label for="">Class Name</label>
											<?php
											echo fed_input_box(
												'class_name', array( 'value' => $row['class_name'] ),
												'single_line'
											);
											?>
										</div>

										<div class="form-group col-md-3">
											<label for="">ID Name</label>
											<?php
											echo fed_input_box(
												'id_name', array( 'value' => $row['id_name'] ),
												'single_line'
											);
											?>
										</div>
									</div>

									<?php
									fed_get_admin_up_display_permission( $row, $action, $type = 'file' );

									fed_get_admin_up_role_based( $row, $action, $menu_options );

									fed_get_input_type_and_submit_btn( 'file', $action );
									?>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<?php
		}

		/**
		 * Color Field
		 *
		 * @param  array  $row
		 * @param  string $action
		 */
		public function fed_extra_admin_input_fields_container_extra_color( $row, $action, $menu_options ) {
			?>
			<div class="row fed_input_type_container fed_input_color_container hide">
				<form method="post"
						class="fed_admin_menu fed_ajax"
						action="<?php echo admin_url( 'admin-ajax.php?action=fed_admin_setting_up_form' ); ?>">

					<?php wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>

					<?php echo fed_loader(); ?>

					<div class="col-md-12">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h3 class="panel-title">
									<b>Color</b>
								</h3>
							</div>
							<div class="panel-body">
								<div class="fed_input_text">
									<?php fed_get_admin_up_label_input_order( $row ); ?>
									<div class="row">
										<?php fed_get_admin_up_input_meta( $row ); ?>

										<div class="form-group col-md-3">
											<label for="">Class Name</label>
											<?php
											echo fed_input_box(
												'class_name', array( 'value' => $row['class_name'] ),
												'single_line'
											);
											?>
										</div>

										<div class="form-group col-md-3">
											<label for="">ID Name</label>
											<?php
											echo fed_input_box(
												'id_name', array( 'value' => $row['id_name'] ),
												'single_line'
											);
											?>
										</div>

									</div>

									<?php
									fed_get_admin_up_display_permission( $row, $action );

									fed_get_admin_up_role_based( $row, $action, $menu_options );

									fed_get_input_type_and_submit_btn( 'color', $action );
									?>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<?php
		}

		/**
		 * Label Field
		 *
		 * @param  array  $row
		 * @param  string $action
		 */
		public function fed_extra_admin_input_fields_container_extra_label( $row, $action, $menu_options ) {
			?>
			<div class="row fed_input_type_container fed_input_label_container hide">
				<form method="post"
						class="fed_admin_menu fed_ajax"
						action="<?php echo admin_url( 'admin-ajax.php?action=fed_admin_setting_up_form' ); ?>">

					<?php wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>

					<?php echo fed_loader(); ?>

					<div class="col-md-12">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h3 class="panel-title">
									<b>Label</b>
								</h3>
							</div>
							<div class="panel-body">
								<div class="fed_input_text">
									<?php fed_get_admin_up_label_input_order( $row ); ?>
									<div class="row">
										<?php fed_get_admin_up_input_meta( $row ); ?>

										<div class="form-group col-md-3">
											<label for="">Class Name</label>
											<?php
											echo fed_input_box(
												'class_name', array( 'value' => $row['class_name'] ),
												'single_line'
											);
											?>
										</div>

										<div class="form-group col-md-3">
											<label for="">ID Name</label>
											<?php
											echo fed_input_box(
												'id_name', array( 'value' => $row['id_name'] ),
												'single_line'
											);
											?>
										</div>

									</div>

									<?php
									fed_get_admin_up_display_permission( $row, $action );

									fed_get_admin_up_role_based( $row, $action, $menu_options );
									?>

									<div class="row">
										<div class="col-md-12">
											<label for="">Label Content</label>
											<?php
											wp_editor( $row['input_value'], 'input_value',
												array( 'media_buttons' => false ) );
											?>
										</div>
									</div>
									<?php
									fed_get_input_type_and_submit_btn( 'color', $action );
									?>
								</div>
							</div>
						</div>
					</div>
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
