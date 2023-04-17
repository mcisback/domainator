#!/bin/bash

env="dev"
runner="Manually"

if [ -n "$1" ]; then
    env="$1"
    runner="$2"
fi

echo "[*] Building for \"$env\""

git restore .
git pull

composer install

composer dump-autoload

npm install && npm run $env

php artisan migrate

php artisan optimize:clear

php artisan ziggy:generate

echo "[$(git log --format="%H" -n 1)][Run by $runner]: Deployed at $(date +'%d/%m/%Y %H:%M:%S')" >> .deployed
