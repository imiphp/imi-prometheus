<?php

declare(strict_types=1);

namespace Imi\Prometheus;

use Imi\Meter\Histogram;

/**
 * @property PrometheusMeterRegistry|null $meterRegistry
 */
class PrometheusHistogram extends Histogram
{
    public function record(float $value): void
    {
        parent::record($value);
        $tags = $this->getTags();
        $this->meterRegistry->getCollectorRegistry()->getOrRegisterHistogram('', $this->getName(), $this->getDescription(), array_keys($tags), $this->getBuckets())->observe($value, array_values($tags));
    }

    public function count(): int
    {
        throw new \RuntimeException(sprintf('Unsupport %s::%s', static::class, __FUNCTION__));
    }

    public function totalAmount(): float
    {
        throw new \RuntimeException(sprintf('Unsupport %s::%s', static::class, __FUNCTION__));
    }

    public function mean(): float
    {
        throw new \RuntimeException(sprintf('Unsupport %s::%s', static::class, __FUNCTION__));
    }
}
