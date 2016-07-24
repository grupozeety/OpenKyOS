<?php

namespace component\Notificador\Clase;

use component\Notificador\interfaz\INotificador;

require_once ('component/Notificador/Interfaz/INotificador.php');

class RegistradorNotificacion implements INotificador {
    
    private $miNotificacion;
    var $miConfigurador;
    var $miSql;
    
    function __construct() {
        
        $this->miConfigurador = \Configurador::singleton ();
    }
    
    function setSql($sql){
        $this->miSql=$sql;
    }
    
    function datosNotificacionSistema($notificacion) {
        
        $respuesta = true;
        
        $this->miNotificacion = json_decode ( $notificacion );
        
        if ($this->miNotificacion != NULL) {
            
            $respuesta = $this->revisarDatos ();
            
            if ($respuesta) {
                $respuesta = $this->registrarTransaccion ();
            }
        } else {
            $respuesta = false;
        }
        return $respuesta;
    
    }
    
    private function revisarDatos() {
        
        $campos = array (
                'idProceso',
                'idRemitente',
                'idDestinatario',
                'asunto',
                'descripcion',
                'criticidad',
                'tipoMecanismo' 
        );
        
        $resultado = true;
        foreach ( $campos as $clave => $valor ) {
            
            if (! isset ( $this->miNotificacion->$valor )) {
                $resultado = false;
            }
        }
        
        if ($resultado) {
            
            $tipoMecanismo = $this->miNotificacion->tipoMecanismo;
            
            if (($tipoMecanismo == 2 && ! isset ( $this->miNotificacion ['email'] )) || ($tipoMecanismo == 3 && (! isset ( $this->miNotificacion ['celular'] ) || ! isset ( $this->miNotificacion ['textoSMS'] )))) {
                $resultado = false;
            }
        
        }
        
        return $resultado;
    }
    
    private function registrarTransaccion() {
        
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
