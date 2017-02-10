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

    public function __construct($sql)
    {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");
        $this->miSesionSso = \SesionSso::singleton();

        $info_usuario = $this->miSesionSso->getParametrosSesionAbierta();

        // Conexion a Base de Datos
        $conexion = "produccion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        // Clasificar y Estruturar Notificacion
        $this->clasificarNotificacion($_REQUEST['notificacion']);

    }

    public function clasificarNotificacion($notificacion)
    {

        switch ($notificacion) {
            case 'estadoProyectos':
                $this->estadoProyectos();
                break;
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
            $cadenaSql = $this->miSql->getCadenaSql('cantidadSinActaPortatil', $value['id_proyecto']);
            $cant_sin_ac_portatil = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
            $proyectos[$key]['cantidad_sin_acta_portatil'] = $cant_sin_ac_portatil['cant_beneficiarios'];

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

        // Estruturar Contenido Parametrizable Correo

        $meta = '';

        foreach ($proyectos as $key => $value) {

            if ($meta != $value['meta']) {
                $this->contenidoParametrizable .= ' <b>Meta #' . $value['meta'] . "</b><br>";
                $meta = $value['meta'];
            }

        }

        echo $this->contenidoParametrizable;exit;
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

        $miConfigurador = \Configurador::singleton();

        foreach ($this->users as $key => $user) {

            $variable = 'pagina=gestionUsuarios';
            $variable .= '&opcion=editarUsuario';
            $variable .= '&nombre_usuario=' . $user['uid'][0];
            $variable .= '&rol=' . $user['description'][0];
            $variable .= '&nombre_completo=' . $user['givenname'][0];
            $variable .= '&correo_electronico=' . $user['mail'][0];
            $variable .= '&telefono=' . $user['telephonenumber'][0];

            $url = $miConfigurador->configuracion["host"] . $miConfigurador->configuracion["site"] . "/index.php?";
            $enlace = $miConfigurador->configuracion['enlace'];
            $variable = $miConfigurador->fabricaConexiones->crypto->codificar($variable);
            $_REQUEST[$enlace] = $enlace . '=' . $variable;
            $redireccion = $url . $_REQUEST[$enlace];

            if ($key !== "count") {
                if ($user['description'][0] == "inactivo") {
                    $infoUser[$key]['uid'] = "<a id='inactivo' href='$redireccion'>" . $user['uid'][0] . "</a>";
                } else {
                    $infoUser[$key]['uid'] = "<a href='$redireccion'>" . $user['uid'][0] . "</a>";
                }
                $infoUser[$key]['description'] = $valores[$user['description'][0]];
                $infoUser[$key]['mail'] = $user['mail'][0];
                $infoUser[$key]['givenname'] = $user['givenname'][0];
                $infoUser[$key]['telephonenumber'] = $user['telephonenumber'][0];
            }

        }

        $total = count($infoUser);

        $resultado = json_encode($infoUser);

        $resultado = '{
                "recordsTotal":' . $total . ',
                "recordsFiltered":' . $total . ',
                "data":' . $resultado . '}';

        echo $resultado;

    }
}
$miDocumento = new Notificaciones($this->sql);
