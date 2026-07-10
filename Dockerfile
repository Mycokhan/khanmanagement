# 1. Tumia Image rasmi ya PHP yenye Apache (Inafaa kwa mifumo ya PHP ya kawaida)
FROM php:8.2-apache

# 2. Sakinisha Node.js na NPM ndani ya seva ya PHP ili WebSocket iweze kufanya kazi
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# 3. Sakinisha PHP Extensions zinazohitajika kwa ajili ya MySQL (PDO na mysqli)
RUN docker-php-ext-install pdo pdo_mysql mysqli

# 4. Washa Apache Rewrite Module (Inasaidia kama unatumia .htaccess au routing)
RUN a2enmod rewrite

# 5. Weka folda la kazi (Working Directory) la Apache
WORKDIR /var/www/html

# 6. Nakili kodi zako zote za mradi kwenda kwenye seva
COPY . .

# 7. Kama una faili la package.json kwa ajili ya Node.js/WebSocket, sakinisha herufi (dependencies)
RUN if [ -f package.json ]; then npm install; fi

# 8. Fungua Ports: Port 80 (Apache/HTTP) na Port 8080 (WebSocket)
EXPOSE 80 8080

# 9. Amri ya kuwasha Apache na WebSocket Seva kwa pamoja
# Hapa tunatumia script fupi ya shell kuwasha node server.js nyuma ya pazia (&) na kisha kuwasha apache kwa mbele
CMD node server.js & apache2-foreground
