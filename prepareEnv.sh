#!/usr/bin/env bash

mkdir services

cd ./services
git clone https://github.com/AwesomeTeamPlayer/auth-service.git
cd ./auth-service
rm ./.travis.yml
composer install
mkdir ./var/
chmod a+rwx ./var
mkdir ./var/log
mkdir ./var/mysql
mkdir ./var/log/nginx
cd ../../

cd ./services
git clone https://github.com/AwesomeTeamPlayer/ProjectsService.git
cd ./ProjectsService
rm ./.travis.yml
composer install
mkdir ./var/
chmod a+rwx ./var
mkdir ./var/log
mkdir ./var/mysql
mkdir ./var/log/nginx
cd ../

cd ./services
git clone https://github.com/AwesomeTeamPlayer/users-service.git
cd ./users-service
rm ./.travis.yml
composer install
mkdir ./var/
chmod a+rwx ./var
mkdir ./var/log
mkdir ./var/mysql
mkdir ./var/log/nginx
cd ../

#git clone https://github.com/AwesomeTeamPlayer/SourceListener.git
#cd ./SourceListener
#composer install
#cd ../
