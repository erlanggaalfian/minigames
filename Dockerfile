# Menggunakan Ubuntu 22.04 sebagai dasar image karena fleksibilitasnya.
FROM ubuntu:22.04

# Mencegah dialog interaktif yang bisa menghentikan proses build.
ENV DEBIAN_FRONTEND=noninteractive

# 1. Update & Install semua dependensi yang dibutuhkan.
RUN apt-get update && \
    apt-get install -y \
    apache2 \
    php8.1 \
    libapache2-mod-php8.1 \
    mysql-server \
    php8.1-mysql \
    phpmyadmin \
    dos2unix \
    vim

# 2. Konfigurasi Apache.
# MODIFIKASI: Menggunakan sintaks ENV key=value yang direkomendasikan.
ENV APACHE_DOCUMENT_ROOT=/var/www/minigame/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/minigame!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite
COPY phpmyadmin.apache.conf /etc/apache2/conf-available/phpmyadmin.conf
RUN a2enconf phpmyadmin

# 3. Salin semua file proyek ke lokasi target di dalam container.
COPY . /var/www/minigame/

# 4. Salin dan siapkan skrip startup (entrypoint).
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN dos2unix /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# 5. Atur izin kepemilikan folder.
RUN chown -R www-data:www-data /var/www/minigame

# Port yang akan diekspos oleh container.
EXPOSE 80

# Menetapkan skrip entrypoint sebagai perintah utama saat container dijalankan.
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

