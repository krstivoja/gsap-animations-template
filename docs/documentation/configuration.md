# Configuration

FX has global config options that control triggering, performance, accessibility, and mobile behavior.

## Config Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `sectionSelector` | String | `'section'` | CSS selector for containers that enable bare-class and tagMap auto-triggering |
| `scrollStart` | String | `'top 85%'` | Default ScrollTrigger start position for all scroll-triggered animations |
| `scrollOnce` | Boolean | `true` | Whether scroll animations play once or replay every time the element enters the viewport |
| `tagMap` | Object or null | `null` | Map of CSS selectors to effect names for zero-class animation |
| `excludeSelectors` | String | `''` | CSS selector string for elements that should never be animated |
| `disableMobile` | Boolean | `false` | When true, skips all animations if viewport width is at or below `mobileBreakpoint` |
| `mobileBreakpoint` | Number | `768` | Width threshold in pixels for mobile detection. Only used when `disableMobile` is true |
| `speedMultiplier` | Number | `1` | Multiplies all animation durations globally. Values < 1 = faster, > 1 = slower |
| `respectReducedMotion` | Boolean | `true` | When true, skips all animations if the user's OS has `prefers-reduced-motion: reduce` enabled |

## Setting Configuration

Add a `<script>` block **before** the `fx.js` script tag:

```html
<script>
window.__FX_CONFIG__ = {
    sectionSelector: 'section',
    scrollOnce: true,
    excludeSelectors: '.no-fx, .wp-block-navigation',
    disableMobile: false,
    mobileBreakpoint: 768,
    speedMultiplier: 1,
    respectReducedMotion: true,
    tagMap: {
        'h1,h2,h3,h4,h5,h6': 'textReveal',
        'p,blockquote':       'textReveal',
        'img,video':          'reveal',
    }
};
</script>
<script src="src/fx.js"></script>
```

When FX loads, it reads `window.__FX_CONFIG__` and applies those settings before scanning the DOM.

---

## sectionSelector

Controls which containers act as scroll triggers for bare classes (no `-pl`/`-st` suffix).

### Default behavior

```html
<!-- This works because <section> is the default selector -->
<section>
    <h2 class="fx-text-reveal">Auto triggered</h2>
</section>
```

### Custom selectors

```html
<script>
window.__FX_CONFIG__ = {
    sectionSelector: 'section, .wp-block-group, .animate-section'
};
</script>
```

Now bare classes work inside `<section>`, `.wp-block-group`, or `.animate-section` containers.

### Disabling section trigger

```html
<script>
window.__FX_CONFIG__ = {
    sectionSelector: false
};
</script>
```

With this, only explicit `-pl` and `-st` classes are recognized.

---

## scrollStart

Controls **where** scroll-triggered animations fire. Uses GSAP's ScrollTrigger `start` format: `"triggerPosition viewportPosition"`.

### Default

```js
scrollStart: 'top 85%'
```

This means: animate when the **top** of the trigger element reaches **85% down** from the top of the viewport (near the bottom of the screen).

### Changing the default

```html
<script>
window.__FX_CONFIG__ = {
    scrollStart: 'top center'   // trigger at viewport center
};
</script>
```

### Common values

| Value | When it triggers |
|-------|-----------------|
| `'top 85%'` | Element near bottom of viewport (default, earliest) |
| `'top 70%'` | Element at 70% down |
| `'top center'` | Element at viewport center |
| `'top top'` | Element at very top of viewport (latest) |
| `'center center'` | Element vertically centered in viewport |

### Per-element override

Use the `fx-start-[]` modifier class to override on a specific element:

```html
<h2 class="fx-text-reveal-st fx-start-[top center]">Custom trigger point</h2>
```

---

## scrollOnce

Controls whether scroll-triggered animations replay when the element re-enters the viewport.

### Default

```js
scrollOnce: true   // play once, never replay
```

### Replaying animations

```html
<script>
window.__FX_CONFIG__ = {
    scrollOnce: false   // replay every time the element enters the viewport
};
</script>
```

---

## excludeSelectors

CSS selector string for elements that should never be animated, even if they have `.fx-*` classes or match a tagMap rule. Matched elements are skipped entirely during initialization.

### Default

```js
excludeSelectors: ''   // nothing excluded
```

### Excluding specific elements

```html
<script>
window.__FX_CONFIG__ = {
    excludeSelectors: '.no-fx, .wp-block-navigation'
};
</script>
```

Now any element matching `.no-fx` or `.wp-block-navigation` is ignored by FX:

```html
<section>
    <h2 class="fx-text-reveal-st">This animates</h2>
    <nav class="wp-block-navigation">This is skipped</nav>
    <p class="no-fx fx-reveal-st">This is also skipped</p>
</section>
```

### Combining with tagMap

`excludeSelectors` takes priority over tagMap. If an element matches both a tagMap rule and an exclude selector, it will not be animated:

```html
<script>
window.__FX_CONFIG__ = {
    excludeSelectors: '.site-header h1',
    tagMap: {
        'h1,h2,h3': 'textReveal',
    }
};
</script>

<header class="site-header">
    <h1>Not animated (excluded)</h1>
</header>

<section>
    <h1>Animated by tagMap</h1>
</section>
```

---

## disableMobile

When set to `true`, skips all FX animations if `window.innerWidth` is less than or equal to the `mobileBreakpoint` value. Useful for performance or when animations don't work well on small screens.

### Default

```js
disableMobile: false   // animations run on all screen sizes
```

### Disabling on mobile

```html
<script>
window.__FX_CONFIG__ = {
    disableMobile: true   // no animations on screens <= 768px wide
};
</script>
```

---

## mobileBreakpoint

Width threshold in pixels used for mobile detection. Only has an effect when `disableMobile` is `true`.

### Default

```js
mobileBreakpoint: 768
```

### Custom breakpoint

```html
<script>
window.__FX_CONFIG__ = {
    disableMobile: true,
    mobileBreakpoint: 1024   // treat tablets as mobile too
};
</script>
```

---

## speedMultiplier

Multiplies all animation durations globally. Affects every effect without needing per-element overrides.

### Default

```js
speedMultiplier: 1   // normal speed
```

### Examples

| Value | Effect |
|-------|--------|
| `0.5` | Half duration — animations are twice as fast |
| `1` | Normal speed (default) |
| `1.5` | 50% slower |
| `2` | Double duration — animations are twice as slow |

### Making animations snappier

```html
<script>
window.__FX_CONFIG__ = {
    speedMultiplier: 0.5   // everything runs at half the normal duration
};
</script>
```

### Slowing everything down

```html
<script>
window.__FX_CONFIG__ = {
    speedMultiplier: 2   // everything takes twice as long
};
</script>
```

**Note:** Per-element `fx-duration-[]` overrides still apply after the multiplier. The multiplier scales the base duration before any per-element override is checked.

---

## respectReducedMotion

When `true`, FX checks the user's operating system accessibility setting for `prefers-reduced-motion: reduce`. If enabled, all animations are skipped entirely — elements appear in their final state immediately.

### Default

```js
respectReducedMotion: true   // enabled by default for accessibility
```

This is on by default because users who enable reduced motion in their OS have explicitly requested less motion for accessibility or comfort reasons.

### Disabling the check

```html
<script>
window.__FX_CONFIG__ = {
    respectReducedMotion: false   // animations run regardless of OS setting
};
</script>
```

**Important:** Disabling this means users with vestibular disorders or motion sensitivities will still see all animations. Only disable this if you have an alternative accessibility mechanism in place.

---

## tagMap

Automatically animates elements inside sections based on their tag name — no classes needed on any element.

### Basic setup

```html
<script>
window.__FX_CONFIG__ = {
    tagMap: {
        'h1,h2,h3,h4,h5,h6': 'textReveal',
        'p':                   'textReveal',
        'img,video':           'reveal',
    }
};
</script>
<script src="src/fx.js"></script>

<!-- Now this section auto-animates with zero classes -->
<section>
    <h2>This heading gets textReveal</h2>
    <p>This paragraph gets textReveal</p>
    <img src="photo.jpg" />  <!-- This gets reveal -->
</section>
```

### Available effect names for tagMap

| Effect Name | What it does |
|------------|-------------|
| `textReveal` | Split text mask reveal |
| `reveal` | Slide up with fade |
| `spinReveal` | Rotate and scale in |
| `bgReveal` | Background slide up |
| `scaleIn` | Scale up with fade |

### Using CSS selectors (not just tags)

The keys in tagMap are CSS selectors, so you can be specific:

```js
window.__FX_CONFIG__ = {
    tagMap: {
        'h1,h2,h3':          'textReveal',
        '.card':              'scaleIn',
        'img:not(.no-anim)':  'reveal',
    }
};
```

### Limiting tagMap to specific sections

Combine `sectionSelector` with `tagMap` to restrict auto-animation:

```html
<script>
window.__FX_CONFIG__ = {
    sectionSelector: '.auto-animate',
    tagMap: {
        'h1,h2,h3,h4,h5,h6': 'textReveal',
        'p': 'textReveal',
        'img': 'reveal',
    }
};
</script>

<!-- This section auto-animates -->
<section class="auto-animate">
    <h2>Animated</h2>
    <p>Also animated</p>
</section>

<!-- This section does NOT auto-animate (no .auto-animate class) -->
<section>
    <h2>Not animated</h2>
</section>
```

---

## Priority: Classes vs tagMap

Elements with explicit `.fx-*` classes are always processed first. tagMap only picks up elements that haven't been animated yet:

```html
<section>
    <!-- This uses the explicit class (processed first) -->
    <h2 class="fx-text-reveal-pl">Page load animation</h2>

    <!-- This gets picked up by tagMap (no class) -->
    <p>Auto-animated by tag</p>
</section>
```

Full priority order:
1. `-pl` classes (page load)
2. `-st` classes (scroll trigger)
3. Bare classes inside sections
4. tagMap auto-animation

---

## Runtime configuration

You can also change config after FX has loaded and re-run initialization:

```js
// Change section selector
FX.config.sectionSelector = '.new-container';

// Enable tagMap
FX.config.tagMap = {
    'h2,h3': 'textReveal',
    'img': 'reveal',
};

// Adjust speed
FX.config.speedMultiplier = 0.75;

// Exclude navigation
FX.config.excludeSelectors = '.wp-block-navigation';

// Re-scan the DOM with new config
FX.init();
```

**Note:** `FX.init()` does not skip previously animated elements when called again. Use this for dynamically added content, not for re-animating existing elements.
