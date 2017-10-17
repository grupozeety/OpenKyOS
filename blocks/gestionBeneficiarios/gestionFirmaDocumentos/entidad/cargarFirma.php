<?php

namespace gestionBeneficiarios\gestionFirmaDocumentos\entidad;

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

        $this->rutaURL      = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");
        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");

        if (isset($_REQUEST['beneficiario']) && $_REQUEST['beneficiario'] == '') {
            Redireccionador::redireccionar('errorBeneficiario');
        }

        if (isset($_REQUEST['proceso']) && $_REQUEST['proceso'] != '1' && $_REQUEST['proceso'] != '2' && $_REQUEST['proceso'] != '3' && $_REQUEST['proceso'] != '4') {
            Redireccionador::redireccionar('errorProceso');
        }

        switch ($_REQUEST['proceso']) {
            case '1':
                $estado = 'FALSE';

                $cadenaSql = $this->miSql->getCadenaSql('actulizarBeneficiarios', $estado);

                $this->beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

                break;

            case '2':
                $estado = 'TRUE';

                $cadenaSql = $this->miSql->getCadenaSql('actulizarBeneficiarios', $estado);

                $this->beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");
                break;

            case '3':
                $estado = 'REVISION';

                $cadenaSql = $this->miSql->getCadenaSql('actulizarBeneficiariosInterventoria', $estado);

                $this->beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");
                break;

            case '4':
                $estado = 'APROBADO';

                $cadenaSql = $this->miSql->getCadenaSql('actulizarBeneficiariosInterventoria', $estado);

                $this->beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");
                break;

        }

        if ($this->beneficiario) {
            Redireccionador::redireccionar('exitoActualizacion');
        } else {
            Redireccionador::redireccionar('errorActualizacion');
        }
    }
}

$miProcesador = new GenerarReporteInstalaciones($this->sql);
