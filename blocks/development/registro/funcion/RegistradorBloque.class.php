<?php

namespace development\registro\funcion;

class RegistradorBloque {
    
    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $miSql;
    var $conexion;
    
    function __construct($lenguaje, $sql) {
        
        $this->miConfigurador = \Configurador::singleton ();
        $this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;
    
    }
    
    function procesarFormulario() {
        
        $resultado=true;
        // 1. Verificar la integridad de las variables        
        if (! isset ( $_REQUEST ['nombreBloque'] ) || 
                ! isset ( $_REQUEST ['descripcionBloque'] ) ||
                ! isset ( $_REQUEST ['grupoBloque'] )|| 
                $_REQUEST ['nombreBloque']=='')
        {
            $resultado = false;
        }else        
        {
            $this->conexion = $this->miConfigurador->fabricaConexiones->getRecursoDB ( 'estructura' );
            if (! $this->conexion) {
                error_log ( "No se conectó" );
                $resultado = false;
            }
        }
        
        if ($resultado == false) {
            $this->miConfigurador->setVariableConfiguracion('mostrarMensaje','errorDatos');
            return $resultado;
        } else {
                    $resultado=$this->getBloque();
                    if(!$resultado){
                        $resultado=$this->setBloque();                        
                    }else {
                        $this->miConfigurador->setVariableConfiguracion('mostrarMensaje','errorNombre');
                        $resultado=false;                        
                    }
                    
                    return $resultado;
            
        }
    
    }
    
    function resetForm(){
        foreach($_REQUEST as $clave=>$valor){
             
            if($clave !='pagina' && $clave!='development' && $clave !='jquery' &&$clave !='tiempo'){
                unset($_REQUEST[$clave]);
            }
        }
    }
    
    function getBloque(){
        
        $cadenaSql = $this->miSql->getCadenaSql ( 'buscarBloque' );
        return $this->conexion->ejecutarAcceso ( $cadenaSql, 'busqueda' );        
    }
    
    function setBloque(){
        $cadenaSql = $this->miSql->getCadenaSql ( 'insertarBloque' );
        $this->conexion->ejecutarAcceso ( $cadenaSql, 'insertar' );
        
        $resultado=$this->getBloque();
        
        if(is_array($resultado)){
            //Armar un mensaje codificado en json
            $mensaje=json_encode($resultado);
            
        }
        
        $this->miConfigurador->setVariableConfiguracion('mostrarMensaje',$mensaje);
        $this->miConfigurador->setVariableConfiguracion('tipoMensaje','json');
        /**
         * Después de realizar esto se borran todas las variables relacionadas con este
         * Formulario
        */
        $this->resetForm();
        
        return true;
    }
    
}

$miRegistrador = new RegistradorBloque ( $this->lenguaje, $this->sql );

$resultado= $miRegistrador->procesarFormulario ();



