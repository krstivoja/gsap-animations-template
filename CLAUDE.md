# @fancoolo/fx — Fancoolo FX

## What this is
A class-driven GSAP animation wrapper for WordPress and static sites. Users add CSS classes to HTML elements and get animations — no JS needed per page. Built for WordPress/Gutenberg where only class names (not data attributes or inline styles) can be added to blocks.

## Architecture

### Core (`src/fx.js`)
- Plain IIFE — no ES imports, no build step, no bundling
- Expects gsap, ScrollTrigger, SplitText loaded as globals via script tags before it
- Registers plugins, defines 5 effects, auto-scans DOM for `.fx-*` classes on DOMContentLoaded
- Exposes `window.FX` global

### Effects
| Effect | Function | What it does |
|--------|----------|-------------|
| Text Reveal | `textReveal()` | SplitText → lines → overflow:hidden wrappers → slide up |
| Reveal | `reveal()` | Slide up from y:80 + fade |
| Spin Reveal | `spinReveal()` | Rotation:-30 + scale:0.9 + fade |
| BG Reveal | `bgReveal()` | Slide up from y:100% + fade |
| Scale In | `scaleIn()` | Scale from 0.92 + fade |

### Three trigger modes
1. **Explicit suffix**: `.fx-{effect}-pl` (page load) or `.fx-{effect}-st` (scroll trigger)
2. **Bare class in section**: `.fx-{effect}` (no suffix) inside a `<section>` → auto scroll-triggered using the section as trigger
3. **Tag map**: `FX.config.tagMap = { 'h1,h2': 'textReveal' }` → zero-class auto-animation by tag name inside sections

### Modifier overrides
- `.fx-{property}-[{value}]` e.g. `fx-duration-[2]`, `fx-delay-[0.3]`
- Bracket syntax chosen because Gutenberg class field supports it and values can contain dots

### Config (`FX.config`)
- `sectionSelector` (default: `'section'`) — CSS selector for containers that enable bare-class and tag-map auto-triggering
- `tagMap` (default: `null`) — map of CSS selectors → effect names for zero-class animation

### Pre-configuration
Set `window.__FX_CONFIG__` before the FX script loads to configure `sectionSelector` and `tagMap` without needing JS after load.

### Auto-stagger
Elements with the same `.fx-*` class grouped under the same parent are staggered automatically (0.15s gap).

### Processing priority
init() uses a `processed` Set to avoid double-animating: explicit `-pl`/`-st` first, then bare classes in sections, then tagMap. Each step skips already-processed elements.

## Example project (`example/`)
- `example/index.html` — demo page showing all effects
- `example/src/animations.js` — sample project-specific code (ICAP site orchestration)
- Uses `file:..` dependency to reference FX locally

## No build step
No bundler, no compilation. GSAP + plugins are loaded as separate script tags from `node_modules/gsap/dist/`, then `src/fx.js` runs as a plain IIFE.

## Key decisions
- **No build step**: plain script tags in the correct order — simpler to maintain, debug, and integrate with WordPress `wp_enqueue_script`
- **CSS classes over data attributes**: Gutenberg block editor only exposes "Additional CSS classes" field
- **Bracket syntax for modifiers** (`fx-duration-[2]`): allows decimal values in class names, inspired by Tailwind arbitrary values
- **GSAP from npm, not CDN**: version-locked via package.json, files served from node_modules
- **`window.FX` global**: allows project-specific JS files to call effects (useful for WordPress where scripts are enqueued separately)

## Adding new effects
1. Add default config to `EFFECT_DEFAULTS` in `src/fx.js`
2. Create the effect function (follow existing pattern: resolveOptions → build tweenVars → conditional scrollTrigger → gsap.from)
3. Add to the `effects` map for class-based auto-discovery
4. Add to `effectsByName` for tagMap lookups
5. Add to `window.FX`
