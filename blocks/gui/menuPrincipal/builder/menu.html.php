<?php 
/*
 *  Sintaxis recomendada para las plantillas PHP
 */ 
?>
  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

<nav id="main-menu" class="navbar navbar-default navbar-fixed-top"
     role="banner">
		<div class="container">
		
		<div class="navbar-header">
            <a class="navbar-brand" href="index.html"><img
                    src="images/logo.png" alt="logo"></a>
        </div>
        
         <div class="collapse navbar-collapse navbar-right">
		
			<ul  class="nav navbar-nav">
		<?php  foreach ( $this->atributos['enlaces']  as $nombrePagina => $columnas ): ?>	
			<?php
				//Si el tipo es menú, se llama el título del menú y el enlace del menú.
				$tituloMenu = array_keys($columnas['columna']['menu'])[0];
				$enlaceMenu = $columnas['columna']['menu'][$tituloMenu];
				//Cuando se tienen estos datos, se elimina este primer término.
				unset($columnas['columna']);
				//Si solo tenía un registro de la clase menú, no se seguirá dibujando, de lo contrario entra a dibujar los menús y submenús.
				if (count($columnas)>0): ?>	
					<?php $numColumnas = count($columnas); $tit=0; ?>	
					<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $tituloMenu ?><span class="caret"></span></a>
					<ul class="dropdown-menu">
					<?php foreach ( $columnas as $col=>$item): ?>
						<?php foreach ( $item as $clase=>$paginas): ?>	
							<?php foreach ( $paginas as $nombrePagina=>$enlace): ?>
								<?php if ($clase=='submenu'):?>
									<?php if ($tit==0):?>
											<li class="dropdown dropdown-submenu"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $nombrePagina ?></a>
												<ul class="dropdown-menu">
												<?php $tit++;?>
									<?php elseif ($tit>0):?>
											</ul>
										</li>
										<li class="dropdown dropdown-submenu"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $nombrePagina ?></a>
					<ul class="dropdown-menu">
									<?php endif; ?>
					      		<?php else:?>
					      			<li><a href="<?php echo $enlace ?>"><?php echo $nombrePagina ?></a></li>
					      		<?php endif; ?>			      							
							<?php endforeach; ?> 
								
						<?php endforeach; ?> 	
							<?php if ($tit>0):?>
									<?php $tit=0;?>
										</ul>
										</li>
								<?php endif; ?>
					<?php endforeach; ?> 
					</ul>
			
			<?php else: ?>
				<li><a href='<?php echo $enlaceMenu ?>'><?php echo $tituloMenu ?></a></li>
			<?php endif; ?>
		<?php endforeach; ?>
					</li>
				</ul>	
				</div>
	</div>
</nav>


