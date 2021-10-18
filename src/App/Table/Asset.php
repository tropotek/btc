<?php
namespace App\Table;

use Tk\Table\Cell;

/**
 * Example:
 * <code>
 *   $table = new Asset::create();
 *   $table->init();
 *   $list = ObjectMap::getObjectListing();
 *   $table->setList($list);
 *   $tableTemplate = $table->show();
 *   $template->appendTemplate($tableTemplate);
 * </code>
 *
 * @author Mick Mifsud
 * @created 2021-10-15
 * @link http://tropotek.com.au/
 * @license Copyright 2021 Tropotek
 */
class Asset extends \Bs\TableIface
{


    /**
     * @return $this
     * @throws \Exception
     */
    public function init()
    {

        $this->appendCell(new Cell\Checkbox('id'));
        $this->appendCell(new Cell\Text('marketId'))->setUrl($this->getEditUrl())->addCss('key')->addOnPropertyValue(function (\Tk\Table\Cell\Iface $cell, \App\Db\Asset $obj, $value) {
            if ($obj->getMarket()) {
                $value = sprintf('%s [%s]', $obj->getMarket()->getName(), $obj->getMarket()->getSymbol());
            }
            return $value;
        });
        $this->appendCell(new Cell\Text('currency'))->addCss('d-none d-lg-table-cell text-center')->addOnPropertyValue(function (\Tk\Table\Cell\Iface $cell, \App\Db\Asset $obj, $value) {
            $value = 'AUD';
            if ($obj->getMarket() && $obj->getMarket()->getExchange()) {
                $value = $obj->getMarket()->getExchange()->getCurrency();
            }
            return $value;
        });
        $this->appendCell(new Cell\Text('unitSell'))->addCss('d-none d-md-table-cell text-right')->addOnPropertyValue(function (\Tk\Table\Cell\Iface $cell, \App\Db\Asset $obj, $value) {
            $value = $obj->getMarketUnitValue(\App\Db\Asset::MARKET_BID);
            $value = '$' . number_format($value, 2);
            return $value;
        });
        $this->appendCell(new Cell\Text('unitBuy'))->addCss('d-none d-lg-table-cell text-right')->addOnPropertyValue(function (\Tk\Table\Cell\Iface $cell, \App\Db\Asset $obj, $value) {
            $value = $obj->getMarketUnitValue(\App\Db\Asset::MARKET_ASK);
            $value = '$' . number_format($value, 2);
            return $value;
        });
        $this->appendCell(new Cell\Text('units'))->addCss('text-right')->addOnPropertyValue(function (\Tk\Table\Cell\Iface $cell, \App\Db\Asset $obj, $value) {
            // TODO: have a global long flot format function
            $value = sprintf('%01.8f', $value);
            return $value;
        });
        $this->appendCell(new Cell\Text('unitValue'))->setLabel('$')->addCss('text-right')->addOnPropertyValue(function (\Tk\Table\Cell\Iface $cell, \App\Db\Asset $obj, $value) {
            $value = $obj->getMarketTotalValue(\App\Db\Asset::MARKET_BID);
            $value = '$' . number_format($value, 2);
            return $value;
        });

        $this->appendCell(new Cell\Text('spark'))->addCss('d-none d-md-table-cell text-right')->addOnCellHtml(function (\Tk\Table\Cell\Iface $cell, \App\Db\Asset $obj, $html) {
            $list = $obj->getAssetTotalHistory(65);
            $list = array_reverse($list);
            $vals = implode(',', $list);
            $html = sprintf('<span class="tk-graph" style="background: #EFEFEF;display: inline-block; padding: 3px;margin: 5px 25px;min-width: 200px;">%s</span>', $vals);
            return $html;
        });

        $template = $this->getRenderer()->getTemplate();

        $js = <<<JS
jQuery(function($) {
  $('.tk-graph').each(function () {
    $(this).sparkline('html', {height: '2.5em', enableTagOptions: true});
  });
})
JS;
        $template->appendJs($js);

        $this->appendCell(new Cell\Date('created'));

        // Actions
        $this->appendAction(\Tk\Table\Action\Delete::create());
        $this->appendAction(\Tk\Table\Action\Csv::create());

        return $this;
    }

    /**
     * @param array $filter
     * @param null|\Tk\Db\Tool $tool
     * @return \Tk\Db\Map\ArrayObject|\App\Db\Asset[]
     * @throws \Exception
     */
    public function findList($filter = array(), $tool = null)
    {
        if (!$tool) $tool = $this->getTool();
        try {
            $filter = array_merge($this->getFilterValues(), $filter);
            $list = \App\Db\AssetMap::create()->findFiltered($filter, $tool);
        } catch (\Tk\Db\Exception $e) {
            if (strstr($e->getMessage(), 'order clause') !== false) {
                $this->resetSessionTool();
                $this->getBackUrl()->redirect();
            } else throw $e;
        }
        return $list;
    }

}