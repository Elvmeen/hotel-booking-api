<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| REST API Configuration
|--------------------------------------------------------------------------
*/

// Default output format: json, xml, html, php, serialize, csv
$config['rest_default_format'] = 'json';

// Supported formats
$config['rest_supported_formats'] = array(
    'json',
    'xml',
    'html',
    'csv',
    'php',
    'serialize',
    'jsonp',
);

// Log API requests to database
$config['rest_enable_logging'] = FALSE;
$config['rest_logs_table']     = 'api_logs';

// Enable API key authentication
$config['rest_enable_keys'] = FALSE;
$config['rest_keys_table']  = 'api_keys';

// IP whitelist/blacklist
$config['rest_ip_whitelist_enabled'] = FALSE;
$config['rest_ip_whitelist']         = '';
$config['rest_ip_blacklist_enabled'] = FALSE;
$config['rest_ip_blacklist']         = '';

// CORS settings
$config['rest_enable_cors']            = TRUE;
$config['rest_allow_any_cors_domain']  = TRUE;
$config['rest_allowed_cors_origins']   = array();
$config['rest_allowed_cors_headers']   = array(
    'Origin',
    'X-Requested-With',
    'Content-Type',
    'Accept',
    'Authorization',
);
$config['rest_allowed_cors_methods']   = array(
    'GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS',
);
$config['rest_force_https']            = FALSE;
