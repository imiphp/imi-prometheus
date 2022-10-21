<?php

declare(strict_types=1);

namespace Imi\Prometheus;

use Imi\Meter\Summary;

/**
 * @property PrometheusMeterRegistry|null $meterRegistry
 */
class PrometheusSummary extends Summary
{
    public function record(float $value): void
    {
        $tags = $this->getTags();
        $this->meterRegistry->getCollectorRegistry()->getOrRegisterSummary('', $this->getName(), $this->getDescription(), array_keys($tags), $options['maxAgeSeconds'] ?? 600, $this->getPercentile())->observe($value, array_values($tags));
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
