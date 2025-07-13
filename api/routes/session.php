<?php
session_start();

$response = [];

if (isset($_SESSION['admin'])) {
    $response = [
        "logAdmin" => true,
        "usuario" => $_SESSION['admin']
    ];
} else if(isset($_SESSION['user'])) {
    $response = [
        "logUser" => true,
        "usuario" => $_SESSION['user']
    ];
} else {
    $response = [
        "logueado" => false
    ];
}

echo json_encode($response);
?>