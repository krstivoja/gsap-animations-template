/**
 * Shared documentation layout for Fancoolo FX.
 *
 * Each page sets window.DOC_PAGE = { id, file } before loading this script.
 * This script builds the full page shell (header, sidebar, footer) and
 * loads the markdown file into the content area.
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

    var currentPage = window.DOC_PAGE || {};
    var currentId = currentPage.id || '';

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

    // Build sidebar nav
    function buildSidebar() {
        var html = '';
        groupOrder.forEach(function (group) {
            html += '<div class="mb-6">';
            html += '<h3 class="text-[11px] font-bold text-brand-orange uppercase tracking-wider mb-2">' + group + '</h3>';
            html += '<ul class="space-y-0.5">';
            groups[group].forEach(function (p) {
                var active = p.id === currentId;
                var cls = active
                    ? 'block px-3 py-1.5 text-sm rounded-md font-semibold text-brand-blue bg-brand-blue-light'
                    : 'block px-3 py-1.5 text-sm rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition';
                var href = p.id === currentId ? '#' : p.id + '.html';
                html += '<li><a href="' + href + '" class="' + cls + '">' + p.title + '</a></li>';
            });
            html += '</ul></div>';
        });
        html += '<div class="pt-4 border-t border-gray-200/60">';
        html += '<a href="https://www.npmjs.com/package/fancoolo-fx" class="flex items-center gap-2 px-3 py-1.5 text-xs font-mono text-gray-400 hover:text-brand-blue transition">npm i fancoolo-fx</a>';
        html += '</div>';
        return html;
    }

    // Find prev/next pages
    var flatIndex = -1;
    pages.forEach(function (p, i) { if (p.id === currentId) flatIndex = i; });
    var prevPage = flatIndex > 0 ? pages[flatIndex - 1] : null;
    var nextPage = flatIndex < pages.length - 1 ? pages[flatIndex + 1] : null;

    function buildPagination() {
        var html = '<div class="flex justify-between items-center mt-16 pt-8 border-t border-gray-200/60">';
        if (prevPage) {
            html += '<a href="' + prevPage.id + '.html" class="group flex flex-col">';
            html += '<span class="text-xs text-gray-400 mb-1">&larr; Previous</span>';
            html += '<span class="text-sm font-semibold text-brand-blue group-hover:underline">' + prevPage.title + '</span>';
            html += '</a>';
        } else {
            html += '<div></div>';
        }
        if (nextPage) {
            html += '<a href="' + nextPage.id + '.html" class="group flex flex-col text-right">';
            html += '<span class="text-xs text-gray-400 mb-1">Next &rarr;</span>';
            html += '<span class="text-sm font-semibold text-brand-blue group-hover:underline">' + nextPage.title + '</span>';
            html += '</a>';
        }
        html += '</div>';
        return html;
    }

    // GitHub icon SVG
    var ghIcon = '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>';

    // Render page shell
    document.getElementById('app').innerHTML =
        // Header
        '<header class="bg-white/80 backdrop-blur-md border-b border-gray-200/60 sticky top-0 z-50">' +
        '<div class="max-w-7xl mx-auto px-6 h-14 flex items-center justify-between">' +
        '<a href="../" class="flex items-center gap-2"><span class="text-lg font-extrabold tracking-tight">Fancoolo</span><span class="text-xs font-bold text-white bg-brand-blue px-2 py-0.5 rounded">FX</span></a>' +
        '<div class="flex items-center gap-3">' +
        '<a href="../" class="text-sm text-gray-500 hover:text-gray-900 font-medium hidden sm:inline">Live Demo</a>' +
        '<a href="https://github.com/krstivoja/gsap-animations-template" class="flex items-center gap-2 text-sm bg-gray-900 text-white px-3.5 py-1.5 rounded-full font-medium hover:bg-gray-700 transition">' + ghIcon + ' GitHub</a>' +
        '</div></div></header>' +

        '<div class="max-w-7xl mx-auto flex min-h-screen">' +

        // Sidebar
        '<aside id="sidebar" class="w-64 shrink-0 border-r border-gray-200/60 bg-white/50 hidden lg:block">' +
        '<nav class="sticky top-14 p-6 max-h-[calc(100vh-3.5rem)] overflow-y-auto">' +
        buildSidebar() +
        '</nav></aside>' +

        // Mobile toggle
        '<button id="mobile-nav-btn" class="lg:hidden fixed bottom-6 right-6 z-50 bg-brand-blue text-white w-12 h-12 rounded-full shadow-lg flex items-center justify-center">' +
        '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>' +

        // Main
        '<main class="flex-1 min-w-0"><div class="px-6 lg:px-12 py-10 max-w-3xl">' +
        '<div id="doc-content" class="doc-prose prose prose-gray prose-lg max-w-none">' +
        '<p class="text-gray-400">Loading...</p></div>' +
        '<div id="pagination"></div>' +
        '</div></main></div>' +

        // Footer
        '<footer class="border-t border-gray-200/60 bg-white/30">' +
        '<div class="max-w-7xl mx-auto px-6 py-8 flex items-center justify-between text-sm text-gray-400">' +
        '<span>Fancoolo FX &mdash; A GSAP animation wrapper</span>' +
        '<a href="https://gsap.com" class="hover:text-brand-blue transition">Built on GSAP</a>' +
        '</div></footer>';

    // Load markdown
    var contentEl = document.getElementById('doc-content');
    var file = currentPage.file;
    if (file) {
        fetch(file)
            .then(function (r) { return r.text(); })
            .then(function (md) {
                contentEl.innerHTML = marked.parse(md);
                document.getElementById('pagination').innerHTML = buildPagination();
            })
            .catch(function () {
                contentEl.innerHTML = '<p class="text-red-500">Failed to load page.</p>';
            });
    }

    // Mobile nav
    document.getElementById('mobile-nav-btn').addEventListener('click', function () {
        var aside = document.getElementById('sidebar');
        aside.classList.toggle('hidden');
        aside.classList.toggle('fixed');
        aside.classList.toggle('inset-0');
        aside.classList.toggle('z-40');
        aside.classList.toggle('block');
    });
})();
