<?php

declare(strict_types=1);

namespace Imi\Prometheus;

use Imi\Meter\Counter;

/**
 * @property PrometheusMeterRegistry|null $meterRegistry
 */
class PrometheusCounter extends Counter
{
    public function increment(float $amount = 1): void
    {
        parent::increment($amount);
        if ($this->meterRegistry)
        {
            $tags = $this->getTags();
            $this->meterRegistry->getCollectorRegistry()->getOrRegisterCounter('', $this->getName(), $this->getDescription(), array_keys($tags))->incBy($amount, array_values($tags));
        }
    }

    public function value(): float
    {
        throw new \RuntimeException(sprintf('Unsupport %s::%s', static::class, __FUNCTION__));
    }
}
