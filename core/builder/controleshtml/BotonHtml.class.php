<?php

require_once ("core/builder/HtmlBase.class.php");

require_once ("core/builder/controleshtml/Input.class.php");


class BotonHtml extends HtmlBase{

    function campoBoton($atributos) {
        
        $this->setAtributos ( $atributos );
        
        $this->campoSeguro();
        
        if(isset($_REQUEST['formSecureId'])){
            $this->atributos [self::NOMBREFORMULARIO]=$_REQUEST['formSecureId'];
        }

        $this->cadenaHTML = '';
        
        $final='';
        
        if(!isset ( $atributos [self::ESTILOMARCO] ) || $atributos [self::ESTILOMARCO] == '' || $atributos [self::ESTILOMARCO] == 'jqueryui' ){
            $atributos [self::ESTILOMARCO]='campoBoton';
        }
        
        if (! isset ( $atributos [self::SINDIVISION] )) {
    
            $this->cadenaHTML .= "<div class='" . $atributos [self::ESTILOMARCO] . "'>\n";
            
            $final='</div>';
        }
    
        $this->cadenaHTML .= $this->boton ( $this->configuracion);
        
        return $this->cadenaHTML.$final;
    
    }
    
    function cuadroAsociado(){
        
        $cuadroTexto=new Input();
        $this->atributos [self::TIPO] = self::HIDDEN;
        $this->atributos ["obligatorio"] = false;
        $this->atributos [self::ETIQUETA] = "";
        $this->atributos [self::VALOR] = "false";
        return $cuadroTexto->cuadro_texto($this->atributos );
        
    }
    
    private function boton($datosConfiguracion) {
        
        if($this->atributos [self::ESTILOBOTON]=='jqueryui'){
            $this->atributos [self::ESTILOBOTON]='ui-button ui-state-default ui-corner-all ui-button-text-only';
        }
        if ($this->atributos [self::TIPO] == "boton") {
            $this->cadenaBoton = "<button ";
            $this->cadenaBoton .= "class='".$this->atributos [self::ESTILOBOTON]."' ";
            $this->cadenaBoton .= self::HTMLVALUE . "'" . $this->atributos [self::VALOR] . "' ";
            $this->cadenaBoton .= "id='" . $this->atributos [self::ID] . "A' ";
            $this->cadenaBoton .= self::HTMLTABINDEX . "'" . $this->atributos [self::TABINDEX] . "' ";
    
            $this->cadenaBoton .= $this->atributosGeneralesBoton ();
    
            if (! isset ( $this->atributos ["cancelar"] ) && (isset ( $this->atributos [self::VERIFICARFORMULARIO] ) && $this->atributos [self::VERIFICARFORMULARIO] != "")) {
                $this->cadenaBoton .= "onclick=\"if(" . $this->atributos [self::VERIFICARFORMULARIO] . "){document.forms['" . $this->atributos [self::NOMBREFORMULARIO] . $cadenaHtml[0] . $this->atributos [self::ID] . "'].value= 'true';";
                if (isset ( $this->atributos [self::TIPOSUBMIT] ) && $this->atributos [self::TIPOSUBMIT] == "jquery") {
                    $this->cadenaBoton .= " $(this).closest('form').submit();";
                } else {
                    $this->cadenaBoton .= "document.forms['" . $this->atributos [self::NOMBREFORMULARIO] . "'].submit()";
                }
                $this->cadenaBoton .= "}else{this.disabled=false;false}\">" . $this->atributos [self::VALOR] . '</button>\n';
                // El cuadro de Texto asociado
                $this->cadenaBoton .= $this->cuadroAsociado();
                
                
                
            } else {
    
                $this->cadenaBoton .= $this->atributoOnclickBoton ();
    
                $this->cadenaBoton .= "\">" . $this->atributos [self::VALOR] . "</button>\n";
    
                // El cuadro de Texto asociado
                $this->cadenaBoton .= $this->cuadroAsociado();
            }
        } else {
    
            $this->cadenaBoton = "<input ";
            $this->cadenaBoton .= self::HTMLVALUE . "'" . $this->atributos [self::VALOR] . "' ";
            $this->cadenaBoton .= self::HTMLNAME . "'" . $this->atributos [self::ID] . "' ";
            $this->cadenaBoton .= "id='" . $this->atributos [self::ID] . "' ";
            $this->cadenaBoton .= self::HTMLTABINDEX . "'" . $this->atributos [self::TABINDEX] . "' ";
            $this->cadenaBoton .= "type='submit' ";
            $this->cadenaBoton .= ">\n";
        }
        return $this->cadenaBoton;
    
    }
    
    function atributoOnclickBoton() {
    
        $cadena = '';
        if (isset ( $this->atributos [self::TIPOSUBMIT] ) && $this->atributos [self::TIPOSUBMIT] == "jquery") {
            // Utilizar esto para garantizar que se procesan los controladores de eventos de javascript al momento de enviar el form
            $cadena .= "onclick=\"document.forms['" . $this->atributos [self::NOMBREFORMULARIO] . "'].elements['" . $this->atributos [self::ID] . "'].value='true';";
            $cadena .= " $(this).closest('form').submit();";
        } else {
            if (! isset ( $this->atributos [self::ONCLICK] )) {
    
                $cadena .= "onclick=\"document.forms['" . $this->atributos [self::NOMBREFORMULARIO] . "'].elements['" . $this->atributos [self::ID] . "'].value='true';";
                $cadena .= "document.forms['" . $this->atributos [self::NOMBREFORMULARIO] . "'].submit()";
            }
        }
    
        if (isset ( $this->atributos [self::ONCLICK] ) && $this->atributos [self::ONCLICK] != '') {
            $cadena .= "onclick=\" " . $this->atributos [self::ONCLICK] . "\" ";
        }
    
        return $cadena;
    
    }
    
    function atributosGeneralesBoton() {
    
        $cadena = '';
        if (isset ( $this->atributos ['submit'] ) && $this->atributos ['submit']) {
            $cadena .= "type='submit' ";
        } else {
            $cadena .= "type='button' ";
        }
    
        if (! isset ( $this->atributos ["onsubmit"] )) {
            $this->atributos ["onsubmit"] = "";
        }
    
        // Poner el estilo en línea definido por el usuario
        if (isset ( $this->atributos [self::ESTILOENLINEA] ) && $this->atributos [self::ESTILOENLINEA] != "") {
            $cadena .= "style='" . $this->atributos [self::ESTILOENLINEA] . "' ";
        }
    
        return $cadena;
    
    }
    
    
}