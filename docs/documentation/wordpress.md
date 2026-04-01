# WordPress & Gutenberg Integration

FX was designed with WordPress in mind. It uses CSS classes because Gutenberg's block editor only supports the "Additional CSS class(es)" field — no data attributes or inline styles.

## Enqueuing Scripts in Your Theme

In your theme's `functions.php`:

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
