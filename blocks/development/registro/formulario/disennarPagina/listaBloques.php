<?php

//1. Crear el arreglo que irá en $atributos['items']

$items=array();

foreach ($resultado as $indice=>$arreglo){
	
		$items[$indice]=$arreglo['nombre'];	
	
}

$atributos['items']=$items;
$atributos['id']='listaBloques';

//2. Crear la lista

echo $this->miFormulario->listaNoOrdenada ( $atributos );

