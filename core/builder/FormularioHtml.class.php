<?php

require_once ("core/general/Agregador.class.php");
require_once ("core/builder/controleshtml/BotonHtml.class.php");
require_once ("core/builder/controleshtml/CheckBoxHtml.class.php");
require_once ("core/builder/controleshtml/Div.class.php");
require_once ("core/builder/controleshtml/Fieldset.class.php");
require_once ("core/builder/controleshtml/Form.class.php");
require_once ("core/builder/controleshtml/Img.class.php");
require_once ("core/builder/controleshtml/Input.class.php");
require_once ("core/builder/controleshtml/Link.class.php");
require_once ("core/builder/controleshtml/ListHtml.class.php");
require_once ("core/builder/controleshtml/RadioButtonHtml.class.php");
require_once ("core/builder/controleshtml/RecaptchaHtml.class.php");
require_once ("core/builder/controleshtml/Select.class.php");
require_once ("core/builder/controleshtml/TextArea.class.php");

class FormularioHtml extends Agregador{
	
	/*
	 * Permite guardar los Id y el String de validación del atributo "validar"
	 * de los componentes del formulario
	 */
    var $validadorCampos;
    /*
     * Se codifican el Objeto que se le pase o el atributo $validadorCampos de manera
     * predeterminada. Esto permite que los objetos pasen de un formuario de origen a
	 * un formulario destino por medio del $_REQUEST como un valor String.  
     */
    function codificarCampos($valor=''){
    	$valor=($valor=='')?$this->validadorCampos:'';
    	return base64_encode(serialize($valor));
    }
    /*
     * Permite decodificar los campos de $_REQUEST que hayan sido enviados codificados
     * con la funcion "codificarCampos". Esta se puede acceder cuando se envía a un Formulario
     * y no a una Función o petición Ajax. Hay que revisar en estos la forma correcta de hacerlo.
	 * Hasta donde se deja la funcionalidad se hace con una instanacia de core/builder/InspectorHTML.class.php
     */
    function decodificarCampos($valor){
    	return unserialize(base64_decode($valor));
    }
    
    function __construct(){
    	       
        $this->aggregate('BotonHtml');
        $this->aggregate('CheckBoxHtml');
        $this->aggregate('Div');
        $this->aggregate('Fieldset');
        $this->aggregate('Form');
        $this->aggregate('Img');
        $this->aggregate('Input');
        $this->aggregate('Link');
        $this->aggregate('ListHtml');
        $this->aggregate('RadioButtonHtml');
        $this->aggregate('RecaptchaHtml');
        $this->aggregate('Select');
        $this->aggregate('TextArea');
        
    }
    
    

}

// Fin de la clase FormularioHtml
?>
