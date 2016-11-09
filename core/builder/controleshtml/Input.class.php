<?php

/**
 * $atributo['estilo']
 * $atributo['marco']
 * $atributo['columnas']
 * $atributo['etiqueta']
 * $atributo['anchoEtiqueta']
 * $atributo['estiloEtiqueta']
 * $atributo['dobleLinea']: La etiqueta va en una línea diferente a la del control asociado.
 * $atributo['tipo']        : text (para ingreso de texto), file (para seleccionar archivos)
 * $atributo['maximoTamanno']
 * $atributo['data-validate'] : Específico si se trabaja con el plugin Ketchup
 * $atributo['validar'] : Específico si se trabaja con el plugin validation-engine
 * $atributo['evento']
 * $atributo['tabIndex']
 * $atributo['name']
 * $atributo['id']
 * $atributo['valor']
 * $atributo['titulo']
 * $atributo['deshabilitado']
 * $atributo['tamanno']
 * $atributo['etiquetaBoton']
 *
 */
require_once "core/builder/HtmlBase.class.php";
class Input extends HtmlBase {
    public $cadenaHTML = '';
    public function campoCuadroTexto($atributos) {

        $this->setAtributos($atributos);
        /**
         * @todo explicar esta funcionalidad
         */
        if (isset($this->atributos["validar"])) {
            $this->instanciaFormulario->validadorCampos[$this->atributos["id"]] = $this->atributos["validar"];
        } else {
            $this->instanciaFormulario->validadorCampos[$this->atributos["id"]] = '';
        }

        $this->cadenaHTML = '';

        $this->campoSeguro();

        $final = '';

        if (isset($this->atributos[self::BOOTSTRAP]) && $this->atributos[self::BOOTSTRAP] == true) {

            $this->cadenaHTML .= $this->cuadro_texto();

        } else {

            if (isset($this->atributos[self::ESTILO]) && $this->atributos[self::ESTILO] == 'file') {
                $this->definirEstilo('seleccionarArchivo');
            } else {
                $this->definirEstilo('campoCuadroTexto');
            }

            if (isset($this->atributos[self::MARCO]) && $this->atributos[self::MARCO]) {

                if (isset($this->atributos[self::ESTILOMARCO])) {
                    $this->cadenaHTML .= "<div class='" . $this->atributos[self::ESTILOMARCO] . " ";
                } else {
                    $this->cadenaHTML .= "<div class='";
                }

                if (isset($this->atributos[self::COLUMNAS]) && $this->atributos[self::COLUMNAS] != "" && is_numeric($this->atributos[self::COLUMNAS])) {

                    $this->cadenaHTML .= " campoCuadroTexto anchoColumna" . $this->atributos[self::COLUMNAS];
                }

                $this->cadenaHTML .= "'>\n";

                $final = '</div>';
            }

            if (isset($this->atributos[self::ETIQUETA]) && $this->atributos[self::ETIQUETA] != "") {
                $this->cadenaHTML .= self::etiqueta();
            }
            if (isset($this->atributos["dobleLinea"]) && $this->atributos["dobleLinea"]) {
                $this->cadenaHTML .= "<br>";
            }
            $this->cadenaHTML .= $this->cuadro_texto();

            $this->cadenaHTML .= $final;

        }

        return $this->cadenaHTML;

    }

    public function cuadro_texto($atributos = '') {

        if ($atributos != '') {
            $this->setAtributos($atributos);
        }

        $cadena = '<input ';

        if ($this->atributos[self::TIPO] == self::FILE && $this->atributos[self::BOOTSTRAP] == true) {
        	

        	if (isset($this->atributos[self::ETIQUETA]) && $this->atributos[self::ETIQUETA] != "") {
        		 
        		//Manejo de responsiveness
        		$relacion= $this->atributos['anchoEtiqueta']*100/12;
        		$estiloLabel='';
        		$estiloControl='';
        	
        		// Para xs = extra small screens (mobile phones)
      
    	if($relacion<33){    	
    		$estiloLabel.='col-xs-12 ';
    		$estiloControl.='col-xs-12 ';    		
    	}else{
    		$estiloLabel.='col-xs-'.$this->atributos['anchoEtiqueta'].' ';
    		$estiloControl.='col-xs-'.$this->atributos['anchoCaja'].' ';
    	}
    	$estiloLabel.='col-sm-'.$this->atributos['anchoEtiqueta'].' col-md-'.$this->atributos['anchoEtiqueta'].' col-lg-'.$this->atributos['anchoEtiqueta'];
    	$estiloControl.='col-sm-'.$this->atributos['anchoCaja'].' col-md-'.$this->atributos['anchoCaja'].' col-lg-'.$this->atributos['anchoCaja'];
    	
        		//Fin manejo de responsiveness
        		$this->cadenaHTML .= '<div class="form-group row">';
        		$this->cadenaHTML .= '<label for="'. $this->atributos['id'].'" class="'.$estiloLabel.' col-form-label">';
        		$this->cadenaHTML .= $this->atributos['etiqueta'];

        		$this->cadenaHTML .= '</label>';
        		$this->cadenaHTML .= '<div class="'.$estiloControl.'">';
        	}
        	 
            $cadena = '<input ';
            $cadena .= $this->definirAtributosGenerales();

            $cadena .= "type='file' ";
            $cadena .= "id='" . $this->atributos[self::ID] . "' ";
            $cadena .= "class='filestyle'  ";

   	if (isset ( $this->atributos [self::ETIQUETA_BOTON] ) && $this->atributos [self::ETIQUETA_BOTON] != "") {
				$cadena .= "data-icon='true'";
				$cadena .= "data-buttonText='' ";
			} else {
				$cadena .= "data-buttonText='Seleccionar' ";
				$cadena .= "data-icon='false'";
			}
            
            if (isset($this->atributos[self::TAMANNO])) {
                $cadena .= "size = '" . $this->atributos[self::TAMANNO] . "'";
            }

            if (isset($this->atributos[self::VALIDAR])) {
                $cadena .= $this->atributos[self::VALIDAR];
            }

            $cadena .= "></div></div>\n";

        } elseif (!isset($this->atributos[self::TIPO]) || $this->atributos[self::TIPO] != self::HIDDEN) {

            // Desde HtmlBase
            $cadena .= $this->definirAtributosGenerales();

            $cadena .= $this->atributoClassCuadroTexto();

            $cadena .= $this->atributosGeneralesCuadroTexto(); 

            $cadena .= ">\n";
        } elseif ($this->atributos[self::TIPO] == self::HIDDEN) {

            $cadena .= "type = 'hidden'";
            $cadena .= self::HTMLNAME . "'" . $this->atributos[self::ID] . "'";
            $cadena .= "id = '" . $this->atributos[self::ID] . "'";
            if (isset($this->atributos[self::VALOR])) {
                $cadena .= self::HTMLVALUE . "'" . $this->atributos[self::VALOR] . "'";
            }

            $cadena .= ">\n";
        }

        return $cadena;
    }
    private function atributosGeneralesCuadroTexto() {
        $cadena = '';

        if (!isset($this->atributos[self::TIPO])) {
            $this->atributos[self::TIPO] = "text";
        }

        $cadena .= "type = '" . $this->atributos[self::TIPO] . "'";

        if (isset($this->atributos[self::DESHABILITADO]) && $this->atributos[self::DESHABILITADO]) {
            $cadena .= "readonly = 'readonly'";
        }

        if (isset($this->atributos[self::VALOR])) {
            $cadena .= self::HTMLVALUE . "'" . $this->atributos[self::VALOR] . "'";
        }

        if (isset($this->atributos[self::TAMANNO])) {
            $cadena .= "size = '" . $this->atributos[self::TAMANNO] . "'";
        }

        if (!isset($this->atributos[self::MAXIMOTAMANNO])) {
            $this->atributos[self::MAXIMOTAMANNO] = 100;
        }

        if (isset($this->atributos[self::TEXTOFONDO])) {
            $cadena .= "placeholder = '" . $this->atributos[self::TEXTOFONDO] . "'";
        }

        $cadena .= "maxlength = '" . $this->atributos[self::MAXIMOTAMANNO] . "'";

        // Si se utiliza ketchup
        if (isset($this->atributos["data - validate"])) {
            $cadena .= "data - validate = 'validate(" . $this->atributos["data-validate"] . ")'";
        }

        // Si utiliza algun evento especial
        if (isset($this->atributos[self::EVENTO])) {
            $cadena .= "" . $this->atributos[self::EVENTO] . "";
        }

        return $cadena;
    }
    private function atributoClassCuadroTexto() {
        $cadena = self::HTMLCLASS . "'";

        // --------------Atributo class --------------------------------

        $cadena .= $this->atributos[self::ESTILO] . " ";

        // Si se utiliza jQuery-Validation-Engine
        if (isset($this->atributos["validar"])) {
            $cadena .= " validate[" . $this->atributos["validar"] . "] ";
            // Si se utiliza jQuery-Validation-Engine
            if (isset($this->atributos["categoria"]) && $this->atributos["categoria"] = "fecha") {
                $cadena .= "datepicker ";
            }
        }

        return $cadena .= "'";

        // ----------- Fin del atributo class ----------------------------
    }
    public function campoFecha($atributos = '') {
        $this->setAtributos($atributos);

        if (isset($this->atributos[self::ESTILO]) && $this->atributos[self::ESTILO] != "") {
            $this->cadenaHTML = " < divclass  = '" . $this->atributos[self::ESTILO] . "' > \n";
        } else {
            $this->cadenaHTML = " < divclass  = 'campoFecha' > \n";
        }
        $this->cadenaHTML .= $this->etiqueta($this->atributos);
        $this->cadenaHTML .= " < divstyle = 'display:table-cell;vertical-align:top;float:left;' >  < spanstyle = 'white-space:pre;' >  <  / span > ";
        $this->cadenaHTML .= $this->cuadro_texto($this->configuracion, $this->atributos);
        $this->cadenaHTML .= " <  / div > ";
        $this->cadenaHTML .= " < divstyle = 'display:table-cell;vertical-align:top;float:left;' > ";
        $this->cadenaHTML .= " < spanstyle = 'white-space:pre;' >  <  / span >  < imgsrc = \"" . $this->configuracion["host"] . $this->configuracion["site"] . $this->configuracion["grafico"] . "/calendarito.jpg\" ";
        $this->cadenaHTML .= "id=\"imagen" . $this->atributos["id"] . "\" style=\"cursor: pointer; border: 0px solid red;\" ";
        $this->cadenaHTML .= "title=\"Selector de Fecha\" alt=\"Selector de Fecha\" onmouseover=\"this.style.background='red';\" onmouseout=\"this.style.background=''\" />";
        $this->cadenaHTML .= "</div>";
        $this->cadenaHTML .= "</div>\n";

        return $this->cadenaHTML;
    }
}
