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
	var shareBox = document.getElementById( 'reflectionShare' );
	var anonWrap = document.getElementById( 'reflectionAnonWrap' );

	if ( shareBox && anonWrap ) {
		shareBox.addEventListener( 'change', function () {
			anonWrap.hidden = ! shareBox.checked;

			if ( ! shareBox.checked ) {
				var anonBox = document.getElementById( 'reflectionAnonymous' );
				if ( anonBox ) {
					anonBox.checked = false;
				}
			}
		} );
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

	// Let the reflection runtime call the same helper after a save.
	window.reciPatchJournalShare = patchShare;
}() );
