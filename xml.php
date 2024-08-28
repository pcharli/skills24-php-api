<?php
include('xml-parser.php');
header("Content-Type:application/xml");

$array = [
    'nom' => 'Charlier',
    'prenom' => 'Pierre',
    'hobbies' => [
        'tennis', 'php'
    ]
];
$xml = XMLParser::encode(array(
    'bla' => 'blub',
    'foo' => 'bar',
    'another_array' => array (
        'stack' => 'overflow'
        )
    ));
    exit;
// @$xml instanceof SimpleXMLElement
echo $xml->asXML();