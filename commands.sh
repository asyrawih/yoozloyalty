#!/bin/bash

# Auto run all commands
git pull origin develop
php artisan autorun:commands
npm run dev
