import { apiUrl } from '#/lib/api'
import type {
  CreateOrderPayload,
  CreateOrderResponse,
  OrderSummary,
  OrdersListResponse,
} from './types'

export async function listOrders(params: {
  page?: number
  perPage?: number
}): Promise<OrdersListResponse> {
  const { page = 1, perPage = 20 } = params
  const url = `${apiUrl('/api/orders')}?page=${page}&perPage=${perPage}`
  const res = await fetch(url)
  if (!res.ok) {
    throw new Error(`Failed to list orders: ${res.status}`)
  }
  return res.json()
}

export async function getOrder(id: string): Promise<OrderSummary | null> {
  const res = await fetch(apiUrl(`/api/orders/${id}`))
  if (res.status === 404) return null
  if (!res.ok) {
    throw new Error(`Failed to get order: ${res.status}`)
  }
  return res.json()
}

export async function createOrder(
  payload: CreateOrderPayload
): Promise<CreateOrderResponse> {
  const res = await fetch(apiUrl('/api/orders'), {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(payload),
  })
  if (!res.ok) {
    const err = (await res.json().catch(() => ({}))) as {
      error?: { message?: string }
      message?: string
    }
    const message =
      err?.error?.message ?? err?.message ?? `Failed to create order: ${res.status}`
    throw new Error(message)
  }
  return res.json()
}
