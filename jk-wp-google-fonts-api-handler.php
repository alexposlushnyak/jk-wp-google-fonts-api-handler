<?php defined('ABSPATH') || exit;

class jk_wp_google_fonts_api_handler
{

    public static function get_fonts_list()
    {

        $google_api_key = get_option('jk-google-settings-google-api-key');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/webfonts/v1/webfonts?key=" . $google_api_key);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json"
        ));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $fonts_list = json_decode(curl_exec($ch), true);

        curl_close($ch);

        return $fonts_list;

    }

    public static function caching_fonts_list()
    {

        $current_date = new DateTime(date('Y-m-d-G'));

        $expire_date = get_option('jk_cache_google_fonts_expire_date');

        $google_api = JK_Settings_Getter::get_field_value('jk-google-settings', 'api-toggle');

        $google_api_key = JK_Settings_Getter::get_field_value('jk-google-settings', 'google-api-key');

        $force = false;

        if (!empty($google_api_key) && $google_api):

            if ($current_date >= $expire_date || empty($expire_date) || $force):

                $fonts_list = self::get_fonts_list()['items'];

                $expire_date = new DateTime(date('Y-m-d-G'));

                $expire_date->modify('+7 days');

                update_option('jk_cache_google_fonts_expire_date', $expire_date, false);

                if (!empty($fonts_list)):

                    update_option('jk_cache_google_fonts', $fonts_list, false);

                endif;

            endif;

        endif;

    }

    public static function get_caching_fonts_list()
    {

        self::caching_fonts_list();

        $fonts_list = get_option('jk_cache_google_fonts');

        if (!empty($fonts_list)):

            return $fonts_list;

        endif;

    }

}
