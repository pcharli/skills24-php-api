<?php

$data = file_get_contents('http://localhost/skills24-php-api/');

print_r(json_decode($data, true));

$data2 = file_get_contents('file.xml');
echo '<pre>';
print_r(new SimpleXMLElement($data2));