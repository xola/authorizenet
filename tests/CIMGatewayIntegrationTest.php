<?php

namespace Omnipay\AuthorizeNet;

use Omnipay\Tests\TestCase;
use Guzzle\Http\Client;
use Guzzle\Log\MessageFormatter;
use Guzzle\Log\PsrLogAdapter;
use Guzzle\Plugin\Log\LogPlugin;

/**
 * Integration tests for the CIM Gateway. These tests make real requests to Authorize.NET sandbox environment.
 *
 * In order to run, these tests require your Authorize.NET sandbox credentials without which, they just skip. Configure
 * the following environment variables:
 *
 *   1. AUTHORIZE_NET_API_LOGIN_ID
 *   2. AUTHORIZE_NET_TRANSACTION_KEY
 *
 * Once configured, the tests will no longer skip.
 */
class CIMGatewayIntegrationTest extends TestCase
{
    /** @var CIMGateway */
    protected $gateway;

    public function setUp()
    {
        parent::setUp();

//        $apiLoginId = getenv('AUTHORIZE_NET_API_LOGIN_ID');
//        $transactionKey = getenv('AUTHORIZE_NET_TRANSACTION_KEY');
        //todo: Remove this before final commit
        $apiLoginId = '3wM8sJ9qR';
        $transactionKey = '4d3v32QJtB78tBTT';

        if ($apiLoginId && $transactionKey) {

            $logger = new \Monolog\Logger('authorizenet_cim');
            $logger->pushHandler(new \Monolog\Handler\StreamHandler('/var/log/php/debug.log', \Monolog\Logger::DEBUG));
            $logger->pushHandler(new \Monolog\Handler\FirePHPHandler());
            $adapter = new PsrLogAdapter($logger);
            $logPlugin = new LogPlugin($adapter, MessageFormatter::DEBUG_FORMAT);

            $client = new Client();
            $client->addSubscriber($logPlugin);

            $this->gateway = new CIMGateway($client, $this->getHttpRequest());
            $this->gateway->setDeveloperMode(true);
            $this->gateway->setApiLoginId($apiLoginId);
            $this->gateway->setTransactionKey($transactionKey);
        } else {
            // No credentials were found, so skip this test
            $this->markTestSkipped();
        }
    }

    public function testCreateCard()
    {
        $rand = rand(100000, 999999);
        // Create card
        $params = array(
            'card' => $this->getValidCard(),
            'name' => 'Kaywinnet Lee Frye',
            'email' => "kaylee$rand@serenity.com",
        );
        $request = $this->gateway->createCard($params);
        $request->setTestMode(true);

        $response = $request->send();
        $this->assertTrue($response->isSuccessful(), 'Profile should get created');
        $this->assertNotNull($response->getCardReference(), 'Card response should be returned');
    }
}