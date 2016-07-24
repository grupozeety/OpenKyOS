<?php

namespace core\general;

require_once ("Rangos.class.php");
require_once ("Tipos.class.php");
require_once ("Agregador.class.php");
/*
 * Se contruye una clase que reune las funciones de las clases Rangos y Tipos
 * con esto se busca hacer que las funciones privadas se puedan llamar de la misma forma
 * la otra ventaja es separan funcionalmente las funciones de validacion/evaluacion de rangos
 * y tipos. Por último permite registrar modularmente los demás componentes de validación
 * como son longitud del texto, restricción de carácteres y palabras. 
 */
class ValidadorCampos extends \Agregador{
    function __construct(){
        
        $this->aggregate('Rangos');
        $this->aggregate('Tipos');        
        
    }
}

?>
