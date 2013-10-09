#!/bin/bash

# Install necessary packages
sudo add-apt-repository -y ppa:chris-lea/node.js
sudo apt-get update -y
sudo apt-get install -y git python-software-properties build-essential libxml2-dev nodejs redis-server php5 php5-curl
sudo npm install -g apiaxle-repl apiaxle-proxy apiaxle-api

# Start ApiAxle processes
apiaxle-proxy -p 3000 &
sleep 3
apiaxle-api -p 8000 &
sleep 3

# Create non-shared folder for running tests
sudo mkdir /apiaxle
sudo cp -R /vagrant/* /apiaxle/
sudo chown -R vagrant:vagrant /apiaxle

# Reuse travis configuration to provision apiaxle api and key
cd /apiaxle
./travis.setup.sh

# Update composer dependencies
cd /apiaxle
php composer.phar self-update
php composer.phar update

# Run unit tests
cd /apiaxle
./vendor/bin/phpunit tests/