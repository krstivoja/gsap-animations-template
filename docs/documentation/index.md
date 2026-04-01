---
title: "Documentation"
permalink: /documentation/
layout: single
sidebar:
  nav: "docs"
toc: true
---

# FX Animation SDK

A lightweight, class-driven GSAP animation framework. Add a CSS class to any HTML element and it animates — no JavaScript needed per page. Built for WordPress/Gutenberg where only class names can be added to blocks.

## Quick Start

```bash
npm install
```

```html
<script src="node_modules/gsap/dist/gsap.min.js"></script>
<script src="node_modules/gsap/dist/ScrollTrigger.min.js"></script>
<script src="node_modules/gsap/dist/SplitText.min.js"></script>
<script src="src/fx.js"></script>
```

```html
<h1 class="fx-text-reveal-pl">This animates on page load</h1>
```

## Documentation

| Page | Description |
|------|-------------|
| [Installation](/documentation/installation/) | Setup, dependencies, and loading order |
| [Effects](/documentation/effects/) | All 5 animation effects with defaults and usage |
| [Trigger Modes](/documentation/trigger-modes/) | Page load, scroll trigger, section trigger |
| [Modifiers](/documentation/modifiers/) | Override timing, duration, delay, stagger, easing, and scroll position |
| [Configuration](/documentation/configuration/) | Global config, tagMap, sectionSelector, scrollStart |
| [JavaScript API](/documentation/javascript-api/) | FX global for compound sequences and dynamic content |
| [WordPress & Gutenberg](/documentation/wordpress/) | Integration guide for WordPress themes and blocks |
| [Examples](/documentation/examples/) | Full page examples, common patterns, and recipes |
