<?php
$dbHost = 'dbhost';
$dbName = 'dbname';
$dbUser = 'dbuser';
$dbPass = 'dbpass';
$dbConn = pg_connect("host=$dbHost dbname=$dbName user=$dbUser password=$dbPass")
    or die('Could not connect: ' . pg_last_error());
?>
