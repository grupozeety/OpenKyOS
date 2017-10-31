<?php
namespace gestionCapacitaciones\casosExito\entidad;

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

            case 'registrarCasoExito':
                //Validación Beneficiario

                if ($_REQUEST['id_beneficiario'] != '') {

                    $cadenaSql = $this->miSql->getCadenaSql('consultarBeneficiario', $_REQUEST['id_beneficiario']);
                    $beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                    if (!$beneficiario) {
                        Redireccionador::redireccionar("ErrorValidacionBeneficiario");
                    }
                } else {
                    Redireccionador::redireccionar("ErrorValidacionBeneficiario");
                }

                $arreglo = array(
                    'anio' => 2015,
                    'nit_operador' => "8301159934",
                    'cedula' => $_REQUEST['cedulaAdmin'],
                    'nombre_administrador' => $_REQUEST['nombreAdmin'],
                    'telefono_fijo_admin' => $_REQUEST['telefonoAdmin'],
                    'telefono_celular_admin' => $_REQUEST['celularAdmin'],
                    'direccion_email_admin' => $_REQUEST['emailAdmin'],
                    'perfil_facebook_admin' => $_REQUEST['perfilAdmin'],
                    'email_facebook_admin' => $_REQUEST['emailPerfilAdmin'],
                    'cedula_coordinador' => $_REQUEST['cedulaCoord'],
                    'telefono_fijo_coord' => $_REQUEST['telefonoCoord'],
                    'telefono_celular_coord' => $_REQUEST['celularCoord'],
                    'direccion_email_coord' => $_REQUEST['emailCoord'],
                    'perfil_facebook_coord' => $_REQUEST['perfilCoord'],
                    'email_facebook_coord' => $_REQUEST['emailPerfilCoord'],
                    'titulo_caso_exit' => $_REQUEST['titulo'],
                    'etiqueta' => $_REQUEST['etiqueta'],
                    'resumen' => $_REQUEST['resumen'],
                    'testimonio' => $_REQUEST['testimonio'],
                    'contexto' => $_REQUEST['contexto'],
                    'imagen_1' => $_REQUEST['imagen1'],
                    'imagen_2' => $_REQUEST['imagen2'],
                    'imagen_3' => $_REQUEST['imagen3'],
                    'codigo_embebido' => $_REQUEST['codigo'],
                    'categorizacion_aprendizaje' => $_REQUEST['categoriaAprendizaje'],
                    'categorizacion_apropiacion' => $_REQUEST['categoriaApropiacion'],
                    'relacion_plan' => implode(",", $_REQUEST['relacionPlan']),
                    'id_beneficiario' => $_REQUEST['id_beneficiario'],
                    'dane_centro_poblado' => "NO APLICA",
                    'dane_departamento' => $_REQUEST['departamento'],
                    'dane_institucion' => "NO APLICA",
                    'dane_municipio' => $_REQUEST['municipio'],
                    'numero_contrato' => 681,
                    'codigo_simona' => "NO APLICA",
                    'region' => "KVD-R6",
                );

                $cadenaSql = $this->miSql->getCadenaSql('registroExitoCaso', $arreglo);

                $this->proceso = $this->esteRecursoDBOtunWs->ejecutarAcceso($cadenaSql, "acceso");

                if (isset($this->proceso) && $this->proceso != null) {
                    Redireccionador::redireccionar("ExitoRegistro");
                } else {
                    Redireccionador::redireccionar("ErrorRegistro");
                }

                break;

            case 'actualizarCasoExito':

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
