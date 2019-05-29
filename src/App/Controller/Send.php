<?php
namespace App\Controller;

use Tk\Request;
use Tk\Form;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Send extends \Bs\Controller\Iface
{

    /**
     * @var Form
     */
    protected $form = null;


    /**
     * @param Request $request
     * @throws \Exception
     */
    public function doDefault(Request $request)
    {
        $this->setPageTitle('Message sent');

        if ($request->getReferer()->getHost() != \Tk\Uri::create()->getHost()) {
            throw new \Tk\Exception('Unknown server error.');
        }
        
        $this->config = \Tk\Config::getInstance();

        if ($request->has('send')) {
            $this->doSend($request);
            $this->redirect();
        }


    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function doSend(Request $request)
    {
        //vd($_REQUEST);
        $siteEmail = $this->getConfig()->get('site.email');
        $email = $request->get('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !filter_var($siteEmail, FILTER_VALIDATE_EMAIL)) {
            \Tk\Alert::addWarning('Message unsent, invalid email address.');
            return;
        }

        $message = $this->getConfig()->createMessage($request->get('__template'));
        $message->addTo($email);
        $message->setFrom($siteEmail);
        $message->setSubject('Safe Soda - Contact Us Email');
        if ($request->has('__subject')) {
            $message->setSubject($request->get('__subject'));
        }
        foreach ($request->all() as $k => $v) {
            if ($k == 'send' || $k[0] == '__') continue;
            if (is_array($v)) {
                $v = implode(', ', $v);
            }
            $message->set($k, $v);
        }
        // Send message to client
        $this->getConfig()->getEmailGateway()->send($message);

        // TODO: Handle files and attach them to the message.


        // Send message to site admin
        $message->reset();
        $message->addTo($siteEmail);
        $message->setFrom($email);
        $message->setSubject($message->getSubject() . ' [Admin]');
        $this->getConfig()->getEmailGateway()->send($message);

        \Tk\Alert::addSuccess('Your form has been sent successfully.');
    }

    /**
     *
     */
    private function redirect()
    {
        if ($this->getRequest()->getReferer())
            $this->getRequest()->getReferer()->redirect();
        \Tk\Uri::create()->redirect();
    }

    /**
     * @return \Dom\Template
     * @throws \Exception
     */
    public function show()
    {
        $template = parent::show();


        return $template;
    }
    
}