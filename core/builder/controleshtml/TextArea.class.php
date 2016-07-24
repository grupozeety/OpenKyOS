<?php
require_once ("core/builder/HtmlBase.class.php");
/**
 * 
 * @author paulo
 * 
 * $atributos['estilo']
 * $atributos['filas']
 * $atributos['columnas']
 *
 */
class TextArea  extends HtmlBase{
    
    
    function campoTextArea($atributos) {
    	/*
    	 * Estas 2 líneas hacen el paso de atributos de los componentes que heredan de la clase HtmlBase
    	 * en este caso de pasan del componente a la instancia de FormularioHtml
    	 */
    	$campoValidar = (isset($atributos ["validar"]))?$atributos ["validar"]:'';
    	$this->instanciaFormulario->validadorCampos[$atributos ["id"]] = $campoValidar;
    	
        $this->setAtributos ( $atributos );
        $this->campoSeguro();
        
        if ($atributos [self::ESTILO] == self::JQUERYUI) {
            $this->cadenaHTML = "<div>\n";
            $this->cadenaHTML .= "<fieldset class='ui-widget ui-widget-content'>\n";
            $this->cadenaHTML .= "<legend class='ui-state-default ui-corner-all'>\n" . $this->atributos [self::ETIQUETA] . "</legend>\n";
            $this->cadenaHTML .= $this->area_texto ( $this->configuracion );
            $this->cadenaHTML .= "\n</fieldset>\n";
            $this->cadenaHTML .= "</div>\n";
            return $this->cadenaHTML;
        } else {
    
            if (isset ( $this->atributos [self::ESTILO] ) && $this->atributos [self::ESTILO] != "") {
                $this->cadenaHTML = "<div class='" . $this->atributos [self::ESTILO] . "'>\n";
            } else {
                $this->cadenaHTML = "<div class='campoAreaTexto'>\n";
            }
    
            $this->cadenaHTML .= $this->etiqueta ( $this->atributos );
            $this->cadenaHTML .= "<div class='campoAreaContenido'>\n";
            $this->cadenaHTML .= $this->area_texto ( $this->configuracion);
            $this->cadenaHTML .= "\n</div>\n";
            $this->cadenaHTML .= "</div>\n";
            return $this->cadenaHTML;
        }
    
    }
    
    function area_texto($datosConfiguracion) {
    
        $this->mi_cuadro = "<textarea ";
    
        $this->mi_cuadro .= "id='" . $this->atributos [self::ID] . "' ";
        $this->mi_cuadro .= $this->atributosGeneralesAreaTexto ();
    
        if (isset ( $this->atributos [self::ESTILOAREA] ) && $this->atributos [self::ESTILOAREA] != "") {
            $this->mi_cuadro .= self::HTMLCLASS . "'" . $this->atributos [self::ESTILOAREA] . "' ";
        } else {
            $this->mi_cuadro .= "class='areaTexto' ";
        }
    
        $this->mi_cuadro .= self::HTMLTABINDEX . "'" . $this->atributos [self::TABINDEX] . "' ";
        $this->mi_cuadro .= ">\n";
        if (isset ( $this->atributos [self::VALOR] )) {
            $this->mi_cuadro .= $this->atributos [self::VALOR];
        } else {
            $this->mi_cuadro .= "";
        }
        $this->mi_cuadro .= "</textarea>\n";
    
        if (isset ( $this->atributos [self::TEXTOENRIQUECIDO] ) && $this->atributos [self::TEXTOENRIQUECIDO]) {
            $this->mi_cuadro .= "<script type=\"text/javascript\">\n";
            $this->mi_cuadro .= "mis_botones='" . $datosConfiguracion ["host"] . $datosConfiguracion ["site"] . $datosConfiguracion ["grafico"] . "/textarea/';\n";
            $this->mi_cuadro .= "archivo_css='" . $datosConfiguracion ["host"] . $datosConfiguracion ["site"] . $datosConfiguracion ["estilo"] . "/basico/estilo.php';\n";
            $this->mi_cuadro .= "editor_html('" . $this->atributos [self::ID] . "', 'bold italic underline | left center right | number bullet | wikilink');";
            $this->mi_cuadro .= "\n</script>";
        }
    
        return $this->mi_cuadro;
    
    }
    
    function atributosGeneralesAreaTexto() {
    
        $cadena = '';
        
        if (isset ( $this->atributos [self::DESHABILITADO] ) && $this->atributos [self::DESHABILITADO]) {
            $cadena .= "readonly='1' ";
        }
    
        if (isset ( $this->atributos [self::NOMBRE] ) && $this->atributos [self::NOMBRE] != "") {
            $cadena .= self::HTMLNAME . "'" . $this->atributos [self::NOMBRE] . "' ";
        } else {
            $cadena .= self::HTMLNAME . "'" . $this->atributos [self::ID] . "' ";
        }
    
        if (isset ( $this->atributos ["columnas"] )) {
            $cadena .= "cols='" . $this->atributos ["columnas"] . "' ";
        } else {
            $cadena .= "cols='50' ";
        }
    
        if (isset ( $this->atributos ["filas"] )) {
            $cadena .= "rows='" . $this->atributos ["filas"] . "' ";
        } else {
            $cadena .= "rows='2' ";
        }
    
        return $cadena;
    
    }
    
    
}