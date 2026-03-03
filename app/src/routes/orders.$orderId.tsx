import { createFileRoute, Link } from '@tanstack/react-router'
import { useOrder } from '#/hooks/useOrders'

export const Route = createFileRoute('/orders/$orderId')({
  component: OrderDetail,
})

function OrderDetail() {
  const { orderId } = Route.useParams()
  const { data: order, isPending, isError, error } = useOrder(orderId)

  if (isPending) {
    return (
      <main className="page-wrap px-4 pb-8 pt-14">
        <div className="island-shell rounded-2xl p-8 text-center text-[var(--sea-ink-soft)]">
          Loading order...
        </div>
      </main>
    )
  }

  if (isError || !order) {
    return (
      <main className="page-wrap px-4 pb-8 pt-14">
        <div className="island-shell rounded-2xl p-8 text-center text-red-600">
          {isError ? error?.message ?? 'Failed to load order' : 'Order not found'}
        </div>
        <Link
          to="/orders"
          className="mt-4 inline-block text-[var(--lagoon-deep)] hover:underline"
        >
          ← Zurück zur Liste
        </Link>
      </main>
    )
  }

  return (
    <main className="page-wrap px-4 pb-8 pt-14">
      <section className="mb-6">
        <Link
          to="/orders"
          className="mb-4 inline-block text-sm text-[var(--lagoon-deep)] hover:underline"
        >
          ← Zurück zur Liste
        </Link>
        <p className="island-kicker mb-2">Order Details</p>
        <h1 className="display-title m-0 text-4xl font-bold tracking-tight text-[var(--sea-ink)] sm:text-5xl">
          {order.orderId}
        </h1>
      </section>

      <section className="island-shell rounded-2xl p-6 sm:p-8">
        <dl className="grid gap-4 sm:grid-cols-2">
          <div>
            <dt className="text-sm font-medium text-[var(--sea-ink-soft)]">
              Order ID
            </dt>
            <dd className="mt-1 text-base font-semibold text-[var(--sea-ink)]">
              {order.orderId}
            </dd>
          </div>
          <div>
            <dt className="text-sm font-medium text-[var(--sea-ink-soft)]">
              Customer ID
            </dt>
            <dd className="mt-1 text-base font-semibold text-[var(--sea-ink)]">
              {order.customerId}
            </dd>
          </div>
          <div>
            <dt className="text-sm font-medium text-[var(--sea-ink-soft)]">
              Total
            </dt>
            <dd className="mt-1 text-base font-semibold text-[var(--sea-ink)]">
              {(order.totalCents / 100).toFixed(2)} €
            </dd>
          </div>
          <div>
            <dt className="text-sm font-medium text-[var(--sea-ink-soft)]">
              Paid
            </dt>
            <dd className="mt-1 text-base font-semibold text-[var(--sea-ink)]">
              {order.paid ? 'Yes' : 'No'}
            </dd>
          </div>
        </dl>
      </section>
    </main>
  )
}
