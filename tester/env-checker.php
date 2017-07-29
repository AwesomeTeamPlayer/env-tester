#!/usr/bin/env php
<?php

/*
 * This file is responsible for checking test env status.
 * If env is ready this command will return code = 0.
 * If env is NOT ready this command will return code = 1.
 */

$host = $argv[1];
$port = (int) $argv[2];
$username = $argv[3];
$password = $argv[4];
$database = $argv[5];

function isMySqlConnected($host, $port, $username, $password, $database)
{
	$mysqli = @(new mysqli($host, $username, $password, $database, $port));

	echo "\n\nDatabase:\n";
	echo " HOST: " . $host . "\n";
	echo " PORT: " . $port . "\n";
	echo " USERNAME: " . $username. "\n";
	echo " PASSWORD: " . $password . "\n";
	echo " DATABASE: " . $database . "\n";

	if ($mysqli->connect_errno) {
		echo " - Database is not ready";
		return false;
	}

	if ($mysqli->ping()) {
		echo " - Database is ready";
	} else {
		echo " - Can not ping database";
		return false;
	}

	$mysqli->close();

	return true;
}

if (
    isMySqlConnected(
        getenv('PROJECTS_SERVICE_MYSQL_HOST'),
        getenv('PROJECTS_SERVICE_MYSQL_PORT'),
        getenv('PROJECTS_SERVICE_MYSQL_LOGIN'),
        getenv('PROJECTS_SERVICE_MYSQL_PASSWORD'),
        getenv('PROJECTS_SERVICE_MYSQL_DATABASE')
    ) === false
) {
    exit(1);
}


exit(0);
