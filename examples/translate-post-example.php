<?php

require_once 'vendor/autoload.php';

use Sitesoft\WpApiTranslator\DeeplTranslator;
use Sitesoft\WpApiTranslator\WPMLPostTranslator;

function translatePost( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	if ( get_post_type( $post_id ) !== "your_custom_post_type" ) {
		return;
	}

	remove_action( "save_post", "translatePost" );

	$deepTranslator = new DeeplTranslator( 'YOUR_DEEPL_API_KEY' );

	$wpmlTranslator = new WPMLPostTranslator( $deepTranslator );

	$wpmlTranslator->translatePost( $post_id, [ "fr" ] );

	add_action( "save_post", "translatePost" );
}

add_action( 'save_post', 'translatePost' );
