FROM node:22-alpine AS build

WORKDIR /app
COPY frontend/web-admin/package.json frontend/web-admin/package-lock.json ./
RUN npm ci

COPY frontend/web-admin/. .
ARG VITE_API_BASE_URL=http://localhost:8000/api/v1
ENV VITE_API_BASE_URL=$VITE_API_BASE_URL
RUN npm run build

FROM nginx:alpine
COPY --from=build /app/dist /usr/share/nginx/html
COPY docker/web-admin/nginx.conf /etc/nginx/conf.d/default.conf
EXPOSE 80
