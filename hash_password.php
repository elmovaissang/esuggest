<?php

require_once 'includes/functions.php';
$password = '123456';
$hash = hashPassword($password);
echo "Hash pour '$password' : $hash";
