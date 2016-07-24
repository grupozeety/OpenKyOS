<?php
$divisiones = array (
		'seccionAG',
		'seccionBG',
		'seccionCG',
		'seccionDG',
		'seccionEG' 
);

// ------------------Inicio de la Division Columna 1 ----------------------------
$atributos ['id'] = 'columna1';
$atributos ['estilo'] = '';
echo $this->miFormulario->division ( 'inicio', $atributos );

// ------------------Inicio de la Division Navegador ----------------------------
$atributos ['id'] = 'navegador';
$atributos ['estilo'] = '';
echo $this->miFormulario->division ( 'inicio', $atributos );


$this->construirLista();


// -------------------Fin de la División Navegador -------------------------------
echo $this->miFormulario->division ( "fin" );

// -------------------Fin de la División Columna 1 -------------------------------
echo $this->miFormulario->division ( "fin" );

// ------------------Inicio de la Division Columna 1 ----------------------------
$atributos ['id'] = 'columna2';
$atributos ['estilo'] = '';
echo $this->miFormulario->division ( 'inicio', $atributos );

foreach ( $divisiones as $clave => $valor ) {
	// ------------------Inicio de la Division ----------------------------
	$atributos ['id'] = $valor;
	$atributos ['estilo'] = '';
	echo $this->miFormulario->division ( 'inicio', $atributos );
	
	// -------------------Fin de la División -------------------------------
	echo $this->miFormulario->division ( "fin" );
}

// -------------------Fin de la División Columna 2 -------------------------------
echo $this->miFormulario->division ( "fin" );
