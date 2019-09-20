<?php

class LodgixTranslate {

    const LOCALIZATION_DOMAIN = 'p_lodgix';

    public static function translate($str) {
        return __($str, self::LOCALIZATION_DOMAIN);
    }

    public static function translateTerm($str, $language) {
        global $wpdb;
        $table = $wpdb->prefix . LodgixConst::TABLE_TRANSLATONS;
        $safeSql = $wpdb->prepare(
            "SELECT translation FROM $table WHERE eng_name=%s AND lang=%s",
            $str,
            $language
        );
        $name = $wpdb->get_var($safeSql);
        if ($name) {
            return $name;
        }
        return $str;
    }

    // Singleton
    protected function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

}
