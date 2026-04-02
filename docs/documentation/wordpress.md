# WordPress & Gutenberg Integration

FX was designed with WordPress in mind. It uses CSS classes because Gutenberg's block editor only supports the "Additional CSS class(es)" field — no data attributes or inline styles.

## Fancoolo FX Plugin

The easiest way to use FX in WordPress is with the **Fancoolo FX plugin**. Activating it automatically enqueues GSAP, ScrollTrigger, SplitText, and fx.js on the frontend — no theme code needed.

The admin page lives at **Appearance → Fancoolo FX** and has three tabs:

1. **Editor** — A CodeMirror JavaScript editor for writing custom animation code, plus a settings sidebar on the right.
2. **Config Reference** — Tables of all config options, the JS API, and copy-ready code examples.
3. **Classes Reference** — Every available CSS class organized by effect, with click-to-copy.

Custom JS entered in the Editor tab is saved to `wp-content/uploads/fancoolo-fx/custom.js` and loaded after fx.js on the frontend.

## Plugin Settings

The **Editor** tab includes a settings sidebar on the right side. These controls configure FX behavior globally — no PHP or theme code required.

### Scroll Start

Text input. Default: `top 85%`.

Sets the ScrollTrigger start position for all scroll-triggered animations. The value follows ScrollTrigger's `start` syntax: the first keyword is the element edge, the second is the viewport edge.

Common values:
- `top 85%` — triggers when the element's top crosses 85% down the viewport (default)
- `top center` — triggers at the viewport midpoint
- `top bottom` — triggers as soon as the element enters the viewport

### Section Selector

Text input. Default: `section`.

The CSS selector that defines which containers enable bare-class and tagMap auto-triggering. Elements with a bare FX class (e.g. `fx-reveal` without `-st` or `-pl`) only animate if they are inside a matching container.

For WordPress sites, you typically want to include Gutenberg wrapper blocks:

```
section, .wp-block-group, .wp-block-cover
```

### Exclude Selectors

Text input. Default: empty.

CSS selectors for elements that should never be animated, even if they have FX classes or match a tagMap rule. Useful for excluding specific sections or elements that conflict with other scripts.

```
.no-animate, .hero-static
```

### Play Once

Checkbox. Default: checked.

When checked, scroll-triggered animations play once and do not replay when the user scrolls back up. Uncheck to replay animations every time the element re-enters the viewport.

### Debug Markers

Checkbox. Default: unchecked.

Shows ScrollTrigger start/end marker lines on the frontend so you can see exactly where animations trigger. Markers are only visible to logged-in administrators — visitors never see them.

### Disable on Mobile

Checkbox + breakpoint input. Default: unchecked, 768px.

When checked, all FX animations are skipped on screens narrower than the specified breakpoint (in pixels). Elements render in their final state with no animation. Useful for performance on low-powered devices or when animations feel distracting on small screens.

### Respect Reduced Motion

Checkbox. Default: checked.

When checked, FX skips all animations if the user's operating system has the "reduce motion" accessibility setting enabled (`prefers-reduced-motion: reduce`). Elements appear in their final state immediately.

### Speed Multiplier

Dropdown. Default: 1x.

Scales all animation durations globally. Available values:

| Value | Effect |
|-------|--------|
| 0.5x  | Twice as fast |
| 0.75x | 25% faster |
| 1x    | Normal speed (default) |
| 1.25x | 25% slower |
| 1.5x  | 50% slower |
| 2x    | Twice as slow |

This affects every FX animation on the site. Individual animations can still override duration with the `fx-duration-[value]` modifier class.

### Global Tag Map

A repeater field for mapping CSS selectors to effects. Each row has:

- **CSS Selector** — the elements to target (e.g. `h1,h2,h3`, `p`, `img,video`)
- **Effect** — dropdown of all available effects (textReveal, reveal, fadeIn, etc.)
- **Remove** button — deletes the row

This is the GUI equivalent of setting `FX.config.tagMap` in JavaScript. Elements matching the selector inside any section container are animated automatically — no classes needed.

Example configuration:

| Selector | Effect |
|----------|--------|
| `h1,h2,h3,h4,h5,h6` | textReveal |
| `p` | textReveal |
| `img,video` | reveal |

### Import / Export

Three actions at the bottom of the sidebar:

- **Export** — Downloads all current settings as a JSON file. Includes every sidebar setting, the tagMap rows, and the custom JS from the editor.
- **Import** — Uploads a previously exported JSON file and applies all settings.
- **Reset to Defaults** — Restores every setting to its factory default and clears the custom JS editor.

Use export/import to copy your FX configuration between sites or to back up before making changes.

## Enqueuing Scripts in Your Theme

If you prefer not to use the plugin, you can enqueue scripts manually in your theme's `functions.php`:

```php
function my_theme_enqueue_animations() {
    // 1. GSAP core
    wp_enqueue_script(
        'gsap',
        get_template_directory_uri() . '/assets/js/gsap.min.js',
        array(),
        '3.14.2',
        true
    );

    // 2. ScrollTrigger
    wp_enqueue_script(
        'gsap-scrolltrigger',
        get_template_directory_uri() . '/assets/js/ScrollTrigger.min.js',
        array('gsap'),
        '3.14.2',
        true
    );

    // 3. SplitText
    wp_enqueue_script(
        'gsap-splittext',
        get_template_directory_uri() . '/assets/js/SplitText.min.js',
        array('gsap'),
        '3.14.2',
        true
    );

    // 4. Fancoolo FX
    wp_enqueue_script(
        'fx-sdk',
        get_template_directory_uri() . '/assets/js/fx.js',
        array('gsap', 'gsap-scrolltrigger', 'gsap-splittext'),
        '1.0.0',
        true
    );

    // 5. Optional: project-specific animations
    wp_enqueue_script(
        'theme-animations',
        get_template_directory_uri() . '/assets/js/animations.js',
        array('fx-sdk'),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_animations');
```

**Key points:**
- Use the `array()` dependency parameter to ensure correct load order
- Set the last parameter to `true` to load scripts in the footer
- Copy GSAP files from `node_modules/gsap/dist/` to your theme's `assets/js/`

## Adding FX Config via WordPress

To set `__FX_CONFIG__` (for tagMap), use `wp_add_inline_script`:

```php
function my_theme_fx_config() {
    $config = array(
        'tagMap' => array(
            'h1,h2,h3,h4,h5,h6' => 'textReveal',
            'p'                   => 'textReveal',
            'img,video'           => 'reveal',
        ),
        'sectionSelector' => 'section, .wp-block-group',
    );

    wp_add_inline_script(
        'fx-sdk',
        'window.__FX_CONFIG__ = ' . wp_json_encode($config) . ';',
        'before'
    );
}
add_action('wp_enqueue_scripts', 'my_theme_fx_config');
```

## Adding Classes in Gutenberg

### Block sidebar

1. Select any block in the editor
2. Open the **Advanced** panel in the right sidebar
3. In the **Additional CSS class(es)** field, type your FX classes

Example for a Heading block:
```
fx-text-reveal-st
```

Example with modifiers:
```
fx-text-reveal-st fx-duration-[2] fx-stagger-[0.25]
```

### Group / Columns blocks

For grouped content, add the effect class to each child block, or use the section trigger by wrapping in a Group block:

```
<!-- wp:group -->
<div class="wp-block-group">
    <!-- wp:heading -->
    <h2 class="fx-text-reveal">Heading</h2>
    <!-- /wp:heading -->
    <!-- wp:paragraph -->
    <p class="fx-text-reveal">Paragraph</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
```

For this to work with bare classes, add `.wp-block-group` to the section selector:

```php
$config = array(
    'sectionSelector' => 'section, .wp-block-group',
);
```

## Common WordPress Selectors for sectionSelector

```php
$config = array(
    'sectionSelector' => implode(', ', array(
        'section',
        '.wp-block-group',
        '.wp-block-columns',
        '.wp-block-cover',
        '.entry-content > *',
    )),
);
```

## Theme File Structure

```
your-theme/
├── assets/
│   └── js/
│       ├── gsap.min.js           (from node_modules/gsap/dist/)
│       ├── ScrollTrigger.min.js  (from node_modules/gsap/dist/)
│       ├── SplitText.min.js      (from node_modules/gsap/dist/)
│       ├── fx.js                 (from src/)
│       └── animations.js         (your custom code, optional)
├── functions.php
├── style.css
└── templates/
```

## Performance Tips

- Load all scripts in the footer (`true` as the last parameter in `wp_enqueue_script`)
- Use `-pl` only for above-the-fold content; use `-st` or bare classes for everything else
- tagMap scans all sections on page load — on pages with hundreds of elements, prefer explicit classes
- Consider using `wp_enqueue_script` conditionally (only on pages that need animations):

```php
function my_theme_conditional_animations() {
    if (is_front_page() || is_page_template('template-animated.php')) {
        // Only load on front page or specific template
        my_theme_enqueue_animations();
    }
}
add_action('wp_enqueue_scripts', 'my_theme_conditional_animations');
```

## Gutenberg Block Theme (FSE)

For block themes using Full Site Editing, enqueue scripts the same way in `functions.php`. The classes work identically in the Site Editor's template parts and templates.

You can also add FX classes to block theme patterns:

```php
<!-- wp:heading {"className":"fx-text-reveal-st"} -->
<h2 class="fx-text-reveal-st">Pattern heading</h2>
<!-- /wp:heading -->
```
