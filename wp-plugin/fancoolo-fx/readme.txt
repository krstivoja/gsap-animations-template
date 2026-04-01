=== Fancoolo FX ===
Contributors: krstivoja
Tags: animation, gsap, scroll, gutenberg, blocks
Requires at least: 5.8
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: ISC

A class-driven GSAP animation wrapper. Add CSS classes in Gutenberg and get animations.

== Description ==

Fancoolo FX wraps GSAP, ScrollTrigger, and SplitText into a simple class-based animation system designed for WordPress and Gutenberg.

**How it works:**

1. Activate the plugin
2. Edit any block in Gutenberg
3. Add a class like `fx-text-reveal-st` in the "Additional CSS classes" field
4. The block animates on scroll

**5 Built-in Effects:**

* **Text Reveal** (`fx-text-reveal`) — Split text into lines with masked reveal
* **Reveal** (`fx-reveal`) — Slide up with fade
* **Spin Reveal** (`fx-spin-reveal`) — Rotate and scale in
* **BG Reveal** (`fx-bg-reveal`) — Background slide up
* **Scale In** (`fx-scale-in`) — Scale up with fade

**Trigger Modes:**

* `-pl` suffix — Animate on page load
* `-st` suffix — Animate on scroll
* No suffix inside a `<section>` — Auto scroll-triggered

**Modifier Classes:**

* `fx-duration-[2]` — Custom duration
* `fx-delay-[0.3]` — Custom delay
* `fx-stagger-[0.25]` — Custom stagger
* `fx-ease-[power2.inOut]` — Custom easing
* `fx-start-[top center]` — Custom scroll trigger position

**Custom JavaScript Editor:**

Go to Appearance > Fancoolo FX to write custom animation sequences or configure tagMap for zero-class auto-animation.

== Installation ==

1. Upload the `fancoolo-fx` folder to `/wp-content/plugins/`
2. Activate through the Plugins menu
3. Add `.fx-*` classes to blocks in Gutenberg

== Frequently Asked Questions ==

= Do I need to write any JavaScript? =

No. Just add CSS classes to blocks in Gutenberg. For advanced use, the plugin includes a JavaScript editor under Appearance > Fancoolo FX.

= Does it work with any theme? =

Yes. The plugin loads GSAP and the animation wrapper on the frontend regardless of theme.

= Can I customize the scroll trigger position? =

Yes. Use the `fx-start-[top center]` modifier class, or set `scrollStart` in the custom JavaScript editor.

== Changelog ==

= 1.0.0 =
* Initial release
* 5 animation effects
* Page load, scroll trigger, and section trigger modes
* Modifier classes for timing overrides
* Custom JavaScript editor with CodeMirror
* Built-in quick reference table
