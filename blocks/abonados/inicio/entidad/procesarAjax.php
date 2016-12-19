<?php
namespace cambioClave\entidad;
class procesarAjax {
    public $miConfigurador;
    public $sql;
    public function __construct($sql) {
        $this->miConfigurador = \Configurador::singleton();

        $this->ruta = $this->miConfigurador->getVariableConfiguracion("rutaBloque");

        $this->sql = $sql;

        $conexion = "interoperacion";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        switch ($_REQUEST['funcion']) {

            case 'guardarColor':
            	
            	$colores = array("color1" => $_REQUEST['color1'], "color2" => $_REQUEST['color2'], "color3" => $_REQUEST['color3']);
            	 
            	if($_REQUEST['existeColor'] == "true"){
            		$cadenaSql = $this->sql->getCadenaSql('actualizarColor', $colores);echo $cadenaSql;
            		$resultadoItems = $esteRecursoDB->ejecutarAcceso($cadenaSql, "actualizar");
            	}else {
            		$cadenaSql = $this->sql->getCadenaSql('guardarColor', $colores);echo $cadenaSql;
            		$resultadoItems = $esteRecursoDB->ejecutarAcceso($cadenaSql, "insertar");
            	}
            	
               
				
        }
    }
}

$miProcesarAjax = new procesarAjax($this->sql);

?>
