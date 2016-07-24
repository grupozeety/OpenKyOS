<?php
/**
 * Formulario para el ingreso de los datos de conexión a la base de datos principal.
 *
 * @author	Paulo Cesar Coronado
 * @version	0.0.0.2, 25/03/2012
 * @package 	framework:BCK:instalacion
 * @copyright Universidad Distrital F.J.C
 * @license	GPL Version 3.0 o posterior
 *
 */
$indice = strpos ( $_SERVER ["REQUEST_URI"], "/index.php" );

if ($indice === false) {
    $indice = strpos ( $_SERVER ["REQUEST_URI"], "/", 1 );
}
$sitio = substr ( $_SERVER ["REQUEST_URI"], 0, $indice );

// Validacion
$formulario = "variables";
$validar = "control_vacio(" . $formulario . ",'dbdns','Direcci&oacute;n Servidor de Bases de Datos')";
$validar .= "&&control_vacio(" . $formulario . ",'dbnombre','Nombre de la base de datos')";
$validar .= "&&control_vacio(" . $formulario . ",'dbusuario','Usuario de la base de datos')";
$validar .= "&&control_vacio(" . $formulario . ",'dbclave','Clave de acceso a la base de datos')";
$validar .= "&&control_vacio(" . $formulario . ",'dbsys','Sistema de Gesti&oacute;n de la Base de Datos')";
$validar .= "&&control_vacio(" . $formulario . ",'dbpuerto','Puerto de acceso al servidor de Bases de Datos')";
$validar .= "&&control_vacio(" . $formulario . ",'nombreAplicativo','Nombre del Aplicativo')";
$validar .= "&&control_vacio(" . $formulario . ",'raizDocumento','Directorio raiz de los documentos')";
$validar .= "&&control_vacio(" . $formulario . ",'host','Direcci&oacute;n (URL) ra&iacute;z del servidor')";
$validar .= "&&control_vacio(" . $formulario . ",'site','Carpeta del sitio en el servidor')";
$validar .= "&&control_vacio(" . $formulario . ",'nombreAdministrador','Usuario administrador del aplicativo')";
$validar .= "&&control_vacio(" . $formulario . ",'claveAdministrador','Clave del administrador')";
$validar .= "&&control_vacio(" . $formulario . ",'correoAdministrador','Correo del administrador')";
$validar .= "&&control_vacio(" . $formulario . ",'enlace','Nombre del par&aacute;metro GET')";
$validar .= "&&verificar_correo(" . $formulario . ",'correoAdministrador')";

$tab = 0;
?>
<html>
<head>
<title>Informaci&oacute;n de Conexi&oacute;n a la Base de Datos</title>
<meta content="text/html;" http-equiv="content-type" charset="utf-8">
<script>
<?php include_once "install/funciones.js"?>
</script>
<style type="text/css">
body {
	font-family: "Lucida Grande", "Lucida Sans Unicode", Verdana, Arial,
		Helvetica, sans-serif;
	font-size: 12px;
}

p,h1,form,button {
	border: 0;
	margin: 0;
	padding: 0;
}

.spacer {
	clear: both;
	height: 10px;
}
/* ----------- My Form ----------- */
.miFormulario {
	margin: 0 auto;
	width: 600px;
	padding: 14px;
}

/* ----------- formulario ----------- */
#formulario {
	border: solid 2px #b7ddf2;
	background: #ebf4fb;
}

#formulario h1 {
	font-size: 14px;
	font-weight: bold;
	margin-bottom: 8px;
}

#formulario p {
	font-size: 11px;
	color: #666666;
	margin-bottom: 20px;
	border-bottom: solid 1px #b7ddf2;
	padding-bottom: 10px;
}

#formulario label {
	display: block;
	font-weight: bold;
	text-align: right;
	width: 300px;
	float: left;
}

#formulario .textoPequenno {
	color: #666666;
	display: block;
	font-size: 11px;
	font-weight: normal;
	text-align: right;
	width: 300px;
}

#formulario input {
	float: left;
	font-size: 12px;
	padding: 4px 2px;
	border: solid 1px #aacfe4;
	width: 250px;
	margin: 2px 0 20px 10px;
}

#formulario select {
	float: left;
	font-size: 12px;
	padding: 4px 2px;
	border: solid 1px #aacfe4;
	width: 150px;
	margin: 2px 0 20px 10px;
}

#formulario input.button {
	clear: both;
	margin-left: 250px;
	width: 125px;
	height: 31px;
	background: #666666 url(img/button.png) no-repeat;
	text-align: center;
	line-height: 31px;
	color: #FFFFFF;
	font-size: 11px;
	font-weight: bold;
}

.clean-red {
	margin: 0 auto;
	width: 600px;
	padding: 20px;
	border: solid 1px #FF0000;
	background: #F6CBCA;
	color: #FF0000;
	text-align: center;
}
</style>
</head>
<body>


<?php
if (isset ( $_REQUEST ["instalador"] )) {
    ?>
	<div class="clean-red"><?php echo $mensajeError?></div>	
	<?php
}
?>
	<div id="formulario" class="miFormulario">
		<form id="<?php
echo $formulario;
?>"
			name="<?php
echo $formulario;
?>" method="post" action="index.php"
			accept-charset="UTF-8">
			<h1>Informaci&oacute;n de Conexi&oacute;n</h1>
			<p>Informaci&oacute;n de conexi&oacute;n a la base de datos principal
				del aplicativo.</p>
			<label>Tipo: <span class="textoPequenno">Motor de DB a utilizar</span></label>
			<select id="dbsys" name="dbsys">
				<option value="mysql">Mysql</option>
				<option value="pgsql">PostgreSQL</option>
				<option value="oracle">Oracle</option>
			</select> <label>Servidor <span class="textoPequenno">DNS del
					servidor de base de datos</span></label> <input type="text"
				name="dbdns" id="dbDns"
				value="<?php
    if (isset ( $_REQUEST ["dbdns"] )) {
        echo $_REQUEST ["dbdns"];
    } else {
        echo "localhost";
    }
    ?>" /> <label>Puerto <span class="textoPequenno">Puerto de acceso al
					servidor de base de datos</span></label> <input type="text"
				name="dbpuerto" id="dbPuerto"
				value="<?php
    if (isset ( $_REQUEST ["dbpuerto"] )) {
        echo $_REQUEST ["dbpuerto"];
    } else {
        echo "0";
    }
    ?>" /> <label>Esquema <span class="textoPequenno">Dejar vacio si hay un único esquema
    </span></label> <input type="text"
				name="dbesquema" id="dbesquema"
				value="<?php
    
    if (isset ( $_REQUEST ["dbesquema"] )) {
        echo $_REQUEST ["dbesquema"];
    }
    ?>" />  <label>Nombre <span class="textoPequenno">Nombre de la base
					de datos principal</span></label> <input type="text"
				name="dbnombre" id="dbNombre"
				value="<?php
    
    if (isset ( $_REQUEST ["dbnombre"] )) {
        echo $_REQUEST ["dbnombre"];
    }
    ?>" /> <label>Usuario <span class="textoPequenno">Usuario de la base
					de datos principal</span>
			</label> <input type="text" name="dbusuario" id="dbUsuario"
				value="<?php
    if (isset ( $_REQUEST ["dbusuario"] )) {
        echo $_REQUEST ["dbusuario"];
    } else {
        echo "";
    }
    ?>" /> <label>Clave <span class="textoPequenno">Clave de acceso a la
					base de datos.</span>
			</label> <input type="password" name="dbclave" id="dbClave" /> <label>Prefijo
				<span class="textoPequenno">Prefijo para las tablas de la base de
					datos</span>
			</label> <input type="text" name="prefijo" id="prefijo"
				value="<?php
    if (isset ( $_REQUEST ["prefijo"] )) {
        echo $_REQUEST ["prefijo"];
    } else {
        echo "aplicativo_";
    }
    ?>" />

			<div class="spacer"></div>
			<div class="spacer">
				<input type="hidden" name="instalador" id="instalador" />
			</div>
			<div class="spacer"></div>

			<h1>Configuraci&oacute;n del Aplicativo</h1>
			<p>Datos de configuraci&oacute;n del aplicativo.</p>

			<label>Nombre del Aplicativo <span class="textoPequenno">M&aacute;ximo
					255 letras</span></label> <input type="text"
				name="nombreAplicativo" id="nombreAplicativo"
				value="<?php
    if (isset ( $_REQUEST ["nombreAplicativo"] )) {
        echo $_REQUEST ["nombreAplicativo"];
    } else {
        echo "";
    }
    ?>" /> <label>Directorio ra&iacute;z en el servidor <span
				class="textoPequenno">En caso de duda preguntar al administrador de
					su servidor.</span>
			</label> <input type="text" name="raizDocumento" id="raizDocumento"
				value="<?php echo $_SERVER["DOCUMENT_ROOT"]?>" /> <label>Direcci&oacute;n
				(URL) ra&iacute;z del servidor <span class="textoPequenno">Ej:
					http://mi_servidor, sin subdirectorios</span>
			</label> <input type="text" name="host" id="host"
				value="<?php echo "http://".$_SERVER["HTTP_HOST"]?>" /> <label>Carpeta
				del aplicativo <span class="textoPequenno">Ej: /aplicativo</span>
			</label> <input type="text" name="site" id="site"
				value="<?php echo $sitio?>" /> <label>Usuario Administrador <span
				class="textoPequenno">Con privilegios generales sobre el aplicativo.</span></label>
			<input type="text" name="nombreAdministrador"
				id="nombreAdministrador"
				value="<?php
    if (isset ( $_REQUEST ["nombreAdministrador"] )) {
        echo $_REQUEST ["nombreAdministrador"];
    } else {
        echo "administrador";
    }
    ?>" /> <label>Clave Administrador <span class="textoPequenno">Clave
					de acceso del administrador.</span>
			</label> <input type="password" name="claveAdministrador"
				id="claveAdministrador" /> <label>Correo Administrador <span
				class="textoPequenno">e-mail del administrador.</span>
			</label> <input type="text" name="correoAdministrador"
				id="correoAdministrador"
				value="<?php
    if (isset ( $_REQUEST ["correoAdministrador"] )) {
        echo $_REQUEST ["correoAdministrador"];
    } else {
        echo "";
    }
    ?>" /> <label>Indice de Parámetros <span class="textoPequenno">Nombre
					del parámetro GET.</span>
			</label> <input type="text" name="enlace" id="enlace"
				value="<?php
    if (isset ( $_REQUEST ["enlace"] )) {
        echo $_REQUEST ["enlace"];
    } else {
        echo "data";
    }
    ?>" />
        <label>Estilo predeterminado <span class="textoPequenno">Estilo jquery-ui predeterminado</span></label> <input type="text"
    				name="estiloPredeterminado" id="estiloPredeterminado"
    				value="<?php
        if (isset ( $_REQUEST ['estiloPredeterminado'] )) {
            echo $_REQUEST ['estiloPredeterminado'];
        } else {
            echo 'cupertino';
        }
        ?>" />
            <div class="spacer"></div>
			<div class="spacer"></div>
			<h1>Módulo de desarrollo</h1>
			<p>Definir si el aplicativo incluirá módulos de desarrollo.</p>
			<label>Instalar módulo de desarrollo</label><input type="checkbox"
				name="moduloDesarrollo" value="moduloDesarrollo" checked="checked"/>
			<div class="spacer"></div>
			<div class="spacer"></div>
			<h1>Servicios Web</h1>
			<p>Llaves para algunos servicios Web.</p>

			<label>Googlemaps <span class="textoPequenno">Llave p&uacute;blica
					para el servicio de GoogleMaps</span></label> <input type="text"
				name="googlemaps" id="googlemaps" /> <label>ReCatcha P&uacute;blica
				<span class="textoPequenno">Llave p&uacute;blica para el servicio de
					recatcha</span>
			</label> <input type="text" name="recatchapublica"
				id="recatchapublica" /> <label>ReCatcha Privada<span
				class="textoPequenno">Llave privada para el servicio de recatcha</span></label>
			<input type="text" name="recatchaprivada" id="recatchaprivada" />

			<div class="spacer"></div>
			<div class="spacer"></div>

			<h1>Sesiones</h1>
			<p>Datos de configuraci&oacute;n para las sesiones de usuario.</p>

			<label>Expiraci&oacute;n <span class="textoPequenno">Tiempo de espera
					si no se detecta actividad. (En minutos)</span></label> <input
				type="text" name="expiracion" id="expiracion" value="5" />
			<div class="spacer"></div>
			<div class="spacer"></div>

			<input class="button" name='aceptar' value='Aceptar' type='button'
				onclick="return(<?php  echo $validar; ?>)?this.form.submit():false"><br>
			<div class="spacer"></div>
		</form>
	</div>
</body>
</html>
