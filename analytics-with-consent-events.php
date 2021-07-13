<?php
/**
 * Analytics with Consent Events (Plugin extension)
 *
 * @package     AnalyticsWithConsentEvents
 * @author      dxw
 * @copyright   2021
 * @license     MIT
 *
 * @analytics-with-consent-events
 * Plugin Name: Analytics with Consent Events
 * Plugin URI: https://github.com/dxw/analytics-with-consent-events
 * Description: Adds event tracking to Analytics with Consent plugin
 * Author: dxw
 * Version: 0.1.0
 * Network: True
 */

$registrar = require __DIR__.'/src/load.php';
$registrar->register();
