<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Allsecureexchange\Allsecureexchange\Helper;

use Allsecureexchange\Allsecureexchange\Model\OrderFactory as AllsecurePayOrder;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\ResourceConnection $resource
     */
    public $resource = '';
    
    /**
     * @var Allsecureexchange\Allsecureexchange\Model\OrderFactory $allsecurepayOrder
     */
    protected $allsecurepayOrder;
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param AllsecurePayOrder $allsecurepayOrder
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        AllsecurePayOrder $allsecurepayOrder
    ) {
        parent::__construct($context);
        $this->resource = $resource;
        $this->allsecurepayOrder = $allsecurepayOrder;
    }
    
    /**
     * Get Transaction By Order Id
     *
     * @param string $order_id
     * 
     * return string
     */
    public function getTransactionByOrderId($order_id)
    {
        $model = $this->allsecurepayOrder->create();
        $model = $model->load($order_id, 'order_id');
        
        if ($model && $model->getId() > 0) {
            $row = [
                'id' => $model->getId(),
                'order_id' => $model->getOrderId(),
                'response' => $model->getResponse(),
            ];
            return $row;
        } else {
            return false;
        }
    }
    
    /**
     * Update Transaction
     *
     * @param string $order_id
     * @param string $column
     * @param string $value
     * 
     * return void
     */
    public function updateTransaction($order_id, $column, $value)
    {
        $connection = $this->resource->getConnection();
        $tableName = $this->resource->getTableName('allsecureexchange_order');
        
        $transaction = $this->getTransactionByOrderId($order_id);
        if ($transaction) {
            $connection->update(
                $tableName,
                [$column => $value],
                "order_id=".$order_id
            );
        } else {
            $connection->insert(
                $tableName,
                [$column => $value, 'order_id' => $order_id]
            );
        }
    }
    
    /**
     * Update Invoice
     *
     * @param string $invoice_id
     * @param string $column
     * @param string $value
     * 
     * return void
     */
    public function updateInvoice($invoice_id, $column, $value)
    {
        $connection = $this->resource->getConnection();
        $tableName = $this->resource->getTableName('sales_invoice');

        $connection->update(
            $tableName,
            [$column => $value],
            "entity_id=".$invoice_id
        );
    }
    
    /**
     * Get transaction single record
     *
     * @param string $order_id
     * @param string $column
     * 
     * return string
     */
    public function getTransactionSingle($order_id, $column)
    {
        $connection= $this->resource->getConnection();
        $table = $this->resource->getTableName('allsecureexchange_order');
        $sql = $connection->select()
                  ->from($table, [$column])
                  ->where('order_id = ?', (int)($order_id));
        return $connection->fetchOne($sql);
    }

    /**
     * Get transaction response value
     *
     * @param string $order_id
     * @param string $key
     * 
     * return string
     */
    public function getTransactionResponseSingle($order_id, $key)
    {
        $response = $this->getTransactionSingle($order_id ,'response');
        if ($response) {
            $result = json_decode($response, true);
            if (isset($result[$key])) {
                return $result[$key];
            }
        }
        return false;
    }

    /**
     * Update transaction response value
     *
     * @param string $order_id
     * @param string $param
     * @param string $value
     * 
     * return void
     */
    public function updateTransactionResponse($order_id, $param, $value)
    {
        $metaData = $this->getTransactionSingle($order_id ,'response');
        if (!empty($metaData)) {
            $metaData = json_decode($metaData, true);
            $metaData[$param] = $value;
            $paymentData = json_encode($metaData);
            
            $connection = $this->resource->getConnection();
            $table = $this->resource->getTableName('allsecureexchange_order');
            $where = ['order_id = (?)' => (int)($order_id)];
            $connection->update($table, ['response' => $paymentData], $where);
        }
    }

    /**
     * Unset transaction response key
     *
     * @param string $order_id
     * @param string $param
     * 
     * return void
     */
    public function deleteTransactionResponse($order_id, $param)
    {
        $metaData = $this->getTransactionSingle($order_id ,'response');
        if (!empty($metaData)) {
            $metaData = json_decode($metaData, true);
            if (isset($metaData[$param])) {
                unset($metaData[$param]);
            }
            $paymentData = json_encode($metaData);
            
            $connection = $this->resource->getConnection();
            $table = $this->resource->getTableName('allsecureexchange_order');
            $where = ['order_id = (?)' => (int)($order_id)];
            $connection->update($table, ['response' => $paymentData], $where);
        }
    }
    
    /**
     * Get Encoded Order Id
     *
     * @param string $orderId
     * return string
     */
    public function encodeOrderId($orderId)
    {
        return $orderId . '-' . date('YmdHis') . substr(sha1(uniqid()), 0, 10);
    }

    /**
     * Get Decoded Order Id
     *
     * @param string $orderId
     * return string
     */
    public function decodeOrderId($orderId)
    {
        if (strpos($orderId, '-') === false) {
            return $orderId;
        }

        $orderIdParts = explode('-', $orderId);

        if(count($orderIdParts) === 2) {
            $orderId = $orderIdParts[0];
        }

        if(count($orderIdParts) === 3) {
            $orderId = $orderIdParts[1];
        }

        return $orderId;
    }
}
