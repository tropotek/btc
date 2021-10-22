<?php
namespace App\Table;

use App\Db\AuthMap;
use Bs\Uri;
use Tk\Form\Field;
use Tk\Request;
use Tk\Response;
use Tk\Table\Cell;

/**
 * Example:
 * <code>
 *   $table = new Auth::create();
 *   $table->init();
 *   $list = ObjectMap::getObjectListing();
 *   $table->setList($list);
 *   $tableTemplate = $table->show();
 *   $template->appendTemplate($tableTemplate);
 * </code>
 *
 * @author Mick Mifsud
 * @created 2021-10-22
 * @link http://tropotek.com.au/
 * @license Copyright 2021 Tropotek
 */
class Auth extends \Bs\TableIface
{

    /**
     * @return $this
     * @throws \Exception
     */
    public function init()
    {
        if ($this->getRequest()->has('c')) {
            $this->doAuthtool($this->getRequest());
        }

        $this->appendCell(new Cell\Checkbox('id'));

        $this->getActionCell()->addButton(Cell\ActionButton::create('Edit ', $this->getEditUrl(), 'fa fa-globe'))
            ->addOnShow(function ($cell, \App\Db\Auth $obj, Cell\ActionButton $button) {
                if ($obj->getUrl()) {
                    $button->setUrl(Uri::create($obj->getUrl()));
                    $button->setAttr('target', '_blank');
                } else{
                    $button->setVisible(false);
                }
            });
        $this->appendCell($this->getActionCell());


        $this->appendCell(new Cell\Text('authtool'))
            ->addOnPropertyValue(function (\Tk\Table\Cell\Iface $cell, \App\Db\Auth $obj, $value) {
                $value = '';
                return $value;
            })->addOnCellHtml(function (\Tk\Table\Cell\Iface $cell, \App\Db\Auth $obj, $html) {
                if ($obj->getAuthtool()) {
                    $html = sprintf('<button class="btn btn-sm btn-success authtool" data-auth-id="%s"><i class="fa fa-refresh"></i></button> <strong class="authcode">------</strong>',
                        $obj->getId());
                }
                return $html;
            });
        $this->appendCell(new Cell\Text('name'))->addCss('key')->setUrl($this->getEditUrl());
        //$this->appendCell(new Cell\Text('url'));  // Make a button
        $this->appendCell(new Cell\Text('username'));
        $this->appendCell(new Cell\Text('password'));

        $this->appendCell(new Cell\Date('modified'));
        $this->appendCell(new Cell\Date('created'));

        // Filters
        $this->appendFilter(new Field\Input('keywords'))->setAttr('placeholder', 'Search');

        // Actions
        //$this->appendAction(\Tk\Table\Action\Link::createLink('New Auth', \Bs\Uri::createHomeUrl('/authEdit.html'), 'fa fa-plus'));
        //$this->appendAction(\Tk\Table\Action\ColumnSelect::create()->setUnselected(array('modified', 'created')));
        $this->appendAction(\Tk\Table\Action\Delete::create());
        //$this->appendAction(\Tk\Table\Action\Csv::create());

        // load table
        //$this->setList($this->findList());

        $js = <<<JS

jQuery(function($) {
  function copyToClipboard(el) {
    var range = document.createRange();
    range.selectNode(el);
    window.getSelection().removeAllRanges();
    window.getSelection().addRange(range);
    document.execCommand("copy");
    window.getSelection().removeAllRanges();
    
    // Select the text
    range = document.createRange();
    range.selectNodeContents(el);
    var sel = window.getSelection();
    sel.removeAllRanges();
    sel.addRange(range);s
  }
  
  $('button.authtool').each(function () {
    var btn = $(this);
    btn.on('click', function (e) {
      var params = {'c': btn.data('authId'), 'nolog': 'nolog'};
      $.post(document.location, params, function (data) {
        btn.next().text(data.code);
        var txt = btn.next().get(0);
        copyToClipboard(txt);
      });
      return false;
    });
  });
});
JS;
        $this->getRenderer()->getTemplate()->appendJs($js);


        return $this;
    }

    public function doAuthtool(Request $request)
    {
        /** @var \App\Db\Auth $auth */
        $auth = AuthMap::create()->find($request->get('c'));
        if ($auth) {
            $code = '------';
            $str = trim(exec($auth->getAuthtool()));
            if (preg_match('/^[0-9]{6}$/', $str)) {
                $code = $str;
            }
            \Tk\ResponseJson::createJson(['status' => 'ok', 'code' => $code])->send();
            exit();
        }
        \Tk\ResponseJson::createJson(['status' => 'err', 'msg' => 'Invalid Auth ID'], Response::HTTP_INTERNAL_SERVER_ERROR)->send();
        exit();
    }


    /**
     * @param array $filter
     * @param null|\Tk\Db\Tool $tool
     * @return \Tk\Db\Map\ArrayObject|\App\Db\Auth[]
     * @throws \Exception
     */
    public function findList($filter = array(), $tool = null)
    {
        if (!$tool) $tool = $this->getTool();
        $filter = array_merge($this->getFilterValues(), $filter);
        $list = \App\Db\AuthMap::create()->findFiltered($filter, $tool);
        return $list;
    }

}