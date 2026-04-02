# JavaScript API

For most use cases, CSS classes are all you need. The JavaScript API is for compound sequences, dynamic content, or custom trigger logic that classes alone can't express.

## The FX Global

When `fx.js` loads, it creates a `window.FX` object with these methods:

| Method | Description |
|--------|-------------|
| `FX.textReveal(el, opts)` | Split text mask reveal |
| `FX.reveal(el, opts)` | Slide up with fade |
| `FX.spinReveal(el, opts)` | Rotate and scale in |
| `FX.bgReveal(el, opts)` | Background slide up |
| `FX.scaleIn(el, opts)` | Scale up with fade |
| `FX.fadeIn(el, opts)` | Opacity only, no movement |
| `FX.blurIn(el, opts)` | Fade in while deblurring |
| `FX.clipUp(el, opts)` | Clip-path wipe from bottom |
| `FX.clipDown(el, opts)` | Clip-path wipe from top |
| `FX.tiltIn(el, opts)` | 3D perspective reveal (scrub-based) |
| `FX.typeWriter(el, opts)` | Character-by-character typing reveal |
| `FX.drawSVG(el, opts)` | SVG stroke drawing animation |
| `FX.parallax(el, opts)` | Scroll-linked Y parallax shift |
| `FX.splitWords(el, opts)` | Word-by-word fade and slide up |
| `FX.slideIn(el, opts)` | Horizontal slide from left or right |
| `FX.init()` | Re-scan DOM and apply animations |
| `FX.config` | Global config object |

## Basic Usage

```js
// Animate a single element on page load
FX.textReveal(document.querySelector('.hero-title'));

// Animate with scroll trigger
FX.reveal(document.querySelector('.hero-img'), {
    trigger: 'scroll'
});
```

## Options

All effect functions accept `(element, options)`:

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `duration` | Number | Varies by effect | Animation duration in seconds |
| `ease` | String | `'power3.out'` | GSAP easing function |
| `delay` | Number | `0` | Delay before animation starts |
| `stagger` | Number | `0.1` | Delay between staggered items (textReveal only) |
| `trigger` | String | — | Set to `'scroll'` to enable ScrollTrigger |
| `scrollTrigger` | Object | — | ScrollTrigger config object |

### Effect-specific options

| Option | Effect | Type | Default | Description |
|--------|--------|------|---------|-------------|
| `y` | reveal | Number | `80` | Vertical offset in pixels |
| `rotation` | spinReveal | Number | `-30` | Starting rotation in degrees |
| `scale` | spinReveal, scaleIn | Number | `0.9` / `0.92` | Starting scale |
| `direction` | slideIn | String | `'left'` | Slide direction: `'left'` or `'right'` |
| `x` | slideIn | Number | `100` | Horizontal offset in pixels |

## Scroll Trigger Options

Set `trigger: 'scroll'` to enable scroll-triggered animation:

```js
FX.textReveal(el, {
    trigger: 'scroll'
});
```

Use `scrollTrigger` to customize the trigger element or behavior:

```js
FX.textReveal(el, {
    trigger: 'scroll',
    scrollTrigger: {
        trigger: document.querySelector('.hero-section'),  // Different trigger element
        start: 'top 70%',     // Override default (top 85%)
        once: true,            // Play once (default)
    }
});
```

## Compound Sequences

The main reason to use the JS API — orchestrating multiple animations with specific timing:

```js
document.addEventListener('DOMContentLoaded', function () {
    var hero = document.querySelector('.hero-section');
    if (!hero) return;

    // 1. Background scales in first
    FX.scaleIn(hero.querySelector('.bg-panel'), {
        trigger: 'scroll',
        scrollTrigger: { trigger: hero }
    });

    // 2. Heading reveals 0.2s later
    FX.textReveal(hero.querySelector('h2'), {
        trigger: 'scroll',
        delay: 0.2,
        scrollTrigger: { trigger: hero }
    });

    // 3. Paragraph reveals 0.4s later
    FX.textReveal(hero.querySelector('p'), {
        trigger: 'scroll',
        delay: 0.4,
        scrollTrigger: { trigger: hero }
    });

    // 4. Button slides up 0.6s later
    FX.reveal(hero.querySelector('.btn'), {
        trigger: 'scroll',
        delay: 0.6,
        scrollTrigger: { trigger: hero }
    });
});
```

## Dynamic Content

For content loaded after the initial page render (AJAX, SPA navigation, infinite scroll):

```js
// After new content is added to the DOM
function onNewContent() {
    // Animate specific new elements
    document.querySelectorAll('.new-item').forEach(function (el) {
        FX.reveal(el, { delay: 0 });
    });

    // Or re-run full initialization
    FX.init();
}
```

## Staggered Groups

Animate a collection of elements with stagger:

```js
var cards = document.querySelectorAll('.team-card');
var parent = cards[0].closest('.team-grid');

cards.forEach(function (card, i) {
    // Image slides up
    var img = card.querySelector('img');
    if (img) {
        FX.reveal(img, {
            trigger: 'scroll',
            delay: i * 0.15,
            scrollTrigger: { trigger: parent }
        });
    }

    // Name reveals
    var name = card.querySelector('h3');
    if (name) {
        FX.textReveal(name, {
            trigger: 'scroll',
            delay: i * 0.15 + 0.1,
            scrollTrigger: { trigger: parent }
        });
    }
});
```

## Creating a Project-Specific Animation File

For sites with complex animations, create a separate file loaded after `fx.js`:

```html
<script src="node_modules/gsap/dist/gsap.min.js"></script>
<script src="node_modules/gsap/dist/ScrollTrigger.min.js"></script>
<script src="node_modules/gsap/dist/SplitText.min.js"></script>
<script src="src/fx.js"></script>
<!-- Project-specific animations -->
<script src="js/animations.js"></script>
```

```js
// js/animations.js
document.addEventListener('DOMContentLoaded', function () {

    // Quote block with specific timing
    var quote = document.querySelector('.quote-block');
    if (quote) {
        FX.textReveal(quote.querySelector('.line-1'), {
            trigger: 'scroll',
            scrollTrigger: { trigger: quote }
        });
        FX.textReveal(quote.querySelector('.line-2'), {
            trigger: 'scroll',
            delay: 0.2,
            scrollTrigger: { trigger: quote }
        });
    }

});
```
