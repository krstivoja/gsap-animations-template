/**
 * Shared documentation layout for Fancoolo FX.
 *
 * Single HTML page — reads the page ID from the URL hash.
 * Example: documentation/#effects loads effects.md
 * No hash or #index shows the overview.
 */
(function () {
    var pages = [
        { id: 'installation',   title: 'Installation',           file: 'installation.md',   group: 'Getting Started' },
        { id: 'effects',        title: 'Effects',                 file: 'effects.md',        group: 'Core Concepts' },
        { id: 'trigger-modes',  title: 'Trigger Modes',           file: 'trigger-modes.md',  group: 'Core Concepts' },
        { id: 'modifiers',      title: 'Modifiers',               file: 'modifiers.md',      group: 'Core Concepts' },
        { id: 'configuration',  title: 'Configuration',           file: 'configuration.md',  group: 'Core Concepts' },
        { id: 'javascript-api', title: 'JavaScript API',          file: 'javascript-api.md', group: 'Advanced' },
        { id: 'wordpress',      title: 'WordPress & Gutenberg',   file: 'wordpress.md',      group: 'Advanced' },
        { id: 'examples',       title: 'Examples',                file: 'examples.md',       group: 'Advanced' },
        { id: 'skill',          title: 'Claude Code Skill',       file: 'skill.md',          group: 'Advanced' },
    ];

    // Read page from hash
    function getCurrentId() {
        var hash = window.location.hash.replace('#', '');
        return hash || 'index';
    }

    var currentId = getCurrentId();

    // Find current page object
    function findPage(id) {
        for (var i = 0; i < pages.length; i++) {
            if (pages[i].id === id) return pages[i];
        }
        return null;
    }

    // Group pages by section
    var groups = {};
    var groupOrder = [];
    pages.forEach(function (p) {
        if (!groups[p.group]) {
            groups[p.group] = [];
            groupOrder.push(p.group);
        }
        groups[p.group].push(p);
    });

    // GitHub icon SVG
    var ghIcon = '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>';

    // Build sidebar nav
    function buildSidebar(activeId) {
        var html = '';
        groupOrder.forEach(function (group) {
            html += '<div class="mb-6">';
            html += '<h3 class="text-[11px] font-bold text-brand-orange uppercase tracking-wider mb-2">' + group + '</h3>';
            html += '<ul class="space-y-0.5">';
            groups[group].forEach(function (p) {
                var active = p.id === activeId;
                var cls = active
                    ? 'nav-item block px-3 py-1.5 text-sm rounded-md font-semibold text-brand-blue bg-brand-blue-light'
                    : 'nav-item block px-3 py-1.5 text-sm rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition';
                html += '<li><a href="#' + p.id + '" class="' + cls + '" data-page="' + p.id + '">' + p.title + '</a></li>';
            });
            html += '</ul></div>';
        });
        html += '<div class="pt-4 border-t border-gray-200/60">';
        html += '<a href="https://www.npmjs.com/package/fancoolo-fx" class="flex items-center gap-2 px-3 py-1.5 text-xs font-mono text-gray-400 hover:text-brand-blue transition">npm i fancoolo-fx</a>';
        html += '</div>';
        return html;
    }

    function buildPagination(activeId) {
        var flatIndex = -1;
        pages.forEach(function (p, i) { if (p.id === activeId) flatIndex = i; });
        var prev = flatIndex > 0 ? pages[flatIndex - 1] : null;
        var next = flatIndex < pages.length - 1 ? pages[flatIndex + 1] : null;

        var html = '<div class="flex justify-between items-center mt-16 pt-8 border-t border-gray-200/60">';
        if (prev) {
            html += '<a href="#' + prev.id + '" class="group flex flex-col">';
            html += '<span class="text-xs text-gray-400 mb-1">&larr; Previous</span>';
            html += '<span class="text-sm font-semibold text-brand-blue group-hover:underline">' + prev.title + '</span></a>';
        } else {
            html += '<div></div>';
        }
        if (next) {
            html += '<a href="#' + next.id + '" class="group flex flex-col text-right">';
            html += '<span class="text-xs text-gray-400 mb-1">Next &rarr;</span>';
            html += '<span class="text-sm font-semibold text-brand-blue group-hover:underline">' + next.title + '</span></a>';
        }
        html += '</div>';
        return html;
    }

    function buildOverview() {
        var cards = '';
        pages.forEach(function (p) {
            cards += '<a href="#' + p.id + '" class="block p-5 bg-white rounded-xl border border-gray-200/60 hover:border-brand-blue/30 hover:shadow-md transition">';
            cards += '<h3 class="font-bold text-base mb-1">' + p.title + '</h3>';
            cards += '<p class="text-sm text-gray-500">' + p.group + '</p></a>';
        });
        return '<h1>Fancoolo FX Documentation</h1>' +
               '<p class="text-lg text-gray-600">A class-driven GSAP animation wrapper for WordPress and static sites.</p>' +
               '<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-8 not-prose">' + cards + '</div>';
    }

    // Render shell once
    document.getElementById('app').innerHTML =
        '<header class="bg-white/80 backdrop-blur-md border-b border-gray-200/60 sticky top-0 z-50">' +
        '<div class="max-w-7xl mx-auto px-6 h-14 flex items-center justify-between">' +
        '<a href="../" class="flex items-center gap-2"><span class="text-lg font-extrabold tracking-tight">Fancoolo</span><span class="text-xs font-bold text-white bg-brand-blue px-2 py-0.5 rounded">FX</span></a>' +
        '<div class="flex items-center gap-3">' +
        '<a href="../" class="text-sm text-gray-500 hover:text-gray-900 font-medium hidden sm:inline">Live Demo</a>' +
        '<a href="https://github.com/krstivoja/fancoolo-fx" class="flex items-center gap-2 text-sm bg-gray-900 text-white px-3.5 py-1.5 rounded-full font-medium hover:bg-gray-700 transition">' + ghIcon + ' GitHub</a>' +
        '</div></div></header>' +
        '<div class="max-w-7xl mx-auto flex min-h-screen">' +
        '<aside id="sidebar" class="w-64 shrink-0 border-r border-gray-200/60 bg-white/50 hidden lg:block">' +
        '<nav id="sidebar-nav" class="sticky top-14 p-6 max-h-[calc(100vh-3.5rem)] overflow-y-auto"></nav></aside>' +
        '<button id="mobile-nav-btn" class="lg:hidden fixed bottom-6 right-6 z-50 bg-brand-blue text-white w-12 h-12 rounded-full shadow-lg flex items-center justify-center">' +
        '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>' +
        '<main class="flex-1 min-w-0"><div class="px-6 lg:px-12 py-10 max-w-3xl">' +
        '<div id="doc-content" class="doc-prose prose prose-gray prose-lg max-w-none">' +
        '<p class="text-gray-400">Loading...</p></div>' +
        '<div id="pagination"></div>' +
        '</div></main></div>' +
        '<footer class="border-t border-gray-200/60 bg-white/30">' +
        '<div class="max-w-7xl mx-auto px-6 py-8 flex items-center justify-between text-sm text-gray-400">' +
        '<span>Fancoolo FX &mdash; A GSAP animation wrapper</span>' +
        '<a href="https://gsap.com" class="hover:text-brand-blue transition">Built on GSAP</a>' +
        '</div></footer>';

    // Load a page by ID
    function loadPage(id) {
        var contentEl = document.getElementById('doc-content');
        var paginationEl = document.getElementById('pagination');
        var sidebarNav = document.getElementById('sidebar-nav');

        currentId = id;
        sidebarNav.innerHTML = buildSidebar(id);

        if (id === 'index' || !findPage(id)) {
            contentEl.innerHTML = buildOverview();
            paginationEl.innerHTML = '';
            document.title = 'Documentation — Fancoolo FX';
        } else {
            var page = findPage(id);
            contentEl.innerHTML = '<p class="text-gray-400">Loading...</p>';
            document.title = page.title + ' — Fancoolo FX';
            fetch(page.file)
                .then(function (r) { return r.text(); })
                .then(function (md) {
                    contentEl.innerHTML = marked.parse(md);
                    paginationEl.innerHTML = buildPagination(id);
                })
                .catch(function () {
                    contentEl.innerHTML = '<p class="text-red-500">Failed to load page.</p>';
                });
        }

        window.scrollTo(0, 0);
    }

    // Initial load
    loadPage(currentId);

    // Handle hash changes (sidebar clicks, prev/next, browser back/forward)
    window.addEventListener('hashchange', function () {
        loadPage(getCurrentId());
    });

    // Mobile nav toggle
    document.getElementById('mobile-nav-btn').addEventListener('click', function () {
        var aside = document.getElementById('sidebar');
        aside.classList.toggle('hidden');
        aside.classList.toggle('fixed');
        aside.classList.toggle('inset-0');
        aside.classList.toggle('z-40');
        aside.classList.toggle('block');
    });
})();
