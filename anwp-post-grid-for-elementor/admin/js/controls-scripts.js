/* eslint-disable camelcase */
/**
 * AnWP Post Grid - Controls Scripts
 * https://anwp.pro
 *
 * Licensed under the GPLv2+ license.
 */

window.AnWPPostGridControls = window.AnWPPostGridControls || {};

( function( window, document, $, plugin ) {

	'use strict';

	var $c = {};

	plugin.init = function() {
		plugin.cache();
		plugin.bindEvents();
	};

	plugin.cache = function() {
		$c.window   = $( window );
		$c.body     = $( document.body );
		$c.document = $( document );

		$c.searchData = {
			context: '',
			s: ''
		};

		$c.activeLink  = null;
		$c.xhr         = null;
		$c.initialized = false;

		$c.backdropDown = false;
	};

	plugin.bindEvents = function() {
		if ( 'loading' !== document.readyState ) {
			plugin.onPageReady();
		} else {
			document.addEventListener( 'DOMContentLoaded', plugin.onPageReady );
		}
	};

	plugin.onPageReady = function() {

		elementor.hooks.addAction( 'panel/open_editor/widget', function( panel, model, view ) {

			if ( ! panel.el || ! $( panel.el ).find( '.anwp-pg-selector' ).length ) {
				return false;
			}

			if ( ! $c.initialized ) {

				if ( 'undefined' !== typeof anwp_PG_ID_Selector ) {
					$c.body.append( anwp_PG_ID_Selector.selectorHtml );
				} else {
					return false;
				}

				$c.initialized = true;

				$c.body.on( 'click', '.anwp-pg-selector', plugin.openSelectorModaal );

				$c.body.on( 'click', '#anwp-pg-selector-modaal__cancel', function( e ) {
					e.preventDefault();
					plugin.closeSelectorModaal();
				} );

				// Backdrop click closes the selector. Track mousedown too, so a text selection dragged
				// out of the search input onto the backdrop doesn't close it.
				$c.body.on( 'mousedown', '#anwp-pg-selector-modaal', function( e ) {
					$c.backdropDown = e.target === this;
				} );

				$c.body.on( 'click', '#anwp-pg-selector-modaal', function( e ) {
					if ( e.target === this && $c.backdropDown ) {
						plugin.closeSelectorModaal();
					}
				} );

				// Elementor's "esc" shortcut (keydown on window) calls preventDefault(), which cancels
				// the dialog's native close on Esc - close it here and keep the key from Elementor.
				$c.body.on( 'keydown', '#anwp-pg-selector-modaal', function( e ) {
					if ( 'Escape' === e.key ) {
						e.preventDefault();
						e.stopPropagation();
						plugin.closeSelectorModaal();
					}
				} );

				$c.body.on( 'click', '.anwp-pg-selector-action', function( e ) {
					e.preventDefault();
					plugin.addSelected( $( this ).closest( 'tr' ).data( 'id' ), $( this ).closest( 'tr' ).data( 'name' ) );
				} );

				$c.body.on( 'click', '.anwp-pg-selector-action-no', function( e ) {
					e.preventDefault();
					$( this ).closest( '.anwp-pg-selector-modaal__selected-item' ).remove();
				} );

				$c.body.on( 'click', '#anwp-pg-selector-modaal__insert', function( e ) {
					e.preventDefault();

					var output = [];

					$c.body.find( '#anwp-pg-selector-modaal__selected .anwp-pg-selector-modaal__selected-item' ).each( function() {
						output.push( $( this ).find( '.anwp-pg-selector-action-no' ).data( 'id' ) );
					} );

					plugin.closeSelectorModaal();
					$c.activeLink.prev( 'input' ).val( output.join( ',' ) );
					$c.activeLink.prev( 'input' ).trigger( 'input' );
					$c.activeLink.prev( 'input' ).trigger( 'change' );
				} );

				$c.body.on( 'keyup', '#anwp-pg-selector-modaal__search', _.debounce( function() {
					plugin.sendSearchRequest();
				}, 500 ) );
			}
		} );
	};

	plugin.hideSpinner = function() {
		$( '#anwp-pg-selector-modaal__initial-spinner' ).addClass( 'd-none' );
	};

	plugin.showSpinner = function() {
		$( '#anwp-pg-selector-modaal__initial-spinner' ).removeClass( 'd-none' );
	};

	plugin.openSelectorModaal = function( evt ) {

		var dialog = document.getElementById( 'anwp-pg-selector-modaal' );

		$c.activeLink = $( evt.currentTarget );

		if ( ! dialog.open ) {
			dialog.showModal();
		}

		plugin.initializeSelectorContent();
	};

	plugin.closeSelectorModaal = function() {
		document.getElementById( 'anwp-pg-selector-modaal' ).close();
	};

	plugin.addSelected = function( id, name ) {

		var $wrapper = $( '#anwp-pg-selector-modaal__selected' );

		if ( $wrapper.find( '[data-id="' + id + '"]' ).length ) {
			return false;
		}

		var appendHTML = '<div class="anwp-pg-selector-modaal__selected-item"><button type="button" class="anwp-g-button anwp-pg-selector-action-no" data-id="' + id + '"><span class="dashicons dashicons-no"></span></button><span>' + name + '</span></div>';

		$wrapper.append( appendHTML );
	};

	plugin.initializeSelectorContent = function() {

		$c.searchData.context = $c.activeLink.data( 'context' );
		$c.searchData.s       = '';

		plugin.showSpinner();

		// Load Initial Values
		if ( $c.activeLink.prev( 'input' ).val() ) {
			$.ajax( {
				url: anwp_PG_ID_Selector.restUrl + 'anwp-pg/v1/selector/initial',
				type: 'POST',
				dataType: 'json',
				contentType: 'application/json',
				headers: { 'X-WP-Nonce': anwp_PG_ID_Selector.restNonce },
				data: JSON.stringify( {
					initial: $c.activeLink.prev( 'input' ).val(),
					data_context: $c.searchData.context
				} )
			} ).done( function( response ) {
				if ( response && response.items ) {
					_.each( response.items, function( pp ) {
						plugin.addSelected( pp.id, pp.name );
					} );
				}
			} ).always( function() {
				plugin.hideSpinner();
			} );
		} else {
			plugin.hideSpinner();
		}

		// Update form
		$( '#anwp-pg-selector-modaal__header-context' ).html( $c.searchData.context );
		$( '#anwp-pg-selector-modaal__content' ).html( '' );
		$( '#anwp-pg-selector-modaal__selected' ).html( '' );
		$( '#anwp-pg-selector-modaal__search' ).val( '' );

		plugin.sendSearchRequest();
	};

	plugin.sendSearchRequest = function() {

		if ( $c.xhr && 4 !== $c.xhr.readyState ) {
			$c.xhr.abort();
		}

		$( '#anwp-pg-selector-modaal__content' ).addClass( 'anwp-search-is-active' ).html( '' );

		// Search Data
		$c.searchData.s = $( '#anwp-pg-selector-modaal__search' ).val();

		$c.xhr = $.ajax( {
			url: anwp_PG_ID_Selector.restUrl + 'anwp-pg/v1/selector/search',
			type: 'POST',
			dataType: 'json',
			contentType: 'application/json',
			headers: { 'X-WP-Nonce': anwp_PG_ID_Selector.restNonce },
			data: JSON.stringify( {
				s: $c.searchData.s,
				context: $c.searchData.context
			} )
		} ).done( function( response ) {
			if ( response ) {
				$( '#anwp-pg-selector-modaal__content' ).html( response.html );
			}
		} ).always( function() {
			$( '#anwp-pg-selector-modaal__content' ).removeClass( 'anwp-search-is-active' );
		} );
	};

	plugin.init();
}( window, document, jQuery, window.AnWPPostGridControls ) );
