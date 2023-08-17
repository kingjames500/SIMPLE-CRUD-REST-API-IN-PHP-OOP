-- Active: 1692003512432@@127.0.0.1@3306@api
<?php

require_once __DIR__ . '/config.php';
class Products{
    private $connect;
    public function __construct(){
        $this->connect = new Connection;
    }


//method for returning all types of errors
private function errorMessages($statusCode, $errorMessage){
    $error = array(
        'status' => $statusCode,
        'message' => $errorMessage
    );
    header('HTTP/1.1' . $statusCode. ' ' . $errorMessage);
    return json_encode($error);
    exit();
}

//method for creating a single person onto the database
function createPerson($dataInput){
    $stmt = $this->connect->prepare("INSERT INTO person (name, age, email) VALUES (:name, :age, :email)");
    $stmt->bindParam(':name', $dataInput['name']);
    $stmt->bindParam(':age', $dataInput['age']);
    $stmt->bindParam(':email', $dataInput['email']);

    if (empty($dataInput['name']) || empty($dataInput['age']) || empty($dataInput['email'])) {
        return $this->errorMessages(422, 'All fields are required');
    }
    else {
        if ($stmt->execute()) {
            $success = array(
                'status' => '200 OK',
                'message' => 'Person added successfully'
            );
            header('HTTP/1.1 200 OK');
            return json_encode($success);
        }
        else {
            return $this->errorMessages(500, 'Internal Server Error');
        } 
    }

}
//method for getting all the products from the database
function requestPersonAll(){
    $results = array();
    $data = $this->connect->prepare("SELECT * from person;");
    
    if ($data->execute()) {
        if ($data->rowCount() > 0) {
            while ($outputData = $data->fetch(PDO::FETCH_ASSOC)){
            $results[] = $outputData;
            $prodData = array(
                'status' => 200,
                'message' => 'products fetched successfully',
                'data' => $results
            );
        }
        header('HTTP/1.1 200 OK');
        return json_encode($prodData);
        }
        else {
            $this->errorMessages(404, 'No products found');
        }
    }
    else {
        return $this->errorMessages(500, 'Internal Server Error');
    }
}
//method for creating a single person onto the database


function getPerson($id){
    // if the id is empty, return an error message
    if ($id == null) {
        return $this->errorMessages(422, 'ID is required');
    }
    $stmt = $this->connect->prepare("SELECT * FROM person WHERE id = :id LIMIT 1");
    $stmt->bindParam(':id', $id['id']);

    if ($stmt->execute(array($id['id']))) {

        if ($stmt->rowCount() == 1) {
            $results = array();
            while ($outputData = $stmt->fetch(PDO::FETCH_ASSOC)){
                $results[] = $outputData;
                $personData = array(
                    'status' => 200,
                    'message' =>'single record was fetched successfully',
                    'data' => $results
                );
            }
            header('HTTP/1.1 200 OK');
            return json_encode($personData);
        }
        else {
            return $this->errorMessages(404, 'Person not found');
        }
    }
    else {
        return $this->errorMessages(500, 'Internal Server Error');
    }
}
//method for updating a single person onto the database

function updateRecord(array $id, array $newUpdate){
    $stmt = $this->connect->prepare("UPDATE person SET name = :name, age = :age, email = :email WHERE id = :id");
    $stmt->bindParam(':id', $id['id'], PDO::PARAM_INT);
    $stmt->bindParam(':name', $newUpdate['name'], PDO::PARAM_STR);
    $stmt->bindParam(':age', $newUpdate['age'], PDO::PARAM_INT);
    $stmt->bindParam(':email', $newUpdate['email'], PDO::PARAM_STR);

    if ($id['id'] == null) {
        return $this->errorMessages(422, 'ID is required');
    }
    else {
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                $message = array(
                    'status' => '200 OK',
                    'message' => 'Person updated successfully'
                );
                header('HTTP/1.1 200 OK');
                return json_encode($message);
            }
            else {
                return $this->errorMessages(404, 'Person not found');
            }
        }
        else {
            return $this->errorMessages(500, 'Internal Server Error');
        }
    }
}
//method for deleting a single person onto the database
function deletePerson($id){
    if (empty($id)) {
        return $this->errorMessages(422, 'ID is required');
    }else {
        $stmt = $this->connect->prepare("DELETE FROM person WHERE id = :id");
        $stmt->bindParam(':id', $id['id'], PDO::PARAM_INT);
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                $message = array(
                    'status' => '200 OK',
                    'message' => 'Person deleted successfully'
                );
                header('HTTP/1.1 200 OK');
                return json_encode($message);
            }
            else {
                return $this->errorMessages(404, 'Person not found');
            }
        }
        else {
            return $this->errorMessages(500, 'Internal Server Error');
        }
    }

}
}
