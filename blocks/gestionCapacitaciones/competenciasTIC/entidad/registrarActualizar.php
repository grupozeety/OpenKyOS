<?php
namespace gestionCapacitaciones\competenciasTIC\entidad;

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
        $conexion = "otunWs";
        $this->esteRecursoDBOtunWs = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $_REQUEST['tiempo'] = time();

        switch ($_REQUEST['opcion']) {

            case 'registrarCompetencia':

                //Validación de Actividad con Capacitado : Debe solo estar asociada a una sola actividad

                if ($_REQUEST['identificadorActividad'] != '') {

                    $arregloValidar = array(
                        'id_actividad' => $_REQUEST['identificadorActividad'],
                        'identificacion' => $_REQUEST['identificacion'],

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

                    $id_actividad = $this->esteRecursoDBOtunWs->ejecutarAcceso($cadenaSql, "busqueda")[0];

                    if (is_null($id_actividad['max'])) {
                        $_REQUEST['identificadorActividad'] = 1;

                    } else {

                        $_REQUEST['identificadorActividad'] = $id_actividad['max'] + 1;
                    }

                }

                //Actualización Información Beneficiarios en Caso de existir

                if ($_REQUEST['id_beneficiario'] != 'NO ASIGNADO') {

                    $arregloBeneficiario = array(
                        'pertenenciaEtnica' => $this->obtenerCodigoParametro($_REQUEST['pertenenciaEtnica']),
                        'ocupacion' => $this->obtenerCodigoParametro($_REQUEST['ocupacion']),
                        'nivelEducacion' => $this->obtenerCodigoParametro($_REQUEST['nivelEducativo']),
                        'genero' => ($_REQUEST['genero'] == 'F') ? 1 : (($_REQUEST['genero'] == 'M') ? 2 : null),
                        'idBeneficiario' => $_REQUEST['id_beneficiario'],
                        'estrato' => $_REQUEST['estrato'],
                        'correo' => $_REQUEST['correo'],
                        'edad' => $_REQUEST['edad'],
                        'telefono' => $_REQUEST['telefono'],
                    );

                    $cadenaSql = $this->miSql->getCadenaSql('actualizarBeneficiario', $arregloBeneficiario);

                    $beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

                    $cadenaSql = $this->miSql->getCadenaSql('actualizarContrato', $arregloBeneficiario);

                    $contrato = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

                }

                $arreglo = array(
                    'anio' => 2015,
                    'nit_operador' => "8301159934",
                    'id_capacitado' => $_REQUEST['identificacion'],
                    'dane_centro_poblado' => "NA",
                    'dane_departamento' => $_REQUEST['departamento'],
                    'dane_institucion' => "NO APLICA",
                    'dane_municipio' => $_REQUEST['municipio'],
                    'nombre_capacitado' => $_REQUEST['nombre'],
                    'correo_capacitado' => $_REQUEST['correo'],
                    'telefono_contacto' => $_REQUEST['telefono'],
                    'genero' => $_REQUEST['genero'],
                    'pertenecia_etnica' => $_REQUEST['pertenenciaEtnica'],
                    'nivel_educativo' => $_REQUEST['nivelEducativo'],
                    'servicio_capacitacion' => $_REQUEST['servicio'],
                    'detalle_servicio' => $_REQUEST['detalleServicio'],
                    'ocupacion' => $_REQUEST['ocupacion'],
                    'edad' => $_REQUEST['edad'],
                    'estrato' => $_REQUEST['estrato'],
                    'deserto' => $_REQUEST['desercion'],
                    'fecha_capacitacion' => $_REQUEST['fechaCapacitacion'],
                    'horas_capacitacion' => $_REQUEST['horas'],
                    'id_actividad' => $_REQUEST['identificadorActividad'],
                    'actividad' => $_REQUEST['actividad'],
                    'id_beneficiario' => $_REQUEST['id_beneficiario'],
                    'numero_contrato' => 681,
                    'codigo_simona' => "NO APLICA",
                    'region' => "KVD-R6",
                );

                $cadenaSql = $this->miSql->getCadenaSql('registroCompetencia', $arreglo);

                $this->proceso = $this->esteRecursoDBOtunWs->ejecutarAcceso($cadenaSql, "busqueda");

                if (isset($this->proceso) && $this->proceso != null) {
                    Redireccionador::redireccionar("ExitoRegistro", $this->proceso[0][0]);
                } else {
                    Redireccionador::redireccionar("ErrorRegistro");
                }

                break;

            case 'actualizarCompetencia':

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

    public function obtenerCodigoParametro($codigoHomologado)
    {

        $cadenaSql = $this->miSql->getCadenaSql('consultarCodigoParametro', $codigoHomologado);

        $codigo = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0]['codigo'];

        return $codigo;

    }

}

$miProcesador = new FormProcessor($this->lenguaje, $this->sql);
