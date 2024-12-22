<?php

namespace Sitesoft\WpApiTranslator;

use Sitesoft\WpApiTranslator\TranslatorInterface;

class FakeTranslator implements TranslatorInterface {

	public function translate( string $text, string $targetLang ): string {
		return $text;
	}
}