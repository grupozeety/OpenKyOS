<?php
namespace gestionBeneficiarios\aprobacionContrato\entidad;
if (!isset($GLOBALS["autorizado"])) {
    include "index.php";
    exit();
}
class Redireccionador {
    public static function redireccionar($opcion, $valor = "") {

        $miConfigurador = \Configurador::singleton();

        switch ($opcion) {

            case "actualizoContrato":

                $variable = 'pagina=aprobacionContrato';
                $variable .= '&mensaje=inserto';
                $variable .= '&numero_contrato=' . $_REQUEST['numero_contrato'];

                break;

            case "noActualizo":

                $variable = 'pagina=aprobacionContrato';
                $variable .= '&opcion=aprobarContrato';
                $variable .= '&mensaje=noinserto';
                $variable .= "&id_contrato=" . $_REQUEST['id_contrato'];
                $variable .= "&numero_contrato=" . $_REQUEST['numero_contrato'];
                $variable .= "&nombre_beneficiario=" . $_REQUEST['nombre_beneficiario'];
                $variable .= "&identificacion_beneficiario=" . $_REQUEST['identificacion_beneficiario'];

                break;

            case "errorArchivo":

                $variable = 'pagina=aprobacionContrato';
                $variable .= '&opcion=aprobarContrato';
                $variable .= '&mensaje=errorArchivo';
                $variable .= "&id_contrato=" . $_REQUEST['id_contrato'];
                $variable .= "&numero_contrato=" . $_REQUEST['numero_contrato'];
                $variable .= "&nombre_beneficiario=" . $_REQUEST['nombre_beneficiario'];
                $variable .= "&identificacion_beneficiario=" . $_REQUEST['identificacion_beneficiario'];

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