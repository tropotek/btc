<?php
namespace App\Controller\Btc;

use Tk\Request;
use Dom\Template;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Account extends \Bs\Controller\AdminIface
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
        $this->setPageTitle('Exchange Account');
    }


    /**
     * @param \Tk\Request $request
     * @param string $exchange
     * @throws \Exception
     */
    public function doDefault(\Tk\Request $request, $exchange)
    {
        $this->exchange = \App\Db\ExchangeMap::create()->findFiltered(array('driver' => $exchange, 'userId' => $this->getConfig()->getUser()->getId()))->current();
        if (!$this->exchange) {
            \Tk\Alert::addError('Exchange not found!');
            \Tk\Uri::create()->redirect();
        }

        $this->setPageTitle($this->exchange->getName() . ' Exchange Account');






        

    }

    /**
     * @return Template
     * @throws \Exception
     */
    public function show()
    {
        $template = parent::show();

        $template->setAttr('form', 'data-panel-icon', $this->exchange->icon);
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