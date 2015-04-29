<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;

/**
 * Authorize.Net AIM Abstract Request
 */
abstract class AIMAbstractRequest extends AbstractRequest
{
    protected $liveEndpoint = 'https://api.authorize.net/xml/v1/request.api';
    protected $developerEndpoint = 'https://apitest.authorize.net/xml/v1/request.api';

    protected $action = null;

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

    public function getCustomerId()
    {
        return $this->getParameter('customerId');
    }

    public function setCustomerId($value)
    {
        return $this->setParameter('customerId', $value);
    }

    /**
     * @return mixed|\SimpleXMLElement
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getBaseData()
    {
        $data = new \SimpleXMLElement('<createTransactionRequest/>');
        $data->addAttribute('xmlns', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd');

        // Credentials
        $data->merchantAuthentication->name = $this->getApiLoginId();
        $data->merchantAuthentication->transactionKey = $this->getTransactionKey();

        // User-assigned transaction ID
        $txnId = $this->getTransactionId();
        if (!empty($txnId)) {
            $data->refId = $this->getTransactionId();
        }

        // Transaction type
        if (!$this->action) {
            // The extending class probably hasn't specified an "action"
            throw new InvalidRequestException();
        }
        $data->transactionRequest->transactionType = $this->action;

        return $data;
    }

    /**
     * Adds billing data to a partially filled request data object.
     *
     * @param \SimpleXMLElement $data
     *
     * @return \SimpleXMLElement
     */
    protected function addBillingData(\SimpleXMLElement $data)
    {
        /** @var mixed $req */
        $req = $data->transactionRequest;

        // Merchant assigned customer ID
        if (!empty($this->getCustomerId())) {
            $req->customer->id = $this->getCustomerId();
        }

        if (!empty($this->getEmail())) {
            $req->customer->email = $this->getEmail();
        }

        /** @var CreditCard $card */
        if ($card = $this->getCard()) {
            // A card is present, so include billing and shipping details
            $req->billTo->firstName = $card->getBillingFirstName();
            $req->billTo->lastName = $card->getBillingLastName();
            $req->billTo->company = $card->getBillingCompany();
            $req->billTo->address = trim($card->getBillingAddress1() . " \n" . $card->getBillingAddress2());
            $req->billTo->city = $card->getBillingCity();
            $req->billTo->state = $card->getBillingState();
            $req->billTo->zip = $card->getBillingPostcode();
            $req->billTo->country = $card->getBillingCountry();

            $req->shipTo->firstName = $card->getShippingLastName();
            $req->shipTo->lastName = $card->getShippingLastName();
            $req->shipTo->company = $card->getShippingCompany();
            $req->shipTo->address = trim($card->getShippingAddress1() . " \n" . $card->getShippingAddress2());
            $req->shipTo->city = $card->getShippingCity();
            $req->shipTo->state = $card->getShippingState();
            $req->shipTo->zip = $card->getShippingPostcode();
            $req->shipTo->country = $card->getShippingCountry();
        }

        return $data;
    }

    protected function addTestModeSetting(\SimpleXMLElement $data)
    {
        // Test mode setting
        $data->transactionRequest->transactionSettings->setting->settingName = 'testRequest';
        $data->transactionRequest->transactionSettings->setting->settingValue = $this->getTestMode() ? 'true' : 'false';

        return $data;
    }

    public function sendData($data)
    {
        $headers = array('Content-Type' => 'text/xml; charset=utf-8');
        $data = $data->saveXml();
        $httpResponse = $this->httpClient->post($this->getEndpoint(), $headers, $data)->send();

        return $this->response = new AIMResponse($this, $httpResponse->getBody());
    }

    public function getEndpoint()
    {
        return $this->getDeveloperMode() ? $this->developerEndpoint : $this->liveEndpoint;
    }
}
