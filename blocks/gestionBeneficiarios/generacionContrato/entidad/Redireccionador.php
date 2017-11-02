<?php

namespace gestionBeneficiarios\generacionContrato\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "index.php";
    exit();
}
class Redireccionador
{

    public static function redireccionar($opcion, $valor = "")
    {
        $miConfigurador = \Configurador::singleton();

        switch ($opcion) {

            case 'ActualizoInformacionContrato':
                $variable = 'pagina=generacionContrato';
                $variable .= '&opcion=validarRequisitos';
                $variable .= '&proceso=gestionarContrato';
                $variable .= '&mensaje=ActualizoinformacionContrato';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                $variable .= '&tipo_beneficiario=' . $_REQUEST['tipo_beneficiario'];

                break;

            case "Inserto":

                $variable = 'pagina=generacionContrato';
                $variable .= '&opcion=validarRequisitos';
                $variable .= '&mensaje=inserto';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                $variable .= '&tipo=' . $_REQUEST['tipo'];
                $variable .= '&alfresco=' . $valor;
                $variable .= '&proceso=cargueRequisitos';
                break;

            case "NoInserto":
                $variable = 'pagina=generacionContrato';
                $variable .= '&opcion=validarRequisitos';
                $variable .= '&mensaje=noinserto';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                $variable .= '&tipo=' . $_REQUEST['tipo'];
                $variable .= '&proceso=cargueRequisitos';
                break;

            case "InsertoInformacionContrato":

                $variable = 'pagina=generacionContrato';
                $variable .= '&opcion=validarRequisitos';
                $variable .= '&proceso=gestionarContrato';
                $variable .= '&mensaje=insertoInformacionContrato';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                $variable .= '&tipo=' . $_REQUEST['tipo_beneficiario'];
                break;

            case "NoInsertoInformacionContrato":

                $variable = 'pagina=generacionContrato';
                $variable .= '&opcion=validarRequisitos';
                $variable .= '&mensaje=noInsertoInformacionContrato';
                $variable .= '&proceso=gestionarContrato';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                $variable .= '&tipo=' . $_REQUEST['id_beneficiario'];
                break;

            case "verifico":
                $variable = 'pagina=generacionContrato';
                $variable .= '&opcion=validarRequisitos';
                $variable .= '&mensaje=verifico';
                $variable .= '&proceso=verificarRequisitos';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                break;

            case "noverifico":
                $variable = 'pagina=generacionContrato';
                $variable .= '&opcion=validarRequisitos';
                $variable .= '&mensaje=noverifico';
                $variable .= '&proceso=verificarRequisitos';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                break;

            case "ErrorCargarFicheroDirectorio":
                $variable = 'pagina=generacionContrato';
                $variable .= '&opcion=validarRequisitos';
                $variable .= '&mensaje=novalido';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                $variable .= '&proceso=cargueRequisitos';
                break;

            case "InsertoInformacionDocumento":

                $variable = 'pagina=generacionContrato';
                $variable .= '&opcion=validarRequisitos';
                $variable .= '&proceso=gestionarContrato';
                $variable .= '&mensaje=registroSoporteContrato';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                $variable .= '&tipo=' . $_REQUEST['tipo_beneficiario'];
                break;

            case "NoInsertoInformacionDocumento":

                $variable = 'pagina=generacionContrato';
                $variable .= '&opcion=validarRequisitos';
                $variable .= '&mensaje=errorRegistroSoporteContrato';
                $variable .= '&proceso=gestionarContrato';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                $variable .= '&tipo=' . $_REQUEST['tipo'];
                break;

            case "ErrorTipoArhivoCargar":

                $variable = 'pagina=generacionContrato';
                $variable .= '&opcion=validarRequisitos';
                $variable .= '&mensaje=errorTipoSoporteContrato';
                $variable .= '&proceso=gestionarContrato';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                $variable .= '&tipo=' . $_REQUEST['tipo'];
                break;

            case 'ErrorArchivo':
                $variable = 'pagina=generacionContrato';
                $variable .= '&opcion=validarRequisitos';
                $variable .= '&mensaje=errorArchivo';
                $variable .= '&proceso=gestionarContrato';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                $variable .= '&tipo=' . $_REQUEST['tipo'];
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
