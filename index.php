<!DOCTYPE html>
<html>
<head>
	<title>Procesa Encuestas</title>
	<style type="text/css">
	#formulario{
		width: 500px;
		margin: 50px auto auto auto;
	}
	#formulario h1{
		font-size: 1.2em;
		background-color: #056E96;
		color: #FFFFFF;
		padding: 10px 10px 10px 10px;
	}
	#btn_archivo{
	    border-radius: 10px;
		background-color: #fff;
		color: #000;
		border: 2px solid #666;
		padding: 5px 0px 5px 15px;
		margin: 3px 0px 3px 0px;
		width: 90%;
	}
	#btn_procesar{
	    border-radius: 10px;
		background-color: #fff;
		color: #000;
		border: 2px solid #0AB063;
		padding: 5px 15px 5px 15px;
		margin: 3px 0px 3px 0px;
		width: 100%;
		background-color: #1BC174;
		color: #FFFFFF;
		font-size: 1.1em;
		font-weight: bolder;
	}
	</style>
</head>
<body>

	<form method="POST" action="librerias/procesadorResultados.php" enctype="multipart/form-data" id="formulario" target="_BLANK">
		<center><h1>GENERAR REPORTE DE ENCUESTAS</h1></center>
		<center><input type="file" name="archivo_encuestas" id="btn_archivo"  /></center>
		<br>
		<input type="submit" value="Procesar" id="btn_procesar"/>
	</form>
</body>
</html>