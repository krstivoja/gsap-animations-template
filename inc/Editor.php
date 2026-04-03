<?php

namespace FancooloFX;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Editor {

	public static function enqueue() {
		$s = Settings::get();
		if ( ! $s['gutenberg_panel'] ) {
			return;
		}

		$asset_file = FANCOOLO_FX_PATH . 'assets/editor/index.asset.php';
		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$asset = include $asset_file;
		wp_enqueue_script(
			'fancoolo-fx-editor',
			FANCOOLO_FX_URL . 'assets/editor/index.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);
	}
}
