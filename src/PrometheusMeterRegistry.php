<?php

declare(strict_types=1);

namespace Imi\Prometheus;

use Imi\App;
use Imi\Meter\Contract\BaseMeterRegistry;
use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Util\Http\Consts\ResponseHeader;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class PrometheusMeterRegistry extends BaseMeterRegistry
{
    public const COUNTER_CLASS = PrometheusCounter::class;

    public const GAUGE_CLASS = PrometheusGauge::class;

    public const TIMER_CLASS = PrometheusTimer::class;

    public const HISTOGRAM_CLASS = PrometheusHistogram::class;

    public const SUMMARY_CLASS = PrometheusSummary::class;

    protected ?CollectorRegistry $collectorRegistry = null;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        if (isset($config['adapter']['class']))
        {
            // @phpstan-ignore-next-line
            $this->collectorRegistry = new CollectorRegistry(App::newInstance($config['adapter']['class'], $config['adapter']['options'] ?? []));
        }
    }

    public function getCollectorRegistry(): ?CollectorRegistry
    {
        return $this->collectorRegistry;
    }

    /**
     * @param StreamInterface|ResponseInterface|IHttpResponse|resource|null $to
     */
    public function render(?string $renderClass = null, &$to = null): string
    {
        $renderClass ??= RenderTextFormat::class;
        $render = new $renderClass();
        $content = $render->render($this->getCollectorRegistry()->getMetricFamilySamples());
        if ($to instanceof StreamInterface)
        {
            $to->write($content);
        }
        elseif ($to instanceof IHttpResponse)
        {
            $to->setHeader(ResponseHeader::CONTENT_TYPE, $renderClass::MIME_TYPE)
               ->getBody()->write($content);
        }
        elseif ($to instanceof ResponseInterface)
        {
            $to = $to->withHeader(ResponseHeader::CONTENT_TYPE, $renderClass::MIME_TYPE);
            $to->getBody()->write($content);
        }
        elseif (\is_resource($to))
        {
            fwrite($to, $content);
        }

        return $content;
    }

    public function wipeStorage(): void
    {
        $this->getCollectorRegistry()->wipeStorage();
    }
}
