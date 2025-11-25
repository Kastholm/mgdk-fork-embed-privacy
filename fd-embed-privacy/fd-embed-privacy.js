/* disable-perfmatters */

	console.log("EP started");
// is triggered if user clicks on the overlay or the enable slider
function fd_checkboxActivation(target) {
	var embedProvider = target.getAttribute( 'data-embed-provider' );
	var cookie = ( fd_embed_get_cookie( 'embed-privacy' ) ? JSON.parse( fd_embed_get_cookie( 'embed-privacy' ) ) : '' );
	
	var overlays = document.querySelectorAll( '.embed-'+embedProvider+' .embed-privacy-overlay');
	var contents = document.querySelectorAll( '.embed-'+embedProvider+' .embed-privacy-content');
	var toggles = document.querySelectorAll( '.' + embedProvider + '__toggle' );

	if ( ! target.checked && cookie !== null ) {
		overlays.forEach((el) => {el.style.display = "table";});		
		contents.forEach((el) => {el.style.display = "none";});
		toggles.forEach((el) => {el.checked = false;});
	} else {
		overlays.forEach((el) => {el.style.display = "none";});		
		contents.forEach((el) => {el.style.display = "block";});
		toggles.forEach((el) => {el.checked = true;});
	}
}

// special handling to set the cookie when enabled via the overlay
// needs to be checked if the EP plugin changes
function fd_ep_activate_cookie( target ) {
	var embedProvider = target.closest('.embed-privacy-container').getAttribute( 'data-embed-provider' );
	var cookie = ( fd_embed_get_cookie( 'embed-privacy' ) ? JSON.parse( fd_embed_get_cookie( 'embed-privacy' ) ) : '' );
	if ( cookie !== null && Object.keys( cookie ).length !== 0 && cookie.constructor === Object ) {
		cookie[ embedProvider ] = true;		
		fd_ep_set_cookie( 'embed-privacy', JSON.stringify( cookie ) );
	}
	else {
		fd_ep_set_cookie( 'embed-privacy', '{"' + embedProvider + '":true}', 365 );
	}
}

document.addEventListener( 'DOMContentLoaded', function() {
	// handle the slider
	var checkboxes = document.querySelectorAll( '.embed-privacy-inner .embed-privacy-input' );
	for ( var i = 0; i < checkboxes.length; i++ ) {
		checkboxes[ i ].addEventListener( 'click', function( event ) {
			// don't trigger the overlays click
			event.stopPropagation();
			fd_checkboxActivation( event.currentTarget );
		} );
	}	

	// handle the overlay
	var overlays = document.querySelectorAll( '.embed-privacy-overlay' );
	for ( var i = 0; i < overlays.length; i++ ) {
		overlays[ i ].addEventListener( 'click', function( event ) {
			// we are only here, because the overlay was visible&clicked
			var embedProvider = event.currentTarget.parentNode.getAttribute( 'data-embed-provider' );
			var all_overlays = document.querySelectorAll( '.embed-'+embedProvider+' .embed-privacy-overlay');
			var contents = document.querySelectorAll( '.embed-'+embedProvider+' .embed-privacy-content');
			var toggles = document.querySelectorAll( '.' + embedProvider + '__toggle' );
			all_overlays.forEach((el) => {
				fd_overlayClick(el);
				el.style.display = "none";
			});		
			fd_ep_activate_cookie(event.target); // set cookie only once per provider
			contents.forEach((el) => {el.style.display = "block";});
			toggles.forEach((el) => {el.checked = true;});			
		} );
	}	

} );

// figure out is embed can be enabled because of cookie
function fd_embed_check_status(target){
	var embedProvider = target.getAttribute('data-embed-provider');
	var cookie = ( fd_embed_get_cookie( 'embed-privacy' ) ? JSON.parse( fd_embed_get_cookie( 'embed-privacy' ) ) : '' );

	if (cookie[ embedProvider ] == true) {
        target.setAttribute('checked','true');
    } else {
        target.removeAttribute('checked');
    }
}

function fd_embed_get_cookie( name ) {
	var nameEQ = name + '=';
	var ca = document.cookie.split( ';' );
	for ( var i = 0; i < ca.length; i++ ) {
		var c = ca[ i ];
		while ( c.charAt( 0 ) == ' ' ) c = c.substring( 1, c.length );
		if ( c.indexOf( nameEQ ) == 0 ) return c.substring( nameEQ.length, c.length );
	}
	return null;
}

// this function is inspired from plugins\embed-privacy\assets\js\embed-privacy.js
// and might be updated if it changes in the original
function fd_overlayClick( target ) {
	var embedContainer = target.parentNode;
	var embedContent = target.nextElementSibling;
	if( embedContainer.classList.contains( 'is-enabled' ) ){
		return;
	}
	embedContainer.classList.remove( 'is-disabled' );
	embedContainer.classList.add( 'is-enabled' );
	// hide the embed overlay
	target.style.display = 'none';
	// get stored content from JavaScript
	var embedObject = JSON.parse( window[ '_' + target.parentNode.getAttribute( 'data-embed-id' ) ] );
	
	// phpcs:ignore WordPressVIPMinimum.JS.InnerHTML.Found
	embedContent.innerHTML = fd_ep_htmlentities_decode( embedObject.embed );
	
	// reset wrapper inline CSS set in setMinHeight()
	var wrapper = embedContainer.parentNode;
	
	if ( wrapper.classList.contains( 'wp-block-embed__wrapper' ) ) {
		wrapper.style.removeProperty( 'height' );
	}
	
	// get all script tags inside the embed
	var scriptTags = embedContent.querySelectorAll( 'script' );
	
	// insert every script tag inside the embed as a new script
	// to execute it
	for ( var n = 0; n < scriptTags.length; n++ ) {
		var element = document.createElement( 'script' );
		
		if ( scriptTags[ n ].src ) {
			// if script tag has a src attribute
			element.src = scriptTags[ n ].src;
			element.classList.add('test-embed');
		}
		else {
			// if script tag has content
			// phpcs:ignore WordPressVIPMinimum.JS.InnerHTML.Found
			element.innerHTML = scriptTags[ n ].innerHTML;
		}
		
		// append it to body
		embedContent.appendChild( element );
	}
	
	if ( typeof jQuery !== 'undefined' ) {
		const videoShortcode = jQuery( '.wp-video-shortcode' );
		
		if ( videoShortcode.length ) {
			videoShortcode.mediaelementplayer();
		}
	}
}

/**
 * Set a cookie.
 * 
 * @link	https://stackoverflow.com/a/24103596/3461955
 * 
 * @param	{string}	name The name of the cookie
 * @param	{string}	value The value of the cookie
 * @param	{number}	days The expiration in days
 */
function fd_ep_set_cookie( name, value, days ) {
	var expires = '';
	if ( days ) {
		var date = new Date();
		date.setTime( date.getTime() + ( days * 24 * 60 * 60 * 1000 ) );
		expires = '; expires=' + date.toUTCString();
	}
	document.cookie = name + '=' + ( value || '' ) + expires + '; path=/';
}

/**
 * Decode a string with HTML entities.
 * 
 * @param	{string}	content The content to decode
 * @return	{string} The decoded content
 */
function fd_ep_htmlentities_decode( content ) {
	var textarea = document.createElement( 'textarea' );
	// phpcs:ignore WordPressVIPMinimum.JS.InnerHTML.Found
	textarea.innerHTML = content;
	textarea.classList.add('test-embed-textsarea')
	
	return textarea.value;
}

