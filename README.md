# WP API Translator

This package provides an easy way to automatically translate WordPress posts and ACF fields into multiple languages
using translation APIs such as Deepl and Google Translate. It integrates with WPML for managing translations.

## Table of Contents

- [Installation](#installation)
- [Example Usage](#example-usage)
    - [1. Translate a Post on Save](#1-translate-a-post-on-save)
    - [2. Translate ACF Fields](#2-translate-acf-fields)
- [Translator Configuration](#translator-configuration)
    - [1. Deepl Translator](#1-deepl-translator)
    - [2. Google Translate Translator](#2-google-translate-translator)
- [WPML Integration](#wpml-integration)
- [Supported Languages](#supported-languages)
- [Notes](#notes)
- [License](#license)

## Installation

You can install this package via Composer by running the following command in the root of your WordPress project:

```bash
composer require sitesoft-be/wp-api-translator
```

## Example Usage

## 1. Translate a Post on Save

```php
use Sitesoft\WpApiTranslator\DeeplTranslator;
use Sitesoft\WpApiTranslator\WPMLPostTranslator;

require_once 'vendor/autoload.php';

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

	$wpmlTranslator->translatePost( $post_id, [ "fr", "en" ] );

	add_action( "save_post", "translatePost" );
}

add_action( 'save_post', 'translatePost' );
```

## 2. Translate ACF Fields

```php
use Sitesoft\WpApiTranslator\DeeplTranslator;
use Sitesoft\WpApiTranslator\WPMLPostTranslator;

require_once 'vendor/autoload.php';

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

	$wpmlTranslator->translatePostACF( $post_id, [ "fr", "en" ] );

	add_action( "save_post", "translatePost" );
}

add_action( 'save_post', 'translatePost' );
```

## Translator Configuration

## 1. Deepl Translator

You can use the Deepl API for high-quality translations. To configure it, replace the FakeTranslator with
DeeplTranslator:

```php
use Sitesoft\WpApiTranslator\DeeplTranslator;

$deeplTranslator = new DeeplTranslator( 'your-api-key-here' );
$wpmlTranslator = new WPMLPostTranslator( $deeplTranslator );

```

## 2. Google Translate Translator

```php
use Sitesoft\WpApiTranslator\GoogleTranslateTranslator;

$googleTranslator = new GoogleTranslateTranslator( 'your-api-key-here' );
$wpmlTranslator = new WPMLPostTranslator( $googleTranslator );
```

## WPML Integration

This package integrates with WPML to manage the multilingual content. It uses the wpml_set_element_language_details hook
to set the language details for newly translated posts.

## Supported Languages

This package supports any language that is available in the translation API you choose to use. By default, you can use
any language code supported by Deepl or Google Translate.

## Notes

The package relies on WPML for managing translations. Ensure WPML is installed and configured on your WordPress site.
Make sure to replace API keys with your actual API credentials for Deepl or Google Translate.
You may need to configure your ACF fields to ensure they are correctly translated.

## License

This package is licensed under the MIT License. See the LICENSE file for more details.
