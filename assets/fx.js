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

        /** Disable all animations on mobile (window.innerWidth <= mobileBreakpoint). */
        disableMobile: false,
        mobileBreakpoint: 768,

        /** Multiply all animation durations globally (e.g. 0.5 = half speed, 2 = double). */
        speedMultiplier: 1,

        /** Skip all animations when OS prefers-reduced-motion is enabled. */
        respectReducedMotion: true,

        /** CSS selector string — matching elements are never animated. */
        excludeSelectors: '',
    };

    // ── Defaults ────────────────────────────────

    var EFFECT_DEFAULTS = {
        textReveal:  { duration: 1.2, ease: 'power3.out', stagger: 0.1 },
        reveal:      { duration: 1,   ease: 'power3.out' },
        spinReveal:  { duration: 1.4, ease: 'power3.out' },
        bgReveal:    { duration: 1,   ease: 'power3.out' },
        scaleIn:     { duration: 1,   ease: 'power3.out' },
        fadeIn:      { duration: 1.4, ease: 'power1.out' },
        blurIn:      { duration: 1.2, ease: 'power2.out' },
        clipUp:      { duration: 1,   ease: 'power3.inOut' },
        clipDown:    { duration: 1,   ease: 'power3.inOut' },
        tiltIn:      { duration: 1.4, ease: 'power3.out' },
        typeWriter:  { duration: 0.05, ease: 'none', stagger: 0.03 },
        drawSVG:     { duration: 2,   ease: 'power2.inOut' },
        parallax:    { duration: 1,   ease: 'none' },
        splitWords:  { duration: 0.8, ease: 'power3.out', stagger: 0.05 },
        slideIn:     { duration: 1,   ease: 'power3.out' },
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
        var dur = getClassModifier(el, 'duration', overrides.duration != null ? overrides.duration : d.duration);
        if (config.speedMultiplier && config.speedMultiplier !== 1) {
            dur = dur * config.speedMultiplier;
        }
        return {
            duration: dur,
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

        // Debug markers (set via window.__FX_DEBUG_MARKERS__ or WP plugin toggle)
        if (window.__FX_DEBUG_MARKERS__) st.markers = true;

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

    function fadeIn(el, opts) {
        opts = opts || {};
        var o = resolveOptions(el, 'fadeIn', opts);

        var tweenVars = {
            opacity: 0,
            scale: opts.scale != null ? opts.scale : 0.95,
            duration: o.duration,
            ease: o.ease,
            delay: o.delay,
        };

        if (opts.trigger === 'scroll' || opts.scrollTrigger) {
            tweenVars.scrollTrigger = buildScrollTrigger(el, opts.scrollTrigger || {});
        }

        gsap.from(el, tweenVars);
    }

    function blurIn(el, opts) {
        opts = opts || {};
        var o = resolveOptions(el, 'blurIn', opts);

        var tweenVars = {
            filter: 'blur(' + (opts.blur != null ? opts.blur : 12) + 'px)',
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

    function clipUp(el, opts) {
        opts = opts || {};
        var o = resolveOptions(el, 'clipUp', opts);

        var tweenVars = {
            clipPath: 'inset(100% 0 0 0)',
            duration: o.duration,
            ease: o.ease,
            delay: o.delay,
        };

        if (opts.trigger === 'scroll' || opts.scrollTrigger) {
            tweenVars.scrollTrigger = buildScrollTrigger(el, opts.scrollTrigger || {});
        }

        gsap.from(el, tweenVars);
    }

    function clipDown(el, opts) {
        opts = opts || {};
        var o = resolveOptions(el, 'clipDown', opts);

        var tweenVars = {
            clipPath: 'inset(0 0 100% 0)',
            duration: o.duration,
            ease: o.ease,
            delay: o.delay,
        };

        if (opts.trigger === 'scroll' || opts.scrollTrigger) {
            tweenVars.scrollTrigger = buildScrollTrigger(el, opts.scrollTrigger || {});
        }

        gsap.from(el, tweenVars);
    }

    function tiltIn(el, opts) {
        opts = opts || {};
        var o = resolveOptions(el, 'tiltIn', opts);

        gsap.fromTo(el, {
            rotationX: opts.rotationX != null ? opts.rotationX : 45,
            scale: opts.scale != null ? opts.scale : 0.8,
            opacity: opts.opacity != null ? opts.opacity : 0,
            transformPerspective: opts.perspective != null ? opts.perspective : 1000,
            transformOrigin: opts.transformOrigin || 'center bottom',
        }, {
            rotationX: 0,
            scale: 1,
            opacity: 1,
            transformPerspective: 1000,
            ease: o.ease,
            scrollTrigger: {
                trigger: (opts.scrollTrigger && opts.scrollTrigger.trigger) || el,
                start: config.scrollStart || 'top 85%',
                end: opts.end || 'top 20%',
                scrub: opts.scrub != null ? opts.scrub : 0.6,
            },
        });
    }

    function typeWriter(el, opts) {
        opts = opts || {};
        var o = resolveOptions(el, 'typeWriter', opts);

        var split = new SplitText(el, { type: 'chars' });
        gsap.set(split.chars, { opacity: 0 });

        var tweenVars = {
            opacity: 1,
            duration: o.duration,
            ease: o.ease,
            stagger: o.stagger,
            delay: o.delay,
        };

        if (opts.trigger === 'scroll' || opts.scrollTrigger) {
            tweenVars.scrollTrigger = buildScrollTrigger(el, opts.scrollTrigger || {});
        }

        gsap.to(split.chars, tweenVars);
    }

    function drawSVG(el, opts) {
        opts = opts || {};
        var o = resolveOptions(el, 'drawSVG', opts);

        var paths = el.tagName === 'path' || el.tagName === 'line' || el.tagName === 'circle' || el.tagName === 'polyline'
            ? [el]
            : el.querySelectorAll('path, line, circle, polyline, polygon, ellipse, rect');

        if (!paths.length) return;

        Array.prototype.forEach.call(paths, function(path) {
            if (typeof path.getTotalLength === 'function') {
                var len = path.getTotalLength();
                gsap.set(path, { strokeDasharray: len, strokeDashoffset: len });
            }
        });

        // Scrub mode: SVG draws as user scrolls (class fx-scrub-[0.6] or opts.scrub)
        var scrubVal = getClassModifier(el, 'scrub', opts.scrub != null ? opts.scrub : null);
        if (scrubVal !== null) {
            gsap.to(paths, {
                strokeDashoffset: 0,
                ease: o.ease,
                scrollTrigger: {
                    trigger: (opts.scrollTrigger && opts.scrollTrigger.trigger) || el,
                    start: config.scrollStart || 'top 85%',
                    end: opts.end || 'top 20%',
                    scrub: scrubVal === true || scrubVal === 'true' ? true : scrubVal,
                },
            });
            return;
        }

        var tweenVars = {
            strokeDashoffset: 0,
            duration: o.duration,
            ease: o.ease,
            delay: o.delay,
        };

        if (opts.trigger === 'scroll' || opts.scrollTrigger) {
            tweenVars.scrollTrigger = buildScrollTrigger(el, opts.scrollTrigger || {});
        }

        gsap.to(paths, tweenVars);
    }

    function parallax(el, opts) {
        opts = opts || {};
        // Read y from modifier class fx-y-[80] or opts or default 50
        var yShift = getClassModifier(el, 'y', opts.y != null ? opts.y : 50);

        gsap.fromTo(el, {
            y: -yShift,
        }, {
            y: yShift,
            ease: 'none',
            scrollTrigger: {
                trigger: (opts.scrollTrigger && opts.scrollTrigger.trigger) || el,
                start: config.scrollStart || 'top 85%',
                end: opts.end || 'bottom top',
                scrub: opts.scrub != null ? opts.scrub : true,
            },
        });
    }

    function splitWords(el, opts) {
        opts = opts || {};
        var o = resolveOptions(el, 'splitWords', opts);

        var split = new SplitText(el, { type: 'words' });

        var tweenVars = {
            y: opts.y != null ? opts.y : 30,
            opacity: 0,
            duration: o.duration,
            ease: o.ease,
            stagger: o.stagger,
            delay: o.delay,
        };

        if (opts.trigger === 'scroll' || opts.scrollTrigger) {
            tweenVars.scrollTrigger = buildScrollTrigger(el, opts.scrollTrigger || {});
        }

        gsap.from(split.words, tweenVars);
    }

    function slideIn(el, opts) {
        opts = opts || {};
        var o = resolveOptions(el, 'slideIn', opts);
        var direction = opts.direction || 'left';
        var xVal = opts.x != null ? opts.x : 100;

        var tweenVars = {
            x: direction === 'left' ? -xVal : xVal,
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
        'fx-fade-in':     fadeIn,
        'fx-blur-in':     blurIn,
        'fx-clip-up':     clipUp,
        'fx-clip-down':   clipDown,
        'fx-type-writer': typeWriter,
        'fx-draw-svg':    drawSVG,
        'fx-split-words': splitWords,
        'fx-slide-left':  function(el, opts) { opts = opts || {}; opts.direction = 'left'; slideIn(el, opts); },
        'fx-slide-right': function(el, opts) { opts = opts || {}; opts.direction = 'right'; slideIn(el, opts); },
    };

    var effectsByName = {
        textReveal: textReveal,
        reveal: reveal,
        spinReveal: spinReveal,
        bgReveal: bgReveal,
        scaleIn: scaleIn,
        fadeIn: fadeIn,
        blurIn: blurIn,
        clipUp: clipUp,
        clipDown: clipDown,
        tiltIn: tiltIn,
        typeWriter: typeWriter,
        drawSVG: drawSVG,
        parallax: parallax,
        splitWords: splitWords,
        slideIn: slideIn,
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

    function isExcluded(el) {
        if (!config.excludeSelectors) return false;
        try { return el.matches(config.excludeSelectors); } catch (e) { return false; }
    }

    // ── Init ────────────────────────────────────

    function init() {
        var processed = new Set();

        Object.keys(effects).forEach(function (name) {
            var fn = effects[name];

            // 1. Page-load variant: .fx-<name>-pl
            var plGroups = groupByParent(document.querySelectorAll('.' + name + '-pl'));
            plGroups.forEach(function (group) {
                group = group.filter(function (el) { return !isExcluded(el); });
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
                group = group.filter(function (el) { return !isExcluded(el); });
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
                        .filter(function (el) { return !processed.has(el) && !isExcluded(el); });
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

        // 4. Scrub-based effects — always scroll-linked, processed before tagMap.
        document.querySelectorAll('.fx-tilt-in-st, .fx-tilt-in-pl, .fx-tilt-in').forEach(function (el) {
            if (!processed.has(el) && !isExcluded(el)) {
                tiltIn(el);
                processed.add(el);
            }
        });
        document.querySelectorAll('.fx-parallax-st, .fx-parallax-pl, .fx-parallax').forEach(function (el) {
            if (!processed.has(el) && !isExcluded(el)) {
                parallax(el);
                processed.add(el);
            }
        });
        document.querySelectorAll('.fx-draw-svg-scrub').forEach(function (el) {
            if (!processed.has(el) && !isExcluded(el)) {
                drawSVG(el, { scrub: getClassModifier(el, 'scrub', 0.6) });
                processed.add(el);
            }
        });

        // 5. Tag-based auto-animation inside sections
        if (config.tagMap && config.sectionSelector) {
            document.querySelectorAll(config.sectionSelector).forEach(function (section) {
                Object.keys(config.tagMap).forEach(function (selector) {
                    var effectName = config.tagMap[selector];
                    var fn = effects['fx-' + camelToKebab(effectName)] || effectsByName[effectName];
                    if (!fn) return;

                    var els = Array.from(section.querySelectorAll(selector))
                        .filter(function (el) { return !processed.has(el) && !isExcluded(el); });
                    if (els.length === 0) return;

                    var groups = groupByParent(els);
                    groups.forEach(function (group) {
                        applyScrollGroup(fn, group, section);
                        group.forEach(function (el) { processed.add(el); });
                    });
                });
            });
        }
        // 5. fx-stagger-all-[selector] — target children, effect from sibling class
        //    Requires an effect class on the same element (e.g. fx-reveal-st).
        document.querySelectorAll('[class*="fx-stagger-all-"]').forEach(function (container) {
            // Parse selector from fx-stagger-all-[img,p]
            var childSelector = null;
            for (var ci = 0; ci < container.classList.length; ci++) {
                var cls = container.classList[ci];
                if (cls.indexOf('fx-stagger-all-[') === 0 && cls.charAt(cls.length - 1) === ']') {
                    childSelector = cls.slice('fx-stagger-all-['.length, -1);
                    break;
                }
            }
            if (!childSelector) return;

            // Find which effect class is on this container
            var effectFn = null;
            var effectName = null;
            Object.keys(effects).forEach(function (name) {
                if (container.classList.contains(name + '-st') ||
                    container.classList.contains(name + '-pl') ||
                    container.classList.contains(name)) {
                    effectFn = effects[name];
                    effectName = name;
                }
            });
            if (!effectFn) return; // No effect class paired — do nothing

            var isScroll = container.classList.contains(effectName + '-st') ||
                           container.classList.contains(effectName);
            var children = Array.from(container.querySelectorAll(childSelector))
                .filter(function (el) { return !processed.has(el); });
            if (children.length === 0) return;

            children.forEach(function (child, i) {
                var opts = { delay: i * 0.15 };
                if (isScroll) {
                    opts.trigger = 'scroll';
                    opts.scrollTrigger = { trigger: child };
                }
                effectFn(child, opts);
                processed.add(child);
            });
        });

    }

    // ── Boot ────────────────────────────────────

    function applyPreConfig() {
        var pre = window.__FX_CONFIG__;
        if (!pre) return;
        if (pre.sectionSelector !== undefined) config.sectionSelector = pre.sectionSelector;
        if (pre.scrollStart !== undefined) config.scrollStart = pre.scrollStart;
        if (pre.scrollOnce !== undefined) config.scrollOnce = pre.scrollOnce;
        if (pre.tagMap !== undefined) config.tagMap = pre.tagMap;
        if (pre.disableMobile !== undefined) config.disableMobile = pre.disableMobile;
        if (pre.mobileBreakpoint !== undefined) config.mobileBreakpoint = pre.mobileBreakpoint;
        if (pre.speedMultiplier !== undefined) config.speedMultiplier = pre.speedMultiplier;
        if (pre.respectReducedMotion !== undefined) config.respectReducedMotion = pre.respectReducedMotion;
        if (pre.excludeSelectors !== undefined) config.excludeSelectors = pre.excludeSelectors;
    }

    function boot() {
        applyPreConfig();

        // Skip animations if OS reduced motion is enabled
        if (config.respectReducedMotion && window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        // Skip animations on mobile
        if (config.disableMobile && window.innerWidth <= config.mobileBreakpoint) {
            return;
        }

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
        fadeIn: fadeIn,
        blurIn: blurIn,
        clipUp: clipUp,
        clipDown: clipDown,
        tiltIn: tiltIn,
        typeWriter: typeWriter,
        drawSVG: drawSVG,
        parallax: parallax,
        splitWords: splitWords,
        slideIn: slideIn,
        init: init,
    };
})();
