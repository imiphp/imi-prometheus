<?php

declare(strict_types=1);

namespace app\Listener;

use app\Service\TestService;
use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Timer\Timer;
use Imi\Worker;

/**
 * @Listener(eventName="IMI.SERVER.WORKER_START")
 */
class WorkerStart implements IEventListener
{
    /**
     * @Inject
     */
    protected TestService $testService;

    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        $this->testService->recordMemoryUsage();
        Timer::tick(5000, fn () => $this->testService->recordMemoryUsage());
    }
}
