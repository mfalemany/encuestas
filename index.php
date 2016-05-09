<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Procesador de Encuestas</title>
	<script type="text/javascript" src="./assets/js/script.js"></script>
	<link rel="stylesheet" type="text/css" href="./assets/css/estilos.css" />
	<link href="favicon.ico" type="image/x-icon" rel="shortcut icon" />
</head>
<body>
	
	<form method="POST" action="librerias/procesadorResultados.php" enctype="multipart/form-data" id="formulario" target="_BLANK">
		<h1>GENERAR REPORTE DE ENCUESTAS</h1>
		<input type="file" name="archivo_encuestas" id="archivo"  />
		<br>
		<input type="submit" value="Procesar" id="btn_procesar"/>
	</form>
	<div id="contenedor_error">
		No se ha seleccionado un archivo de resultados para procesar
	</div>
</body>
</html>