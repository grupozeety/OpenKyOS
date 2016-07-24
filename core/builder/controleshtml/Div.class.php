<?php

require_once ("core/builder/HtmlBase.class.php");

/**
 * Para elemento DIV:
 * $atributos['estilo']
 * $atributos['estiloEnLinea']
 * $atributos['titulo']
 * $atributos['id']
 *
 * Para Cuadros de Mensaje
 * $atributos['tipo']
 * $atributos['mensaje']
 *
 * Para campoTexto
 * $atributos['texto'] (Requerido)
 * $atributos['estilo'] (Opcional)
 * $atributos['columnas'] (Opcional)
 * $atributos['etiqueta'] (Opcional)
 *
 * Para CampoMensaje
 * $atributos['mensaje']
 * $atributos['estilo']
 * $atributos['estiloEnLinea']
 * $atributos['etiqueta']
 * $atributos['estiloEtiqueta']
 * $atributos['contenido']
 * $atributos['estiloContenido']
 * $atributos['tamanno']
 * $atributos['linea']: Define si se coloca una línea horizontal de separación
 */

class Div extends HtmlBase {
    
    function division($tipo, $atributos = "") {
        
        $this->setAtributos ( $atributos );
        $this->campoSeguro();
        $this->definirEstilo();        
        
        $this->cadenaHTML = '';
        if ($tipo == self::INICIO) {
            if (isset ( $this->atributos [self::ESTILO] )) {
                
                
                $this->cadenaHTML = "<div class='" . $this->atributos [self::ESTILO] . "' ";
            } else {
                $this->cadenaHTML = "<div ";
            }
            
            if (isset ( $this->atributos [self::ESTILOENLINEA] ) && $this->atributos [self::ESTILOENLINEA] != "") {
                $this->cadenaHTML .= "style='" . $this->atributos [self::ESTILOENLINEA] . "' ";
            }
            
            if (isset ( $this->atributos [self::TITULO] )) {
                $this->cadenaHTML .= "title='" . $this->atributos [self::TITULO] . "' ";
            }
            
            $this->cadenaHTML .= "id='" . $this->atributos ["id"] . "' ";
            
            $this->cadenaHTML .= ">\n";
        } else {
            
            $this->cadenaHTML .= "\n</div>\n";
        }
        
        return $this->cadenaHTML;
    
    }
    
    function cuadroMensaje($atributos) {
        
        $this->setAtributos ( $atributos );
        $this->campoSeguro();
        $this->cadenaHTML = "<div id='mensaje' class='" . $atributos ["tipo"] . " shadow " . $atributos [self::ESTILO] . "' >";
        $this->cadenaHTML .= "<span>" . $atributos ["mensaje"] . "</span>";
        $this->cadenaHTML .= "</div><br>";
        return $this->cadenaHTML;
    
    }
    
    function campoTexto($atributos) {
        
        $this->setAtributos ( $atributos );
        $this->campoSeguro();
        
        if (isset ( $atributos [self::ESTILO] ) && $atributos [self::ESTILO] != "") {
            if ($atributos [self::ESTILO] == self::JQUERYUI) {
                $this->cadenaHTML = "<div class='ui-widget ";
            } else {
                $this->cadenaHTML = "<div class='" . $atributos [self::ESTILO] . " ";
            }
        } else {
            $this->cadenaHTML = "<div class='campoTexto' ";
        }
        
        if (isset ( $atributos [self::COLUMNAS] ) && $atributos [self::COLUMNAS] != "" && is_numeric ( $atributos [self::COLUMNAS] )) {
            $this->cadenaHTML .= " anchoColumna" . $atributos [self::COLUMNAS] . "' ";
        } else {
            $this->cadenaHTML .= " anchoColumna1' ";
        }
        
        $this->cadenaHTML .= ">\n";
        if (isset ( $atributos [self::ETIQUETA] )) {
            $this->cadenaHTML .= "<div class='campoTextoEtiqueta'>\n";
            $this->cadenaHTML .= $atributos [self::ETIQUETA];
            $this->cadenaHTML .= "\n</div>\n";
            $this->cadenaHTML .= "<div class='campoTextoContenido'>\n";
        } else {
            $this->cadenaHTML .= "<div class='campoTextoContenidoSolo'>\n";
        }
        
        if ($atributos [self::TEXTO] != "") {
            $this->cadenaHTML .= nl2br ( $atributos [self::TEXTO] );
        } else {
            $this->cadenaHTML .= "--";
        }
        $this->cadenaHTML .= "\n</div>\n";
        $this->cadenaHTML .= "\n</div>\n";
        
        return $this->cadenaHTML;
    
    }
    
    /**
     *
     * Muestra un mensaje enmarcado en una división.
     * 
     * @param array $atributos            
     * @return string
     */
    
    function campoMensaje($atributos) {
        
        $this->setAtributos ( $atributos );
        $this->campoSeguro();
        
        $this->cadenaHTML .=$this->efectos();
        
        
        
        if( isset ( $atributos ['json'] )){

            $atributos[self::MENSAJE]=$this->campoMensajeJson($atributos);
            $atributos [self::ESTILOENLINEA]='display:inline-block';
        }
        
        if (isset ( $atributos [self::ESTILO] ) && $atributos [self::ESTILO] == self::JQUERYUI) {
            return $this->campoMensajeEstilo ( $atributos );
        }
        
        if (! isset ( $atributos [self::ESTILO] )) {
            $atributos [self::ESTILO] = 'campoMensaje';
        }
        
        $this->cadenaHTML .= "<div class='" . $atributos [self::ESTILO] . "' ";
        
        if (isset ( $atributos [self::ESTILOENLINEA] ) && $atributos [self::ESTILOENLINEA] != "") {
            $this->cadenaHTML .= "style='" . $atributos [self::ESTILOENLINEA] . "' ";
        }
        
        if (isset ( $atributos [self::ID] )) {
        $this->cadenaHTML .= "id='" . $atributos [self::ID] . "' ";
        }
        $this->cadenaHTML .= ">\n";
        
        $inicioSpan = '';
        $finSpan = '';
        
        //tmaño es un string: 'pequenno', 'mediano', 'grande'...
        if (isset ( $atributos [self::TAMANNO] ) && !isset($atributos ['json'])) {
            
            $atributos [self::TAMANNO] = ucfirst ( $atributos [self::TAMANNO] );
            $inicioSpan = "<span class='texto" . $atributos [self::TAMANNO] . "'> ";
            $finSpan = '</span>';
        }
        $this->cadenaHTML .= $inicioSpan . $atributos [self::MENSAJE] . $finSpan;
        
        if (isset ( $atributos ['linea'] ) && $atributos ['linea']) {
            $this->cadenaHTML .= "<hr class='hr_division'>";
        }
        $this->cadenaHTML .= "</div>\n";
        
        return $this->cadenaHTML;
    
    }
    
    private function campoMensajeEstilo($atributos) {
        
        $this->cadenaHTML = "<div  class='ui-accordion ui-widget ui-helper-reset'>";
        if (isset ( $atributos [self::ETIQUETA] )) {
            switch ($atributos [self::ETIQUETA]) {
                case 'simple' :
                    $this->cadenaHTML .= '<h3 class="ui-accordion-header ui-state-default">' . $this->cadenaHTML .= $atributos [self::MENSAJE] . "</h3>";
                    break;
                
                case 'encabezado2' :
                    $this->cadenaHTML .= '<h3 class="ui-accordion-header ui-helper-reset ui-state-default ui-accordion-header-active ui-state-active ui-corner-top ui-accordion-icons">' . $this->cadenaHTML .= $atributos [self::MENSAJE] . "</h3>";
                    break;
                
                default :
                    $this->cadenaHTML .= '<h3 class="ui-accordion-header ui-state-default ui-accordion-icons ui-corner-all">';
                    $this->cadenaHTML .= "<span class='ui-accordion-header-icon ui-icon ui-icon-document'></span>" . $atributos [self::MENSAJE] . "</h3>";
            }
        }
        $this->cadenaHTML .= "</div>";
        return $this->cadenaHTML;
    
    }
    
    function campoMensajeEtiqueta($atributos) {
        
        if (isset ( $atributos [self::ESTILO] ) && $atributos [self::ESTILO] != "") {
            $this->cadenaHTML = "<div class='" . $atributos [self::ESTILO] . "'>\n";
        } else {
            $this->cadenaHTML = "<div class='campoMensajeEtiqueta'>\n";
        }
        
        if (isset ( $atributos [self::ESTILOETIQUETA] ) && $atributos [self::ESTILOETIQUETA] != "") {
            $this->cadenaHTML .= "<div class='" . $atributos [self::ESTILOETIQUETA] . "'>\n";
        } else {
            $this->cadenaHTML .= "<div class='campoEtiquetaMensaje'>\n";
        }
        $this->cadenaHTML .= $atributos [self::ETIQUETA];
        $this->cadenaHTML .= "\n</div>\n";
        
        if (isset ( $atributos [self::ESTILOCONTENIDO] ) && $atributos [self::ESTILOCONTENIDO] != "") {
            $this->cadenaHTML .= "<div class='" . $atributos [self::ESTILOCONTENIDO] . "'>\n";
        } else {
            $this->cadenaHTML .= "<div class='campoContenidoMensaje'>\n";
        }
        if ($atributos [self::TEXTO] != "") {
            $this->cadenaHTML .= nl2br ( $atributos [self::TEXTO] );
        } else {
            $this->cadenaHTML .= "--";
        }
        $this->cadenaHTML .= "\n</div>\n";
        $this->cadenaHTML .= "\n</div>\n";
        
        return $this->cadenaHTML;
    
    }
    
    function campoEspacio() {
        
        $this->cadenaHTML = "<div class='espacioBlanco'>\n</div>\n";
        return $this->cadenaHTML;
    
    }
    
    function campoMapa($atributos) {
        
        $this->cadenaCampoMapa = "<div class='campoMapaEtiqueta'>\n";
        $this->cadenaCampoMapa .= $atributos [self::ETIQUETA];
        $this->cadenaCampoMapa .= "</div>\n";
        $this->cadenaCampoMapa .= "<div class='campoMapa'>\n";
        $this->cadenaCampoMapa .= $this->division ( self::INICIO, $atributos );
        $this->cadenaCampoMapa .= $this->division ( "fin", $atributos );
        $this->cadenaCampoMapa .= "\n</div>\n";
        
        return $this->cadenaCampoMapa;
    
    }
    
    /**
     * Función para mostrar mensajes de tipo tabla cuyos valores son pasados como una cadena json
     */
    function campoMensajeJson($atributos) {
        
        $error=false;
        // /1. Decodificar el mensaje
        $datos = json_decode ( $atributos [self::MENSAJE] );
        
        // 2. Verificar la integridad esperada es la de una matriz (arreglo de arreglos)
        // $a=array(0=>array('columna1=>'Fila1','Columna2'=>'Fila1'),1=>array('columna1'=>'Fila2','columna2'=>'Fila2'));
        
        if ($datos != NULL && is_array ( $datos )) {
            
            foreach ( $datos as $nodo => $fila ) {
                if(!is_array($fila)){
                    $error=true;
                }
            }
            
            //3. Crear la tabla
            return $this->crearTabla($datos);
        }
        
            
    }
    
    function crearTabla($datos){
        
        $cadenaTabla='<table>';
        foreach ( $datos as $nodo => $fila ) {            
            $fila=(array) $fila;
            foreach ($fila as $columna=>$valor){
                if(!is_numeric($columna)){
                $cadenaTabla.='<tr>';
                $cadenaTabla.='<td>'.strtoupper(trim($columna)).'</td><td>'.trim($valor)."</td>\n";
                $cadenaTabla.="</tr>\n";
                }
            }
        }
        $cadenaTabla.='</table>';
        return $cadenaTabla;
        
        
    }
    
    private function efectos(){
        $cadena='';
        
        
        if(isset($this->atributos['efecto'])){
            
            
            $efectos=explode(' ', $this->atributos['efecto']);
            
            if(in_array('desvanecer', $efectos)){
                $cadena='<script>setTimeout(function() { $("#divMensaje").hide( "drop", { direction: "up" }, "slow" );}, 4000);</script>';
            }            
        }
        
        return $cadena;
    }

}