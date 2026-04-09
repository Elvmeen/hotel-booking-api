<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
| Leave blank — CodeIgniter auto-detects the URL.
| This works correctly on Render, localhost, and any other host.
*/
$config['base_url'] = '';

$config['index_page'] = '';

$config['uri_protocol'] = 'REQUEST_URI';
$config['url_suffix'] = '';
$config['language'] = 'english';
$config['charset'] = 'UTF-8';
$config['enable_hooks'] = FALSE;
$config['subclass_prefix'] = 'MY_';
$config['composer_autoload'] = FCPATH . 'vendor/autoload.php';
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';
$config['allow_get_array'] = TRUE;
$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd';
$config['log_errors'] = TRUE;
$config['log_threshold'] = (ENVIRONMENT === 'development') ? 4 : 1;
$config['log_path'] = APPPATH . 'logs/';
$config['log_file_extension'] = '';
$config['log_file_permissions'] = 0644;
$config['log_date_format'] = 'Y-m-d H:i:s';
$config['error_views_path'] = '';
$config['cache_path'] = APPPATH . 'cache/';
$config['cache_query_string'] = FALSE;
$config['encryption_key'] = getenv('ENCRYPTION_KEY') ?: 'CHANGE_THIS_32_CHAR_KEY_IN_PROD12';
$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'hotel_session';
$config['sess_expiration'] = 7200;
$config['sess_save_path'] = sys_get_temp_dir();
$config['sess_match_ip'] = FALSE;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = FALSE;
$config['cookie_prefix'] = 'hotel_';
$config['cookie_domain'] = '';
$config['cookie_path'] = '/';
$config['cookie_secure'] = (ENVIRONMENT === 'production');
$config['cookie_httponly'] = FALSE;
$config['standardize_newlines'] = FALSE;
$config['global_xss_filtering'] = FALSE;
$config['csrf_protection'] = FALSE;
$config['csrf_token_name'] = 'csrf_token';
$config['csrf_cookie_name'] = 'csrf_cookie';
$config['csrf_expire'] = 7200;
$config['csrf_regenerate'] = TRUE;
$config['csrf_exclude_uris'] = array('api/auth/login', 'api/auth/register');
$config['compress_output'] = FALSE;
$config['time_reference'] = 'local';
$config['rewrite_short_tags'] = FALSE;
$config['proxy_ips'] = '';

/*
|--------------------------------------------------------------------------
| JWT Configuration
|--------------------------------------------------------------------------
*/
$config['jwt_secret_key'] = getenv('JWT_SECRET') ?: 'CHANGE_THIS_JWT_SECRET_IN_PRODUCTION';
$config['jwt_expire']     = 3600 * 24; // 24 hours

/*
|--------------------------------------------------------------------------
| Application Settings
|--------------------------------------------------------------------------
*/
$config['app_name']    = 'Hotel Booking API';
$config['app_version'] = '1.0.0';
