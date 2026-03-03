export const API_BASE_URL =
  import.meta.env.VITE_API_URL ?? (typeof window !== 'undefined' ? '' : 'https://localhost')

export function apiUrl(path: string): string {
  const base = API_BASE_URL.replace(/\/$/, '')
  const p = path.startsWith('/') ? path : `/${path}`
  return `${base}${p}`
}
