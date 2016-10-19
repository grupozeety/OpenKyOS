<?php
namespace gestionComisionamiento\generacionActas\entidad;
if (!isset($GLOBALS["autorizado"])) {
    include "index.php";
    exit();
}
class Redireccionador {
    public static function redireccionar($opcion, $valor = "") {

        $miConfigurador = \Configurador::singleton();

        switch ($opcion) {

            case "archivoGenerado":
                $variable = 'pagina=generacionActas';
                $variable .= '&mensaje=archivoGenerado';
                $variable .= "&nombre_archivo=" . $valor['nombre_archivo'];
                break;

            case "archivoNoGenerado":
                echo "No existe";
                var_dump($_REQUEST);exit;
                $variable = 'pagina=segundaPagina';
                $variable .= '&variable' . $valor;
                break;
            default:
                echo "salida";exit;
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
