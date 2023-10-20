<?php
require_once "./php/main.php";
$url="index.php?vista=product_list&page=";
$campos="producto.producto_id,producto.producto_codigo,producto.producto_nombre,producto.producto_precio,producto.producto_stock,producto.producto_foto,producto.categoria_id,producto.usuario_id,categoria.categoria_id,categoria.categoria_nombre,usuario.usuario_id,usuario.usuario_nombre,usuario.usuario_apellido";
$tabla = "";
$consulta_datos = "SELECT $campos FROM producto
    INNER JOIN categoria ON producto.categoria_id = categoria.categoria_id
    INNER JOIN usuario ON producto.usuario_id = usuario.usuario_id
    ORDER BY producto.producto_nombre ASC";

$conexion = conexion();

$datos = $conexion->query($consulta_datos);
$datos = $datos->fetchAll();

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
                            <strong>CODIGO:</strong> ' . $rows['producto_codigo'] . ', <strong>PRECIO:</strong> $' . $rows['producto_precio'] . ', <strong>STOCK:</strong> ' . $rows['producto_stock'] . ', <strong>CATEGORIA:</strong> ' . $rows['categoria_nombre'] . ', <strong>REGISTRADO POR:</strong> ' . $rows['usuario_nombre'] . ' ' . $rows['usuario_apellido'] . '
                        </p>
                    </div>
                    <div class="has-text-right">
                        <a onclick="Agregar(' . $rows['producto_id'] . ')" class="button is-link is-rounded is-small">Agregar</a>
                    </div>
                </div>
            </article>

            <hr>
        ';

        $contador++;
    }
} else {
    $tabla .= '<p class="has-text-centered" >No hay registros en el sistema</p>';
}

echo $tabla;
?>

<script>
	function Agregar(producto){
		usuario = '<?php echo $_SESSION['id']; ?>'
		const datosAInsertar = {
        campo1: producto,
        campo2: usuario,
    };
		// Realiza una solicitud AJAX para enviar los datos al servidor.
		const xhr = new XMLHttpRequest();
    xhr.open('POST', './php/agregar_registro.php', true); // 'agregar_registro.php' es el script en el servidor que manejará la inserción.
    xhr.setRequestHeader('Content-Type', 'application/json');

    // Define una función que se ejecutará cuando se complete la solicitud.
    xhr.onload = function () {
        if (xhr.status === 200) {
            // Procesa la respuesta del servidor si es necesario.
            console.log('Registro agregado con éxito.');
        } else {
            console.error('Error al agregar el registro.');
        }
    };

    // Envía los datos como JSON al servidor.
	console.log(datosAInsertar);
    xhr.send(JSON.stringify(datosAInsertar));
	}
</script>