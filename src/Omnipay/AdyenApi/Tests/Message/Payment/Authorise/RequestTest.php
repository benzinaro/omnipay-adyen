<?php
namespace Omnipay\AdyenApi\Tests\Message\Payment\Authorise;

use Guzzle\Http\ClientInterface as HttpClient;
use Guzzle\Http\Message\EntityEnclosingRequestInterface;
use Guzzle\Http\Message\Response as GuzzleResponse;
use Omnipay\AdyenApi\Message\Payment\Authorise\Request;
use Omnipay\AdyenApi\Message\Payment\Authorise\Response;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

/**
 * Class RequestTest
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    /** @var Request */
    private $request;

    /** @var HttpClient|ObjectProphecy */
    private $httpClient;

    /** @var HttpRequest|ObjectProphecy */
    private $httpRequest;

    /**
     * @{inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->httpClient = $this->prophesize('Guzzle\Http\ClientInterface');
        $this->httpRequest = $this->prophesize('Symfony\Component\HttpFoundation\Request');
        $this->request = new Request(
            $this->httpClient->reveal(),
            $this->httpRequest->reveal()
        );
        $this->request->initialize();
    }

    /**
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::appendAdditionalData
     */
    public function testGetDataWithBasicData()
    {
        $data = array(
            'amountValue' => 10,
            'amountCurrency' => 'CURRENCY',
            'reference' => 'REFERENCE',
            'merchantAccount' => 'MERCHANT',
            'encryptedForm' => 'FORM',
        );

        $this->request->initialize($data);

        $this->assertEquals(
            array(
                'amount' => array(
                    'value' => $data['amountValue']*100,
                    'currency' => $data['amountCurrency'],
                ),
                'reference' => $data['reference'],
                'merchantAccount' => $data['merchantAccount'],
                'additionalData' => array(
                    'card.encrypted.json' => $data['encryptedForm'],
                ),
            ),
            $this->request->getData()
        );
    }

    /**
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::appendAdditionalData
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::appendAdditionalAmountData
     */
    public function testGetDataWithAdditionalAmount()
    {
        $data = array(
            'amountValue' => 0,
            'amountCurrency' => 'CURRENCY',
            'reference' => 'REFERENCE',
            'merchantAccount' => 'MERCHANT',
            'encryptedForm' => 'FORM',
            'additionalAmountValue' => 200,
            'additionalAmountCurrency' => 'CURRENCY2',
        );

        $this->request->initialize($data);

        $this->assertEquals(
            array(
                'amount' => array(
                    'value' => $data['amountValue']*100,
                    'currency' => $data['amountCurrency'],
                ),
                'reference' => $data['reference'],
                'merchantAccount' => $data['merchantAccount'],
                'additionalData' => array(
                    'card.encrypted.json' => $data['encryptedForm'],
                ),
                'additionalAmount' => array(
                    'value' => $data['additionalAmountValue']*100,
                    'currency' => $data['additionalAmountCurrency'],
                ),
            ),
            $this->request->getData()
        );
    }

    /**
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::appendAdditionalData
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::appendRecurringData
     */
    public function testGetDataWithRecurringData()
    {
        $data = array(
            'amountValue' => 10,
            'amountCurrency' => 'CURRENCY',
            'reference' => 'REFERENCE',
            'merchantAccount' => 'MERCHANT',
            'encryptedForm' => 'FORM',
            'recurringContract' => 'RecurringContract',
            'recurringDetailName' => 'RecurringDetailName',
        );

        $this->request->initialize($data);

        $this->assertEquals(
            array(
                'amount' => array(
                    'value' => $data['amountValue']*100,
                    'currency' => $data['amountCurrency'],
                ),
                'reference' => $data['reference'],
                'merchantAccount' => $data['merchantAccount'],
                'additionalData' => array(
                    'card.encrypted.json' => $data['encryptedForm'],
                ),
                'recurring' => array(
                    'contract' => $data['recurringContract'],
                    'recurringDetailName' => $data['recurringDetailName'],
                ),
            ),
            $this->request->getData()
        );
    }

    /**
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::appendAdditionalData
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::appendShopperData
     */
    public function testGetDataWithShopperData()
    {
        $data = array(
            'amountValue' => 10,
            'amountCurrency' => 'CURRENCY',
            'reference' => 'REFERENCE',
            'merchantAccount' => 'MERCHANT',
            'encryptedForm' => 'FORM',
            'shopperReference' => 'ShopperReference',
        );

        $this->request->initialize($data);

        $this->assertEquals(
            array(
                'amount' => array(
                    'value' => $data['amountValue']*100,
                    'currency' => $data['amountCurrency'],
                ),
                'reference' => $data['reference'],
                'merchantAccount' => $data['merchantAccount'],
                'additionalData' => array(
                    'card.encrypted.json' => $data['encryptedForm'],
                ),
                'shopperReference' => $data['shopperReference'],
            ),
            $this->request->getData()
        );
    }

    /**
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::appendAdditionalData
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::appendAdditionalAmountData
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::appendRecurringData
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::appendShopperData
     */
    public function testGetDataWithAllData()
    {
        $data = array(
            'amountValue' => 10,
            'amountCurrency' => 'CURRENCY',
            'reference' => 'REFERENCE',
            'merchantAccount' => 'MERCHANT',
            'encryptedForm' => 'FORM',
            'additionalAmountValue' => 200,
            'additionalAmountCurrency' => 'CURRENCY2',
            'recurringContract' => 'RecurringContract',
            'recurringDetailName' => 'RecurringDetailName',
            'shopperReference' => 'ShopperReference',
        );

        $this->request->initialize($data);

        $this->assertEquals(
            array(
                'amount' => array(
                    'value' => $data['amountValue']*100,
                    'currency' => $data['amountCurrency'],
                ),
                'reference' => $data['reference'],
                'merchantAccount' => $data['merchantAccount'],
                'additionalData' => array(
                    'card.encrypted.json' => $data['encryptedForm'],
                ),
                'additionalAmount' => array(
                    'value' => $data['additionalAmountValue']*100,
                    'currency' => $data['additionalAmountCurrency'],
                ),
                'recurring' => array(
                    'contract' => $data['recurringContract'],
                    'recurringDetailName' => $data['recurringDetailName'],
                ),
                'shopperReference' => $data['shopperReference'],
            ),
            $this->request->getData()
        );
    }

    /**
     */
    public function testSendData()
    {
        /** @var EntityEnclosingRequestInterface|ObjectProphecy $httpRequest */
        $httpRequest = $this->prophesize('Guzzle\Http\Message\EntityEnclosingRequestInterface');
        /** @var GuzzleResponse|ObjectProphecy $response */
        $response = $this->prophesize('Guzzle\Http\Message\Response');

        $this->httpClient->post(Argument::type('string'))
            ->willReturn($httpRequest->reveal())
            ->shouldBeCalledTimes(1);
        $httpRequest->setAuth(Argument::type('string'), Argument::type('string'))
            ->shouldBeCalledTimes(1);
        $httpRequest->setBody(Argument::type('string'))
            ->shouldBeCalledTimes(1);
        $httpRequest->setHeader('Content-Type', 'application/json;charset=utf-8')
            ->shouldBeCalledTimes(1);

        $httpRequest->send()
            ->willReturn($response->reveal())
            ->shouldBeCalledTimes(1);

        $response->getBody()
            ->willReturn(json_encode(array('ok' => true)))
            ->shouldBeCalledTimes(1);


        $this->request->initialize(array(
            'apiUser' => 'USER',
            'apiPassword' => 'PASSWORD',
            'amountCurrency' => 'CURRENCY',
            'reference' => 'REFERENCE',
            'merchantAccount' => 'MERCHANT',
            'encryptedForm' => 'FORM',
        ));

        /** @var Response $response */
        $response = $this->request->sendData(array());
        $this->assertInstanceOf(
            'Omnipay\AdyenApi\Message\Payment\Authorise\Response',
            $response
        );

        $this->assertEquals(
            true,
            $response->getData()->ok
        );
    }

    /**
     * @dataProvider getParameters
     *
     * @param string $parameterName
     * @param mixed  $parameterValue
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::getAmountValue
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::getAmountCurrency
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::getAdditionalAmountValue
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::getAdditionalAmountCurrency
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::getRecurringContract
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::getRecurringDetailName
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::getShopperReference
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::getReference
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::getEncryptedForm
     */
    public function testParametersGetAfterInitialize($parameterName, $parameterValue)
    {
        $this->request->initialize(array($parameterName => $parameterValue));

        $getter = sprintf(
            'get%s',
            ucfirst($parameterName)
        );

        $this->assertTrue(method_exists($this->request, $getter));

        $this->assertSame(
            $parameterValue,
            $this->request->$getter()
        );
    }

    /**
     * @dataProvider getParameters
     *
     * @param string $parameterName
     * @param mixed  $parameterValue
     *
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::getAmountValue
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::getAmountCurrency
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::getAdditionalAmountValue
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::getAdditionalAmountCurrency
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::getRecurringContract
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::getRecurringDetailName
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::getShopperReference
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::getReference
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::getEncryptedForm
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::setAmountValue
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::setAmountCurrency
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::setAdditionalAmountValue
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::setAdditionalAmountCurrency
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::setRecurringContract
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::setRecurringDetailName
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::setShopperReference
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::setReference
     * @covers Omnipay\AdyenApi\Message\Payment\Authorise\Request::setEncryptedForm
     */
    public function testParametersSetGet($parameterName, $parameterValue)
    {
        $getter = sprintf(
            'get%s',
            ucfirst($parameterName)
        );
        $setter = sprintf(
            'set%s',
            ucfirst($parameterName)
        );

        $this->assertTrue(method_exists($this->request, $setter));

        $this->request->$setter($parameterValue);

        $this->assertSame(
            $parameterValue,
            $this->request->$getter()
        );
    }

    /**
     */
    public function testGetMethodName()
    {
        $this->assertSame(
            'authorise',
            $this->request->getMethodName()
        );
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return array(
            'AMOUNT_VALUE' => array('amountValue', 'MyAmountValue'),
            'AMOUNT_CURRENCY' => array('amountCurrency', 'MyAmountCurrency'),
            'ADDITIONAL_AMOUNT_VALUE' => array('additionalAmountValue', 'MyAdditionalAmountValue'),
            'ADDITIONAL_AMOUNT_CURRENCY' => array('additionalAmountCurrency', 'MyAmountCurrency'),
            'RECURRING_CONTRACT' => array('recurringContract', 'MyRecurringContract'),
            'RECURRING_DETAIL_NAME' => array('recurringDetailName', 'MyRecurringDetailName'),
            'SHOPPER_REFERENCE' => array('shopperReference', 'MyShopperReference'),
            'REFERENCE' => array('reference', 'MyReference'),
            'ENCRYPTED_FORM' => array('encryptedForm', 'MyEncryptedForm'),
        );
    }
}
