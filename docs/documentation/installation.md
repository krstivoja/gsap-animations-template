# Installation

## Requirements

- [GSAP 3.x](https://gsap.com) (included via npm)
- ScrollTrigger plugin (included with GSAP)
- SplitText plugin (included with GSAP)

All GSAP plugins are free as of GSAP 3.12.

## Step 1: Install dependencies

```bash
npm install
```

This installs GSAP into `node_modules/gsap/`.

## Step 2: Add script tags

Load the scripts in this exact order — GSAP core first, then plugins, then FX:

```html
<!-- 1. GSAP core -->
<script src="node_modules/gsap/dist/gsap.min.js"></script>

<!-- 2. GSAP plugins -->
<script src="node_modules/gsap/dist/ScrollTrigger.min.js"></script>
<script src="node_modules/gsap/dist/SplitText.min.js"></script>

<!-- 3. Fancoolo FX -->
<script src="src/fx.js"></script>
```

**Order matters.** GSAP core must load before the plugins, and all three must load before `fx.js`.

## Step 3: Add classes to your HTML

```html
<h1 class="fx-text-reveal-pl">This animates on page load</h1>
```

That's it. No JavaScript needed.

## File structure

```
your-project/
├── node_modules/
│   └── gsap/
│       └── dist/
│           ├── gsap.min.js
│           ├── ScrollTrigger.min.js
│           └── SplitText.min.js
├── src/
│   └── fx.js
├── package.json
└── index.html
```

## Using in an existing project

If you already have a project with its own `package.json`:

```bash
# Copy fx.js into your project
cp src/fx.js /path/to/your/project/js/fx.js

# Install GSAP in your project
cd /path/to/your/project
npm install gsap
```

Then add the four script tags to your HTML template.

## Verifying the installation

Open your browser's developer console. If FX loaded correctly, you can type:

```js
FX
// Should output: {config: {…}, textReveal: ƒ, reveal: ƒ, spinReveal: ƒ, …}
```

If you see `[FX] Missing dependencies`, check that the GSAP scripts are loading before `fx.js`.
