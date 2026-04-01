# Trigger Modes

Every effect supports three trigger modes that control **when** the animation plays.

## Overview

| Mode | Suffix | When it animates |
|------|--------|-----------------|
| Page Load | `-pl` | Immediately when the page loads |
| Scroll Trigger | `-st` | When the element scrolls into the viewport |
| Section Trigger | _(none)_ | When the parent `<section>` scrolls into the viewport |

---

## Page Load (`-pl`)

Animations play immediately when the DOM is ready. Use for hero sections and above-the-fold content.

```html
<h1 class="fx-text-reveal-pl">Visible immediately</h1>
<img src="hero.jpg" class="fx-reveal-pl" />
<div class="badge fx-spin-reveal-pl">New</div>
```

**When to use:**
- Hero headings and text
- Above-the-fold images
- Navigation elements
- Anything visible without scrolling

**How staggering works:**
Siblings with the same `-pl` class sharing the same parent are staggered with a 0.15s delay:

```html
<div class="hero-badges">
    <!-- These stagger: 0s, 0.15s, 0.3s -->
    <span class="fx-spin-reveal-pl">Fast</span>
    <span class="fx-spin-reveal-pl">Simple</span>
    <span class="fx-spin-reveal-pl">Free</span>
</div>
```

---

## Scroll Trigger (`-st`)

Animations play when the element enters the viewport during scrolling. The most common mode for content below the fold.

```html
<h2 class="fx-text-reveal-st">Appears on scroll</h2>
<img src="photo.jpg" class="fx-reveal-st" />
```

### How scroll detection works

When the SDK sees a `-st` element, it creates a GSAP ScrollTrigger with these defaults:

- **`start: 'top 85%'`** — the animation fires when the top edge of the element (or its parent) reaches 85% down from the top of the viewport. This means the element is near the bottom of the screen.
- **`once: true`** — the animation plays only once. Scrolling back up and down again won't replay it.

### Changing the trigger position

The `start` value uses GSAP's ScrollTrigger format: `"triggerPosition viewportPosition"`.

**Per-element** — use the `fx-start-[]` modifier class:

```html
<!-- Triggers when element reaches the center of the viewport -->
<h2 class="fx-text-reveal-st fx-start-[top center]">Triggers at center</h2>

<!-- Triggers earlier (near the bottom of viewport) -->
<img class="fx-reveal-st fx-start-[top 90%]" src="photo.jpg" />

<!-- Triggers when element reaches the top of viewport -->
<div class="fx-scale-in-st fx-start-[top top]">Late trigger</div>
```

**Globally** — change the default for all scroll-triggered animations:

```html
<script>
window.__FX_CONFIG__ = {
    scrollStart: 'top center',  // default: 'top 85%'
    scrollOnce: false,          // default: true (replay on every scroll)
};
</script>
<script src="src/fx.js"></script>
```

Or at runtime:

```js
FX.config.scrollStart = 'top 70%';
FX.config.scrollOnce = false;
```

**Common start values:**

| Value | When it triggers |
|-------|-----------------|
| `top 85%` | Element near viewport bottom (default) |
| `top center` | Element at viewport center |
| `top 70%` | Element at 70% down the viewport |
| `top top` | Element at the very top of viewport |
| `center center` | Element centered in viewport |

### Shared trigger for siblings

Siblings with the same `-st` class share their parent as the scroll trigger:

```html
<div class="grid">
    <!-- All 3 trigger when .grid reaches 85% of viewport -->
    <!-- Staggered: 0s, 0.15s, 0.3s -->
    <img class="fx-reveal-st" src="1.jpg" />
    <img class="fx-reveal-st" src="2.jpg" />
    <img class="fx-reveal-st" src="3.jpg" />
</div>
```

This means all three images animate together (with stagger) when the grid enters the viewport, rather than each triggering independently as they scroll in.

---

## Section Trigger (bare class, no suffix)

The simplest mode. Add a bare class like `fx-text-reveal` (no `-pl` or `-st`) inside a `<section>` tag. The SDK uses the section as the scroll trigger automatically.

```html
<section>
    <!-- All auto scroll-triggered by the <section> -->
    <h2 class="fx-text-reveal">No suffix needed</h2>
    <p class="fx-text-reveal">Works inside any section</p>
    <img class="fx-reveal" src="photo.jpg" />
</section>
```

**When to use:**
- When you want scroll-triggered animations but don't want to think about `-st`
- WordPress/Gutenberg where sections are natural containers
- Any content that lives inside `<section>` tags

### Customizing the container selector

By default, the SDK looks for `<section>` elements. You can change this:

```html
<script>
window.__FX_CONFIG__ = {
    sectionSelector: 'section, .wp-block-group'
};
</script>
<script src="src/fx.js"></script>
```

Now bare classes also work inside `.wp-block-group` containers.

---

## Priority order

When an element could match multiple modes, the SDK processes them in this order:

1. **`-pl`** (page load) — processed first
2. **`-st`** (explicit scroll trigger) — processed second
3. **Bare class in section** — processed third, skips already-animated elements
4. **tagMap** — processed last, skips already-animated elements

An element is never animated twice. Once processed by any mode, it's skipped by later modes.

---

## Choosing the right mode

| Scenario | Recommended mode |
|----------|-----------------|
| Hero heading | `fx-text-reveal-pl` |
| Hero background | `fx-bg-reveal-pl` |
| Blog post images | `fx-reveal-st` |
| Content sections | `fx-text-reveal` (bare, inside `<section>`) |
| Full-page auto-animation | tagMap config |
| Cards in a grid | `fx-reveal-st` or `fx-scale-in-st` |
| Navigation badges | `fx-spin-reveal-pl` |
