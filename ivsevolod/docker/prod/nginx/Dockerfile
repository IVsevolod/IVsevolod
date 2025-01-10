FROM nginx:1.21.1-alpine

RUN apk add --no-cache curl

WORKDIR /app/public

RUN rm /etc/nginx/conf.d/default.conf
COPY ./nginx/conf.d/*.conf /etc/nginx/conf.d/