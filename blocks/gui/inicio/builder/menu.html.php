<?php 
/*
 *  Sintaxis recomendada para las plantillas PHP
 */ 
?>
  
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<title>Conexiones Digitales II</title>

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
<!--/head-->

<body id="home" class="homepage">

	<header id="header">
		<nav id="main-menu" class="navbar navbar-default navbar-fixed-top"
			role="banner">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse"
						data-target=".navbar-collapse">
						<span class="sr-only">Toggle navigation</span> <span
							class="icon-bar"></span> <span class="icon-bar"></span> <span
							class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="index.php"><img
						src="images/logo.png" alt="logo"></a>
				</div>

				<div class="collapse navbar-collapse navbar-right">
					<ul class="nav navbar-nav">
						<li class="scroll active"><a href="#home">Inicio</a></li>
						<li class="scroll"><a href="#features">Beneficiarios</a></li>
						<li class="scroll"><a href="#cta">Velocímetro</a></li>
						<li class="scroll"><a href="#coverage">Cubrimiento</a></li>
						<li class="scroll"><a href="#services">Servicios</a></li>
						<!--<li class="scroll"><a href="#about">Proyecto</a></li>-->
						<li class="scroll"><a href="#meet-team">Equipo</a></li>
						<li class="scroll"><a href="#pricing">Precios</a></li>
						<li class="scroll"><a href="#blog">Noticias</a></li>
						<li class="scroll"><a href="#get-in-touch">Contacto</a></li>
						<li class="dropdown">
        					<a class="dropdown-toggle" data-toggle="dropdown" href="#">Mi Cuenta
        					<span class="caret"></span></a>
        					<ul class="dropdown-menu">
        						<?php if($this->atributos['login']==false): ?>
                        			<li class="scroll"><a href="index.php?data=BYkdYOXtri-IDXy89MeNSTjGsvKNtF-e0ECsjNMNXcE">Iniciar Sesión</a></li>
                        		<?php elseif ($this->atributos['login']==true):?>
                        			<li class="scroll"><a href="index.php?data=BYkdYOXtri-IDXy89MeNSTjGsvKNtF-e0ECsjNMNXcE">Mi Menú</a></li>
                        			<li class="scroll"><a href="index.php?data=SRuAbusjKCfT8KmAcSIspwNNay_JPuA5kJEZUp1J0Xo">Cerrar Sesión</a></li>
                        		<?php endif; ?>
        					</ul>
     					 </li>
					</ul>
				</div>
			</div>
			<!--/.container-->
		</nav>
		<!--/nav-->
	</header>
	<!--/header-->

	<section id="main-slider">
		<div class="owl-carousel">
			<div class="item"
				style="background-image: url(images/slider/bg1.jpg);">
				<div class="slider-inner">
					<div class="container">
						<div class="row">
							<div class="col-sm-6">
								<div class="carousel-content">
									<h2>
										<span>Conexiones Digitales II</span> Internet de Banda Ancha
										para Todos
									</h2>
									<p>Red para la masificación de accesos de banda ancha</p>
									<a class="btn btn-primary btn-lg" href="#">Leer Más</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--/.item-->
			<div class="item"
				style="background-image: url(images/slider/bg2.jpg);">
				<div class="slider-inner">
					<div class="container">
						<div class="row">
							<div class="col-sm-6">
								<div class="carousel-content">
									<h2>
										Ministerio TIC ofrece <span>INTERNET A BAJO COSTO</span> en
										los hogares
									</h2>
									<p>Con esta iniciativa los hogares de estrato 1 y 2 y de
										Viviendas de Interés Prioritario podrán acceder a un mundo de
										oportunidades a través de Internet</p>
									<a class="btn btn-primary btn-lg"
										href="http://www.mintic.gov.co/portal/604/w3-propertyvalue-556.html">Leer
										Más</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--/.item-->
		</div>
		<!--/.owl-carousel-->
	</section>
	<!--/#main-slider-->

	<section id="features">
		<div class="container">
			<div class="section-header">
				<h2 class="section-title text-center wow fadeInDown">Beneficiarios</h2>
				<p class="text-center wow fadeInDown">El proyecto Conexiones
					Digitales II fomenta la equidad social en el acceso al sevicio de
					Internet</p>
			</div>
			<div class="row">
				<div class="col-sm-6 wow fadeInLeft">
					<img class="img-responsive" src="images/main-feature.png" alt="">
				</div>
				<div class="col-sm-6">
					<div class="media service-box wow fadeInRight">
						<div class="pull-left">
							<i class="fa fa-line-chart"></i>
						</div>
						<div class="media-body">
							<h4 class="media-heading">Hogares Estrato 1 y 2</h4>
							<p>Predios que no hayan contado con el mencionado servicio a
								través de ningún proveedor por lo menos en los seis (6) meses
								anteriores a la instalación del mismo</p>
						</div>
					</div>

					<div class="media service-box wow fadeInRight">
						<div class="pull-left">
							<i class="fa fa-cubes"></i>
						</div>
						<div class="media-body">
							<h4 class="media-heading">Viviendas de Interés Prioritario</h4>
							<p>Para que las familias colombianas tengan una vivienda
								digna con servicios básicos incluyendo el acceso a internet con
								conexiones de banda ancha</p>
						</div>
					</div>

					<div class="media service-box wow fadeInRight">
						<div class="pull-left">
							<i class="fa fa-pie-chart"></i>
						</div>
						<div class="media-body">
							<h4 class="media-heading">Córdoba y Sucre</h4>
							<p>El proyecto es de ámbito nacional pero nuestra empresa es
								responsable de los accesos en los departamentos de Córdoba y
								Sucre</p>
						</div>
					</div>

					<div class="media service-box wow fadeInRight">
						<div class="pull-left">
							<i class="fa fa-pie-chart"></i>
						</div>
						<div class="media-body">
							<h4 class="media-heading">Tres años de servicio</h4>
							<p>Los usuarios podrán gozar por tres (3) años del servicio,
								si así lo deciden, y no estarán obligados a la compra de otros
								servicios o terminales, ni a cláusulas de permanencia para la
								obtención del beneficio.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section id="cta"">
		<div class="container">
			<div class="section-header">
				<h2 class="section-title text-center wow fadeInDown">Acceso de
					Banda Ancha de Alta Calidad</h2>
				<p class="text-center wow fadeInDown">Si eres un usuario de
					nuestra red, puedes verificar la calidad de nuestro servicio de
					Internet a través de un sencillo medidor de velocidad. En tiempo
					real medirá el estado de tu conexión!!!</p>
			</div>
			<div class="text-center">
				<img class="img-responsive center-block wow fadeIn"
					src="images/cta/gauge.png" alt="">
				<p class="wow fadeInUp" data-wow-duration="300ms"
					data-wow-delay="200ms">
					<a class="btn btn-primary btn-lg"
						href="http://speedtest.huilaconstruyendomundo.com/">Haz la
						prueba ahora!!</a>
				</p>
			</div>
		</div>
	</section>
	<!--/#cta-->

	<section id="coverage">
		<div class="container">
			<div class="section-header">
				<h2 class="section-title text-center wow fadeInDown">Departamentos
					de Córdoba y Sucre</h2>
				<p class="text-center wow fadeInDown">Más de 13.900
					beneficiarios distribuidos en 11 municipios</p>
			</div>
			<div class="row">
				<div class="col-sm-6 wow fadeInLeft">
					<div class="embed-responsive embed-responsive-4by3">
						<iframe
							src="https://www.google.com/maps/d/embed?mid=1DLqK7MVC81cuyzVkM5gcHYIBs_Y&z=9"
							width="560" height="480"></iframe>
					</div>
				</div>
				<div class="col-sm-6">
					<h3 class="column-title">Municipios</h3>
					<div role="tabpanel">
						<ul class="nav main-tab nav-justified" role="tablist">
							<li role="presentation" class="active"><a href="#tab1"
								role="tab" data-toggle="tab" aria-controls="tab1"
								aria-expanded="true">Córdoba</a></li>
							<li role="presentation"><a href="#tab2" role="tab"
								data-toggle="tab" aria-controls="tab2" aria-expanded="false">Sucre</a>
							</li>
						</ul>
						<div id="tab-content" class="tab-content">
							<div role="tabpanel" class="tab-pane fade active in" id="tab1"
								aria-labelledby="tab1">
								<ul>
									<li>Monteria</li>
									<li>Cereté</li>
									<li>Lorica</li>
									<li>Momil</li>
									<li>Planeta Rica</li>
									<li>Purísima</li>
								</ul>
							</div>
							<div role="tabpanel" class="tab-pane fade" id="tab2"
								aria-labelledby="tab2">
								<ul>
									<li>Sincelejo</li>
									<li>Corozal</li>
									<li>Galeras</li>
									<li>Sampués</li>
									<li>Since</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>


	<section id="cta2">
		<div class="container">
			<div class="text-center">
				<h2 class="wow fadeInUp" data-wow-duration="300ms"
					data-wow-delay="0ms">
					<span>CONEXIONES DIGITALES II</span> UN MUNDO DE OPORTUNIDADES
				</h2>
				<p class="wow fadeInUp" data-wow-duration="300ms"
					data-wow-delay="100ms">Con el proyecto el Ministerio TIC pone a
					disposición de la población un canal de acceso a las oportunidades
					que ofrece la Internet.</p>
				<p class="wow fadeInUp" data-wow-duration="300ms"
					data-wow-delay="200ms">
					<a class="btn btn-primary btn-lg"
						href="http://www.mintic.gov.co/portal/vivedigital/612/w3-propertyname-509.html#iniciativas_aplicaciones">Explora!!!</a>
				</p>
				<img class="img-responsive wow fadeIn"
					src="images/cta2/cta2-img.png" alt="" data-wow-duration="300ms"
					data-wow-delay="300ms">
			</div>
		</div>
	</section>

	<section id="services">
		<div class="container">

			<div class="section-header">
				<h2 class="section-title text-center wow fadeInDown">Nuestros
					Servicios</h2>
				<p class="text-center wow fadeInDown">El objetivo del proyecto
					Conexiones Digitales II es proveer acceso de banda ancha a precios
					bajos. Para garantizar la prestación del servicio con los más altos
					estándares de calidad se pone a disposición de nuestros usuarios:</p>
			</div>

			<div class="row">
				<div class="features">
					<div class="col-md-4 col-sm-6 wow fadeInUp"
						data-wow-duration="300ms" data-wow-delay="0ms">
						<div class="media service-box">
							<div class="pull-left">
								<i class="fa fa-line-chart"></i>
							</div>
							<div class="media-body">
								<h4 class="media-heading">Mesa de Ayuda</h4>
								<p>Con diferentes canales de comunicación para atender de
									una manera ágil todas sus peticiones, quejas o reclamaciones.</p>
							</div>
						</div>
					</div>
					<!--/.col-md-4-->

					<div class="col-md-4 col-sm-6 wow fadeInUp"
						data-wow-duration="300ms" data-wow-delay="100ms">
						<div class="media service-box">
							<div class="pull-left">
								<i class="fa fa-cubes"></i>
							</div>
							<div class="media-body">
								<h4 class="media-heading">Centro de Gestión</h4>
								<p>Centro de operaciones especializado en el seguimiento,
									diagnóstico, mantenimiento, operación y crecimiento de nuestra
									red.</p>
							</div>
						</div>
					</div>
					<!--/.col-md-4-->

					<div class="col-md-4 col-sm-6 wow fadeInUp"
						data-wow-duration="300ms" data-wow-delay="200ms">
						<div class="media service-box">
							<div class="pull-left">
								<i class="fa fa-pie-chart"></i>
							</div>
							<div class="media-body">
								<h4 class="media-heading">Portal de Usuario</h4>
								<p>Al alcance de un solo click podrás obtener toda la
									información de tu servicio: estado de tu solicitud,
									facturación, historial de servicios.</p>
							</div>
						</div>
					</div>
					<!--/.col-md-4-->


				</div>
			</div>
			<!--/.row-->
		</div>
		<!--/.container-->
	</section>
	<!--/#services-->

	<section id="about">
		<div class="container">
			<div class="section-header">
				<h2 class="section-title text-center wow fadeInDown">Conexiones
					Digitales II</h2>
				<p class="text-center wow fadeInDown">El Gobierno Nacional ha
					dispuesto dentro de su política de telecomunicaciones sociales, una
					serie de directrices para el desarrollo de la Sociedad de la
					Información, entre las que se destaca la promoción de estrategias
					de acceso y servicio universal a las mismas.</p>
			</div>

			<div class="row">
				<div class="col-sm-6 wow fadeInLeft">
					<h3 class="column-title">Yo amo Internet!!</h3>
					<!-- 16:9 aspect ratio -->
					<div class="embed-responsive embed-responsive-16by9">
						<iframe width="560" height="315"
							src="https://www.youtube.com/embed/DYo3kR8jZmc" frameborder="0"
							allowfullscreen></iframe>
					</div>
				</div>

				<div class="col-sm-6 wow fadeInRight">
					<h3 class="column-title">Todos por un nuevo país</h3>
					<p>El Ministerio de Tecnologías de la Información y las
						Comunicaciones, en el marco de la política, lineamientos y ejes de
						acción a desarrollarse dentro del “Plan Vive Digital”, busca que
						se generen las condiciones adecuadas para que el sector de las
						telecomunicaciones aumente su cobertura a través del despliegue de
						infraestructura, aumente la penetración de Banda Ancha, se
						intensifique el uso y la apropiación de las TIC, así como la
						generación de contenidos y aplicaciones convergiendo dentro de un
						ecosistema digital.</p>
				</div>

				<a class="btn btn-primary"
					href="http://www.mintic.gov.co/portal/604/w3-propertyvalue-556.html">Conocer
					Más</a>

			</div>
		</div>
	</section>
	<!--/#about-->

	<section id="work-process">
		<div class="container">
			<div class="section-header">
				<h2 class="section-title text-center wow fadeInDown">Proceso de
					Implementación</h2>
				<p class="text-center wow fadeInDown">
					<br>Para garantizar la calidad y tranparencia del proyecto, el
					Ministerio TIC a través de la Dirección de Conectividad y con la
					interventoría de ConCol Ingeniería S.A.S; hacen seguimiento
					estricto a los compromisos contractuales.
				</p>
			</div>

			<div class="row text-center">
				<div class="col-md-2 col-md-4 col-xs-6">
					<div class="wow fadeInUp" data-wow-duration="400ms"
						data-wow-delay="0ms">
						<div class="icon-circle">
							<span>1</span> <i class="fa fa-coffee fa-2x"></i>
						</div>
						<h3>Planificación</h3>
					</div>
				</div>
				<div class="col-md-2 col-md-4 col-xs-6">
					<div class="wow fadeInUp" data-wow-duration="400ms"
						data-wow-delay="100ms">
						<div class="icon-circle">
							<span>2</span> <i class="fa fa-bullhorn fa-2x"></i>
						</div>
						<h3>Instalación</h3>
					</div>
				</div>
				<div class="col-md-2 col-md-4 col-xs-6">
					<div class="wow fadeInUp" data-wow-duration="400ms"
						data-wow-delay="200ms">
						<div class="icon-circle">
							<span>3</span> <i class="fa fa-image fa-2x"></i>
						</div>
						<h3>Operación</h3>
					</div>
				</div>
				<div class="col-md-2 col-md-4 col-xs-6">
					<div class="wow fadeInUp" data-wow-duration="400ms"
						data-wow-delay="300ms">
						<div class="icon-circle">
							<span>4</span> <i class="fa fa-heart fa-2x"></i>
						</div>
						<h3>Mantenimiento</h3>
					</div>
				</div>
				<div class="col-md-2 col-md-4 col-xs-6">
					<div class="wow fadeInUp" data-wow-duration="400ms"
						data-wow-delay="400ms">
						<div class="icon-circle">
							<span>5</span> <i class="fa fa-shopping-cart fa-2x"></i>
						</div>
						<h3>Seguimiento</h3>
					</div>
				</div>
				<div class="col-md-2 col-md-4 col-xs-6">
					<div class="wow fadeInUp" data-wow-duration="400ms"
						data-wow-delay="500ms">
						<div class="icon-circle">
							<span>6</span> <i class="fa fa-space-shuttle fa-2x"></i>
						</div>
						<h3>Apropiación Social</h3>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--/#work-process-->

	<section id="meet-team">
		<div class="container">
			<div class="section-header">
				<h2 class="section-title text-center wow fadeInDown">Equipo</h2>
				<p class="text-center wow fadeInDown">El proyecto se ejecuta a
					partir de la alianza estratégica contractual entre as siguientes
					entidades:</p>
			</div>

			<div class="row">
				<div class="col-sm-6 col-md-3">
					<div class="team-member wow fadeInUp" data-wow-duration="400ms"
						data-wow-delay="0ms">
						<div class="team-img">
							<img class="img-responsive" src="images/team/01.jpg" alt="">
						</div>
						<div class="team-info">
							<h3>Ministerio TIC</h3>
							<span>Financiamiento</span>
						</div>
						<p>Ministerio de Tecnologías de la Información y las
							Comunicaciones.</p>
					</div>
				</div>
				<div class="col-sm-6 col-md-3">
					<div class="team-member wow fadeInUp" data-wow-duration="400ms"
						data-wow-delay="100ms">
						<div class="team-img">
							<img class="img-responsive" src="images/team/02.jpg" alt="">
						</div>
						<div class="team-info">
							<h3>Vive Digital 2</h3>
							<span>Proyecto Marco</span>
						</div>
						<p>Conexiones Digitales II se realiza en el marco del proyecto
							Vive Digital 2.</p>
					</div>
				</div>
				<div class="col-sm-6 col-md-3">
					<div class="team-member wow fadeInUp" data-wow-duration="400ms"
						data-wow-delay="200ms">
						<div class="team-img">
							<img class="img-responsive" src="images/team/03.jpg" alt="">
						</div>
						<div class="team-info">
							<h3>ConCol</h3>
							<span>Interventoría</span>
						</div>
						<p>Empresa de ingeniería encargada de realizar el seguimiento
							a la ejecución del contrato</p>
					</div>
				</div>
				<div class="col-sm-6 col-md-3">
					<div class="team-member wow fadeInUp" data-wow-duration="400ms"
						data-wow-delay="300ms">
						<div class="team-img">
							<img class="img-responsive" src="images/team/04.jpg" alt="">
						</div>
						<div class="team-info">
							<h3>Politécnica</h3>
							<span>Ejecutor del Contrato</span>
						</div>
						<p>Ofrece los servicios de instalación, administración y
							operación de redes, conectividad a Internet e intranet y soporte
							técnico.</p>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--/#meet-team-->

	<section id="#statistics">
		<div class="container">
			<div class="row">
				<div class="col-sm-4">
					<h3 class="column-title">Estadísticas</h3>
					<strong>Informe Detallado de Ingeniería</strong>
					<div class="progress">
						<div class="progress-bar progress-bar-primary" role="progressbar"
							data-width="100">100%</div>
					</div>
					<strong>Servicios Web en Desarrollo</strong>
					<div class="progress">
						<div class="progress-bar progress-bar-primary" role="progressbar"
							data-width="50">50%</div>
					</div>
					<strong>Accesos en fase de Diseño</strong>
					<div class="progress">
						<div class="progress-bar progress-bar-primary" role="progressbar"
							data-width="80">80%</div>
					</div>
					<strong>Usuarios</strong>
					<div class="progress">
						<div class="progress-bar progress-bar-primary" role="progressbar"
							data-width="0">0%</div>
					</div>
				</div>
				<div class="col-sm-4">
					<h3 class="column-title">Departamentos</h3>
					<div role="tabpanel">
						<ul class="nav main-tab nav-justified" role="tablist">
							<li role="presentation" class="active"><a href="#tab1"
								role="tab" data-toggle="tab" aria-controls="tab1"
								aria-expanded="true">Córdoba</a></li>
							<li role="presentation"><a href="#tab2" role="tab"
								data-toggle="tab" aria-controls="tab2" aria-expanded="false">Sucre</a>
							</li>
						</ul>
						<div id="tab-content" class="tab-content">
							<div role="tabpanel" class="tab-pane fade active in" id="tab1"
								aria-labelledby="tab1">
								<ul>
									<li>Monteria</li>
									<li>Cereté</li>
									<li>Lorica</li>
									<li>Momil</li>
									<li>Planeta Rica</li>
									<li>Purísima</li>
								</ul>
							</div>
							<div role="tabpanel" class="tab-pane fade" id="tab2"
								aria-labelledby="tab2">
								<ul>
									<li>Sincelejo</li>
									<li>Corozal</li>
									<li>Galeras</li>
									<li>Sampués</li>
									<li>Since</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<h3 class="column-title">Tecnología</h3>
					<div class="panel-group" id="accordion" role="tablist"
						aria-multiselectable="true">
						<div class="panel panel-default">
							<div class="panel-heading" role="tab" id="headingOne">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion"
										href="#collapseOne" aria-expanded="true"
										aria-controls="collapseOne"> Híbrida Fibra Coaxial (HFC) </a>
								</h4>
							</div>
							<div id="collapseOne" class="panel-collapse collapse in"
								role="tabpanel" aria-labelledby="headingOne">
								<div class="panel-body">Red de fibra óptica que incorpora
									tanto fibra óptica como cable coaxial para crear una red de
									banda ancha.</div>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="panel-heading" role="tab" id="headingTwo">
								<h4 class="panel-title">
									<a class="collapsed" data-toggle="collapse"
										data-parent="#accordion" href="#collapseTwo"
										aria-expanded="false" aria-controls="collapseTwo"> Red
										Inalámbrica </a>
								</h4>
							</div>
							<div id="collapseTwo" class="panel-collapse collapse"
								role="tabpanel" aria-labelledby="headingTwo">
								<div class="panel-body">Transmisión de señales utilizando
									ondas electromagnéticas.</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>

	</section>
	<!--/#statistics-->

	<section id="animated-number">
		<div class="container">
			<div class="section-header">
				<h2 class="section-title text-center wow fadeInDown">Datos de
					Interés</h2>
				<p class="text-center wow fadeInDown">Datos de interés que se
					van generando en el transcurso del proyecto. Conócelos!!!</p>
			</div>

			<div class="row text-center">
				<div class="col-sm-3 col-xs-6">
					<div class="wow fadeInUp" data-wow-duration="400ms"
						data-wow-delay="0ms">
						<div class="animated-number" data-digit="10675"
							data-duration="1000"></div>
						<strong>FAMILIAS ENTREVISTADAS</strong>
					</div>
				</div>
				<div class="col-sm-3 col-xs-6">
					<div class="wow fadeInUp" data-wow-duration="400ms"
						data-wow-delay="100ms">
						<div class="animated-number" data-digit="16" data-duration="1000"></div>
						<strong>PROYECTOS VIP</strong>
					</div>
				</div>
				<div class="col-sm-3 col-xs-6">
					<div class="wow fadeInUp" data-wow-duration="400ms"
						data-wow-delay="200ms">
						<div class="animated-number" data-digit="11" data-duration="1000"></div>
						<strong>MUNICIPIOS</strong>
					</div>
				</div>
				<div class="col-sm-3 col-xs-6">
					<div class="wow fadeInUp" data-wow-duration="400ms"
						data-wow-delay="300ms">
						<div class="animated-number" data-digit="0" data-duration="1000"></div>
						<strong>ACCESOS INSTALADOS</strong>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--/#animated-number-->

	<section id="pricing">
		<div class="container">
			<div class="section-header">
				<h2 class="section-title text-center wow fadeInDown">Tarifas</h2>
				<p class="text-center wow fadeInDown">Como mecanismo de
					equilibrio social, el proyecto Conexiones Digitales II ofrece
					tarifas diferenciales para sus usuarios</p>
			</div>

			<div class="row">
				<div class="col-sm-6 col-md-4">
					<div class="wow zoomIn" data-wow-duration="400ms"
						data-wow-delay="0ms">
						<ul class="pricing featured">
							<li class="plan-header">
								<div class="price-duration">
									<span class="price"> $6.500 </span> <span class="duration">
										Mensual </span>
								</div>

								<div class="plan-name">Viviendas de Interés Prioritario</div>
							</li>
							<li><strong>4 Mb</strong> de Acceso</li>
							<li><strong>1</strong> blog personalizado</li>
							<li><strong>1</strong> dirección de correo electrónico</li>
							<li><strong>24/7</strong> de soporte</li>
						</ul>
					</div>
				</div>
				<div class="col-sm-6 col-md-4">
					<div class="wow zoomIn" data-wow-duration="400ms"
						data-wow-delay="200ms">
						<ul class="pricing featured">
							<li class="plan-header">
								<div class="price-duration">
									<span class="price"> $12.600 </span> <span class="duration">
										Mensual </span>
								</div>

								<div class="plan-name">Estrato 1</div>
							</li>
							<li><strong>4 Mb</strong> de Acceso</li>
							<li><strong>1</strong> blog personalizado</li>
							<li><strong>1</strong> dirección de correo electrónico</li>
							<li><strong>24/7</strong> de soporte</li>
						</ul>
					</div>
				</div>
				<div class="col-sm-6 col-md-4">
					<div class="wow zoomIn" data-wow-duration="400ms"
						data-wow-delay="400ms">
						<ul class="pricing featured">
							<li class="plan-header">
								<div class="price-duration">
									<span class="price"> $17.600 </span> <span class="duration">
										Mensual </span>
								</div>
								<div class="plan-name">Estrato 2</div>
							</li>
							<li><strong>4 Mb</strong> de Acceso</li>
							<li><strong>1</strong> blog personalizado</li>
							<li><strong>1</strong> dirección de correo electrónico</li>
							<li><strong>24/7</strong> de soporte</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--/#pricing-->
	<section id="testimonial">
		<div class="container">
			<div class="row">
				<div class="col-sm-8 col-sm-offset-2">

					<div id="carousel-testimonial" class="carousel slide text-center"
						data-ride="carousel">
						<!-- Wrapper for slides -->
						<div class="carousel-inner" role="listbox">
							<div class="item active">
								<p>
									<img class="img-circle img-thumbnail"
										src="images/testimonial/01.jpg" alt="">
								</p>
								<h4>Carlos Madera</h4>
								<small>Director Ejecutivo Politécnica</small>
								<p>Con conexiones Digitales II, el Ministerio de TIC nos
									ofrece una oprtunidad única para cumplir con el objetivo social
									de nuestra organización</p>
							</div>
							<div class="item">
								<p>
									<img class="img-circle img-thumbnail"
										src="images/testimonial/01.jpg" alt="">
								</p>
								<h4>Diana Leonor Tinjacá</h4>
								<small>Líder Equipo de Sistemas de Información</small>
								<p>Con la apropiación de la filosofía del software libre
									estamos fomentando nichos de negocio sostenibles.</p>
							</div>
						</div>

						<!-- Controls -->
						<div class="btns">
							<a class="btn btn-primary btn-sm" href="#carousel-testimonial"
								role="button" data-slide="prev"> <span
								class="fa fa-angle-left" aria-hidden="true"></span> <span
								class="sr-only">Anterior</span>
							</a> <a class="btn btn-primary btn-sm" href="#carousel-testimonial"
								role="button" data-slide="next"> <span
								class="fa fa-angle-right" aria-hidden="true"></span> <span
								class="sr-only">Siguiente</span>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--/#testimonial-->

	<section id="blog">
		<div class="container">
			<div class="section-header">
				<h2 class="section-title text-center wow fadeInDown">Últimas
					Noticias</h2>
				<p class="text-center wow fadeInDown">
					Para nosotros es de vital importancia construir canales de
					comunicación que contribuyan al desarrollo social.<br> Conozca
					aquí como va nuestro proyecto!!!
				</p>
			</div>

			<div class="row">
				<div class="col-sm-6">
					<div class="blog-post blog-large wow fadeInLeft"
						data-wow-duration="300ms" data-wow-delay="0ms">
						<article>
							<header class="entry-header">
								<div class="entry-thumbnail">
									<img class="img-responsive" src="images/blog/01.jpg" alt="">
									<span class="post-format post-format-video"><i
										class="fa fa-image"></i></span>
								</div>
								<div class="entry-date">Mayo de 2016</div>
								<h2 class="entry-title">
									<a href="#">Aprobado el Informe Detallado de Ingeniería </a>
								</h2>
							</header>

							<div class="entry-content">
								<P>Luego de una minuciosa revisión por parte de la firma
									interventora, nos complace informar que el Informe Detallado de
									Ingeniería y Operación del proyecto ha sido aprobado en su
									totalidad</P>
							</div>
						</article>
					</div>
				</div>
				<!--/.col-sm-6-->
				<div class="col-sm-6">
					<div class="blog-post blog-media wow fadeInRight"
						data-wow-duration="300ms" data-wow-delay="100ms">
						<article class="media clearfix">
							<div class="entry-thumbnail pull-left">
								<img class="img-responsive" src="images/blog/02.jpg" alt="">
								<span class="post-format post-format-gallery"><i
									class="fa fa-image"></i></span>
							</div>
							<div class="media-body">
								<header class="entry-header">
									<div class="entry-date">Febrero de 2016</div>
									<h2 class="entry-title">
										<a href="#">Aplicación de Encuestas</a>
									</h2>
								</header>

								<div class="entry-content">
									<P>Con éxito se está llevando a cabo el estudio de mercado
										y demanda. Agradecemos a todos los que han participado
										contestando la encuesta!!</P>
								</div>
							</div>
						</article>
					</div>
					<div class="blog-post blog-media wow fadeInRight"
						data-wow-duration="300ms" data-wow-delay="200ms">
						<article class="media clearfix">
							<div class="entry-thumbnail pull-left">
								<img class="img-responsive" src="images/blog/03.jpg" alt="">
								<span class="post-format post-format-gallery"><i
									class="fa fa-image"></i></span>
							</div>
							<div class="media-body">
								<header class="entry-header">
									<div class="entry-date">01 Marzo de 2016</div>
									<h2 class="entry-title">
										<a href="#">Ecosistema de Software Libre</a>
									</h2>
								</header>

								<div class="entry-content">
									<P>Con la aprobación del Plan de Sistemas de Información y
										Servicios Web, el Ministerio TIC apoya el desarrollo de la
										industria del software libre en el país. Casi la totalidad de
										las herramientas utilizadas en el proyecto estarán a
										disposición de la comunidad.</P>
								</div>
							</div>
						</article>
					</div>
				</div>
			</div>

		</div>
	</section>

	<section id="get-in-touch">
		<div class="container">
			<div class="section-header">
				<h2 class="section-title text-center wow fadeInDown">Comunícate
					con nosotros</h2>
				<p class="text-center wow fadeInDown">Si quieres conocer más del
					proyecto y estás en los municipios beneficiarios, no dudes en
					comunicarte con nosotros.</p>
			</div>
		</div>
	</section>
	<!--/#get-in-touch-->
	<section id="contact">
		<div id="google-map" style="height: 650px" data-latitude="9.298230"
			data-longitude="-75.392851"></div>
		<div class="container-wrapper">
			<div class="container">
				<div class="row">
					<div class="col-sm-4 col-sm-offset-8">
						<div class="contact-form">
							<h3>Información de Contacto</h3>

							<address>
								<strong>Corporación Politécnica</strong><br> Carrera 20 #
								27-87<br> Edificio Cámara de Comercio- Piso 3. Oficina 302<br>
								Sincelejo, Sucre<br>
							</address>

							<form id="main-contact-form" name="contact-form" method="post"
								action="#">
								<div class="form-group">
									<input type="text" name="name" class="form-control"
										placeholder="Name" required>
								</div>
								<div class="form-group">
									<input type="email" name="email" class="form-control"
										placeholder="Email" required>
								</div>
								<div class="form-group">
									<input type="text" name="subject" class="form-control"
										placeholder="Subject" required>
								</div>
								<div class="form-group">
									<textarea name="message" class="form-control" rows="8"
										placeholder="Message" required></textarea>
								</div>
								<button type="submit" class="btn btn-primary">Enviar
									Mensaje</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--/#bottom-->

	<footer id="footer">
		<div class="container">
			<div class="row">
				<div class="col-sm-6">
					&copy; 2016 Corporación Politécnica Nacional de Colombia. Basado en
					el diseño de <a target="_blank" href="http://shapebootstrap.net/"
						title="Free Twitter Bootstrap WordPress Themes and HTML templates">ShapeBootstrap</a>
				</div>
				<div class="col-sm-6">
					<ul class="social-icons">
						<li><a href="#"><i class="fa fa-facebook"></i></a></li>
						<li><a href="#"><i class="fa fa-twitter"></i></a></li>
						<li><a href="#"><i class="fa fa-google-plus"></i></a></li>
						<li><a href="#"><i class="fa fa-pinterest"></i></a></li>
						<li><a href="#"><i class="fa fa-dribbble"></i></a></li>
						<li><a href="#"><i class="fa fa-behance"></i></a></li>
						<li><a href="#"><i class="fa fa-flickr"></i></a></li>
						<li><a href="#"><i class="fa fa-youtube"></i></a></li>
						<li><a href="#"><i class="fa fa-linkedin"></i></a></li>
						<li><a href="#"><i class="fa fa-github"></i></a></li>
					</ul>
				</div>
			</div>
		</div>
	</footer>
	<!--/#footer-->

</body>