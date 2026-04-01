/**
 * Fancoolo FX — A class-driven GSAP animation wrapper
 *
 * Load GSAP + plugins BEFORE this file:
 *   <script src="node_modules/gsap/dist/gsap.min.js"></script>
 *   <script src="node_modules/gsap/dist/ScrollTrigger.min.js"></script>
 *   <script src="node_modules/gsap/dist/SplitText.min.js"></script>
 *   <script src="src/fx.js"></script>
 *
 * Three ways to trigger animations:
 *
 *   1. Explicit classes with trigger suffix:
 *      .fx-text-reveal-pl  (page load)
 *      .fx-text-reveal-st  (scroll trigger)
 *
 *   2. Bare classes inside <section> — auto scroll-triggered:
 *      <section>
 *        <h2 class="fx-text-reveal">Auto scroll-triggered by section</h2>
 *      </section>
 *
 *   3. Tag-based auto-animation (zero classes):
 *      FX.config.tagMap = { 'h1,h2,h3': 'textReveal', 'img': 'reveal' }
 *
 * Modifier classes (Gutenberg-friendly):
 *   .fx-duration-[1.5]  .fx-delay-[0.3]  .fx-stagger-[0.2]  .fx-ease-[power2.inOut]
 *
 * JS API:
 *   FX.textReveal(el, { trigger: 'scroll', delay: 0.2 })
 */
(function () {
    'use strict';

    if (typeof gsap === 'undefined' || typeof SplitText === 'undefined' || typeof ScrollTrigger === 'undefined') {
        console.error('[FX] Missing dependencies. Load gsap, ScrollTrigger, and SplitText before fx.js');
        return;
    }

    gsap.registerPlugin(ScrollTrigger, SplitText);

    // ── Config ──────────────────────────────────

    var config = {
        /**
         * CSS selector for section containers.
         * Elements with bare .fx-* classes (no -pl/-st suffix) inside matching
         * containers are auto scroll-triggered using the container as trigger.
         */
        sectionSelector: 'section',

        /**
         * Default ScrollTrigger start position.
         * Format: "triggerPosition viewportPosition"
         * Examples: 'top 85%', 'top center', 'top 90%', 'center center'
         * See: https://gsap.com/docs/v3/Plugins/ScrollTrigger/
         */
        scrollStart: 'top 85%',

        /**
         * Whether scroll-triggered animations play only once.
         * Set to false to replay every time the element enters the viewport.
         */
        scrollOnce: true,

        /**
         * Map of CSS selectors → effect names for zero-class auto-animation.
         * Elements matching these selectors inside sections get animated automatically.
         * Set to null/false to disable. Override before DOMContentLoaded or call FX.init().
         *
         * Example:
         *   FX.config.tagMap = {
         *       'h1,h2,h3,h4,h5,h6': 'textReveal',
         *       'p,blockquote':       'textReveal',
         *       'img,video':          'reveal',
         *   }
         */
        tagMap: null,
    };

    // ── Defaults ────────────────────────────────

    var EFFECT_DEFAULTS = {
        textReveal:  { duration: 1.2, ease: 'power3.out', stagger: 0.1 },
        reveal:      { duration: 1,   ease: 'power3.out' },
        spinReveal:  { duration: 1.4, ease: 'power3.out' },
        bgReveal:    { duration: 1,   ease: 'power3.out' },
        scaleIn:     { duration: 1,   ease: 'power3.out' },
    };

    // ── Helpers ──────────────────────────────────

    function getClassModifier(el, name, fallback) {
        var prefix = 'fx-' + name + '-[';
        for (var i = 0; i < el.classList.length; i++) {
            var cls = el.classList[i];
            if (cls.indexOf(prefix) === 0 && cls.charAt(cls.length - 1) === ']') {
                var val = cls.slice(prefix.length, -1);
                var num = parseFloat(val);
                return isNaN(num) ? val : num;
            }
        }
        return fallback;
    }

    function resolveOptions(el, effectName, overrides) {
        var d = EFFECT_DEFAULTS[effectName];
        return {
            duration: getClassModifier(el, 'duration', overrides.duration != null ? overrides.duration : d.duration),
            ease:     getClassModifier(el, 'ease',     overrides.ease     != null ? overrides.ease     : d.ease),
            stagger:  getClassModifier(el, 'stagger',  overrides.stagger  != null ? overrides.stagger  : (d.stagger || 0)),
            delay:    getClassModifier(el, 'delay',     overrides.delay    != null ? overrides.delay    : 0),
        };
    }

    function buildScrollTrigger(el, scrollTriggerOpts) {
        var defaults = {
            start: config.scrollStart,
            once: config.scrollOnce,
        };
        var st = { trigger: scrollTriggerOpts.trigger || el };
        for (var key in defaults) st[key] = defaults[key];
        for (var key2 in scrollTriggerOpts) st[key2] = scrollTriggerOpts[key2];

        // Per-element override: fx-start-[top center] or fx-start-[top 70%]
        var startOverride = getClassModifier(el, 'start', null);
        if (startOverride !== null) st.start = startOverride;

        return st;
    }

    // ── Effects ──────────────────────────────────

    function textReveal(el, opts) {
        opts = opts || {};
        var o = resolveOptions(el, 'textReveal', opts);

        var split = new SplitText(el, { type: 'lines', linesClass: 'line-wrapper' });

        split.lines.forEach(function (line) {
            var wrapper = document.createElement('div');
            wrapper.style.overflow = 'hidden';
            line.parentNode.insertBefore(wrapper, line);
            wrapper.appendChild(line);
        });

        var tweenVars = {
            y: '100%',
            opacity: 0,
            duration: o.duration,
            ease: o.ease,
            stagger: o.stagger,
            delay: o.delay,
            onComplete: function () {
                split.lines.forEach(function (line) {
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

    function reveal(el, opts) {
        opts = opts || {};
        var o = resolveOptions(el, 'reveal', opts);

        var tweenVars = {
            y: opts.y != null ? opts.y : 80,
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

    function spinReveal(el, opts) {
        opts = opts || {};
        var o = resolveOptions(el, 'spinReveal', opts);

        var tweenVars = {
            rotation: opts.rotation != null ? opts.rotation : -30,
            scale: opts.scale != null ? opts.scale : 0.9,
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

    function bgReveal(el, opts) {
        opts = opts || {};
        var o = resolveOptions(el, 'bgReveal', opts);

        var tweenVars = {
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

    function scaleIn(el, opts) {
        opts = opts || {};
        var o = resolveOptions(el, 'scaleIn', opts);

        var tweenVars = {
            scale: opts.scale != null ? opts.scale : 0.92,
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

    var effects = {
        'fx-text-reveal': textReveal,
        'fx-reveal':      reveal,
        'fx-spin-reveal': spinReveal,
        'fx-bg-reveal':   bgReveal,
        'fx-scale-in':    scaleIn,
    };

    var effectsByName = {
        textReveal: textReveal,
        reveal: reveal,
        spinReveal: spinReveal,
        bgReveal: bgReveal,
        scaleIn: scaleIn,
    };

    // ── Helpers ──────────────────────────────────

    function groupByParent(nodeList) {
        var map = new Map();
        nodeList.forEach(function (el) {
            var parent = el.parentElement;
            if (!map.has(parent)) map.set(parent, []);
            map.get(parent).push(el);
        });
        var groups = [];
        map.forEach(function (arr) { groups.push(arr); });
        return groups;
    }

    function applyScrollGroup(fn, group, triggerEl) {
        group.forEach(function (el, i) {
            fn(el, {
                trigger: 'scroll',
                delay: i * 0.15,
                scrollTrigger: { trigger: triggerEl },
            });
        });
    }

    function camelToKebab(str) {
        return str.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
    }

    // ── Init ────────────────────────────────────

    function init() {
        var processed = new Set();

        Object.keys(effects).forEach(function (name) {
            var fn = effects[name];

            // 1. Page-load variant: .fx-<name>-pl
            var plGroups = groupByParent(document.querySelectorAll('.' + name + '-pl'));
            plGroups.forEach(function (group) {
                group.forEach(function (el, i) {
                    fn(el, { delay: i * 0.15 });
                    processed.add(el);
                });
            });

            // 2. Explicit scroll-trigger variant: .fx-<name>-st
            //    Each element triggers itself (not the parent), so it works
            //    regardless of how deep it sits in the DOM.
            var stEls = document.querySelectorAll('.' + name + '-st');
            var stGroups = groupByParent(stEls);
            stGroups.forEach(function (group) {
                group.forEach(function (el, i) {
                    fn(el, {
                        trigger: 'scroll',
                        delay: i * 0.15,
                        scrollTrigger: { trigger: el },
                    });
                    processed.add(el);
                });
            });

            // 3. Bare class inside a section: .fx-<name> (no suffix)
            //    Only elements inside a matching section are picked up,
            //    but each element triggers itself (same as -st).
            if (config.sectionSelector) {
                document.querySelectorAll(config.sectionSelector).forEach(function (section) {
                    var bareEls = Array.from(section.querySelectorAll('.' + name))
                        .filter(function (el) { return !processed.has(el); });
                    if (bareEls.length === 0) return;

                    var groups = groupByParent(bareEls);
                    groups.forEach(function (group) {
                        group.forEach(function (el, i) {
                            fn(el, {
                                trigger: 'scroll',
                                delay: i * 0.15,
                                scrollTrigger: { trigger: el },
                            });
                            processed.add(el);
                        });
                    });
                });
            }
        });

        // 4. Tag-based auto-animation inside sections
        if (config.tagMap && config.sectionSelector) {
            document.querySelectorAll(config.sectionSelector).forEach(function (section) {
                Object.keys(config.tagMap).forEach(function (selector) {
                    var effectName = config.tagMap[selector];
                    var fn = effects['fx-' + camelToKebab(effectName)] || effectsByName[effectName];
                    if (!fn) return;

                    var els = Array.from(section.querySelectorAll(selector))
                        .filter(function (el) { return !processed.has(el); });
                    if (els.length === 0) return;

                    var groups = groupByParent(els);
                    groups.forEach(function (group) {
                        applyScrollGroup(fn, group, section);
                        group.forEach(function (el) { processed.add(el); });
                    });
                });
            });
        }
    }

    // ── Boot ────────────────────────────────────

    function applyPreConfig() {
        var pre = window.__FX_CONFIG__;
        if (!pre) return;
        if (pre.sectionSelector !== undefined) config.sectionSelector = pre.sectionSelector;
        if (pre.scrollStart !== undefined) config.scrollStart = pre.scrollStart;
        if (pre.scrollOnce !== undefined) config.scrollOnce = pre.scrollOnce;
        if (pre.tagMap !== undefined) config.tagMap = pre.tagMap;
    }

    function boot() {
        applyPreConfig();
        init();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    // ── Public API ──────────────────────────────

    window.FX = {
        config: config,
        textReveal: textReveal,
        reveal: reveal,
        spinReveal: spinReveal,
        bgReveal: bgReveal,
        scaleIn: scaleIn,
        init: init,
    };
})();
