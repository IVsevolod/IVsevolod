FROM nginx:1.21.1-alpine AS nginx

RUN apk add --no-cache curl nano

WORKDIR /app

RUN rm /etc/nginx/conf.d/default.conf
COPY ./gateway/nginx/prod/conf.d/http.conf /etc/nginx/conf.d/http.conf