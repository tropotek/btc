<?php
namespace App\Listener;

use Tk\Event\Subscriber;
use Symfony\Component\HttpKernel\KernelEvents;
use Tk\Event\AuthEvent;
use Tk\Auth\AuthEvents;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class AuthHandler extends \Bs\Listener\AuthHandler
{

    /**
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
     * @throws \Exception
     */
    public function onRequest($event)
    {
        // if a user is in the session add them to the global config
        // Only the identity details should be in the auth session not the full user object, to save space and be secure.
        $config = \Bs\Config::getInstance();
        $auth = $config->getAuth();
        $user = null;                       // public user
        if ($auth->getIdentity()) {         // Check if user is logged in
            $user = $config->getUserMapper()->findByAuthIdentity($auth->getIdentity());
            if ($user && $user->isActive()) {
                // We set the user here for each page load
                $config->setUser($user);
            }
        }
        // ---------------- deprecated  ---------------------
        // The following is deprecated in preference of the validatePageAccess() method below
//        $role = $event->getRequest()->attributes->get('role');
//        vd($event->getRequest()->attributes);
//        // no role means page is publicly accessible
//        if (!$role || empty($role)) return;
//        if ($user) {
//            if (!$user->getRole()->hasType($role)) {
//                // Could redirect to a authentication error page.
//                \Tk\Alert::addWarning('You do not have access to the requested page.');
//                $config->getUserHomeUrl($user)->redirect();
//            }
//        } else {
//            $this->getLoginUrl()->redirect();
//        }
        //-----------------------------------------------------
    }


    /**
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
     * @throws \Exception
     */
    public function validatePageAccess($event)
    {
        $config = \Bs\Config::getInstance();

        // --------------------------------------------------------
        // Deprecated remove when role is no longer used as a route attribute
//        $role = $event->getRequest()->attributes->get('role');
//        if ($role) {
//            \Tk\Log::notice('Using legacy page permission system');
//            return;
//        }
        // --------------------------------------------------------

        $urlRole = \Bs\Uri::create()->getRoleType($config->getAvailableUserRoleTypes());
        //if ($urlRole && !$urlRole != 'public') {          // What happened here ?????
        if ($urlRole && $urlRole != 'public') {
            if (!$config->getAuthUser()) {  // if no user and the url has permissions set
                $this->getLoginUrl()->redirect();
            }
            $role = $config->getAuthUser()->getRoleType();
            if ($role != $urlRole) {   // Finally check if the use has access to the url
                \Tk\Alert::addWarning('1000: You do not have access to the requested page.');
                $config->getUserHomeUrl($config->getAuthUser())->redirect();
            }
        }
    }


}