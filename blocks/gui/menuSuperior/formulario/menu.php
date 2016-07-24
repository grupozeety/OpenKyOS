<?php
$directorio = $this->miConfigurador->getVariableConfiguracion("host");
$directorio .= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
$directorio .= $this->miConfigurador->getVariableConfiguracion("enlace");

$_REQUEST ['tiempo'] = time();
$enlaceTerminos ['enlace'] = "pagina=consultarPerfil";
$enlaceTerminos ['enlace'].= "&opcion=terminos";
$enlaceTerminos ['urlCodificada'] = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($enlaceTerminos ['enlace'], $directorio);
$enlaceTerminos ['nombre'] = "Terminos y Condiciones";

$enlaceIndicadores ['enlace'] = "pagina=consultarPerfil";
$enlaceIndicadores ['enlace'].= "&opcion=indicador";
$enlaceIndicadores ['urlCodificada'] = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($enlaceIndicadores ['enlace'], $directorio);
$enlaceIndicadores ['nombre'] = "Indicadores del Servicio";

$enlaceregistroMantenimiento ['enlace'] = "pagina=consultarPerfil";
$enlaceregistroMantenimiento ['enlace'].= "&opcion=registroMantenimiento";
$enlaceregistroMantenimiento ['urlCodificada'] = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($enlaceregistroMantenimiento ['enlace'], $directorio);
$enlaceregistroMantenimiento ['nombre'] = "Registrar Solicitud Mantenimiento";

$enlaceconsultaMantenimiento ['enlace'] = "pagina=consultarPerfil";
$enlaceconsultaMantenimiento ['enlace'].= "&opcion=consultaMantenimiento";
$enlaceconsultaMantenimiento ['urlCodificada'] = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($enlaceconsultaMantenimiento ['enlace'], $directorio);
$enlaceconsultaMantenimiento ['nombre'] = "Consultar Historial de Solicitudes Mantenimiento";

$enlacePerfil ['enlace'] = "pagina=consultarPerfil";
$enlacePerfil ['enlace'].= "&campoSeguro=" . $_REQUEST ['tiempo'];
//$enlacePerfil ['enlace'] .= "&usuario=" . $id_usuario;
$enlacePerfil ['urlCodificada'] = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($enlacePerfil ['enlace'], $directorio);
$enlacePerfil ['nombre'] = "Mi Perfil";
?>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Conexiones Digitales II</title>
    <!-- core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animate.min.css" rel="stylesheet">
    <link href="css/owl.carousel.css" rel="stylesheet">
    <link href="css/owl.transitions.css" rel="stylesheet">
    <link href="css/prettyPhoto.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">
    <!--[if lt IE 9]>
        <script src="js/html5shiv.js"></script>
        <script src="js/respond.min.js"></script>
        <![endif]-->
    <link rel="shortcut icon" href="images/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144"
          href="images/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114"
          href="images/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72"
          href="images/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed"
          href="images/ico/apple-touch-icon-57-precomposed.png">
</head>		

<nav id="main-menu" class="navbar navbar-default navbar-fixed-top"
     role="banner">
    <div class="container">
        <div class="navbar-header">
            <!--button type="button" class="navbar-toggle" data-toggle="collapse"
                    data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span> <span
                    class="icon-bar"></span> <span class="icon-bar"></span> <span
                    class="icon-bar"></span>
            </button-->
            <a class="navbar-brand" href="index.html"><img
                    src="images/logo.png" alt="logo"></a>
        </div>

        <div class="collapse navbar-collapse navbar-right">
            <ul class="nav navbar-nav">
                <li class="scroll active"><a href="/PortalCommunity">Home</a></li>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Mis Facturas</a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Consultar Factura Actual</a></li>
                        <li><a href="#">Consultar Historial de Facturas</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Mi Servicio</a>
                    <ul class="dropdown-menu">
                    <li><a href="<?php echo $enlaceIndicadores['urlCodificada'] ?>"><?php echo ($enlaceIndicadores['nombre']) ?></a></li>
                        <li><a href="<?php echo $enlaceTerminos['urlCodificada'] ?>"><?php echo ($enlaceTerminos['nombre']) ?></a></li>
                        <li class="dropdown-submenu">
                            <a tabindex="-1" href="#">Solicitud de Mantenimiento</a>
                            <ul class="dropdown-menu">
                               <li><a href="<?php echo $enlaceregistroMantenimiento['urlCodificada'] ?>"><?php echo ($enlaceregistroMantenimiento['nombre']) ?></a></li>
                                <!--li class="dropdown-submenu">
                                    <a href="#">Even More..</a>
                                    <ul class="dropdown-menu">
                                        <li><a href="#">3rd level</a></li>
                                        <li><a href="#">3rd level</a></li>
                                    </ul>
                                </li-->
                               <li><a href="<?php echo $enlaceconsultaMantenimiento['urlCodificada'] ?>"><?php echo ($enlaceconsultaMantenimiento['nombre']) ?></a></li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li><a href="<?php echo $enlacePerfil['urlCodificada'] ?>"><?php echo ($enlacePerfil['nombre']) ?></a></li>
            </ul>
        </div>
    </div>
    <!--/.container-->
</nav>