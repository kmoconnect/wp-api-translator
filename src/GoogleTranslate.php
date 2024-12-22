<?php

namespace Sitesoft\WpApiTranslator;

class GoogleTranslate implements TranslatorInterface {
	private $apiKey;
	private $endpoint;

	public function __construct(
		string $apiKey,
		string $endpoint = 'https://translation.googleapis.com/language/translate/v2'
	) {
		$this->apiKey   = $apiKey;
		$this->endpoint = $endpoint;
	}

	public function translate( string $text, string $targetLang ): string {
		$response = wp_remote_post( $this->endpoint, [
			'body' => [
				'q'      => $text,
				'target' => strtolower( $targetLang ),
				'key'    => $this->apiKey,
			],
		] );

		if ( is_wp_error( $response ) ) {
			error_log( 'Google Translate API Error: ' . $response->get_error_message() );

			return $text; // Fallback naar originele tekst
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		return $body['data']['translations'][0]['translatedText'] ?? $text;
	}
}