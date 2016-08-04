<?php
require_once ("core/builder/HtmlBase.class.php");

class RadioButtonBootstrapHtml extends HtmlBase{
    
    function campoBotonRadialBootstrap($atributos) {
        
        $this->setAtributos ( $atributos );
        $this->campoSeguro();
    
//         if (isset ( $this->atributos [self::ESTILO] ) && $this->atributos [self::ESTILO] != "") {
//             $this->cadenaHTML = "<div class='" . $this->atributos [self::ESTILO] . "'>\n";
//         } else {
//             $this->cadenaHTML = "<div class='campoBotonRadial'>\n";
//         }
    
        $this->cadenaHTML = "<div class='" . "radio" . "'>\n";
        
        if (isset ( $this->atributos [self::ETIQUETA] ) && $this->atributos [self::ETIQUETA] != "") {
            $this->cadenaHTML .= '<label>';
        }
    
        $this->cadenaHTML .= $this->radioButton ();
        $this->cadenaHTML .= "\n</div>\n";
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
            $this->miOpcion .= self::HTMLENDLABEL;
            $this->miOpcion .= '<br>';
        }
        return $this->miOpcion;
    
    }
    
    function opcionesRadioButton($opciones) {
    
        $cadena = '';
        foreach ( $opciones as $clave => $valor ) {
            $opcion = explode ( "&", $valor );
            if ($opcion [0] != "") {
                if ($opcion [0] != $this->atributos ["seleccion"]) {
                    $cadena .= "<div>";
                    $cadena .= "<input type='radio' id='" . $id . "' " . self::HTMLNAME . "'" . $nombre . "' value='" . $opcion [0] . "' />";
                    $cadena .= self::HTMLLABEL . "'" . $id . "'>";
                    $cadena .= $opcion [1] . "";
                    $cadena .= "</label>";
                    $cadena .= "</div>";
                } else {
                    $cadena .= "<div>";
                    $cadena .= "<input type='radio' id='" . $id . "' " . self::HTMLNAME . "'" . $nombre . "' value='" . $opcion [0] . "' checked /> ";
                    $cadena .= self::HTMLLABEL . "'" . $id . "'>";
                    $cadena .= $opcion [1] . "";
                    $cadena .= "</label>";
                    $cadena .= "</div>";
                }
            }
        }
    
        return $cadena;
    
    }
}