FROM php:7.2-cli
COPY . /var/www/diplom
WORKDIR /diplom
CMD [ "php", "./index.php" ]
