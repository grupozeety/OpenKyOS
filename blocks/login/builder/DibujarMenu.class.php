<?php

namespace gui\menuPrincipal\builder;

if (! isset ( $GLOBALS ['autorizado'] )) {
	include ('index.php');
	exit ();
}

class Dibujar {
	//Este atributo guarda los atributos con que se quiere crear el elemento
	var $atributos;
	//Este elemento string guarda el JS con que se crea el elemento
	var $cadenaHTML;
	/**
	 * Se guardan los atributos que se usaran en la contrucción del HTML del elemento
	 */
	function setAtributos($misAtributos) {	
		$this->atributos = $misAtributos;
	}
	/**
	 * Se hace la renderización del fichero HTML.PHP
	 */
	private function parsePHPFile($filename){
		ob_start();
		include($filename);
		return ob_get_clean();
	}
	/**
	 * Se inicia la contrucción del elemento html con los atributos obtenidos
	 */
	
    function html($atributos) {   	
    	 
        $this->setAtributos ( $atributos );
                
        $this->cadenaHTML = '';
   
       	$this->cadenaHTML .= $this->parsePHPFile('menu.html.php');            
           
        return $this->cadenaHTML;
    
    }
 
}

?>

