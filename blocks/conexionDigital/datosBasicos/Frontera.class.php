<?php

namespace conexionDigital\datosBasicos;

if (!isset($GLOBALS ["autorizado"])) {
    include ("../index.php");
    exit();
}

include_once ("core/manager/Configurador.class.php");

class Frontera {

    var $ruta;
    var $sql;
    var $funcion;
    var $lenguaje;
    var $miFormulario;
    var $miConfigurador;

    function __construct() {

        $this->miConfigurador = \Configurador::singleton();
    }

    public function setRuta($unaRuta) {
        $this->ruta = $unaRuta;
    }

    public function setLenguaje($lenguaje) {
        $this->lenguaje = $lenguaje;
    }

    public function setFormulario($formulario) {
        $this->miFormulario = $formulario;
    }

    function frontera() {
        $this->html();
    }

    function setSql($a) {
        $this->sql = $a;
    }

    function setFuncion($funcion) {
        $this->funcion = $funcion;
    }

    function html() {

        $this->ruta = $this->miConfigurador->getVariableConfiguracion("rutaBloque");

        if (isset($_REQUEST ['opcion'])) {
            switch ($_REQUEST ['opcion']) {
                case "terminos" :
                    include_once ($this->ruta . "/formulario/terminos.php");
                    break;

                case "mostrar" :
                    include_once ($this->ruta . "/formulario/form.php");
                    break;

                case "indicador" :
                    include_once ($this->ruta . "/formulario/spagoBI.php");
                    break;

                case "registroMantenimiento" :
                    include_once ($this->ruta . "/formulario/registroMantenimiento.php");
                    break;

                case "consultaMantenimiento" :
                    include_once ($this->ruta . "/formulario/consultaMantenimiento.php");
                    break;
            }
        } else {

            include_once ($this->ruta . "/formulario/form.php");
        }
    }

}

?>
