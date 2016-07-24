<?php

namespace component\EnsambladorEmail\Clase;

use component\EnsambladorEmail\interfaz\IEnsambladorEmail;

require_once ('component/Notificador/Interfaz/IEnsambladorEmail.php');

class EnsambladorEmail implements IEnsambladorEmail {
    
    private $miNotificacion;
    var $miConfigurador;
    var $miSql;
    
    function __construct() {
        
        $this->miConfigurador = \Configurador::singleton ();
    }
    
    function setSql($sql){
        $this->miSql=$sql;
    }
    
    function datosNotificacionEmail($notificacion) {
        
    }
    
    private function buscarDatos() {
        
        $resultado=false;
        
        $conexion = 'aplicativo';
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
        if ($esteRecursoDB) {
            
            $cadenaSql=$this->miSql->getCadenaSql('insertarRegistro',$this->miNotificacion);
            $resultado=$esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'acceso' );
        }
        
        return $resultado;
    }

}
