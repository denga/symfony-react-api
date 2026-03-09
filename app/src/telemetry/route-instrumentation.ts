import { type Span, SpanStatusCode, trace } from '@opentelemetry/api'
import type { Router } from '@tanstack/react-router'

const TRACER_NAME = 'tanstack-router'

let activeNavigationSpan: Span | undefined

export function instrumentRouter(router: Router<any, any, any>): () => void {
  const tracer = trace.getTracer(TRACER_NAME)

  const unsubBeforeNav = router.subscribe('onBeforeNavigate', (event) => {
    if (activeNavigationSpan) {
      activeNavigationSpan.end()
      activeNavigationSpan = undefined
    }

    const to = event.toLocation
    activeNavigationSpan = tracer.startSpan(`navigate ${to.pathname}`, {
      attributes: {
        'navigation.type': event.pathChanged ? 'push' : 'replace',
        'http.url': to.href,
        'http.route': to.pathname,
        'navigation.path_changed': event.pathChanged,
        'navigation.hash_changed': event.hashChanged,
      },
    })
  })

  const unsubResolved = router.subscribe('onResolved', (event) => {
    if (!activeNavigationSpan) return

    activeNavigationSpan.setStatus({ code: SpanStatusCode.OK })
    activeNavigationSpan.setAttribute(
      'navigation.resolved_url',
      event.toLocation.href,
    )
    activeNavigationSpan.end()
    activeNavigationSpan = undefined
  })

  return () => {
    unsubBeforeNav()
    unsubResolved()
    if (activeNavigationSpan) {
      activeNavigationSpan.end()
      activeNavigationSpan = undefined
    }
  }
}
