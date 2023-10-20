<?php
// Recibe los datos enviados por JavaScript (puede ser mediante POST, GET u otros métodos).
$datosAInsertar = json_decode(file_get_contents("php://input"), true);
$parametro1 = $datosAInsertar["campo1"];
$parametro2 = $datosAInsertar["campo2"];

// Conexión a la base de datos y código para procesar la solicitud
require_once "main.php";
$conexion=conexion();

$obtener_carrito = $conexion->prepare("SELECT carrito_id FROM carrito WHERE usuario_id = :usuario");
$obtener_carrito->bindParam(":usuario", $parametro2);
$obtener_carrito->execute();

$respuesta = $obtener_carrito->fetch(PDO::FETCH_ASSOC);
$carrito_id = $respuesta['carrito_id'];

$consulta = $conexion->prepare("DELETE FROM detalle_carrito WHERE Id_carrito = :carrito AND Id_producto = :producto");
$consulta->bindParam(":carrito", $carrito_id);
$consulta->bindParam(":producto", $parametro1);
if ($consulta->execute()) {
    echo 'Registro eliminado con éxito.';
} else {
    echo 'Error al eliminar el registro.';
}



?>