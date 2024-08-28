<?php
    require('config.php');

      
    //si pas de route , on affiche la doc
    if(!isset($_GET['route'])):
        $response['message'] = 'documentation';
        $response['contenu'] = 'blablab';
        echo json_encode($response);
        die();
    endif;
    
    //validation des routes
    $route = $_GET['route'];
    $routes_valides = ['clients', 'commandes', 'pays', 'adresses', 'localities', 'produits', 'cities'];
    $methods_valides = ['GET', 'POST', 'DELETE', 'PUT'];

    $routes_vues = [
        'produits' => "produits_categories",
        'cities' => 'cities_regions_pays'
    ];
 

    if (!in_array($route, $routes_valides)) :
        $response['message'] = 'Not found';
        $response['code'] = 404;
        echo json_encode($response);
        http_response_code(404);
        die();
    endif;

    if(isset($routes_vues[$route])) :
        $route = $routes_vues[$route];
    endif;

    //validation des méthodes
    if (!in_array($_SERVER['REQUEST_METHOD'], $methods_valides)) :
        $response['message'] = 'Method not allowed';
        $response['code'] = 405;
        echo json_encode($response);
        http_response_code(405);
        die();
    endif;

//security
    if($_SERVER['REQUEST_METHOD'] != 'GET') :
        if(!isset($_SESSION['token'])) :
            $response['message'] = "Vous n'êtes pas connecté";
            http_response_code(403);
            echo json_encode($response);
            die();
        elseif (time() > $_SESSION['expiration']) :
            $response['message'] = "Session expirée";
            http_response_code(403);
            echo json_encode($response);
            die();
            else :
                $token = getAuthorization();
                if($token != $_SESSION['token']):
                    $response['message'] = "Token invalide";
                    http_response_code(403);
                    echo json_encode($response);
                    die();
                endif;
        endif;
    endif;

//routeur

$args = [];
switch($_SERVER['REQUEST_METHOD']) :
    case 'GET' :
        $sql = "SELECT * FROM $route";
        if (isset($_GET['id'])) :
            $sql .= " WHERE ID = :id";
            $args['id'] = $_GET['id'];
        endif;
        $response['message'] = "Contenu : $route";
    break;
    case 'POST' :
        $data = json_decode(file_get_contents('php://input'), true);
        unset($data['token']);
        //myPrint_r($data);
        
        $sql = "INSERT INTO $route SET ";
        foreach($data AS $field => $value) :
            $sql .= "$field = :$field,";
            $args[$field] = $value;
        endforeach;
        $sql = rtrim($sql,',');
        $response['message'] = "$route ajouté";
    break;
    case 'DELETE' :
        if(!isset($_GET['id'])) :
            $response['message'] = "Il manque un id";
            echo json_encode($response);
            http_response_code(405);
            die();
        endif;
        $sql = "DELETE FROM $route WHERE ID = :id";
        $args['id'] = $_GET['id'];
        $response['message'] = "Suppression $route";
    break;    
    case 'PUT' :
        if(!isset($_GET['id'])) :
            $response['message'] = "Il manque un id";
            echo json_encode($response);
            http_response_code(405);
            die();
        endif;
        $data = json_decode(file_get_contents('php://input'), true);
        unset($data['token']);
        
        $sql = "UPDATE $route SET ";
        foreach($data AS $field => $value) :
            $sql .= "$field = :$field,";
            $args[$field] = $value;
        endforeach;
        $sql = rtrim($sql,',');
        $sql .=" WHERE ID = :id";
        $args['id'] = $_GET['id'];
        $response['message'] = "$route modifé";
    break;
endswitch;

    $rq = $db-> prepare($sql);
    try {
        $rq->execute($args);
    }
    catch (Exception $e){
        if(stristr($e, '1451')) :
            $response['message'] = "1451 : Impossible du supprimer 1 $route utilisé ailleurs";
        else :
        $response['message'] = "Une erreur est survenue : $e";
        endif;
        http_response_code(500);
    }
    
    //myPrint_r($rq);

    if($_SERVER['REQUEST_METHOD'] == 'GET') :
        $response['nb_hits'] = $rq->rowCount();
        $rows = $rq->fetchAll(PDO::FETCH_ASSOC);
        $response['data'] = $rows;
    endif;

    if($_SERVER['REQUEST_METHOD'] == 'POST') :
        $response['id'] = $db->lastInsertId();
    endif;
    echo json_encode($response);