<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Files.
 *
 * @param  array $options  Options.
 *
 * @return string
 */
function fed_form_files( $options ) {
	$name     = fed_get_data( 'input_meta', $options );
	$value    = fed_get_data( 'user_value', $options );
	$class    = fed_get_data( 'class_name', $options );
	$fa_icon  = fed_get_data( 'fa_icon', $options, 'fas fa-cloud-upload-alt' );
	$required = fed_get_data( 'is_required', $options ) == 'true' ? 'required="required"' : '';
	$id       = fed_get_data( 'id_name', $options ) != '' ? 'id="' . esc_attr( $options['id_name'] ) . '"' : '';
	$img      = '';
	$filename = '';

	if ( ! empty( $value ) ) {
		$options['user_value'] = (int) $value;
		$img                   = fede_get_image_by_type( $options );
		$is_image              = ! empty( $img );
		$filename              = get_the_title( $options['user_value'] );
		if ( empty( $filename ) ) {
			$filename = basename( (string) get_attached_file( $options['user_value'] ) );
		}
	} else {
		$options['user_value'] = '';
		$is_image              = false;
	}

	if ( empty( $filename ) ) {
		$filename = __( 'File selected', 'frontend-dashboard' );
	}

	ob_start();
	?>
	<div class="fed_upload_wrapper w-full block <?php echo esc_attr( $class ); ?>" style="width: 100% !important; max-width: 100% !important; height: auto !important; display: block !important;" <?php echo $id; ?>>
		<!-- Empty Upload Dropzone -->
		<div class="fed_upload_container fed_upload_image_dummy group w-full cursor-pointer rounded-2xl border-2 border-dashed border-slate-200/90 bg-slate-50/60 p-5 sm:p-6 transition-all duration-200 hover:border-indigo-400 hover:bg-indigo-50/20 hover:shadow-xs flex flex-col items-center justify-center text-center gap-2.5 <?php echo $is_image ? 'fed_hide hidden' : ''; ?>" style="width: 100% !important; height: auto !important;">
			<div class="w-12 h-12 rounded-2xl bg-white border border-slate-200/80 text-indigo-600 flex items-center justify-center text-xl shadow-2xs group-hover:scale-105 group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-600 transition-all duration-200">
				<i class="<?php echo esc_attr( $fa_icon ); ?> fed_upload_icon"></i>
			</div>
			<div>
				<p class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 transition-colors m-0">
					<?php esc_html_e( 'Click to browse or upload file', 'frontend-dashboard' ); ?>
				</p>
				<p class="text-[11px] text-slate-400 m-0 mt-0.5">
					<?php esc_html_e( 'PNG, JPG, PDF, DOCX, ZIP or media files', 'frontend-dashboard' ); ?>
				</p>
			</div>
		</div>

		<!-- Uploaded File Card -->
		<div class="fed_upload_image_actual w-full rounded-2xl border border-slate-200/90 bg-white p-3.5 shadow-2xs flex items-center justify-between gap-4 transition-all <?php echo ! $is_image ? 'fed_hide hidden' : ''; ?>">
			<div class="fed_upload_container flex items-center gap-3.5 min-w-0 flex-1 cursor-pointer group">
				<div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 overflow-hidden shrink-0 flex items-center justify-center shadow-2xs group-hover:border-indigo-200 transition-colors">
					<img src="<?php echo esc_url( $img ); ?>" class="w-full h-full object-cover" alt="<?php esc_attr_e( 'Preview', 'frontend-dashboard' ); ?>" />
				</div>
				<div class="min-w-0 flex-1 text-left">
					<p class="text-xs font-bold text-slate-900 truncate fed_upload_filename m-0 mb-1 group-hover:text-indigo-600 transition-colors">
						<?php echo esc_html( $filename ); ?>
					</p>
					<div class="flex items-center gap-2 text-[11px]">
						<span class="inline-flex items-center gap-1 font-semibold text-emerald-600 bg-emerald-50 border border-emerald-100/80 px-2 py-0.5 rounded-md">
							<i class="fas fa-check text-[9px]"></i> <?php esc_html_e( 'Ready', 'frontend-dashboard' ); ?>
						</span>
						<span class="text-indigo-600 hover:text-indigo-700 font-medium transition-colors">
							<?php esc_html_e( 'Change file', 'frontend-dashboard' ); ?>
						</span>
					</div>
				</div>
			</div>

			<!-- Remove / Delete Button -->
			<button type="button" class="fed_remove_image w-8 h-8 rounded-xl bg-slate-50 hover:bg-rose-50 text-slate-400 hover:text-rose-600 border border-slate-200 hover:border-rose-200 flex items-center justify-center text-xs transition-all shadow-2xs cursor-pointer shrink-0" title="<?php esc_attr_e( 'Remove file', 'frontend-dashboard' ); ?>">
				<i class="fas fa-trash-alt"></i>
			</button>
		</div>

		<input type="hidden" name="<?php echo esc_attr( $name ); ?>" class="fed_upload_input" value="<?php echo esc_attr( $options['user_value'] ); ?>" <?php echo $required; ?> />
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Get Image by Type.
 *
 * @param  array $options  Options.
 *
 * @return string
 */
function fede_get_image_by_type( $options ) {
	$mime_type = get_post_mime_type( $options['user_value'] );
	FED_Log::writeLog( $options['user_value']);
	FED_Log::writeLog( $mime_type);
	$default   = fed_image_mime_types();
	if ( strpos( $mime_type, 'image' ) !== false ) {
		return wp_get_attachment_image_url( $options['user_value'], array( 100, 100 ) );
	}

	if ( isset( $default[ $mime_type ] ) ) {
		return $default[ $mime_type ];
	}

	return site_url() . '/wp-includes/images/media/default.png';
}
