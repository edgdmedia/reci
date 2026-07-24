(function () {
	'use strict';

	let debounceTimer;
	const DEBOUNCE_MS = 300;
	const MIN_CHARS = 3;
	const AJAX_URL = window.location.origin + '/wp-admin/admin-ajax.php';

	function init() {
		const searchInputs = document.querySelectorAll('.reci-live-search');
		searchInputs.forEach(function (input) {
			input.addEventListener('input', handleInput);
			input.addEventListener('keydown', handleKeydown);
		});

		document.addEventListener('click', handleOutsideClick);
	}

	function handleInput(e) {
		const query = e.target.value.trim();

		clearTimeout(debounceTimer);

		if (query.length < MIN_CHARS) {
			hideDropdown(e.target);
			return;
		}

		debounceTimer = setTimeout(function () {
			performSearch(query, e.target);
		}, DEBOUNCE_MS);
	}

	function handleKeydown(e) {
		const dropdown = getDropdown(e.target);
		if (!dropdown) return;

		const items = dropdown.querySelectorAll('a');
		if (items.length === 0) return;

		if (e.key === 'ArrowDown') {
			e.preventDefault();
			const first = dropdown.querySelector('a:focus');
			if (first) {
				first.blur();
				const next = first.nextElementSibling || items[0];
				next.focus();
			} else {
				items[0].focus();
			}
		} else if (e.key === 'ArrowUp') {
			e.preventDefault();
			const focused = dropdown.querySelector('a:focus');
			if (focused) {
				const prev = focused.previousElementSibling || items[items.length - 1];
				focused.blur();
				prev.focus();
			} else {
				items[items.length - 1].focus();
			}
		} else if (e.key === 'Escape') {
			hideDropdown(e.target);
			e.target.blur();
		}
	}

	function handleOutsideClick(e) {
		if (!e.target.closest('.reci-live-search') && !e.target.closest('.reci-search-dropdown')) {
			document.querySelectorAll('.reci-search-dropdown').forEach(hideDropdownById);
		}
	}

	function getDropdown(input) {
		const container = input.closest('.reci-search-container');
		return container ? container.querySelector('.reci-search-dropdown') : null;
	}

	function hideDropdown(input) {
		const dropdown = getDropdown(input);
		if (dropdown) {
			dropdown.classList.add('hidden');
			dropdown.innerHTML = '';
		}
	}

	function hideDropdownById(id) {
		const dropdown = typeof id === 'string' ? document.getElementById(id) : id;
		if (dropdown) {
			dropdown.classList.add('hidden');
			dropdown.innerHTML = '';
		}
	}

	function performSearch(query, input) {
		const dropdown = getDropdown(input);
		if (!dropdown) return;

		dropdown.innerHTML = '<div class="p-4 text-center text-sm text-neutral-600">Searching...</div>';
		dropdown.classList.remove('hidden');

		const url = AJAX_URL + '?action=reci_live_search&q=' + encodeURIComponent(query);

		const xhr = new XMLHttpRequest();
		xhr.open('GET', url, true);

		xhr.onload = function () {
			if (xhr.status === 200) {
				try {
					const response = JSON.parse(xhr.responseText);
					renderResults(response.data.results || [], dropdown, query);
				} catch (e) {
					dropdown.innerHTML = '<div class="p-4 text-center text-sm text-neutral-600">Error: ' + escHtml(xhr.responseText.substring(0, 100)) + '</div>';
				}
			} else {
				dropdown.innerHTML = '<div class="p-4 text-center text-sm text-neutral-600">Error: ' + xhr.status + '</div>';
			}
		};

		xhr.onerror = function () {
			dropdown.innerHTML = '<div class="p-4 text-center text-sm text-neutral-600">Network error</div>';
		};

		xhr.send();
	}

	function renderResults(results, dropdown, query) {
		if (results.length === 0) {
			dropdown.innerHTML = '<div class="p-4 text-center text-sm text-neutral-600">No results found for "' + escHtml(query) + '"</div>';
			return;
		}

		let html = '';
		results.forEach(function (item) {
			html += '<a href="' + escHtml(item.url) + '" class="flex flex-col gap-1 p-4 hover:bg-slate-50 border-b border-zinc-100 last:border-0 transition-colors">';
			html += '<div class="flex items-center gap-2 text-xs text-neutral-600">';
			html += '<span class="px-2 py-0.5 bg-amber-400 text-neutral-800 rounded font-medium">' + escHtml(item.type) + '</span>';
			html += '<span>' + escHtml(item.date) + '</span>';
			html += '</div>';
			html += '<div class="text-sm font-medium text-neutral-800 line-clamp-2">' + escHtml(item.title) + '</div>';
			if (item.excerpt) {
				html += '<div class="text-xs text-neutral-600 line-clamp-1">' + escHtml(item.excerpt) + '</div>';
			}
			html += '</a>';
		});

		html += '<a href="/search/?s=' + encodeURIComponent(query) + '" class="block p-3 text-center text-sm text-[#003594] hover:bg-slate-50 font-medium border-t border-zinc-200">';
		html += 'View all results for "' + escHtml(query) + '"</a>';

		dropdown.innerHTML = html;
	}

	function escHtml(str) {
		const div = document.createElement('div');
		div.textContent = str;
		return div.innerHTML;
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
