/**
 * Share controls for journal entries.
 *
 * Reveals the anonymity option only when sharing is chosen, and drives the
 * PATCH /reci/v1/journals/{id}/share route from both the reflection page and
 * the dashboard list.
 */
( function () {
	'use strict';

	var settings = window.reciJournalSharing || {};

	function patchShare( journalId, shared, anonymous ) {
		return window.fetch( settings.root + 'reci/v1/journals/' + journalId + '/share', {
			method: 'PATCH',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': settings.nonce || ''
			},
			body: JSON.stringify( { shared: shared, anonymous: anonymous } )
		} ).then( function ( response ) {
			if ( ! response.ok ) {
				throw new Error( 'share request failed: ' + response.status );
			}
			return response.json();
		} );
	}

	// Reflection page: reveal the anonymity option only when sharing.
	//
	// Delegated, because a reflection may contain several prompt chapters and
	// each renders its own copy of the controls. They are matched by data
	// attribute and scoped to their own wrapper, so no ids can collide.
	document.addEventListener( 'change', function ( event ) {
		var target = event.target;

		if ( ! target || ! target.matches || ! target.matches( '[data-reci-share]' ) ) {
			return;
		}

		var scope = target.closest( '[data-reci-share-controls]' );

		if ( ! scope ) {
			return;
		}

		var anonWrap = scope.querySelector( '[data-reci-anon-wrap]' );
		var anonBox = scope.querySelector( '[data-reci-anonymous]' );

		if ( anonWrap ) {
			anonWrap.hidden = ! target.checked;
		}

		if ( ! target.checked && anonBox ) {
			anonBox.checked = false;
		}
	} );

	/**
	 * Read the share intent nearest a given element.
	 *
	 * Both reflection save paths - the panel runtime and the immersive stage
	 * runtime - call this after a successful save, so the two families share one
	 * definition of what the controls mean.
	 */
	function readShareIntent( fromElement ) {
		var root = fromElement && fromElement.closest ? fromElement.closest( '.reci-stage' ) : null;
		var scope = root
			? root.querySelector( '[data-reci-share-controls]' )
			: document.querySelector( '[data-reci-share-controls]' );

		if ( ! scope ) {
			return { share: false, anonymous: false };
		}

		var shareBox = scope.querySelector( '[data-reci-share]' );
		var anonBox = scope.querySelector( '[data-reci-anonymous]' );

		return {
			share: !! ( shareBox && shareBox.checked ),
			anonymous: !! ( anonBox && anonBox.checked ),
			reset: function () {
				if ( shareBox ) { shareBox.checked = false; }
				if ( anonBox ) { anonBox.checked = false; }
				var wrap = scope.querySelector( '[data-reci-anon-wrap]' );
				if ( wrap ) { wrap.hidden = true; }
			}
		};
	}

	// Dashboard list: one control per row.
	document.querySelectorAll( '[data-reci-journal-share]' ).forEach( function ( control ) {
		control.addEventListener( 'change', function () {
			var row = control.closest( '[data-journal-id]' );
			var journalId = row ? row.getAttribute( 'data-journal-id' ) : null;
			var anonBox = row ? row.querySelector( '[data-reci-journal-anonymous]' ) : null;
			var status = row ? row.querySelector( '[data-reci-journal-status]' ) : null;

			if ( ! journalId ) {
				return;
			}

			control.disabled = true;

			patchShare( journalId, control.checked, anonBox ? anonBox.checked : false )
				.then( function ( result ) {
					if ( status ) {
						status.textContent = result.status === 'pending'
							? ( settings.pendingLabel || 'Pending review' )
							: ( settings.privateLabel || 'Private' );
					}
					if ( anonBox ) {
						anonBox.disabled = ! control.checked;
					}
				} )
				.catch( function () {
					// Put the control back where it was, so the UI never claims
					// a share that did not happen.
					control.checked = ! control.checked;
					if ( status ) {
						status.textContent = settings.errorLabel || 'Could not update. Try again.';
					}
				} )
				.finally( function () {
					control.disabled = false;
				} );
		} );
	} );

	function renderSharedEntry( entry ) {
		var article = document.createElement( 'article' );
		var author = document.createElement( 'strong' );
		var prompt = document.createElement( 'p' );
		var response = document.createElement( 'p' );

		article.className = 'rounded-[18px] border border-[color:var(--reflection-border)] bg-[var(--reflection-card)] p-4';
		author.textContent = entry.author_name;
		prompt.className = 'mt-1 text-xs uppercase tracking-[0.08em] reci-reflection-muted';
		prompt.textContent = entry.prompt;
		response.className = 'mt-3 text-base leading-8 reci-reflection-soft-text';
		response.textContent = entry.response;

		article.appendChild( author );
		article.appendChild( prompt );
		article.appendChild( response );

		return article;
	}

	function loadSharedJournals( overlay ) {
		var list = document.getElementById( 'reci-shared-journals-list' );
		var status = document.getElementById( 'reci-shared-journals-status' );

		if ( ! list || overlay.dataset.reciLoaded === '1' ) {
			return;
		}

		overlay.dataset.reciLoaded = '1';

		if ( ! settings.root ) {
			status.textContent = 'Shared reflections are unavailable.';
			return;
		}

		status.textContent = 'Loading shared reflections...';

		window.fetch( settings.root + 'reci/v1/reflections/' + overlay.dataset.reflectionId + '/shared-journals', {
			credentials: 'same-origin',
			headers: { 'X-WP-Nonce': settings.nonce || '' }
		} )
			.then( function ( response ) {
				if ( ! response.ok ) {
					throw new Error( 'request failed' );
				}
				return response.json();
			} )
			.then( function ( data ) {
				list.replaceChildren.apply( list, ( data.items || [] ).map( renderSharedEntry ) );
				status.textContent = ( data.items || [] ).length
					? 'Showing ' + data.items.length + ' shared reflection' + ( data.items.length === 1 ? '' : 's' ) + '.'
					: 'No shared reflections are available yet.';
			} )
			.catch( function () {
				overlay.dataset.reciLoaded = '0';
				status.textContent = 'Could not load shared reflections. Try again.';
			} );
	}

	function initSharedJournalOverlay() {
		var overlay = document.getElementById( 'reci-shared-journals' );
		var openButtons = document.querySelectorAll( '[data-reci-open-shared]' );
		var closeButton = document.getElementById( 'reci-shared-journals-close' );

		if ( ! overlay || ! openButtons.length || ! closeButton ) {
			return;
		}

		// Every prompt chapter may show its own button; they all open the one
		// pooled overlay.
		openButtons.forEach( function ( openButton ) {
			openButton.addEventListener( 'click', function () {
				overlay.classList.remove( 'hidden' );
				loadSharedJournals( overlay );
			} );
		} );

		closeButton.addEventListener( 'click', function () {
			overlay.classList.add( 'hidden' );
		} );

		overlay.addEventListener( 'click', function ( event ) {
			if ( event.target === overlay ) {
				overlay.classList.add( 'hidden' );
			}
		} );
	}

	initSharedJournalOverlay();

	// Let both reflection runtimes call the same helpers after a save.
	window.reciPatchJournalShare = patchShare;
	window.reciReadShareIntent = readShareIntent;
}() );
