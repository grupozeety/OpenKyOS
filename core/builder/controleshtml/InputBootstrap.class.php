<?php

/**
 * $atributo['estilo'] 
 * $atributo['marco']
 * $atributo['columnas']
 * $atributo['etiqueta']
 * $atributo['anchoEtiqueta']
 * $atributo['estiloEtiqueta']
 * $atributo['dobleLinea']: La etiqueta va en una línea diferente a la del control asociado.
 * $atributo['tipo']		: text (para ingreso de texto), file (para seleccionar archivos)
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
 * 
 */
require_once ("core/builder/HtmlBase.class.php");

class InputBootstrap extends HtmlBase {
	
	var $cadenaHTML = '';
	
	function campoCuadroTextoBootstrap($atributos) {
		
		$this->setAtributos ( $atributos );
		
		$this->cadenaHTML = '';
				
		/**
		 * @todo explicar esta funcionalidad
		 */
		if (isset ( $this->atributos ["validar"] )) {
			$this->instanciaFormulario->validadorCampos [$this->atributos ["id"]] = $this->atributos ["validar"];
		} else {
			$this->instanciaFormulario->validadorCampos [$this->atributos ["id"]] =  '';
		}
		
// 		if (isset($atributos [self::COLUMNAS]) && $atributos [self::COLUMNAS] != '' && is_numeric($atributos [self::COLUMNAS])) {
		
// 			$this->cadenaHTML .= "<div class='campoCuadroTexto anchoColumna" . $atributos [self::COLUMNAS] . "'>\n";
// 		} else {
// 			$this->cadenaHTML .= "<div class='campoCuadroTexto'>\n";
// 		}
		
		$this->campoSeguro ();
		
		$final = '';
		
			
		
		$this->cadenaHTML .= '<div class="';
		$this->cadenaHTML .= 'form-group row';
		$this->cadenaHTML .= '">';
		
		if (isset ( $this->atributos [self::ETIQUETA] ) && $this->atributos [self::ETIQUETA] != "") {
			
	        $this->cadenaHTML .= '<label for="';
	        $this->cadenaHTML .= $this->atributos['id'];
	        $this->cadenaHTML .= '" class="col-xs-';
	        $this->cadenaHTML .= $this->atributos['anchoEtiqueta'];
	        $this->cadenaHTML .= ' col-form-label">';
	        $this->cadenaHTML .= $this->atributos['etiqueta'];
	        $this->cadenaHTML .= '</label>';
	        $this->cadenaHTML .= '<div class="col-xs-';
	        $this->cadenaHTML .= $this->atributos['anchoCaja'];
	        $this->cadenaHTML .= '">';
	        
	         
		}
		
		if (isset ( $this->atributos ["dobleLinea"] ) && $this->atributos ["dobleLinea"]) {
			$this->cadenaHTML .= "<br>";
		}
		
		$this->cadenaHTML .= $this->cuadro_texto ();
		
		$this->cadenaHTML .= '</div>';
		
		$this->cadenaHTML .= '</div>';
		
// 		$this->cadenaHTML .= '</div>';

		unset($atributos);
		unset($this->atributos);
		
		return $this->cadenaHTML;
	}
	
	
	function cuadro_texto($atributos='') {
		
		
		
		$cadena = '<input ';
		
		if (! isset ( $this->atributos [self::TIPO] ) || $this->atributos [self::TIPO] != self::HIDDEN) {
			
			// Desde HtmlBase
			$cadena .= $this->definirAtributosGenerales ();
			
			$cadena .= $this->atributoClassCuadroTexto ();
			
			$cadena .= $this->atributosGeneralesCuadroTexto ();
		} else {
			
			$cadena .= "type='hidden' ";
			$cadena .= self::HTMLNAME . "'" . $this->atributos [self::ID] . "' ";
			$cadena .= "id='" . $this->atributos [self::ID] . "' ";
			if (isset ( $this->atributos [self::VALOR] )) {
				$cadena .= self::HTMLVALUE . "'" . $this->atributos [self::VALOR] . "' ";
			}
		}
		
		$cadena .= ">\n";
		return $cadena;
	}
	private function atributosGeneralesCuadroTexto() {
		$cadena = '';
		
		if (! isset ( $this->atributos [self::TIPO] )) {
			$this->atributos [self::TIPO] = "text";
		}
		
		$cadena .= "type='" . $this->atributos [self::TIPO] . "' ";
		
		if (isset ( $this->atributos [self::DESHABILITADO] ) && $this->atributos [self::DESHABILITADO]) {
			$cadena .= "disabled ";
		}
		
		if (isset ( $this->atributos [self::MINIMO] )) {
			$cadena .= "min=" . "'" . $this->atributos [self::MINIMO] . "' ";
		}
		
		if (isset ( $this->atributos [self::VALOR] )) {
			$cadena .= self::HTMLVALUE . "'" . $this->atributos [self::VALOR] . "' ";
		}
		
		if (isset ( $this->atributos [self::TAMANNO] )) {
			$cadena .= "size='" . $this->atributos [self::TAMANNO] . "' ";
		}
		
		if (! isset ( $this->atributos [self::MAXIMOTAMANNO] )) {
			$this->atributos [self::MAXIMOTAMANNO] = 100;
		}
		
		if (isset ( $this->atributos [self::PLACEHOLDER] )) {
			$cadena .= "placeholder='" . $this->atributos [self::PLACEHOLDER] . "' ";
		}
		
		$cadena .= "maxlength='" . $this->atributos [self::MAXIMOTAMANNO] . "' ";
		
		// Si utiliza algun evento especial
		if (isset ( $this->atributos [self::EVENTO] )) {
			$cadena .= " " . $this->atributos [self::EVENTO] . " ";
		}
		
		if (isset ( $this->atributos ["validar"] ) && $this->atributos ["validar"] == "required") {
			$cadena .= 'required="true"';
		}
		
		return $cadena;
	}
	private function atributoClassCuadroTexto() {
		$cadena = self::HTMLCLASS . "'";
		
		// --------------Atributo class --------------------------------
		
		if(isset ( $this->atributos [self::TIPO] ) & $this->atributos [self::TIPO] == "file"){
			$this->atributos[self::ESTILO] = 'file';
		}else{
			$this->atributos[self::ESTILO] = 'form-control ';
		}

		$cadena .= $this->atributos[self::ESTILO];
		
		return $cadena .= "' ";
		
		// ----------- Fin del atributo class ----------------------------
	}
	
}