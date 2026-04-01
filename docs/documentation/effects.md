---
title: "Effects"
permalink: /documentation/effects/
layout: single
sidebar:
  nav: "docs"
toc: true
toc_sticky: true
---

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

## All Defaults at a Glance

| Effect | Duration | Ease | Stagger | Unique Props |
|--------|----------|------|---------|-------------|
| textReveal | 1.2s | power3.out | 0.1s | y: 100% |
| reveal | 1s | power3.out | — | y: 80px |
| spinReveal | 1.4s | power3.out | — | rotation: -30, scale: 0.9 |
| bgReveal | 1s | power3.out | — | y: 100% |
| scaleIn | 1s | power3.out | — | scale: 0.92 |

All effects animate `opacity` from `0` to `1`.
