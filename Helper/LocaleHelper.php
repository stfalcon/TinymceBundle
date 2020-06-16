<?php

namespace Stfalcon\Bundle\TinymceBundle\Helper;

/**
 * Class LocaleHelper
 *
 * @package Stfalcon\Bundle\TinymceBundle\Helper
 */
class LocaleHelper
{
    static private $locales = array(
        'bn' => 'bn_BD',
        'bg' => 'bg_BG',
        'cn' => 'zh_CN',
        'fr' => 'fr_FR',
        'hu' => 'hu_HU',
        'il' => 'he_IL',
        'is' => 'is_IS',
        'sl' => 'sl_SI',
        'tr' => 'tr_TR',
        'tw' => 'zh_TW',
        'uk' => 'uk_UA',
    );

    /**
     * @param string $locale
     *
     * @return string
     */
    public static function getLanguage($locale)
    {
        return isset(self::$locales[$locale])
            ? self::$locales[$locale]
            : $locale;
    }
}
