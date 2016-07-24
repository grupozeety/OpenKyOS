<?php

// Evitar un acceso directo a este archivo
if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}

// Todo bloque debe implementar la interfaz Bloque
include_once ("core/builder/Bloque.interface.php");

include_once ("core/manager/Configurador.class.php");

// Elementos que constituyen un bloque típico CRUD.

// Interfaz gráfica
include_once ("Frontera.class.php");

// Funciones de procesamiento de datos
include_once ("Funcion.class.php");

// Compilación de clausulas SQL utilizadas por el bloque
include_once ("Sql.class.php");

// Mensajes
include_once ("Lenguaje.class.php");

// Esta clase actua como control del bloque en un patron FCE

// Para evitar redefiniciones de clases el nombre de la clase del archivo bloque debe corresponder al nombre del bloque
// precedida por la palabra Bloque

if (class_exists ( 'Bloquebanner' ) === false) {
	class Bloquebanner implements Bloque {
		var $nombreBloque;
		var $miFuncion;
		var $miSql;
		var $miConfigurador;
		public 

	
    
    function __construct($esteBloque, $lenguaje = "") {
        
        // El objeto de la clase Configurador debe ser único en toda la aplicación
        $this->miConfigurador = Configurador::singleton ();
        
        $ruta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
        $rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );
        
        if (! isset ( $esteBloque ["grupo"] ) || $esteBloque ["grupo"] == "") {
            $ruta .= "/blocks/" . $esteBloque ["nombre"] . "/";
            $rutaURL .= "/blocks/" . $esteBloque ["nombre"] . "/";
        } else {
            $ruta .= "/blocks/" . $esteBloque ["grupo"] . "/" . $esteBloque ["nombre"] . "/";
            $rutaURL .= "/blocks/" . $esteBloque ["grupo"] . "/" . $esteBloque ["nombre"] . "/";
        }
        
        $this->miConfigurador->setVariableConfiguracion ( "rutaBloque", $ruta );
        $this->miConfigurador->setVariableConfiguracion ( "rutaUrlBloque", $rutaURL );
        
        $nombreClaseFuncion = "Funcion" . $esteBloque ["nombre"];
        $this->miFuncion = new $nombreClaseFuncion ();
        
        $nombreClaseSQL = "Sql" . $esteBloque ["nombre"];
        $this->miSql = new $nombreClaseSQL ();
        
        $nombreClaseFrontera = "Frontera" . $esteBloque ["nombre"];
        $this->miFrontera = new $nombreClaseFrontera ();
        
        $nombreClaseLenguaje = "Lenguaje" . $esteBloque ["nombre"];
        $this->miLenguaje = new $nombreClaseLenguaje ();
    
    }
		public 

	
    
    function bloque() {
        
        if (isset ( $_REQUEST ['botonCancelar'] ) && $_REQUEST ['botonCancelar'] == "true") {
            $this->miFuncion->redireccionar ( "paginaPrincipal" );
        } else {
            
            if (! isset ( $_REQUEST ['action'] )) {
                
                $this->miFrontera->setSql ( $this->miSql );
                $this->miFrontera->setFuncion ( $this->miFuncion );
                $this->miFrontera->setLenguaje ( $this->miLenguaje );
                $this->miFrontera->frontera ();
            } else {
                
                $this->miFuncion->setSql ( $this->miSql );
                $this->miFuncion->setFuncion ( $this->miFuncion );
                $this->miFuncion->setLenguaje ( $this->miLenguaje );
                $this->miFuncion->action ();
            }
        }
    
    }
}
}
// @ Crear un objeto bloque especifico
// El arreglo $unBloque está definido en el objeto de la clase ArmadorPagina o en la clase ProcesadorPagina

$estaClase = "Bloque" . $unBloque ["nombre"];

$this->miConfigurador->setVariableConfiguracion ( "esteBloque", $unBloque );

if (isset ( $lenguaje )) {
	$esteBloque = new $estaClase ( $unBloque, $lenguaje );
} else {
	$esteBloque = new $estaClase ( $unBloque );
}

$esteBloque->bloque ();

?>
