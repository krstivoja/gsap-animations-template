# @fancoolo/fx — Animation SDK

A lightweight, class-driven GSAP animation SDK. Add a CSS class to any element and it animates — no JavaScript needed per page.

## Install

```bash
npm install
```

## Usage

Load GSAP + plugins + FX as separate script tags — no build step needed:

```html
<!-- 1. GSAP core -->
<script src="node_modules/gsap/dist/gsap.min.js"></script>
<!-- 2. GSAP plugins -->
<script src="node_modules/gsap/dist/ScrollTrigger.min.js"></script>
<script src="node_modules/gsap/dist/SplitText.min.js"></script>
<!-- 3. FX Animation SDK -->
<script src="src/fx.js"></script>
```

Then add classes in your HTML. Done. The SDK auto-initializes on DOMContentLoaded.

## Quick Start

```html
<h1 class="fx-text-reveal-pl">Hello World</h1>
```

The heading animates with a masked line-reveal on page load.

## Available Effects

| Effect | Page Load | Scroll Trigger | Description |
|--------|-----------|----------------|-------------|
| Text Reveal | `.fx-text-reveal-pl` | `.fx-text-reveal-st` | Split lines, mask, slide up |
| Reveal | `.fx-reveal-pl` | `.fx-reveal-st` | Slide up + fade |
| Spin Reveal | `.fx-spin-reveal-pl` | `.fx-spin-reveal-st` | Rotate + scale in |
| BG Reveal | `.fx-bg-reveal-pl` | `.fx-bg-reveal-st` | Background slide up |
| Scale In | `.fx-scale-in-pl` | `.fx-scale-in-st` | Scale up + fade |

**Three trigger modes:**
- `-pl` — **Page load**: animates when the DOM is ready
- `-st` — **Scroll trigger**: animates when the element enters the viewport
- **No suffix** — **Section trigger**: bare `.fx-text-reveal` inside a `<section>` is auto scroll-triggered using the section as the trigger

## How Scroll Triggering Works

When the SDK sees a scroll-triggered element (`-st` suffix or bare class inside a section), it creates a GSAP ScrollTrigger with these defaults:

- **`start: 'top 85%'`** — the animation fires when the top of the element (or its section) reaches 85% down from the top of the viewport
- **`once: true`** — plays once, doesn't replay on re-scroll

For grouped siblings (same class, same parent), the parent is used as the shared trigger — so all items animate together with stagger, rather than each triggering independently.

## Section Auto-Trigger

Elements with bare `.fx-*` classes (no `-pl`/`-st` suffix) inside a `<section>` are automatically scroll-triggered using the section as the trigger:

```html
<section>
    <h2 class="fx-text-reveal">This auto-triggers on scroll</h2>
    <p class="fx-text-reveal">No suffix needed inside a section</p>
    <img src="photo.jpg" class="fx-reveal" />
</section>
```

Change the container selector via config:

```js
FX.config.sectionSelector = '.animate-section';  // only sections with this class
FX.config.sectionSelector = 'section, .wp-block-group';  // multiple selectors
```

## Tag-Based Auto-Animation

For zero-class animation, configure `tagMap` to automatically animate elements by their tag name inside sections:

```html
<!-- Set config BEFORE the SDK script loads -->
<script>
    window.__FX_CONFIG__ = {
        tagMap: {
            'h1,h2,h3,h4,h5,h6': 'textReveal',
            'p,blockquote':       'textReveal',
            'img,video':          'reveal',
        }
    };
</script>
<script src="dist/fx.min.js"></script>
```

Or configure after load and re-init:

```js
FX.config.tagMap = { 'h1,h2,h3': 'textReveal', 'img': 'reveal' };
FX.init();
```

Elements already animated by explicit `.fx-*` classes are skipped — tagMap only picks up unhandled elements.

## Auto-Stagger

Sibling elements with the same class are automatically staggered (0.15s between each):

```html
<div>
    <p class="fx-text-reveal-st">First paragraph</p>
    <p class="fx-text-reveal-st">Second paragraph</p>
    <p class="fx-text-reveal-st">Third paragraph</p>
</div>
```

## Modifier Classes

Override timing per-element using modifier classes (Gutenberg-friendly — no inline styles needed):

| Class | Default | Description |
|-------|---------|-------------|
| `fx-duration-[n]` | `1.2` (text) / `1` (others) | Animation duration in seconds |
| `fx-delay-[n]` | `0` | Start delay in seconds |
| `fx-stagger-[n]` | `0.1` | Delay between staggered items |
| `fx-ease-[name]` | `power3.out` | GSAP easing function |

```html
<h2 class="fx-text-reveal-st fx-duration-[2] fx-stagger-[0.25]">
    Slower and wider stagger
</h2>
```

Add these in Gutenberg via the "Additional CSS class(es)" field alongside the effect class.

## JavaScript API

For compound sequences or dynamic content, use the `FX` global:

```js
FX.textReveal(document.querySelector('.hero-title'), {
    trigger: 'scroll',
    delay: 0.3,
    scrollTrigger: { trigger: '.hero-section' }
});
```

### API Reference

All functions accept `(element, options)`:

| Function | Options |
|----------|---------|
| `textReveal(el, opts)` | `duration`, `ease`, `stagger`, `delay`, `trigger`, `scrollTrigger` |
| `reveal(el, opts)` | `y` (default 80), `duration`, `ease`, `delay`, `trigger`, `scrollTrigger` |
| `spinReveal(el, opts)` | `rotation` (default -30), `scale` (default 0.9), `duration`, `ease`, `delay`, `trigger`, `scrollTrigger` |
| `bgReveal(el, opts)` | `duration`, `ease`, `delay`, `trigger`, `scrollTrigger` |
| `scaleIn(el, opts)` | `scale` (default 0.92), `duration`, `ease`, `delay`, `trigger`, `scrollTrigger` |

Set `trigger: 'scroll'` to enable ScrollTrigger. Pass `scrollTrigger: { trigger: someEl }` to use a different trigger element.

## Using in a New Project

1. Copy this repo (or `npm install`)
2. Add the 4 script tags (gsap, ScrollTrigger, SplitText, fx.js)
3. Add `.fx-*` classes in your HTML

For compound sequences, create a project-specific JS file loaded after fx.js:

```js
// animations.js — loaded after fx.js
document.addEventListener('DOMContentLoaded', function () {
    var hero = document.querySelector('.hero');
    if (hero) {
        FX.scaleIn(hero.querySelector('.card'), { trigger: 'scroll', scrollTrigger: { trigger: hero } });
        FX.textReveal(hero.querySelector('h2'), { trigger: 'scroll', delay: 0.2, scrollTrigger: { trigger: hero } });
    }
});
```

## File Structure

```
├── package.json                ← npm deps (gsap)
├── node_modules/gsap/dist/     ← GSAP core + plugins (loaded via script tags)
├── src/fx.js                   ← FX Animation SDK
├── example/
│   ├── index.html              ← Demo page
│   └── src/animations.js       ← Sample project-specific code
├── CLAUDE.md                   ← Project context for Claude
└── README.md
```

## WordPress / Gutenberg

This SDK uses CSS classes which you can add via the "Additional CSS class(es)" field in the block sidebar. No data attributes or inline styles needed.
