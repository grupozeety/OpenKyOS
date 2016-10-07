<?php

require dirname(__FILE__) . '/FrappeClient.php';
require dirname(__FILE__) . '/OpenProject.php';

class Consultar {
    public $ordenVenta = '';
    public $error;
    public $itemOrdenVenta = array();
    public $itemNotaEntrega = array();
    public $notaEntrega = '';
    public $numeroFactura = '';
    public $cantidad = 0;
    public $producto = '0';
    public $clientFrappe;
    public $clientOpenProject;
    public $expenseAccount;
    public $debitAccount;
    public $debitToAccount;
    public $name;
    public $miConfigurador;
    public $sql;

    public function configurarERPNext($datosConexion) {

        try {
            $this->clientFrappe = new FrappeClient();
            $this->clientFrappe->configurar($datosConexion);
        } catch (Exception $e) {
            var_dump($e);
        }
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        });
    }

    public function configurarOpenProject($datosConexion) {

        try {
            $this->clientOpenProject = new OpenProject();
            $this->clientOpenProject->configurar($datosConexion);
        } catch (Exception $e) {
            var_dump($e);
        }
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        });
    }

    public function codificarNombre($nombre) {

        include_once "core/builder/FormularioHtml.class.php";

        $miFormulario = new \FormularioHtml();

        if (!isset($_REQUEST['tiempo'])) {
            $_REQUEST['tiempo'] = time();
        }
        //Estas funciones se llaman desde ajax.php y estas a la vez realizan las consultas de Sql.class.php

        $_REQUEST['ready'] = true;

        return $miFormulario->campoSeguro($nombre);

    }

    public function obtenerAlmacen($datosConexion) {

        $this->configurarERPNext($datosConexion);

        $data = array(
        );

        $fields = array(
            "name",
            "default_warehouse",
            "stock_uom",
        );

        $result = $this->clientFrappe->search("Item", $data, $fields);

        if (!empty($result->body->data)) {

            echo json_encode($result->body->data);

        }

        return false;

    }

    public function obtenerNombreAlmacen($nombre) {

        $data = array(
            "name" => str_replace(' ', '%20', $nombre),
        );

        $fields = array(
            "warehouse_name",
        );

        $result = $this->clientFrappe->search("Warehouse", $data, $fields);

        if (!empty($result->body->data)) {

            echo json_encode($result->body->data[0]->warehouse_name);

        }

        return false;

    }

    public function obtenerUrbanizaciones($datosConexion) {

        $this->configurarOpenProject($datosConexion);

        $this->name = 'name';

        $data = '';

        $fields = 'projects';

        $result = $this->clientOpenProject->search("", $data, $fields);

        $arreglo = array();

        if (!empty($result)) {

            $tree = $this->buildTree($result->body['projects']);

            $tree = $tree[0]['nodes'][5]['nodes'];

            foreach ($tree as $key => $value) {

                $isCabecera = strpos($value['text'], "Cabecera");
                $isWman = strpos($value['text'], "wMAN");

                if (($isCabecera !== false) | ($isWman !== false)) {
                    unset($tree[$key]);
                }
            }

            echo json_encode($tree);

        }

        return false;

    }
    public function obtenerProjectos($datosConexion) {

        $this->configurarOpenProject($datosConexion);

        $this->name = 'name';

        $data = '';

        $fields = 'projects';

        $result = $this->clientOpenProject->search("", $data, $fields);

        $arreglo = array();

        if (!empty($result)) {

            $tree = $this->buildTree($result->body['projects']);
            print_r($tree[0]['nodes'][5]['nodes']);

            echo json_encode($tree);

        }
        return false;

    }

    public function obtenerActividades($datosConexion, $proyecto) {

        $this->configurarOpenProject($datosConexion);

        $this->name = 'subject';

        $data = $proyecto;

        $fields = 'planning_elements';

        $result = $this->clientOpenProject->search("projects", $data, $fields);

        if (!empty($result)) {

            $tree = $this->buildTree($result->body['planning_elements']);

            echo json_encode($tree);

        }
        return false;

    }

    public function buildTree(array &$elements, $parentId = 0) {
        $branch = array();

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $branch[] = array('text' => $element[$this->name], 'custom' => $element['id'], $element, 'nodes' => $children);
                } else {
                    $branch[] = array('text' => $element[$this->name], 'custom' => $element['id'], $element);
                }
//                 unset($elements[$element['id']]);
            }
        }
        return $branch;
    }

    public function obtenerOrdenTrabajo($datosConexion) {

        $this->configurarERPNext($datosConexion);

        $data = array(
        );

        $fields = array(
            "id_orden_trabajo",
            "name",
            "purpose",
        );

        $result = $this->clientFrappe->search("Stock Entry", $data, $fields);

        if (!empty($result->body->data)) {
            echo json_encode($result->body->data);

        }

        return false;

    }

    public function obtenerOrdenTrabajoModificada($datosConexion, $nombre) {

        $this->configurarERPNext($datosConexion);

        $data = array(
            "project" => str_replace(' ', '%20', $nombre),
        );

        $fields = array(
            "id_orden_trabajo",
            "descripcion_orden",
            "purpose",
            "name",
        );

        $result = $this->clientFrappe->search("Stock Entry", $data, $fields);

        if (!empty($result->body->data)) {
            echo json_encode($result->body->data);

        }

        return false;

    }

    public function obtenerProyectoErp($datosConexion) {

        $this->configurarERPNext($datosConexion);

        $data = array(

        );

        $fields = array(
            "project",
        );

        $result = $this->clientFrappe->search("Stock Entry", $data, $fields);

        if (!empty($result->body->data)) {

            echo json_encode($result->body->data);

        }

        return false;

    }

    public function obtenerMaterialesOrdenModificado($datosConexion, $parent) {

        $this->configurarERPNext($datosConexion);

        $materiales = array();

        $parent = json_decode($parent, true);

        foreach ($parent as $nombre) {
            $data = array(
                "parent" => str_replace(' ', '%20', $nombre),
            );

            $fields = array(
                "name",
                "item_name",
                "uom",
                "description",
                "item_code",
                "qty",
                "parent",

            );

            $result = $this->clientFrappe->search("Stock Entry Detail", $data, $fields);

            $contador = 0;

            foreach ($result->body->data as $data) {
                $data->{"material"} = $this->codificarNombre("material:" . $data->name . ":" . $data->item_name . ":" . $data->qty . ":" . $data->parent);
                $contador++;
            }

            if (!empty($result->body->data)) {

                $materiales = array_merge($materiales, $result->body->data);

            }

        }

        if (!empty($result->body->data)) {

            echo json_encode($materiales);

        }

        return false;

    }

    public function obtenerMaterialesOrden($datosConexion, $nombre) {
        $this->configurarERPNext($datosConexion);
        $data = array(
            "parent" => str_replace(' ', '%20', $nombre),
        );
        $fields = array(
            "name",
            "item_name",
            "uom",
            "description",
            "item_code",
            "qty",
            "parent",
        );
        $result = $this->clientFrappe->search("Stock Entry Detail", $data, $fields);
        $contador = 0;
        foreach ($result->body->data as $data) {
            $data->{"material"} = $this->codificarNombre("material:" . $data->name . ":" . $data->item_name . ":" . $data->qty . ":" . $data->parent);
            $contador++;
        }
        if (!empty($result->body->data)) {
            echo json_encode($result->body->data);
        }
        return false;
    }

    public function obtenerDetalleOrden($datosConexion, $nombre) {

        $this->configurarERPNext($datosConexion);

        $data = array(
            "name" => str_replace(' ', '%20', $nombre),
        );

        $fields = array(
            "name",
            "id_orden_trabajo",
            "project",
            "descripcion_orden",
        );

        $result = $this->clientFrappe->search("Stock Entry", $data, $fields);

        if (!empty($result->body->data)) {

            echo json_encode($result->body->data);

        }

        return false;

    }
    /**
     * Funcion Consultar Projectos-Salida en ErpNext
     * Consultar los proyectos relacionados con la salida
     * Autor: Verdugo,S
     * Version : 1.0.0.0
     * Fecha : 2016/08/25
     **/
    public function obtenerProjectosSalida($datosConexion = '') {

        $this->configurarERPNext($datosConexion);

        $data = array(
        );

        $fields = array(
            "project",
        );

        $result = $this->clientFrappe->search("Stock Entry", $data, $fields);

        if (!empty($result->body->data)) {
            $array = json_decode(json_encode($result->body->data), True);

            foreach ($array as $value) {
                if (!is_null($value['project'])) {
                    $proyectos[] = $value['project'];
                }

            }

            // Eliminar Duplicados
            $proyectos = array_unique($proyectos);

            echo json_encode($proyectos);

        }

        return false;

    }

    /**
     * Funcion Consultar Identificadores Salida en ErpNext
     * Consultar los identificadores relacionados con la salida
     * Autor: Verdugo,S
     * Version : 1.0.0.0
     * Fecha : 2016/08/26
     **/
    public function obtenerIdentificadoresSalida($datosConexion = '', $proyecto = '') {

        $this->configurarERPNext($datosConexion);

        $data = array(
            "project" => str_replace(' ', '%20', $proyecto),
            "purpose" => "Material%20Issue",
        );

        $fields = array(
            "name",
        );

        $result = $this->clientFrappe->search("Stock Entry", $data, $fields);

        if (!empty($result->body->data)) {

            $array = json_decode(json_encode($result->body->data), True);

            foreach ($array as $value) {
                if (!is_null($value['name'])) {
                    $idSalida[] = $value['name'];
                }

            }

            // Eliminar Duplicados
            $idSalida = array_unique($idSalida);

            echo json_encode($idSalida);

        }

        return false;

    }

    /**
     * Funcion Consultar Projectos de OpenProyect
     * Consultar los proyectos en General
     * Autor: Verdugo,S
     * Version : 1.0.0.0
     * Fecha : 2016/09/20
     **/
    public function obtenerProjectosGeneral($datosConexion) {

        $this->configurarOpenProject($datosConexion);

        $this->name = 'name';

        $data = '';

        $fields = 'projects';

        $result = $this->clientOpenProject->search("", $data, $fields);

        if (!empty($result)) {

            $proyectos = ($result->body['projects']);

            echo json_encode($proyectos);

        }
        return false;

    }

    /**
     * Funcion Consultar el detalle del Proyecto
     * Consultar los detalles del Proyecto
     * Autor: Verdugo,S
     * Version : 1.0.0.0
     * Fecha : 2016/09/20
     **/
    public function obtenerDetalleProjecto($datosConexion, $variable = '') {

        $this->configurarOpenProject($datosConexion);

        $this->name = 'name';

        $data = '';

        $tema = 'projects';

        $id_proyecto = $variable;

        $result = $this->clientOpenProject->search($tema, $data, $id_proyecto);

        if (!empty($result)) {

            $proyectos = ($result->body['project']);

            echo json_encode($proyectos);

        }
        return false;

    }

    /**
     * Funcion Consultar el Paquetes  Trabajo del Proyecto
     * Consultar los detalles del Proyecto
     * Autor: Verdugo,S
     * Version : 1.0.0.0
     * Fecha : 2016/09/20
     **/
    public function obtenerPaquetesTrabajo($datosConexion, $variable = '') {

        $this->configurarOpenProject($datosConexion);

        $this->name = 'name';

        $data = $variable;

        $tema = 'projects';

        $paquetesTrabajo = 'planning_elements';

        $result = $this->clientOpenProject->search($tema, $data, $paquetesTrabajo);

        if (!empty($result)) {

            $paquetesTrabajo = ($result->body['planning_elements']);

            echo json_encode($paquetesTrabajo);

        }
        return false;

    }

    /**
     * Funcion Consultar Actividades Con Respectos a un Paquete de Trabajo Particular
     * Autor: Verdugo,S
     * Version : 1.0.0.0
     * Fecha : 2016/09/22
     **/
    public function obtenerActividadesPaquetesTrabajo($datosConexion, $variable = '') {

        $this->configurarOpenProject($datosConexion);

        $this->name = 'name';

        $data = $variable;

        $tema = 'work_packages';

        $paquetesTrabajo = 'activities';

        $result = $this->clientOpenProject->search($tema, $data, $paquetesTrabajo, 'v3');

        if (!empty($result)) {

            $paquetesTrabajo = ($result->body['_embedded']['elements']);

            echo (json_encode($paquetesTrabajo));

        }
        return false;

    }

    /**
     * Funcion Crear Paquete de Trabajo en Proyecto Particular
     * Autor: Verdugo,S
     * Version : 1.0.0.0
     * Fecha : 2016/10/06
     **/
    public function crearPaqueteTrabajo($datosConexion, $variable = '') {

        $this->configurarOpenProject($datosConexion);

        $this->name = 'name';

        $paquetesTrabajo = 'work_packages';

        $proyecto = $variable['proyecto'];

        $tema = 'projects';

        $result = $this->clientOpenProject->search($tema, $proyecto, $paquetesTrabajo, 'v3', 'POST', $variable);

        if (!empty($result)) {

            $paquetesTrabajo = ($result->body['_links']);

            echo (json_encode($paquetesTrabajo));

        }
        return false;

    }
}

?>
