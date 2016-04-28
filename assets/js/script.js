window.onload = function(){

	document.getElementById('btn_procesar').onclick = function(e){
		//Si no se selecciona un archivo, se muestra un error en
		if(document.getElementById('archivo').files.length == 0){
			mostrarError('No se ha seleccionado un archivo de resultados para procesar');
			e.preventDefault();
		}else{
			if(document.getElementById('archivo').files[0].type != "text/plain"){
				mostrarError('Tipo de archivo no permitido. Por favor, seleccione el archivo de texto (con extensi&oacute;n .txt) generado con SIU-Kolla');
				e.preventDefault();	
			}
		}
	}
}



function mostrarError(mensaje){
	document.getElementById('contenedor_error').style.display = 'block';
	document.getElementById('contenedor_error').innerHTML = mensaje;
	setTimeout(function(){
		document.getElementById('contenedor_error').style.display = 'none';
	},3000)
}