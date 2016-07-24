<?php
namespace component\CreadorCampos;
/**
 * Esta Clase es la Fachada para el componente.
 */
 
/**
 * Obligatorio: Registrar la clase para gestionar Comnponentes
 */

use component\Component;

/**
 * Obligatorio: Registrar las interfaces del componente
 */

use component\Notificador\interfaz\INotificador;

/**
 * Obligatorio: Registrar las clases que hacen parte del componente
 */
use component\Notificador\Clase\RegistradorNotificacion;


/**
 * Obligatorio: Declarar los scripts donde están los diferentes elementos del componente
 */
//require_once ('component/Component.class.php');
//require_once ('component/Notificador/Clase/RegistradorNotificacion.class.php');

/**
 * Opcional: Mantener en un solo sitio las clausulas SQL que utilizará el componente
 */

include_once ("Sql.class.php");



class Componente extends Component implements ICreadorCampos{
    
    private $miNotificador;
    private $miSql;
    
    
    

    
    /**
     * 
     * @param \INotificador $notificador Un objeto de una clase que implemente la interfaz INotificador
     */
    public function __construct()
    {
        
        $this->miNotificador = new RegistradorNotificacion();
        $this->miSql= new Sql();
        $this->miNotificador->setSql($this->miSql);
        
    }
    
    public function crearCampo($notificacion) {
        return $this->miNotificador->datosNotificacionSistema($notificacion);
    }
    
    
    
    
}

