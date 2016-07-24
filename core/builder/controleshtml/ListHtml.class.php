<?php
/**
 * Generador de Listas Ordenadas y no ordenadas
 * @todo Listas anidadas
 * 
 * Listado de atributos que se requieren para definir el control:
 * 
 * $atributos['menu']	:	true la lista se le aplica la clase listaMenu
 * @todo Definir el estilo y las funciones javascript asociadas a esa clase
 * $atributos['id']		:	id del control. Equivale al atributo id de html.
 * $atributos ['items']	: Arreglo. Contiene el listado de items que contendrá la lista. El arreglo puede contener los
 * 						 siguientes elementos:
 * 						 $valor ['nombre']	: El nombre que aparece en la lista
 * 						 $valor ['icono']	: Nombre del ícono que aparece al lado del nombre. Se utiliza junto con jqueryui
 * 						 $valor ['toolTip']	: Texto que se mostrará cuando se pase el puntero sobre el elemento 
 * 						 $valor ['enlace']	: Si está definido indica que el nombre esta asociado a una URL 
 * 						 $valor ['urlCodificada']: Url asociada al nombre cuando es un enlace.
 * 						 	
 * $atributos ['estilo']	: Estilo que se aplicará a la lista p.e: 'jqueryui' o uno definido por el usuario
 * $atributos ['pestañas']	: Se utiliza para crear pestañas en controles jqueryui
 * $atributos ['enlaces']	: True el elemento se trata como un enlace. El arreglo items solo contiene una cadena en formato:
 * 							  'etiqueta|enlace'.
 * 
 * 
 */
require_once ("core/builder/HtmlBase.class.php");
class ListHTML extends HtmlBase {
	function listaNoOrdenada($atributos) {
		$this->setAtributos ( $atributos );
		$this->campoSeguro ();
		
		if (isset ( $this->atributos ['id'] )) {
			$this->cadenaHTML = "<ul id='" . $this->atributos ['id'] . "' ";
		} else {
			$this->cadenaHTML = "<ul ";
		}
		
		if (isset ( $this->atributos ['menu'] ) && $this->atributos ['menu']) {
			$this->cadenaHTML .= "class='listaMenu' ";
		}
		
		$this->cadenaHTML .= ">";
		
		foreach ( $this->atributos ["items"] as $clave => $valor ) {
			// La clave es la fila, el valor es un arreglo con los datos de cada columna
			// $arreglo[fila][columna]
			
			$this->cadenaHTML .= '<li ';
			
			if (isset ( $valor ['toolTip'] )) {
				$this->cadenaHTML .= " title='" . $valor ['toolTip'] . "' ";
			}
			
			$this->cadenaHTML .= '>';
			
			$this->procesarValor ( $valor, $clave );
			
			$this->cadenaHTML .= "</li>";
		}
		
		$this->cadenaHTML .= "</ul>";
		
		return $this->cadenaHTML;
	}
	private function procesarValor($valor, $clave) {
		if (isset ( $this->atributos ['menu'] ) && $this->atributos ['menu']) {
			$claseEnlace = "class='enlaceMenu' ";
		} else {
			$claseEnlace = '';
		}
		
		if (is_array ( $valor )) {
			
			if (isset ( $valor ['icono'] )) {
				$icono = '<span class="ui-accordion-header-icon ui-icon ' . $valor ['icono'] . '"></span>';
			} else {
				$icono = '';
			}
			
			if (isset ( $valor ['enlace'] ) && $this->atributos ['estilo'] == self::JQUERYUI) {
				$this->cadenaHTML .= "<a  id='pes" . $clave . "' " . $claseEnlace . " href='" . $valor ['urlCodificada'] . "'>";
				$this->cadenaHTML .= "<div id='tab" . $clave . "' class='ui-accordion ui-widget ui-helper-reset'>";
				$this->cadenaHTML .= "<span class='ui-accordion-header ui-state-default ui-accordion-icons ui-corner-all'>" . $icono . $valor ['nombre'] . "</span>";
				$this->cadenaHTML .= "</div>";
				$this->cadenaHTML .= "</a>";
			}
		} else {
			
			//Para manejar pestañas
			if (isset ( $this->atributos ["pestañas"] ) && $this->atributos ["pestañas"]) {
				$this->cadenaHTML .= "<a id='pes" . $clave . "' " . $claseEnlace . " href='#" . $clave . "'><div id='tab" . $clave . "'>" . $valor . "</div></a>";
			}elseif(isset ( $this->atributos ["enlaces"] ) && $this->atributos ["enlaces"]) {
				$enlace = explode ( '|', $valor );
				$this->cadenaHTML .= "<a href='" . $enlace [1] . "' " . $claseEnlace . ">" . $enlace [0] . "</a>";
			}else{
				//Una lista normal
				$this->cadenaHTML .=$valor; 
			}
			
		}
		
		return true;
	}
}