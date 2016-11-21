<?php
namespace reportes\certificadoNoInternet\entidad;
if (!isset($GLOBALS["autorizado"])) {
    include "index.php";
    exit();
}
class Redireccionador {
    public static function redireccionar($opcion, $valor = "") {

        $miConfigurador = \Configurador::singleton();

        switch ($opcion) {

            case "InsertoInformacionCertificado":
                $variable = 'pagina=certificadoNoInternet';
                $variable .= '&opcion=resultadoCertificado';
                $variable .= '&mensaje=insertoInformacionCertificado';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                break;

            case "ActualizoInformacionCertificado":
                $variable = 'pagina=certificadoNoInternet';
                $variable .= '&opcion=resultadoCertificado';
                $variable .= '&mensaje=insertoInformacionCertificado';
                $variable .= '&mensaje_modal=actualizoInformacionCertificado';
                $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
                break;

            case "NoInsertoInformacionCertificado":
                $variable = 'pagina=certificadoNoInternet';
                $variable .= '&opcion=resultadoCertificado';
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
