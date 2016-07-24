<?php


// ------------------Division para los botones-------------------------
$atributos ["id"] = "botones";
$atributos ["estilo"] = "marcoBotones";
echo $this->miFormulario->division ( "inicio", $atributos );

// -----------------CONTROL: Botón ----------------------------------------------------------------
$esteCampo = 'botonAceptar';
$atributos ["id"] = $esteCampo;
$atributos ["tabIndex"] = $this->tab;
$atributos ["tipo"] = 'boton';
// submit: no se coloca si se desea un tipo button genérico
$atributos ['submit'] = true;
$atributos ["estiloMarco"] = '';
$atributos ["estiloBoton"] = 'jqueryui';
// verificar: true para verificar el formulario antes de pasarlo al servidor.
$atributos ["verificar"] = '';
$atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
$atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
$atributos ['nombreFormulario'] = $this->esteBloque ['nombre'];
$this->tab ++;

// Aplica atributos globales al control
$atributos = array_merge ( $atributos, $this->atributosGlobales );
echo $this->miFormulario->campoBoton ( $atributos );
// -----------------FIN CONTROL: Botón -----------------------------------------------------------

// -----------------CONTROL: Botón ----------------------------------------------------------------
$esteCampo = 'botonCancelar';
$atributos ["id"] = $esteCampo;
$atributos ["tabIndex"] = $this->tab;
$atributos ["tipo"] = 'boton';
// submit: no se coloca si se desea un tipo button genérico
$atributos ['submit'] = true;
$atributos ["estiloMarco"] = '';
$atributos ["estiloBoton"] = 'jqueryui';
// verificar: true para verificar el formulario antes de pasarlo al servidor.
$atributos ["verificar"] = '';
$atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
$atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
$atributos ['nombreFormulario'] = $this->esteBloque ['nombre'];
$this->tab ++;

// Aplica atributos globales al control
$atributos = array_merge ( $atributos, $this->atributosGlobales );
echo $this->miFormulario->campoBoton ( $atributos );
// -----------------FIN CONTROL: Botón -----------------------------------------------------------

// ------------------Fin Division para los botones-------------------------
echo $this->miFormulario->division ( "fin" );

?>