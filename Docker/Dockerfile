FROM webdevops/php-apache:8.2

# Document root = répertoire parent des sous-projets
ENV WEB_DOCUMENT_ROOT=/app \
    APPLICATION_PATH=/app \
    WEB_ALIAS_DOMAIN=devops-project.*

WORKDIR /app

# Créer un index.php à la racine qui redirige vers le login
RUN echo '<?php header("Location: /devops-project/php-login/index.php"); exit(); ?>' > /app/index.php

# Copier chaque portail dans le répertoire web
COPY App/Demander      /app/devops-project/Demander
COPY App/Validateur    /app/devops-project/Validateur
COPY App/admin         /app/devops-project/admin
COPY App/php-login     /app/devops-project/php-login
COPY App/shared        /app/devops-project/shared

RUN rm -f /etc/apache2/mods-enabled/mpm_event.* \
    && ln -s /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load \
    && ln -s /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

RUN mkdir -p /app/devops-project/uploads \
    && chown -R application:application /app/devops-project/uploads

EXPOSE 80