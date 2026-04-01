# fancoolo-fx — Fancoolo FX

## What this is
A class-driven GSAP animation wrapper for WordPress and static sites. Users add CSS classes to HTML elements and get animations — no JS needed per page. Built for WordPress/Gutenberg where only class names (not data attributes or inline styles) can be added to blocks.

## Architecture

### SDK (`src/fx.js`)
- Plain IIFE — no ES imports, no build step, no bundling
- Expects gsap, ScrollTrigger, SplitText loaded as globals via script tags before it
- Registers plugins, defines 10 effects, auto-scans DOM for `.fx-*` classes on DOMContentLoaded
- Exposes `window.FX` global

### Effects
| Effect | Function | What it does |
|--------|----------|-------------|
| Text Reveal | `textReveal()` | SplitText → lines → overflow:hidden wrappers → slide up |
| Reveal | `reveal()` | Slide up from y:80 + fade |
| Spin Reveal | `spinReveal()` | Rotation:-30 + scale:0.9 + fade |
| BG Reveal | `bgReveal()` | Slide up from y:100% + fade |
| Scale In | `scaleIn()` | Scale from 0.92 + fade |
| Fade In | `fadeIn()` | Opacity + subtle scale (0.95→1), no movement |
| Blur In | `blurIn()` | Fade + deblur (camera focus effect) |
| Clip Up | `clipUp()` | Clip-path wipe from bottom |
| Clip Down | `clipDown()` | Clip-path wipe from top |
| Tilt In | `tiltIn()` | 3D perspective reveal, **scrub-based** (tied to scroll position) |

### Three trigger modes
1. **Explicit suffix**: `.fx-{effect}-pl` (page load) or `.fx-{effect}-st` (scroll trigger)
2. **Bare class in section**: `.fx-{effect}` (no suffix) inside a `<section>` → auto scroll-triggered using the section as trigger
3. **Tag map**: `FX.config.tagMap = { 'h1,h2': 'textReveal' }` → zero-class auto-animation by tag name inside sections

**Note:** Both `-st` and bare classes trigger on the **element itself**, not the parent/section. The section is only used to scope which elements get picked up.

### Stagger Children modifier
`fx-stagger-all-[selector]` targets child elements by CSS selector. Must be paired with an effect class on the same container. Without an effect class, does nothing.

```html
<div class="fx-stagger-all-[img] fx-reveal-st">
```

### Modifier overrides
- `.fx-{property}-[{value}]` e.g. `fx-duration-[2]`, `fx-delay-[0.3]`, `fx-start-[top center]`
- Bracket syntax chosen because Gutenberg class field supports it and values can contain dots

### Config (`FX.config`)
- `sectionSelector` (default: `'section'`) — CSS selector for containers that enable bare-class and tag-map auto-triggering
- `scrollStart` (default: `'top 85%'`) — when scroll animations trigger
- `scrollOnce` (default: `true`) — play once or replay on every scroll
- `tagMap` (default: `null`) — map of CSS selectors → effect names for zero-class animation

### Pre-configuration
Set `window.__FX_CONFIG__` before the FX script loads to configure `sectionSelector`, `scrollStart`, `scrollOnce`, and `tagMap` without needing JS after load.

### Auto-stagger
Elements with the same `.fx-*` class grouped under the same parent are staggered automatically (0.15s gap).

### Processing priority
init() uses a `processed` Set to avoid double-animating:
1. `-pl` classes (page load)
2. `-st` classes (scroll trigger — element triggers itself)
3. Bare classes in sections (element triggers itself, section only scopes)
4. **tiltIn** (scrub-based, processed before tagMap so images aren't grabbed by tagMap first)
5. tagMap auto-animation
6. `fx-stagger-all-[selector]` modifier

## Project structure
```
├── src/fx.js                          ← Core wrapper (the npm package)
├── package.json                       ← npm: fancoolo-fx
├── docs/                              ← GitHub Pages site
│   ├── index.html                     ← Landing page with live demos
│   ├── vendor/                        ← GSAP + fx.js copies for the demo
│   └── documentation/                 ← Docs site (Tailwind + marked.js)
│       ├── index.html                 ← Single page shell
│       ├── layout.js                  ← Builds sidebar, loads .md by hash
│       └── *.md                       ← Content files (edit these)
├── wp-plugin/fancoolo-fx/             ← WordPress plugin
│   ├── fancoolo-fx.php                ← Single-file plugin
│   ├── assets/                        ← GSAP + fx.js copies
│   └── readme.txt                     ← WP plugin readme
├── skills/SKILL.md                    ← Claude Code skill (copy to ~/.claude/skills/fancoolo-fx/)
├── .github/workflows/release.yml      ← Tags → GitHub Release with plugin zip
├── CLAUDE.md                          ← This file
└── README.md
```

## WordPress plugin
- Activating loads GSAP + fx.js automatically on frontend
- Admin page: Appearance → Fancoolo FX
- Three tabs: Editor (CodeMirror), Config Reference, Classes Reference
- Custom JS saved to `wp-content/uploads/fancoolo-fx/custom.js`
- Click-to-copy on all classes and code examples

## Publishing
- **npm**: `npm publish --otp=CODE` (package: fancoolo-fx)
- **GitHub Release**: push a tag like `1.2.0` → Actions zips wp-plugin → attaches to release
- **GitHub Pages**: auto-deploys from `docs/` folder

## No build step
No bundler, no compilation. GSAP + plugins are loaded as separate script tags from `node_modules/gsap/dist/`, then `src/fx.js` runs as a plain IIFE.

## Key decisions
- **No build step**: plain script tags in the correct order — simpler to maintain, debug, and integrate with WordPress `wp_enqueue_script`
- **CSS classes over data attributes**: Gutenberg block editor only exposes "Additional CSS classes" field
- **Bracket syntax for modifiers** (`fx-duration-[2]`): allows decimal values in class names, inspired by Tailwind arbitrary values
- **GSAP from npm, not CDN**: version-locked via package.json, files served from node_modules
- **`window.FX` global**: allows project-specific JS files to call effects without import (useful for WordPress where scripts are enqueued separately)
- **-st triggers element itself**: not the parent container, to work correctly in WordPress where parents are large wrappers
- **tiltIn is scrub-based**: uses gsap.fromTo with scrub, processed before tagMap to prevent tagMap from grabbing elements first

## Adding new effects
1. Add default config to `EFFECT_DEFAULTS` in `src/fx.js`
2. Create the effect function (follow existing pattern: resolveOptions → build tweenVars → conditional scrollTrigger → gsap.from)
3. Add to the `effects` map for class-based auto-discovery
4. Add to `effectsByName` for tagMap lookups
5. Add to `window.FX`
6. Copy fx.js to `docs/vendor/fx.js` and `wp-plugin/fancoolo-fx/assets/fx.js`
7. Update: effects.md, skills/SKILL.md, WP plugin classes/API tables
8. Bump version in package.json + fancoolo-fx.php, tag, push, npm publish
