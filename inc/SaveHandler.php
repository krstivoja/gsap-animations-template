<?php

namespace FancooloFX;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SaveHandler {

	public static function handle() {
		if ( ! isset( $_POST['fancoolo_fx_save'] ) ) {
			return;
		}

		if ( ! check_admin_referer( 'fancoolo_fx_save_action', 'fancoolo_fx_nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		// Save custom JS file.
		$content    = isset( $_POST['fancoolo_fx_code'] ) ? wp_unslash( $_POST['fancoolo_fx_code'] ) : '';
		$upload_dir = wp_upload_dir();
		$dir        = $upload_dir['basedir'] . '/fancoolo-fx';

		if ( ! file_exists( $dir ) ) {
			wp_mkdir_p( $dir );
		}

		file_put_contents( $dir . '/custom.js', $content );

		// Build tag map.
		$tag_map = array();
		if ( ! empty( $_POST['fancoolo_fx_tag_map'] ) && is_array( $_POST['fancoolo_fx_tag_map'] ) ) {
			foreach ( $_POST['fancoolo_fx_tag_map'] as $row ) {
				$selector = sanitize_text_field( $row['selector'] ?? '' );
				$effect   = sanitize_text_field( $row['effect'] ?? '' );
				if ( '' !== $selector && '' !== $effect ) {
					$tag_map[] = array( 'selector' => $selector, 'effect' => $effect );
				}
			}
		}

		// Save settings.
		$settings = array(
			'scroll_start'           => sanitize_text_field( $_POST['fancoolo_fx_scroll_start'] ?? 'top 85%' ),
			'scroll_once'            => isset( $_POST['fancoolo_fx_scroll_once'] ) ? '1' : '0',
			'section_selector'       => sanitize_text_field( $_POST['fancoolo_fx_section_selector'] ?? 'section' ),
			'debug_markers'          => isset( $_POST['fancoolo_fx_debug_markers'] ) ? '1' : '0',
			'disable_mobile'         => isset( $_POST['fancoolo_fx_disable_mobile'] ) ? '1' : '0',
			'mobile_breakpoint'      => sanitize_text_field( $_POST['fancoolo_fx_mobile_breakpoint'] ?? '768' ),
			'speed_multiplier'       => sanitize_text_field( $_POST['fancoolo_fx_speed_multiplier'] ?? '1' ),
			'respect_reduced_motion' => isset( $_POST['fancoolo_fx_respect_reduced_motion'] ) ? '1' : '0',
			'exclude_selectors'      => sanitize_text_field( $_POST['fancoolo_fx_exclude_selectors'] ?? '' ),
			'gutenberg_panel'        => isset( $_POST['fancoolo_fx_gutenberg_panel'] ) ? '1' : '0',
			'tag_map'                => $tag_map,
		);

		Settings::save( $settings );

		add_settings_error( 'fancoolo_fx', 'fancoolo_fx_saved', 'Settings saved.', 'success' );
	}
}
