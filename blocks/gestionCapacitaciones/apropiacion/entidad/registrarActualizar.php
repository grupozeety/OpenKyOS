<?php
namespace gestionCapacitaciones\apropiacion\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

require_once 'Redireccionador.php';

class FormProcessor
{

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $archivos_datos;
    public $esteRecursoDB;

    public function __construct($lenguaje, $sql)
    {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;

        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");

        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");

        if (!isset($_REQUEST["bloqueGrupo"]) || $_REQUEST["bloqueGrupo"] == "") {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloque"] . "/";
            $this->rutaAbsoluta .= "/blocks/" . $_REQUEST["bloque"] . "/";
        } else {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
            $this->rutaAbsoluta .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
        }
        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        //Conexion a Base de Datos OtunWs
        $conexion = "otunWs";
        $this->esteRecursoDBOtunWS = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $_REQUEST['tiempo'] = time();

        switch ($_REQUEST['opcion']) {
            case 'registrarApropiacion':
                var_dump($_REQUEST);
                //Validación Actividad
                if ($_REQUEST['identificadorActividad'] != '') {

                    $arregloValidar = array(
                        'id_actividad' => $_REQUEST['identificadorActividad'],
                        'identificacion' => $_REQUEST['identificacion'],
                        'fecha_actividad' => $_REQUEST['fechaApropiacion'],

                    );

                    $cadenaSql = $this->miSql->getCadenaSql('consultarActividadValidar', $arregloValidar);

                    $validar_actividad = $this->esteRecursoDBOtunWs->ejecutarAcceso($cadenaSql, "busqueda");
                    if ($validar_actividad != false) {

                        Redireccionador::redireccionar("ErrorAsociacionActividad", $_REQUEST['identificadorActividad']);

                    }

                }

                // Creación identificador actividad

                if ($_REQUEST['identificadorActividad'] == '') {

                    $cadenaSql = $this->miSql->getCadenaSql('consultarIdentificadorActividad');

                    $id_actividad = $this->esteRecursoDBOtunWS->ejecutarAcceso($cadenaSql, "busqueda")[0];

                    if (is_null($id_actividad['max'])) {
                        $_REQUEST['identificadorActividad'] = 1;

                    } else {

                        $_REQUEST['identificadorActividad'] = $id_actividad['max'] + 1;
                    }

                }

                $arreglo = array(
                    'anio' => 2015,
                    'nit_operador' => "8301159934",
                    'dane_centro_poblado' => 'NA',
                    'dane_departamento' => $_REQUEST['departamento'],
                    'dane_institucion' => "NO APLICA",
                    'dane_municipio' => $_REQUEST['municipio'],
                    'tipo_actividad' => $_REQUEST['tipoActividad'],
                    'actividad' => $_REQUEST['actividad'],
                    'id_actividad' => $_REQUEST['identificadorActividad'],
                    'asistentes_actividad' => $_REQUEST['numeroAsistentes'],
                    'fecha_actividad' => $_REQUEST['fechaApropiacion'],
                    'personas_visitadas' => $_REQUEST['numeroPersonasVisitadas'],
                    'id_beneficiario' => $_REQUEST['id_beneficiario'],
                    'numero_contrato' => 681,
                    'region' => "KVD-R6",
                    'codigo_simona' => "NO APLICA",
                );

                $cadenaSql = $this->miSql->getCadenaSql('registrarApropiacion', $arreglo);

                $this->proceso = $this->esteRecursoDBOtunWS->ejecutarAcceso($cadenaSql, "busqueda");

                if (isset($this->proceso) && $this->proceso != null) {
                    Redireccionador::redireccionar("ExitoRegistro", $this->proceso);
                } else {
                    Redireccionador::redireccionar("ErrorRegistro");
                }

                break;

            case 'actualizarApropiacion':

                $arreglo = array(
                    'unidad' => $_REQUEST['unidad'],
                    'valor' => $_REQUEST['valor'],
                    'id_periodo' => $_REQUEST['id_periodo'],
                );

                $cadenaSql = $this->miSql->getCadenaSql('actualizarPeriodo', $arreglo);

                $this->proceso = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

                if (isset($this->proceso) && $this->proceso != null) {
                    Redireccionador::redireccionar("ExitoActualizacion", $this->proceso);
                } else {
                    Redireccionador::redireccionar("ErrorActualizacion");
                }

                break;
        }
    }
}

$miProcesador = new FormProcessor($this->lenguaje, $this->sql);
