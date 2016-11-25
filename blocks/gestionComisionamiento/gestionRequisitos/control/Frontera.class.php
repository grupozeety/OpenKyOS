<?php

namespace gestionComisionamiento\gestionRequisitos;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

include_once "core/manager/Configurador.class.php";
class Frontera {
    public $ruta;
    public $sql;
    public $miEntidad;
    public $lenguaje;
    public $miFormulario;
    public $miConfigurador;
    public function __construct() {
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
    public function frontera() {
        $this->html();
    }
    public function setSql($a) {
        $this->sql = $a;
    }
    public function setEntidad($entidad) {
        $this->miEntidad = $entidad;
    }
    public function html() {
        include_once "core/builder/FormularioHtml.class.php";

        $this->ruta = $this->miConfigurador->getVariableConfiguracion("rutaBloque");
        $this->miFormulario = new \FormularioHtml();

        $miBloque = $this->miConfigurador->getVariableConfiguracion('esteBloque');
        $resultado = $this->miConfigurador->getVariableConfiguracion('errorFormulario');

        if (isset($_REQUEST['opcion'])) {

            if (isset($_REQUEST['proceso'])) {
                switch ($_REQUEST['proceso']) {

                    case 'verificarRequisitos':
                        include_once $this->ruta . "frontera/verificarRequisitos.php";
                        break;

                    case 'cargueRequisitos':
                        $_REQUEST['id_beneficiario'] = $_REQUEST['id_beneficiario'];
                        include_once $this->ruta . "frontera/requisitosBeneficiario.php";
                        break;

                    case 'gestionarContrato':
                        include_once $this->ruta . "frontera/gestionarContrato.php";
                        break;

                    default:
                        include_once $this->ruta . "frontera/consultaBeneficiario.php";
                        break;
                }
            } else {

                switch ($_REQUEST['opcion']) {

                    case 'mostrarContrato':
                        include_once $this->ruta . "frontera/contratoBeneficiario.php";
                        break;

                    case 'verArchivo':
                        include_once $this->ruta . "frontera/verArchivo.php";
                        break;

                    default:
                        include_once $this->ruta . "frontera/consultaBeneficiario.php";
                        break;
                }
            }
        } else {

            include_once $this->ruta . "frontera/consultaBeneficiario.php";
        }
    }
}
?>

