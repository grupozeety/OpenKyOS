<?php
/**
 * Pagina.class.php
 * 
 *  Implementa el patrón Fachada para el paquete builder.
 */
require_once ("core/manager/Configurador.class.php");
require_once ("core/builder/builderSql.class.php");
require_once ("core/builder/ArmadorPagina.class.php");
require_once ("core/builder/ProcesadorPagina.class.php");
include_once ("core/crypto/Encriptador.class.php");

class Pagina {
    
    var $miConfigurador;
    
    var $recursoDB;
    
    var $pagina;
    
    var $generadorClausulas;
    
    var $tipoError;
    
    var $armadorPagina;
    
    var $cripto;
    
    
    const PARAMETRO='parametro';
    
    function __construct() {
        
        $this->miConfigurador = Configurador::singleton ();
        
        $this->generadorClausulas = BuilderSql::singleton ();
        
        $this->armadorPagina = new ArmadorPagina ();
        
        $this->procesadorPagina = new ProcesadorPagina ();
        
        $this->cripto = Encriptador::singleton ();
    
    /**
     * El recurso de conexión que utilizan los objetos de esta clase es "configuracion"
     * y corresponde a la base de datos registrada en el archivo config.inc.php
     */
    }
    
    function inicializarPagina($laPagina) {
        
        $this->recursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( "configuracion" );
        
        if ($this->recursoDB) {            

            
            $this->especificar_pagina ( $laPagina );
            
            /**
             * En este punto pueden ocurrir tres cosas:
             * a) No existe una variable action por tanto se muestra la página
             * b) Existe una variable action por tanto se ejecuta solo el bloque que procesa la petición
             * c) Existe una variable actionBloque por tanto se carga la página y se incluye el resultado del 
             *    procesamiento del bloque. IMPORTANTE: Se espera que el bloque no realice ningún redirect, incluir
             *    esa funcionalidad podría acarrear comportamientos no especificados.
             */
            
            if(isset ( $_REQUEST ['actionBloque'] )){
                //(c)
                $resultado=$this->mostrarPagina();
            }elseif(isset ( $_REQUEST ['action'] )){
                //(b)
                $resultado=$this->procesarPagina();
            }else{
                //(a)
                $resultado= $this->mostrarPagina ();
            }            
            
            return $resultado;
            
                
            
        }
        
        return false;
    
    }
    
    function especificar_pagina($nombre) {
        
        if(isset($_REQUEST['pagina']) && $_REQUEST['pagina']!=''){
            $this->pagina = $_REQUEST['pagina'];
        }else{
            $this->pagina = $nombre;
        }
    
    }
    
    function mostrarPagina() {
        // 1. Buscar los bloques que constituyen la página
        $totalRegistros = 0;
        
        $cadenaSql = $this->generadorClausulas->cadenaSql ( "bloquesPagina", $this->pagina );
        
        $registro = $this->recursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
        
        if ($registro) {
            
            $totalRegistros = $this->recursoDB->getConteo ();
            
            if (isset ( $registro [0] [self::PARAMETRO] ) && trim ( $registro [0] [self::PARAMETRO] ) != "") {
                $parametros = explode ( "&", trim ( $registro [0] [self::PARAMETRO] ) );
            } else {
                $parametros = array ();
            }
            
            foreach ( $parametros as $valor ) {
                $elParametro = explode ( "=", $valor );
                $_REQUEST [$elParametro [0]] = $elParametro [1];
            }
            
            $this->armadorPagina->armarHTML ( $registro );
            return true;
        } else {
            $this->tipoError = "paginaSinBloques";
            return false;
        }
    
    }
    
    function procesarPagina(){
    
        $this->procesadorPagina->procesarPagina();
    
        return true;
    }
    
    
    function getError() {
        
        return $this->tipoError;
    
    }

}

?>
