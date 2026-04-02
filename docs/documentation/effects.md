# Effects Reference

FX ships with 15 animation effects. Most trigger on page load (`-pl`), on scroll (`-st`), or automatically inside a section (bare class). Tilt In and Parallax are special — they're scrub-based and tied to scroll position.

## Quick Reference Table

| Effect | Class | JS Function | Description |
|--------|-------|-------------|-------------|
| Text Reveal | `fx-text-reveal` | `FX.textReveal()` | Split text into lines, mask reveal upward |
| Reveal | `fx-reveal` | `FX.reveal()` | Slide up from below with fade |
| Spin Reveal | `fx-spin-reveal` | `FX.spinReveal()` | Rotate and scale in |
| BG Reveal | `fx-bg-reveal` | `FX.bgReveal()` | Background container slide up |
| Scale In | `fx-scale-in` | `FX.scaleIn()` | Scale up from smaller size with fade |
| Fade In | `fx-fade-in` | `FX.fadeIn()` | Opacity + subtle scale, no movement |
| Blur In | `fx-blur-in` | `FX.blurIn()` | Fade in while deblurring |
| Clip Up | `fx-clip-up` | `FX.clipUp()` | Clip-path wipe revealing from bottom |
| Clip Down | `fx-clip-down` | `FX.clipDown()` | Clip-path wipe revealing from top |
| Tilt In | `fx-tilt-in` | `FX.tiltIn()` | 3D perspective reveal (scrub-based) |
| Type Writer | `fx-type-writer` | `FX.typeWriter()` | Character-by-character typing reveal |
| Draw SVG | `fx-draw-svg` | `FX.drawSVG()` | Stroke-dashoffset animation for SVG paths |
| Split Words | `fx-split-words` | `FX.splitWords()` | Word-by-word fade and slide up |
| Slide In | `fx-slide-left` / `fx-slide-right` | `FX.slideIn()` | Horizontal slide from left or right |
| Parallax | `fx-parallax` | `FX.parallax()` | Scrub-based Y-shift on scroll |

---

## Text Reveal

Splits text into lines using GSAP's SplitText, wraps each line in an `overflow: hidden` mask, and slides each line up from below.

**Class:** `fx-text-reveal-pl` | `fx-text-reveal-st` | `fx-text-reveal`

**Defaults:**

| Property | Value |
|----------|-------|
| Duration | `1.2s` |
| Ease | `power3.out` |
| Stagger | `0.1s` |
| From Y | `100%` |

**Usage:**

```html
<!-- Heading reveals on page load -->
<h1 class="fx-text-reveal-pl">Welcome to our site</h1>

<!-- Paragraph reveals on scroll -->
<p class="fx-text-reveal-st">
    This paragraph will split into lines and each line
    will slide up when scrolled into view.
</p>
```

**Best for:** Headings, paragraphs, quotes, any text content.

**How it works internally:**
1. SplitText splits the element's text into individual lines
2. Each line gets wrapped in a `<div>` with `overflow: hidden`
3. GSAP animates each line from `y: 100%` (below the mask) to `y: 0`
4. Lines are staggered by 0.1s by default
5. After completion, inline transforms are cleaned up

---

## Reveal

Slides the element up from below with a fade-in. A general-purpose entrance animation.

**Class:** `fx-reveal-pl` | `fx-reveal-st` | `fx-reveal`

**Defaults:**

| Property | Value |
|----------|-------|
| Duration | `1s` |
| Ease | `power3.out` |
| From Y | `80px` |
| From Opacity | `0` |

**Usage:**

```html
<!-- Single image -->
<img src="hero.jpg" class="fx-reveal-pl" />

<!-- Staggered grid -->
<div class="grid">
    <img src="photo-1.jpg" class="fx-reveal-st" />
    <img src="photo-2.jpg" class="fx-reveal-st" />
    <img src="photo-3.jpg" class="fx-reveal-st" />
</div>
```

**Best for:** Images, cards, containers, any block-level element.

**JS override:** Control the Y offset:
```js
FX.reveal(el, { y: 120 }); // Slide up from further away
```

---

## Spin Reveal

Rotates and scales the element in from a slight offset. Creates a playful entrance.

**Class:** `fx-spin-reveal-pl` | `fx-spin-reveal-st` | `fx-spin-reveal`

**Defaults:**

| Property | Value |
|----------|-------|
| Duration | `1.4s` |
| Ease | `power3.out` |
| From Rotation | `-30deg` |
| From Scale | `0.9` |
| From Opacity | `0` |

**Usage:**

```html
<!-- Badge spins in on load -->
<div class="badge fx-spin-reveal-pl">New</div>

<!-- Multiple badges stagger on scroll -->
<div class="tags">
    <span class="tag fx-spin-reveal-st">Design</span>
    <span class="tag fx-spin-reveal-st">Motion</span>
    <span class="tag fx-spin-reveal-st">Code</span>
</div>
```

**Best for:** Badges, tags, icons, decorative elements, small UI pieces.

**JS override:** Customize rotation and scale:
```js
FX.spinReveal(el, { rotation: -45, scale: 0.8 });
```

---

## BG Reveal

Slides a background container up from the bottom. Designed for full-width or hero background panels.

**Class:** `fx-bg-reveal-pl` | `fx-bg-reveal-st` | `fx-bg-reveal`

**Defaults:**

| Property | Value |
|----------|-------|
| Duration | `1s` |
| Ease | `power3.out` |
| From Y | `100%` |
| From Opacity | `0` |

**Usage:**

```html
<div class="hero" style="position: relative; overflow: hidden;">
    <!-- Background slides up -->
    <div class="hero-bg fx-bg-reveal-pl" style="position: absolute; inset: 0;"></div>
    <!-- Content on top -->
    <div class="hero-content" style="position: relative; z-index: 1;">
        <h1 class="fx-text-reveal-pl">Hello</h1>
    </div>
</div>
```

**Best for:** Hero backgrounds, section backgrounds, overlay panels.

**Note:** The parent container should have `overflow: hidden` and `position: relative` for the slide-up effect to look correct.

---

## Scale In

Scales the element up from a slightly smaller size with a fade. Subtle and elegant.

**Class:** `fx-scale-in-pl` | `fx-scale-in-st` | `fx-scale-in`

**Defaults:**

| Property | Value |
|----------|-------|
| Duration | `1s` |
| Ease | `power3.out` |
| From Scale | `0.92` |
| From Opacity | `0` |

**Usage:**

```html
<!-- Card scales in on scroll -->
<div class="card fx-scale-in-st">
    <h3>Feature title</h3>
    <p>Feature description</p>
</div>

<!-- Glass panel scales in on load -->
<div class="glass-panel fx-scale-in-pl">
    Content here
</div>
```

**Best for:** Cards, panels, featured sections, glass-morphism containers.

**JS override:** Control the starting scale:
```js
FX.scaleIn(el, { scale: 0.85 }); // More dramatic scale
```

---

## Fade In

Pure opacity fade — no movement, no transform. The most subtle reveal.

**Class:** `fx-fade-in-pl` | `fx-fade-in-st` | `fx-fade-in`

**Defaults:**

| Property | Value |
|----------|-------|
| Duration | `0.8s` |
| Ease | `power2.out` |
| From Opacity | `0` |

**Usage:**

```html
<div class="overlay fx-fade-in-pl"></div>
<img src="bg.jpg" class="fx-fade-in-st" />
```

**Best for:** Backgrounds, overlays, decorative elements that shouldn't draw attention with movement.

---

## Blur In

Fades in while deblurring — like a camera coming into focus.

**Class:** `fx-blur-in-pl` | `fx-blur-in-st` | `fx-blur-in`

**Defaults:**

| Property | Value |
|----------|-------|
| Duration | `1.2s` |
| Ease | `power2.out` |
| From Blur | `12px` |
| From Opacity | `0` |

**Usage:**

```html
<h1 class="fx-blur-in-pl">Hero headline</h1>
<img src="feature.jpg" class="fx-blur-in-st" />
```

**Best for:** Images, headings, hero text — organic and cinematic feel.

**JS override:** Control blur amount:
```js
FX.blurIn(el, { blur: 20 }); // Heavier blur
```

---

## Clip Up

Reveals the element with a clip-path wipe from the bottom edge upward. Clean geometric reveal with no movement or opacity change.

**Class:** `fx-clip-up-pl` | `fx-clip-up-st` | `fx-clip-up`

**Defaults:**

| Property | Value |
|----------|-------|
| Duration | `1s` |
| Ease | `power3.inOut` |
| From clipPath | `inset(100% 0 0 0)` |

**Usage:**

```html
<img src="photo.jpg" class="fx-clip-up-st" />
<div class="hero-image fx-clip-up-pl"></div>
```

**Best for:** Images, sections — premium architectural feel.

---

## Clip Down

Same as Clip Up but reveals from the top edge downward.

**Class:** `fx-clip-down-pl` | `fx-clip-down-st` | `fx-clip-down`

**Defaults:**

| Property | Value |
|----------|-------|
| Duration | `1s` |
| Ease | `power3.inOut` |
| From clipPath | `inset(0 0 100% 0)` |

**Usage:**

```html
<div class="dropdown-panel fx-clip-down-st">Content</div>
```

**Best for:** Dropdown panels, top-aligned reveals.

---

## Stagger Children

`fx-stagger-all-[selector]` is a trigger modifier that targets child elements by CSS selector. It must be paired with an effect class — without one, nothing happens.

**Usage:**

```html
<!-- Stagger all images with reveal -->
<div class="fx-stagger-all-[img] fx-reveal-st">
    <img src="1.jpg" />
    <img src="2.jpg" />
    <img src="3.jpg" />
</div>

<!-- Stagger headings and paragraphs with text reveal -->
<div class="fx-stagger-all-[h2,p] fx-text-reveal-st">
    <h2>Title</h2>
    <p>Description</p>
</div>

<!-- Stagger by CSS class with blur effect -->
<div class="fx-stagger-all-[.card] fx-blur-in-st">
    <div class="card">A</div>
    <div class="card">B</div>
    <div class="card">C</div>
</div>
```

**Best for:** Card grids, image galleries, feature lists — no need to add classes to every child element.

---

## Tilt In

3D perspective reveal tied to scroll position. Unlike other effects, this one is **scrub-based** — the animation progress follows your scroll position. Scroll down and the element gradually stands up; scroll back up and it tilts back.

**Class:** `fx-tilt-in-st` | `fx-tilt-in`

**Defaults:**

| Property | Value |
|----------|-------|
| Duration | `1.4s` |
| Ease | `power3.out` |
| From rotationX | `45deg` |
| From Scale | `0.8` |
| From Opacity | `0` |
| Perspective | `1000px` |
| Transform Origin | `center bottom` |
| Scrub | `0.6` (smooth lag) |
| Scroll Range | `top 85%` → `top 20%` |

**Usage:**

```html
<!-- Image tilts into place as you scroll -->
<img src="photo.jpg" class="fx-tilt-in-st" />

<!-- Card with 3D perspective -->
<div class="card fx-tilt-in-st">Content</div>
```

**Best for:** Images, cards, hero panels — creates depth and a premium feel.

**Note:** This is the only scrub-based effect. It doesn't use `-pl` (page load) since it needs scroll to drive it. The animation reverses when scrolling back up.

---

## Type Writer

Reveals text character by character using GSAP's SplitText, simulating a typewriter effect. Each character appears one at a time with no easing for a mechanical feel.

**Class:** `fx-type-writer-pl` | `fx-type-writer-st` | `fx-type-writer`

**Defaults:**

| Property | Value |
|----------|-------|
| Duration | `0.05s` (per character) |
| Ease | `none` |
| Stagger | `0.03s` |
| From Opacity | `0` |

**Usage:**

```html
<!-- Hero heading types out on page load -->
<h1 class="fx-type-writer-pl">Welcome to the future</h1>

<!-- Tagline types on scroll -->
<p class="fx-type-writer-st">Building the web, one pixel at a time.</p>
```

**Best for:** Headings, taglines, hero text — creates a terminal or editorial feel.

**How it works internally:**
1. SplitText splits the element's text into individual characters
2. All characters start invisible (`opacity: 0`)
3. GSAP staggers each character to `opacity: 1` with `ease: "none"` for a sharp on/off reveal
4. The stagger timing (0.03s) controls the typing speed
5. After completion, inline styles are cleaned up

**JS override:** Control the typing speed:
```js
FX.typeWriter(el, { stagger: 0.06 }); // Slower typing
FX.typeWriter(el, { stagger: 0.01 }); // Rapid typing
```

---

## Draw SVG

Animates SVG path strokes using `stroke-dashoffset`, drawing the path from invisible to fully rendered. Works on any SVG element that contains `<path>`, `<line>`, `<circle>`, `<rect>`, `<polyline>`, or `<polygon>` elements with a visible stroke.

**Class:** `fx-draw-svg-pl` | `fx-draw-svg-st` | `fx-draw-svg` | `fx-draw-svg-scrub`

**Defaults:**

| Property | Value |
|----------|-------|
| Duration | `2s` |
| Ease | `power2.inOut` |

**Usage:**

```html
<!-- Icon draws on page load -->
<svg class="fx-draw-svg-pl" viewBox="0 0 100 100">
    <path d="M10,50 Q50,10 90,50 Q50,90 10,50" stroke="#000" fill="none" />
</svg>

<!-- Illustration draws on scroll -->
<svg class="fx-draw-svg-st" viewBox="0 0 200 200">
    <circle cx="100" cy="100" r="80" stroke="#333" fill="none" stroke-width="2" />
    <path d="M60,100 L90,130 L140,80" stroke="#333" fill="none" stroke-width="2" />
</svg>
```

**Best for:** Icons, illustrations, decorative SVGs, logo reveals.

**How it works internally:**
1. Finds all stroke-based child elements (`path`, `line`, `circle`, etc.)
2. Measures each element's total stroke length via `getTotalLength()`
3. Sets `stroke-dasharray` and `stroke-dashoffset` to the full length (hiding the stroke)
4. GSAP animates `stroke-dashoffset` to `0`, revealing the drawn path
5. Multiple paths within the same SVG are staggered slightly

**Scrub mode:** Use `fx-draw-svg-scrub` to draw the SVG progressively as the user scrolls — the stroke follows the scroll position instead of playing once:

```html
<svg class="fx-draw-svg-scrub" viewBox="0 0 200 200">
    <circle cx="100" cy="100" r="80" stroke="#333" fill="none" stroke-width="2" />
</svg>
```

**JS override:** Control draw speed or enable scrub programmatically:
```js
FX.drawSVG(el, { duration: 3 }); // Slower draw
FX.drawSVG(el, { scrub: 0.6 });  // Scrub-based draw
```

**Note:** The SVG paths must have a `stroke` set and `fill: none` (or a separate fill) for the drawing effect to be visible. If the path has a fill and no stroke, nothing will appear to animate.

---

## Split Words

Splits text into individual words using GSAP's SplitText, then fades and slides each word up with a stagger. More granular than Text Reveal (which splits by lines) but less granular than Type Writer (which splits by characters).

**Class:** `fx-split-words-pl` | `fx-split-words-st` | `fx-split-words`

**Defaults:**

| Property | Value |
|----------|-------|
| Duration | `0.8s` |
| Ease | `power3.out` |
| Stagger | `0.05s` |
| From Y | `20px` |
| From Opacity | `0` |

**Usage:**

```html
<!-- Quote reveals word by word on scroll -->
<blockquote class="fx-split-words-st">
    Design is not just what it looks like, design is how it works.
</blockquote>

<!-- Paragraph with word-by-word entrance on load -->
<p class="fx-split-words-pl">
    We build digital experiences that inspire and engage.
</p>
```

**Best for:** Paragraphs, quotes, descriptions — flowing, readable text reveal.

**How it works internally:**
1. SplitText splits the element's text into individual words
2. Each word starts at `opacity: 0` and `y: 20px` (below its final position)
3. GSAP staggers each word to `opacity: 1` and `y: 0`
4. The stagger timing (0.05s) creates a smooth wave effect across the text
5. After completion, inline transforms are cleaned up

**JS override:** Control the stagger and slide distance:
```js
FX.splitWords(el, { stagger: 0.08, y: 30 }); // Slower, more dramatic
FX.splitWords(el, { stagger: 0.02 }); // Rapid word reveal
```

---

## Slide In

Slides the element in horizontally from the left or right edge. Use `fx-slide-left` for an entrance from the left or `fx-slide-right` for an entrance from the right.

**Class:** `fx-slide-left-pl` | `fx-slide-left-st` | `fx-slide-left` | `fx-slide-right-pl` | `fx-slide-right-st` | `fx-slide-right`

**Defaults:**

| Property | Value |
|----------|-------|
| Duration | `1s` |
| Ease | `power3.out` |
| From X | `100px` (right) or `-100px` (left) |
| From Opacity | `0` |

**Usage:**

```html
<!-- Image slides in from the left on scroll -->
<img src="feature.jpg" class="fx-slide-left-st" />

<!-- Card slides in from the right on scroll -->
<div class="card fx-slide-right-st">
    <h3>Our Process</h3>
    <p>Step by step, we build your vision.</p>
</div>

<!-- Side-by-side layout with opposing directions -->
<div class="two-col">
    <div class="col fx-slide-left-st">Left content</div>
    <div class="col fx-slide-right-st">Right content</div>
</div>
```

**Best for:** Cards, images, side-by-side layouts, feature sections.

**JS override:** Control the slide distance and direction:
```js
FX.slideIn(el, { direction: 'left', x: 200 });  // Slide from further left
FX.slideIn(el, { direction: 'right', x: 150 }); // Slide from further right
```

---

## Parallax

Scrub-based vertical shift tied to scroll position. As the user scrolls, the element shifts along the Y axis, creating a depth illusion. Like Tilt In, this effect is always scroll-linked — there is no `-pl` (page load) variant.

**Class:** `fx-parallax-st` | `fx-parallax`

**Defaults:**

| Property | Value |
|----------|-------|
| Y Shift | `50px` (shifts from `-50` to `+50`) |
| Scrub | `true` |

**Usage:**

```html
<!-- Background image shifts on scroll -->
<img src="bg-pattern.jpg" class="fx-parallax-st" />

<!-- Decorative element creates depth -->
<div class="floating-shape fx-parallax-st"></div>

<!-- Combine with a container for layered depth -->
<section class="hero">
    <img src="hero-bg.jpg" class="fx-parallax-st" />
    <h1 class="fx-text-reveal-st">Layered depth</h1>
</section>
```

**Best for:** Background images, decorative elements, layered hero sections.

**How it works internally:**
1. GSAP creates a `fromTo` tween: `y: -50` to `y: 50` (based on the `y` default)
2. ScrollTrigger is configured with `scrub: true` for direct 1:1 scroll linkage
3. The scroll range covers the element's full visibility window
4. No opacity change — the element is always visible, only its position shifts

**Per-element speed:** Use `fx-y-[value]` to control parallax intensity per element:

```html
<!-- Subtle shift -->
<img src="bg.jpg" class="fx-parallax-st fx-y-[20]" />

<!-- Dramatic shift -->
<img src="fg.jpg" class="fx-parallax-st fx-y-[80]" />
```

**JS override:** Control the shift amount:
```js
FX.parallax(el, { y: 100 }); // Stronger parallax (-100 to +100)
FX.parallax(el, { y: 20 });  // Subtle parallax (-20 to +20)
```

**Note:** Like Tilt In, this effect has no page-load variant since it requires scroll position to drive the animation. The element moves in both directions — upward above center, downward below center.

---

## All Defaults at a Glance

| Effect | Duration | Ease | Stagger | Unique Props |
|--------|----------|------|---------|-------------|
| textReveal | 1.2s | power3.out | 0.1s | y: 100% |
| reveal | 1s | power3.out | — | y: 80px |
| spinReveal | 1.4s | power3.out | — | rotation: -30, scale: 0.9 |
| bgReveal | 1s | power3.out | — | y: 100% |
| scaleIn | 1s | power3.out | — | scale: 0.92 |
| fadeIn | 1.4s | power1.out | — | opacity + scale: 0.95 |
| blurIn | 1.2s | power2.out | — | filter: blur(12px) |
| clipUp | 1s | power3.inOut | — | clipPath: inset(100% 0 0 0) |
| clipDown | 1s | power3.inOut | — | clipPath: inset(0 0 100% 0) |
| tiltIn | 1.4s | power3.out | — | rotationX: 45, scale: 0.8, scrub: 0.6 |
| typeWriter | 0.05s | none | 0.03s | SplitText chars, per-character reveal |
| drawSVG | 2s | power2.inOut | — | stroke-dashoffset animation |
| splitWords | 0.8s | power3.out | 0.05s | SplitText words, y: 20px |
| slideIn | 1s | power3.out | — | x: 100 (left/right direction) |
| parallax | — | — | — | y: 50, scrub: true |

All effects except clipUp/clipDown, tiltIn, and parallax animate `opacity` from `0` to `1`. Tilt In animates opacity from `0` but is scrub-based (tied to scroll position). Parallax has no opacity change — only Y position shifts.
