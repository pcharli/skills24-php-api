<?php

function myPrint_r($value) {
    if($_ENV['MODE'] == 'dev') :
        echo '<pre>';
        print_r($value);
        echo '</pre>';
    endif;
}

function getAuthorization () {
    $headers = getallheaders();
    if(isset($headers['Authorization']) ) :
    //myPrint_r($headers);
    $bearer = explode(' ', $headers['Authorization']);
    //myPrint_r($bearer);
    $token = $bearer[1];
    return $token;
    else : return false;
    endif;
}