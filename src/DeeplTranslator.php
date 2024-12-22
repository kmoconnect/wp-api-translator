<?php

namespace Sitesoft\WpApiTranslator;

class DeeplTranslator implements TranslatorInterface {
	private $apiKey;
	private $endpoint;

	public function __construct( string $apiKey, string $endpoint = 'https://api-free.deepl.com/v2/translate' ) {
		$this->apiKey   = $apiKey;
		$this->endpoint = $endpoint;
	}

	public function translate( string $text, string $targetLang ): string {
		$response = wp_remote_post( $this->endpoint, [
			'body' => [
				'auth_key'    => $this->apiKey,
				'text'        => $text,
				'target_lang' => strtoupper( $targetLang ),
			],
		] );

		if ( is_wp_error( $response ) ) {
			error_log( 'Deepl API Error: ' . $response->get_error_message() );

			return $text;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		return $body['translations'][0]['text'] ?? $text;
	}
}
