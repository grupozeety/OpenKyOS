<?php
namespace gestionComisionamiento\generacionActas\entidad;
include_once "core/builder/FormularioHtml.class.php";

class FormProcessor {

    public $miConfigurador;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $urlApiProyectos;
    public $informacion_agendamientos;

    public function __construct($sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->miFormulario = new \FormularioHtml();
        $_REQUEST['tiempo'] = time();

        /**
         * 1. Consultar Información Agendamiento
         **/
        $this->consultaInformacionAgendamiento();

        /**
         * 2. Estructurar Tabla Proyectos a Retornar
         **/

        $this->estruturarTabla();

        /**
         * 3. Retornar Tabla
         **/
        echo $this->contenidoTabla;

        exit;

    }

    public function consultaInformacionAgendamiento() {

        $conexion = "interoperacion";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $cadenaSql = $this->miSql->getCadenaSql('consultarAgendamientos');
        $this->informacion_agendamientos = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

    }

    public function estruturarTabla() {
        $atributosGlobales['campoSeguro'] = true;
        $tab = 1;
        $i = 1;
        foreach ($this->informacion_agendamientos as $key => $value) {

            // ---------------- CONTROL: Cuadro de Texto -----------
            $nombre = 'item' . $i;
            $atributos['id'] = $nombre;
            $atributos['nombre'] = $nombre;
            $atributos['marco'] = true;
            $atributos['estiloMarco'] = true;
            $atributos["etiquetaObligatorio"] = true;
            $atributos['columnas'] = 1;
            $atributos['dobleLinea'] = 1;
            $atributos['tabIndex'] = $tab;
            $atributos['etiqueta'] = '';
            $atributos['seleccionado'] = false;
            $atributos['evento'] = ' ';
            $atributos['eventoFuncion'] = ' ';
            $atributos['valor'] = base64_encode(json_encode($value));
            $atributos['deshabilitado'] = false;
            $tab++;

            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);

            $item = $this->miFormulario->campoCuadroSeleccion($atributos);

            $resultadoFinal[] = array(

                'numero_agendamiento' => $value['id_agendamiento'],
                'nodo' => $value['codigo_nodo'],
                'cantidad_beneficiarios' => $value['cantidad_beneficiarios'],
                'fecha_agendamiento' => $value['fecha_agendamiento'],
                'responsable' => $value['responsable'],
                'opcion' => $item,

            );
            $i++;
        }

        $total = count($resultadoFinal);

        $resultado = json_encode($resultadoFinal);

        $resultado = '{
                "recordsTotal":' . $total . ',
                "recordsFiltered":' . $total . ',
                "data":' . $resultado . '}';

        $this->contenidoTabla = $resultado;

    }

}

$miProcesador = new FormProcessor($this->sql);

?>

