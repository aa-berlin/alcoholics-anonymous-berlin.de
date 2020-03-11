# For docs see https://dockerfile.readthedocs.io/en/latest/content/DockerImages/dockerfiles/php-apache-dev.html
FROM webdevops/php-apache-dev:7.3

RUN curl -sL https://deb.nodesource.com/setup_12.x | bash -
RUN apt-get update
RUN apt-get install -y nodejs
RUN apt-get install -y mariadb-client

# For docs see https://wp-cli.org/
RUN curl -sL https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar > /usr/local/bin/wp
RUN chmod +x /usr/local/bin/wp

WORKDIR /app
