<?php

namespace Omnipay\AuthorizeNet;

use Omnipay\AuthorizeNet\Message\AIMAuthorizeRequest;
use Omnipay\AuthorizeNet\Message\AIMCaptureRequest;
use Omnipay\AuthorizeNet\Message\AIMPurchaseRequest;
use Omnipay\AuthorizeNet\Message\AIMRefundRequest;
use Omnipay\AuthorizeNet\Message\AIMVoidRequest;
use Omnipay\Common\AbstractGateway;

/**
 * Authorize.Net AIM Class
 */
class AIMGateway extends AbstractGateway
{
    public function getName()
    {
        return 'Authorize.Net AIM';
    }

    public function getDefaultParameters()
    {
        return array(
            'apiLoginId'        => '',
            'transactionKey'    => '',
            'solutionId'        => '',
            'testMode'          => false,
            'developerMode'     => false,
            'liveEndpoint'      => 'https://api.authorize.net/xml/v1/request.api',
            'developerEndpoint' => 'https://apitest.authorize.net/xml/v1/request.api',
            'deviceType'        => 1 // Device used to make the transaction. Required for card present. "1" = Unknown.
        );
    }

    public function getApiLoginId()
    {
        return $this->getParameter('apiLoginId');
    }

    public function setApiLoginId($value)
    {
        return $this->setParameter('apiLoginId', $value);
    }

    public function getTransactionKey()
    {
        return $this->getParameter('transactionKey');
    }

    public function setTransactionKey($value)
    {
        return $this->setParameter('transactionKey', $value);
    }

    public function getDeveloperMode()
    {
        return $this->getParameter('developerMode');
    }

    public function setDeveloperMode($value)
    {
        return $this->setParameter('developerMode', $value);
    }

    public function setEndpoints($endpoints)
    {
        $this->setParameter('liveEndpoint', $endpoints['live']);
        return $this->setParameter('developerEndpoint', $endpoints['developer']);
    }

    public function getLiveEndpoint()
    {
        return $this->getParameter('liveEndpoint');
    }

    public function setLiveEndpoint($value)
    {
        return $this->setParameter('liveEndpoint', $value);
    }

    public function getDeveloperEndpoint()
    {
        return $this->getParameter('developerEndpoint');
    }

    public function setDeveloperEndpoint($value)
    {
        return $this->setParameter('developerEndpoint', $value);
    }

    public function getDuplicateWindow()
    {
        return $this->getParameter('duplicateWindow');
    }

    public function setDuplicateWindow($value)
    {
        return $this->setParameter('duplicateWindow', $value);
    }

    public function getDeviceType()
    {
        return $this->getParameter('deviceType');
    }

    public function setSolutionId($value)
    {
        return $this->setParameter('solutionId', $value);
    }

    public function getSolutionId()
    {
        return $this->getParameter('solutionId');
    }

    /**
     * Sets the type of device used to collect the credit card data. A device type is required for card present
     * transactions.
     *
     * 1 = Unknown
     * 2 = Unattended Terminal
     * 3 = Self Service Terminal
     * 4 = Electronic Cash Register
     * 5 = Personal Computer-Based Terminal
     * 6 = AirPay
     * 7 = Wireless POS
     * 8 = Website
     * 9 = Dial Terminal
     * 10 = Virtual Terminal
     *
     * @see http://developer.authorize.net/api/reference/#payment-transactions-charge-a-credit-card
     * @param $value
     * @return $this
     */
    public function setDeviceType($value)
    {
        return $this->setParameter('deviceType', $value);
    }

    /**
     * @param array $parameters
     * @return AIMAuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AIMAuthorizeRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return AIMCaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AIMCaptureRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return AIMPurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AIMPurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return AIMVoidRequest
     */
    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AIMVoidRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return AIMRefundRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AIMRefundRequest', $parameters);
    }
}
