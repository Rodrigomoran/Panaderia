<?php
require_once "./php/main.php";
$url="index.php?vista=product_list&page=";
$campos="producto.producto_id,producto.producto_codigo,producto.producto_nombre,producto.producto_precio,producto.producto_stock,producto.producto_foto,producto.categoria_id,producto.usuario_id,categoria.categoria_id,categoria.categoria_nombre,usuario.usuario_id,usuario.usuario_nombre,usuario.usuario_apellido";
$tabla = "";

$usuario = $_SESSION['id'];

$consulta_datos = "SELECT producto.producto_id, producto.producto_codigo, producto.producto_foto, producto.producto_nombre, detalle_carrito.cantidad, detalle_carrito.total FROM detalle_carrito
                   INNER JOIN producto ON detalle_carrito.Id_producto = producto.producto_id
                   INNER JOIN carrito ON detalle_carrito.Id_carrito = carrito.carrito_id
                   INNER JOIN usuario ON carrito.usuario_id = usuario.usuario_id
                   WHERE carrito.usuario_id = " . $usuario;


$conexion = conexion();

$datos = $conexion->query($consulta_datos);
$datos = $datos->fetchAll();

$consulta = $conexion->prepare("SELECT SUM(total) as TOTAL FROM `detalle_carrito` GROUP BY Id_carrito");
$consulta->execute();
$resultado = $consulta->fetch(PDO::FETCH_ASSOC);
$total = $resultado['TOTAL'];

$conexion = null;

if (count($datos) > 0) {
    $contador = 1;
    foreach ($datos as $rows) {
        $tabla .= '
            <article class="media">
                <figure class="media-left">
                    <p class="image is-64x64">';
        if (is_file("./img/producto/" . $rows['producto_foto'])) {
            $tabla .= '<img src="./img/producto/' . $rows['producto_foto'] . '">';
        } else {
            $tabla .= '<img src="./img/producto.png">';
        }
        $tabla .= '</p>
                </figure>
                <div class="media-content">
                    <div class="content">
                        <p>
                            <strong>' . $contador . ' - ' . $rows['producto_nombre'] . '</strong><br>
                            <strong>CODIGO:</strong> ' . $rows['producto_codigo'] . ', <strong>CANTIDAD:</strong> ' . $rows['cantidad'] . ', <strong>TOTAL:</strong> Q' . $rows['total'] . '
                        </p>
                    </div>
                    <div class="has-text-right">
                        <a onclick="Borrar(' . $rows['producto_id'] . ')" class="button is-link is-rounded is-small">BORRAR</a>
                    </div>
                </div>
            </article>

            <hr>

        ';

        $contador++;
    }
    $tabla .= '<div>
    <p> TOTAL A PAGAR: Q' . $total . '
</div>';
} else {
    $tabla .= '<p class="has-text-centered" >No hay registros en su carrito</p>';
}

echo $tabla;
?>

<script>
    function Borrar(producto){
        usuario = '<?php echo $_SESSION['id']; ?>'
        const datosAInsertar = {
        campo1: producto,
        campo2: usuario,
    };
    // Realiza una solicitud AJAX para enviar los datos al servidor.
		const xhr = new XMLHttpRequest();
    xhr.open('POST', './php/borrar_registro.php', true); // 'agregar_registro.php' es el script en el servidor que manejará la inserción.
    xhr.setRequestHeader('Content-Type', 'application/json');

    // Define una función que se ejecutará cuando se complete la solicitud.
    xhr.onload = function () {
        if (xhr.status === 200) {
            // Procesa la respuesta del servidor si es necesario.
            console.log('Registro eliminar con éxito.');
        } else {
            console.error('Error al eliminar el registro.');
        }
    };

    // Envía los datos como JSON al servidor.
	console.log(datosAInsertar);
    xhr.send(JSON.stringify(datosAInsertar));

    location.reload();
	}
</script>
