<?php
use Dotenv\Dotenv;

require "../vendor/autoload.php";

$dotenv = Dotenv::create(dirname(__DIR__));
$dotenv->load();

require "../Core/Database.php";