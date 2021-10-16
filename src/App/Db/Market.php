<?php
namespace App\Db;

use App\Db\Traits\ExchangeTrait;
use Bs\Db\Traits\TimestampTrait;

/**
 * @author Mick Mifsud
 * @created 2021-10-15
 * @link http://tropotek.com.au/
 * @license Copyright 2021 Tropotek
 */
class Market extends \Tk\Db\Map\Model implements \Tk\ValidInterface
{
    use TimestampTrait;
    use ExchangeTrait;

    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $exchangeId = 0;

    /**
     * @var string
     */
    public $symbol = '';

    /**
     * @var string
     */
    public $name = '';

    /**
     * @var string
     */
    public $image = '';

    /**
     * @var string
     */
    public $notes = '';

    /**
     * @var bool
     */
    public $active = true;

    /**
     * @var \DateTime
     */
    public $modified = null;

    /**
     * @var \DateTime
     */
    public $created = null;


    /**
     * Market
     */
    public function __construct()
    {
        $this->_TimestampTrait();
    }

    /**
     * @param string $symbol
     * @return Market
     */
    public function setSymbol($symbol) : Market
    {
        $this->symbol = $symbol;
        return $this;
    }

    /**
     * @return string
     */
    public function getSymbol() : string
    {
        return $this->symbol;
    }

    /**
     * @param string $name
     * @return Market
     */
    public function setName($name) : Market
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $image
     * @return Market
     */
    public function setImage($image) : Market
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return string
     */
    public function getImage() : string
    {
        return $this->image;
    }

    /**
     * @param string $notes
     * @return Market
     */
    public function setNotes($notes) : Market
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * @return string
     */
    public function getNotes() : string
    {
        return $this->notes;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return Market
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * return the status list for a select field
     * @param null|string $currentId
     * @return array
     */
    public static function getSelectList($currentId = null)
    {
        $arr = MarketMap::create()->findFiltered(['active' => true]);
        $arr2 = array();
        foreach ($arr as $k => $obj) {
            $k = $obj->getName();
            if ($obj->getExchange())
                $k .= ' [' . $obj->getExchange()->getName() . ' - ' .$obj->getExchange()->getCurrency() . ']';
            if ($obj->getId() == $currentId) {
                if ($obj->getExchange())
                    $k .= ' (Current)';
            }
            $arr2[$k] = $obj->getId();
            $arr = $arr2;
        }
        return $arr;
    }

    /**
     * @return array
     */
    public function validate()
    {
        $errors = array();

        if (!$this->symbol) {
            $errors['symbol'] = 'Invalid value: symbol';
        }

        if (!$this->name) {
            $errors['name'] = 'Invalid value: name';
        }

        return $errors;
    }

}
