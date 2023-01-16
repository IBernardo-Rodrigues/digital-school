<?php

const URL = "http://localhost/digital-school";

const MAINTENANCE = false;

const JWT_KEY = "itsarandomkey";

const DATA_LAYER_CONFIG = [
  "driver" => "mysql",
  "host" => "localhost",
  "port" => "3306",
  "dbname" => "digital_school",
  "username" => "root",
  "passwd" => "",
  "options" => [
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    PDO::ATTR_CASE => PDO::CASE_NATURAL
    ]
  ];

const CACHE_TIME = 10;

const CACHE_DIR = 'C:/wamp/www/digital-school/cache';