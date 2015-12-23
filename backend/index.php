<?php
/**
 * Created by PhpStorm.
 * User: raiym
 * Date: 12/23/15
 * Time: 12:09 PM
 */

echo 'Hello world!';
$pg_credentials = "host=ec2-107-21-223-110.compute-1.amazonaws.com port=5432 dbname=d9drs0g01eqeir user=xdmfdolmqushkf password=iBwpLgt1wIhrSZa5cPi7FIW_Op sllmode=require";
$db = pg_connect($pg_credentials);

if (!$db) {
    echo 'Database connection error';
    exit;
}

echo 'Connection is alive';
// postgres://xdmfdolmqushkf:iBwpLgt1wIhrSZa5cPi7FIW_Op@ec2-107-21-223-110.compute-1.amazonaws.com:5432/d9drs0g01eqeir