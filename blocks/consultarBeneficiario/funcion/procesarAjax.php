<?php

$conexion = "interoperacion";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
if ($_REQUEST['funcion'] == "consultarBeneficiarios") {

    $cadenaSql = $this->sql->getCadenaSql('consultarBeneficiario');
    $resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

    for ($i = 0; $i < count($resultado); $i++) {

        $resultadoFinal[] = array(
            'urbanizacion' => $resultado[$i]['urbanizacion'],
            'nombre' => $resultado[$i]['nombre'],
            'identificacion' => $resultado[$i]['identificacion'],
            'tipo_beneficiario' => $resultado[$i]['tipo_beneficiario'],
            'id_beneficiario' => $resultado[$i]['id_beneficiario'],
        );
    }

    $total = count($resultadoFinal);

    $resultado = json_encode($resultadoFinal);

    $resultado = '{
                "recordsTotal":' . $total . ',
                "recordsFiltered":' . $total . ',
				"data":' . $resultado . '}';

    echo $resultado;

} else if ($_REQUEST['funcion'] == "inhabilitarBeneficiario") {

    $conexion = "interoperacion";
    $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

    $cadenaSql = $this->sql->getCadenaSql('inhabilitarBeneficiario', $_REQUEST['valor']);
    $resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "actualizar");

    echo $resultado;

} else if ($_REQUEST['funcion'] == "redireccionar") {

    include_once "core/builder/FormularioHtml.class.php";

    $miFormulario = new \FormularioHtml();

    if (!isset($_REQUEST['tiempo'])) {
        $_REQUEST['tiempo'] = time();
    }
    //Estas funciones se llaman desde ajax.php y estas a la vez realizan las consultas de Sql.class.php

    $_REQUEST['ready'] = true;

    $valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar($_REQUEST['valor'] . $_REQUEST['id']);

    $enlace = $_REQUEST['directorio'] . '=' . $valorCodificado;

    echo json_encode($enlace);

} else if ($_REQUEST['funcion'] == "consultaBeneficiarios") {
    $cadenaSql = $this->sql->getCadenaSql('consultarBeneficiariosPotenciales');

    $resultadoItems = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

    foreach ($resultadoItems as $key => $values) {
        $keys = array(
            'value',
            'data',
        );
        $resultado[$key] = array_intersect_key($resultadoItems[$key], array_flip($keys));
    }
    echo '{"suggestions":' . json_encode($resultado) . '}';

}

?>