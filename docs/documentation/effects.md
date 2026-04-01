# Effects Reference

FX ships with 10 animation effects. Most trigger on page load (`-pl`), on scroll (`-st`), or automatically inside a section (bare class). Tilt In is special — it's scrub-based and tied to scroll position.

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

All effects except clipUp/clipDown and tiltIn animate `opacity` from `0` to `1`. Tilt In animates opacity from `0` but is scrub-based (tied to scroll position).
