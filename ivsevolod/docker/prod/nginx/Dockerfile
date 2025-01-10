FROM nginx:1.21.1-alpine

RUN apk add --no-cache curl

WORKDIR /app/public

RUN rm /etc/nginx/conf.d/default.conf
COPY ./ivsevolod/docker/prod/nginx/conf.d/nginx.conf /etc/nginx/conf.d/