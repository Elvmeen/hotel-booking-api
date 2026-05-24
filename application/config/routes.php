<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'welcome';
$route['404_override']       = '';
$route['translate_uri_dashes'] = FALSE;

/* -------------------------------------------------------
 | Authentication
 ------------------------------------------------------- */
$route['api/auth/login']['POST']    = 'api/auth/login';
$route['api/auth/register']['POST'] = 'api/auth/register';
$route['api/auth/logout']['POST']   = 'api/auth/logout';
$route['api/auth/me']['GET']        = 'api/auth/me';

/* -------------------------------------------------------
 | Rooms
 ------------------------------------------------------- */
$route['api/rooms']['GET']              = 'api/rooms/index';
$route['api/rooms/(:num)']['GET']       = 'api/rooms/show/$1';
$route['api/rooms']['POST']             = 'api/rooms/create';
$route['api/rooms/(:num)']['PUT']       = 'api/rooms/update/$1';
$route['api/rooms/(:num)']['PATCH']     = 'api/rooms/update/$1';
$route['api/rooms/(:num)']['DELETE']    = 'api/rooms/delete/$1';
$route['api/rooms/available']['GET']    = 'api/rooms/available';

/* -------------------------------------------------------
 | Bookings
 ------------------------------------------------------- */
$route['api/bookings']['GET']           = 'api/bookings/index';
$route['api/bookings/(:num)']['GET']    = 'api/bookings/show/$1';
$route['api/bookings']['POST']          = 'api/bookings/create';
$route['api/bookings/(:num)']['PUT']    = 'api/bookings/update/$1';
$route['api/bookings/(:num)']['PATCH']  = 'api/bookings/update/$1';
$route['api/bookings/(:num)']['DELETE'] = 'api/bookings/delete/$1';

/* -------------------------------------------------------
 | Users (Admin)
 ------------------------------------------------------- */
$route['api/users']['GET']           = 'api/users/index';
$route['api/users/(:num)']['GET']    = 'api/users/show/$1';
$route['api/users/(:num)']['PUT']    = 'api/users/update/$1';
$route['api/users/(:num)']['PATCH']  = 'api/users/update/$1';
$route['api/users/(:num)']['DELETE'] = 'api/users/delete/$1';

/* -------------------------------------------------------
 | Customer Frontend
 ------------------------------------------------------- */
$route['rooms']              = 'frontend/rooms';
$route['room/(:num)']        = 'frontend/room/$1';
$route['login']              = 'frontend/login';
$route['register']           = 'frontend/register';
$route['dashboard']          = 'frontend/dashboard';

/* -------------------------------------------------------
 | Admin Dashboard (Web Interface)
 ------------------------------------------------------- */
$route['admin']                    = 'admin/dashboard';
$route['admin/dashboard']          = 'admin/dashboard';
$route['admin/rooms']              = 'admin/rooms';
$route['admin/bookings']           = 'admin/bookings';
$route['admin/users']              = 'admin/users';
$route['admin/login']              = 'admin/login';
$route['admin/logout']             = 'admin/logout_action';
$route['admin/login/do']['POST']   = 'admin/login_action';
