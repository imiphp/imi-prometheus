<?php

declare(strict_types=1);

namespace Imi\Prometheus\Test;

use function Imi\env;
use Imi\Util\Http\Consts\StatusCode;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Yurun\Util\HttpRequest;

abstract class BaseTest extends TestCase
{
    protected static Process $process;

    protected static string $httpHost = '';

    protected string $registryServiceName = '';

    protected static function __startServer(): void
    {
        throw new \RuntimeException('You must implement the __startServer() method');
    }

    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass(): void
    {
        self::$httpHost = env('HTTP_SERVER_HOST', 'http://127.0.0.1:8080/');
        static::__startServer();
        $httpRequest = new HttpRequest();
        for ($i = 0; $i < 20; ++$i)
        {
            sleep(1);
            if ('imi' === $httpRequest->timeout(3000)->get(self::$httpHost)->body())
            {
                sleep(3); // 等待心跳

                return;
            }
        }
        throw new \RuntimeException('Server started failed');
    }

    /**
     * {@inheritDoc}
     */
    public static function tearDownAfterClass(): void
    {
        if (isset(self::$process))
        {
            self::$process->stop(10, \SIGTERM);
        }
    }

    public function testTests(): void
    {
        $httpRequest = new HttpRequest();
        $response = $httpRequest->get(self::$httpHost . '/test');
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
    }

    public function testManual(): void
    {
        $httpRequest = new HttpRequest();
        $response = $httpRequest->get(self::$httpHost . '/testManual');
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
    }

    public function testMetrics(): void
    {
        $httpRequest = new HttpRequest();
        $response = $httpRequest->get(self::$httpHost . '/metrics');
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
    }
}
