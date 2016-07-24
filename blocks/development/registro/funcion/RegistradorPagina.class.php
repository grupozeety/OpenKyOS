<?php

namespace development\registro\funcion;

class RegistradorPagina {
    
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
        if (! isset ( $_REQUEST ['nombrePagina'] ) || 
                ! isset ( $_REQUEST ['descripcionPagina'] ) || 
                ! isset ( $_REQUEST ['moduloPagina'] ) || 
                ! isset ( $_REQUEST ['nivelPagina'] ) || 
                ! isset ( $_REQUEST ['parametroPagina'] )|| 
                $_REQUEST ['nombrePagina']=='' || 
                $_REQUEST ['nivelPagina']=='')
        {
        	error_log ('REGISTRAR PAGINA: No estan todos los datos');
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
                    $resultado=$this->getPagina();
                    if(!$resultado){
                        $resultado=$this->setPagina();                        
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
    
    function getPagina(){
        
        $cadenaSql = $this->miSql->getCadenaSql ( 'buscarPagina' );
        return $this->conexion->ejecutarAcceso ( $cadenaSql, 'busqueda' );        
    }
    
    function setPagina(){
        $cadenaSql = $this->miSql->getCadenaSql ( "insertarPagina" );
        $this->conexion->ejecutarAcceso ( $cadenaSql, 'insertar' );
        
        $resultado=$this->getPagina();
        
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

$miRegistrador = new RegistradorPagina ( $this->lenguaje, $this->sql );

$resultado= $miRegistrador->procesarFormulario ();



