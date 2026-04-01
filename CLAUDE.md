# @fancoolo/fx — GSAP Animation SDK

## What this is
A class-driven GSAP animation framework. Users add CSS classes to HTML elements and get animations — no JS needed per page. Built for WordPress/Gutenberg where only class names (not data attributes or inline styles) can be added to blocks.

## Architecture

### SDK (`src/fx.js`)
- Self-contained ES module that imports gsap, ScrollTrigger, SplitText from npm
- Registers plugins, defines 5 effects, auto-scans DOM for `.fx-*` classes on DOMContentLoaded
- Exposes named exports AND `window.FX` global for script-tag usage
- Builds to IIFE (`dist/fx.min.js`) and ESM (`dist/fx.esm.min.js`) via esbuild

### Effects
| Effect | Function | What it does |
|--------|----------|-------------|
| Text Reveal | `textReveal()` | SplitText → lines → overflow:hidden wrappers → slide up |
| Reveal | `reveal()` | Slide up from y:80 + fade |
| Spin Reveal | `spinReveal()` | Rotation:-30 + scale:0.9 + fade |
| BG Reveal | `bgReveal()` | Slide up from y:100% + fade |
| Scale In | `scaleIn()` | Scale from 0.92 + fade |

### Class naming convention
- Effect + trigger: `.fx-{effect}-{trigger}` where trigger is `pl` (page load) or `st` (scroll trigger)
- Modifier overrides: `.fx-{property}-[{value}]` e.g. `fx-duration-[2]`, `fx-delay-[0.3]`
- Bracket syntax chosen because Gutenberg class field supports it and values can contain dots

### Auto-stagger
Elements with the same `.fx-*` class grouped under the same parent are staggered automatically (0.15s gap).

## Example project (`example/`)
- `example/index.html` — demo page showing all effects
- `example/src/animations.js` — sample project-specific code (ICAP site orchestration)
- Uses `file:..` dependency to reference the SDK locally

## Build commands
- `npm run build` — production IIFE bundle
- `npm run build:esm` — production ESM bundle
- `npm run build:all` — both
- `npm run build:dev` — dev with sourcemaps
- `npm run watch` — rebuild on changes

## Key decisions
- **CSS classes over data attributes**: Gutenberg block editor only exposes "Additional CSS classes" field
- **Bracket syntax for modifiers** (`fx-duration-[2]`): allows decimal values in class names, inspired by Tailwind arbitrary values
- **GSAP from npm, not CDN**: easier dependency management, single bundled output
- **esbuild**: chosen for speed and simplicity over webpack/rollup
- **IIFE + ESM dual output**: IIFE for WordPress script enqueue, ESM for modern bundled projects
- **Auto-init + manual API**: SDK scans DOM automatically but also exports functions for compound sequences
- **`window.FX` global**: allows project-specific JS files to call effects without import (useful for WordPress where scripts are enqueued separately)

## npm publishing
Package name is `@fancoolo/fx`. Run `npm publish` (or `npm publish --access public` for scoped packages) to publish. The `prepublishOnly` script builds automatically.

## Adding new effects
1. Add default config to `EFFECT_DEFAULTS` in `src/fx.js`
2. Create the effect function (follow existing pattern: resolveOptions → build tweenVars → conditional scrollTrigger → gsap.from)
3. Export it
4. Add to the `effects` map for class-based auto-discovery
5. Add to `window.FX`
