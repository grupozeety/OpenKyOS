<?php

namespace development\plugin\funcion;

class ConfigurarArchivos {
    
    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
        
    function __construct($lenguaje) {
        
        $this->miConfigurador = \Configurador::singleton ();
        $this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
        $this->lenguaje = $lenguaje;        
    
    }
    
    function procesarFormulario() {            
            foreach ($_REQUEST ['variablesConf'] as $a){
            	$this->modificarArchivo( $a['archivo'], $a['cadena'], $a['linea'] );
            }            
    }
    
    function modificarArchivo($archivo, $cadena, $linea){
		//Se abre  el archivo en modo de lectura y se cargan todas sus lineas en un arreglo.
		$F=fopen($archivo,'r');
		
		while (!feof($F)) 
		{
			$arreglo[]=fgets($F,4096);		
		}
			
		$arreglo[$linea] = $cadena;
		
		//Se abre el archivo en modo de sobrescribir modificando la linea # 3.
		 
		$escibe = fopen($archivo,'w');
		
		foreach ($arreglo as $lineas){
			fwrite($escibe, $lineas);
		}
		fclose($escibe);
	}
    
    function resetForm(){
        foreach($_REQUEST as $clave=>$valor){
             
            if($clave !='pagina' && $clave!='development' && $clave !='jquery' &&$clave !='tiempo'){
                unset($_REQUEST[$clave]);
            }
        }
    }    
}

$miRegistrador = new ConfigurarArchivos ( $this->lenguaje );

$resultado= $miRegistrador->procesarFormulario ();

