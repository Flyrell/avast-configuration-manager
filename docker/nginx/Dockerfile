FROM nginx:1.21-alpine

ARG NGINX_SYMFONY_SERVER_NAME

COPY nginx.conf /etc/nginx/nginx.conf
COPY sites-available/* /etc/nginx/sites-available/

RUN mkdir -p /etc/nginx/sites-enabled \
    && mkdir -p /etc/nginx/sites-available \
    && ln -s /etc/nginx/sites-available/*.conf /etc/nginx/sites-enabled/

CMD ["nginx", "-g", "daemon off;"]

EXPOSE 80
EXPOSE 443
