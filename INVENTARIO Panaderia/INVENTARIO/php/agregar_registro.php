<?php
// Recibe los datos enviados por JavaScript (puede ser mediante POST, GET u otros métodos).
$datosAInsertar = json_decode(file_get_contents("php://input"), true);
$parametro1 = $datosAInsertar["campo1"];
$parametro2 = $datosAInsertar["campo2"];

// Conexión a la base de datos y código para procesar la solicitud
require_once "main.php";
$conexion=conexion();

$consulta = $conexion->prepare("SELECT COUNT(*) AS total FROM carrito WHERE usuario_id = :usuario");
$consulta->bindParam(":usuario", $parametro2);
$consulta->execute();

$resultado = $consulta->fetch(PDO::FETCH_ASSOC);
$totalRegistros = $resultado['total'];

// Inserta el registro en la base de datos
// Por ejemplo:
if ($totalRegistros > 0) {
    $cantidad = 1;
    // Ya existe un registro con los mismos parámetros, devuelve un mensaje de error o realiza alguna acción correspondiente.
    $consulta = $conexion->prepare("SELECT carrito_id FROM carrito WHERE usuario_id = :usuario");
    $consulta->bindParam(":usuario", $parametro2);
    $consulta->execute();
    $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
    $carrito_id = $resultado['carrito_id'];

    $consulta = $conexion->prepare("SELECT producto_precio FROM producto WHERE producto_id = :producto");
    $consulta->bindParam(":producto", $parametro1);
    $consulta->execute();
    $resultado2 = $consulta->fetch(PDO::FETCH_ASSOC);
    $total = $resultado2['producto_precio'];

    $cantidadPrecio = $total * $cantidad;

    $consulta3 = $conexion->prepare("SELECT COUNT(*) AS total FROM detalle_carrito WHERE Id_carrito = :carrito AND Id_producto = :producto");
    $consulta3->bindParam(":carrito", $carrito_id);
    $consulta3->bindParam(":producto", $parametro1);
    $consulta3->execute();
    $resultado3 = $consulta3->fetch(PDO::FETCH_ASSOC);
    $total3 = $resultado3['total'];

    if($total3 > 0){
        $consulta4 = $conexion->prepare("SELECT total,cantidad FROM detalle_carrito WHERE Id_carrito = :carrito AND Id_producto = :producto");
        $consulta4->bindParam(":carrito", $carrito_id);
        $consulta4->bindParam(":producto", $parametro1);
        $consulta4->execute();
        $resultado4 = $consulta4->fetch(PDO::FETCH_ASSOC);
        $total4 = $resultado4['total'];
        $total5 = $resultado4['cantidad'];

        $total6 = $total4 + $cantidadPrecio;
        $cantidad2 = $total5 + 1;

        $update = $conexion->prepare("UPDATE detalle_carrito SET total = :total , cantidad = :cantidad WHERE Id_carrito = :carrito AND Id_producto = :producto");
        $update->bindParam(":total", $total6);
        $update->bindParam(":cantidad", $cantidad2);
        $update->bindParam(":carrito", $carrito_id);
        $update->bindParam(":producto", $parametro1);
        if ($update->execute()) {
            echo 'Registro actualizado con éxito.';
        } else {
            echo 'Error al actualizar el registro.';
        }

    }else{
        $insercion = $conexion->prepare("INSERT INTO detalle_carrito (Id_carrito,Id_producto,cantidad,total) VALUES (:carrito,:producto,:cantidad,:total)");
        $insercion->bindParam(":carrito", $carrito_id);
        $insercion->bindParam(":producto",$parametro1);
        $insercion->bindParam(":cantidad",$cantidad);
        $insercion->bindParam(":total",$cantidadPrecio);

        if ($insercion->execute()) {
            echo 'Registro agregado con éxito.';
        } else {
            echo 'Error al agregar el registro.';
        }
    }

} else {
    // No existe un registro con los mismos parámetros, procede a realizar la inserción.
    $insercion = $conexion->prepare("INSERT INTO carrito (usuario_id) VALUES (:usuario)");
    $insercion->bindParam(":usuario", $parametro2);

    if ($insercion->execute()) {
        echo 'Registro agregado con éxito.';
    } else {
        echo 'Error al agregar el registro.';
    }

    $cantidad = 1;
    // Ya existe un registro con los mismos parámetros, devuelve un mensaje de error o realiza alguna acción correspondiente.
    $consulta = $conexion->prepare("SELECT carrito_id FROM carrito WHERE usuario_id = :usuario");
    $consulta->bindParam(":usuario", $parametro2);
    $consulta->execute();
    $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
    $carrito_id = $resultado['carrito_id'];

    $consulta = $conexion->prepare("SELECT producto_precio FROM producto WHERE producto_id = :producto");
    $consulta->bindParam(":producto", $parametro1);
    $consulta->execute();
    $resultado2 = $consulta->fetch(PDO::FETCH_ASSOC);
    $total = $resultado2['producto_precio'];

    $cantidadPrecio = $total * $cantidad;

    $insercion = $conexion->prepare("INSERT INTO detalle_carrito (Id_carrito,Id_producto,cantidad,total) VALUES (:carrito,:producto,:cantidad,:total)");
    $insercion->bindParam(":carrito", $carrito_id);
    $insercion->bindParam(":producto",$parametro1);
    $insercion->bindParam(":cantidad",$cantidad);
    $insercion->bindParam(":total",$cantidadPrecio);

    if ($insercion->execute()) {
        echo 'Registro agregado con éxito.';
    } else {
        echo 'Error al agregar el registro.';
    }
}
// Asegúrate de validar y escapar los datos para evitar problemas de seguridad.

// Devuelve una respuesta (puede ser un mensaje de éxito o error) al cliente.
// Por ejemplo:
// echo 'Registro agregado con éxito.';
?>