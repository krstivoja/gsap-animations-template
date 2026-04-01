# @fancoolo/fx — Animation SDK

A lightweight, class-driven GSAP animation SDK. Add a CSS class to any element and it animates — no JavaScript needed per page.

## Install

```bash
npm install @fancoolo/fx
```

## Build (for SDK development)

```bash
npm run build         # Minified → dist/fx.min.js
npm run build:esm     # ESM bundle → dist/fx.esm.min.js
npm run build:all     # Both formats
npm run build:dev     # Unminified + sourcemaps
npm run watch         # Rebuild on changes
```

## Usage

### Option A: Script tag (WordPress, static sites)

Load the single bundled file — includes GSAP + ScrollTrigger + SplitText + FX:

```html
<script src="node_modules/@fancoolo/fx/dist/fx.min.js"></script>
```

Then add classes in your HTML. Done.

### Option B: ES module import (bundled projects)

```js
import { textReveal, reveal, spinReveal, bgReveal, scaleIn } from '@fancoolo/fx';
```

The SDK auto-initializes on DOMContentLoaded — any `.fx-*` classes in the DOM are picked up automatically. Use the JS API for compound sequences or dynamic content.

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

**Trigger suffixes:**
- `-pl` — **Page load**: animates when the DOM is ready
- `-st` — **Scroll trigger**: animates when the element enters the viewport

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

For compound sequences or dynamic content, use named exports or the `FX` global:

```js
import { textReveal, reveal } from '@fancoolo/fx';

// Or via global: FX.textReveal(el, opts)

textReveal(document.querySelector('.hero-title'), {
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

## Using in a new project

```bash
npm install @fancoolo/fx
```

Create your project-specific animation file:

```js
// src/animations.js
import { textReveal, reveal, scaleIn } from '@fancoolo/fx';

document.addEventListener('DOMContentLoaded', () => {
    // Custom compound sequences go here
    const hero = document.querySelector('.hero');
    if (hero) {
        scaleIn(hero.querySelector('.card'), { trigger: 'scroll', scrollTrigger: { trigger: hero } });
        textReveal(hero.querySelector('h2'), { trigger: 'scroll', delay: 0.2, scrollTrigger: { trigger: hero } });
    }
});
```

Most animations need zero JS — just add classes in your HTML/Gutenberg.

## File Structure

```
@fancoolo/fx (this package)
├── src/fx.js              ← SDK source
├── dist/fx.min.js         ← IIFE bundle (script tag)
├── dist/fx.esm.min.js     ← ESM bundle (import)
└── README.md

Your project
├── package.json            ← depends on @fancoolo/fx
├── src/animations.js       ← Your project-specific sequences
├── dist/                   ← Your built bundle
└── *.html / templates      ← Add .fx-* classes here
```

## WordPress / Gutenberg

This SDK uses CSS classes which you can add via the "Additional CSS class(es)" field in the block sidebar. No data attributes or inline styles needed.
