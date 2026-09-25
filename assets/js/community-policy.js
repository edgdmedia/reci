/**
 * Typing-time community guideline warning.
 *
 * Mirrors the PHP normaliser in inc/features/community-policy.php. If the two
 * drift, the warning a writer sees stops predicting what the server flags, so
 * any change to one belongs in both.
 *
 * This only ever warns. It never disables a control and never blocks a save.
 */
( function () {
	'use strict';

	var data = window.reciCommunityPolicy || {};
	var terms = data.terms || [];
	var accentMap = data.accentMap || {};
	var policyHtml = data.policyHtml || '';

	// Deliberately no early return on an empty term list. The guideline link
	// and the term matcher are independent: a site may publish a policy without
	// listing a single term, and it should still be reachable from every box
	// people write in.
	if ( ! terms.length && ! policyHtml ) {
		return;
	}

	function normalize( text ) {
		var out = String( text ).toLowerCase();

		out = out.replace( /./g, function ( ch ) {
			return Object.prototype.hasOwnProperty.call( accentMap, ch ) ? accentMap[ ch ] : ch;
		} );

		// Digits only, matching the PHP map. Mapping punctuation to letters
		// would corrupt ordinary punctuation.
		out = out.replace( /[431057]/g, function ( ch ) {
			var digits = { '4': 'a', '3': 'e', '1': 'i', '0': 'o', '5': 's', '7': 't' };
			return Object.prototype.hasOwnProperty.call( digits, ch ) ? digits[ ch ] : ch;
		} );

		out = out.replace( /[^a-z0-9\s]+/g, '' );
		out = out.replace( /\s+/g, ' ' );

		return out.trim();
	}

	function matches( text ) {
		var haystack = normalize( text );

		if ( ! haystack ) {
			return [];
		}

		return terms.filter( function ( term ) {
			var needle = normalize( term );

			if ( ! needle ) {
				return false;
			}

			var pattern = new RegExp(
				'(?:^|[^a-z0-9])' + needle.replace( /[.*+?^${}()|[\]\\]/g, '\\$&' ) + '(?:[^a-z0-9]|$)'
			);

			return pattern.test( haystack );
		} );
	}

	function buildNotice() {
		var notice = document.createElement( 'div' );
		notice.className = 'reci-policy-notice mt-2 rounded-lg bg-amber-50 border border-amber-200 px-3 py-2 text-xs text-amber-900';
		notice.hidden = true;
		notice.setAttribute( 'role', 'status' );
		notice.textContent = data.warning || '';
		return notice;
	}

	function openPolicyDialog() {
		var existing = document.getElementById( 'reci-policy-dialog' );

		if ( existing ) {
			existing.classList.remove( 'hidden' );
			existing.classList.add( 'flex' );
			return;
		}

		// Mirrors the reflection sign-in modal in inc/admin/dashboard.php, so the
		// two read as the same product rather than two different dialogs.
		var overlay = document.createElement( 'div' );
		overlay.id = 'reci-policy-dialog';
		overlay.className = 'fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4';
		overlay.setAttribute( 'role', 'dialog' );
		overlay.setAttribute( 'aria-modal', 'true' );

		var panel = document.createElement( 'div' );
		panel.className = 'bg-white rounded-2xl p-8 max-w-lg w-full mx-4 shadow-2xl max-h-[80vh] overflow-y-auto';

		var header = document.createElement( 'div' );
		header.className = 'flex items-center justify-between mb-6';

		var heading = document.createElement( 'h2' );
		heading.className = 'text-xl font-bold font-heading text-zinc-800';
		heading.textContent = data.linkLabel || 'Community guideline';

		var close = document.createElement( 'button' );
		close.type = 'button';
		close.className = 'text-zinc-400 hover:text-zinc-600 text-2xl leading-none';
		close.innerHTML = '&times;';
		close.setAttribute( 'aria-label', 'Close' );

		var body = document.createElement( 'div' );
		body.className = 'prose prose-zinc max-w-none text-sm leading-7 text-zinc-600';
		// policyHtml comes from wp_kses_post() on save, so it carries only the
		// markup an editor may publish anywhere else on the site.
		body.innerHTML = policyHtml;

		function hide() {
			overlay.classList.add( 'hidden' );
			overlay.classList.remove( 'flex' );
		}

		close.addEventListener( 'click', hide );
		overlay.addEventListener( 'click', function ( event ) {
			if ( event.target === overlay ) {
				hide();
			}
		} );
		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key ) {
				hide();
			}
		} );

		header.appendChild( heading );
		header.appendChild( close );
		panel.appendChild( header );
		panel.appendChild( body );
		overlay.appendChild( panel );
		document.body.appendChild( overlay );

		overlay.classList.remove( 'hidden' );
		overlay.classList.add( 'flex' );
	}

	function buildLink( textarea ) {
		// A link, not a button: it sits inline under the box people are writing
		// in, and should read as part of the sentence around it.
		var link = document.createElement( 'a' );
		link.href = '#';

		// Full width inside a reflection prompt, where it sits in a stacked
		// column under the textarea. The comment form lays out differently, so
		// it keeps the inline width it already had.
		var inPrompt = textarea && textarea.closest && textarea.closest( '[data-reci-prompt]' );
		var width = inPrompt ? 'w-full ' : '';

		link.className = 'reci-policy-link ' + width + 'inline-block mt-2 text-xs font-medium text-amber-600 underline underline-offset-2 hover:text-amber-700';
		link.textContent = data.linkLabel || 'Community guideline';
		link.addEventListener( 'click', function ( event ) {
			event.preventDefault();
			openPolicyDialog();
		} );
		return link;
	}

	function attach( textarea ) {
		if ( textarea.dataset.reciPolicyBound ) {
			return;
		}
		textarea.dataset.reciPolicyBound = '1';

		var notice = buildNotice();
		textarea.insertAdjacentElement( 'afterend', notice );

		if ( policyHtml ) {
			textarea.insertAdjacentElement( 'afterend', buildLink( textarea ) );
		}

		if ( ! terms.length ) {
			return;
		}

		var timer = null;

		textarea.addEventListener( 'input', function () {
			window.clearTimeout( timer );

			timer = window.setTimeout( function () {
				notice.hidden = matches( textarea.value ).length === 0;
			}, 250 );
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		[ '[data-reci-response]', '#comment' ].forEach( function ( selector ) {
			document.querySelectorAll( selector ).forEach( attach );
		} );
	} );
}() );
