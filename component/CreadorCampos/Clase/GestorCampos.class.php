<?php

namespace component\CreadorCampos\Clase;

class GestorCampos {
    
    private $miNotificacion;
    var $miConfigurador;
    var $miSql;
    
    function __construct() {
        
        $this->miConfigurador = \Configurador::singleton ();
    }
    
    function setSql($sql){
        $this->miSql=$sql;
    }
    
    function getCampo($parametros){
    	
    }
    
    function setCampo($parametros){
    	 
    }
    
    function deleteCampo($parametros){
    	 
    }
    
    
    function updateCampo($parametros){
    	 
    }    

}
?>