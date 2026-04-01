/**
 * FX — Lightweight GSAP Animation SDK
 *
 * Apply animations via CSS classes:
 *   Page load:      .fx-text-reveal-pl, .fx-reveal-pl, .fx-spin-reveal-pl, .fx-bg-reveal-pl, .fx-scale-in-pl
 *   Scroll trigger: .fx-text-reveal-st, .fx-reveal-st, .fx-spin-reveal-st, .fx-bg-reveal-st, .fx-scale-in-st
 *
 * Override per-element with modifier classes (Gutenberg-friendly):
 *   .fx-duration-[1.5]  .fx-delay-[0.3]  .fx-stagger-[0.2]  .fx-ease-[power2.inOut]
 *
 * Or use the JS API:
 *   FX.textReveal('.my-heading', { trigger: 'scroll', delay: 0.2 })
 */
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';

gsap.registerPlugin(ScrollTrigger, SplitText);

// ── Defaults ────────────────────────────────

const SCROLL_DEFAULTS = { start: 'top 85%', once: true };

const EFFECT_DEFAULTS = {
    textReveal:  { duration: 1.2, ease: 'power3.out', stagger: 0.1 },
    reveal:      { duration: 1,   ease: 'power3.out' },
    spinReveal:  { duration: 1.4, ease: 'power3.out' },
    bgReveal:    { duration: 1,   ease: 'power3.out' },
    scaleIn:     { duration: 1,   ease: 'power3.out' },
};

// ── Helpers ──────────────────────────────────

/**
 * Parse modifier classes like .fx-duration-[1.5] .fx-delay-[0.3] from an element.
 * Uses bracket syntax so values with dots work in CSS class names.
 */
function getClassModifier(el, name, fallback) {
    const prefix = 'fx-' + name + '-[';
    for (const cls of el.classList) {
        if (cls.startsWith(prefix) && cls.endsWith(']')) {
            const val = cls.slice(prefix.length, -1);
            const num = parseFloat(val);
            return isNaN(num) ? val : num;
        }
    }
    return fallback;
}

function resolveOptions(el, effectName, overrides) {
    const d = EFFECT_DEFAULTS[effectName];
    return {
        duration: getClassModifier(el, 'duration', overrides.duration ?? d.duration),
        ease:     getClassModifier(el, 'ease',     overrides.ease     ?? d.ease),
        stagger:  getClassModifier(el, 'stagger',  overrides.stagger  ?? d.stagger ?? 0),
        delay:    getClassModifier(el, 'delay',     overrides.delay    ?? 0),
    };
}

function buildScrollTrigger(el, scrollTriggerOpts) {
    return {
        trigger: scrollTriggerOpts.trigger || el,
        ...SCROLL_DEFAULTS,
        ...scrollTriggerOpts,
    };
}

// ── Effects ──────────────────────────────────

/**
 * Split text into lines with overflow-hidden mask, reveal upward.
 */
export function textReveal(el, opts = {}) {
    const o = resolveOptions(el, 'textReveal', opts);

    const split = new SplitText(el, {
        type: 'lines',
        linesClass: 'line-wrapper',
    });

    split.lines.forEach(line => {
        const wrapper = document.createElement('div');
        wrapper.style.overflow = 'hidden';
        line.parentNode.insertBefore(wrapper, line);
        wrapper.appendChild(line);
    });

    const tweenVars = {
        y: '100%',
        opacity: 0,
        duration: o.duration,
        ease: o.ease,
        stagger: o.stagger,
        delay: o.delay,
        onComplete() {
            split.lines.forEach(line => {
                line.style.transform = '';
                line.style.opacity = '';
            });
        },
    };

    if (opts.trigger === 'scroll' || opts.scrollTrigger) {
        tweenVars.scrollTrigger = buildScrollTrigger(el, opts.scrollTrigger || {});
    }

    gsap.from(split.lines, tweenVars);
}

/**
 * Slide-up reveal (images, cards, generic elements).
 */
export function reveal(el, opts = {}) {
    const o = resolveOptions(el, 'reveal', opts);

    const tweenVars = {
        y: opts.y ?? 80,
        opacity: 0,
        duration: o.duration,
        ease: o.ease,
        delay: o.delay,
    };

    if (opts.trigger === 'scroll' || opts.scrollTrigger) {
        tweenVars.scrollTrigger = buildScrollTrigger(el, opts.scrollTrigger || {});
    }

    gsap.from(el, tweenVars);
}

/**
 * Rotate + scale in (badges, icons, decorative elements).
 */
export function spinReveal(el, opts = {}) {
    const o = resolveOptions(el, 'spinReveal', opts);

    const tweenVars = {
        rotation: opts.rotation ?? -30,
        scale: opts.scale ?? 0.9,
        opacity: 0,
        duration: o.duration,
        ease: o.ease,
        delay: o.delay,
    };

    if (opts.trigger === 'scroll' || opts.scrollTrigger) {
        tweenVars.scrollTrigger = buildScrollTrigger(el, opts.scrollTrigger || {});
    }

    gsap.from(el, tweenVars);
}

/**
 * Background container slide-up.
 */
export function bgReveal(el, opts = {}) {
    const o = resolveOptions(el, 'bgReveal', opts);

    const tweenVars = {
        y: '100%',
        opacity: 0,
        duration: o.duration,
        ease: o.ease,
        delay: o.delay,
    };

    if (opts.trigger === 'scroll' || opts.scrollTrigger) {
        tweenVars.scrollTrigger = buildScrollTrigger(el, opts.scrollTrigger || {});
    }

    gsap.from(el, tweenVars);
}

/**
 * Scale-in (glass panels, cards).
 */
export function scaleIn(el, opts = {}) {
    const o = resolveOptions(el, 'scaleIn', opts);

    const tweenVars = {
        scale: opts.scale ?? 0.92,
        opacity: 0,
        duration: o.duration,
        ease: o.ease,
        delay: o.delay,
    };

    if (opts.trigger === 'scroll' || opts.scrollTrigger) {
        tweenVars.scrollTrigger = buildScrollTrigger(el, opts.scrollTrigger || {});
    }

    gsap.from(el, tweenVars);
}

// ── Class-to-effect mapping ─────────────────

const effects = {
    'fx-text-reveal': textReveal,
    'fx-reveal':      reveal,
    'fx-spin-reveal': spinReveal,
    'fx-bg-reveal':   bgReveal,
    'fx-scale-in':    scaleIn,
};

/**
 * Group a NodeList by their direct parent element.
 */
function groupByParent(nodeList) {
    const map = new Map();
    nodeList.forEach(el => {
        const parent = el.parentElement;
        if (!map.has(parent)) map.set(parent, []);
        map.get(parent).push(el);
    });
    return Array.from(map.values());
}

/**
 * Scan the DOM for .fx-* classes and apply animations.
 * Called automatically on DOMContentLoaded, or manually via FX.init().
 */
export function init() {
    Object.keys(effects).forEach(name => {
        const fn = effects[name];

        // Page-load variant: .fx-<name>-pl
        const plGroups = groupByParent(document.querySelectorAll('.' + name + '-pl'));
        plGroups.forEach(group => {
            group.forEach((el, i) => fn(el, { delay: i * 0.15 }));
        });

        // Scroll-trigger variant: .fx-<name>-st
        const stGroups = groupByParent(document.querySelectorAll('.' + name + '-st'));
        stGroups.forEach(group => {
            const sharedTrigger = group[0].parentElement || group[0];
            group.forEach((el, i) => {
                fn(el, {
                    trigger: 'scroll',
                    delay: i * 0.15,
                    scrollTrigger: { trigger: sharedTrigger },
                });
            });
        });
    });
}

// ── Auto-init ───────────────────────────────

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}

// ── Public API on window ────────────────────

window.FX = { textReveal, reveal, spinReveal, bgReveal, scaleIn, init };
