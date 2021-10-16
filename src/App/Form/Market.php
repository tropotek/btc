<?php
namespace App\Form;

use App\Db\ExchangeMap;
use Tk\Form\Field;
use Tk\Form\Event;
use Tk\Form;

/**
 * Example:
 * <code>
 *   $form = new Market::create();
 *   $form->setModel($obj);
 *   $formTemplate = $form->getRenderer()->show();
 *   $template->appendTemplate('form', $formTemplate);
 * </code>
 *
 * @author Mick Mifsud
 * @created 2021-10-15
 * @link http://tropotek.com.au/
 * @license Copyright 2021 Tropotek
 */
class Market extends \Bs\FormIface
{

    /**
     * @throws \Exception
     */
    public function init()
    {
        $layout = $this->getRenderer()->getLayout();
        $layout->removeRow('symbol', 'col');
        $layout->removeRow('exchangeId', 'col');

        $this->appendField(new Field\Input('name'));
        $this->appendField(new Field\Input('symbol'));
        //$list = ExchangeMap::create()->findFiltered(['active' => true]);
        $list = \App\Db\Exchange::getSelectList($this->getMarket()->getExchangeId());
        $this->appendField(Field\Select::createSelect('exchangeId', $list));
        //$this->appendField(new Field\Input('image'));
        //$this->appendField(new Field\Textarea('notes'));

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
        $this->load(\App\Db\MarketMap::create()->unmapForm($this->getMarket()));
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
        \App\Db\MarketMap::create()->mapForm($form->getValues(), $this->getMarket());

        // Do Custom Validations

        $form->addFieldErrors($this->getMarket()->validate());
        if ($form->hasErrors()) {
            return;
        }

        $isNew = (bool)$this->getMarket()->getId();
        $this->getMarket()->save();

        // Do Custom data saving

        \Tk\Alert::addSuccess('Record saved!');
        $event->setRedirect($this->getBackUrl());
        if ($form->getTriggeredEvent()->getName() == 'save') {
            $event->setRedirect(\Tk\Uri::create()->set('marketId', $this->getMarket()->getId()));
        }
    }

    /**
     * @return \Tk\Db\ModelInterface|\App\Db\Market
     */
    public function getMarket()
    {
        return $this->getModel();
    }

    /**
     * @param \App\Db\Market $market
     * @return $this
     */
    public function setMarket($market)
    {
        return $this->setModel($market);
    }

}