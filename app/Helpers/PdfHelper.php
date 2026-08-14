<?php

namespace App\Helpers;

use ArPHP\I18N\Arabic;

class PdfHelper
{
    protected static ?Arabic $arabic = null;

    /**
     * Reshape Arabic string for DomPDF LTR engine
     */
    public static function ar(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        // Check if string contains Arabic characters
        if (preg_match('/\p{Arabic}/u', $text)) {
            if (!self::$arabic) {
                self::$arabic = new Arabic();
            }
            return self::$arabic->utf8Glyphs($text);
        }

        return $text;
    }
}
