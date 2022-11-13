#!/bin/bash

php="php"
composer="composer"
env="dev"

server="$1"

if [ "$server" == "contabo" ]; then
    php="$HOME/bin/php81"
    composer="$php $HOME/bin/composer"
fi

if [ "$server" == "attila" ]; then
    php="php81"
    composer="$php $(which composer)"
fi

shift

if [ -n "$1" ]; then
    env="$server"
    server="localhost"
fi

echo "[*] Building for \"$env\" environment on \"$server\""

git restore .
git pull

$composer install

$composer dump-autoload

npm install && npm run $env

if [ "$server" == "attila" ]; then
    echo "[+] Changing permissions to 776 for storage and bootstrap/cache"
    chmod 777 -R bootstrap/cache
#    sudo chmod 777 -R storage
#    sudo chmod 777 -R bootstrap/cache

#    echo "[+] Changing permissions to 776 for sqlite"
#    chmod o+w database/
#    chmod 776 database/*.sqlite

    echo "[+] Check Downloads Dir"
    if [ ! -f "public/downloads" ]; then
        mkdir "public/downloads"
    fi

    chmod 777 "public/downloads"

    echo "[+] Check Generated Templates Dir"
    if [ ! -f "public/generated-templates" ]; then
        mkdir "public/generated-templates"
    fi

    chmod 777 "public/generated-templates"

    echo "[+] Check ./tmp Dir"
    if [ ! -f "tmp" ]; then
        mkdir "tmp"
    fi

    chmod 777 "tmp"

    for trans in `find resources/views/templates/* | grep translations | grep \.json`; do
        echo "chmod 776 $trans"

        chmod 776 "$trans"
    done

    for fields in `find resources/views/templates/* | grep -i "fields.json"`; do
        echo "chmod 776 $fields"

        chmod 776 "$fields"
    done

fi

$php artisan optimize:clear

