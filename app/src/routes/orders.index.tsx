import {
  createFileRoute,
  Link,
  useSearch,
  useRouter,
} from '@tanstack/react-router'
import {
  createColumnHelper,
  flexRender,
  getCoreRowModel,
  useReactTable,
} from '@tanstack/react-table'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '#/components/ui/table'
import { Button } from '#/components/ui/button'
import { useOrdersList } from '#/hooks/useOrders'
import type { OrderSummary } from '#/api/types'

export const Route = createFileRoute('/orders/')({
  validateSearch: (search: Record<string, unknown>) => ({
    page: Number(search.page) || 1,
    perPage: Number(search.perPage) || 20,
  }),
  component: OrdersIndex,
})

const columnHelper = createColumnHelper<OrderSummary>()

const columns = [
  columnHelper.accessor('orderId', {
    header: 'Order ID',
    cell: (info) => (
      <Link
        to="/orders/$orderId"
        params={{ orderId: info.getValue() }}
        className="font-medium text-[var(--lagoon-deep)] hover:underline"
      >
        {info.getValue()}
      </Link>
    ),
  }),
  columnHelper.accessor('customerId', {
    header: 'Customer ID',
  }),
  columnHelper.accessor('totalCents', {
    header: 'Total',
    cell: (info) => `${(info.getValue() / 100).toFixed(2)} €`,
  }),
  columnHelper.accessor('paid', {
    header: 'Paid',
    cell: (info) => (info.getValue() ? 'Yes' : 'No'),
  }),
]

function OrdersIndex() {
  const router = useRouter()
  const { page, perPage } = useSearch({ from: '/orders/' })
  const { data, isPending, isError, error } = useOrdersList({ page, perPage })

  if (isPending) {
    return (
      <main className="page-wrap px-4 pb-8 pt-14">
        <div className="island-shell rounded-2xl p-8 text-center text-[var(--sea-ink-soft)]">
          Loading orders...
        </div>
      </main>
    )
  }

  if (isError) {
    return (
      <main className="page-wrap px-4 pb-8 pt-14">
        <div className="island-shell rounded-2xl p-8 text-center text-red-600">
          Error: {error?.message ?? 'Failed to load orders'}
        </div>
      </main>
    )
  }

  const { meta, data: orders } = data
  const table = useReactTable({
    data: orders,
    columns,
    getCoreRowModel: getCoreRowModel(),
  })

  return (
    <main className="page-wrap px-4 pb-8 pt-14">
      <section className="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
          <p className="island-kicker mb-2">Orders</p>
          <h1 className="display-title m-0 text-4xl font-bold tracking-tight text-[var(--sea-ink)] sm:text-5xl">
            Order List
          </h1>
        </div>
        <Link to="/orders/create">
          <Button className="rounded-full border border-[rgba(50,143,151,0.3)] bg-[rgba(79,184,178,0.14)] px-5 py-2.5 text-sm font-semibold text-[var(--lagoon-deep)] hover:bg-[rgba(79,184,178,0.24)]">
            Neue Order
          </Button>
        </Link>
      </section>

      <section className="island-shell overflow-hidden rounded-2xl">
        <Table>
          <TableHeader>
            {table.getHeaderGroups().map((headerGroup) => (
              <TableRow key={headerGroup.id}>
                {headerGroup.headers.map((header) => (
                  <TableHead key={header.id}>
                    {header.isPlaceholder
                      ? null
                      : flexRender(
                          header.column.columnDef.header,
                          header.getContext()
                        )}
                  </TableHead>
                ))}
              </TableRow>
            ))}
          </TableHeader>
          <TableBody>
            {table.getRowModel().rows?.length ? (
              table.getRowModel().rows.map((row) => (
                <TableRow key={row.id}>
                  {row.getVisibleCells().map((cell) => (
                    <TableCell key={cell.id}>
                      {flexRender(
                        cell.column.columnDef.cell,
                        cell.getContext()
                      )}
                    </TableCell>
                  ))}
                </TableRow>
              ))
            ) : (
              <TableRow>
                <TableCell
                  colSpan={columns.length}
                  className="h-24 text-center text-[var(--sea-ink-soft)]"
                >
                  No orders found.
                </TableCell>
              </TableRow>
            )}
          </TableBody>
        </Table>

        {meta.totalPages > 1 && (
          <div className="flex flex-wrap items-center justify-between gap-4 border-t border-[var(--line)] px-4 py-3">
            <p className="text-sm text-[var(--sea-ink-soft)]">
              Page {meta.page} of {meta.totalPages} ({meta.total} total)
            </p>
            <div className="flex gap-2">
              <Button
                variant="outline"
                size="sm"
                disabled={page <= 1}
                className="rounded-full"
                onClick={() =>
                  router.navigate({
                    to: '/orders',
                    search: { page: page - 1, perPage },
                  })
                }
              >
                Previous
              </Button>
              <Button
                variant="outline"
                size="sm"
                disabled={page >= meta.totalPages}
                className="rounded-full"
                onClick={() =>
                  router.navigate({
                    to: '/orders',
                    search: { page: page + 1, perPage },
                  })
                }
              >
                Next
              </Button>
            </div>
          </div>
        )}
      </section>
    </main>
  )
}
