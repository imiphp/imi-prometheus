<?php

declare(strict_types=1);

namespace app\Service;

use Imi\Meter\Annotation\Gauged;
use Imi\Meter\Annotation\Histogram;
use Imi\Meter\Annotation\Summary;
use Imi\Meter\Annotation\Timed;
use Imi\Meter\Enum\TimeUnit;
use Imi\Meter\Facade\MeterRegistry;
use Imi\Worker;

class TestService
{
    /**
     * @Gauged(name="test_memory_usage", description="memory usage", tags={"workerId"="{returnValue.workerId}"}, value="{returnValue.memory}")
     */
    public function recordMemoryUsage(): array
    {
        return [
            'workerId' => Worker::getWorkerId(),
            'memory'   => memory_get_usage(),
        ];
    }

    /**
     * @Timed(name="test_timed", description="memory usage", baseTimeUnit=TimeUnit::MILLI_SECONDS, options={"percentile"={0.1, 0.5, 0.99}})
     */
    public function testTimed(): int
    {
        $ms = mt_rand(10, 1000);
        usleep($ms * 1000);

        return $ms;
    }

    /**
     * @Timed(name="test_timed_histogram", description="memory usage", baseTimeUnit=TimeUnit::MILLI_SECONDS, options={"histogram"=true, "buckets"={50, 100, 300, 600, 800, 1000}})
     */
    public function testTimedHistogram(): int
    {
        $ms = mt_rand(10, 1000);
        usleep($ms * 1000);

        return $ms;
    }

    /**
     * @Histogram(name="test_histogram", baseTimeUnit=TimeUnit::MILLI_SECONDS, buckets={50, 100, 300, 600, 800, 1000})
     */
    public function testHistogram(): int
    {
        return mt_rand(10, 1000);
    }

    /**
     * @Summary(name="test_summary", baseTimeUnit=TimeUnit::MILLI_SECONDS, percentile={0.1, 0.5, 0.99})
     */
    public function testSummary(): int
    {
        return mt_rand(10, 1000);
    }

    public function testCounterManual(): void
    {
        MeterRegistry::getDriverInstance()->counter('testCounterManual', ['result' => 'success'], 'test')->increment();
    }

    public function testGaugeManual(): void
    {
        MeterRegistry::getDriverInstance()->gauge('testGaugeManual', ['result' => 'success'], 'test')->record(114514);
    }

    public function testTimedManual(): void
    {
        $timer = MeterRegistry::getDriverInstance()->timer('testTimedManual', ['result' => 'success'], 'test', TimeUnit::MILLI_SECONDS);
        $timerSample = $timer->start();
        usleep(mt_rand(10, 1000) * 1000);
        $timerSample->stop($timer);
    }

    public function testTimedHistogramManual(): void
    {
        $timer = MeterRegistry::getDriverInstance()->timer('testTimedHistogramManual', ['result' => 'success'], 'test', TimeUnit::MILLI_SECONDS, [
            'histogram' => true,
            'buckets'   => [100, 500, 1500],
        ]);
        $timerSample = $timer->start();
        usleep(mt_rand(10, 1000) * 1000);
        $timerSample->stop($timer);
    }

    public function testHistogramManual(): void
    {
        MeterRegistry::getDriverInstance()->histogram('testHistogramManual', ['result' => 'success'], 'test', [100, 500, 1500])->record(114514);
    }

    public function testSummaryManual(): void
    {
        MeterRegistry::getDriverInstance()->summary('testHistogramManual', ['result' => 'success'], 'test', [0.1, 0.5, 0.99])->record(114514);
    }
}
