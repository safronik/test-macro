#!/bin/bash

# init composer
composer install --ignore-platform-reqs

# as soon as DB is initializing in parallel with application, 
## there is a risk that the working database is not exists when application is trying to connect
sleep 10s

# Copy CLI entry point to /bin
cp macro /bin

# init application
macro Migrations initDB
macro Migrations addTestData