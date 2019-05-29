<?php
/* 
 * NOTE: Be sure to add routes in correct order as the first match will win
 * 
 * Route Structure
 * $route = new Route(
 *     '/archive/{month}',              // path
 *     '\Namespace\Class::method',      // Callable or class::method string
 *     array('month' => 'Jan'),         // Params and defaults to path params... all will be sent to the request object.
 *     array('GET', 'POST', 'HEAD')     // methods
 * );
 */

$config = \App\Config::getInstance();
$routes = $config->getRouteCollection();
if (!$routes) return;

// Public Pages
$routes->add('home', new \Tk\Routing\Route('/index.html', 'App\Controller\Index::doDefault'));
$routes->add('home-base', new \Tk\Routing\Route('/', 'App\Controller\Index::doDefault'));

$routes->add('login', new \Tk\Routing\Route('/login.html', 'App\Controller\Login::doDefault'));
$routes->add('recover', new \Tk\Routing\Route('/recover.html', 'App\Controller\Recover::doDefault'));
$routes->add('register', new \Tk\Routing\Route('/register.html', 'App\Controller\Register::doDefault'));

$routes->add('contact', new \Tk\Routing\Route('/contact.html', 'App\Controller\Contact::doDefault'));
$routes->add('send', new \Tk\Routing\Route('/send.html', 'App\Controller\Send::doDefault'));

// Admin Pages
$routes->add('admin-dashboard', new \Tk\Routing\Route('/admin/index.html', 'App\Controller\Admin\Dashboard::doDefault'));
$routes->add('admin-dashboard-base', new \Tk\Routing\Route('/admin/', 'App\Controller\Admin\Dashboard::doDefault'));
$routes->add('admin-settings', new \Tk\Routing\Route('/admin/settings.html', 'App\Controller\Admin\Settings::doDefault'));

// User Pages
$routes->add('user-dashboard', new \Tk\Routing\Route('/user/index.html', 'App\Controller\User\Dashboard::doDefault'));
$routes->add('user-dashboard-base', new \Tk\Routing\Route('/user/', 'App\Controller\User\Dashboard::doDefault'));
