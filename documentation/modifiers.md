# Modifier Classes

Override animation timing on individual elements using modifier classes. No JavaScript or inline styles needed — add them alongside effect classes in the HTML.

## Syntax

```
fx-{property}-[{value}]
```

Uses bracket syntax (inspired by Tailwind CSS arbitrary values) so decimal values work in CSS class names.

## Available Modifiers

| Modifier | Default | Type | Description |
|----------|---------|------|-------------|
| `fx-duration-[n]` | `1.2` (text) / `1` (others) | Number (seconds) | How long the animation takes |
| `fx-delay-[n]` | `0` | Number (seconds) | Wait before starting |
| `fx-stagger-[n]` | `0.1` | Number (seconds) | Delay between staggered siblings |
| `fx-ease-[name]` | `power3.out` | String | GSAP easing function |
| `fx-start-[pos]` | `top 85%` | String | ScrollTrigger start position (scroll-triggered only) |

## Duration

Control how long the animation takes:

```html
<!-- Fast (0.5 seconds) -->
<h2 class="fx-text-reveal-st fx-duration-[0.5]">Quick reveal</h2>

<!-- Slow (3 seconds) -->
<h2 class="fx-text-reveal-st fx-duration-[3]">Dramatic slow reveal</h2>

<!-- Default is 1.2s for text effects, 1s for others -->
<h2 class="fx-text-reveal-st">Default timing</h2>
```

## Delay

Add a wait before the animation starts:

```html
<!-- Wait 0.5 seconds before animating -->
<p class="fx-text-reveal-st fx-delay-[0.5]">Delayed paragraph</p>

<!-- Useful for sequencing elements manually -->
<h2 class="fx-text-reveal-st">First (no delay)</h2>
<p class="fx-text-reveal-st fx-delay-[0.3]">Second (0.3s delay)</p>
<img class="fx-reveal-st fx-delay-[0.6]" src="photo.jpg" />
```

**Note:** Delay is added on top of auto-stagger. If an element is the 3rd sibling (0.3s stagger delay) and has `fx-delay-[0.5]`, total delay is 0.8s.

## Stagger

Control the delay between siblings with the same class:

```html
<!-- Wider gaps between each item -->
<div class="grid">
    <img class="fx-reveal-st fx-stagger-[0.3]" src="1.jpg" />
    <img class="fx-reveal-st fx-stagger-[0.3]" src="2.jpg" />
    <img class="fx-reveal-st fx-stagger-[0.3]" src="3.jpg" />
</div>

<!-- Tight stagger for rapid-fire effect -->
<div>
    <p class="fx-text-reveal-st fx-stagger-[0.05]">Line one</p>
    <p class="fx-text-reveal-st fx-stagger-[0.05]">Line two</p>
    <p class="fx-text-reveal-st fx-stagger-[0.05]">Line three</p>
</div>
```

**Important:** The stagger modifier is read from the **first** element in the group — it doesn't need to be on every sibling (but it's fine if it is).

## Ease

Change the animation's easing curve. Uses [GSAP easing names](https://gsap.com/docs/v3/Eases/):

```html
<!-- Bounce effect -->
<div class="fx-reveal-st fx-ease-[bounce.out]">Bouncy</div>

<!-- Elastic -->
<div class="fx-reveal-st fx-ease-[elastic.out(1,0.3)]">Springy</div>

<!-- Linear (no easing) -->
<div class="fx-reveal-st fx-ease-[none]">Constant speed</div>

<!-- Smooth in-out -->
<h2 class="fx-text-reveal-st fx-ease-[power2.inOut]">Smooth</h2>
```

### Common easing values

| Value | Feel |
|-------|------|
| `power3.out` | Default — fast start, smooth deceleration |
| `power2.inOut` | Smooth acceleration and deceleration |
| `power4.out` | More dramatic deceleration |
| `back.out(1.7)` | Slight overshoot |
| `elastic.out(1,0.3)` | Spring/elastic feel |
| `bounce.out` | Bouncing ball effect |
| `none` | Linear, constant speed |

## Start Position

Control when the scroll-triggered animation fires. Uses GSAP's ScrollTrigger `start` format: `"triggerPosition viewportPosition"`.

```html
<!-- Trigger when element reaches center of viewport -->
<h2 class="fx-text-reveal-st fx-start-[top center]">Center trigger</h2>

<!-- Trigger earlier (90% down = near bottom) -->
<img class="fx-reveal-st fx-start-[top 90%]" src="photo.jpg" />

<!-- Trigger very late (at the top edge of viewport) -->
<div class="fx-scale-in-st fx-start-[top top]">Top trigger</div>
```

**Common values:**

| Value | Meaning |
|-------|---------|
| `top 85%` | Default — element near bottom of viewport |
| `top center` | Element at viewport center |
| `top 70%` | Element at 70% down |
| `top top` | Element at the very top |
| `center center` | Element vertically centered |

**Note:** Only applies to scroll-triggered animations (`-st`, bare classes, tagMap). Has no effect on `-pl` (page load) animations.

To change the default for **all** elements globally, use config instead:

```html
<script>
window.__FX_CONFIG__ = { scrollStart: 'top center' };
</script>
```

## Combining Modifiers

Stack multiple modifiers on one element:

```html
<h2 class="fx-text-reveal-st fx-duration-[2] fx-stagger-[0.25] fx-ease-[power2.inOut]">
    Slow, wide stagger, smooth easing
</h2>
```

In Gutenberg, add all classes in the "Additional CSS class(es)" field:

```
fx-text-reveal-st fx-duration-[2] fx-delay-[0.3]
```

## Modifier Priority

Modifiers override defaults. The priority chain is:

1. **Modifier class** on the element (highest priority)
2. **JS option** passed via the API
3. **Effect default** (lowest priority)
