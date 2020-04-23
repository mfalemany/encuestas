<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Procesador de Encuestas</title>
	
	<link rel="stylesheet" type="text/css" href="./assets/css/estilos.css" />
	<link rel="stylesheet" type="text/css" href="./assets/css/bootstrap.min.css">
	<link href="favicon.ico" type="image/x-icon" rel="shortcut icon" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="container">
	<div class="row">
		<div class="col-xs-12">
			<h4 id="encabezado">GENERAR REPORTE DE ENCUESTAS</h4>	
		</div>	
	</div>
	
	<form method="POST" action="librerias/procesadorResultados.php" enctype="multipart/form-data" id="formulario" target="_BLANK">
		<!-- ############################ DETALLES DE LA ENCUESTA ########################### -->
		<div class="row">
			<div class="col-xs-12" id="detalles_encuesta">
				<h4>Detalles de la Encuesta</h4>
			</div>	
		</div>			
		<!-- ########################################################################################## -->	

		<!-- ############################ SELECCI? DE A? ############################################ -->
		<div class="row">
			<div class="col-xs-10" style="text-align:right;">
				Año de la encuesta:
			</div>	
			<div class="col-xs-2">
				<input type="number" name="anio_encuesta" value="2020" required="">
			</div>	
		</div>

		<!-- ############################ RESULTADOS DE ENCUESTAS A DOCENTE ########################### -->
		<div class="row">
			<div class="col-xs-12" id="datos_docente">
				
				<div class="form-group" id="archivos_docente">
					<p class="help-block" style="background-color:#428442; color:#FFF; padding: 2px 0px 2px 10px;">
						Seleccione los archivos, exportados del SIU-Kolla, que contengan los resultados de encuestas.
					</p>
					<!-- ############ SE REPITE POR CADA ARCHIVO ###################-->
					
					<div class="form-inline">
						<input type="file" name="resultados[]" id="resultados" class="form-control btn btn-xs" multiple="multiple" />
					</div>
					<!-- ###########################################################-->	
				</div>
			</div>
		</div>	
		<!-- ########################################################################################## -->	
		

		<div class="row">
			<div class="col-xs-8 col-xs-offset-2">
				<div class="form-group">
					<input type="submit" value="Procesar" id="btn_procesar" class="form-control btn btn-primary" />
				</div>
			</div>
		</div>
		</form>	
	</div>
	
	<div id="contenedor_error">
		No se ha seleccionado un archivo de resultados para procesar
	</div>

	<!-- Script de jQuery -->
	<script type="text/javascript" src="./assets/js/jquery.min.js"></script>
	<!-- Script de la aplicacion -->
	<!-- <script type="text/javascript" src="./assets/js/script.js"></script> -->
	<!-- Script de Bootstrap -->
	<script type="text/javascript" src="./assets/js/bootstrap.min.js"></script>
	
</body>
</html>