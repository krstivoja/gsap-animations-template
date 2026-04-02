jQuery(function($) {
	// Fallback copy that works on HTTP (no clipboard API needed)
	function copyText(text) {
		var ta = document.createElement('textarea');
		ta.value = text;
		ta.style.position = 'fixed';
		ta.style.opacity = '0';
		document.body.appendChild(ta);
		ta.select();
		document.execCommand('copy');
		document.body.removeChild(ta);
	}

	// Tab switching
	$('.ffx-tab').on('click', function() {
		var tab = $(this).data('tab');
		$('.ffx-tab').removeClass('active');
		$(this).addClass('active');
		$('.ffx-panel').removeClass('active');
		$('.ffx-panel[data-panel="' + tab + '"]').addClass('active');
	});

	// Copy code blocks
	$('.ffx-copy-btn').on('click', function() {
		var btn = $(this);
		var target = $('#' + btn.data('target'));
		copyText(target.text());
		btn.text('Copied!').addClass('copied');
		setTimeout(function() { btn.text('Copy').removeClass('copied'); }, 1500);
	});

	// Copy class on click
	$('[data-copy]').on('click', function() {
		var el = $(this);
		var original = el.text();
		copyText(original);
		el.text('Copied!').addClass('copied');
		setTimeout(function() { el.text(original).removeClass('copied'); }, 1000);
	});

	// ── Tag Map repeater ───────────────────────
	var effectOptions = '<option value="textReveal">Text Reveal</option>'
		+ '<option value="reveal">Reveal</option>'
		+ '<option value="spinReveal">Spin Reveal</option>'
		+ '<option value="bgReveal">BG Reveal</option>'
		+ '<option value="scaleIn">Scale In</option>'
		+ '<option value="fadeIn">Fade In</option>'
		+ '<option value="blurIn">Blur In</option>'
		+ '<option value="clipUp">Clip Up</option>'
		+ '<option value="clipDown">Clip Down</option>'
		+ '<option value="tiltIn">Tilt In</option>'
		+ '<option value="typeWriter">Type Writer</option>'
		+ '<option value="drawSVG">Draw SVG</option>'
		+ '<option value="parallax">Parallax</option>'
		+ '<option value="splitWords">Split Words</option>'
		+ '<option value="slideIn">Slide In</option>';

	function reindexTagMap() {
		$('#ffx-tagmap-rows .ffx-tagmap-row').each(function(i) {
			$(this).find('input').attr('name', 'fancoolo_fx_tag_map[' + i + '][selector]');
			$(this).find('select').attr('name', 'fancoolo_fx_tag_map[' + i + '][effect]');
		});
	}

	$('#ffx-tagmap-add').on('click', function() {
		var i = $('#ffx-tagmap-rows .ffx-tagmap-row').length;
		var row = '<div class="ffx-tagmap-row">'
			+ '<input type="text" name="fancoolo_fx_tag_map[' + i + '][selector]" placeholder="h1,h2,h3">'
			+ '<select name="fancoolo_fx_tag_map[' + i + '][effect]">' + effectOptions + '</select>'
			+ '<button type="button" class="ffx-tagmap-remove" title="Remove">&times;</button>'
			+ '</div>';
		$('#ffx-tagmap-rows').append(row);
	});

	$('#ffx-tagmap-rows').on('click', '.ffx-tagmap-remove', function() {
		$(this).closest('.ffx-tagmap-row').remove();
		reindexTagMap();
	});

	// ── Mobile breakpoint toggle ───────────────
	$('#ffx-disable-mobile').on('change', function() {
		$('.ffx-mobile-breakpoint').toggle(this.checked);
	});

	// ── Export Settings ─────────────────────────
	$('#ffx-export').on('click', function() {
		var tagMap = [];
		$('#ffx-tagmap-rows .ffx-tagmap-row').each(function() {
			tagMap.push({
				selector: $(this).find('input').val(),
				effect: $(this).find('select').val()
			});
		});

		var data = {
			scroll_start: $('#ffx-scroll-start').val(),
			scroll_once: $('#ffx-scroll-once').is(':checked') ? '1' : '0',
			section_selector: $('#ffx-section-selector').val(),
			exclude_selectors: $('#ffx-exclude-selectors').val(),
			debug_markers: $('#ffx-debug-markers').is(':checked') ? '1' : '0',
			disable_mobile: $('#ffx-disable-mobile').is(':checked') ? '1' : '0',
			mobile_breakpoint: $('#ffx-mobile-breakpoint').val(),
			speed_multiplier: $('#ffx-speed-multiplier').val(),
			respect_reduced_motion: $('#ffx-respect-reduced-motion').is(':checked') ? '1' : '0',
			tag_map: tagMap,
			custom_js: ''
		};

		// Get CodeMirror content if available
		var cmEl = document.querySelector('.CodeMirror');
		if (cmEl && cmEl.CodeMirror) {
			data.custom_js = cmEl.CodeMirror.getValue();
		} else {
			data.custom_js = $('#fancoolo-fx-editor').val();
		}

		var blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
		var url = URL.createObjectURL(blob);
		var a = document.createElement('a');
		a.href = url;
		a.download = 'fancoolo-fx-settings.json';
		a.click();
		URL.revokeObjectURL(url);
	});

	// ── Import Settings ─────────────────────────
	$('#ffx-import-btn').on('click', function() {
		$('#ffx-import-file').click();
	});

	$('#ffx-import-file').on('change', function(e) {
		var file = e.target.files[0];
		if (!file) return;

		var reader = new FileReader();
		reader.onload = function(ev) {
			try {
				var data = JSON.parse(ev.target.result);
			} catch (err) {
				alert('Invalid JSON file.');
				return;
			}

			// Populate fields
			if (data.scroll_start !== undefined) $('#ffx-scroll-start').val(data.scroll_start);
			if (data.section_selector !== undefined) $('#ffx-section-selector').val(data.section_selector);
			if (data.exclude_selectors !== undefined) $('#ffx-exclude-selectors').val(data.exclude_selectors);
			if (data.mobile_breakpoint !== undefined) $('#ffx-mobile-breakpoint').val(data.mobile_breakpoint);
			if (data.speed_multiplier !== undefined) $('#ffx-speed-multiplier').val(data.speed_multiplier);

			$('#ffx-scroll-once').prop('checked', data.scroll_once === '1');
			$('#ffx-debug-markers').prop('checked', data.debug_markers === '1');
			$('#ffx-disable-mobile').prop('checked', data.disable_mobile === '1').trigger('change');
			$('#ffx-respect-reduced-motion').prop('checked', data.respect_reduced_motion === '1');

			// Rebuild tag map
			$('#ffx-tagmap-rows').empty();
			if (data.tag_map && data.tag_map.length) {
				data.tag_map.forEach(function(row, i) {
					var html = '<div class="ffx-tagmap-row">'
						+ '<input type="text" name="fancoolo_fx_tag_map[' + i + '][selector]" value="' + (row.selector || '') + '" placeholder="h1,h2,h3">'
						+ '<select name="fancoolo_fx_tag_map[' + i + '][effect]">' + effectOptions + '</select>'
						+ '<button type="button" class="ffx-tagmap-remove" title="Remove">&times;</button>'
						+ '</div>';
					$('#ffx-tagmap-rows').append(html);
					$('#ffx-tagmap-rows .ffx-tagmap-row:last select').val(row.effect);
				});
			}

			// Set editor content
			if (data.custom_js !== undefined) {
				var cmEl = document.querySelector('.CodeMirror');
				if (cmEl && cmEl.CodeMirror) {
					cmEl.CodeMirror.setValue(data.custom_js);
				} else {
					$('#fancoolo-fx-editor').val(data.custom_js);
				}
			}

			alert('Settings imported. Click Save Changes to apply.');
		};
		reader.readAsText(file);

		// Reset file input so same file can be re-imported
		$(this).val('');
	});

	// ── Reset to Defaults ───────────────────────
	$('#ffx-reset').on('click', function() {
		if (!confirm('Reset all settings to defaults? You still need to click Save Changes to apply.')) return;

		$('#ffx-scroll-start').val('top 85%');
		$('#ffx-section-selector').val('section');
		$('#ffx-exclude-selectors').val('');
		$('#ffx-mobile-breakpoint').val('768');
		$('#ffx-speed-multiplier').val('1');

		$('#ffx-scroll-once').prop('checked', true);
		$('#ffx-debug-markers').prop('checked', false);
		$('#ffx-disable-mobile').prop('checked', false).trigger('change');
		$('#ffx-respect-reduced-motion').prop('checked', true);

		$('#ffx-tagmap-rows').empty();

		var cmEl = document.querySelector('.CodeMirror');
		if (cmEl && cmEl.CodeMirror) {
			cmEl.CodeMirror.setValue('');
		} else {
			$('#fancoolo-fx-editor').val('');
		}
	});
});
