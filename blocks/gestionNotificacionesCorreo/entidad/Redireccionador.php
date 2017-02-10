<?php

namespace gestionNotificacionesCorreo\entidad;

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

            case "sucess":
                $variable = 'pagina=gestionUsuarios';
                $variable .= '&mensaje=sucess';
                $variable .= '&valor=' . $valor;
                break;

            case "error":
                $variable = 'pagina=gestionUsuarios';
                $variable .= '&opcion=crearUsuario';
                $variable .= '&mensaje=error';
                $variable .= '&valor=' . $valor['mensaje'];
                $variable .= '&nombre_completo=' . $valor['nombre_completo'];
                $variable .= '&nombre_usuario=' . $valor['nombre_usuario'];
                $variable .= '&correo_electronico=' . $valor['correo_electronico'];
                $variable .= '&telefono=' . $valor['telefono'];
                $variable .= '&rol=' . $valor['rol'];
                break;

            case "errorModificar":
                $variable = 'pagina=gestionUsuarios';
                $variable .= '&opcion=editarUsuario';
                $variable .= '&mensaje=error';
                $variable .= '&valor=' . $valor['mensaje'];
                $variable .= '&nombre_completo=' . $valor['nombre_completo'];
                $variable .= '&nombre_usuario=' . $valor['nombre_usuario'];
                $variable .= '&correo_electronico=' . $valor['correo_electronico'];
                $variable .= '&telefono=' . $valor['telefono'];
                $variable .= '&rol=' . $valor['rol'];
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
