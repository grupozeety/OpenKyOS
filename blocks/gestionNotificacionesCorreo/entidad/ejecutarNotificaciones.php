<?php

namespace gestionNotificacionesCorreo\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

include_once 'Redireccionador.php';

class Notificaciones
{
    public $miConfigurador;
    public $miSql;
    public $conexion;
    public $rutaURL;
    public $esteRecursoDB;
    public $rutaAbsoluta;
    public $miSesionSso;
    public $message;
    public $users;
    public $contenidoParametrizable = '';
    public $contenidoCorreo;
    public $designatariosCorreo;

    public function __construct($sql)
    {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");
        $this->miSesionSso = \SesionSso::singleton();

        $this->ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");

        $info_usuario = $this->miSesionSso->getParametrosSesionAbierta();

        // Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionApi', 'gmail');
        $this->datosConexion = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

        // Consultar Informacón Usuarios
        $this->consultarUsuarios();

        // Clasificar y Estruturar Notificacion
        $this->clasificarNotificacion($_REQUEST['notificacion']);

        // Enviar Notificación
        $this->enviarNotificacion();

    }

    public function clasificarNotificacion($notificacion)
    {

        switch ($notificacion) {
            case 'estadoProyectos':

                $roles = array(
                    '0' => 'supervisor',
                    '1' => 'gestoradministrativo',
                );
                $this->clasificarCorreoUsuarios($roles);
                $this->contenidoCorreo = 'Ingenieros nos permitimos informales el estado general del proceso de comisionamiento a la fecha de los siguientes proyectos : <br>';
                $this->estadoProyectos();
                $this->contenidoCorreo .= $this->contenidoParametrizable;

                break;
        }

    }

    public function clasificarCorreoUsuarios($roles = '')
    {
        foreach ($roles as $key => $value) {

            $this->extraerCorreos($value);
        }

    }

    public function extraerCorreos($rol = '')
    {

        unset($this->users['count']);

        foreach ($this->users as $key => $value) {

            if (isset($value['description'][0]) && $value['description'][0] == $rol) {
                $this->designatariosCorreo[] = $value['mail'][0];

            }

        }

    }

    public function estadoProyectos()
    {

        $cadenaSql = $this->miSql->getCadenaSql('consultarProyectos');

        $proyectos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        foreach ($proyectos as $key => $value) {

            //Cantidad Beneficiarios
            $cadenaSql = $this->miSql->getCadenaSql('cantidadBeneficiarios', $value['id_proyecto']);
            $cant_beneficiarios = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
            $proyectos[$key]['cantidad_beneficiarios'] = $cant_beneficiarios['cant_beneficiarios'];

            //Cantidad Beneficiarios Sin Contrato
            $cadenaSql = $this->miSql->getCadenaSql('cantidadSinContrato', $value['id_proyecto']);
            $cant_sin_contrato = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
            $proyectos[$key]['cantidad_sin_contato'] = $cant_sin_contrato['cant_beneficiarios'];

            //Cantidad Beneficiarios Sin Acta Portatil
            $cadenaSql = $this->miSql->getCadenaSql('cantidadSinActaPortatil', $value['id_proyecto']);
            $cant_sin_ac_portatil = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
            $proyectos[$key]['cantidad_sin_acta_portatil'] = $cant_sin_ac_portatil['cant_beneficiarios'];

            //Cantidad Beneficiarios Sin Acta Entrega Servicios
            $cadenaSql = $this->miSql->getCadenaSql('cantidadSinActaServicios', $value['id_proyecto']);
            $cant_sin_ac_portatil = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
            $proyectos[$key]['cantidad_sin_acta_servicios'] = $cant_sin_ac_portatil['cant_beneficiarios'];

            //Cantidad Beneficiarios Sin Portatil y Esclavo
            {
                $cadenaSql = $this->miSql->getCadenaSql('cantidadSinPortatilAsociado', $value['id_proyecto']);
                $cant_sin_portatil = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
                $proyectos[$key]['cantidad_sin_portatil'] = $cant_sin_portatil['cant_beneficiarios'];

                $cadenaSql = $this->miSql->getCadenaSql('cantidadSinEsclavoAsociado', $value['id_proyecto']);
                $cant_sin_esclavo = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
                $proyectos[$key]['cantidad_sin_esclavo'] = $cant_sin_esclavo['cant_beneficiarios'];

            }

            //Beneficiarios sin documentacion subida al sistema de la fase de contratacion

            {
                $cadenaSql = $this->miSql->getCadenaSql('cantidadBeneficiariariosDocumentosContratacion', $value['id_proyecto']);
                $cant_documentacion_contratacion = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
                $proyectos[$key]['cantidad_sin_documentacion_contratacion'] = $cant_beneficiarios['cant_beneficiarios'] - $cant_documentacion_contratacion['cant_beneficiarios'];

            }

            //Beneficiarios sin documentacion subida al sistema de la fase de comisionamiento

            {
                $cadenaSql = $this->miSql->getCadenaSql('cantidadBeneficiariariosDocumentosComisionamiento', $value['id_proyecto']);
                $cant_documentacion_comisionamiento = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
                $proyectos[$key]['cantidad_sin_documentacion_comisionamiento'] = $cant_beneficiarios['cant_beneficiarios'] - $cant_documentacion_comisionamiento['cant_beneficiarios'];

            }

            //Beneficiarios sin información tecnica

            {
                $cadenaSql = $this->miSql->getCadenaSql('cantidadSinInformacionTecnica', $value['id_proyecto']);
                $cant_sin_informacion_tecnica = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
                $proyectos[$key]['cantidad_sin_informacion_tecnica'] = $cant_sin_informacion_tecnica['cant_beneficiarios'];

            }

            //Beneficiarios sin información tecnica

            {
                $cadenaSql = $this->miSql->getCadenaSql('cantidadSinPruebasAsociadas', $value['id_proyecto']);
                $cant_sin_pruebas_asociadas = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
                $proyectos[$key]['cantidad_sin_pruebas_asociadas'] = $cant_sin_pruebas_asociadas['cant_beneficiarios'];

            }

        }

        //var_dump($proyectos);exit;
        // Estruturar Contenido Parametrizable Correo

        $meta = '';

        foreach ($proyectos as $key => $value) {

            if ($meta != $value['meta']) {
                $this->contenidoParametrizable .= ' <b>Meta #' . $value['meta'] . "</b><br><br>";
                $meta = $value['meta'];
            }

            $this->contenidoParametrizable .= 'Proyecto : <b>' . $value['proyecto'] . "</b><br><br>";

            $this->contenidoParametrizable .= 'Total beneficiarios sistema : <b>' . $value['cantidad_beneficiarios'] . "</b><br>";

            $this->contenidoParametrizable .= 'Beneficiarios sin contrato generado : <b>' . $value['cantidad_sin_contato'] . "</b><br>";

            $this->contenidoParametrizable .= 'Beneficiarios sin actas de portatil : <b>' . $value['cantidad_sin_acta_portatil'] . "</b><br>";

            $this->contenidoParametrizable .= 'Beneficiarios sin actas de servicio : <b>' . $value['cantidad_sin_acta_servicios'] . "</b><br>";

            $this->contenidoParametrizable .= 'Beneficiarios sin portatil relacionado : <b>' . $value['cantidad_sin_portatil'] . "</b><br>";

            $this->contenidoParametrizable .= 'Beneficiarios sin esclavo relacionado : <b>' . $value['cantidad_sin_esclavo'] . "</b><br>";

            $this->contenidoParametrizable .= 'Beneficiarios sin documentacion subida al sistema de la fase de contratacion : <b>' . $value['cantidad_sin_documentacion_contratacion'] . "</b><br>";

            $this->contenidoParametrizable .= 'Beneficiarios sin documentacion subida al sistema de la fase de comisionamiento : <b>' . $value['cantidad_sin_documentacion_comisionamiento'] . "</b><br>";

            $this->contenidoParametrizable .= 'Beneficiarios sin información técnica relacionada : <b>' . $value['cantidad_sin_informacion_tecnica'] . "</b><br>";

            $this->contenidoParametrizable .= 'Beneficiarios sin pruebas relacionadas : <b>' . $value['cantidad_sin_pruebas_asociadas'] . "</b><br><br>";

        }

    }
    /**
     * LDAP PHP Change Password Webpage
     *
     * @author : Matt Rude <http://mattrude.com>
     *         @website: http://technology.mattrude.com/2010/11/ldap-php-change-password-webpage/
     *
     *
     *         GNU GENERAL PUBLIC LICENSE
     *         Version 2, June 1991
     *
     *         Copyright (C) 1989, 1991 Free Software Foundation, Inc.,
     *         51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
     *         Everyone is permitted to copy and distribute verbatim copies
     *         of this license document, but changing it is not allowed.
     */
    public function consultarUsuarios()
    {

        global $message;
        global $message_css;

        $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionApi', 'ldap');
        $ldap = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

        $cadenaSql = $this->miSql->getCadenaSql('rol');
        $roles = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $valores = array();

        foreach ($roles as $key => $rol) {
            $valores[$rol['rol']] = $rol['descripcion'];
        }

        $server = $ldap['host'];
        $dn = $ldap['ruta_cookie'];

        $ldaprdn = $ldap['usuario'];
        $ldappass = $ldap['password'];

        error_reporting(0);
        $con = ldap_connect($server);
        ldap_set_option($con, LDAP_OPT_PROTOCOL_VERSION, 3);

        $filter = "(objectClass=person)";

        $justthese = array("uid", "givenName", "mail", "description", "telephonenumber");

        $sr = ldap_search($con, $dn, $filter, $justthese);

        $this->users = ldap_get_entries($con, $sr);

    }

    public function enviarNotificacion()
    {

        /**
         * This example shows settings to use when sending via Google's Gmail servers.
         */

        // SMTP needs accurate times, and the PHP time zone MUST be set
        // This should be done in your php.ini, but this is how to do it if you don't have access to that

        require $this->ruta . '/plugin/PHPMailer/PHPMailerAutoload.php';

        // Create a new PHPMailer instance
        $mail = new \PHPMailer();

        $mail->CharSet = 'UTF-8';

        // Tell PHPMailer to use SMTP
        $mail->isSMTP();

        // Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $mail->SMTPDebug = 0;

        // Ask for HTML-friendly debug output
        $mail->Debugoutput = 'html';

        // Set the hostname of the mail server
        $mail->Host = 'smtp.gmail.com';
        // use
        // $mail->Host = gethostbyname('smtp.gmail.com');
        // if your network does not support SMTP over IPv6

        // Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $mail->Port = 587;

        // Set the encryption system to use - ssl (deprecated) or tls
        $mail->SMTPSecure = 'tls';

        // Whether to use SMTP authentication
        $mail->SMTPAuth = true;

        // Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = $this->datosConexion['usuario'];

        // Password to use for SMTP authentication
        $mail->Password = $this->datosConexion['password'];

        // Set who the message is to be sent from
        $mail->setFrom($this->datosConexion['usuario'], 'Conexiones Digitales - Sistema OpenKyOS');

        // Set an alternative reply-to address
        // $mail->addReplyTo ( 'replyto@example.com', 'First Last' );

        // Set who the message is to be sent to

        if (is_array($this->designatariosCorreo) == true) {

            foreach ($this->designatariosCorreo as $key => $value) {

                $mail->addAddress($value);
            }
        } else {
            $mail->addAddress($this->designatariosCorreo);
        }

        // Set the subject line
        $mail->Subject = 'Estados Actuales Proyectos';
        $body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
                        <html>
                        <head>
                          <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                          <title>Estados Actuales Proyectos</title>
                        </head>
                        <body>
                        <div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">
                          <h1>Estados Actuales Proyectos</h1>
                          <p>' . $this->contenidoCorreo . '<br><br>Notificación de Sistema OpenKyOS</p>
                          <div align="center">
                          </div>
                        </div>
                        </body>
                        </html>
        ';

        // Read an HTML message body from an external file, convert referenced images to embedded,
        // convert HTML into a basic plain-text alternative body
        $mail->msgHTML($body, dirname(__FILE__));

        // Replace the plain text body with one created manually
        // $mail->AltBody = 'Hemos recibido una solicitud de restauración de contraseña, si usted realizo la solicitud de clic sobre el siguiente link . Si usted no realizo dicha solicitud por favor omita este mensaje';

        // Attach an image file
        // $mail->addAttachment ( $this->ruta . '/plugin/PHPMailer/examples/images/phpmailer_mini.png' );

        // send the message, check for errors
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            echo "Message sent!";
        }

        exit;
    }

}
$miDocumento = new Notificaciones($this->sql);
