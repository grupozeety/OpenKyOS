<?php

require_once 'enviar.php';

class Procesador {
	
    public $miConfigurador;
    public $miSql;
    public $enviar;

    public function __construct($sql) {

        $this->miConfigurador = \Configurador::singleton();

        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        $conexion = "interoperacion";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionApi', 'gmail');
        $this->datosConexion = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
        
        $this->enviar = new Enviar();
        $this->procesar();

    }
    
    public function procesar() {
        if (isset($_REQUEST['metodo'])) {
        	
            switch ($_REQUEST['metodo']) {

                case 'recuperarClave':
                    $resultado = $this->enviar->recuperarClave($this->datosConexion, $_REQUEST['destinatario'],$_REQUEST['link'],$_REQUEST['usuario']);
                    break;
            }
        }
    }
}

$api = new Procesador($this->sql);
