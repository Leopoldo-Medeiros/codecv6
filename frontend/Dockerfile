# syntax=docker/dockerfile:1
#
# Production image for the Nuxt frontend (Nitro Node server). The API base and
# Sentry DSN are public runtime config — set them as env at run time, not build
# time, so one image promotes across environments. See ../DEPLOYMENT.md.

# ---- Stage 1: build -------------------------------------------------------
FROM node:24-slim AS build
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# ---- Stage 2: runtime -----------------------------------------------------
FROM node:24-slim AS runtime
WORKDIR /app
ENV NODE_ENV=production
ENV NITRO_PORT=3000
ENV NITRO_HOST=0.0.0.0
COPY --from=build /app/.output ./.output
EXPOSE 3000
CMD ["node", ".output/server/index.mjs"]
