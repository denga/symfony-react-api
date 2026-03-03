import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { useRouter } from '@tanstack/react-router'
import * as ordersApi from '#/api/orders'
import type { CreateOrderPayload } from '#/api/types'

export function useOrdersList(params: { page?: number; perPage?: number }) {
  const { page = 1, perPage = 20 } = params
  return useQuery({
    queryKey: ['orders', 'list', page, perPage],
    queryFn: () => ordersApi.listOrders({ page, perPage }),
  })
}

export function useOrder(id: string | undefined) {
  return useQuery({
    queryKey: ['orders', 'detail', id],
    queryFn: () => ordersApi.getOrder(id!),
    enabled: !!id,
  })
}

export function useCreateOrder() {
  const queryClient = useQueryClient()
  const router = useRouter()

  return useMutation({
    mutationFn: (payload: CreateOrderPayload) => ordersApi.createOrder(payload),
    onSuccess: (data) => {
      queryClient.invalidateQueries({ queryKey: ['orders'] })
      router.navigate({ to: '/orders/$orderId', params: { orderId: data.orderId } })
    },
  })
}
