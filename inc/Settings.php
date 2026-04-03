<?php

namespace FancooloFX;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings {

	private static $defaults = array(
		'scroll_start'           => 'top 85%',
		'scroll_once'            => '1',
		'section_selector'       => 'section',
		'debug_markers'          => '0',
		'disable_mobile'         => '0',
		'mobile_breakpoint'      => '768',
		'speed_multiplier'       => '1',
		'respect_reduced_motion' => '1',
		'exclude_selectors'      => '',
		'gutenberg_panel'        => '1',
		'tag_map'                => array(),
	);

	public static function get_defaults() {
		return self::$defaults;
	}

	public static function get() {
		return wp_parse_args( get_option( 'fancoolo_fx_settings', array() ), self::$defaults );
	}

	public static function save( $settings ) {
		update_option( 'fancoolo_fx_settings', $settings );
	}

	public static function get_custom_file_path() {
		$upload_dir = wp_upload_dir();
		return $upload_dir['basedir'] . '/fancoolo-fx/custom.js';
	}
}
