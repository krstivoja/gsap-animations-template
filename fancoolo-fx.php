<?php
/**
 * Plugin Name: Fancoolo FX
 * Plugin URI: https://github.com/krstivoja/fancoolo-fx
 * Description: A class-driven GSAP animation wrapper. Add CSS classes in Gutenberg and get animations — no JavaScript needed.
 * Version: 1.5.0
 * Author: Fancoolo
 * Author URI: https://github.com/krstivoja
 * License: ISC
 * Text Domain: fancoolo-fx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FANCOOLO_FX_VERSION', '1.5.0' );
define( 'FANCOOLO_FX_PATH', plugin_dir_path( __FILE__ ) );
define( 'FANCOOLO_FX_URL', plugin_dir_url( __FILE__ ) );

/**
 * ─── Settings helpers ───────────────────────────────────────────────
 */
function fancoolo_fx_get_settings() {
	return wp_parse_args( get_option( 'fancoolo_fx_settings', array() ), array(
		'scroll_start'     => 'top 85%',
		'scroll_once'      => '1',
		'section_selector' => 'section',
		'debug_markers'    => '0',
		'disable_mobile'   => '0',
		'mobile_breakpoint' => '768',
		'speed_multiplier' => '1',
		'respect_reduced_motion' => '1',
		'exclude_selectors' => '',
		'gutenberg_panel'  => '1',
		'tag_map'          => array(),
	) );
}

/**
 * ─── Frontend: Enqueue GSAP + FX scripts ────────────────────────────
 */
function fancoolo_fx_enqueue_scripts() {
	// 1. GSAP core
	wp_enqueue_script(
		'gsap',
		FANCOOLO_FX_URL . 'assets/gsap.min.js',
		array(),
		'3.14.2',
		true
	);

	// 2. ScrollTrigger
	wp_enqueue_script(
		'gsap-scrolltrigger',
		FANCOOLO_FX_URL . 'assets/ScrollTrigger.min.js',
		array( 'gsap' ),
		'3.14.2',
		true
	);

	// 3. SplitText
	wp_enqueue_script(
		'gsap-splittext',
		FANCOOLO_FX_URL . 'assets/SplitText.min.js',
		array( 'gsap' ),
		'3.14.2',
		true
	);

	// 4. Fancoolo FX
	wp_enqueue_script(
		'fancoolo-fx',
		FANCOOLO_FX_URL . 'assets/fx.js',
		array( 'gsap', 'gsap-scrolltrigger', 'gsap-splittext' ),
		FANCOOLO_FX_VERSION,
		true
	);

	// 5. Inject settings as __FX_CONFIG__ before fx.js
	$s = fancoolo_fx_get_settings();
	$config = array(
		'scrollStart'           => $s['scroll_start'],
		'scrollOnce'            => (bool) $s['scroll_once'],
		'sectionSelector'       => $s['section_selector'],
		'disableMobile'         => (bool) $s['disable_mobile'],
		'mobileBreakpoint'      => (int) $s['mobile_breakpoint'],
		'speedMultiplier'       => (float) $s['speed_multiplier'],
		'respectReducedMotion'  => (bool) $s['respect_reduced_motion'],
		'excludeSelectors'      => $s['exclude_selectors'],
	);

	// Build tagMap object from saved rows: { 'h1,h2': 'textReveal', 'img': 'reveal' }
	if ( ! empty( $s['tag_map'] ) ) {
		$tag_map_obj = array();
		foreach ( $s['tag_map'] as $row ) {
			$tag_map_obj[ $row['selector'] ] = $row['effect'];
		}
		$config['tagMap'] = (object) $tag_map_obj;
	}

	$config_js = 'window.__FX_CONFIG__ = ' . wp_json_encode( $config ) . ';';
	wp_add_inline_script( 'fancoolo-fx', $config_js, 'before' );

	// 6. Debug markers (admin only) — must run before fx.js init()
	if ( $s['debug_markers'] && is_user_logged_in() && current_user_can( 'manage_options' ) ) {
		wp_add_inline_script( 'fancoolo-fx', 'window.__FX_DEBUG_MARKERS__ = true;', 'before' );
	}

	// 7. Custom modifiers (if file exists and is not empty)
	$custom_file = fancoolo_fx_get_custom_file_path();
	if ( file_exists( $custom_file ) && filesize( $custom_file ) > 0 ) {
		$upload_dir = wp_upload_dir();
		$custom_url = $upload_dir['baseurl'] . '/fancoolo-fx/custom.js';
		wp_enqueue_script(
			'fancoolo-fx-custom',
			$custom_url,
			array( 'fancoolo-fx' ),
			filemtime( $custom_file ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'fancoolo_fx_enqueue_scripts' );

/**
 * ─── Block Editor: Enqueue FX Animation inspector panel ────────────
 */
function fancoolo_fx_enqueue_editor_assets() {
	$s = fancoolo_fx_get_settings();
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
add_action( 'enqueue_block_editor_assets', 'fancoolo_fx_enqueue_editor_assets' );

/**
 * ─── Helper: Get path to custom.js in uploads ──────────────────────
 */
function fancoolo_fx_get_custom_file_path() {
	$upload_dir = wp_upload_dir();
	return $upload_dir['basedir'] . '/fancoolo-fx/custom.js';
}

/**
 * ─── Admin: Register settings page under Appearance ─────────────────
 */
function fancoolo_fx_add_admin_page() {
	add_theme_page(
		'Fancoolo FX',
		'Fancoolo FX',
		'edit_theme_options',
		'fancoolo-fx',
		'fancoolo_fx_render_admin_page'
	);
}
add_action( 'admin_menu', 'fancoolo_fx_add_admin_page' );

/**
 * ─── Admin: Enqueue CodeMirror on our settings page ─────────────────
 */
function fancoolo_fx_admin_enqueue( $hook ) {
	if ( 'appearance_page_fancoolo-fx' !== $hook ) {
		return;
	}

	// Admin CSS
	wp_enqueue_style(
		'fancoolo-fx-admin',
		FANCOOLO_FX_URL . 'assets/admin.css',
		array(),
		FANCOOLO_FX_VERSION
	);

	// Admin JS
	wp_enqueue_script(
		'fancoolo-fx-admin',
		FANCOOLO_FX_URL . 'assets/admin.js',
		array( 'jquery' ),
		FANCOOLO_FX_VERSION,
		true
	);

	// WordPress built-in CodeMirror
	$settings = wp_enqueue_code_editor( array( 'type' => 'text/javascript' ) );

	if ( false !== $settings ) {
		wp_add_inline_script(
			'code-editor',
			sprintf(
				'jQuery(function($) {
					if ($("#fancoolo-fx-editor").length) {
						var editor = wp.codeEditor.initialize($("#fancoolo-fx-editor"), %s);
						// Auto-resize
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
add_action( 'admin_enqueue_scripts', 'fancoolo_fx_admin_enqueue' );

/**
 * ─── Admin: Handle form save ────────────────────────────────────────
 */
function fancoolo_fx_handle_save() {
	if ( ! isset( $_POST['fancoolo_fx_save'] ) ) {
		return;
	}

	if ( ! check_admin_referer( 'fancoolo_fx_save_action', 'fancoolo_fx_nonce' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	// Save custom JS
	$content = isset( $_POST['fancoolo_fx_code'] ) ? wp_unslash( $_POST['fancoolo_fx_code'] ) : '';

	$upload_dir = wp_upload_dir();
	$dir        = $upload_dir['basedir'] . '/fancoolo-fx';

	if ( ! file_exists( $dir ) ) {
		wp_mkdir_p( $dir );
	}

	$file = $dir . '/custom.js';
	file_put_contents( $file, $content );

	// Save settings
	$tag_map = array();
	if ( ! empty( $_POST['fancoolo_fx_tag_map'] ) && is_array( $_POST['fancoolo_fx_tag_map'] ) ) {
		foreach ( $_POST['fancoolo_fx_tag_map'] as $row ) {
			$selector = sanitize_text_field( $row['selector'] ?? '' );
			$effect   = sanitize_text_field( $row['effect'] ?? '' );
			if ( $selector !== '' && $effect !== '' ) {
				$tag_map[] = array( 'selector' => $selector, 'effect' => $effect );
			}
		}
	}

	$settings = array(
		'scroll_start'     => sanitize_text_field( $_POST['fancoolo_fx_scroll_start'] ?? 'top 85%' ),
		'scroll_once'      => isset( $_POST['fancoolo_fx_scroll_once'] ) ? '1' : '0',
		'section_selector' => sanitize_text_field( $_POST['fancoolo_fx_section_selector'] ?? 'section' ),
		'debug_markers'    => isset( $_POST['fancoolo_fx_debug_markers'] ) ? '1' : '0',
		'disable_mobile'   => isset( $_POST['fancoolo_fx_disable_mobile'] ) ? '1' : '0',
		'mobile_breakpoint' => sanitize_text_field( $_POST['fancoolo_fx_mobile_breakpoint'] ?? '768' ),
		'speed_multiplier' => sanitize_text_field( $_POST['fancoolo_fx_speed_multiplier'] ?? '1' ),
		'respect_reduced_motion' => isset( $_POST['fancoolo_fx_respect_reduced_motion'] ) ? '1' : '0',
		'exclude_selectors' => sanitize_text_field( $_POST['fancoolo_fx_exclude_selectors'] ?? '' ),
		'gutenberg_panel'  => isset( $_POST['fancoolo_fx_gutenberg_panel'] ) ? '1' : '0',
		'tag_map'          => $tag_map,
	);
	update_option( 'fancoolo_fx_settings', $settings );

	add_settings_error(
		'fancoolo_fx',
		'fancoolo_fx_saved',
		'Settings saved.',
		'success'
	);
}
add_action( 'admin_init', 'fancoolo_fx_handle_save' );

/**
 * ─── Admin: Render the settings page ────────────────────────────────
 */
function fancoolo_fx_render_admin_page() {
	$custom_file = fancoolo_fx_get_custom_file_path();
	$content     = file_exists( $custom_file ) ? file_get_contents( $custom_file ) : '';
	$s           = fancoolo_fx_get_settings();

	settings_errors( 'fancoolo_fx' );
	?>
	<!-- Styles and scripts enqueued via fancoolo_fx_admin_enqueue() -->

	<div class="wrap">
		<h1>Fancoolo FX</h1>
		<p>
			GSAP animation wrapper is active. Add <code>.fx-*</code> classes to blocks in Gutenberg
			and they will animate automatically.
		</p>


		<!-- ── Tabs ── -->
		<div class="ffx-tabs">
			<button class="ffx-tab active" data-tab="editor">Editor</button>
			<button class="ffx-tab" data-tab="config">Config Reference</button>
			<button class="ffx-tab" data-tab="classes">Classes Reference</button>
		</div>

		<!-- ═══ Editor Tab ═══ -->
		<div class="ffx-panel ffx-panel-editor active" data-panel="editor">
			<form method="post">
				<?php wp_nonce_field( 'fancoolo_fx_save_action', 'fancoolo_fx_nonce' ); ?>

				<!-- Editor main area -->
				<div class="ffx-editor-main">
					<input type="submit" name="fancoolo_fx_save" class="button button-primary ffx-save-btn" value="Save Changes">
					<textarea
						id="fancoolo-fx-editor"
						name="fancoolo_fx_code"
						rows="20"
						style="width: 100%; font-family: monospace;"
					><?php echo esc_textarea( $content ); ?></textarea>
				</div>

				<!-- Settings sidebar -->
				<div class="ffx-sidebar">
					<h3>Settings</h3>

					<label for="ffx-scroll-start">Scroll Start</label>
					<input type="text" id="ffx-scroll-start" name="fancoolo_fx_scroll_start" value="<?php echo esc_attr( $s['scroll_start'] ); ?>" placeholder="top 85%">
					<p class="ffx-hint">When scroll animations trigger (e.g. top 85%, top center)</p>

					<label for="ffx-section-selector">Section Selector</label>
					<input type="text" id="ffx-section-selector" name="fancoolo_fx_section_selector" value="<?php echo esc_attr( $s['section_selector'] ); ?>" placeholder="section">
					<p class="ffx-hint">Containers for bare-class triggering</p>

					<label for="ffx-exclude-selectors">Exclude Selectors</label>
					<input type="text" id="ffx-exclude-selectors" name="fancoolo_fx_exclude_selectors" value="<?php echo esc_attr( $s['exclude_selectors'] ); ?>" placeholder=".no-fx, .wp-block-navigation">
					<p class="ffx-hint">Elements matching these selectors are never animated</p>

					<hr>

					<div class="ffx-toggle">
						<input type="checkbox" id="ffx-scroll-once" name="fancoolo_fx_scroll_once" value="1" <?php checked( $s['scroll_once'], '1' ); ?>>
						<label for="ffx-scroll-once">Play once</label>
					</div>
					<p class="ffx-hint">Uncheck to replay animations on every scroll</p>

					<div class="ffx-toggle">
						<input type="checkbox" id="ffx-debug-markers" name="fancoolo_fx_debug_markers" value="1" <?php checked( $s['debug_markers'], '1' ); ?>>
						<label for="ffx-debug-markers">Debug markers</label>
					</div>
					<p class="ffx-hint">Show ScrollTrigger markers (admin only, visitors won't see them)</p>

					<div class="ffx-toggle">
						<input type="checkbox" id="ffx-disable-mobile" name="fancoolo_fx_disable_mobile" value="1" <?php checked( $s['disable_mobile'], '1' ); ?>>
						<label for="ffx-disable-mobile">Disable on mobile</label>
					</div>
					<div class="ffx-mobile-breakpoint" style="<?php echo $s['disable_mobile'] === '1' ? '' : 'display:none;'; ?>">
						<label for="ffx-mobile-breakpoint">Breakpoint (px)</label>
						<input type="text" id="ffx-mobile-breakpoint" name="fancoolo_fx_mobile_breakpoint" value="<?php echo esc_attr( $s['mobile_breakpoint'] ); ?>" placeholder="768" style="width: 80px; margin-bottom: 12px;">
					</div>

					<div class="ffx-toggle">
						<input type="checkbox" id="ffx-respect-reduced-motion" name="fancoolo_fx_respect_reduced_motion" value="1" <?php checked( $s['respect_reduced_motion'], '1' ); ?>>
						<label for="ffx-respect-reduced-motion">Respect reduced motion</label>
					</div>
					<p class="ffx-hint">Skip animations when OS has reduced motion enabled</p>

					<label for="ffx-speed-multiplier">Speed Multiplier</label>
					<select id="ffx-speed-multiplier" name="fancoolo_fx_speed_multiplier" style="width: 100%; margin-bottom: 12px;">
						<?php foreach ( array( '0.5' => '0.5x (faster)', '0.75' => '0.75x', '1' => '1x (default)', '1.25' => '1.25x', '1.5' => '1.5x', '2' => '2x (slower)' ) as $val => $lbl ) : ?>
							<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $s['speed_multiplier'], $val ); ?>><?php echo esc_html( $lbl ); ?></option>
						<?php endforeach; ?>
					</select>

					<div class="ffx-toggle">
						<input type="checkbox" id="ffx-gutenberg-panel" name="fancoolo_fx_gutenberg_panel" value="1" <?php checked( $s['gutenberg_panel'], '1' ); ?>>
						<label for="ffx-gutenberg-panel">Gutenberg integration</label>
					</div>
					<p class="ffx-hint">Show FX Animation panel in the block editor sidebar</p>

					<hr>

					<h3>Global Tag Map</h3>
					<p class="ffx-hint" style="margin-top: -8px;">Auto-animate elements by tag inside sections. No classes needed.</p>

					<div id="ffx-tagmap-rows">
						<?php foreach ( $s['tag_map'] as $i => $row ) : ?>
						<div class="ffx-tagmap-row">
							<input type="text" name="fancoolo_fx_tag_map[<?php echo $i; ?>][selector]" value="<?php echo esc_attr( $row['selector'] ); ?>" placeholder="h1,h2,h3">
							<select name="fancoolo_fx_tag_map[<?php echo $i; ?>][effect]">
								<?php foreach ( array(
									'textReveal' => 'Text Reveal',
									'reveal'     => 'Reveal',
									'spinReveal' => 'Spin Reveal',
									'bgReveal'   => 'BG Reveal',
									'scaleIn'    => 'Scale In',
									'fadeIn'     => 'Fade In',
									'blurIn'     => 'Blur In',
									'clipUp'     => 'Clip Up',
									'clipDown'   => 'Clip Down',
									'tiltIn'     => 'Tilt In',
									'typeWriter' => 'Type Writer',
									'drawSVG'    => 'Draw SVG',
									'parallax'   => 'Parallax',
									'splitWords' => 'Split Words',
									'slideIn'    => 'Slide In',
								) as $value => $label ) : ?>
									<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $row['effect'], $value ); ?>><?php echo esc_html( $label ); ?></option>
								<?php endforeach; ?>
							</select>
							<button type="button" class="ffx-tagmap-remove" title="Remove">&times;</button>
						</div>
						<?php endforeach; ?>
					</div>
					<button type="button" id="ffx-tagmap-add" class="button button-small">+ Add Rule</button>

					<hr>

					<h3>Import / Export</h3>
					<div class="ffx-import-export">
						<button type="button" id="ffx-export" class="button button-small" style="width: 100%; margin-bottom: 6px;">Export Settings</button>
						<button type="button" id="ffx-import-btn" class="button button-small" style="width: 100%; margin-bottom: 6px;">Import Settings</button>
						<input type="file" id="ffx-import-file" accept=".json" style="display: none;">
						<button type="button" id="ffx-reset" class="button button-small" style="width: 100%; color: #a00;">Reset to Defaults</button>
					</div>
				</div>
			</form>
		</div>

		<!-- ═══ Config Reference Tab ═══ -->
		<div class="ffx-panel" data-panel="config">

			<h3>Config Options</h3>
			<table class="widefat fixed striped" style="max-width: 700px;">
				<thead>
					<tr><th>Option</th><th>Default</th><th>Description</th></tr>
				</thead>
				<tbody>
					<tr><td><code>FX.config.tagMap</code></td><td><code>null</code></td><td>Auto-animate elements by tag/selector — no classes needed</td></tr>
					<tr><td><code>FX.config.sectionSelector</code></td><td><code>'section'</code></td><td>Containers for bare-class and tagMap triggering</td></tr>
					<tr><td><code>FX.config.scrollStart</code></td><td><code>'top 85%'</code></td><td>When scroll animations trigger (GSAP ScrollTrigger start format)</td></tr>
					<tr><td><code>FX.config.scrollOnce</code></td><td><code>true</code></td><td>Play once or replay on every scroll</td></tr>
					<tr><td><code>FX.config.excludeSelectors</code></td><td><code>''</code></td><td>CSS selectors for elements to never animate</td></tr>
					<tr><td><code>FX.config.disableMobile</code></td><td><code>false</code></td><td>Skip all animations on mobile</td></tr>
					<tr><td><code>FX.config.mobileBreakpoint</code></td><td><code>768</code></td><td>Width threshold for mobile detection (px)</td></tr>
					<tr><td><code>FX.config.speedMultiplier</code></td><td><code>1</code></td><td>Global duration multiplier (0.5 = faster, 2 = slower)</td></tr>
					<tr><td><code>FX.config.respectReducedMotion</code></td><td><code>true</code></td><td>Skip animations when OS prefers reduced motion</td></tr>
				</tbody>
			</table>

			<h3 style="margin-top: 24px;">JS API Functions</h3>
			<table class="widefat fixed striped" style="max-width: 700px;">
				<thead>
					<tr><th>Function</th><th>Description</th></tr>
				</thead>
				<tbody>
					<tr><td><code>FX.textReveal(el, opts)</code></td><td>Split text lines, masked reveal upward</td></tr>
					<tr><td><code>FX.reveal(el, opts)</code></td><td>Slide up with fade</td></tr>
					<tr><td><code>FX.spinReveal(el, opts)</code></td><td>Rotate and scale in</td></tr>
					<tr><td><code>FX.bgReveal(el, opts)</code></td><td>Background slide up</td></tr>
					<tr><td><code>FX.scaleIn(el, opts)</code></td><td>Scale up with fade</td></tr>
					<tr><td><code>FX.fadeIn(el, opts)</code></td><td>Opacity + subtle scale, no movement</td></tr>
					<tr><td><code>FX.blurIn(el, opts)</code></td><td>Fade in while deblurring</td></tr>
					<tr><td><code>FX.clipUp(el, opts)</code></td><td>Clip-path wipe from bottom</td></tr>
					<tr><td><code>FX.clipDown(el, opts)</code></td><td>Clip-path wipe from top</td></tr>
					<tr><td><code>FX.tiltIn(el, opts)</code></td><td>3D perspective reveal (scrub-based)</td></tr>
					<tr><td><code>FX.typeWriter(el, opts)</code></td><td>Character-by-character typing reveal</td></tr>
					<tr><td><code>FX.drawSVG(el, opts)</code></td><td>SVG stroke drawing animation</td></tr>
					<tr><td><code>FX.parallax(el, opts)</code></td><td>Scroll-linked Y parallax shift</td></tr>
					<tr><td><code>FX.splitWords(el, opts)</code></td><td>Word-by-word fade and slide up</td></tr>
					<tr><td><code>FX.slideIn(el, opts)</code></td><td>Horizontal slide from left or right</td></tr>
					<tr><td><code>FX.init()</code></td><td>Re-scan DOM — call after changing any config</td></tr>
				</tbody>
			</table>

			<h3 style="margin-top: 24px;">Examples <span style="font-weight:normal;color:#646970;font-size:13px;">(click code to copy)</span></h3>

			<h4 style="margin-top: 16px;">Auto-animate by tag</h4>
			<div class="ffx-copy-wrap">
				<button class="ffx-copy-btn" data-target="ex1">Copy</button>
				<pre class="ffx-pre" id="ex1">FX.config.tagMap = {
    'h1,h2,h3,h4,h5,h6': 'textReveal',
    'p,blockquote':       'textReveal',
    'img,video':          'reveal',
};
FX.config.sectionSelector = 'section, .wp-block-group';
FX.init();</pre>
			</div>

			<h4 style="margin-top: 16px;">Change scroll trigger position</h4>
			<div class="ffx-copy-wrap">
				<button class="ffx-copy-btn" data-target="ex2">Copy</button>
				<pre class="ffx-pre" id="ex2">FX.config.scrollStart = 'top center';
FX.init();</pre>
			</div>

			<h4 style="margin-top: 16px;">Replay animations on re-scroll</h4>
			<div class="ffx-copy-wrap">
				<button class="ffx-copy-btn" data-target="ex3">Copy</button>
				<pre class="ffx-pre" id="ex3">FX.config.scrollOnce = false;
FX.init();</pre>
			</div>

			<h4 style="margin-top: 16px;">Compound sequence</h4>
			<div class="ffx-copy-wrap">
				<button class="ffx-copy-btn" data-target="ex4">Copy</button>
				<pre class="ffx-pre" id="ex4">document.addEventListener('DOMContentLoaded', function () {
    var hero = document.querySelector('.wp-block-cover');
    if (!hero) return;

    FX.scaleIn(hero, {
        trigger: 'scroll',
        scrollTrigger: { trigger: hero }
    });

    var heading = hero.querySelector('h2');
    if (heading) {
        FX.textReveal(heading, {
            trigger: 'scroll',
            delay: 0.2,
            scrollTrigger: { trigger: hero }
        });
    }
});</pre>
			</div>
		</div>

		<!-- ═══ Classes Reference Tab ═══ -->
		<div class="ffx-panel" data-panel="classes">

			<p style="margin-bottom: 16px; color: #646970;">
				Add these in Gutenberg: select a block &rarr; Advanced &rarr; Additional CSS class(es). Click any class to copy it.
			</p>

			<!-- Text Reveal -->
			<div class="ffx-group-title">Text Reveal</div>
			<div class="ffx-class-row"><code data-copy>fx-text-reveal-pl</code><span class="ffx-desc">Page load — masked line-by-line reveal</span></div>
			<div class="ffx-class-row"><code data-copy>fx-text-reveal-st</code><span class="ffx-desc">Scroll triggered</span></div>
			<div class="ffx-class-row"><code data-copy>fx-text-reveal</code><span class="ffx-desc">Auto triggered inside a section</span></div>

			<!-- Reveal -->
			<div class="ffx-group-title">Reveal</div>
			<div class="ffx-class-row"><code data-copy>fx-reveal-pl</code><span class="ffx-desc">Page load — slide up with fade</span></div>
			<div class="ffx-class-row"><code data-copy>fx-reveal-st</code><span class="ffx-desc">Scroll triggered</span></div>
			<div class="ffx-class-row"><code data-copy>fx-reveal</code><span class="ffx-desc">Auto triggered inside a section</span></div>

			<!-- Spin Reveal -->
			<div class="ffx-group-title">Spin Reveal</div>
			<div class="ffx-class-row"><code data-copy>fx-spin-reveal-pl</code><span class="ffx-desc">Page load — rotate and scale in</span></div>
			<div class="ffx-class-row"><code data-copy>fx-spin-reveal-st</code><span class="ffx-desc">Scroll triggered</span></div>
			<div class="ffx-class-row"><code data-copy>fx-spin-reveal</code><span class="ffx-desc">Auto triggered inside a section</span></div>

			<!-- BG Reveal -->
			<div class="ffx-group-title">BG Reveal</div>
			<div class="ffx-class-row"><code data-copy>fx-bg-reveal-pl</code><span class="ffx-desc">Page load — background slide up</span></div>
			<div class="ffx-class-row"><code data-copy>fx-bg-reveal-st</code><span class="ffx-desc">Scroll triggered</span></div>
			<div class="ffx-class-row"><code data-copy>fx-bg-reveal</code><span class="ffx-desc">Auto triggered inside a section</span></div>

			<!-- Scale In -->
			<div class="ffx-group-title">Scale In</div>
			<div class="ffx-class-row"><code data-copy>fx-scale-in-pl</code><span class="ffx-desc">Page load — scale up with fade</span></div>
			<div class="ffx-class-row"><code data-copy>fx-scale-in-st</code><span class="ffx-desc">Scroll triggered</span></div>
			<div class="ffx-class-row"><code data-copy>fx-scale-in</code><span class="ffx-desc">Auto triggered inside a section</span></div>

			<!-- Fade In -->
			<div class="ffx-group-title">Fade In</div>
			<div class="ffx-class-row"><code data-copy>fx-fade-in-pl</code><span class="ffx-desc">Page load — opacity only, no movement</span></div>
			<div class="ffx-class-row"><code data-copy>fx-fade-in-st</code><span class="ffx-desc">Scroll triggered</span></div>
			<div class="ffx-class-row"><code data-copy>fx-fade-in</code><span class="ffx-desc">Auto triggered inside a section</span></div>

			<!-- Blur In -->
			<div class="ffx-group-title">Blur In</div>
			<div class="ffx-class-row"><code data-copy>fx-blur-in-pl</code><span class="ffx-desc">Page load — fade in while deblurring</span></div>
			<div class="ffx-class-row"><code data-copy>fx-blur-in-st</code><span class="ffx-desc">Scroll triggered</span></div>
			<div class="ffx-class-row"><code data-copy>fx-blur-in</code><span class="ffx-desc">Auto triggered inside a section</span></div>

			<!-- Clip Up -->
			<div class="ffx-group-title">Clip Reveal</div>
			<div class="ffx-class-row"><code data-copy>fx-clip-up-pl</code><span class="ffx-desc">Page load — clip-path wipe from bottom</span></div>
			<div class="ffx-class-row"><code data-copy>fx-clip-up-st</code><span class="ffx-desc">Scroll triggered</span></div>
			<div class="ffx-class-row"><code data-copy>fx-clip-up</code><span class="ffx-desc">Auto triggered inside a section</span></div>
			<div class="ffx-class-row"><code data-copy>fx-clip-down-pl</code><span class="ffx-desc">Page load — clip-path wipe from top</span></div>
			<div class="ffx-class-row"><code data-copy>fx-clip-down-st</code><span class="ffx-desc">Scroll triggered</span></div>
			<div class="ffx-class-row"><code data-copy>fx-clip-down</code><span class="ffx-desc">Auto triggered inside a section</span></div>

			<!-- Type Writer -->
			<div class="ffx-group-title">Type Writer</div>
			<div class="ffx-class-row"><code data-copy>fx-type-writer-pl</code><span class="ffx-desc">Page load — character-by-character typing</span></div>
			<div class="ffx-class-row"><code data-copy>fx-type-writer-st</code><span class="ffx-desc">Scroll triggered</span></div>
			<div class="ffx-class-row"><code data-copy>fx-type-writer</code><span class="ffx-desc">Auto triggered inside a section</span></div>

			<!-- Draw SVG -->
			<div class="ffx-group-title">Draw SVG</div>
			<div class="ffx-class-row"><code data-copy>fx-draw-svg-pl</code><span class="ffx-desc">Page load — SVG stroke drawing animation</span></div>
			<div class="ffx-class-row"><code data-copy>fx-draw-svg-st</code><span class="ffx-desc">Scroll triggered</span></div>
			<div class="ffx-class-row"><code data-copy>fx-draw-svg</code><span class="ffx-desc">Auto triggered inside a section</span></div>
			<div class="ffx-class-row"><code data-copy>fx-draw-svg-scrub</code><span class="ffx-desc">Draws progressively as you scroll (scrub-based)</span></div>

			<!-- Split Words -->
			<div class="ffx-group-title">Split Words</div>
			<div class="ffx-class-row"><code data-copy>fx-split-words-pl</code><span class="ffx-desc">Page load — word-by-word fade and slide</span></div>
			<div class="ffx-class-row"><code data-copy>fx-split-words-st</code><span class="ffx-desc">Scroll triggered</span></div>
			<div class="ffx-class-row"><code data-copy>fx-split-words</code><span class="ffx-desc">Auto triggered inside a section</span></div>

			<!-- Slide In -->
			<div class="ffx-group-title">Slide In</div>
			<div class="ffx-class-row"><code data-copy>fx-slide-left-pl</code><span class="ffx-desc">Page load — slide in from left</span></div>
			<div class="ffx-class-row"><code data-copy>fx-slide-left-st</code><span class="ffx-desc">Scroll triggered — from left</span></div>
			<div class="ffx-class-row"><code data-copy>fx-slide-left</code><span class="ffx-desc">Auto triggered inside a section — from left</span></div>
			<div class="ffx-class-row"><code data-copy>fx-slide-right-pl</code><span class="ffx-desc">Page load — slide in from right</span></div>
			<div class="ffx-class-row"><code data-copy>fx-slide-right-st</code><span class="ffx-desc">Scroll triggered — from right</span></div>
			<div class="ffx-class-row"><code data-copy>fx-slide-right</code><span class="ffx-desc">Auto triggered inside a section — from right</span></div>

			<!-- Tilt In -->
			<div class="ffx-group-title">Tilt In <span style="font-weight:normal;color:#646970;font-size:12px;">(scrub — tied to scroll position)</span></div>
			<div class="ffx-class-row"><code data-copy>fx-tilt-in-st</code><span class="ffx-desc">3D perspective reveal linked to scroll</span></div>
			<div class="ffx-class-row"><code data-copy>fx-tilt-in</code><span class="ffx-desc">Auto triggered inside a section</span></div>

			<!-- Parallax -->
			<div class="ffx-group-title">Parallax <span style="font-weight:normal;color:#646970;font-size:12px;">(scrub — tied to scroll position)</span></div>
			<div class="ffx-class-row"><code data-copy>fx-parallax-st</code><span class="ffx-desc">Parallax Y-shift linked to scroll</span></div>
			<div class="ffx-class-row"><code data-copy>fx-parallax</code><span class="ffx-desc">Auto triggered inside a section</span></div>

			<!-- Stagger All -->
			<div class="ffx-group-title" style="margin-top: 20px;">Stagger Children <span style="font-weight:normal;color:#646970;font-size:12px;">(pair with an effect class)</span></div>
			<div class="ffx-class-row"><code data-copy>fx-stagger-all-[img]</code><span class="ffx-desc">Target all img children — requires effect class</span></div>
			<div class="ffx-class-row"><code data-copy>fx-stagger-all-[img,p]</code><span class="ffx-desc">Target img and p children</span></div>
			<div class="ffx-class-row"><code data-copy>fx-stagger-all-[.card]</code><span class="ffx-desc">Target children by CSS class</span></div>

			<!-- Modifiers -->
			<div class="ffx-group-title" style="margin-top: 20px;">Modifiers <span style="font-weight:normal;color:#646970;font-size:12px;">(combine with any effect class)</span></div>
			<div class="ffx-class-row"><code data-copy>fx-duration-[1.5]</code><span class="ffx-desc">Custom duration (seconds)</span></div>
			<div class="ffx-class-row"><code data-copy>fx-delay-[0.3]</code><span class="ffx-desc">Delay before animating (seconds)</span></div>
			<div class="ffx-class-row"><code data-copy>fx-stagger-[0.25]</code><span class="ffx-desc">Delay between staggered siblings</span></div>
			<div class="ffx-class-row"><code data-copy>fx-ease-[power2.inOut]</code><span class="ffx-desc">GSAP easing function</span></div>
			<div class="ffx-class-row"><code data-copy>fx-start-[top center]</code><span class="ffx-desc">Scroll trigger start position</span></div>
			<div class="ffx-class-row"><code data-copy>fx-y-[80]</code><span class="ffx-desc">Parallax Y-shift intensity (parallax only)</span></div>
			<div class="ffx-class-row"><code data-copy>fx-scrub-[0.6]</code><span class="ffx-desc">Scrub smoothing (drawSVG scrub mode)</span></div>
		</div>

		<p style="margin-top: 24px; color: #646970;">
			This code loads after fx.js on the frontend. Leave empty to use defaults only.<br>
			<strong style="color:#1d2327;">Important:</strong> Always add <code>FX.init();</code> at the end when changing config — it re-scans the page with your new settings.
		</p>
		<p style="margin-top: 12px;">
			<a href="https://krstivoja.github.io/fancoolo-fx/documentation/" target="_blank">
				Full Documentation &rarr;
			</a>
		</p>
	</div>

	<!-- Admin JS enqueued via fancoolo_fx_admin_enqueue() -->
	<?php
}
