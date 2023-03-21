<?php
namespace App\Form;

use App\Db\MarketMap;
use Tk\Form\Field;
use Tk\Form\Event;
use Tk\Form;

/**
 * Example:
 * <code>
 *   $form = new Asset::create();
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
class Asset extends \Bs\FormIface
{

    /**
     * @throws \Exception
     */
    public function init()
    {

        //$list = MarketMap::create()->findFiltered(['active' => true]);
        $list = \App\Db\Market::getSelectList($this->getAsset()->getMarketId());
        $this->appendField(Field\Select::createSelect('marketId', $list)->prependOption('-- Select --'));
//        $list = CategoryMap::create()->findFiltered(['active' => true]);
//        $this->appendField(Field\Select::createSelect('categoryId', $list)->prependOption('-- Select --'));
        $this->appendField(new Field\Input('units'));
        $this->appendField(new Field\Checkbox('inTotal'))->setNotes('Include this asset in the totals calculations');
        $this->appendField(new Field\Textarea('notes'));

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
        $this->load(\App\Db\AssetMap::create()->unmapForm($this->getAsset()));
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
        \App\Db\AssetMap::create()->mapForm($form->getValues(), $this->getAsset());

        // Do Custom Validations

        $form->addFieldErrors($this->getAsset()->validate());
        if ($form->hasErrors()) {
            return;
        }

        $isNew = (bool)$this->getAsset()->getId();
        $this->getAsset()->save();

        // Do Custom data saving

        \Tk\Alert::addSuccess('Record saved!');
        $event->setRedirect($this->getBackUrl());
        if ($form->getTriggeredEvent()->getName() == 'save') {
            $event->setRedirect(\Tk\Uri::create()->set('assetId', $this->getAsset()->getId()));
        }
    }

    /**
     * @return \Tk\Db\ModelInterface|\App\Db\Asset
     */
    public function getAsset()
    {
        return $this->getModel();
    }

    /**
     * @param \App\Db\Asset $asset
     * @return $this
     */
    public function setAsset($asset)
    {
        return $this->setModel($asset);
    }

}