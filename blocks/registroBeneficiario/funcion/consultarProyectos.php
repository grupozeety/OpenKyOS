<?php
namespace reportes\instalacionesGenerales\entidad;

include_once "core/builder/FormularioHtml.class.php";
class FormProcessor {

    public $miConfigurador;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $urlApiProyectos;
    public $proyectos;
    public $Respuesta;
    public $proyectosConsultados;

    public function __construct($sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->miFormulario = new \FormularioHtml();

        $_REQUEST['tiempo'] = time();

        /**
         * 1. Construcción Url Api Proyectos
         **/
        $this->crearUrlProyectos();

        /**
         * 2. Obtener Proyectos de Api
         **/

        $this->obtenerProyectos();

        /**
         * 2. Obtener Proyectos de Api
         **/

        $this->filtrarProyectosPrincipales();

        /**
         * 3. Obtener detalle Proyectos
         **/

        $this->obtenerDetalleProyectos();

        /**
         * 5. Estructurar Tabla Proyectos a Retornar
         **/

        $this->estruturarRespuesta();

        /**
         * 6. Retornar Resultado
         **/
        echo $this->Respuesta;

        exit;

    }

    public function filtrarProyectosPrincipales() {

        foreach ($this->proyectos as $key => $value) {

            $valor = strpos($value['name'], "CABECERA");

            $val = strpos($value['description'], 'Proyecto/Urbanizacion');

            if ($valor === false && $value['identifier'] != 'ins' && !is_numeric($val)) {

                unset($this->proyectos[$key]);

            }

        }
        $this->proyectosConsultados = $this->proyectos;
        
        foreach ($this->proyectos as $key => $value) {

            $val = strpos($value['description'], 'Proyecto/Urbanizacion');

            if (!is_numeric($val)) {

                unset($this->proyectos[$key]);
            }

        }

    }
    public function estruturarRespuesta() {
        $atributosGlobales['campoSeguro'] = true;
        $tab = 1;
        $i = 1;
        foreach ($this->proyectos as $key => $value) {
            $resultadoFinal[] = array(

                'id' => $value['id'],
                'urbanizacion' =>$value['custom_fields']['2']['value'],
            	'departamento' => $value['custom_fields']['0']['value'],
            	'municipio' => $value['custom_fields']['1']['value'],
            );
            $i++;
        }
        $this->Respuesta = json_encode($resultadoFinal);
    }

    public function obtenerDetalleProyectos() {

        foreach ($this->proyectos as $key => $value) {

            $urlDetalle = $this->crearUrlDetalleProyectos($value['id']);

            $detalle = file_get_contents($urlDetalle);

            $detalle = json_decode($detalle, true);

            $this->proyectos[$key]['custom_fields'] = $detalle['custom_fields'];

        }

    }

    public function crearUrlDetalleProyectos($var = '') {

        // URL base
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $url .= "/index.php?";
        // Variables
        $variable = "pagina=openKyosApi";
        $variable .= "&procesarAjax=true";
        $variable .= "&action=index.php";
        $variable .= "&bloqueNombre=" . "llamarApi";
        $variable .= "&bloqueGrupo=" . "";
        $variable .= "&tiempo=" . $_REQUEST['tiempo'];
        $variable .= "&metodo=proyectosDetalle";
        $variable .= "&id_proyecto=" . $var;

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $urlApi = $url . $cadena;

        return $urlApi;
    }
    public function obtenerProyectos() {
        $proyectos = file_get_contents($this->urlApiProyectos);
        $this->proyectos = json_decode($proyectos, true);
    }

    public function crearUrlProyectos() {

        // URL base
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $url .= "/index.php?";
        // Variables
        $variable = "pagina=openKyosApi";
        $variable .= "&procesarAjax=true";
        $variable .= "&action=index.php";
        $variable .= "&bloqueNombre=" . "llamarApi";
        $variable .= "&bloqueGrupo=" . "";
        $variable .= "&tiempo=" . $_REQUEST['tiempo'];
        $variable .= "&metodo=proyectosGeneral";

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $this->urlApiProyectos = $url . $cadena;

    }

}

$miProcesador = new FormProcessor($this->sql);

?>

