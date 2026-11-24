
window.anwpPostGridElementor = {};
( function( window, $, app, l10n ) {

	// Constructor.
	app.init = function() {
		app.cache();
		app.bindEvents();
	};

	// Cache document elements.
	app.cache = function() {
		app.$c = {
			body: $( document.body )
		};
	};

	// Combine all events.
	app.bindEvents = function() {
		$( window ).on( 'elementor/frontend/init', function() {

			elementorFrontend.hooks.addAction( 'frontend/element_ready/anwp-pg-simple-slider.default', function( $scope ) {
				app.initSwiper( $scope );
			} );

			elementorFrontend.hooks.addAction( 'frontend/element_ready/anwp-pg-flex-slider.default', function( $scope ) {
				app.initFlexSwiper( $scope );
			} );

			elementorFrontend.hooks.addAction( 'frontend/element_ready/anwp-pg-classic-slider.default', function( $scope ) {
				app.initSwiper( $scope );
			} );

			elementorFrontend.hooks.addAction( 'frontend/element_ready/anwp-pg-classic-blog.default', function( $scope ) {
				app.initPagination( $scope );
				app.initEntrance( $scope );
			} );

			elementorFrontend.hooks.addAction( 'frontend/element_ready/anwp-pg-classic-grid.default', function( $scope ) {
				app.initPagination( $scope );
				app.initEntrance( $scope );
			} );

			elementorFrontend.hooks.addAction( 'frontend/element_ready/anwp-pg-light-grid.default', function( $scope ) {
				app.initPagination( $scope );
				app.initEntrance( $scope );
			} );

			elementorFrontend.hooks.addAction( 'frontend/element_ready/anwp-pg-simple-grid.default', function( $scope ) {
				app.initPagination( $scope );
				app.initEntrance( $scope );
			} );

			app.initLoadMore();
			app.initPromotionTooltip();

			app.$c.body.addClass( 'anwp-pg-ready' );

		} );
	};

	app.initPromotionTooltip = function() {

		if ( l10n.premium_active ) {
			return false;
		}

		$( parent.document ).on( 'click', function( e ) {
			var wrapper = $( e.target ).closest( '.elementor-element--promotion' );

			if ( wrapper.length && wrapper.find( '.anwp-pg-pro-promotion-icon' ).length ) {
				window.open( 'https://grid.anwp.pro/premium-demo/', '_blank' );
			}
		} );
	};

	/**
	 * Card entrance animation (1.5.0) on the core grids: the widget element carries
	 * anwp-pg-entrance-{fade-up|fade|scale} (Elementor prefix_class); CSS in scss/public/blocks/_display-options.scss.
	 *
	 * - Runs only with IntersectionObserver and without prefers-reduced-motion; otherwise the posts wrapper
	 *   never gets anwp-pg-entrance--js and the cards stay visible (the CSS hides nothing on its own).
	 * - Cards already on screen at init are settled at once (never hidden): the page has painted them, so
	 *   hiding them would flash and push back LCP. Only cards that scroll in later, and every card added by
	 *   load more / pagination / premium header filter, animate.
	 * - Stagger = the card's place in the batch that entered the viewport together (one row, or a whole
	 *   load more / pagination batch), in DOM (= reading) order, capped at 8 steps.
	 * - New teasers (load more appends, pagination / header filter replace) are picked up by a
	 *   MutationObserver on the posts wrapper, so those handlers need no call here.
	 *
	 * @param {jQuery} $scope Widget element (frontend/element_ready).
	 */
	app.initEntrance = function( $scope ) {
		var STEP_MS = 90;
		var MAX_STEPS = 8;
		var el = $scope[ 0 ];
		var wrapper, io, vh;

		if ( ! el || ! ( 'IntersectionObserver' in window ) || window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
			return;
		}

		if ( ! el.classList.contains( 'anwp-pg-entrance-fade-up' ) && ! el.classList.contains( 'anwp-pg-entrance-fade' ) && ! el.classList.contains( 'anwp-pg-entrance-scale' ) ) {
			return;
		}

		wrapper = el.querySelector( '.anwp-pg-posts-wrapper' );

		if ( ! wrapper || wrapper.classList.contains( 'anwp-pg-entrance--js' ) ) {
			return;
		}

		function isTeaser( card ) {
			return card.classList.contains( 'anwp-pg-post-teaser' );
		}

		function settle( card ) {
			card.classList.add( 'is-done' );
			card.style.transitionDelay = '';
		}

		function watch() {
			Array.prototype.forEach.call( wrapper.children, function( card ) {
				if ( isTeaser( card ) && ! card.classList.contains( 'is-in' ) ) {
					io.observe( card );
				}
			} );
		}

		io = new IntersectionObserver( function( entries ) {
			var batch = entries
				.filter( function( e ) {
					return e.isIntersecting;
				} )
				.map( function( e ) {
					return e.target;
				} )
				.sort( function( a, b ) {
					return a.compareDocumentPosition( b ) & 4 ? -1 : 1; // eslint-disable-line no-bitwise
				} );

			batch.forEach( function( card, k ) {
				io.unobserve( card );
				card.style.transitionDelay = ( Math.min( k, MAX_STEPS ) * STEP_MS ) + 'ms';
				card.addEventListener( 'transitionend', function onEnd( ev ) {
					if ( ev.target === card && 'opacity' === ev.propertyName ) {
						card.removeEventListener( 'transitionend', onEnd );
						settle( card );
					}
				} );
				card.classList.add( 'is-in' );
			} );
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.15 } );

		// On screen now = already painted: settle before --js can hide it.
		vh = window.innerHeight || document.documentElement.clientHeight;

		Array.prototype.forEach.call( wrapper.children, function( card ) {
			var r = card.getBoundingClientRect();

			if ( isTeaser( card ) && r.top < vh && r.bottom > 0 ) {
				card.classList.add( 'is-in' );
				card.classList.add( 'is-done' );
			}
		} );

		watch();
		wrapper.classList.add( 'anwp-pg-entrance--js' );

		new MutationObserver( watch ).observe( wrapper, { childList: true } );
	};

	app.initLoadMore = function() {
		var $btn = app.$c.body.find( '.anwp-pg-load-more__btn' );

		if ( ! $btn.length ) {
			return false;
		}

		if ( $btn.closest( '.anwp-pg-wrap' ).find( '.anwp-pg-widget-header__has-filters' ).length ) {
			return false;
		}

		$btn.on( 'click', function( e ) {
			e.preventDefault();

			var $this = $( this );

			if ( $this.hasClass( 'anwp-pg-load-more--active' ) ) {
				return false;
			}

			var $postsWrapper = $this.closest( '.anwp-pg-wrap' ).find( '.anwp-pg-posts-wrapper' );

			$this.addClass( 'anwp-pg-load-more--active disabled' );
			$this.prop( 'disabled', true );
			app.clearRequestError( $postsWrapper );

			$.ajax( {
				url: l10n.rest_url + 'anwp-pg/v1/posts/load-more',
				type: 'POST',
				dataType: 'json',
				contentType: 'application/json',
				data: JSON.stringify( {
					args: $this.data( 'anwp-load-more' ),
					loaded: $this.data( 'anwp-loaded-qty' ),
					qty: $this.data( 'anwp-posts-per-load' )
				} )
			} ).done( function( response ) {
				if ( response ) {

					$( response.html ).appendTo( $postsWrapper );
					$this.data( 'anwp-loaded-qty', response.offset );

					if ( ! response.next ) {
						$this.closest( '.anwp-pg-load-more' ).remove();
					}

					$( document.body ).trigger( 'post-load' );
					$( document ).trigger( 'resize' );
				}
			} ).fail( function() {
				app.showRequestError( $postsWrapper );
			} ).always( function() {
				$this.removeClass( 'anwp-pg-load-more--active disabled' );
				$this.prop( 'disabled', false );
			} );

		} );
	};

	/**
	 * Show the "couldn't load posts" notice right after the posts wrapper.
	 *
	 * @param {jQuery} $postsWrapper Widget posts wrapper.
	 */
	app.showRequestError = function( $postsWrapper ) {
		app.clearRequestError( $postsWrapper );

		$( '<div class="anwp-pg-request-error" role="alert"></div>' )
			.text( l10n.request_error )
			.insertAfter( $postsWrapper );
	};

	/**
	 * Remove the request-error notice of a widget.
	 *
	 * @param {jQuery} $postsWrapper Widget posts wrapper.
	 */
	app.clearRequestError = function( $postsWrapper ) {
		$postsWrapper.siblings( '.anwp-pg-request-error' ).remove();
	};

	app.initSwiper = function( $scope ) {
		var $slider = $scope.find( '.anwp-pg-swiper-wrapper' ).eq( 0 );

		if ( ! $slider.length ) {
			return false;
		}

		// Get Swiper options
		var swiperOptions = {
			autoHeight: 'yes' !== $slider.data( 'pg-show-read-more' ),
			roundLengths: true,
			effect: $slider.data( 'pg-effect' ),
			spaceBetween: $slider.data( 'pg-space-between' )
		};

		$.extend( swiperOptions, app.getSwiperBreakpoints( $slider ) );

		if ( 'yes' === $slider.data( 'pg-loop' ) ) {
			swiperOptions.loop = true;
		}

		if ( 'yes' === $slider.data( 'pg-autoplay' ) ) {
			swiperOptions.autoplay = {
				delay: $slider.data( 'pg-autoplay-delay' )
			};
		}

		if ( 'yes' === $slider.data( 'pg-enable-observer' ) ) {
			swiperOptions.observer = true;
			swiperOptions.observeParents = true;
		}

		if ( 'fade' === swiperOptions.effect ) {
			swiperOptions.fadeEffect = {
				crossFade: true
			};
		}

		if ( $scope.find( '.swiper-pagination' ).length ) {
			swiperOptions.pagination = {
				el: '.swiper-pagination',
				type: 'bullets',
				clickable: true
			};
		}

		if ( $scope.find( '.elementor-swiper-button-prev' ).length ) {
			swiperOptions.navigation = {
				prevEl: '.elementor-swiper-button-prev',
				nextEl: '.elementor-swiper-button-next'
			};
		}

		if ( 'undefined' === typeof Swiper ) {
			new elementorFrontend.utils.swiper( $slider, swiperOptions );
		} else {
			new Swiper( $slider[0], swiperOptions );
		}
	};

	/**
	 * Swiper slidesPerView / slidesPerGroup per active Elementor device (also used by premium).
	 *
	 * Swiper breakpoints are min-widths: the smallest device is the base, each next device starts
	 * at the previous device's max + 1, widescreen at its own (min) value. A device without a
	 * value inherits from the next larger one, as in Elementor.
	 *
	 * @param {jQuery} $slider Slider with data-pg-responsive (slides_to_show / slides_to_scroll + _{device} keys).
	 * @return {Object} slidesPerView, slidesPerGroup and breakpoints
	 */
	app.getSwiperBreakpoints = function( $slider ) {
		var settings    = $slider.data( 'pg-responsive' );
		var breakpoints = elementorFrontend.config.responsive.activeBreakpoints;
		var devices     = elementorFrontend.breakpoints.getActiveBreakpointsList( { withDesktop: true } );
		var output      = { breakpoints: {} };

		// Template overrides from before 1.4.0 print the old fixed attributes.
		if ( ! settings || undefined === settings.slides_to_show ) {
			settings = {
				slides_to_show: $slider.data( 'pg-slides-per-view' ),
				slides_to_show_tablet: $slider.data( 'pg-slides-per-view-tablet' ),
				slides_to_show_mobile: $slider.data( 'pg-slides-per-view-mobile' ),
				slides_to_scroll: $slider.data( 'pg-slides-per-group' ),
				slides_to_scroll_tablet: $slider.data( 'pg-slides-per-group-tablet' ),
				slides_to_scroll_mobile: $slider.data( 'pg-slides-per-group-mobile' )
			};
		}

		// Same walk as Elementor's getResponsiveControlValue() (3.11+, the plugin supports 3.5):
		// the device's own value, else the next larger device's, up to desktop.
		var getValue = function( key, index ) {
			var device = devices[ index ];

			while ( 'desktop' !== device ) {
				if ( settings[ key + '_' + device ] || 0 === settings[ key + '_' + device ] ) {
					return settings[ key + '_' + device ];
				}

				device = 'widescreen' === device ? 'desktop' : devices[ ++index ];
			}

			return settings[ key ];
		};

		devices.forEach( function( device, index ) {
			var values = {
				slidesPerView: +getValue( 'slides_to_show', index ) || 1,
				slidesPerGroup: +getValue( 'slides_to_scroll', index ) || 1
			};

			if ( 0 === index ) {
				output.slidesPerView  = values.slidesPerView;
				output.slidesPerGroup = values.slidesPerGroup;
			} else if ( 'widescreen' === device ) {
				output.breakpoints[ breakpoints.widescreen.value ] = values;
			} else {
				output.breakpoints[ breakpoints[ devices[ index - 1 ] ].value + 1 ] = values;
			}
		} );

		return output;
	};

	app.initFlexSwiper = function( $scope ) {

		var $slider = $scope.find( '.anwp-pg-swiper-wrapper' ).eq( 0 );

		if ( ! $slider.length ) {
			return false;
		}

		// Get Swiper options
		var swiperOptions = {
			autoHeight: 'yes' !== $slider.data( 'pg-show-read-more' ),
			roundLengths: true,
			spaceBetween: $slider.data( 'pg-space-between' ),
			slidesPerView: 'auto'
		};

		if ( 'yes' === $slider.data( 'pg-autoplay' ) ) {
			swiperOptions.autoplay = {
				delay: $slider.data( 'pg-autoplay-delay' )
			};
		}

		if ( 'yes' === $slider.data( 'pg-loop' ) ) {
			swiperOptions.loop = true;
		} else {
			swiperOptions.loop = false;
		}

		if ( 'yes' === $slider.data( 'pg-free-mode' ) ) {
			swiperOptions.freeMode = true;
		}

		if ( 'yes' === $slider.data( 'pg-enable-observer' ) ) {
			swiperOptions.observer = true;
			swiperOptions.observeParents = true;
		}

		if ( $scope.find( '.swiper-pagination' ).length ) {
			swiperOptions.pagination = {
				el: '.swiper-pagination',
				type: 'bullets',
				clickable: true
			};
		}

		if ( $scope.find( '.elementor-swiper-button-prev' ).length ) {
			swiperOptions.navigation = {
				prevEl: '.elementor-swiper-button-prev',
				nextEl: '.elementor-swiper-button-next'
			};
		}

		if ( 'undefined' === typeof Swiper ) {
			new elementorFrontend.utils.swiper( $slider, swiperOptions );
		} else {
			new Swiper( $slider[0], swiperOptions );
		}
	};

	/**
	 * Pagination.
	 * Logic inspired by https://github.com/superRaytin/paginationjs
	 *
	 * @return {boolean} False if invalid data
	 */
	app.initPagination = function( $scope ) {
		var $wrappers = $scope.find( '.anwp-pg-pagination' );

		if ( ! $wrappers.length ) {
			return false;
		}

		$wrappers.each( function() {
			var $wrapper = $( this );
			var $xhr     = null;
			var $loader  = '<img class="anwp-pg-pagination-loader" src="' + l10n.loader + '" alt="loader">';

			if ( $wrapper.closest( '.anwp-pg-wrap' ).find( '.anwp-pg-widget-header__has-filters' ).length ) {
				return false;
			}

			var options = {
				show_previous: 'yes' === $wrapper.data( 'anwp-show_previous' ),
				show_next: 'yes' === $wrapper.data( 'anwp-show_next' ),
				show_first_ellipsis: 'yes' === $wrapper.data( 'anwp-show_first_ellipsis' ),
				show_last_ellipsis: 'yes' === $wrapper.data( 'anwp-show_last_ellipsis' ),
				auto_hide_previous: 'yes' === $wrapper.data( 'anwp-auto_hide_previous' ),
				auto_hide_next: 'yes' === $wrapper.data( 'anwp-auto_hide_next' ),
				total_pages: $wrapper.data( 'anwp-total' ),
				// SECURITY: the labels are plain text. The browser entity-decodes the attribute, so escape before the HTML concat in renderPagination().
				text_previous: $wrapper.data( 'anwp-text_previous' ) ? $( '<span>' ).text( $wrapper.data( 'anwp-text_previous' ) ).html() : '<span aria-hidden="true">&laquo;</span>',
				text_next: $wrapper.data( 'anwp-text_next' ) ? $( '<span>' ).text( $wrapper.data( 'anwp-text_next' ) ).html() : '<span aria-hidden="true">&raquo;</span>',
				current_page: 1,
				page_range: $wrapper.data( 'anwp-page_range' ),
				range_start: 1,
				range_end: 3,
				show_page_numbers: 'yes' === $wrapper.data( 'anwp-show_page_numbers' )
			};

			var $postsWrapper = $wrapper.closest( '.anwp-pg-wrap' ).find( '.anwp-pg-posts-wrapper' );

			if ( 1 !== $postsWrapper.length ) {
				return false;
			}

			$wrapper.on( 'click', '.anwp-page-link', function( e ) {
				e.preventDefault();

				var $this = $( this );

				if ( $this.hasClass( 'anwp-page-link--active' ) || $this.hasClass( 'anwp-page-link--disabled' ) ) {
					return false;
				}

				var previousPage = options.current_page;

				// Set current page
				options.current_page = Number( $this.data( 'anwp-number' ) );

				// Render pagination block
				renderPagination();

				// Show loader
				$postsWrapper.addClass( 'anwp-pg-pagination--loading' );
				$postsWrapper.append( $loader );
				app.clearRequestError( $postsWrapper );

				// Abort previous Ajax request
				if ( $xhr && 4 !== $xhr.readyState ) {
					$xhr.abort();
				}

				$xhr = $.ajax( {
					url: l10n.rest_url + 'anwp-pg/v1/posts/paginate',
					type: 'POST',
					dataType: 'json',
					contentType: 'application/json',
					data: JSON.stringify( {
						args: $wrapper.data( 'anwp-pagination' ),
						page: options.current_page
					} )
				} ).done( function( response ) {
					if ( response && response.html ) {

						$postsWrapper.html( response.html );

						$( document.body ).trigger( 'post-load' );
						$( document ).trigger( 'resize' );
					}
				} ).fail( function( jqXHR, textStatus ) {

					// A newer page click aborted this request on purpose.
					if ( 'abort' === textStatus ) {
						return;
					}

					// Back to the page still on screen, so the failed page can be clicked again.
					options.current_page = previousPage;
					renderPagination();

					app.showRequestError( $postsWrapper );
				} ).always( function() {
					$postsWrapper.removeClass( 'anwp-pg-pagination--loading' );
				} );
			} );

			renderPagination();

			function renderPagination() {

				var html = '';
				var ii;

				if ( ! options.current_page ) {
					return false;
				}

				options.range_start = options.current_page - options.page_range;
				options.range_end   = options.current_page + options.page_range;

				if ( options.range_end > options.total_pages ) {
					options.range_end   = options.total_pages;
					options.range_start = options.total_pages - options.page_range * 2;
					options.range_start = 1 > options.range_start ? 1 : options.range_start;
				}

				if ( 1 >= options.range_start ) {
					options.range_start = 1;
					options.range_end   = Math.min( options.page_range * 2 + 1, options.total_pages );
				}

				// Show Previous
				if ( options.show_previous ) {
					if ( 1 >= options.current_page ) {
						if ( ! options.auto_hide_previous ) {
							html += '<li class="anwp-page-item"><a class="anwp-page-link anwp-page-link--disabled" href="#">' + options.text_previous + '</a></li>';
						}
					} else {
						html += '<li class="anwp-page-item"><a class="anwp-page-link" title="Previous page" data-anwp-number="' + ( options.current_page - 1 ) + '" href="#">' + options.text_previous + '</a></li>';
					}
				}

				if ( options.show_page_numbers ) {
					if ( 3 >= options.range_start ) {
						for ( ii = 1; ii < options.range_start; ii++ ) {
							if ( ii === options.current_page ) {
								html += '<li class="anwp-page-item"><a class="anwp-page-link anwp-page-link--active" href="#" data-anwp-number="' + ii + '">>' + ii + '</a></li>';
							} else {
								html += '<li class="anwp-page-item"><a class="anwp-page-link" data-anwp-number="' + ii + '" href="#">' + ii + '</a></li>';
							}
						}
					} else {
						if ( options.show_first_ellipsis ) {
							html += '<li class="anwp-page-item"><a href="#" class="anwp-page-link" data-anwp-number="1">1</a></li>';
						}
						html += '<li class="anwp-page-item"><a class="anwp-page-link anwp-page-link--disabled">...</a></li>';
					}

					for ( ii = options.range_start; ii <= options.range_end; ii++ ) {
						if ( ii === options.current_page ) {
							html += '<li class="anwp-page-item"><a href="#" class="anwp-page-link anwp-page-link--active" data-anwp-number="' + ii + '">' + ii + '</a></li>';
						} else {
							html += '<li class="anwp-page-item"><a href="#" class="anwp-page-link" data-anwp-number="' + ii + '">' + ii + '</a></li>';
						}
					}

					if ( options.range_end >= options.total_pages - 2 ) {
						for ( ii = options.range_end + 1; ii <= options.total_pages; ii++ ) {
							html += '<li class="anwp-page-item"><a href="#" class="anwp-page-link" data-anwp-number="' + ii + '">' + ii + '</a></li>';
						}
					} else {
						html += '<li class="anwp-page-item"><a href="#" class="anwp-page-link anwp-page-link--disabled">...</a></li>';

						if ( options.show_last_ellipsis ) {
							html += '<li class="anwp-page-item"><a href="#"  class="anwp-page-link" data-anwp-number="' + options.total_pages + '">' + options.total_pages + '</a></li>';
						}
					}
				}

				// Show Next
				if ( options.show_next ) {
					if ( options.current_page >= options.total_pages ) {
						if ( ! options.auto_hide_next ) {
							html += '<li class="anwp-page-item"><a class="anwp-page-link anwp-page-link--disabled" href="#">' + options.text_next + '</a></li>';
						}
					} else {
						html += '<li class="anwp-page-item"><a class="anwp-page-link" data-anwp-number="' + ( options.current_page + 1 ) + '" title="Next page" href="#">' + options.text_next + '</a></li>';
					}
				}

				$wrapper.html( html );
			}
		} );
	};

	// Engage!
	app.init();
}( window, jQuery, window.anwpPostGridElementor, window.anwpPostGridElementorData ) );
