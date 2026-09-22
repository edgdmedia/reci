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

	if ( ! terms.length ) {
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

	function buildLink() {
		var link = document.createElement( 'a' );
		link.className = 'reci-policy-link';
		link.href = data.policyUrl || '#';
		link.textContent = data.linkLabel || 'Community guideline';
		return link;
	}

	function attach( textarea ) {
		if ( textarea.dataset.reciPolicyBound ) {
			return;
		}
		textarea.dataset.reciPolicyBound = '1';

		var notice = buildNotice();
		var link = buildLink();

		textarea.insertAdjacentElement( 'afterend', notice );
		textarea.insertAdjacentElement( 'afterend', link );

		var timer = null;

		textarea.addEventListener( 'input', function () {
			window.clearTimeout( timer );

			timer = window.setTimeout( function () {
				notice.hidden = matches( textarea.value ).length === 0;
			}, 250 );
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		[ '#reflectionResponse', '#comment' ].forEach( function ( selector ) {
			document.querySelectorAll( selector ).forEach( attach );
		} );
	} );
}() );
