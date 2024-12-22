<?php

namespace Sitesoft\WpApiTranslator;

use function remove_action;
use function wpml_get_default_language;
use function wpml_get_trid;

class WPMLPostTranslator {
	private TranslatorInterface $translator;

	public function __construct( TranslatorInterface $translator ) {
		$this->translator = $translator;
	}

	public function translatePostACF( $post_id, array $targetLanguages ): void {
		$this->translatePost( $post_id, $targetLanguages );

		$acf_fields = get_fields( $post_id );

		if ( ! $acf_fields ) {
			return;
		}

		foreach ( $acf_fields as $field_key => $field_value ) {
			if ( is_string( $field_value ) ) {
				$translated_value = $this->translator->translate( $field_value, $targetLanguages );
				update_field( $field_key, $translated_value, $post_id );
			} elseif ( is_array( $field_value ) ) {
				$translated_array = array_map( function ( $value ) use ( $targetLanguages ) {
					return is_string( $value )
						? $this->translator->translate( $value, $targetLanguages )
						: $value;
				}, $field_value );

				update_field( $field_key, $translated_array, $post_id );
			}
		}
	}

	public function translatePost( $post_id, array $targetLanguages ): void {
		$post = get_post( $post_id );

		if ( ! $post ) {
			return;
		}

		$trid = apply_filters( 'wpml_element_trid', null, $post_id, 'post_' . $post->post_type );

		if ( ! $trid ) {
			error_log( "Kon geen TRID ophalen voor post ID: $post_id" );

			return;
		}

		foreach ( $targetLanguages as $lang ) {
			$translated_title   = $this->translator->translate( $post->post_title, $lang );
			$translated_content = $this->translator->translate( $post->post_content, $lang );

			$new_post_id = wp_insert_post( [
				'post_title'   => $translated_title,
				'post_content' => $translated_content,
				'post_status'  => 'publish',
				'post_type'    => $post->post_type,
				'post_author'  => $post->post_author,
			] );

			if ( $new_post_id ) {
				do_action( 'wpml_set_element_language_details', [
					'element_id'           => $new_post_id,
					'element_type'         => 'post_' . $post->post_type,
					'trid'                 => $trid,
					'language_code'        => $lang,
					'source_language_code' => apply_filters( 'wpml_default_language', null ),
				] );
			}
		}
	}

}
