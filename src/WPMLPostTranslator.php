<?php

namespace Sitesoft\WpApiTranslator;

class WPMLPostTranslator {
	private TranslatorInterface $translator;

	public function __construct( TranslatorInterface $translator ) {
		$this->translator = $translator;
	}

	public function translatePostACF( $post_id, array|string $targetLanguages ): void {
		$this->translatePost( $post_id, $targetLanguages );

		$acf_fields = get_field_objects( $post_id );

		if ( ! $acf_fields ) {
			return;
		}

		foreach ( $acf_fields as $field ) {
			if ( ! is_array( $field ) ) {
				continue;
			}

			if ( ! isset( $field["wpml_cf_preferences"] ) ) {
				continue;
			}

			// only ACF WPML translating -> wpml_cf_preferences = 2
			if ( $field["wpml_cf_preferences"] !== 2 ) {
				continue;
			}

			foreach ( $targetLanguages as $language ) {
				$translated_value = $this->translator->translate( $field["value"], $language );
				update_field( $field["name"], $translated_value, $post_id );
			}
		}
	}

	public function translatePost( $post_id, array|string $targetLanguages ): void {
		$post = get_post( $post_id );

		if ( ! $post ) {
			return;
		}

		$trid = apply_filters( 'wpml_element_trid', null, $post_id, 'post_' . $post->post_type );

		if ( ! $trid ) {
			error_log( "Kon geen TRID ophalen voor post ID: $post_id" );

			return;
		}

		if ( is_string( $targetLanguages ) ) {
			$targetLanguages = [ $targetLanguages ];
		}

		foreach ( $targetLanguages as $lang ) {
			$existing_title   = apply_filters( 'wpml_get_string', null, $post->post_title, $lang );
			$existing_content = apply_filters( 'wpml_get_string', null, $post->post_content, $lang );

			if ( ! empty( $existing_title ) || ! empty( $existing_content ) ) {
				continue;
			}

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
