<?php

namespace AnalyticsWithConsentEvents;

class Scripts implements \Dxw\Iguana\Registerable
{
    public function register() : void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
    }

    public function enqueueScripts() : void
    {
        $apiKey = get_field('civic_cookie_control_api_key', 'option');
        $productType = get_field('civic_cookie_control_product_type', 'option');
        $googleAnalyticsId = get_field('google_analytics_id', 'option');
        $siteurl = get_site_url();
        if ($apiKey && $productType) {
            // enqueue script with variables
            wp_enqueue_script('civicCookieControlAnalyticsEvents', plugins_url('/assets/js/analytics-events.js', dirname(__FILE__)), ['civicCookieControlDefaultAnalytics']);
            wp_localize_script('civicCookieControlAnalyticsEvents', 'cookieControlAnalyticsEvents', [
                'googleAnalyticsId' => $googleAnalyticsId,
                'siteurl' => $siteurl
            ]);
            // run script with onAccept
            add_filter('awc_civic_cookie_control_config', function (array $config) {
                $config['optionalCookies'][0]['onAccept'] = 'analyticsWithConsentEvents.init';
                return $config;
            });
        }
    }
}
