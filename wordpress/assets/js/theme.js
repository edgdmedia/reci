/**
 * RECI Media Hub — theme interactions.
 *
 * Menu overlay toggle with keyboard support and body scroll lock.
 */
( function () {
	'use strict';

	var menuToggle  = document.getElementById( 'menu-toggle' );
	var menuClose   = document.getElementById( 'menu-close' );
	var menuOverlay = document.getElementById( 'menu-overlay' );

	function openMenu() {
		if ( ! menuOverlay ) { return; }
		menuOverlay.classList.remove( 'hidden' );
		menuOverlay.setAttribute( 'aria-hidden', 'false' );
		if ( menuToggle ) { menuToggle.setAttribute( 'aria-expanded', 'true' ); }
		document.body.style.overflow = 'hidden';
		if ( menuClose ) { menuClose.focus(); }
	}

	function closeMenu() {
		if ( ! menuOverlay ) { return; }
		menuOverlay.classList.add( 'hidden' );
		menuOverlay.setAttribute( 'aria-hidden', 'true' );
		if ( menuToggle ) {
			menuToggle.setAttribute( 'aria-expanded', 'false' );
			menuToggle.focus();
		}
		document.body.style.overflow = '';
	}

	if ( menuToggle ) {
		menuToggle.addEventListener( 'click', openMenu );
	}

	if ( menuClose ) {
		menuClose.addEventListener( 'click', closeMenu );
	}

	// Escape key closes overlay
	document.addEventListener( 'keydown', function ( e ) {
		if ( e.key === 'Escape' && menuOverlay && ! menuOverlay.classList.contains( 'hidden' ) ) {
			closeMenu();
		}
	} );

	// Click outside content area closes overlay
	if ( menuOverlay ) {
		menuOverlay.addEventListener( 'click', function ( e ) {
			if ( e.target === menuOverlay ) {
				closeMenu();
			}
		} );
	}

} )();

/**
 * Archive filter forms.
 *
 * - Selects submit immediately on change.
 * - Search submits after debounce when value has >= min chars.
 * - Clearing search submits after debounce.
 */
( function () {
	'use strict';

	var forms = document.querySelectorAll( '[data-archive-filter-form]' );
	if ( ! forms.length ) {
		return;
	}

	function submitForm( form ) {
		if ( ! form ) {
			return;
		}

		if ( typeof form.requestSubmit === 'function' ) {
			form.requestSubmit();
			return;
		}

		form.submit();
	}

	forms.forEach( function ( form ) {
		var minChars = parseInt( form.getAttribute( 'data-search-min' ) || '3', 10 );
		var debounceMs = parseInt( form.getAttribute( 'data-search-debounce' ) || '350', 10 );
		var timer = null;
		var selects = form.querySelectorAll( 'select' );
		var searchInput = form.querySelector( 'input[type="search"][name="search"]' );

		if ( Number.isNaN( minChars ) || minChars < 1 ) {
			minChars = 3;
		}
		if ( Number.isNaN( debounceMs ) || debounceMs < 0 ) {
			debounceMs = 350;
		}

		selects.forEach( function ( select ) {
			select.addEventListener( 'change', function () {
				submitForm( form );
			} );
		} );

		if ( ! searchInput ) {
			return;
		}

		searchInput.addEventListener( 'keydown', function ( event ) {
			if ( event.key === 'Enter' ) {
				event.preventDefault();
				if ( timer ) {
					clearTimeout( timer );
				}
				submitForm( form );
			}
		} );

		searchInput.addEventListener( 'input', function () {
			var valueLength = searchInput.value.trim().length;

			if ( timer ) {
				clearTimeout( timer );
			}

			if ( valueLength === 0 || valueLength >= minChars ) {
				timer = window.setTimeout( function () {
					submitForm( form );
				}, debounceMs );
			}
		} );
	} );
} )();

/**
 * Carousel: Today at RECI, Quotes of the day, Community Pulse.
 *
 * - Items use [data-carousel-item]; non-active items have class="hidden".
 * - Dot indicators use [data-carousel-dot]; active dot gets bg-amber-400.
 * - Prev/next buttons use [data-carousel-prev] / [data-carousel-next].
 *   For community carousel these live inside each slide but are still
 *   wired here because listeners fire on the visible slide's buttons.
 */
( function () {
	'use strict';

	var carousels = document.querySelectorAll( '[data-carousel]' );
	if ( ! carousels.length ) {
		return;
	}

	carousels.forEach( function ( carousel ) {
		var items    = Array.prototype.slice.call( carousel.querySelectorAll( '[data-carousel-item]' ) );
		var dots     = Array.prototype.slice.call( carousel.querySelectorAll( '[data-carousel-dot]' ) );
		var prevBtns = Array.prototype.slice.call( carousel.querySelectorAll( '[data-carousel-prev]' ) );
		var nextBtns = Array.prototype.slice.call( carousel.querySelectorAll( '[data-carousel-next]' ) );
		var total    = items.length;
		var current  = items.findIndex( function ( item ) {
			return ! item.classList.contains( 'hidden' );
		} );

		if ( current < 0 ) {
			current = 0;
		}

		if ( ! total ) {
			return;
		}

		var carouselName = carousel.getAttribute( 'data-carousel' ) || '';
		var dotActive    = 'bg-amber-400';
		var dotInactive  = carouselName === 'quotes' ? 'bg-stone-300' : 'bg-zinc-400';

		function setSlideState( idx, isActive ) {
			if ( ! items[ idx ] ) {
				return;
			}

			items[ idx ].classList.toggle( 'hidden', ! isActive );
			items[ idx ].setAttribute( 'aria-hidden', isActive ? 'false' : 'true' );
		}

		function setDotState( idx, isActive ) {
			if ( ! dots[ idx ] ) {
				return;
			}

			dots[ idx ].classList.toggle( dotActive, isActive );
			dots[ idx ].classList.toggle( dotInactive, ! isActive );
			dots[ idx ].setAttribute( 'aria-current', isActive ? 'true' : 'false' );
		}

		function render( index ) {
			var next = ( ( index % total ) + total ) % total;
			current  = next;

			items.forEach( function ( _, idx ) {
				setSlideState( idx, idx === current );
			} );

			dots.forEach( function ( _, idx ) {
				setDotState( idx, idx === current );
			} );
		}

		function bindControl( element, action ) {
			if ( ! element ) {
				return;
			}

			if ( element.tagName !== 'BUTTON' ) {
				element.setAttribute( 'tabindex', '0' );
			}

			element.addEventListener( 'click', action );
			element.addEventListener( 'keydown', function ( event ) {
				if ( event.key === 'Enter' || event.key === ' ' ) {
					event.preventDefault();
					action();
				}
			} );
		}

		dots.forEach( function ( dot, i ) {
			bindControl( dot, function () {
				render( i );
			} );
		} );

		prevBtns.forEach( function ( btn ) {
			bindControl( btn, function () {
				render( current - 1 );
			} );
		} );

		nextBtns.forEach( function ( btn ) {
			bindControl( btn, function () {
				render( current + 1 );
			} );
		} );

		render( current );
	} );
} )();

/**
 * Assessment quiz navigation.
 *
 * Questions use [data-quiz-question]; non-active ones get class="hidden".
 * Prev/next buttons are [data-quiz-prev] / [data-quiz-next] inside each question.
 * Progress bar:  [data-quiz-progress-bar], label: [data-quiz-progress-label],
 * pct display:  [data-quiz-progress-pct]  — all inside [data-quiz].
 */
( function () {
	'use strict';

	var quizzes = document.querySelectorAll( '[data-quiz]' );
	if ( ! quizzes.length ) {
		return;
	}

	quizzes.forEach( function ( quiz ) {
		var questions = Array.prototype.slice.call( quiz.querySelectorAll( '[data-quiz-question]' ) );
		var total     = questions.length;
		var current   = 0;

		if ( total < 2 ) {
			return;
		}

		var progressBar   = quiz.querySelector( '[data-quiz-progress-bar]' );
		var progressLabel = quiz.querySelector( '[data-quiz-progress-label]' );
		var progressPct   = quiz.querySelector( '[data-quiz-progress-pct]' );

		function updateProgress( idx ) {
			var pct = Math.round( ( idx + 1 ) / total * 100 );
			if ( progressBar ) {
				progressBar.style.width = pct + '%';
				progressBar.setAttribute( 'aria-valuenow', idx + 1 );
			}
			if ( progressLabel ) {
				progressLabel.textContent = 'Question ' + ( idx + 1 ) + ' of ' + total;
			}
			if ( progressPct ) {
				progressPct.textContent = pct + '%';
			}
		}

		function showQuestion( idx ) {
			questions[ current ].classList.add( 'hidden' );
			current = idx;
			questions[ current ].classList.remove( 'hidden' );
			updateProgress( current );
		}

		// Wire up every prev/next button found inside any question panel.
		questions.forEach( function ( qEl ) {
			var prevBtn = qEl.querySelector( '[data-quiz-prev]' );
			var nextBtn = qEl.querySelector( '[data-quiz-next]' );

			if ( prevBtn ) {
				prevBtn.addEventListener( 'click', function () {
					if ( current > 0 ) {
						showQuestion( current - 1 );
					}
				} );
			}

			if ( nextBtn ) {
				nextBtn.addEventListener( 'click', function () {
					if ( current < total - 1 ) {
						showQuestion( current + 1 );
					}
				} );
			}
		} );
	} );
} )();

/**
 * Audio player.
 *
 * Container:        [data-audio-player]
 * Toggle button:    [data-audio-toggle]  +  data-audio-target="<audio-id>"
 * Play icon:        [data-audio-play-icon]   – visible when paused
 * Pause icon:       [data-audio-pause-icon]  – visible when playing
 * Progress track:   [data-audio-progress]    – clickable seek bar
 * Progress fill:    [data-audio-progress-bar]
 * Time display:     [data-audio-time]
 */
( function () {
	'use strict';

	function zeroPad( n ) {
		return n < 10 ? '0' + n : '' + n;
	}

	function formatTime( seconds ) {
		var s   = Math.floor( seconds || 0 );
		var h   = Math.floor( s / 3600 );
		var m   = Math.floor( ( s % 3600 ) / 60 );
		var sec = s % 60;
		return h > 0
			? h + ':' + zeroPad( m ) + ':' + zeroPad( sec )
			: zeroPad( m ) + ':' + zeroPad( sec );
	}

	var players = document.querySelectorAll( '[data-audio-player]' );
	if ( ! players.length ) {
		return;
	}

	players.forEach( function ( player ) {
		var toggleBtn = player.querySelector( '[data-audio-toggle]' );
		if ( ! toggleBtn ) {
			return;
		}

		var targetId = toggleBtn.getAttribute( 'data-audio-target' );
		var audio    = targetId ? document.getElementById( targetId ) : null;
		if ( ! audio ) {
			return;
		}

		var playIcon      = player.querySelector( '[data-audio-play-icon]' );
		var pauseIcon     = player.querySelector( '[data-audio-pause-icon]' );
		var progressTrack = player.querySelector( '[data-audio-progress]' );
		var progressBar   = player.querySelector( '[data-audio-progress-bar]' );
		var timeDisplay   = player.querySelector( '[data-audio-time]' );

		function setPlaying( isPlaying ) {
			if ( playIcon )  { playIcon.classList.toggle( 'hidden', isPlaying ); }
			if ( pauseIcon ) { pauseIcon.classList.toggle( 'hidden', ! isPlaying ); }
			toggleBtn.setAttribute(
				'aria-label',
				isPlaying ? 'Pause episode' : 'Play episode'
			);
		}

		toggleBtn.addEventListener( 'click', function () {
			if ( audio.paused ) {
				var p = audio.play();
				if ( p !== undefined ) {
					p.catch( function () {} );
				}
			} else {
				audio.pause();
			}
		} );

		audio.addEventListener( 'play',  function () { setPlaying( true ); } );
		audio.addEventListener( 'pause', function () { setPlaying( false ); } );
		audio.addEventListener( 'ended', function () {
			setPlaying( false );
			if ( progressBar )   { progressBar.style.width = '0%'; }
			if ( progressTrack ) { progressTrack.setAttribute( 'aria-valuenow', '0' ); }
		} );

		audio.addEventListener( 'timeupdate', function () {
			if ( ! audio.duration ) {
				return;
			}
			var pct = audio.currentTime / audio.duration * 100;
			if ( progressBar )   { progressBar.style.width = pct + '%'; }
			if ( progressTrack ) { progressTrack.setAttribute( 'aria-valuenow', Math.round( pct ) ); }
			if ( timeDisplay )   {
				timeDisplay.textContent =
					formatTime( audio.currentTime ) + ' / ' + formatTime( audio.duration );
			}
		} );

		if ( progressTrack ) {
			progressTrack.addEventListener( 'click', function ( e ) {
				if ( ! audio.duration ) {
					return;
				}
				var rect  = progressTrack.getBoundingClientRect();
				var ratio = ( e.clientX - rect.left ) / rect.width;
				audio.currentTime = Math.max( 0, Math.min( 1, ratio ) ) * audio.duration;
			} );
		}
	} );
} )();

/**
 * Mobile search drawer.
 *
 * #search-toggle  — icon button (visible < md)
 * #search-drawer  — full-width drawer that drops below the navbar
 * #search-close   — ✕ button inside the drawer
 * #search-drawer-input — the actual <input> (focused on open)
 */
( function () {
	'use strict';

	var toggle = document.getElementById( 'search-toggle' );
	var drawer = document.getElementById( 'search-drawer' );
	var closeBtn = document.getElementById( 'search-close' );
	var drawerInput = document.getElementById( 'search-drawer-input' );

	if ( ! toggle || ! drawer ) {
		return;
	}

	function openSearch() {
		drawer.classList.remove( 'hidden' );
		toggle.setAttribute( 'aria-expanded', 'true' );
		if ( drawerInput ) {
			drawerInput.focus();
		}
	}

	function closeSearch() {
		drawer.classList.add( 'hidden' );
		toggle.setAttribute( 'aria-expanded', 'false' );
		toggle.focus();
	}

	toggle.addEventListener( 'click', openSearch );

	if ( closeBtn ) {
		closeBtn.addEventListener( 'click', closeSearch );
	}

	document.addEventListener( 'keydown', function ( e ) {
		if ( e.key === 'Escape' && ! drawer.classList.contains( 'hidden' ) ) {
			closeSearch();
		}
	} );
} )();
