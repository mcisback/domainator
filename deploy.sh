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

echo "[$(git log --format="%H" -n 1)][Run Manually]: Deployed at $(date +'%d/%m/%Y %H:%M:%S')" >> .deployed
