<?php
namespace facturacion\impresionFactura\entidad;
if (!isset($GLOBALS["autorizado"])) {
    include "index.php";
    exit();
}
class Redireccionador {
    public static function redireccionar($opcion, $valor = "") {

        $miConfigurador = \Configurador::singleton();

        switch ($opcion) {

            case "InsertoInformacionActa":
                $variable = 'pagina=actaEntregaPortatil';
                $variable .= '&opcion=resultadoActa';
                $variable .= '&mensaje=insertoInformacionCertificado';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                break;

            case "NoInsertoInformacionActa":
                $variable = 'pagina=actaEntregaPortatil';
                $variable .= '&opcion=resultadoActa';
                $variable .= '&mensaje=noinsertoInformacionCertificado';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                break;

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
