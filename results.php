<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');
include_once __DIR__ . '/OOP CRUD.php';

$serverMethod = $_SERVER['REQUEST_METHOD'];

$res = new Products;

//error message to be returned if a user tries to use a method that is not allowed
function serverMethodNotAllowed($serverMethod) {
    $error = array(
        'status' => 405,
        'message' => $serverMethod . ' Method Not Allowed'
    );
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode($error);
}


switch ($serverMethod) {
    case 'GET':
        if (isset($_GET['id'])) {
            echo $res->getPerson($_GET);
        }
        else {
            echo $res->requestPersonAll();
        }
        break;

    case 'POST':
        $dataInput = json_decode(file_get_contents("php://input"), true);
        echo $res->createPerson($dataInput);
        break;

    case 'PUT':
            $dataUpdate = json_decode(file_get_contents("php://input"), true);
            if (empty($dataUpdate)) {
                $personUpdate = $res->updateRecord($_GET, $_POST);
            }
            else {
                $personUpdate = $res->updateRecord($_GET, $dataUpdate);
            }
            echo $personUpdate;
            break;
    case 'DELETE':
            if (isset($_GET['id'])) {
                echo $res->deletePerson($_GET);
            }
            else {
                $error = array(
                    'status' => 422,
                    'message' => 'ID required'
                );
                header('HTTP/1.1 422 Unprocessable Entity');
                echo json_encode($error);   
            }
            break;
    default:
        serverMethodNotAllowed($serverMethod);
        break;
}