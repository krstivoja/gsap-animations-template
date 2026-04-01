<?php
/**
 * Plugin Name: Fancoolo FX
 * Plugin URI: https://github.com/krstivoja/fancoolo-fx
 * Description: A class-driven GSAP animation wrapper. Add CSS classes in Gutenberg and get animations — no JavaScript needed.
 * Version: 1.1.0
 * Author: Fancoolo
 * Author URI: https://github.com/krstivoja
 * License: ISC
 * Text Domain: fancoolo-fx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FANCOOLO_FX_VERSION', '1.1.0' );
define( 'FANCOOLO_FX_PATH', plugin_dir_path( __FILE__ ) );
define( 'FANCOOLO_FX_URL', plugin_dir_url( __FILE__ ) );

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

	// 5. Custom modifiers (if file exists and is not empty)
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

	$content = isset( $_POST['fancoolo_fx_code'] ) ? wp_unslash( $_POST['fancoolo_fx_code'] ) : '';

	$upload_dir = wp_upload_dir();
	$dir        = $upload_dir['basedir'] . '/fancoolo-fx';

	if ( ! file_exists( $dir ) ) {
		wp_mkdir_p( $dir );
	}

	$file = $dir . '/custom.js';
	file_put_contents( $file, $content );

	add_settings_error(
		'fancoolo_fx',
		'fancoolo_fx_saved',
		'Custom JavaScript saved.',
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

	settings_errors( 'fancoolo_fx' );
	?>
	<style>
		.ffx-tabs { display: flex; gap: 0; border-bottom: 1px solid #c3c4c7; margin: 20px 0 0; }
		.ffx-tab { padding: 10px 20px; cursor: pointer; font-weight: 600; font-size: 14px; color: #50575e; border: 1px solid transparent; border-bottom: none; margin-bottom: -1px; background: none; border-radius: 4px 4px 0 0; }
		.ffx-tab:hover { color: #1d2327; }
		.ffx-tab.active { background: #fff; border-color: #c3c4c7; color: #1d2327; }
		.ffx-panel { display: none; background: #fff; border: 1px solid #c3c4c7; border-top: none; padding: 24px; }
		.ffx-panel.active { display: block; }
		.ffx-panel-editor { padding: 0; position: relative; }
		.ffx-panel-editor .CodeMirror { border: none; }
		.ffx-save-btn { position: absolute; top: 12px; right: 12px; z-index: 10; }
		.ffx-copy-wrap { position: relative; }
		.ffx-copy-btn { position: absolute; top: 8px; right: 8px; padding: 4px 10px; font-size: 11px; cursor: pointer; background: #3c434a; color: #bbb; border: 1px solid #555; border-radius: 3px; }
		.ffx-copy-btn:hover { color: #fff; border-color: #888; }
		.ffx-copy-btn.copied { color: #46b450; border-color: #46b450; }
		.ffx-pre { background: #23282d; color: #eee; padding: 16px; border-radius: 4px; margin: 0; overflow-x: auto; font-size: 13px; line-height: 1.6; }
		.ffx-class-row { display: flex; align-items: center; gap: 8px; padding: 8px 12px; border-bottom: 1px solid #f0f0f1; }
		.ffx-class-row:last-child { border-bottom: none; }
		.ffx-class-row code { background: #f0f0f1; padding: 3px 8px; border-radius: 3px; font-size: 13px; cursor: pointer; transition: background 0.15s; }
		.ffx-class-row code:hover { background: #2271b1; color: #fff; }
		.ffx-class-row code.copied { background: #46b450; color: #fff; }
		.ffx-class-row .ffx-desc { color: #646970; font-size: 13px; }
		.ffx-group-title { font-weight: 600; font-size: 14px; padding: 12px 0 4px; color: #1d2327; border-bottom: 2px solid #2271b1; margin-bottom: 4px; }
	</style>

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
				<input type="submit" name="fancoolo_fx_save" class="button button-primary ffx-save-btn" value="Save Changes">
				<textarea
					id="fancoolo-fx-editor"
					name="fancoolo_fx_code"
					rows="20"
					style="width: 100%; font-family: monospace;"
				><?php echo esc_textarea( $content ); ?></textarea>
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

	<script>
	jQuery(function($) {
		// Fallback copy that works on HTTP (no clipboard API needed)
		function copyText(text) {
			var ta = document.createElement('textarea');
			ta.value = text;
			ta.style.position = 'fixed';
			ta.style.opacity = '0';
			document.body.appendChild(ta);
			ta.select();
			document.execCommand('copy');
			document.body.removeChild(ta);
		}

		// Tab switching
		$('.ffx-tab').on('click', function() {
			var tab = $(this).data('tab');
			$('.ffx-tab').removeClass('active');
			$(this).addClass('active');
			$('.ffx-panel').removeClass('active');
			$('.ffx-panel[data-panel="' + tab + '"]').addClass('active');
		});

		// Copy code blocks
		$('.ffx-copy-btn').on('click', function() {
			var btn = $(this);
			var target = $('#' + btn.data('target'));
			copyText(target.text());
			btn.text('Copied!').addClass('copied');
			setTimeout(function() { btn.text('Copy').removeClass('copied'); }, 1500);
		});

		// Copy class on click
		$('[data-copy]').on('click', function() {
			var el = $(this);
			var original = el.text();
			copyText(original);
			el.text('Copied!').addClass('copied');
			setTimeout(function() { el.text(original).removeClass('copied'); }, 1000);
		});
	});
	</script>
	<?php
}
