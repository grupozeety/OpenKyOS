<?php
/**
 * $tipo : Si es el inicio o el final del formulario. values:'inicio', 'fin'
 * $atributos['id']
 * $atributos['nombre']
 * $atributos['tipoFormulario'] : Como se codificará los datos del formulario al enviar. values: 'multipart/form-data'[1],'application/x-www-form-urlencoded', 'text/plain' 
 * $atributos['metodo'] : values: 'GET', 'POST'
 * $atributos['action'] 
 * $atributos['titulo']
 * $atributos['estilo']
 * $atributos['marco'] : especifica si se coloca una división alrededor del formulario, esto facilita su maquetación. values: 'false', 'true'
 * 
 * [1] 'multipart/form-data' es necesario cunado se tienen controles tipo file.
 *  
 */

require_once ("core/builder/HtmlBase.class.php");

class Form extends HtmlBase {
    
    var $cadenaHTML='';
    
    function formularioConMarco() {
        
        if ($this->atributos ['tipoEtiqueta'] == self::INICIO) {
            
            if (isset ( $this->atributos [self::ESTILO] ) && $this->atributos [self::ESTILO] != "") {
                $this->cadenaHTML .= "<div class='" . $this->atributos [self::ESTILO] . "'>\n";
            } else {
                $this->cadenaHTML .= "<!-- Inicio marco del Formulario --><div class='formulario'>\n";
            }
            $this->cadenaHTML .= $this->procesarAtributosFormulario ( $this->atributos );
        
        } else {
            $this->cadenaHTML .= "</form><!-- Fin del Formulario -->";
            $this->cadenaHTML .= "</div><!-- Fin del marco del Formulario -->";
        }
        
        return $this->cadenaHTML;
    
    }
    
    /**
     * Formulario que no requieren su propia división
     *
     * @param unknown $tipo            
     * @param unknown $atributos            
     * @return Ambigous <string, unknown>
     *        
     */
    function formularioSinMarco() {
        
        if ($this->atributos ['tipoEtiqueta'] == self::INICIO) {
            
            $this->cadenaHTML.= $this->procesarAtributosFormulario ();
        
        } else {
            $this->cadenaHTML .= "</form>\n";
        }
        
        return $this->cadenaHTML;
    
    }
    
    function formulario($atributos) {
        
        $this->setAtributos ( $atributos );
        
        if (! isset ( $this->atributos ['tipoEtiqueta'] )) {
            $this->atributos ['tipoEtiqueta']= 'fin';
        
        }
        
        $this->campoSeguro ('form');
        
        if (isset ( $this->atributos ['validar'] )) {
            $this->cadenaHTML .= '<script type="text/javascript">';
            $this->cadenaHTML .= '$(function() {$("#' . $this->atributos [self::ID] . '").validationEngine({promptPosition : "centerRight",	scroll : false,autoHidePrompt:true})';
            $this->cadenaHTML .= '});</script>';
        } else {
            $this->cadenaHTML = '';
        }
        
        
        
        if (isset ( $this->atributos ['marco'] ) && $this->atributos ['marco']) {
            
            $this->formularioConMarco ();
        
        } else {
            $this->formularioSinMarco ();
        }
        
        return $this->cadenaHTML;
    }
    
    private function procesarAtributosFormulario() {
        
        $cadena = "<!-- Inicio del Formulario -->\n<form ";
        $nombre = '';
        
        if (isset ( $this->atributos [self::ID] )) {
            $cadena .= "id='" . $this->atributos [self::ID] . "' ";
            $nombre = $this->atributos [self::ID];
        }
        
        if (isset ( $this->atributos [self::TIPOFORMULARIO] ) && $this->atributos [self::TIPOFORMULARIO] != '') {
            $cadena .= "enctype='" . $this->atributos [self::TIPOFORMULARIO] . "' ";
        }
        
        if (isset ( $this->atributos [self::METODO] )) {
            $cadena .= "method='" . strtolower ( $this->atributos [self::METODO] ) . "' ";
        }
        
        if (isset ( $this->atributos ["action"] )) {
            $cadena .= "action='" . $this->atributos ["action"] . "' ";
        } else {
            $cadena .= "action='index.php' ";
        }
        
        if (isset ( $this->atributos [self::TITULO] )) {
            $cadena .= "title='" . $this->atributos [self::TITULO] . "' ";
        } else {
            $cadena .= "title='Formulario' ";
        }
        
        if (isset ( $this->atributos ['nombre'] )) {
            $cadena .= "name='" . $this->atributos ["nombre"] . "'>\n";
        } else {
            $cadena .= "name='" . $nombre . "'>\n";
        }
        
        return $cadena;
    }

}