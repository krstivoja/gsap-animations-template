<?php

namespace FancooloFX;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Frontend {

	public static function enqueue() {
		wp_enqueue_script( 'gsap', FANCOOLO_FX_URL . 'assets/gsap.min.js', array(), '3.14.2', true );
		wp_enqueue_script( 'gsap-scrolltrigger', FANCOOLO_FX_URL . 'assets/ScrollTrigger.min.js', array( 'gsap' ), '3.14.2', true );
		wp_enqueue_script( 'gsap-splittext', FANCOOLO_FX_URL . 'assets/SplitText.min.js', array( 'gsap' ), '3.14.2', true );
		wp_enqueue_script( 'fancoolo-fx', FANCOOLO_FX_URL . 'assets/fx.js', array( 'gsap', 'gsap-scrolltrigger', 'gsap-splittext' ), FANCOOLO_FX_VERSION, true );

		$s      = Settings::get();
		$config = array(
			'scrollStart'          => $s['scroll_start'],
			'scrollOnce'           => (bool) $s['scroll_once'],
			'sectionSelector'      => $s['section_selector'],
			'disableMobile'        => (bool) $s['disable_mobile'],
			'mobileBreakpoint'     => (int) $s['mobile_breakpoint'],
			'speedMultiplier'      => (float) $s['speed_multiplier'],
			'respectReducedMotion' => (bool) $s['respect_reduced_motion'],
			'excludeSelectors'     => $s['exclude_selectors'],
		);

		if ( ! empty( $s['tag_map'] ) ) {
			$tag_map_obj = array();
			foreach ( $s['tag_map'] as $row ) {
				$tag_map_obj[ $row['selector'] ] = $row['effect'];
			}
			$config['tagMap'] = (object) $tag_map_obj;
		}

		wp_add_inline_script( 'fancoolo-fx', 'window.__FX_CONFIG__ = ' . wp_json_encode( $config ) . ';', 'before' );

		if ( $s['debug_markers'] && is_user_logged_in() && current_user_can( 'manage_options' ) ) {
			wp_add_inline_script( 'fancoolo-fx', 'window.__FX_DEBUG_MARKERS__ = true;', 'before' );
		}

		$custom_file = Settings::get_custom_file_path();
		if ( file_exists( $custom_file ) && filesize( $custom_file ) > 0 ) {
			$upload_dir = wp_upload_dir();
			$custom_url = $upload_dir['baseurl'] . '/fancoolo-fx/custom.js';
			wp_enqueue_script( 'fancoolo-fx-custom', $custom_url, array( 'fancoolo-fx' ), filemtime( $custom_file ), true );
		}
	}
}
