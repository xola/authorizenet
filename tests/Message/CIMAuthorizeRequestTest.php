<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\Tests\TestCase;

class CIMAuthorizeRequestTest extends TestCase
{
    /** @var CIMAuthorizeRequest */
    protected $request;

    public function setUp()
    {
        $this->request = new CIMAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'cardReference' => '{"customerProfileId":"28972085","customerPaymentProfileId":"26317841","customerShippingAddressId":"27057151"}',
                'amount' => '12.00',
                'description' => 'Test authorize transaction',
                'clientIp' => '10.0.0.1'
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertEquals('12.00', $data->transactionRequest->amount);
        $this->assertEquals('10.0.0.1', $data->transactionRequest->customerIP);
        $this->assertEquals('28972085', $data->transactionRequest->profile->customerProfileId);
        $this->assertEquals('26317841', $data->transactionRequest->profile->paymentProfile->paymentProfileId);
        $this->assertEquals('27057151', $data->transactionRequest->profile->shippingProfileId);
        $this->assertEquals('Test authorize transaction', $data->transactionRequest->order->description);
    }

    public function testShouldTruncateLongDescription()
    {
        $description = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";
        $truncatedDescription = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has su";

        $this->request = new CIMAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'cardReference' => '{"customerProfileId":"28972085","customerPaymentProfileId":"26317841","customerShippingAddressId":"27057151"}',
                'amount' => '12.00',
                'description' => $description,
                'clientIp' => '10.0.0.1'
            )
        );
        $data = $this->request->getData();
        $this->assertEquals($truncatedDescription, $data->transactionRequest->order->description);
    }

    public function testShouldUseTrackDataIfCardPresent()
    {
        $card = $this->getValidCard();
        $card['tracks'] = '%B4242424242424242^SMITH/JOHN ^2511126100000000000000444000000?;4242424242424242=25111269999944401?';
        $this->request->initialize(array(
            'card' => $card,
            'amount' => 21.00,
            'clientIp' => '10.0.0.1'
        ));

        $data = $this->request->getData();

        $this->assertObjectNotHasAttribute('profile', $data->transactionRequest);
        $this->assertObjectNotHasAttribute('customerIP', $data->transactionRequest, 'should not set IP for card present');
        $this->assertObjectHasAttribute('trackData', $data->transactionRequest->payment);
    }
}
