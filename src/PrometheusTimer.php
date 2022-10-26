<?php

declare(strict_types=1);

namespace Imi\Prometheus;

use Imi\Meter\Enum\TimeUnit;
use Imi\Meter\Timer;
use Imi\Meter\Util\TimeUnitUtil;

/**
 * @property PrometheusMeterRegistry|null $meterRegistry
 */
class PrometheusTimer extends Timer
{
    public function record(int $nanoSecond, ?int $timeUnit = null): void
    {
        parent::record($nanoSecond, $timeUnit);
        $tags = $this->getTags();
        $options = $this->getOptions();
        $amount = TimeUnitUtil::convert($nanoSecond, TimeUnit::NANO_SECOND, $timeUnit ?? $this->baseTimeUnit());
        if ($options['histogram'] ?? false)
        {
            $this->meterRegistry->getCollectorRegistry()->getOrRegisterHistogram('', $this->getName(), $this->getDescription(), array_keys($tags), $options['buckets'] ?? null)->observe($amount, array_values($tags));
        }
        else
        {
            $this->meterRegistry->getCollectorRegistry()->getOrRegisterSummary('', $this->getName(), $this->getDescription(), array_keys($tags), $options['maxAgeSeconds'] ?? 600, $options['percentile'] ?? null)->observe($amount, array_values($tags));
        }
    }
}
