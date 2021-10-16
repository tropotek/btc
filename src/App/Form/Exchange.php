<?php
namespace App\Form;

use Tk\Form\Field;
use Tk\Form\Event;
use Tk\Form;

/**
 * Example:
 * <code>
 *   $form = new Exchange::create();
 *   $form->setModel($obj);
 *   $formTemplate = $form->getRenderer()->show();
 *   $template->appendTemplate('form', $formTemplate);
 * </code>
 * 
 * @author Mick Mifsud
 * @created 2019-05-30
 * @link http://tropotek.com.au/
 * @license Copyright 2019 Tropotek
 */
class Exchange extends \Bs\FormIface
{

    /**
     * @throws \Exception
     */
    public function init()
    {
        $layout = $this->getRenderer()->getLayout();
        //$layout->setDefaultCol('col');
        $layout->removeRow('username', 'col');
        $layout->removeRow('secret', 'col');
        $layout->removeRow('icon', 'col');


        $list = \App\Db\Exchange::getDriverList();
        $this->appendField(new Field\Select('driver', $list))->setLabel('Exchange')->prependOption('-- Select --', '');
        $this->appendField(new Field\Input('username'));
        $this->appendField(new Field\Input('apiKey'));
        $this->appendField(new Field\Input('secret'));

        $list = array('AUD' => 'AUD', 'USD' => 'USD', 'BTC' => 'BTC');
        $this->appendField(new Field\Select('currency', $list))->prependOption('-- Select --', '');
        $this->appendField(new Field\Input('icon'));
        $this->appendField(new Field\Checkbox('active'))->setNotes('If active, the cron job will process this exchange.');
        $this->appendField(new Field\Textarea('description'));
        //$this->appendField(new Field\Textarea('options'));

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
        $this->load(\App\Db\ExchangeMap::create()->unmapForm($this->getExchange()));
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
        \App\Db\ExchangeMap::create()->mapForm($form->getValues(), $this->getExchange());

        // Do Custom Validations

        $form->addFieldErrors($this->getExchange()->validate());
        if ($form->hasErrors()) {
            return;
        }
        
        $isNew = (bool)$this->getExchange()->getId();
        $this->getExchange()->save();

        // Do Custom data saving

        \Tk\Alert::addSuccess('Record saved!');
        $event->setRedirect($this->getBackUrl());
        if ($form->getTriggeredEvent()->getName() == 'save') {
            $event->setRedirect(\Tk\Uri::create()->set('exchangeId', $this->getExchange()->getId()));
        }
    }

    /**
     * @return \Tk\Db\ModelInterface|\App\Db\Exchange
     */
    public function getExchange()
    {
        return $this->getModel();
    }

    /**
     * @param \App\Db\Exchange $exchange
     * @return $this
     */
    public function setExchange($exchange)
    {
        return $this->setModel($exchange);
    }
    
}