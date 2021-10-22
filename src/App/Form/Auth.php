<?php
namespace App\Form;

use Tk\Form\Field;
use Tk\Form\Event;
use Tk\Form;

/**
 * Example:
 * <code>
 *   $form = new Auth::create();
 *   $form->setModel($obj);
 *   $formTemplate = $form->getRenderer()->show();
 *   $template->appendTemplate('form', $formTemplate);
 * </code>
 *
 * @author Mick Mifsud
 * @created 2021-10-22
 * @link http://tropotek.com.au/
 * @license Copyright 2021 Tropotek
 */
class Auth extends \Bs\FormIface
{

    /**
     * @throws \Exception
     */
    public function init()
    {
        $mediaPath = 'Auth/'.$this->getAuthUser()->getId();

        $this->appendField(new Field\Input('name'));
        $this->appendField(new Field\Input('url'));
        $this->appendField(new Field\Input('username'));
        //$this->appendField(new Field\Input('password'));
        $this->appendField(new Field\Input('authtool'));
        $this->appendField(new Field\Textarea('keys'))
            ->addCss('mce-min')->setAttr('data-elfinder-path', $mediaPath);
        $this->appendField(new Field\Textarea('notes'))
            ->addCss('mce-min')->setAttr('data-elfinder-path', $mediaPath);

        $this->appendField(new Event\Submit('update', array($this, 'doSubmit')));
        $this->appendField(new Event\Submit('save', array($this, 'doSubmit')));
        $this->appendField(new Event\Link('cancel', $this->getBackUrl()));

    }

    /**
     * @param \Tk\Request $request
     * @throws \Exception
     */
    public function execute($request = null)
    {
        $this->load(\App\Db\AuthMap::create()->unmapForm($this->getAuth()));
        parent::execute($request);
    }

    /**
     * @param Form $form
     * @param Event\Iface $event
     * @throws \Exception
     */
    public function doSubmit($form, $event)
    {
        // Load the object with form data
        \App\Db\AuthMap::create()->mapForm($form->getValues(), $this->getAuth());

        // Do Custom Validations

        $form->addFieldErrors($this->getAuth()->validate());
        if ($form->hasErrors()) {
            return;
        }

        $isNew = (bool)$this->getAuth()->getId();
        $this->getAuth()->save();

        // Do Custom data saving

        \Tk\Alert::addSuccess('Record saved!');
        $event->setRedirect($this->getBackUrl());
        if ($form->getTriggeredEvent()->getName() == 'save') {
            $event->setRedirect(\Tk\Uri::create()->set('authId', $this->getAuth()->getId()));
        }
    }

    /**
     * @return \Tk\Db\ModelInterface|\App\Db\Auth
     */
    public function getAuth()
    {
        return $this->getModel();
    }

    /**
     * @param \App\Db\Auth $auth
     * @return $this
     */
    public function setAuth($auth)
    {
        return $this->setModel($auth);
    }

}