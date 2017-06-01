<?php
require_once ("core/builder/HtmlBase.class.php");

class RadioButtonBootstrapHtml extends HtmlBase{
    
    function campoBotonRadialBootstrap($atributos) {
        
        $this->setAtributos ( $atributos );
        $this->campoSeguro();
    
        $this->cadenaHTML .= $this->radioButton ();

        return $this->cadenaHTML;
    
    }
    
    function radioButton() {
    
        $this->setAtributos ( $this->atributos );
        $this->miOpcion = "";
        $nombre = $this->atributos [self::ID];
        $id = "campo" . rand ();
    
        if (isset ( $this->atributos ["opciones"] )) {
            $opciones = explode ( "|", $this->atributos ["opciones"] );
            if (is_array ( $opciones )) {
    
                $this->miOpcion .= $this->opcionesRadioButton ( $opciones );
            }
        } else {
        	
        	$this->cadenaHTML = "<div class='" . "radio" . "'>\n";
        	
        	if (isset ( $this->atributos [self::ETIQUETA] ) && $this->atributos [self::ETIQUETA] != "") {
        		$this->cadenaHTML .= '<label>';
        	}
    
            $this->miOpcion .= "<input type='radio' ";
            $this->miOpcion .= self::HTMLNAME . "'" . $id . "' ";
            $this->miOpcion .= "id='" . $id . "' ";
            $this->miOpcion .= self::HTMLNAME . "'" . $nombre . "' ";
    
            $this->miOpcion .= self::HTMLVALUE . "'" . $this->atributos [self::VALOR] . "' ";
    
            if (isset ( $this->atributos [self::TABINDEX] )) {
                $this->miOpcion .= self::HTMLTABINDEX . "'" . $this->atributos [self::TABINDEX] . "' ";
            }
            if (isset ( $this->atributos [self::SELECCIONADO] ) & $this->atributos [self::SELECCIONADO]) {
                $this->miOpcion .= "checked='true' ";
            }
    
            $this->miOpcion .= "/> ";
            $this->miOpcion .= $this->atributos['etiqueta'];
            $this->miOpcion .= "</label>";
            $this->miOpcion .= "\n</div>\n";
            $cadena .= "<br>";
        }
        return $this->miOpcion;
    
    }
    
    function opcionesRadioButton($opciones) {
    
        $cadena = '';
        
        $nombre = $this->atributos['id'];
        
        foreach ( $opciones as $clave => $valor ) {
        	
        	
        	$id = $clave;
        	
            $opcion = explode ( "&", $valor );
            if ($opcion [0] != "") {
                if ($opcion [0] != $this->atributos ["seleccion"]) {
                    $cadena .= '<div class="radio">';
                	$cadena .= "<label>";
                    $cadena .= "<input type='radio' id='" . $id . "' " . self::HTMLNAME . "'" . $nombre . "' value='" . $opcion [0] . "' >";
                    $cadena .= $opcion [1] . "";
                    $cadena .= "</label>";
                    $cadena .= "</div>";
                     $cadena .= "<br>";
                } else {
                    $cadena .= '<div class="radio">';
                    $cadena .= "<label>";
                    $cadena .= "<input type='radio' id='" . $id . "' " . self::HTMLNAME . "'" . $nombre . "' value='" . $opcion [0] . "' checked='true' > ";
                    $cadena .= $opcion [1] . "";
                    $cadena .= "</label>";
                    $cadena .= "</div>";
                    $cadena .= "<br>";
                }
            }
        }
        
    
        return $cadena;
    
    }
}