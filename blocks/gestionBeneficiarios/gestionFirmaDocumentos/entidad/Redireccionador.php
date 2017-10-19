<?php

namespace gestionBeneficiarios\gestionFirmaDocumentos\entidad;

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

            case "exitoGestionFirma":
                $variable = 'pagina=gestionFirmaDocumentos';
                $variable .= '&mensaje=exitoGestionFirma';
                break;

            case "errorArchivo":
                $variable = 'pagina=gestionFirmaDocumentos';
                $variable .= '&mensaje=errorArchivo';
                break;

            case "errorFormatoArchivo":
                $variable = 'pagina=gestionFirmaDocumentos';
                $variable .= '&mensaje=errorFormatoArchivo';
                break;

            case "errorCargaArchivo":
                $variable = 'pagina=gestionFirmaDocumentos';
                $variable .= '&mensaje=errorCargaArchivo';
                break;

            case "errorRegistroFirma":
                $variable = 'pagina=gestionFirmaDocumentos';
                $variable .= '&mensaje=errorRegistroFirma';
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

        echo "<script>location.replace('    " . $redireccion . "')</script>";

        exit();
    }
}
