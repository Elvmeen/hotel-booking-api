<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS — PlanetScale (MySQL-compatible)
|--------------------------------------------------------------------------
| All credentials come from environment variables.
| Set DB_HOST, DB_USER, DB_PASS, DB_NAME in Render (production)
| or Replit Secrets (development) — never hardcode them here.
|
| PlanetScale requires TLS. CI3's mysqli driver only enables SSL when
| 'encrypt' is an ARRAY (a boolean TRUE is silently ignored).
*/
$active_group  = 'default';
$query_builder = TRUE;

// Locate system CA bundle (Debian/Ubuntu/Nix all keep it here)
$ca_bundle = getenv('SSL_CERT_FILE') ?: '/etc/ssl/certs/ca-certificates.crt';
if (!file_exists($ca_bundle)) {
    $ca_bundle = '/etc/ssl/cert.pem';   // macOS / Alpine fallback
}

$db['default'] = array(
    'dsn' => 'mysqli:host=aws.connect.psdb.cloud;dbname=hotel_booking;charset=utf8mb4',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on'  => FALSE,
    'cachedir'  => '',
    'char_set'  => 'utf8mb4',
    'dbcollat'  => 'utf8mb4_unicode_ci',
    'swap_pre'  => '',

    /*
    | CI3's mysqli driver only enables SSL when 'encrypt' is an ARRAY.
    | Supplying ssl_ca calls ssl_set() and adds MYSQLI_CLIENT_SSL.
    | Required for PlanetScale (and any SSL-only MySQL host).
    */
    'encrypt' => array(
        'ssl_verify' => FALSE,
        'ssl_ca'     => $ca_bundle,
    ),

    'compress'     => FALSE,
    'stricton'     => FALSE,
    'failover'     => array(),
    'save_queries' => FALSE,
);
