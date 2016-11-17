<?php

namespace gestionComisionamiento\gestionRequisitos\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "index.php";
    exit();
}
class Redireccionador {

    public static function redireccionar($opcion, $valor = "") {
        $miConfigurador = \Configurador::singleton();

        switch ($opcion) {

            case "Inserto":

                $variable = 'pagina=gestionRequisitos';
                $variable .= '&opcion=validarRequisitos';
                $variable .= '&mensaje=inserto';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                $variable .= '&tipo=' . $_REQUEST['tipo'];
                $variable .= '&alfresco=' . $valor;
                $variable .= '&proceso=cargueRequisitos';
                break;

            case "NoInserto":
                $variable = 'pagina=gestionRequisitos';
                $variable .= '&opcion=validarRequisitos';
                $variable .= '&mensaje=noinserto';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                $variable .= '&tipo=' . $_REQUEST['tipo'];
                $variable .= '&proceso=cargueRequisitos';
                break;

            case "InsertoInformacionContrato":

                $variable = 'pagina=gestionRequisitos';
                $variable .= '&opcion=validarRequisitos';
                $variable .= '&proceso=gestionarContrato';
                $variable .= '&mensaje=insertoInformacionContrato';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                $variable .= '&tipo=' . $_REQUEST['tipo'];
                break;

            case "NoInsertoInformacionContrato":

                $variable = 'pagina=gestionRequisitos';
                $variable .= '&opcion=validarRequisitos';
                $variable .= '&mensaje=noInsertoInformacionContrato';
                $variable .= '&proceso=gestionarContrato';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                $variable .= '&tipo=' . $_REQUEST['tipo'];
                break;

            case "verifico":
                $variable = 'pagina=gestionRequisitos';
                $variable .= '&opcion=validarRequisitos';
                $variable .= '&mensaje=verifico';
                $variable .= '&proceso=verificarRequisitos';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                break;

            case "noverifico":
                $variable = 'pagina=gestionRequisitos';
                $variable .= '&opcion=validarRequisitos';
                $variable .= '&mensaje=noverifico';
                $variable .= '&proceso=verificarRequisitos';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                break;

            case "ErrorCargarFicheroDirectorio":
                $variable = 'pagina=gestionRequisitos';
                $variable .= '&opcion=validarRequisitos';
                $variable .= '&mensaje=novalido';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                $variable .= '&proceso=cargueRequisitos';
                break;

            default:
                $variable = '';
        }
        foreach ($_REQUEST as $clave => $valor) {
            unset($_REQUEST[$clave]);
        }

        $url = $miConfigurador->configuracion["host"] . $miConfigurador->configuracion["site"] . "/index.php?";
        $enlace = $miConfigurador->configuracion['enlace'];
        $variable = $miConfigurador->fabricaConexiones->crypto->codificar($variable);
        $_REQUEST[$enlace] = $enlace . '=' . $variable;
        $redireccion = $url . $_REQUEST[$enlace];

        echo "<script>location.replace('" . $redireccion . "')</script>";

        exit();
    }

}
?>
