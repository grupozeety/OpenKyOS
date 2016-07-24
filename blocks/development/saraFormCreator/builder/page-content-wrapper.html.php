<!-- Page Content -->
<div id="page-content-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-6">
				<div class="panel-group" id="accordion">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion"
									href="#collapseOne"><span
									class="glyphicon glyphicon-folder-open"> </span>Contenedores</a>
							</h4>
						</div>
						<div id="collapseOne" class="panel-collapse collapse in">
							<div class="panel-body">
								<div class='row-fluid'>
									<ol class='nested_with_no_drop vertical'>
										<?php
										foreach ($this->atributos['contenedores'] as $nombre){
											echo "<li class='highlight'><i class='icon-move'></i> $nombre ";
											echo "<i class='icon-config' value='$nombre'></i>";
											echo '<i class=\'icon-remove\' removable=\'false\'></i><ol></ol></li>';
										}
										?>
									</ol>
								</div>
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion"
									href="#collapseTwo"><span class="glyphicon glyphicon-th"> </span>Componentes
									básicos</a>
							</h4>
						</div>
						<div id="collapseTwo" class="panel-collapse collapse">
							<div class="panel-body">
								<div class='row-fluid'>
									<ol class='nested_with_no_drop vertical'>
										<?php
										foreach ($this->atributos['componentesBasicos'] as $nombre){
											echo "<li><i class='icon-move'></i> $nombre ";
											echo "<i class='icon-config' value='$nombre'></i>";
											echo '<i class=\'icon-remove\' removable=\'false\'></i></li>';
										}
										?>
									</ol>
								</div>
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion"
									href="#collapseThree"><span class="glyphicon glyphicon-list"> </span>Otros
									componentes</a>
							</h4>
						</div>
						<div id="collapseThree" class="panel-collapse collapse">
							<div class="panel-body">
								<div class='row-fluid'>
									<ol class='nested_with_no_drop vertical'>
										<?php
										foreach ($this->atributos['otrosComponentes'] as $nombre){
											echo "<li><i class='icon-move'></i> $nombre ";
											echo "<i class='icon-config' value='$nombre'></i>";
											echo '<i class=\'icon-remove\' removable=\'false\'></i></li>';
										}
										?>
									</ol>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<a href="#menu-toggle" class="btn btn-default menu-toggle">Mostrar/Ocultar
					código</a>
				<h3>Elementos en página</h3>
				<div>
					<div class='row-fluid box-rounded'>
						<ol class='nested_with_drop vertical' id='contenidoFormulario'>
						</ol>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /#page-content-wrapper -->
