---
name: fancoolo-fx
description: Fancoolo FX — a class-driven GSAP animation wrapper for WordPress and static sites. Use when the user asks about FX animations, fx-text-reveal, fx-reveal, fx-spin-reveal, fx-bg-reveal, fx-scale-in classes, FX.config, tagMap, sectionSelector, modifier classes like fx-duration-[n], or building animations with the fancoolo-fx package. Also use when adding scroll-triggered or page-load animations via CSS classes in WordPress/Gutenberg.
license: MIT
---

# Fancoolo FX

## When to Use This Skill

Apply when working with the `fancoolo-fx` package or when the user mentions:
- FX animation classes (`fx-text-reveal`, `fx-reveal`, `fx-spin-reveal`, `fx-bg-reveal`, `fx-scale-in`)
- Trigger suffixes (`-pl`, `-st`, bare classes in sections)
- FX modifier classes (`fx-duration-[n]`, `fx-delay-[n]`, `fx-stagger-[n]`, `fx-ease-[name]`, `fx-start-[pos]`)
- `FX.config`, `__FX_CONFIG__`, `tagMap`, `sectionSelector`, `scrollStart`, `scrollOnce`
- Class-based GSAP animations for WordPress/Gutenberg

**Related skills:** For GSAP core use **gsap-core**; for ScrollTrigger use **gsap-scrolltrigger**; for timelines use **gsap-timeline**; for GSAP plugins (SplitText etc.) use **gsap-plugins**; for WordPress blocks use **wp-block-development**.

## What Fancoolo FX Is

A class-driven GSAP animation wrapper. Users add CSS classes to HTML elements and get animations — no JS needed per page. Designed for WordPress/Gutenberg where only class names (not data attributes or inline styles) can be added to blocks.

**No build step.** GSAP + plugins load as separate script tags, then `src/fx.js` runs as a plain IIFE.

## Installation

```bash
npm install
```

Load in this order:
```html
<script src="node_modules/gsap/dist/gsap.min.js"></script>
<script src="node_modules/gsap/dist/ScrollTrigger.min.js"></script>
<script src="node_modules/gsap/dist/SplitText.min.js"></script>
<script src="src/fx.js"></script>
```

## Effects

| Effect | Class prefix | JS function | What it does | Duration | Ease |
|--------|-------------|-------------|-------------|----------|------|
| Text Reveal | `fx-text-reveal` | `FX.textReveal()` | SplitText lines, overflow-hidden mask, slide up | 1.2s | power3.out |
| Reveal | `fx-reveal` | `FX.reveal()` | Slide up from y:80 + fade | 1s | power3.out |
| Spin Reveal | `fx-spin-reveal` | `FX.spinReveal()` | Rotation:-30 + scale:0.9 + fade | 1.4s | power3.out |
| BG Reveal | `fx-bg-reveal` | `FX.bgReveal()` | Slide up from y:100% + fade | 1s | power3.out |
| Scale In | `fx-scale-in` | `FX.scaleIn()` | Scale from 0.92 + fade | 1s | power3.out |
| Fade In | `fx-fade-in` | `FX.fadeIn()` | Opacity only, no movement | 0.8s | power2.out |
| Blur In | `fx-blur-in` | `FX.blurIn()` | Fade in + deblur (camera focus effect) | 1.2s | power2.out |
| Clip Up | `fx-clip-up` | `FX.clipUp()` | Clip-path wipe from bottom | 1s | power3.inOut |
| Clip Down | `fx-clip-down` | `FX.clipDown()` | Clip-path wipe from top | 1s | power3.inOut |

## Stagger Children Modifier

`fx-stagger-all-[selector]` targets child elements by CSS selector. Must be paired with an effect class.

```html
<div class="fx-stagger-all-[img] fx-reveal-st">       <!-- stagger images with reveal -->
<div class="fx-stagger-all-[h2,p] fx-text-reveal-st"> <!-- stagger text elements -->
<div class="fx-stagger-all-[.card] fx-blur-in-st">    <!-- stagger by class -->
```

Without an effect class, `fx-stagger-all` does nothing.

## Three Trigger Modes

### 1. Explicit suffix
- `-pl` (page load): `<h1 class="fx-text-reveal-pl">` — animates immediately on DOMContentLoaded
- `-st` (scroll trigger): `<h2 class="fx-text-reveal-st">` — animates when element scrolls into viewport

### 2. Bare class in section
`<section><h2 class="fx-text-reveal">` — no suffix needed inside a `<section>`. Auto scroll-triggered using the section as trigger.

### 3. Tag map (zero classes)
```html
<script>
window.__FX_CONFIG__ = {
    tagMap: {
        'h1,h2,h3,h4,h5,h6': 'textReveal',
        'p,blockquote': 'textReveal',
        'img,video': 'reveal',
    }
};
</script>
<script src="src/fx.js"></script>
```
Elements inside sections animate automatically by tag — no classes needed.

## Modifier Classes (Gutenberg-friendly)

Override timing per-element using bracket syntax:

| Modifier | Default | Example |
|----------|---------|---------|
| `fx-duration-[n]` | 1.2 (text) / 1 (others) | `fx-duration-[2]` |
| `fx-delay-[n]` | 0 | `fx-delay-[0.3]` |
| `fx-stagger-[n]` | 0.1 | `fx-stagger-[0.25]` |
| `fx-ease-[name]` | power3.out | `fx-ease-[power2.inOut]` |
| `fx-start-[pos]` | top 85% | `fx-start-[top center]` |

Example: `<h2 class="fx-text-reveal-st fx-duration-[2] fx-stagger-[0.25]">`

## Config (`FX.config`)

| Option | Default | Description |
|--------|---------|-------------|
| `sectionSelector` | `'section'` | CSS selector for containers that enable bare-class and tagMap triggering |
| `scrollStart` | `'top 85%'` | Default ScrollTrigger start position |
| `scrollOnce` | `true` | Play once or replay on re-scroll |
| `tagMap` | `null` | Map of CSS selectors to effect names |

Pre-configure via `window.__FX_CONFIG__` before the script loads, or modify `FX.config` at runtime and call `FX.init()`.

## JavaScript API

All functions accept `(element, options)`:

```js
FX.textReveal(el, { trigger: 'scroll', delay: 0.2, scrollTrigger: { trigger: parentEl } });
FX.reveal(el, { y: 120 });
FX.spinReveal(el, { rotation: -45, scale: 0.8 });
FX.scaleIn(el, { trigger: 'scroll', scale: 0.85 });
FX.bgReveal(el);
FX.init(); // Re-scan DOM
```

Set `trigger: 'scroll'` to enable ScrollTrigger. Pass `scrollTrigger: { trigger: someEl }` to use a different trigger element.

## Auto-stagger

Siblings with the same `.fx-*` class under the same parent are staggered automatically (0.15s gap between each).

## Processing Priority

`init()` uses a `processed` Set to avoid double-animating:
1. `-pl` classes (page load) — first
2. `-st` classes (scroll trigger) — second
3. Bare classes in sections — third
4. tagMap — last

Each step skips already-processed elements.

## WordPress / Gutenberg

- Uses CSS classes only — add via "Additional CSS class(es)" in the block sidebar
- No data attributes or inline styles (Gutenberg doesn't support them)
- Bracket syntax for modifiers works in the class field
- Enqueue via `wp_enqueue_script` with proper dependencies

```php
wp_enqueue_script('gsap', get_template_directory_uri() . '/assets/js/gsap.min.js', array(), '3.14.2', true);
wp_enqueue_script('gsap-scrolltrigger', get_template_directory_uri() . '/assets/js/ScrollTrigger.min.js', array('gsap'), '3.14.2', true);
wp_enqueue_script('gsap-splittext', get_template_directory_uri() . '/assets/js/SplitText.min.js', array('gsap'), '3.14.2', true);
wp_enqueue_script('fx', get_template_directory_uri() . '/assets/js/fx.js', array('gsap', 'gsap-scrolltrigger', 'gsap-splittext'), '1.0.0', true);
```

## Adding New Effects

1. Add default config to `EFFECT_DEFAULTS` in `src/fx.js`
2. Create the effect function (pattern: resolveOptions, build tweenVars, conditional scrollTrigger, gsap.from)
3. Add to `effects` map for class-based auto-discovery
4. Add to `effectsByName` for tagMap lookups
5. Add to `window.FX`

## Key Architecture Decisions

- **No build step**: plain script tags — simpler for WordPress `wp_enqueue_script`
- **CSS classes over data attributes**: Gutenberg only exposes "Additional CSS classes" field
- **Bracket syntax** (`fx-duration-[2]`): decimal values work in class names, inspired by Tailwind
- **GSAP from npm**: version-locked via package.json, served from node_modules
- **`window.FX` global**: project-specific JS can call effects without imports
