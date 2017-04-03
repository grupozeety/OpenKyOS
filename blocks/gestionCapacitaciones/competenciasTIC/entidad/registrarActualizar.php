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
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $_REQUEST['tiempo'] = time();

        switch ($_REQUEST['opcion']) {
            case 'registrarCompetencia':

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
                    'id_beneficiario' => $_REQUEST['id_beneficiario'],
                    'numero_contrato' => 681,
                    'codigo_simona' => "NO APLICA",
                    'region' => "KVD-R6",
                );

                $cadenaSql = $this->miSql->getCadenaSql('registroCompetencia', $arreglo);

                $this->proceso = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

                if (isset($this->proceso) && $this->proceso != null) {
                    Redireccionador::redireccionar("ExitoRegistro", $this->proceso);
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
}

$miProcesador = new FormProcessor($this->lenguaje, $this->sql);
