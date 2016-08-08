<header class="main-header">
 	<a class="menu-toggle" href="#"><span>Menu</span></a>   	
</header>
<nav id="menu-nav-wrap">
<h3>Menu</h3>
<ul class="nav-list">
<?php 
$menu=array(
		'Ingresar Nuevo'=>'#nuevo',
		'inicio'=>'#inicio',
		'Datos Básicos'=>'#infobasica',
		'Board'=>'#infoboard',
		'CPU'=>'#infocpu',
		'Memoria'=>'#infocpu',
		'Almacenamiento'=>'#infoalmacenamiento',
		'Batería'=>'#infobateria',
		'Video'=>'#infovideo',
		'Audio'=>'#infoaudio',
		'Red'=>'#infored',
		'Inalámbrica'=>'#infoinalambrica',
		'Cámara'=>'#infocamara',
		'Bluetooth'=>'#infobluetooh',
		'Observaciones'=>'#observacion',
		'Editar'=>'#editar'		
);

	foreach ($menu as $etiqueta=>$enlace){
		echo '<li><a class="smoothscroll" href="'. $enlace.'" title="">'.$etiqueta.'</a></li>'."\n";
	}


?>
</ul>
</nav>