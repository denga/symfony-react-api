export const API_BASE_URL =
  typeof window !== 'undefined'
    ? ''
    : (process.env.API_URL ?? 'https://localhost')

export function apiUrl(path: string): string {
  const base = API_BASE_URL.replace(/\/$/, '')
  const p = path.startsWith('/') ? path : `/${path}`
  return `${base}${p}`
}
