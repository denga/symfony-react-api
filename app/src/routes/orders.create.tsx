import { createFileRoute, Link } from '@tanstack/react-router'
import { useForm } from '@tanstack/react-form'
import { Button } from '#/components/ui/button'
import { Input } from '#/components/ui/input'
import { Label } from '#/components/ui/label'
import { useCreateOrder } from '#/hooks/useOrders'
import type { CreateOrderItem } from '#/api/types'

export const Route = createFileRoute('/orders/create')({
  component: OrdersCreate,
})

const defaultItem: CreateOrderItem = { sku: '', quantity: 1, price_cents: 0 }

function OrdersCreate() {
  const createOrder = useCreateOrder()

  const form = useForm({
    defaultValues: {
      customerId: '',
      items: [defaultItem] as CreateOrderItem[],
    },
    onSubmit: async ({ value }) => {
      await createOrder.mutateAsync(value)
    },
  })

  return (
    <main className="page-wrap px-4 pb-8 pt-14">
      <section className="mb-6">
        <Link
          to="/orders"
          className="mb-4 inline-block text-sm text-[var(--lagoon-deep)] hover:underline"
        >
          ← Zurück zur Liste
        </Link>
        <p className="island-kicker mb-2">Neue Order</p>
        <h1 className="display-title m-0 text-4xl font-bold tracking-tight text-[var(--sea-ink)] sm:text-5xl">
          Order anlegen
        </h1>
      </section>

      <form
        onSubmit={(e) => {
          e.preventDefault()
          e.stopPropagation()
          form.handleSubmit()
        }}
        className="island-shell space-y-6 rounded-2xl p-6 sm:p-8"
      >
        <form.Field
          name="customerId"
          validators={{
            onChange: ({ value }) =>
              !value || value.trim() === ''
                ? 'Customer ID erforderlich'
                : undefined,
          }}
        >
          {(field) => (
            <div className="space-y-2">
              <Label htmlFor="customerId">Customer ID</Label>
              <Input
                id="customerId"
                value={field.state.value}
                onChange={(e) => field.handleChange(e.target.value)}
                onBlur={field.handleBlur}
                placeholder="z.B. customer-123"
                className="w-full max-w-md"
              />
              {field.state.meta.isTouched && field.state.meta.errors?.length ? (
                <p className="text-sm text-red-600">
                  {field.state.meta.errors[0]}
                </p>
              ) : null}
            </div>
          )}
        </form.Field>

        <form.Field
          name="items"
          mode="array"
          validators={{
            onChange: ({ value }) =>
              !value?.length
                ? 'Mindestens ein Artikel erforderlich'
                : undefined,
          }}
        >
          {(field) => (
            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <Label>Artikel</Label>
                <Button
                  type="button"
                  variant="outline"
                  size="sm"
                  onClick={() => field.pushValue({ ...defaultItem })}
                >
                  + Artikel hinzufügen
                </Button>
              </div>

              {field.state.value.map((_, i) => (
                <div
                  key={i}
                  className="flex flex-wrap items-end gap-4 rounded-lg border border-[var(--line)] p-4"
                >
                  <form.Field
                    name={`items[${i}].sku`}
                    validators={{
                      onChange: ({ value }) =>
                        !value || value.trim() === ''
                          ? 'SKU erforderlich'
                          : undefined,
                    }}
                  >
                    {(subField) => (
                      <div className="min-w-[120px] flex-1 space-y-1">
                        <Label className="text-xs">SKU</Label>
                        <Input
                          value={subField.state.value}
                          onChange={(e) =>
                            subField.handleChange(e.target.value)
                          }
                          onBlur={subField.handleBlur}
                          placeholder="SKU"
                        />
                        {subField.state.meta.isTouched &&
                        subField.state.meta.errors?.length ? (
                          <p className="text-xs text-red-600">
                            {subField.state.meta.errors[0]}
                          </p>
                        ) : null}
                      </div>
                    )}
                  </form.Field>
                  <form.Field
                    name={`items[${i}].quantity`}
                    validators={{
                      onChange: ({ value }) =>
                        value < 1 ? 'Menge muss positiv sein' : undefined,
                    }}
                  >
                    {(subField) => (
                      <div className="w-24 space-y-1">
                        <Label className="text-xs">Menge</Label>
                        <Input
                          type="number"
                          min={1}
                          value={subField.state.value}
                          onChange={(e) =>
                            subField.handleChange(
                              parseInt(e.target.value, 10) || 0
                            )
                          }
                          onBlur={subField.handleBlur}
                        />
                        {subField.state.meta.isTouched &&
                        subField.state.meta.errors?.length ? (
                          <p className="text-xs text-red-600">
                            {subField.state.meta.errors[0]}
                          </p>
                        ) : null}
                      </div>
                    )}
                  </form.Field>
                  <form.Field
                    name={`items[${i}].price_cents`}
                    validators={{
                      onChange: ({ value }) =>
                        value < 0 ? 'Preis muss >= 0 sein' : undefined,
                    }}
                  >
                    {(subField) => (
                      <div className="w-28 space-y-1">
                        <Label className="text-xs">Preis (Cent)</Label>
                        <Input
                          type="number"
                          min={0}
                          value={subField.state.value}
                          onChange={(e) =>
                            subField.handleChange(
                              parseInt(e.target.value, 10) || 0
                            )
                          }
                          onBlur={subField.handleBlur}
                        />
                        {subField.state.meta.isTouched &&
                        subField.state.meta.errors?.length ? (
                          <p className="text-xs text-red-600">
                            {subField.state.meta.errors[0]}
                          </p>
                        ) : null}
                      </div>
                    )}
                  </form.Field>
                  <Button
                    type="button"
                    variant="destructive"
                    size="sm"
                    disabled={field.state.value.length <= 1}
                    onClick={() => field.removeValue(i)}
                  >
                    Entfernen
                  </Button>
                </div>
              ))}
            </div>
          )}
        </form.Field>

        <form.Subscribe
          selector={(state) => [state.canSubmit, state.isSubmitting]}
        >
          {([canSubmit, isSubmitting]) => (
            <div className="flex gap-3">
              <Button
                type="submit"
                disabled={!canSubmit || isSubmitting}
                className="rounded-full border border-[rgba(50,143,151,0.3)] bg-[rgba(79,184,178,0.14)] px-5 py-2.5 text-sm font-semibold text-[var(--lagoon-deep)] hover:bg-[rgba(79,184,178,0.24)]"
              >
                {isSubmitting ? 'Wird erstellt...' : 'Order erstellen'}
              </Button>
              <Link to="/orders">
                <Button type="button" variant="outline">
                  Abbrechen
                </Button>
              </Link>
            </div>
          )}
        </form.Subscribe>

        {createOrder.isError && (
          <p className="text-sm text-red-600">
            {createOrder.error?.message ?? 'Fehler beim Erstellen'}
          </p>
        )}
      </form>
    </main>
  )
}
