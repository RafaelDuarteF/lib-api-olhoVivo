FROM ubuntu:22.04

LABEL maintainer="Rech"

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND noninteractive
ENV TZ=America/Sao_Paulo

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update

RUN apt-get install -y gnupg gosu curl ca-certificates zip unzip git supervisor libcap2-bin python2 dnsutils nano

RUN curl -sS 'https://keyserver.ubuntu.com/pks/lookup?op=get&search=0x14aa40ec0831756756d7f66c4f4ea0aae5267a6c' | gpg --dearmor | tee /etc/apt/keyrings/ppa_ondrej_php.gpg > /dev/null

RUN echo "deb [signed-by=/etc/apt/keyrings/ppa_ondrej_php.gpg] https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy main" > /etc/apt/sources.list.d/ppa_ondrej_php.list

RUN apt-get update

RUN apt-get install -y php8.2-cli php8.2-dev php8.2-curl php8.2-mbstring php8.2-xml php8.2-zip php8.2-bcmath

RUN curl -sLS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

RUN apt-get -y autoremove

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN groupadd --force -g 1000 www_group && useradd -ms /bin/bash --no-user-group -g www_group -u 1000 app

RUN mkdir -p /var/www/html/vendor

COPY start-container /usr/local/bin/start-container
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY composer.json /var/www/html
COPY composer.lock /var/www/html

RUN chmod +x /usr/local/bin/start-container

EXPOSE 80

ENTRYPOINT ["start-container"]