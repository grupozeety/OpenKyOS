<?php
$plantilla = array (
		"Equipo" => array (
				"Tipo de Equipo"=>"tipo_equipo",
				"Fecha de Compra"=>'fecha_compra',
				"Fecha de Instalación"=>'fecha_instalacion',
				"Código Inventario"=>'id_interno_bodega',
				"Marca"=>'marca',
				"Modelo"=>'modelo',
				"Serial"=>'serial',
				"Número SKU"=>'sku',
				"Fabricante"=>'fabricante',
				"Dimensiones" =>'dimensiones',
				"Peso"=>'peso',
				"Ubicación"=>'ubicacion',
				"Responsable"=>'responsable',
				"Teléfono de Contacto"=>'telefonoContacto' 
		),
		"Tarjeta Madre" => array (
				"Board Tipo"=>'board_nombre',
				"Board Modelo" =>'board_version',
				"Board Fabricante" =>'board_fabricante',
				"Board Serial"  =>'board_serial',
		),
		"Procesador" => array (
				"Procesador Tipo" =>'cpu_tipo',
				"Procesador Modelo"=>'cpu_version',
				"Procesador Fabricante"=>'cpu_fabricante',
				"Cantidad de núcleos"=>'cpu_cantidad_nucleos',
				"Velocidad"=>'cpu_velocidad',
				"Arquitectura"=>'arquitectura',
				"Compatibilidad PAE" =>'cpu_flag_pae',
				"Compatibilidad SSE" =>'cpu_flag_sse',
				"Compatibilidad NX" =>'cpu_flag_nx' 
		),
		"Memoria" => array (
				"Capacidad"=>'memoria_capacidad',
				"Serial"=>'memoria_serial',
				"Factor de Forma"=>'memoria_factorforma',
				"Tipo"=>'memoria_tipo',
				"Velocidad"=>'memoria_velocidad',
				"Fabricante"=>'memoria_fabricante' 
		),
		"Almacenamiento Local" => array (
				"Capacidad"=>'disco_capacidad',
				"Cache"=>'disco_cache',
				"Velocidad"=>'disco_velocidad',
				"Protección contra impacto"=>'disco_proteccion',
				"Serial"=>'disco_serial',
				"Tipo"=>'disco_tipo',
				"Modelo"=>'disco_version',
				"Fabricante"=>'disco_fabricante' 
		),
		"Pantalla" => array (
				"Tipo"=>'pantalla_tipo',
				"Tamaño"=>'pantalla_tamanno',
				"Resolución" =>'pantalla_resolucion'
		),

		
		"Tarjeta de Video" => array (
				"Tipo"=>'video_tipo',
				"Modelo"=>'video_version',
				"Fabricante"=>'video_fabricante',
				"Memoria"=>'video_memoria' 
		),
		"Teclado" => array (
				"Tipo"=>'teclado_tipo',
				"Idioma"=>'teclado_idioma'
		),
		"Dispositivo Apuntador" => array (
				"Tipo"=>'mouse_tipo',
				"Fabricante"=>'video_fabricante' 
		),
		"Cámara" => array (
				"Tipo"=>'camara_tipo',
				"Modelo"=>'camara_version',
				"Formato"=>'camara_formato',
				"Funcionalidad"=>'camara_funcionalidad' 
		),
		
		"Audio" => array (
				"Tipo"=>'audio_tipo',
				"Modelo"=>'audio_version',
				"Fabricante"=>'audio_fabricante',
				"Conector"=>'audio_conector',
				"Tipo Parlantes"=>'parlantes_tipo',
				"Modelo Parlantes"=>'parlantes_version',
				"Micrófono"=>'microfono_tipo' 
		),
		"Conectividad a Red (Alámbrica)" => array (
				"Tipo"=>'red_tipo',
				"Modelo"=>'red_version',
				"Fabricante"=>'red_fabricante',
				"MAC"=>'red_serial',
				"Velocidad"=>'red_velocidad',
				"Estándar"=>'red_estandar',
				"Tipo de Conector"=>'red_tipo_conector',
				"Compatibilidad IPv4"=>'red_ipv4',
				"Compatibilidad IPv6"=>'red_ipv6' 
		),
		"Conectividad a Red (Inalámbrica)" => array (
				"Tipo"=>'wifi_tipo',
				"Modelo"=>'wifi_version',
				"Fabricante"=>'wifi_fabricante',
				"MAC"=>'wifi_serial',
				"Estándar"=>'wifi_estandar',
				"Codificación" =>'wifi_codificacion'
		),
		"Conectividad a Red (Bluetooth)" => array (
				"Tipo"=>'bluetooth_tipo',
				"Modelo"=>'bluetooth_version',
				"Fabricante"=>'bluetooth_fabricante',
				"Velocidad"=>'bluetooth_velocidad'
		),
		"Interfaces Externas" => array (
				"Puerto USB 2.0"=>'puerto_usb2_total',
				"Puerto USB 3.0"=>'puerto_usb3_total',
				"Tarjeta de Memoria"=>'slot_expansion_tipo',
				"Puerto HDMI"=>'puerto_hdmi_total',
				"Puerto VGA" =>'puerto_vga_total'
		),
		
		
		"Alimentación" => array (
				"Tipo"=>'alimentacion_tipo',
				"Fuente"=>'alimentacion_dispositivo',
				"Voltaje"=>'alimentacion_voltaje',
				"Frecuencia"=>'alimentacion_frecuencia'
		),
		"Batería" => array (
				"Tipo"=>'bateria_tipo',
				"Modelo"=>'bateria_version',
				"Serial"=>'bateria_serial',
				"Autonomía"=>'bateria_autonomia',
				"Certificación"=>'bateria_certificacion'
		),
		"Varios" => array (
				"Manuales" =>'manuales'
		),
		"Software" => array (
				"Sistema Operativo"=>'sistema_operativo',
				"Suite de Ofimática"=>'suite_ofimatica',
				"Antivirus"=>'antivirus' 
		),
		"Distribuidor" => array (
				"Nombre"=>'distribuidor_nombre',
				"Dirección"=>'distribuidor_direccion',
				"Teléfono" =>'distribuidor_telefono'
		) 
);