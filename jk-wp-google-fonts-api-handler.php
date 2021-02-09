<?php defined('ABSPATH') || exit;

class jk_wp_google_fonts_api_handler
{

    public function init()
    {

        add_action('wp_enqueue_scripts', [$this, 'load_google_fonts']);

    }

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

    public function load_google_fonts()
    {

        $fontsSubset = get_option('jk-theme-typography-settings-font-subsets');

        $heading_font = get_option('jk-theme-typography-settings-heading-font');

        $content_font = get_option('jk-theme-typography-settings-content-font');

        $meta_font = get_option('jk-theme-typography-settings-content-meta-font');

        $fontsArr = array($heading_font, $content_font, $meta_font);

        if (empty($fontsSubset)):

            $fontsSubset = array('latin');

        endif;

        $fontsSubset = implode(',', $fontsSubset);

        $fonts_url = '';

        $fonts = array();

        foreach ($fontsArr as $font):

            $fonts[] = '' . $font . ':100,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i';

        endforeach;

        $fonts = array_unique($fonts);

        if ($fonts) :

            $fonts_url = add_query_arg(array(
                'family' => urlencode(implode('|', $fonts)),
                'subset' => urlencode($fontsSubset),
            ),
                'https://fonts.googleapis.com/css');

        endif;

        wp_enqueue_style('jk-fonts', $fonts_url, array());

    }

}
