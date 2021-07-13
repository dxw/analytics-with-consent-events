<?php

namespace AnalyticsWithConsentEvents;

describe(Scripts::class, function () {
    beforeEach(function () {
        $this->scripts = new Scripts();
    });

    it('is registerable', function () {
        expect($this->scripts)->toBeAnInstanceOf(\Dxw\Iguana\Registerable::class);
    });

    describe('->register()', function () {
        it('adds actions', function () {
            allow('add_action')->toBeCalled();
            expect('add_action')->toBeCalled()->times(1);
            expect('add_action')->toBeCalled()->with('wp_enqueue_scripts', [$this->scripts, 'enqueueScripts']);
            $this->scripts->register();
        });
    });

    describe('->enqueueScripts()', function () {
        context('Civic Cookie API Key is not set', function () {
            it('does nothing', function () {
                allow('get_field')->toBeCalled()->andReturn('');
                expect('get_field')->toBeCalled()->once()->with('civic_cookie_control_api_key', 'option');
                $this->scripts->enqueueScripts();
            });
        });
        context('Civic Cookie API Key is set', function () {
            context('but Civic Product Type is not set', function () {
                it('does nothing', function () {
                    allow('get_field')->toBeCalled()->andReturn('an_api_key', '');
                    expect('get_field')->toBeCalled()->once()->with('civic_cookie_control_api_key', 'option');
                    expect('get_field')->toBeCalled()->once()->with('civic_cookie_control_product_type', 'option');
                    $this->scripts->enqueueScripts();
                });
            });
            context('and Civic Product Type is set', function () {
                it('enqueues the Analytics with Consent Events script, and injects our settings', function () {
                    allow('get_field')->toBeCalled()->andReturn('an_api_key', 'a_product_type', 'a_ga_id');
                    expect('get_field')->toBeCalled()->once()->with('civic_cookie_control_api_key', 'option');
                    expect('get_field')->toBeCalled()->once()->with('civic_cookie_control_product_type', 'option');
                    expect('get_field')->toBeCalled()->once()->with('google_analytics_id', 'option');
                    allow('wp_enqueue_script')->toBeCalled();
                    allow('dirname')->toBeCalled()->andReturn('/path/to/this/plugin');
                    allow('plugins_url')->toBeCalled()->andReturn('http://path/to/this/plugin/assets/js/analytics-events.js');
                    expect('plugins_url')->toBeCalled()->once()->with('/assets/js/analytics-events.js', '/path/to/this/plugin');
                    expect('wp_enqueue_script')->toBeCalled()->once()->with('civicCookieControlAnalyticsEvents', 'http://path/to/this/plugin/assets/js/analytics.js', ['civicCookieControlDefaultAnalytics']);
                    allow('wp_localize_script')->toBeCalled();
                    expect('wp_localize_script')->toBeCalled()->once()->with('civicCookieControlAnalyticsEvents', 'cookieControlAnalyticsEvents', [
                        'googleAnalyticsId' => 'a_ga_id',
                        'siteurl' => 'https://www.example.com'
                    ]);
                    // allow('apply_filters')->toBeCalled()->andRun(function ($filterName, $filteredData) {
                    //     return $filteredData;
                    // });
                    //expect('apply_filters')->toBeCalled()->once()->with('awc_civic_cookie_control_config', \Kahlan\Arg::toBeAn('array'));
                    //expect('wp_localize_script')->toBeCalled()->once()->with('civicCookieControlConfig', 'cookieControlConfig', \Kahlan\Arg::toBeAn('array'));
                    $this->scripts->enqueueScripts();
                });
            });
        });
    });
});
