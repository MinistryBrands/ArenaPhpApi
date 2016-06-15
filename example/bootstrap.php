<?php
	
session_start();
error_reporting(E_ALL & ~E_NOTICE);
	
require 'config.php';
require '../Arena.class.php';

$arena = new Arena([
   'domainWithProtocol' => 'https://arena.myshelby.org',
   'apiKey' => $apiKey,
   'apiSecret' => $apiSecret,
   'username' => $username,
   'password' => $password,
]);

?>