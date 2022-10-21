<?php

declare(strict_types=1);

namespace app\ApiServer\Controller;

use app\Service\TestService;
use Imi\Aop\Annotation\Inject;
use Imi\Controller\HttpController;
use Imi\Meter\Annotation\Counted;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;
use Imi\Util\Imi;
use function Yurun\Swoole\Coroutine\batch;

/**
 * @Controller("/")
 */
class IndexController extends HttpController
{
    /**
     * @Inject
     */
    protected TestService $testService;

    /**
     * @Action
     * @Route("/")
     *
     * @return mixed
     */
    public function index()
    {
        $this->response->getBody()->write('imi');

        return $this->response;
    }

    /**
     * @Action
     *
     * @return mixed
     */
    public function metrics()
    {
        /** @var \Imi\Prometheus\PrometheusMeterRegistry $driver */
        $driver = \Imi\Meter\Facade\MeterRegistry::getDriverInstance();
        $driver->render(null, $this->response);

        return $this->response;
    }

    /**
     * @Action
     * @Counted(name="test_some_counter", description="it increases", tags={"route"="index"})
     *
     * @return mixed
     */
    public function test()
    {
        $callbacks = [
            fn () => $this->testService->testTimed(),
            fn () => $this->testService->testTimedHistogram(),
            fn () => $this->testService->testHistogram(),
            fn () => $this->testService->testSummary(),
        ];
        if (Imi::checkAppType('swoole'))
        {
            batch($callbacks);
        }
        else
        {
            foreach ($callbacks as $callback)
            {
                $callback();
            }
        }
    }

    /**
     * @Action
     *
     * @return mixed
     */
    public function testManual()
    {
        $callbacks = [
            fn () => $this->testService->testCounterManual(),
            fn () => $this->testService->testGaugeManual(),
            fn () => $this->testService->testTimedManual(),
            fn () => $this->testService->testTimedHistogramManual(),
            fn () => $this->testService->testHistogramManual(),
            fn () => $this->testService->testSummaryManual(),
        ];
        if (Imi::checkAppType('swoole'))
        {
            batch($callbacks);
        }
        else
        {
            foreach ($callbacks as $callback)
            {
                $callback();
            }
        }
    }
}
