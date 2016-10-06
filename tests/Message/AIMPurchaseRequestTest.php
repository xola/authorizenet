<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Tests\TestCase;

class AIMPurchaseRequestTest extends TestCase
{
    /** @var AIMPurchaseRequest */
    private $request;

    public function setUp()
    {
        $this->request = new AIMPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'clientIp' => '10.0.0.1',
                'amount' => '12.00',
                'customerId' => 'cust-id',
                'card' => $this->getValidCard(),
                'solutionId' => 'AAA100302',
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertEquals('authCaptureTransaction', $data->transactionRequest->transactionType);
        $this->assertEquals('10.0.0.1', $data->transactionRequest->customerIP);
        $this->assertEquals('cust-id', $data->transactionRequest->customer->id);
        $this->assertEquals('AAA100302', $data->transactionRequest->solution->id);

        $setting = $data->transactionRequest->transactionSettings->setting[0];
        $this->assertEquals('testRequest', $setting->settingName);
        $this->assertEquals('false', $setting->settingValue);
    }
}
