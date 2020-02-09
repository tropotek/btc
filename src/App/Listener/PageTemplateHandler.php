<?php
namespace App\Listener;

use Symfony\Component\HttpKernel\KernelEvents;
use Tk\Event\Subscriber;

/**
 * This object helps cleanup the structure of the controller code
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class PageTemplateHandler extends \Bs\Listener\PageTemplateHandler
{

    /**
     * @param \Tk\Event\Event $event
     * @throws \Exception
     */
    public function showPage(\Tk\Event\Event $event)
    {
        parent::showPage($event);

        $controller = \Tk\Event\Event::findControllerObject($event);
        if ($controller instanceof \Bs\Controller\Iface) {
            $page = $controller->getPage();
            if (!$page) return;
            $template = $page->getTemplate();
            /** @var \Bs\Db\User $user */
            $user = $controller->getAuthUser();

            if ($user) {
                if (\Bs\Uri::create()->getRoleType(\Tk\ObjectUtil::getClassConstants($this->getConfig()->createRole(), 'TYPE')) != '') {
                    // About dialog\Uni\Uri::create()
                    $dialog = new \Bs\Ui\AboutDialog();
                    $template->appendTemplate($template->getBodyElement(), $dialog->show());

                    // Logout dialog
                    $dialog = new \Bs\Ui\LogoutDialog();
                    $template->appendTemplate($template->getBodyElement(), $dialog->show());
                }

                // Set permission choices
                $perms = $user->getRole()->getPermissions();
                foreach ($perms as $perm) {
                    $template->show($perm);
                    $controller->getTemplate()->show($perm);
                }

                //show user icon 'user-image'
                $img = $user->getImageUrl();
                if ($img) {
                    $template->setAttr('user-image', 'src', $img);
                }
            }

            $template->insertText('login-title', $this->getConfig()->get('site.title'));

            // Add anything to the page template here ...

        }
    }


    /**
     * @return \App\Config|\Tk\Config
     */
    public function getConfig()
    {
        return \App\Config::getInstance();
    }

}