<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group  = 'default';
$query_builder = TRUE;

// Locate system CA bundle
$ca_bundle = getenv('SSL_CERT_FILE') ?: '/etc/ssl/certs/ca-certificates.crt';
if (!file_exists($ca_bundle)) {
    $ca_bundle = '/etc/ssl/cert.pem';
}

// TEMPORARY VERIFICATION: Uncomment the 2 lines below to check env vars are being read.
// Visit your site, confirm the values print, then comment them out again.
var_dump(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_NAME'));
// exit;

$db['default'] = array(
    'hostname'   => getenv('DB_HOST'),
    'username'   => getenv('DB_USER'),
    'password'   => getenv('DB_PASS'),
    'database'   => getenv('DB_NAME'),
    'port'       => 3306,
    'dbdriver'   => 'mysqli',
    'dbprefix'   => '',
    'pconnect'   => FALSE,
    'db_debug'   => (ENVIRONMENT !== 'production'),
    'cache_on'   => FALSE,
    'cachedir'   => '',
    'char_set'   => 'utf8mb4',
    'dbcollat'   => 'utf8mb4_unicode_ci',
    'swap_pre'   => '',
    'encrypt'    => array(
        'ssl_verify' => FALSE,
        'ssl_ca'     => $ca_bundle,
    ),
    'compress'     => FALSE,
    'stricton'     => FALSE,
    'failover'     => array(),
    'save_queries' => FALSE,
);
