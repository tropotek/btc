<?php
namespace App\Controller;

use Tk\Form\Field;
use Tk\Form\Event;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Register extends \Bs\Controller\Register
{

    /**
     * @throws \Exception
     */
    public function init()
    {
        parent::init();

        $this->form = $this->getConfig()->createForm('register-account');
        $this->form->setRenderer($this->getConfig()->createFormRenderer($this->form));
        $this->form->getRenderer()->setFieldGroupRenderer(null);
        $this->form->removeCss('form-horizontal');

        /** @var \Tk\Form\Field\InputGroup $f */
        $f = $this->form->appendField(Field\InputGroup::create('name'))->setLabel(null)->setAttr('placeholder', 'Full Name');
        $f->prepend('<span class="input-group-text input-group-addon"><i class="fa fa-user mx-auto"></i></span>');

        $f = $this->form->appendField(Field\InputGroup::create('email'))->setLabel(null)->setAttr('placeholder', 'Email Address');
        $f->prepend('<span class="input-group-text input-group-addon"><i class="fa fa-envelope mx-auto"></i></span>');

        $f = $this->form->appendField(Field\InputGroup::create('password'))->setType('password')->setLabel(null)->setAttr('placeholder', 'Enter Password');
        $f->prepend('<span class="input-group-text input-group-addon"><i class="fa fa-lock mx-auto"></i></span>');

        $f = $this->form->appendField(Field\InputGroup::create('passwordConf'))->setType('password')->setLabel(null)->setAttr('placeholder', 'Re-Enter Password');
        $f->prepend('<span class="input-group-text input-group-addon"><i class="fa fa-unlock-alt mx-auto"></i></span>');

        $f = $this->form->appendField(Field\Checkbox::create('accept'))->setCheckboxLabel('I agree to the following <a href="/terms.html" target="_blank">terms and conditions</a>');

        $this->form->appendField(new Event\Submit('register', array($this, 'doRegister')))->removeCss('btn-default')->addCss('btn btn-lg btn-success btn-ss col-12');

    }


    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = parent::show();


        return $template;
    }


    /**
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div class="tk-login-panel tk-recover">
  
  <div var="form"></div>

</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }
}