<?php
namespace gestionBeneficiarios\aprobacionContrato\entidad;
class procesarAjax {
    public $miConfigurador;
    public $sql;
    public function __construct($sql) {
        $this->miConfigurador = \Configurador::singleton();

        $this->ruta = $this->miConfigurador->getVariableConfiguracion("rutaBloque");

        $this->sql = $sql;

        switch ($_REQUEST['funcion']) {

            case 'consultarContratos':

            /**
             * Código de Logica Procesar Ajax
             */

                break;

        }
    }
}

$miProcesarAjax = new procesarAjax($this->sql);

?>
