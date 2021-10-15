<?php
namespace App\Controller\Exchange;

use Dom\Template;
use Tk\Request;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Manager extends \Bs\Controller\AdminManagerIface
{



    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setPageTitle('Exchange Manager');
    }


    /**
     * @param Request $request
     * @param string $targetRole
     * @throws \Exception
     */
    public function doDefault(Request $request, $targetRole = 'user')
    {
        $this->table = \App\Table\Exchange::create()->init();
        $filter = [
            'userId' => $this->getConfig()->getAuthUser()->getId()
        ];
        $this->table->setList($this->table->findList($filter));

    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $this->getActionPanel()->append(\Tk\Ui\Link::createBtn('Add Exchange', \Bs\Uri::createHomeUrl('/exchangeEdit.html'), 'fa fa-building-o'));
        $template = parent::show();

        $template->appendTemplate('table', $this->table->show());
        
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
  <div class="tk-panel" data-panel-icon="fa fa-building-o" var="table"></div>
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }


}