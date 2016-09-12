<?php
/**
 * index.php
 *
 * Para tratar de implementar un log de los intentos de ingresos no permitidos a la aplicacion.
 */

// Listado de posibles fuentes para la dirección IP, en orden de prioridad
$fuentes_ip = array (
        "HTTP_X_FORWARDED_FOR",
        "HTTP_X_FORWARDED",
        "HTTP_FORWARDED_FOR",
        "HTTP_FORWARDED",
        "HTTP_X_COMING_FROM",
        "HTTP_COMING_FROM",
        "REMOTE_ADDR" 
);

foreach ( $fuentes_ip as $fuentes_ip ) {
    // Si la fuente existe captura la IP
    if (isset ( $_SERVER [$fuentes_ip] )) {
        $proxy_ip = $_SERVER [$fuentes_ip];
        break;
    }
}

$proxy_ip = (isset ( $proxy_ip )) ? $proxy_ip : @getenv ( "REMOTE_ADDR" );
// Regresa la IP

?>
<html>
<head>
<title>Acceso no autorizado.</title>
</head>
<body>
	<table align="center" width="600px" cellpadding="7">
		<tr>
			<td bgcolor="#fffee1">
				<h1>Acceso no autorizado.</h1>
			</td>
		</tr>
		<tr>
			<td>
				<h3>
					Se ha creado un registro de acceso ilegal desde la
					direcci&oacute;n: <b><? echo $proxy_ip ?></b>.
				</h3>
			</td>
		</tr>
		<tr>
			<td>Si considera que esto es un error por favor comuniquese con el
				administrador del sistema.</td>
		</tr>
		<tr>
			<td style="font-size: 12;"></td>
		</tr>
	</table>
</body>
<html>