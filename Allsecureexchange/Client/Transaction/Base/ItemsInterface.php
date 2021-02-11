<?php

namespace Allsecureexchange\Client\Transaction\Base;
use Allsecureexchange\Client\Data\Item;

/**
 * Interface ItemsInterface
 *
 * @package Allsecureexchange\Client\Transaction\Base
 */
interface ItemsInterface {

    /**
     * @param Item[] $items
     * @return void
     */
    public function setItems($items);

    /**
     * @return Item[]
     */
    public function getItems();

    /**
     * @param Item $item
     * @return void
     */
    public function addItem($item);

}
