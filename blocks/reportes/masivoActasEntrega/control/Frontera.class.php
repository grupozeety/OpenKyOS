<?php
namespace reportes\masivoActas;
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

            switch ($_REQUEST['opcion']) {
                case 'editarCertificacion':
                    include_once $this->ruta . "frontera/informacionCertficado.php";
                    break;

                case 'resultadoActa':
                    include_once $this->ruta . "frontera/gestionActa.php";
                    break;

                default:
                    include_once $this->ruta . "frontera/consultarBeneficiario.php";
                    break;
            }

        } else {
            include_once $this->ruta . "frontera/formRegistro.php";
        }

    }
}
?>

