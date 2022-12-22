#!/bin/bash

env="dev"

if [ -n "$1" ]; then
    env="$1"
fi

echo "[*] Building for \"$env\""

git restore .
git pull

composer install

composer dump-autoload

npm install && npm run $env

php artisan migrate

php artisan optimize:clear

