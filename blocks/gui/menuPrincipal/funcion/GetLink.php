<?php
namespace gui\menuPrincipal\funcion;
if (!isset($GLOBALS['autorizado'])) {
    include 'index.php';
    exit();
}
class GetLink {
    public static function obtener($nombrePagina) {
        $miConfigurador = \Configurador::singleton();
        $miPaginaActual = $miConfigurador->getVariableConfiguracion('pagina');
        $variable = 'pagina=' . $nombrePagina;
        $url = $miConfigurador->configuracion['host'] . $miConfigurador->configuracion['site'] . '/index.php?';
        $enlace = $miConfigurador->configuracion['enlace'];
        $variable = $miConfigurador->fabricaConexiones->crypto->codificar($variable);
        $_REQUEST[$enlace] = $enlace . '=' . $variable;
        $direccion = $url . $_REQUEST[$enlace];

        return $direccion;
    }
    public static function ir($nombrePagina) {
        $direccion = self::obtener($nombrePagina);
        echo "<script>location.replace('" . $direccion . "')</script>";
        exit();
        return true;
    }
}
?>