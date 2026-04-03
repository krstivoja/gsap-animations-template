<?php

namespace FancooloFX;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin {

	public static function register_menu() {
		add_theme_page(
			'Fancoolo FX',
			'Fancoolo FX',
			'edit_theme_options',
			'fancoolo-fx',
			array( AdminPage::class, 'render' )
		);
	}

	public static function enqueue( $hook ) {
		if ( 'appearance_page_fancoolo-fx' !== $hook ) {
			return;
		}

		wp_enqueue_style( 'fancoolo-fx-admin', FANCOOLO_FX_URL . 'assets/admin.css', array(), FANCOOLO_FX_VERSION );
		wp_enqueue_script( 'fancoolo-fx-admin', FANCOOLO_FX_URL . 'assets/admin.js', array( 'jquery' ), FANCOOLO_FX_VERSION, true );

		$settings = wp_enqueue_code_editor( array( 'type' => 'text/javascript' ) );

		if ( false !== $settings ) {
			wp_add_inline_script(
				'code-editor',
				sprintf(
					'jQuery(function($) {
						if ($("#fancoolo-fx-editor").length) {
							var editor = wp.codeEditor.initialize($("#fancoolo-fx-editor"), %s);
							editor.codemirror.on("change", function(cm) {
								cm.refresh();
							});
						}
					});',
					wp_json_encode( $settings )
				)
			);
		}
	}
}
