<?php
class Enviar {
	public $error;
	public $miConfigurador;
	public $sql;
	public function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		
		$this->ruta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		$this->host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/plugin/html2pfd/";
	}
	public function recuperarClave($datosConexion, $destinatario, $link, $usuario) {
		
		/**
		 * This example shows settings to use when sending via Google's Gmail servers.
		 */
		
		// SMTP needs accurate times, and the PHP time zone MUST be set
		// This should be done in your php.ini, but this is how to do it if you don't have access to that
		
		require $this->ruta . '/plugin/PHPMailer/PHPMailerAutoload.php';
		
		// Create a new PHPMailer instance
		$mail = new PHPMailer ();
		
		$mail->CharSet = 'UTF-8';
		
		// Tell PHPMailer to use SMTP
		$mail->isSMTP ();
		
		// Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = 2;
		
		// Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';
		
		// Set the hostname of the mail server
		$mail->Host = 'smtp.gmail.com';
		// use
		// $mail->Host = gethostbyname('smtp.gmail.com');
		// if your network does not support SMTP over IPv6
		
		// Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		$mail->Port = 25;
		
		// Set the encryption system to use - ssl (deprecated) or tls
		$mail->SMTPSecure = 'tls';
		
		// Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		
		// Username to use for SMTP authentication - use full email address for gmail
		$mail->Username = $datosConexion ['usuario'];
		
		// Password to use for SMTP authentication
		$mail->Password = $datosConexion ['password'];
		
		// Set who the message is to be sent from
		$mail->setFrom ( $datosConexion ['usuario'], 'Conexiones Digitales' );
		
		// Set an alternative reply-to address
		// $mail->addReplyTo ( 'replyto@example.com', 'First Last' );
		
		// Set who the message is to be sent to
		$mail->addAddress ( $destinatario, $usuario );
		
		// Set the subject line
		$mail->Subject = 'Restauración de Contraseña';
		
		$body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <title>Restauración de Contraseña</title>
</head>
<body>
<div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">
  <h1>Restauración de Contraseña</h1>
  <p>Hemos recibido una solicitud de restauración de contraseña, si usted realizo dicha solicitud de clic sobre el siguiente link <a href="';
  $body .= $link;
  $body .= '">Restaurar Contraseña</a> . de lo contrario por favor omita este mensaje.</p>
  <div align="center">
  </div>
</div>
</body>
</html>
		';
		// Read an HTML message body from an external file, convert referenced images to embedded,
		// convert HTML into a basic plain-text alternative body
		$mail->msgHTML ( $body, dirname ( __FILE__ ) );
		
		// Replace the plain text body with one created manually
		// $mail->AltBody = 'Hemos recibido una solicitud de restauración de contraseña, si usted realizo la solicitud de clic sobre el siguiente link . Si usted no realizo dicha solicitud por favor omita este mensaje';
		
		// Attach an image file
		// $mail->addAttachment ( $this->ruta . '/plugin/PHPMailer/examples/images/phpmailer_mini.png' );
		
		// send the message, check for errors
		if (! $mail->send ()) {
			echo "Mailer Error: " . $mail->ErrorInfo;
		} else {
			echo "Message sent!";
		}
	}
}
?>
