<?php
namespace component\Notificador;
use component\Component;
use component\Notificador\Clase\RegistradorNotificacion;
use component\Notificador\interfaz\INotificador;
require_once ('component/Component.class.php');
require_once ('component/Notificador/Clase/RegistradorNotificacion.class.php');

// Compilación de clausulas SQL utilizadas por el bloque
include_once ("Sql.class.php");



class Componente extends Component implements INotificador{
    
    
    
    private $miNotificador;
    private $miSql;
    
    
    
    //El componente actua como Fachada
    
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
    
    public function datosNotificacionSistema($notificacion) {
        return $this->miNotificador->datosNotificacionSistema($notificacion);
    }
    
    
    
    
}

