<?php
/**
 * index.php
 * 
 * Punto de entrada al aplicativo.
 *
 * Crea un objeto de la clase Inicializador que se contituye
 * en el controlador primario de la aplicación.
 *
 *
 * @author Paulo Cesar Coronado
 * @copyright Universidad Distrital Francisco Jose de Caldas
 * @license GPL v3 o posterior
 * @version 1.0.0.3, 29/12/2012
 * @
 *
 */
require_once ("core/manager/Bootstrap.class.php");
class Aplicacion {
    
    /**
     * Arreglo.
     * Contiene las rutas donde se encuentran los archivos del aplicativo.
     *
     * @var string
     *
     */
    
    /**
     * Objeto.
     * Se encarga de las tareas preliminares que se requieren para lanzar la aplicación.
     *
     * @var Inicializador
     *
     */
    var $miLanzador;
    
    const RECARGAR='recargar';
    
    function __construct() {
        $GLOBALS ["configuracion"] = TRUE;
        $this->miLanzador = new Bootstrap ();
        do {
            if (isset ( $_REQUEST [self::RECARGAR] )) {
                unset ( $_REQUEST [self::RECARGAR] );
            }
            $this->miLanzador->iniciar ();
        } while ( isset ( $_REQUEST [self::RECARGAR] ) );
    }
}

/**
 * Iniciar la aplicacion.
 */
$miAplicacion = new Aplicacion ();

?>