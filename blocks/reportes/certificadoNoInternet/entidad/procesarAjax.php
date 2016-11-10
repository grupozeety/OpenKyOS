<?php
namespace reportes\certificadoNoInternet\entidad;

include_once "core/auth/SesionSso.class.php";

class procesarAjax {
    public $miConfigurador;
    public $sql;
    public function __construct($sql) {
        $this->miConfigurador = \Configurador::singleton();

        $this->ruta = $this->miConfigurador->getVariableConfiguracion("rutaBloque");

        $this->sql = $sql;

        $conexion = "interoperacion";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

           $sesion = \SesionSso::singleton ();
        $respuesta = $sesion->getParametrosSesionAbierta ();
        $rol = $respuesta ['description'] [0];
        $idusuario = $respuesta ['mail'] [0];
        if ($rol == 'Comisionador') {
        	$comisionador = true;
        } else {
        	$comisionador = false;
        }
         
        switch ($_REQUEST['funcion']) {

            case 'consultaBeneficiarios':
                
                $cadena='';
            	if($comisionador==true){
            		$cadena="AND id_comisionador='".$idusuario."'";
            	}
            	
                $cadenaSql = $this->sql->getCadenaSql('consultarBeneficiariosPotenciales', $cadena);

                $resultadoItems = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                foreach ($resultadoItems as $key => $values) {
                    $keys = array(
                        'value',
                        'data',
                    );
                    $resultado[$key] = array_intersect_key($resultadoItems[$key], array_flip($keys));
                }
                echo '{"suggestions":' . json_encode($resultado) . '}';
                break;

        }
    }
}

$miProcesarAjax = new procesarAjax($this->sql);

?>
