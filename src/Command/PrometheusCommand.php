<?php

declare(strict_types=1);

namespace Imi\Prometheus\Command;

use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Contract\BaseCommand;
use Imi\Meter\Facade\MeterRegistry;
use Imi\Prometheus\PrometheusMeterRegistry;

/**
 * @Command("prometheus")
 */
class PrometheusCommand extends BaseCommand
{
    /**
     * 擦除普罗米修斯本地存储数据.
     *
     * @CommandAction("wipe")
     */
    public function wipe(): void
    {
        $driver = MeterRegistry::getDriverInstance();
        if (!$driver instanceof PrometheusMeterRegistry)
        {
            throw new \RuntimeException(sprintf('The current MeterRegistry driver is not %s', PrometheusMeterRegistry::class));
        }
        $driver->wipeStorage();
    }
}
