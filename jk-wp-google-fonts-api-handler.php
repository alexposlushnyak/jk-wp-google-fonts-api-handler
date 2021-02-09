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

}
