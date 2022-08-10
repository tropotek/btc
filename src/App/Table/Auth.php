<?php
namespace App\Table;

use App\Db\AuthMap;
use Bs\Uri;
use OTPHP\TOTP;
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
        if ($this->getRequest()->has('d')) {
            $this->doAuthtool2($this->getRequest());
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
                    $html = sprintf('<button class="btn btn-sm btn-success authtool" data-auth-id="%s"><i class="fa fa-refresh"></i></button> <strong class="authcode2">------</strong>',
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
        $this->appendAction(\Tk\Table\Action\Delete::create());

        $js = <<<JS

jQuery(function($) {
  function copyToClipboard(el) {
    if(navigator.clipboard) {
        let text = $(el).text();
        console.log(text);
        navigator.clipboard.writeText(text)
    } else {
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
        sel.addRange(range);
    }
  }
  
  $('button.authtool').each(function () {
    var btn = $(this);
    btn.on('click', function (e) {
      //var params = {'d': btn.data('authId'), 'nolog': 'nolog'};
      var params = {'d': btn.data('authId')};
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

    public function doAuthtool2(Request $request)
    {
        /** @var \App\Db\Auth $auth */
        $auth = AuthMap::create()->find($request->get('d'));
        if ($auth) {
            $key = str_replace('oathtool --totp -b ', '', $auth->getAuthtool());
            try {
                $otp = TOTP::create($key);
                $code = $otp->now();
            } catch (\Exception $e) {
                \Tk\Log::error($e->__toString());
                \Tk\ResponseJson::createJson(['status' => 'err', 'msg' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR)->send();
                exit();
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