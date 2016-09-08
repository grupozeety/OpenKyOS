<?php
namespace gestionBeneficiarios\generacionContrato\entidad;
if (!isset($GLOBALS["autorizado"])) {
    include "index.php";
    exit();
}
class Redireccionador {
    public static function redireccionar($opcion, $valor = "") {

        $miConfigurador = \Configurador::singleton();

        switch ($opcion) {

            case "Inserto":

                $variable = 'pagina=generacionContrato';
                $variable .= '&opcion=validarRequisitos';
                $variable .= '&mensaje=inserto';
                $variable .= '&id_contrato=' . $valor[0]['id'];
                $variable .= '&numero_contrato_borrador=' . $valor[0]['numero_contrato'];
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                $variable .= '&tipo=' . $_REQUEST['tipo'];
                break;

            case "NoInserto":

                $variable = 'pagina=generacionContrato';
                $variable .= '&opcion=validarRequisitos';
                $variable .= '&mensaje=noinserto';
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
?>
