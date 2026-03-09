import { W3CTraceContextPropagator } from '@opentelemetry/core'
import { OTLPTraceExporter } from '@opentelemetry/exporter-trace-otlp-http'
import { registerInstrumentations } from '@opentelemetry/instrumentation'
import { DocumentLoadInstrumentation } from '@opentelemetry/instrumentation-document-load'
import { FetchInstrumentation } from '@opentelemetry/instrumentation-fetch'
import { resourceFromAttributes } from '@opentelemetry/resources'
import {
  BatchSpanProcessor,
  ParentBasedSampler,
  TraceIdRatioBasedSampler,
} from '@opentelemetry/sdk-trace-base'
import { WebTracerProvider } from '@opentelemetry/sdk-trace-web'
import { ATTR_SERVICE_NAME } from '@opentelemetry/semantic-conventions'
import { ZoneContextManager } from '@opentelemetry/context-zone'

let provider: WebTracerProvider | null = null

export function initBrowserTelemetry(): WebTracerProvider | null {
  if (typeof window === 'undefined' || provider) return provider

  const samplingRatio = Number.parseFloat(
    import.meta.env.VITE_OTEL_SAMPLING_RATIO ?? '0.1',
  )
  const endpoint = import.meta.env.VITE_OTEL_EXPORTER_ENDPOINT ?? '/otlp'
  const serviceName = import.meta.env.VITE_OTEL_SERVICE_NAME ?? 'order-app'

  const resource = resourceFromAttributes({
    [ATTR_SERVICE_NAME]: serviceName,
  })

  const exporter = new OTLPTraceExporter({
    url: `${endpoint}/v1/traces`,
  })

  provider = new WebTracerProvider({
    resource,
    sampler: new ParentBasedSampler({
      root: new TraceIdRatioBasedSampler(samplingRatio),
    }),
    spanProcessors: [new BatchSpanProcessor(exporter)],
  })

  provider.register({
    contextManager: new ZoneContextManager(),
    propagator: new W3CTraceContextPropagator(),
  })

  registerInstrumentations({
    instrumentations: [
      new FetchInstrumentation({
        propagateTraceHeaderCorsUrls: [/.*/],
      }),
      new DocumentLoadInstrumentation(),
    ],
  })

  return provider
}

export function shutdownTelemetry(): Promise<void> {
  if (!provider) return Promise.resolve()
  return provider.shutdown()
}
