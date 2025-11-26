<?php

/**
 * @package FD_Embed_Privacy
 * 
 * Relocated fd-embed-privacy plugin
 * 
 * Author: Alexander Merz <alexander.merz@funkemedien.de>
 * Version: 1.0.1
 */
namespace FdEmbedPrivacy;

define( 'FD_EMBEDPRIVACY', '1.0.1' );

/**
 * FDL-1174 Vanilla Embed Privacy Plugin
 */
\add_action( 'after_setup_theme', function()	 {
    \remove_theme_support( 'jetpack-responsive-videos' );
}, 11 );

\add_action( 'init', function() {
	$s = \load_plugin_textdomain( 'fd-embed-privacy', false, dirname( \plugin_basename( __FILE__ ) ) . '/../fd-embed-privacy/languages' );
});

\add_filter( 'embed_privacy_content', function( $content, $provider) {


    	$domain = $_SERVER['SERVER_NAME'];
		$html = 'Embed not set';

		#Break down the string to be shorter.
		if (str_contains($provider, 'www')) {
			$provider = str_replace('www.', '', $provider);
		}
		if (str_contains($provider, '.com')) {
			$provider = str_replace('.com', '', $provider);
		}

		#Dynamic name to choose medai image.
		$media = \plugins_url( "../assets/images/embed-{$provider}.png", __FILE__ );
		
		#Decide which language from the URI 
		if(str_contains($domain, '.dk')) {
			$html =
			'<h5 class="fd-embed-privacy-content-line1" style="font-size: 20px">'
			. 'Klik for at vise eksternt indhold fra <b>%s</b>, <br/> <span  style="font-size: 14px">- Du kan altid aktivere og deaktivere tredjepartsindhold.</span>'
			. '</h5>'
			. '<div style="width: 280px; height: 150px; margin: 0 auto; padding: 30px 0;">'
			. '<img style="width:100%%; height:100%%; object-fit:contain; margin: 0 auto;" src="%s" /></div>'
			. '<h5 class="fd-embed-privacy-content-line2">'
			. 'Du accepterer hermed at vise eksternt tredjepartsindhold. Persondata kan blive sendt til udbyderen af indholdet og andre tredjepartstjenester.'
			. '</h5>';
		}
		elseif(str_contains($domain, '.de')) {
			$html =
			'<h5 class="fd-embed-privacy-content-line1" style="font-size: 20px">'
			. 'Klicken Sie hier, um externe Inhalte von <b>%s</b> anzuzeigen, <br/> <span  style="font-size: 14px">- Sie können Drittanbieter-Inhalte jederzeit aktivieren oder deaktivieren.</span>'
			. '</h5>'
			. '<div style="width: 280px; height: 150px; margin: 0 auto; padding: 30px 0;">'
			. '<img style="width:100%%; height:100%%; object-fit:contain; margin: 0 auto;" src="%s" /></div>'
			. '<h5 class="fd-embed-privacy-content-line2">'
			. 'Sie stimmen zu, externe Inhalte von Drittanbietern anzuzeigen. Es ist möglich, dass personenbezogene Daten an den Anbieter der Inhalte und andere Drittanbieter-Dienste übermittelt werden.'
			. '</h5>';
		}
		elseif(str_contains($domain, 'se')) {
			$html =
			'<h5 class="fd-embed-privacy-content-line1" style="font-size: 20px">'
			. 'Klicka för att visa externt innehåll från <b>%s</b>, <br/> <span  style="font-size: 14px">- Du kan alltid aktivera och inaktivera tredjepartsinnehåll.</span>'
			. '</h5>'
			. '<div style="width: 280px; height: 150px; margin: 0 auto; padding: 30px 0;">'
			. '<img style="width:100%%; height:100%%; object-fit:contain; margin: 0 auto;" src="%s" /></div>'
			. '<h5 class="fd-embed-privacy-content-line2">'
			. 'Du godkänner att visa externt innehåll från tredje part. Personuppgifter kan skickas till innehållsleverantören och andra tredjepartstjänster.'
			. '</h5>';
		}
		elseif(str_contains($domain, 'no')) {
			$html =
			'<h5 class="fd-embed-privacy-content-line1" style="font-size: 20px">'
			. 'Klikk for å vise eksternt innhold fra <b>%s</b>, <br/> <span  style="font-size: 14px">- Du kan alltid aktivere og deaktivere tredjepartsinnhold.</span>'
			. '</h5>'
			. '<div style="width: 280px; height: 150px; margin: 0 auto; padding: 30px 0;">'
			. '<img style="width:100%%; height:100%%; object-fit:contain; margin: 0 auto;" src="%s" /></div>'
			. '<h5 class="fd-embed-privacy-content-line2">'
			. 'Du godtar å vise eksternt tredjepartsinnhold. Personopplysninger kan bli sendt til leverandøren av innholdet og andre tredjepartstjenester.'
			. '</h5>';
		}
		else {
			$html =
			'<h5 style="font-size: 20px" class="fd-embed-privacy-content-line1">'
			. 'Click to display external content from <b>%s</b>, <br/> <span  style="font-size: 14px">- You can always enable and disable third-party content.</b>'
			. '</h5>'
			.'<div style="width: 280px; height: 150px; margin: 0 auto; padding: 30px 0;">'
			. '<img style="width:100%%; height:100%%; object-fit:contain; margin: 0 auto;" src="%s" /></div>'
			. '<h5 class="fd-embed-privacy-content-line2">'
			. 'You agree to display external third-party content. Personal data may be sent to the provider of the content and other third-party services.'
			. '</h5>';
		}
		
		return sprintf($html, $provider, $media,  $domain);
}, 10, 2);

\add_filter( 'embed_privacy_markup', function( $markup, $provider ) {
    \wp_enqueue_script( 'fd-embed-privacy', \plugins_url( '../fd-embed-privacy/fd-embed-privacy.js', __FILE__ ), array( 'embed-privacy' ), FD_EMBEDPRIVACY, false );
    \wp_enqueue_style( 'fd-embed-privacy', \plugins_url( '../fd-embed-privacy/fd-embed-privacy.css', __FILE__ ), array( 'embed-privacy' ), FD_EMBEDPRIVACY, false );

	$embed_provider = \esc_html( $provider );
	$embed_provider_lowercase = \esc_attr( \sanitize_title( $provider ) );
	$uid = \esc_attr('check_' . $embed_provider_lowercase . '_' . \wp_unique_id());
	$togglecls =  $embed_provider_lowercase . '__toggle';

    $markup = preg_replace('#<p class="embed-privacy-input-wrapper">(.*?)<\/p>#ms', '<p class="embed-privacy-input-wrapper"></p>', $markup);
	
	$t_alt_switch = sprintf( 
		\__('Enable/Disable recommended external content from %s', 'fd-embed-privacy'),
		$embed_provider);
    $t_switch = \__('External content', 'fd-embed-privacy');
	$t_privacy_text = \__('Read more about in our', 'fd-embed-privacy');
	$t_privacy_link = \__('Privacy statement', 'fd-embed-privacy');
	$t_privacy = sprintf( '%s <a class="consent-box__privacy-url" target="_blank" href="/datenschutz">%s</a>', $t_privacy_text, $t_privacy_link);
    $replace =<<<EOT
</div>
<div class="consent-box consent-box--show-compact">
	<label class="consent-box__toggle-wrapper embed-privacy-inner" title="$t_alt_switch">
		<input type="checkbox" name="consent" id="$uid" class="consent-box__toggle embed-privacy-input $togglecls" data-embed-provider="$embed_provider_lowercase">
		<strong class="consent-box__toggle-visual"></strong>
		<strong class="consent-box__toggle-text">$t_switch</strong>
	</label>
	<script>
		document.addEventListener( 'DOMContentLoaded', function() {
			fd_embed_check_status(document.getElementById('$uid'));
		});
	</script>
	<p>$t_privacy</p>
</div>
<style>
EOT;
	$markup = preg_replace('#<\/div>\s+<style>#', $replace, $markup);

	// finally remove the custom css style for the container
	$markup = preg_replace('#\[data-embed-id.+\}#mUs', '', $markup); 

	return $markup;
}, 10, 2 );

