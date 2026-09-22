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
		notice.className = 'reci-policy-notice';
		notice.hidden = true;
		notice.setAttribute( 'role', 'status' );
		notice.textContent = data.warning || '';
		return notice;
	}

	function openPolicyDialog() {
		var existing = document.getElementById( 'reci-policy-dialog' );

		if ( existing ) {
			existing.hidden = false;
			return;
		}

		var overlay = document.createElement( 'div' );
		overlay.id = 'reci-policy-dialog';
		overlay.setAttribute( 'role', 'dialog' );
		overlay.setAttribute( 'aria-modal', 'true' );
		overlay.style.cssText = 'position:fixed;inset:0;z-index:9999;overflow-y:auto;background:rgba(9,8,7,0.94);padding:48px 16px;';

		var panel = document.createElement( 'div' );
		panel.style.cssText = 'max-width:720px;margin:0 auto;background:#fff;color:#18181b;border-radius:18px;padding:28px;';

		var heading = document.createElement( 'h2' );
		heading.textContent = data.linkLabel || 'Community guideline';
		heading.style.cssText = 'margin:0 0 16px;font-size:1.5rem;';

		var body = document.createElement( 'div' );
		// policyHtml comes from wp_kses_post() on save, so it carries only the
		// markup an editor is allowed to publish anywhere else on the site.
		body.innerHTML = policyHtml;

		var close = document.createElement( 'button' );
		close.type = 'button';
		close.textContent = 'Close';
		close.style.cssText = 'margin-top:24px;border:1px solid currentColor;border-radius:999px;padding:8px 20px;background:none;cursor:pointer;';
		close.addEventListener( 'click', function () {
			overlay.hidden = true;
		} );

		overlay.addEventListener( 'click', function ( event ) {
			if ( event.target === overlay ) {
				overlay.hidden = true;
			}
		} );

		panel.appendChild( heading );
		panel.appendChild( body );
		panel.appendChild( close );
		overlay.appendChild( panel );
		document.body.appendChild( overlay );
	}

	function buildLink() {
		var link = document.createElement( 'button' );
		link.type = 'button';
		link.className = 'reci-policy-link';
		link.textContent = data.linkLabel || 'Community guideline';
		link.style.cssText = 'display:inline-block;margin-top:8px;background:none;border:none;padding:0;text-decoration:underline;cursor:pointer;color:inherit;font:inherit;';
		link.addEventListener( 'click', openPolicyDialog );
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
			textarea.insertAdjacentElement( 'afterend', buildLink() );
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
		[ '#reflectionResponse', '#comment', '.reflect-input' ].forEach( function ( selector ) {
			document.querySelectorAll( selector ).forEach( attach );
		} );
	} );
}() );
