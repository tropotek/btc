<?php
namespace App\Controller\Btc\Exchange;

use Tk\Request;
use Dom\Template;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Edit extends \Bs\Controller\AdminEditIface
{

    /**
     * @var \App\Db\Exchange
     */
    protected $exchange = null;


    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setPageTitle('Exchange Edit');
    }


    /**
     * @param \Tk\Request $request
     * @param string $targetRole
     * @throws \Exception
     */
    public function doDefault(\Tk\Request $request, $targetRole = 'user')
    {
        $this->init($request);


        $this->getForm()->execute();
    }

    /**
     * @param \Tk\Request $request
     * @throws \Exception
     */
    public function init($request)
    {
        $this->exchange = new \App\Db\Exchange();
        $this->exchange->userId = $this->getUser()->getId();
        if ($request->get('exchangeId')) {
            $this->exchange = \App\Db\ExchangeMap::create()->find($request->get('exchangeId'));
        }

        $this->setForm(\App\Form\Exchange::create()->setModel($this->exchange));
    }

    /**
     * @return Template
     * @throws \Exception
     */
    public function show()
    {
        $template = parent::show();
        
        // Render the form
        $template->appendTemplate('form', $this->form->show());

        if ($this->exchange->id)
            $template->setAttr('form', 'data-panel-title', $this->exchange->driver . ' - [ID ' . $this->exchange->getId() . ']');
        
        return $template;
    }


    /**
     * DomTemplate magic method
     *
     * @return Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div>

  <div class="tk-panel" data-panel-icon="fa fa-btc" var="form"></div>
    
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}