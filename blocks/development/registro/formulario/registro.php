<?php

namespace development\registro\formulario;

/**
 * IMPORTANTE: Este formulario está utilizando jquery.
 * Por tanto en el archivo ready.php se declaran algunas funciones js
 * que lo complementan.
 */

class Registrador {
    
   var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    
    function __construct($lenguaje, $formulario) {
        
        $this->miConfigurador = \Configurador::singleton ();
        
        $this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
        
        $this->lenguaje = $lenguaje;
        
        $this->miFormulario = $formulario;
    
    }
    
    function seleccionarForm() {
        // Rescatar los datos de este bloque
        $esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
        
        // ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
        
        // -------------------------------------------------------------------------------------------------
        
        // ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
        $esteCampo = $esteBloque ['nombre'];
        $atributos ['id'] = $esteCampo;
        $atributos ['nombre'] = $esteCampo;
        // Si no se coloca, entonces toma el valor predeterminado 'application/x-www-form-urlencoded'
        $atributos ['tipoFormulario'] = '';
        // Si no se coloca, entonces toma el valor predeterminado 'POST'
        $atributos ['metodo'] = 'POST';
        // Si no se coloca, entonces toma el valor predeterminado 'index.php' (Recomendado)
        $atributos ['action'] = 'index.php';
        $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo );
        // Si no se coloca, entonces toma el valor predeterminado.
        $atributos ['estilo'] = '';
        $atributos ['marco'] = true;
        $tab = 1;
        // ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------
        
        // ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
        $atributos ['tipoEtiqueta'] = 'inicio';
        echo $this->miFormulario->formulario ( $atributos );
        
        // ---------------- SECCION: Controles del Formulario -----------------------------------------------
        
        // ---------------- CONTROL: Cuadro Lista --------------------------------------------------------
        
        $esteCampo = 'seleccionar';
        $atributos ['columnas'] = 1;
        $atributos ['nombre'] = $esteCampo;
        $atributos ['id'] = $esteCampo;
        $atributos ['seleccion'] = - 1;
        $atributos ['evento'] = '';
        $atributos ['deshabilitado'] = false;
        $atributos ['tab'] = $tab;
        $atributos ['tamanno'] = 1;
        $atributos ['estilo'] = 'jqueryui';
        $atributos ['validar'] = '';
        $atributos ['limitar'] = true;
        $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
        $atributos ['anchoEtiqueta'] = 150;
        // Valores a mostrar en el control
        $matrizItems = array (
                array (
                        1,
                        'Registrar Página' 
                ),
                array (
                        2,
                        'Registrar Bloque' 
                ),
                array (
                        3,
                        'Diseñar Página' 
                ) 
        );
        
        $atributos ['matrizItems'] = $matrizItems;
        
        // Utilizar lo siguiente cuando no se pase un arreglo:
        // $atributos['baseDatos']='ponerAquiElNombreDeLaConexión';
        // $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar';
        $tab ++;
        echo $this->miFormulario->campoCuadroLista ( $atributos );
        unset ( $atributos );
        
        // --------------- FIN CONTROL : Cuadro Lista --------------------------------------------------
        
        // ---------------- SECCION: División ----------------------------------------------------------
        $esteCampo = 'division1';
        $atributos ['id'] = $esteCampo;
        $atributos ['estilo'] = 'general';
        echo $this->miFormulario->division ( "inicio", $atributos );
        
        // ---------------- FIN SECCION: División ----------------------------------------------------------
        echo $this->miFormulario->division ( 'fin' );
        
        // ---------------- FIN SECCION: Controles del Formulario -------------------------------------------
        
        // ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
        // Se debe declarar el mismo atributo de marco con que se inició el formulario.
        $atributos ['marco'] = true;
        $atributos ['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->formulario ( $atributos );
    
    }
    
    function mensaje() {
        
    // Si existe algun tipo de error en el login aparece el siguiente mensaje
        $mensaje = $this->miConfigurador->getVariableConfiguracion ( 'mostrarMensaje' );
        $this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', null );
        
        if ($mensaje) {
            $tipoMensaje = $this->miConfigurador->getVariableConfiguracion ( 'tipoMensaje' );
            if ($tipoMensaje == 'json') {
                
                $atributos ['mensaje'] = $mensaje;
                $atributos ['json'] = true;
            } else {
                $atributos ['mensaje'] = $this->lenguaje->getCadena ( $mensaje );
            }
            // ------------------Division para los botones-------------------------
            $atributos ['id'] = 'divMensaje';
            $atributos ['estilo'] = 'marcoBotones';
            echo $this->miFormulario->division ( "inicio", $atributos );
            
            // -------------Control texto-----------------------
            $esteCampo = 'mostrarMensaje';
            $atributos ["tamanno"] = '';
            $atributos ["estilo"] = 'information';
            $atributos ["etiqueta"] = '';
            $atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
            echo $this->miFormulario->campoMensaje ( $atributos );
            unset ( $atributos );
            
            // ------------------Fin Division para los botones-------------------------
            echo $this->miFormulario->division ( "fin" );
        }
    
    }

}

$miSeleccionador = new Registrador ( $this->lenguaje, $this->miFormulario );

$miSeleccionador->mensaje();

$miSeleccionador->seleccionarForm ();


?>