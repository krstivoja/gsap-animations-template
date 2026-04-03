<?php

namespace FancooloFX;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AdminPage {

	public static function render() {
		$custom_file = Settings::get_custom_file_path();
		$content     = file_exists( $custom_file ) ? file_get_contents( $custom_file ) : '';
		$s           = Settings::get();

		settings_errors( 'fancoolo_fx' );
		?>
	<!-- Styles and scripts enqueued via Admin::enqueue() -->

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
								<?php foreach ( self::get_effects_list() as $value => $label ) : ?>
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

			<?php self::render_classes_reference(); ?>
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
		<?php
	}

	public static function get_effects_list() {
		return array(
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
		);
	}

	private static function render_classes_reference() {
		$groups = array(
			'Text Reveal'    => array(
				'fx-text-reveal-pl'  => 'Page load — masked line-by-line reveal',
				'fx-text-reveal-st'  => 'Scroll triggered',
				'fx-text-reveal'     => 'Auto triggered inside a section',
			),
			'Reveal'         => array(
				'fx-reveal-pl'       => 'Page load — slide up with fade',
				'fx-reveal-st'       => 'Scroll triggered',
				'fx-reveal'          => 'Auto triggered inside a section',
			),
			'Spin Reveal'    => array(
				'fx-spin-reveal-pl'  => 'Page load — rotate and scale in',
				'fx-spin-reveal-st'  => 'Scroll triggered',
				'fx-spin-reveal'     => 'Auto triggered inside a section',
			),
			'BG Reveal'      => array(
				'fx-bg-reveal-pl'    => 'Page load — background slide up',
				'fx-bg-reveal-st'    => 'Scroll triggered',
				'fx-bg-reveal'       => 'Auto triggered inside a section',
			),
			'Scale In'       => array(
				'fx-scale-in-pl'     => 'Page load — scale up with fade',
				'fx-scale-in-st'     => 'Scroll triggered',
				'fx-scale-in'        => 'Auto triggered inside a section',
			),
			'Fade In'        => array(
				'fx-fade-in-pl'      => 'Page load — opacity only, no movement',
				'fx-fade-in-st'      => 'Scroll triggered',
				'fx-fade-in'         => 'Auto triggered inside a section',
			),
			'Blur In'        => array(
				'fx-blur-in-pl'      => 'Page load — fade in while deblurring',
				'fx-blur-in-st'      => 'Scroll triggered',
				'fx-blur-in'         => 'Auto triggered inside a section',
			),
			'Clip Reveal'    => array(
				'fx-clip-up-pl'      => 'Page load — clip-path wipe from bottom',
				'fx-clip-up-st'      => 'Scroll triggered',
				'fx-clip-up'         => 'Auto triggered inside a section',
				'fx-clip-down-pl'    => 'Page load — clip-path wipe from top',
				'fx-clip-down-st'    => 'Scroll triggered',
				'fx-clip-down'       => 'Auto triggered inside a section',
			),
			'Type Writer'    => array(
				'fx-type-writer-pl'  => 'Page load — character-by-character typing',
				'fx-type-writer-st'  => 'Scroll triggered',
				'fx-type-writer'     => 'Auto triggered inside a section',
			),
			'Draw SVG'       => array(
				'fx-draw-svg-pl'     => 'Page load — SVG stroke drawing animation',
				'fx-draw-svg-st'     => 'Scroll triggered',
				'fx-draw-svg'        => 'Auto triggered inside a section',
				'fx-draw-svg-scrub'  => 'Draws progressively as you scroll (scrub-based)',
			),
			'Split Words'    => array(
				'fx-split-words-pl'  => 'Page load — word-by-word fade and slide',
				'fx-split-words-st'  => 'Scroll triggered',
				'fx-split-words'     => 'Auto triggered inside a section',
			),
			'Slide In'       => array(
				'fx-slide-left-pl'   => 'Page load — slide in from left',
				'fx-slide-left-st'   => 'Scroll triggered — from left',
				'fx-slide-left'      => 'Auto triggered inside a section — from left',
				'fx-slide-right-pl'  => 'Page load — slide in from right',
				'fx-slide-right-st'  => 'Scroll triggered — from right',
				'fx-slide-right'     => 'Auto triggered inside a section — from right',
			),
			'Tilt In|scrub — tied to scroll position' => array(
				'fx-tilt-in-st'      => '3D perspective reveal linked to scroll',
				'fx-tilt-in'         => 'Auto triggered inside a section',
			),
			'Parallax|scrub — tied to scroll position' => array(
				'fx-parallax-st'     => 'Parallax Y-shift linked to scroll',
				'fx-parallax'        => 'Auto triggered inside a section',
			),
		);

		foreach ( $groups as $title => $classes ) {
			$parts = explode( '|', $title );
			$label = $parts[0];
			$sub   = isset( $parts[1] ) ? ' <span style="font-weight:normal;color:#646970;font-size:12px;">(' . esc_html( $parts[1] ) . ')</span>' : '';
			echo '<div class="ffx-group-title">' . esc_html( $label ) . $sub . '</div>';
			foreach ( $classes as $cls => $desc ) {
				echo '<div class="ffx-class-row"><code data-copy>' . esc_html( $cls ) . '</code><span class="ffx-desc">' . esc_html( $desc ) . '</span></div>';
			}
		}

		// Stagger Children
		echo '<div class="ffx-group-title" style="margin-top: 20px;">Stagger Children <span style="font-weight:normal;color:#646970;font-size:12px;">(pair with an effect class)</span></div>';
		$stagger = array(
			'fx-stagger-all-[img]'    => 'Target all img children — requires effect class',
			'fx-stagger-all-[img,p]'  => 'Target img and p children',
			'fx-stagger-all-[.card]'  => 'Target children by CSS class',
		);
		foreach ( $stagger as $cls => $desc ) {
			echo '<div class="ffx-class-row"><code data-copy>' . esc_html( $cls ) . '</code><span class="ffx-desc">' . esc_html( $desc ) . '</span></div>';
		}

		// Modifiers
		echo '<div class="ffx-group-title" style="margin-top: 20px;">Modifiers <span style="font-weight:normal;color:#646970;font-size:12px;">(combine with any effect class)</span></div>';
		$modifiers = array(
			'fx-duration-[1.5]'       => 'Custom duration (seconds)',
			'fx-delay-[0.3]'          => 'Delay before animating (seconds)',
			'fx-stagger-[0.25]'       => 'Delay between staggered siblings',
			'fx-ease-[power2.inOut]'  => 'GSAP easing function',
			'fx-start-[top center]'   => 'Scroll trigger start position',
			'fx-y-[80]'              => 'Parallax Y-shift intensity (parallax only)',
			'fx-scrub-[0.6]'         => 'Scrub smoothing (drawSVG scrub mode)',
		);
		foreach ( $modifiers as $cls => $desc ) {
			echo '<div class="ffx-class-row"><code data-copy>' . esc_html( $cls ) . '</code><span class="ffx-desc">' . esc_html( $desc ) . '</span></div>';
		}
	}
}
