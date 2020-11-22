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

use Tk\Routing\Route;

$config = \App\Config::getInstance();
$routes = $config->getRouteCollection();
if (!$routes) return;

// Public Pages
$routes->add('home', new Route('/index.html', 'App\Controller\Index::doDefault'));
$routes->add('home-base', new Route('/', 'App\Controller\Index::doDefault'));

$routes->add('login', new Route('/login.html', 'App\Controller\Login::doDefault'));
$routes->add('recover', new Route('/recover.html', 'App\Controller\Recover::doDefault'));
$routes->add('register', new Route('/register.html', 'App\Controller\Register::doDefault'));

$routes->add('contact', new Route('/contact.html', 'App\Controller\Contact::doDefault'));
$routes->add('send', new Route('/send.html', 'App\Controller\Send::doDefault'));

// Admin Pages
$routes->add('admin-dashboard', new Route('/admin/index.html', 'App\Controller\Admin\Dashboard::doDefault'));
$routes->add('admin-dashboard-base', new Route('/admin/', 'App\Controller\Admin\Dashboard::doDefault'));
$routes->add('admin-user-profile', new Route('/admin/profile.html', 'App\Controller\Admin\User\Profile::doDefault'));
$routes->add('admin-settings', new Route('/admin/settings.html', 'App\Controller\Admin\Settings::doDefault'));


// User Pages
$routes->add('member-dashboard', new Route('/member/index.html', 'App\Controller\Member\Dashboard::doDefault'));
$routes->add('member-dashboard-base', new Route('/member/', 'App\Controller\Member\Dashboard::doDefault'));
$routes->add('member-profile', new Route('/member/profile.html', 'App\Controller\Admin\User\Profile::doDefault'));



$routes->add('btc-exchange-manager', new Route('/{role}/exchangeManager.html', 'App\Controller\Btc\Exchange\Manager::doDefault'));
$routes->add('btc-exchange-edit', new Route('/{role}/exchangeEdit.html', 'App\Controller\Btc\Exchange\Edit::doDefault'));
$routes->add('btc-account', new Route('/{role}/{exchange}/account.html', 'App\Controller\Btc\Account::doDefault'));
$routes->add('btc-summary', new Route('/{role}/{exchange}/summary.html', 'App\Controller\Btc\Summary::doDefault'));

