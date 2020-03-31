<?php

namespace allsecureexchange\Client\Transaction\Base;
use allsecureexchange\Client\Data\Item;

/**
 * Interface ItemsInterface
 *
 * @package allsecureexchange\Client\Transaction\Base
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
