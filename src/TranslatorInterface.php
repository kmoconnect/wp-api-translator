<?php

namespace Sitesoft\WpApiTranslator;

interface TranslatorInterface {
	public function translate( string $text, string $targetLang ): string;
}