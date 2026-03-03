export type OrderSummary = {
  orderId: string
  customerId: string
  totalCents: number
  paid: boolean
}

export type CreateOrderItem = {
  sku: string
  quantity: number
  price_cents: number
}

export type CreateOrderPayload = {
  customerId: string
  items: CreateOrderItem[]
}

export type OrdersListResponse = {
  meta: {
    total: number
    page: number
    perPage: number
    totalPages: number
  }
  data: OrderSummary[]
}

export type CreateOrderResponse = {
  orderId: string
  orderUrl: string
}
