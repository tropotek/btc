<?php
namespace App;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2017 Michael Mifsud
 */
class Config extends \Bs\Config
{

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @throws \Exception
     */
    public function setupDispatcher($dispatcher)
    {
        \App\Dispatch::create($dispatcher);
    }

    /**
     * @return \Bs\Listener\PageTemplateHandler
     */
    public function getPageTemplateHandler()
    {
        if (!$this->get('page.template.handler')) {
            $this->set('page.template.handler', new \App\Listener\PageTemplateHandler());
        }
        return $this->get('page.template.handler');
    }

    /**
     * @param \Tk\Table $table
     * @return \Tk\Table\Renderer\Dom\Table
     */
    public function createTableRenderer($table)
    {
        $obj = \Tk\Table\Renderer\Dom\Table::create($table);
        $table->removeCss('table-bordered table-striped');
        return $obj;
    }




}