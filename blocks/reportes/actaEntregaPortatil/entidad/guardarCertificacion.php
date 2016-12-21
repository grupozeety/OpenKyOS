<?php

namespace reportes\actaEntregaPortatil\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

use reportes\actaEntregaPortatil\entidad\Redireccionador;

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

        $url_firma_beneficiario = $_REQUEST['firmaBeneficiario'];

        $arreglo = array(
            'id_beneficiario' => $_REQUEST['id_beneficiario'],
            'fecha_entrega' => $_REQUEST['fecha_entrega'],
            'marca' => $_REQUEST['marca'],
            'modelo' => $_REQUEST['modelo'],
            'serial' => $_REQUEST['serial'],
            'procesador' => $_REQUEST['procesador'],
            'memoria_ram' => $_REQUEST['memoria_ram'],
            'disco_duro' => $_REQUEST['disco_duro'],
            'sistema_operativo' => $_REQUEST['sistema_operativo'],
            'camara' => $_REQUEST['camara'],
            'audio' => $_REQUEST['audio'],
            'bateria' => $_REQUEST['bateria'],
            'targeta_red_alambrica' => $_REQUEST['targeta_red_alambrica'],
            'targeta_red_inalambrica' => $_REQUEST['targeta_red_inalambrica'],
            'cargador' => $_REQUEST['cargador'],
            'pantalla' => $_REQUEST['pantalla'],
            'web_soporte' => $_REQUEST['web_soporte'],
            'telefono_soporte' => $_REQUEST['telefono_soporte'],
            'url_firma_beneficiario' => $url_firma_beneficiario,
        );
        $cadenaSql = $this->miSql->getCadenaSql('registrarActaEntrega', $arreglo);

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

