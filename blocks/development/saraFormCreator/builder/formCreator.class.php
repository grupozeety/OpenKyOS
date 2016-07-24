<?php
namespace development\saraFormCreator\builder;

if (! isset ( $GLOBALS ['autorizado'] )) {
	include ('index.php');
	exit ();
}

class formCreator {
	//Este atributo guarda los atributos con que se quiere crear el elemento
	var $atributos;
	//Este elemento string guarda el HTML con que se crea el elemento
	var $cadenaHTML;
	/**
	 * Se guardan los atributos que se usaran en la contrucción del HTML del elemento
	 */
	function setAtributos($misAtributos) {	
		$this->atributos = $misAtributos;
	}
	/**
	 * Se inicia la contrucción del elemento html con los atributos obtenidos
	 */
    function formulario($atributos) {
    	
        $this->setAtributos ( $atributos );
        
        $this->cadenaHTML = '';
        
        $this->cadenaHTML .= $this->createWrapper();
        
        return $this->cadenaHTML;
    
    }
	private function parsePHPFile($filename){
		ob_start();
		include($filename);
		return ob_get_clean();
	}
    /**
	 * Se crea una elemento MODAL de bootstrap
	 */
    private function createModal(){        
    	$htmlModal = file_get_contents('modal.html.php', true);
        return $htmlModal;        
    }  
	/**
	 * Se crea un contenedor de paneles para bootrap con 2 columnas principales
	 * la de elementos disponibles y elementos en la página
	 */
    private function createPageContentWrapper(){    
    	// $htmlModal = file_get_contents('page-content-wrapper.html.php', true);
    	$htmlModal = $this->parsePHPFile('page-content-wrapper.html.php');
    	return $htmlModal;
    }
	/**
	 * Se crea un panel lateral que muestra el código generado en el proceso
	 * de arrastrar y soltar componentes
	 */
    private function createSidebarWrapper(){
    	$htmlModal = file_get_contents('sidebar-wrapper.html.php', true);
    	return $htmlModal;
    }
	/**
	 * Este contenedor se crea para alojar el Modal, el PageContentWrapper y el SidebarWrapper haciendo
	 * la cohesión para agregarle estilos CSS
	 */
    private function createWrapper(){
    	$inicio = '<!-- #Wrapper -->';
    	$inicio .= '<div id="wrapper" class="toggled">';
    	
    	$fin = '</div>';
    	$fin .= '<!-- /#wrapper -->';
    	
    	$html = $this->createModal();
    	$html .= $this->createSidebarWrapper();
    	$html .= $this->createPageContentWrapper();
    	return $inicio.$html.$fin;
    }
    
}