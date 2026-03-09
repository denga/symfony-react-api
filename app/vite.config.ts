import { defineConfig } from 'vite'
import { devtools } from '@tanstack/devtools-vite'
import tsconfigPaths from 'vite-tsconfig-paths'

import { tanstackStart } from '@tanstack/react-start/plugin/vite'

import viteReact from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'

const otelDeps = [
  '@opentelemetry/api',
  '@opentelemetry/core',
  '@opentelemetry/resources',
  '@opentelemetry/sdk-trace-base',
  '@opentelemetry/sdk-trace-web',
  '@opentelemetry/exporter-trace-otlp-http',
  '@opentelemetry/instrumentation',
  '@opentelemetry/instrumentation-fetch',
  '@opentelemetry/instrumentation-document-load',
  '@opentelemetry/semantic-conventions',
  '@opentelemetry/context-zone',
]

const config = defineConfig({
  server: {
    allowedHosts: true,
  },
  optimizeDeps: {
    include: otelDeps,
  },
  ssr: {
    optimizeDeps: {
      include: otelDeps,
    },
  },
  plugins: [
    devtools(),
    tsconfigPaths({ projects: ['./tsconfig.json'] }),
    tailwindcss(),
    tanstackStart(),
    viteReact(),
  ],
})

export default config
