<?php

namespace usuarios\crearUsuario\funcion;

use usuarios\crearUsuario\funcion\redireccionar;

include_once ('redireccionar.php');
if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class Registrar {
	
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miFuncion;
	var $miSql;
	var $conexion;
	
	function __construct($lenguaje, $sql, $funcion) {
		
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
		$this->miFuncion = $funcion;
	}
	function procesarFormulario() {

		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/usuarios/crearUsuario/";
		$rutaBloque .= $esteBloque ['nombre'];
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/usuarios/crearUsuario/" . $esteBloque ['nombre'];
	
		//Se genera una contraseña aleatoria con una longitud de 10 caracteres.
		$cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
		$longitudCadena=strlen($cadena);
		 
		$pass = "";
		$longitudPass=10;
		 
		for($i=1 ; $i<=$longitudPass ; $i++){
			$pos=rand(0,$longitudCadena-1);
			$pass .= substr($cadena,$pos,1);
		}
		///////////////////////////////////////////////////////////////////////////
		
		
		$_REQUEST['usuario'] = $_REQUEST['tipoDocumento'] . $_REQUEST['documentoUsuario'];
		$_REQUEST['contrasena'] = base64_encode(pack('H*',hash('sha256', $pass)));
		$time = explode(" ",microtime());
		$_REQUEST['timestamp'] = date("y-m-d H:i:s",$time[1]).substr((string)$time[0],1,4);
		$_REQUEST['tenant'] = '-1234';
		$_REQUEST['salt']= '';
		$_REQUEST['domain']= '1';
		$_REQUEST['profileConf']= 'default';
		$_REQUEST['profile']= 'default';
		
		$conexion = "data";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
			
		$cadenaSql = $this->miSql->getCadenaSql ( 'registrarDatosBasicosUsuario', $_REQUEST );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar" );
		
		if($resultado){
			$conexion = "usuario";
			$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'registrarUsuario', $_REQUEST );
			$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar" );
				
		}else{
			$resultado=false;
		}
		
		if($resultado){
			$this->enviarEmail($_REQUEST['usuario'],$pass);
		}else{
			$conexion = "data";
			$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
				
			$resultado=false;
				
			$cadenaSql = $this->miSql->getCadenaSql ( 'errorRegistro', $_REQUEST );
			
			while($resultado==false){
				$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "actualizar" );
			}
			
			$resultado=false;
		}
		
		
		if ($resultado) {
			redireccion::redireccionar ( 'inserto',  $_REQUEST['nombreUsuario']." ".$_REQUEST['apellidoUsuario']);
			exit ();
		} else {
			redireccion::redireccionar ( 'noInserto',$_REQUEST['nombreUsuario']." ".$_REQUEST['apellidoUsuario'] );
			exit ();
		}
	}
	
	function enviarEmail($usuario, $password){
		
		$raizDocumento = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		
		require $raizDocumento.'/plugin/PHPMailer/PHPMailerAutoload.php';
		//Create a new PHPMailer instance
		$mail = new \PHPMailer;
		
		$mail->isSMTP();
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = 0;
		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';
		//Set the hostname of the mail server
		$mail->Host = "smtp.gmail.com	";
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port = 587;
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		//Username to use for SMTP authentication
		$mail->Username = "corporacionpolitecnicaosticket@gmail.com";
		//Password to use for SMTP authentication
		$mail->Password = "osticket2016";
		//Set who the message is to be sent from
		$mail->setFrom('corporacionpolitecnicaosticket@gmail.com', 'Centro de Soporte');
		//Set an alternative reply-to address
// 		$mail->addReplyTo('replyto@example.com', 'First Last');
		//Set who the message is to be sent to
		$mail->addAddress($_REQUEST['email'], $_REQUEST['nombreUsuario']." ".$_REQUEST['apellidoUsuario']);
		//Set the subject line
		$mail->Subject = 'Apertura de Cuenta';
		
		$cuerpo = '<html>
 		</head>
 		<title>Apertura de Cuenta</title>
 		</head>
 		<body>
 		<p>Hola ' . $_REQUEST['nombreUsuario'] . ', nos da gusto que hagas parte del proyecto Conexiones Digitales II.</p>
 		<br>
 		<p>Para acceder a tu cuenta lo puedes hacer ingresando al portal e iniciando sesión en la sección Mi Cuenta mediante el siguiente link.</p>
 		<a href="localhost/workspace/PortalCommunity">Ir al Portal</a>
 		<p>Para acceder has uso de las siguiente credenciales</p>
 		<p>Usuario: '.$usuario.'</p>
 		<p>Contraseña: '.$password.'</p>
 		<br>
 		</body>
		</html>';
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($cuerpo);
		//Replace the plain text body with one created manually
		$mail->AltBody = 'This is a plain-text message body';
		//Attach an image file
// 		$mail->addAttachment('images/phpmailer_mini.png');
		//send the message, check for errors
		if (!$mail->send()) {
		    echo "Mailer Error: " . $mail->ErrorInfo;
		} else {
		    echo "Message sent!";
		}
	}
	
	function resetForm() {
		foreach ( $_REQUEST as $clave => $valor ) {
			
			if ($clave != 'pagina' && $clave != 'development' && $clave != 'jquery' && $clave != 'tiempo') {
				unset ( $_REQUEST [$clave] );
			}
		}
	}
}

$miRegistrador = new Registrar ( $this->lenguaje, $this->sql, $this->funcion );

$resultado = $miRegistrador->procesarFormulario ();

?>
