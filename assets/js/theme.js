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

			var backgroundWrapper = carousel.closest( '[data-carousel-background-wrapper]' );
			var activeItem = items[ current ];
			if ( backgroundWrapper && activeItem ) {
				var backgroundImage = activeItem.getAttribute( 'data-carousel-background-image' ) || '';
				if ( backgroundImage ) {
					backgroundWrapper.style.backgroundImage = 'url("' + backgroundImage.replace( /"/g, '\\"' ) + '")';
				}
			}
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
 * Reflection Gallery background slideshow.
 */
( function () {
	'use strict';

	var slideshows = document.querySelectorAll( '[data-reflection-slideshow]' );
	if ( ! slideshows.length ) {
		return;
	}

	slideshows.forEach( function ( slideshow ) {
		var slides = Array.prototype.slice.call( slideshow.querySelectorAll( '[data-reflection-slide]' ) );
		var total  = slides.length;
		var index  = 0;
		var intervalMs = parseInt( slideshow.getAttribute( 'data-reflection-interval' ) || '6000', 10 );

		if ( total < 2 ) {
			return;
		}

		function render( activeIndex ) {
			slides.forEach( function ( slide, slideIndex ) {
				var isActive = slideIndex === activeIndex;
				slide.classList.toggle( 'opacity-100', isActive );
				slide.classList.toggle( 'opacity-0', ! isActive );
				slide.setAttribute( 'aria-hidden', isActive ? 'false' : 'true' );
			} );
		}

		render( index );

		window.setInterval( function () {
			index = ( index + 1 ) % total;
			render( index );
		}, intervalMs > 0 ? intervalMs : 6000 );
	} );
} )();

/**
 * Localize event times for the visitor's browser timezone.
 */
( function () {
	'use strict';

	var localTimes = document.querySelectorAll( '[data-local-time][datetime]' );
	if ( ! localTimes.length || typeof Intl === 'undefined' || typeof Intl.DateTimeFormat !== 'function' ) {
		return;
	}

	var formatter = new Intl.DateTimeFormat( [], {
		hour: 'numeric',
		minute: '2-digit',
		hour12: true,
	} );

	localTimes.forEach( function ( element ) {
		var datetime = element.getAttribute( 'datetime' );
		if ( ! datetime ) {
			return;
		}

		var parsedDate = new Date( datetime );
		if ( Number.isNaN( parsedDate.getTime() ) ) {
			return;
		}

		element.textContent = formatter.format( parsedDate ).replace( /\s+/g, '' );
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
		var current   = questions.findIndex( function ( question ) {
			return ! question.classList.contains( 'hidden' );
		} );

		if ( total < 2 ) {
			return;
		}

		if ( current < 0 ) {
			current = 0;
		}

		var progressBar   = quiz.querySelector( '[data-quiz-progress-bar]' );
		var progressLabel = quiz.querySelector( '[data-quiz-progress-label]' );
		var progressPct   = quiz.querySelector( '[data-quiz-progress-pct]' );
		var form          = quiz.querySelector( 'form[data-quiz-form]' );
		var formError     = form ? form.querySelector( '[data-quiz-form-error]' ) : null;
		var successPanel  = quiz.querySelector( '[data-quiz-success]' );
		var successMsg    = successPanel ? successPanel.querySelector( '[data-quiz-success-message]' ) : null;
		var progressWrap  = quiz.querySelector( '[data-quiz-progress-wrapper]' );
		var isSubmitting  = false;

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

		function questionAnswered( question ) {
			if ( ! question || question.getAttribute( 'data-required' ) !== 'true' ) {
				return true;
			}

			var checked = question.querySelectorAll( 'input[type="radio"]:checked, input[type="checkbox"]:checked' );
			if ( checked.length ) {
				return true;
			}

			var inputs = question.querySelectorAll( 'input[type="text"], textarea' );
			for ( var i = 0; i < inputs.length; i++ ) {
				if ( inputs[ i ].value.trim() !== '' ) {
					return true;
				}
			}

			return false;
		}

		function setQuestionError( question, show ) {
			var error = question ? question.querySelector( '[data-quiz-error]' ) : null;
			if ( ! error ) {
				return;
			}

			error.classList.toggle( 'hidden', ! show );
		}

		function clearAllQuestionErrors() {
			questions.forEach( function ( question ) {
				setQuestionError( question, false );
			} );
		}

		function setFormError( message ) {
			if ( ! formError ) {
				return;
			}
			var text = String( message || '' ).trim();
			if ( text === '' ) {
				formError.textContent = '';
				formError.classList.add( 'hidden' );
				return;
			}
			formError.textContent = text;
			formError.classList.remove( 'hidden' );
		}

		function collectAnswers( formEl ) {
			var payload = {};
			if ( ! formEl ) {
				return payload;
			}

			var formData = new window.FormData( formEl );
			formData.forEach( function ( value, key ) {
				var match = key.match( /^reci_assessment_answers\[([^\]]+)\](\[\])?$/ );
				if ( ! match ) {
					return;
				}

				var questionId = match[1];
				var isMulti = !! match[2];
				if ( isMulti ) {
					if ( ! Array.isArray( payload[questionId] ) ) {
						payload[questionId] = [];
					}
					payload[questionId].push( String( value ) );
				} else {
					payload[questionId] = String( value );
				}
			} );

			return payload;
		}

		// Wire up every prev/next button found inside any question panel.
		questions.forEach( function ( qEl ) {
			var prevBtn = qEl.querySelector( '[data-quiz-prev]' );
			var nextBtn = qEl.querySelector( '[data-quiz-next]' );
			var inputs  = qEl.querySelectorAll( 'input, textarea' );

			if ( prevBtn ) {
				prevBtn.addEventListener( 'click', function () {
					if ( current > 0 ) {
						setQuestionError( qEl, false );
						showQuestion( current - 1 );
					}
				} );
			}

			if ( nextBtn ) {
				nextBtn.addEventListener( 'click', function () {
					if ( ! questionAnswered( qEl ) ) {
						setQuestionError( qEl, true );
						return;
					}
					setQuestionError( qEl, false );
					if ( current < total - 1 ) {
						showQuestion( current + 1 );
					}
				} );
			}

			inputs.forEach( function ( input ) {
				input.addEventListener( 'change', function () {
					if ( questionAnswered( qEl ) ) {
						setQuestionError( qEl, false );
					}
				} );
				input.addEventListener( 'input', function () {
					if ( questionAnswered( qEl ) ) {
						setQuestionError( qEl, false );
					}
				} );
			} );
		} );

		if ( form ) {
			form.addEventListener( 'submit', function ( event ) {
				var config = window.RECIAssessmentConfig || {};
				var endpoint = typeof config.restEndpoint === 'string' ? config.restEndpoint : '';
				if ( endpoint === '' ) {
					return;
				}

				event.preventDefault();

				if ( isSubmitting ) {
					return;
				}
				isSubmitting = true;
				clearAllQuestionErrors();
				setFormError( '' );

				var submitButton = form.querySelector( 'button[type="submit"]' );
				if ( submitButton ) {
					submitButton.disabled = true;
				}

				var assessmentIdInput = form.querySelector( 'input[name="reci_assessment_id"]' );
				var nonceInput = form.querySelector( 'input[name="reci_assessment_nonce"]' );
				var assessmentId = assessmentIdInput ? parseInt( assessmentIdInput.value, 10 ) : 0;
				var nonce = nonceInput ? String( nonceInput.value || '' ) : '';
				var answers = collectAnswers( form );

				window.fetch( endpoint, {
					method: 'POST',
					credentials: 'same-origin',
					headers: {
						'Content-Type': 'application/json',
						'X-WP-Nonce': typeof config.restNonce === 'string' ? config.restNonce : '',
					},
					body: JSON.stringify( {
						assessment_id: assessmentId,
						nonce: nonce,
						answers: answers,
					} ),
				} )
					.then( function ( response ) {
						return response.json().catch( function () {
							return {};
						} ).then( function ( body ) {
							return {
								ok: response.ok,
								body: body || {},
							};
						} );
					} )
					.then( function ( result ) {
						var body = result.body || {};
						var errors = body.errors && typeof body.errors === 'object' ? body.errors : {};

						if ( ! result.ok || body.submitted !== true ) {
							var errorKeys = Object.keys( errors );
							var firstQuestionIndex = -1;
							errorKeys.forEach( function ( key ) {
								if ( key === 'form' ) {
									return;
								}
								var questionEl = quiz.querySelector( '[data-quiz-question][data-question-id="' + key.replace( /"/g, '\\"' ) + '"]' );
								if ( ! questionEl ) {
									return;
								}
								var errorEl = questionEl.querySelector( '[data-quiz-error]' );
								if ( errorEl ) {
									errorEl.textContent = String( errors[key] || '' );
									errorEl.classList.remove( 'hidden' );
								}
								var idx = questions.indexOf( questionEl );
								if ( idx >= 0 && ( firstQuestionIndex < 0 || idx < firstQuestionIndex ) ) {
									firstQuestionIndex = idx;
								}
							} );

							if ( firstQuestionIndex >= 0 && firstQuestionIndex !== current ) {
								showQuestion( firstQuestionIndex );
							}

							setFormError( errors.form || 'We could not save this submission. Please try again.' );
							return;
						}

						if ( progressWrap ) {
							progressWrap.classList.add( 'hidden' );
						}
						form.classList.add( 'hidden' );

						if ( successMsg && typeof body.completion_message === 'string' && body.completion_message.trim() !== '' ) {
							successMsg.textContent = body.completion_message;
						}
						if ( successPanel ) {
							successPanel.classList.remove( 'hidden' );
							successPanel.classList.add( 'flex' );
						}

						var quizTitle = quiz.querySelector( '.quiz-title-div' );
						if ( quizTitle && typeof body.completion_title === 'string' && body.completion_title.trim() !== '' ) {
							quizTitle.textContent = body.completion_title;
						}

						var instrEl = quiz.querySelector( '[data-quiz-instructions]' );
						if ( instrEl ) {
							instrEl.classList.add( 'hidden' );
						}

						quiz.setAttribute( 'data-quiz-submitted', '' );

						if ( body.result && typeof body.result === 'object' ) {
							var scoreEl = successPanel.querySelector( '[data-quiz-score]' );
							if ( scoreEl ) {
								if ( typeof body.result.score === 'number' && typeof body.result.max_score === 'number' && typeof body.result.percentage === 'number' ) {
									scoreEl.textContent = 'You scored ' + body.result.score + '/' + body.result.max_score + ' (' + body.result.percentage + '%)';
									scoreEl.classList.remove( 'hidden' );
								} else {
									scoreEl.classList.add( 'hidden' );
								}
							}

							var bandEl = successPanel.querySelector( '[data-quiz-band]' );
							if ( bandEl ) {
								if ( body.result.band && typeof body.result.band === 'object' ) {
									var labelEl = bandEl.querySelector( '[data-quiz-band-label]' );
									var msgEl = bandEl.querySelector( '[data-quiz-band-message]' );
									if ( labelEl && typeof body.result.band.label === 'string' && body.result.band.label.trim() !== '' ) {
										labelEl.textContent = body.result.band.label;
									}
									if ( msgEl && typeof body.result.band.message === 'string' && body.result.band.message.trim() !== '' ) {
										msgEl.textContent = body.result.band.message;
									}
									bandEl.classList.remove( 'hidden' );
								} else {
									bandEl.classList.add( 'hidden' );
								}
							}

							var recsSection = successPanel.querySelector( '[data-quiz-recommendations]' );
							if ( recsSection ) {
								var recsGrid = recsSection.querySelector( '[data-quiz-recommendations-grid]' );
								if ( body.result.recommendations && Array.isArray( body.result.recommendations ) && body.result.recommendations.length > 0 ) {
									if ( recsGrid ) {
										recsGrid.innerHTML = '';
										body.result.recommendations.forEach( function ( rec ) {
											if ( typeof rec.title !== 'string' || rec.title.trim() === '' || typeof rec.permalink !== 'string' || rec.permalink.trim() === '' ) {
												return;
											}
											var link = document.createElement( 'a' );
											link.href = rec.permalink;
											link.className = 'no-underline rounded-lg border border-zinc-200 overflow-hidden bg-white hover:shadow-sm transition-shadow';
											if ( typeof rec.image_url === 'string' && rec.image_url.trim() !== '' ) {
												var img = document.createElement( 'img' );
												img.src = rec.image_url;
												img.alt = rec.title;
												img.className = 'w-full h-32 object-cover';
												link.appendChild( img );
											}
											var div = document.createElement( 'div' );
											div.className = 'p-4 flex flex-col gap-2';
											if ( typeof rec.date === 'string' && rec.date.trim() !== '' ) {
												var dateP = document.createElement( 'p' );
												dateP.className = 'text-neutral-500 text-xs font-medium uppercase tracking-wide';
												dateP.textContent = rec.date;
												div.appendChild( dateP );
											}
											var titleP = document.createElement( 'p' );
											titleP.className = 'text-neutral-800 text-base font-bold leading-6';
											titleP.textContent = rec.title;
											div.appendChild( titleP );
											if ( typeof rec.excerpt === 'string' && rec.excerpt.trim() !== '' ) {
												var excerptP = document.createElement( 'p' );
												excerptP.className = 'text-neutral-600 text-sm leading-6';
												excerptP.textContent = rec.excerpt;
												div.appendChild( excerptP );
											}
											link.appendChild( div );
											recsGrid.appendChild( link );
										} );
									}
									recsSection.classList.remove( 'hidden' );
								} else {
									recsSection.classList.add( 'hidden' );
								}
							}
						}
					} )
					.catch( function () {
						setFormError( 'Unable to submit right now. Please try again.' );
					} )
					.finally( function () {
						isSubmitting = false;
						if ( submitButton ) {
							submitButton.disabled = false;
						}
					} );
			} );
		}
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
