<?php

namespace gestionBeneficiarios\gestionEstadoBeneficiarios\entidad;

include_once 'Redireccionador.php';

class GenerarReporteInstalaciones
{
    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $proyectos;
    public $proyectos_general;
    public $directorio_archivos;
    public $ruta_directorio = '';

    public function __construct($sql)
    {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        $_REQUEST['tiempo'] = time();

        $conexion = "interoperacion";

//        $conexion = "produccion";

        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");
        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");

        if (isset($_REQUEST['beneficiario']) && $_REQUEST['beneficiario']=='') {
            Redireccionador::redireccionar('errorBeneficiario');
        }

        if (isset($_REQUEST['proceso']) && $_REQUEST['proceso'] !='1' && $_REQUEST['proceso'] !='2') {
            Redireccionador::redireccionar('errorProceso');
        }

        switch ($_REQUEST['proceso']) {
            case '1':
              $estado='FALSE';
              break;

            case '2':
              $estado='TRUE';
              break;

          }

        $cadenaSql = $this->miSql->getCadenaSql('actulizarBeneficiarios', $estado);

        $this->beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

        if ($this->beneficiario) {
            Redireccionador::redireccionar('exitoActualizacion');
        } else {
            Redireccionador::redireccionar('errorActualizacion');
        }
    }
}

$miProcesador = new GenerarReporteInstalaciones($this->sql);
