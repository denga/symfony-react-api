#syntax=docker/dockerfile:1

FROM node:22-alpine AS base

WORKDIR /app

# -----------------------------------------------------------
# Install dependencies only (layer cache optimisation)
# -----------------------------------------------------------
FROM base AS deps

COPY --link package.json package-lock.json ./
RUN npm ci

# -----------------------------------------------------------
# Development image – used with volume mounts for hot reload
# -----------------------------------------------------------
FROM deps AS dev

ENV NODE_ENV=development

CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]

# -----------------------------------------------------------
# Build the production bundle
# -----------------------------------------------------------
FROM deps AS build

COPY --link . .
RUN npm run build

# -----------------------------------------------------------
# Lean production image – only the compiled output
# -----------------------------------------------------------
FROM base AS production

ENV NODE_ENV=production

RUN addgroup -S appgroup && adduser -S appuser -G appgroup

COPY --from=build --chown=appuser:appgroup /app/.output ./.output

USER appuser

EXPOSE 3000

CMD ["node", ".output/server/index.mjs"]
