<?php

namespace reportes\actaEntregaServicios\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

use reportes\actaEntregaServicios\entidad\Redireccionador;

include_once 'Redireccionador.php';
class FormProcessor {
    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $archivos_datos;
    public $esteRecursoDB;
    public $datos_contrato;
    public $rutaURL;
    public $rutaAbsoluta;
    public $clausulas;
    public $registro_info_contrato;
    public function __construct($lenguaje, $sql) {
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
        // Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $_REQUEST['tiempo'] = time();

        /**
         * 1.
         * CargarArchivos en el Directorio
         */

        $this->cargarArchivos();

        /**
         * 2.
         * Procesar Informacion Contrato
         */

        $this->procesarInformacion();

        if ($_REQUEST['firmaBeneficiario'] != '') {

            include_once "guardarDocumentoCertificacion.php";
        }

        if ($this->registroActa) {
            Redireccionador::redireccionar("InsertoInformacionActa");
        } else {
            Redireccionador::redireccionar("NoInsertoInformacionActa");
        }
    }
    public function procesarInformacion() {
        var_dump($_REQUEST);

        $url_firma_beneficiario = $_REQUEST['firmaBeneficiario'];

        $arreglo = array(
            'id_beneficiario' => $_REQUEST['id_beneficiario'],
            'nombres' => $_REQUEST['nombres'],
            'primer_apellido' => $_REQUEST['primer_apellido'],
            'segundo_apellido' => $_REQUEST['segundo_apellido'],
            'identificacion' => $_REQUEST['numero_identificacion'],
            'tipo_documento' => $_REQUEST['tipo_documento'],
            'fecha_instalacion' => $_REQUEST['fecha_instalacion'],
            'tipo_beneficiario' => $_REQUEST['tipo_beneficiario'],
            'estrato' => $_REQUEST['estrato'],
            'direccion' => $_REQUEST['direccion'],
            'urbanizacion' => $_REQUEST['urbanizacion'],
            'departamento' => $_REQUEST['departamento'],
            'municipio' => $_REQUEST['municipio'],
            'geolocalizacion' => $_REQUEST['geolocalizacion'],
            'tipo_tecnologia' => $_REQUEST['tipo_tecnologia'],
            'mac_esc' => $_REQUEST['mac_esc'],
            'mac2_esc' => $_REQUEST['mac2_esc'],
            'serial_esc' => $_REQUEST['serial_esc'],
            'marca_esc' => $_REQUEST['marca_esc'],
            'cant_esc' => $_REQUEST['cant_esc'],
            'ip_esc' => $_REQUEST['ip_esc'],
            'hora_prueba_vs' => $_REQUEST['hora_prueba_vs'],
            'resultado_vs' => $_REQUEST['resultado_vs'],
            'unidad_vs' => $_REQUEST['unidad_vs'],
            'observaciones_vs' => $_REQUEST['observaciones_vs'],
            'hora_prueba_vb' => $_REQUEST['hora_prueba_vb'],
            'resultado_vb' => $_REQUEST['resultado_vb'],
            'unidad_vb' => $_REQUEST['unidad_vb'],
            'observaciones_vb' => $_REQUEST['observaciones_vb'],
            'hora_prueba_p1' => $_REQUEST['hora_prueba_p1'],
            'resultado_p1' => $_REQUEST['resultado_p1'],
            'unidad_p1' => $_REQUEST['unidad_p1'],
            'observaciones_p1' => $_REQUEST['observaciones_p1'],
            'hora_prueba_p2' => $_REQUEST['hora_prueba_p2'],
            'resultado_p2' => $_REQUEST['resultado_p2'],
            'unidad_p2' => $_REQUEST['unidad_p2'],
            'observaciones_p2' => $_REQUEST['observaciones_p2'],
            'hora_prueba_p3' => $_REQUEST['hora_prueba_p3'],
            'resultado_p3' => $_REQUEST['resultado_p3'],
            'unidad_p3' => $_REQUEST['unidad_p3'],
            'observaciones_p3' => $_REQUEST['observaciones_p3'],
            'hora_prueba_tr1' => $_REQUEST['hora_prueba_tr1'],
            'resultado_tr1' => $_REQUEST['resultado_tr1'],
            'unidad_tr1' => $_REQUEST['unidad_tr1'],
            'observaciones_tr1' => $_REQUEST['observaciones_tr1'],
            'hora_prueba_tr2' => $_REQUEST['hora_prueba_tr2'],
            'resultado_tr2' => $_REQUEST['resultado_tr2'],
            'unidad_tr2' => $_REQUEST['unidad_tr2'],
            'observaciones_tr2' => $_REQUEST['observaciones_tr2'],
            'url_firma_beneficiario' => $url_firma_beneficiario,
            'verificacion_tracert' => $_REQUEST['verificacion_tracert'],
            'reporte_fallos' => $_REQUEST['reporte_fallos'],
            'acceso_reportando' => $_REQUEST['acceso_reportando'],
            'paginas_visitadas' => $_REQUEST['paginas_visitadas'],
        );

        if ($_REQUEST['editar']) {
            $cadenaSql = $this->miSql->getCadenaSql('registrarActaEntrega', $arreglo);
        } else {
            $cadenaSql = $this->miSql->getCadenaSql('registrarActaEntrega', $arreglo);
        }

        $cadenaSql = str_replace("''", 'null', $cadenaSql);
        $this->registroActa = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

    }
    public function cargarArchivos() {
        $archivo_datos = '';
        foreach ($_FILES as $key => $archivo) {

            if ($archivo['error'] == 0) {

                $this->prefijo = substr(md5(uniqid(time())), 0, 6);
                /*
                 * obtenemos los datos del Fichero
                 */
                $tamano = $archivo['size'];
                $tipo = $archivo['type'];
                $nombre_archivo = str_replace(" ", "", $archivo['name']);
                /*
                 * guardamos el fichero en el Directorio
                 */
                $ruta_absoluta = $this->rutaAbsoluta . "/entidad/firmas/" . $this->prefijo . "_" . $nombre_archivo;

                $ruta_relativa = $this->rutaURL . "/entidad/firmas/" . $this->prefijo . "_" . $nombre_archivo;

                $archivo['rutaDirectorio'] = $ruta_absoluta;

                if (!copy($archivo['tmp_name'], $ruta_absoluta)) {
                    Redireccionador::redireccionar("ErrorCargarFicheroDirectorio");
                }

                $archivo_datos[] = array(
                    'ruta_archivo' => $ruta_relativa,
                    'nombre_archivo' => $archivo['name'],
                    'campo' => $key,
                );
            }
        }

        $this->archivos_datos = $archivo_datos;
    }
}

$miProcesador = new FormProcessor($this->lenguaje, $this->sql);
?>

