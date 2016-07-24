<?php

namespace development\plugin;

if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}

include_once ("core/manager/Configurador.class.php");

class Frontera {
    
    var $ruta;
    var $sql;
    var $miFuncion;
    var $lenguaje;
    var $miFormulario;
    
    var 

    $miConfigurador;
    
    function __construct() {
        
        $this->miConfigurador = \Configurador::singleton ();
    
    }
    
    public function setRuta($unaRuta) {
        $this->ruta = $unaRuta;
    }
    
    public function setLenguaje($lenguaje) {
        $this->lenguaje = $lenguaje;
    }
    
    public function setFormulario($formulario) {
        $this->miFormulario = $formulario;
    }
    
    function frontera() {
        $this->html ();
    }
    
    function setSql($a) {
        $this->sql = $a;
    
    }
    
    function setFuncion($funcion) {
        $this->miFuncion = $funcion;
    
    }
    
    function html() {
        
        include_once ("core/builder/FormularioHtml.class.php");
        
        $this->ruta = $this->miConfigurador->getVariableConfiguracion ( "rutaBloque" );
        $this->miFormulario = new \FormularioHtml ();
        
        $miBloque = $this->miConfigurador->getVariableConfiguracion ( 'esteBloque' );
        $resultado = $this->miConfigurador->getVariableConfiguracion ( 'errorFormulario' );
        
        if ($resultado && $resultado == $miBloque ['nombre']) {
            
            if(!isset($_REQUEST ['seleccionar'])){
                $_REQUEST ['seleccionar']=0;
            }
            
            switch ($_REQUEST ['seleccionar']) {
                
                case '1' :
                    include_once ($this->ruta . "/formulario/AgregarPlugin.php");
                    break;
                case '2' :
                   	include_once ($this->ruta . "/formulario/AgregarPlugin.php");
                   	break;
                
                default:
                    include_once ($this->ruta . "/formulario/registro.php");
            
            }
        
        } else {
            include_once ($this->ruta . "/formulario/registro.php");
        }
    
    }

}
?>
