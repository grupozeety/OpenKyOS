<?php
require_once ("core/builder/HtmlBase.class.php");


class ModalBootstrap  extends HtmlBase{
    
    function modal($atributos = "") {

    	$this->setAtributos($atributos);
    	
    	$this->cadenaHTML = "";
        
        if ($this->atributos ['tipoEtiqueta'] == self::INICIO) {

        	$this->cadenaHTML = '<div ';
        	
        	if (isset ( $atributos ["id"] )) {
        		$this->cadenaHTML .= "id='" . $atributos ["id"] . "' ";
        	}
        	
        	$this->cadenaHTML .= $this->atributoClassModal();
        	
        	$this->cadenaHTML .= '>'; 
        	
        	$this->cadenaHTML .= $this->contenidoModal();
        	
        }else{
        	$this->cadenaHTML .= $this->finModal();
        }
        
        return $this->cadenaHTML;
    
    }
    
    
    private function atributoClassModal() {
    	
    	$cadena = self::HTMLCLASS . "'";
    
    	// --------------Atributo class --------------------------------
    
    	$this->atributos[self::ESTILO] = 'modal fade';
    	
    	$cadena .= $this->atributos[self::ESTILO];
    
    	$cadena .= "' ";
    	
    	$cadena .= "role='dialog' ";
    	
    	return $cadena;
    
    	// ----------- Fin del atributo class ----------------------------
    }
    
    
    private function contenidoModal() {
    	 
    	$cadena = '<div class="modal-dialog">';
    	$cadena .= '<div class="modal-content">';
    	$cadena .= '<div class="modal-header">';
    	$cadena .= '<button type="button" class="close" data-dismiss="modal">';
    	$cadena .= '&times;';
    	$cadena .= '</button>';
    	$cadena .= '<h4 class="modal-title">';
    	$cadena .= $this->atributos['titulo'];
    	$cadena .= '</h4>';
    	$cadena .= '</div>';
    	$cadena .= '<div class="modal-body">';
    	 
    	 
    	return $cadena;
    
    }
    
    private function finModal() {
    
    	$cadena = ' </div>';
    	$cadena .= '</div>';
    	$cadena .= ' </div>';
    	$cadena .= ' </div>';
    
    	return $cadena;
    
    }
    
}