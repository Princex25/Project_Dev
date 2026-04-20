FROM webdevops/php-apache:8.2

ENV WEB_DOCUMENT_ROOT=/app

WORKDIR /app
COPY . /app/admin2

RUN rm -f /etc/apache2/mods-enabled/mpm_event.* \
    && ln -s /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load \
    && ln -s /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

RUN mkdir -p /app/admin2/uploads \
    && chown -R application:application /app/admin2/uploads

EXPOSE 80
