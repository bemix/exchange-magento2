<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Allsecureexchange\Allsecureexchange\Controller\Additional;

require_once __DIR__.'/../../Services/initClientAutoload.php';

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;

use Exchange\Client\Client as AllsecureClient;
use Exchange\Client\Callback\Result as AllsecureResult;

class Webhook extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    /**
     * @var \Allsecureexchange\Allsecureexchange\Helper\Data $helper
     */
    protected $helper;
    
    /**
     * @var \Allsecureexchange\Allsecureexchange\Model\PayAbstract $payment
     */
    protected $payment;
    
    /**
     * @var \Magento\Sales\Model\OrderFactory $orderFactory
     */
    protected $orderFactory;
    
    /**
     * @var \Magento\Framework\Filesystem\Driver\File $fileDriver
     */
    protected $fileDriver;
    
    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\OrderSender
     */
    protected $orderSender;

    /**
     * Constructor
     *
     * @param \Allsecureexchange\Allsecureexchange\Helper\Data $helper
     * @param \Allsecureexchange\Allsecureexchange\Model\PayAbstract $payment
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver,
     * @param \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Allsecureexchange\Allsecureexchange\Helper\Data $helper,
        \Allsecureexchange\Allsecureexchange\Model\PayAbstract $payment,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->helper = $helper;
        $this->payment = $payment;
        $this->orderFactory = $orderFactory;
        $this->fileDriver = $fileDriver;
        $this->orderSender = $orderSender;
        parent::__construct($context);
    }
    
    /**
     * CreateCsrfValidationException
     *
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return void
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }
    
    /**
     * ValidateForCsrf
     *
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return void
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * Executor
     *
     * @return void
     */
    public function execute()
    {
        $model = $this->payment;
        $model->log('Webhook Triggered');

        try {
            $client = $model->getClient();
            
            if (!$client->validateCallbackWithGlobals()) {
                $message = __('Callback validation failed.');
                throw new \Magento\Framework\Exception\LocalizedException($message);
            }
            
            $callbackResult = $client->readCallback(file_get_contents('php://input'));
            $model->log((array)($callbackResult));
            $params = $this->getRequest()->getParams();
            if (isset($params['order_id']) && !empty($params['order_id'])) {
                $orderId = trim($params['order_id']);
                $order = $this->orderFactory->create()->loadByIncrementId($orderId);
                if ($order && $order->getId() > 0) {
                    
                    $payment = $order->getPayment();
                    $model = $payment->getMethodInstance();
                
                    $merchantTransactionId = $callbackResult->getMerchantTransactionId();
                    $decodedOrderId = $this->helper->decodeOrderId($merchantTransactionId);
                    $model->log('Concerning order:'.$order->getId());

                    if ($order->getIncrementId() !== $decodedOrderId) {
                       $message = __('Merchant transaction id validation failed.');
                       throw new \Magento\Framework\Exception\LocalizedException($message);
                    }
                    
                    $order_id = $order->getId();
                    // handle result based on it's returnType
                    if ($callbackResult->getResult() == AllsecureResult::RESULT_OK) {
                        //result success
                        $gatewayReferenceId = $callbackResult->getUuid();
                        if ($callbackResult->getTransactionType() == AllsecureResult::TYPE_DEBIT) {
                            //result debit
                            if ( isset($callbackResult->getExtraData()['authCode']) ) {
                                $this->helper->updateTransactionResponse($order_id, 'AuthCode', $callbackResult->getExtraData()['authCode']);
                            } elseif (isset($callbackResult->getExtraData()['adapterReferenceId']) ) {
                                $this->helper->updateTransactionResponse($order_id, 'AuthCode', $callbackResult->getExtraData()['adapterReferenceId']);
                            }

                            $cardData = (array)($callbackResult->getReturnData());
                            $this->helper->updateTransactionResponse($order_id, 'CardData', json_encode($cardData));

                            if ($order->getState() != \Magento\Sales\Model\Order::STATE_PROCESSING) {
                                $this->helper->updateTransaction($order_id, 'status', 'debited');
                                $comment1 = __('Allsecureexchange payment is successfully debited. ');

                                $state = \Magento\Sales\Model\Order::STATE_PROCESSING;
                                $status = \Magento\Sales\Model\Order::STATE_PROCESSING;
                                $order->setState($state);
                                $order->setStatus($status);
                                $order->setTotalPaid($order->getGrandTotal());
                                $comment2 = __('Transaction ID: '). $gatewayReferenceId;
                                $comment = $comment1.$comment2;
                                $order->addStatusHistoryComment($comment, $status, true);
                                $order->save();
                                $this->orderSender->send($order, true);
                                $model->createInvoice($order, $gatewayReferenceId);
                            }
                        } else if ($callbackResult->getTransactionType() == AllsecureResult::TYPE_CAPTURE) {
                            //result capture
                            if ($order->getState() != \Magento\Sales\Model\Order::STATE_PROCESSING) {
                                $this->helper->updateTransaction($order_id, 'status', 'captured');
                                $comment1 = __('Allsecureexchange payment is successfully captured. ');

                                $state = \Magento\Sales\Model\Order::STATE_PROCESSING;
                                $status = \Magento\Sales\Model\Order::STATE_PROCESSING;
                                $order->setState($state);
                                $order->setStatus($status);
                                $order->setTotalPaid($order->getGrandTotal());
                                $comment2 = __('Transaction ID: '). $gatewayReferenceId;
                                $comment = $comment1.$comment2;
                                $order->addStatusHistoryComment($comment, $status, true);
                                $order->save();
                            }
                        } else if ($callbackResult->getTransactionType() == AllsecureResult::TYPE_VOID) {
                            //result void
                            if ($order->getState() != \Magento\Sales\Model\Order::STATE_CANCELED) {
                                $this->helper->updateTransaction($order_id, 'status', 'voided');
                                $comment1 = __('Allsecureexchange payment is successfully voided. ');
                                $comment2 = __('Transaction ID: '). $gatewayReferenceId;
                                $comment = $comment1.$comment2;
                                $order->cancel();
                                $order->addStatusHistoryComment($comment, \Magento\Sales\Model\Order::STATE_CANCELED);
                                $order->save();
                            }
                        } else if ($callbackResult->getTransactionType() == AllsecureResult::TYPE_PREAUTHORIZE) {
                            //result preauthorize
                            if ( isset($callbackResult->getExtraData()['authCode']) ) {
                                $this->helper->updateTransactionResponse($order_id, 'AuthCode', $callbackResult->getExtraData()['authCode']);
                            } elseif (isset($callbackResult->getExtraData()['adapterReferenceId']) ) {
                                $this->helper->updateTransactionResponse($order_id, 'AuthCode', $callbackResult->getExtraData()['adapterReferenceId']);
                            }

                            $cardData = (array)($callbackResult->getReturnData());
                            $this->helper->updateTransactionResponse($order_id, 'CardData', json_encode($cardData));
                            $this->helper->updateTransaction($order_id, 'status', 'preauthorized');
                            
                            $comment1 = __('Allsecureexchange payment is successfully reserved for manual capture. ');
                            $comment2 = __('Transaction ID: '). $gatewayReferenceId;
                            $comment = $comment1.$comment2;
                            
                            if ($order->getState() != \Magento\Sales\Model\Order::STATE_NEW) {
                                $state = \Magento\Sales\Model\Order::STATE_NEW;
                                $status = 'pending';
                                $order->setState($state);
                                $order->setStatus($status);
                                $order->setPaymentAuthorizationAmount($order->getGrandTotal());
                                $order->addStatusHistoryComment($comment, $status, true);
                                $order->save();
                                $this->orderSender->send($order, true);
                            } else {
                                $order->addStatusHistoryComment($comment);
                                $order->save();
                            }
                            
                            $model->createInvoice($order, $gatewayReferenceId, 'not_capture');
                        }
                    } elseif ($callbackResult->getResult() == AllsecureResult::RESULT_ERROR) {
                        //payment error
                        $cardData = (array)($callbackResult->getReturnData());
                        $this->helper->updateTransactionResponse($order_id, 'CardData', json_encode($cardData));
                        $this->helper->updateTransaction($order_id, 'status', 'error');
                        $error = $callbackResult->getFirstError();
                        $errorData = array();
                        $errorData["message"] = $error->getMessage();
                        $errorData["code"] = $error->getCode();
                        $errorData["adapterCode"] = $error->getAdapterCode();
                        $errorData["adapterMessage"] = $error->getAdapterMessage();
                        $this->helper->updateTransactionResponse($order_id, 'errorData', json_encode($errorData));
                    } else {
                        throw new \Magento\Framework\Exception\LocalizedException(__('Unknown callback result type.'));
                    }
                } else {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Order does not exist.'));
                }
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(__('Order ID missing in the callback URL.'));
            }
            echo 'OK';
            exit;
        } catch (\Exception $e) {
            $model->log('Webhook from Gateway Catch Exception: '.$e->getMessage());
            echo 'OK';
            exit;
        }
    }
}
