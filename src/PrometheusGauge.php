<?php

declare(strict_types=1);

namespace Imi\Prometheus;

use Imi\Meter\Gauge;

/**
 * @property PrometheusMeterRegistry|null $meterRegistry
 */
class PrometheusGauge extends Gauge
{
    public function record(float $value): void
    {
        parent::record($value);
        if ($this->meterRegistry)
        {
            $tags = $this->getTags();
            $this->meterRegistry->getCollectorRegistry()->getOrRegisterGauge('', $this->getName(), $this->getDescription(), array_keys($tags))->set($value, array_values($tags));
        }
    }

    public function increment(float $amount = 1): void
    {
        parent::increment($amount);
        if ($this->meterRegistry)
        {
            $tags = $this->getTags();
            $this->meterRegistry->getCollectorRegistry()->getOrRegisterGauge('', $this->getName(), $this->getDescription(), array_keys($tags))->incBy($amount, array_values($tags));
        }
    }

    public function decrement(float $amount = 1): void
    {
        parent::decrement($amount);
        if ($this->meterRegistry)
        {
            $tags = $this->getTags();
            $this->meterRegistry->getCollectorRegistry()->getOrRegisterGauge('', $this->getName(), $this->getDescription(), array_keys($tags))->decBy($amount, array_values($tags));
        }
    }

    public function value(): float
    {
        throw new \RuntimeException(sprintf('Unsupport %s::%s', static::class, __FUNCTION__));
    }
}
