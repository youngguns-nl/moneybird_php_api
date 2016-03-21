<?php

namespace Moneybird;

require_once dirname(__FILE__) . '/../ApiConnector.php';
require_once dirname(__FILE__) . '/../vendor/psr/log/Psr/Log/LoggerInterface.php';

class LogTransportDecoratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogTransportDecorator
     */
    protected $decorator;

    /**
     * @var Transport
     */
    protected $decorated;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $log;

    public static function setUpBeforeClass()
    {
        include ('config.php');
    }

    public function setUp()
    {
        $this->decorated = $this->getMock('Moneybird\Transport');
        $this->log = $this->getMock('Psr\Log\LoggerInterface');
        $this->decorator = new LogTransportDecorator($this->decorated, $this->log);
    }

    public function testLogRequestAndResponse()
    {
        $url = 'http://test.nl';
        $method = 'POST';
        $params = ['foo' => 'bar'];
        $headers = ['cache-control' => 'public'];
        $expectedResponse    = 'content';

        $this->decorated->expects($this->any())
            ->method('requestsLeft')
            ->will($this->returnValue(3));

        $this->decorated->expects($this->once())
            ->method('send')
            ->with($url, $method, $params, $headers)
            ->will($this->returnValue($expectedResponse));

        $this->log->expects($this->at(0))
            ->method('info')
            ->with('MoneyBird Request', [
                'url' => $url,
                'requestMethod' => $method,
                'data' => $params,
                'headers' => $headers
            ]);

        $this->log->expects($this->at(1))
            ->method('info')
            ->with('MoneyBird Response', [
                'response' => $expectedResponse,
                'requestsLeft' => 3
            ]);

        $response = $this->decorator->send($url, $method, $params, $headers);

        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @expectedException \Moneybird\HttpClient\Exception
     */
    public function testLogFailedRequest()
    {
        $url = 'http://test.nl';
        $method = 'POST';
        $params = ['foo' => 'bar'];
        $headers = ['cache-control' => 'public'];
        $exception = new \Moneybird\HttpClient\Exception('test');

        $this->decorated->expects($this->any())
            ->method('requestsLeft')
            ->will($this->returnValue(4));

        $this->decorated->expects($this->once())
            ->method('send')
            ->will($this->throwException($exception));

        $this->log->expects($this->at(0))
            ->method('info')
            ->with('MoneyBird Request', [
                'url' => $url,
                'requestMethod' => $method,
                'data' => $params,
                'headers' => $headers
            ]);

        $this->log->expects($this->at(1))
            ->method('error')
            ->with('Response failed', [
                'exception' => $exception,
                'requestsLeft' => 4
            ]);

        $this->decorator->send($url, $method, $params, $headers);
    }
}
