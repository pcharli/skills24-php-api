<?php
require 'config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'):
    $data = json_decode(file_get_contents('php://input'), true);
    $sql = "SELECT * FROM users WHERE login = :login AND password = :password";

    $rq = $db->prepare($sql);
    $rq->execute([
        "login" => $data['login'],
        "password" => md5($data['password'])
    ]);
    if($rq->rowCount() > 0) :
    $row = $rq->fetch(PDO::FETCH_ASSOC); 
    unset($row['password']);
    //$row['password'] = '';
    //myPrint_r($row);
    $response['data'] = $row;
    $response['message'] = "auth";
    //gestion de la session
    $_SESSION['user']['id'] = $row['ID'];
    $_SESSION['user']['niveau'] = $row['niveau'];
    $_SESSION['token'] = md5(date("DdMYHis"));
    $_SESSION['expiration'] = time() + 1 * 3000;
    $response['token'] = $_SESSION['token'];
    else :
        $response['message'] = "Erreur de login/password";
        http_response_code(401);
    endif;
    elseif($_SERVER['REQUEST_METHOD'] == "DELETE"):
        unset($_SESSION['user']);
        unset($_SESSION['token']);
        unset($_SESSION['expiration']);
        $response['message'] = "Déconnexion";
    else : 
        $response['message'] = "Method not allowed";
        http_response_code(405);
endif;
echo json_encode($response);