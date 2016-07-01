<?php 
	require_once("reporte_encuesta.php");
	
	class ProcesadorResultados{
		private $error; //contiente detalles de errores en caso de ocurrir
		private $preguntas;
		private $observaciones;
		private $nombre_elemento; //Contiene un nombre de catedra o de docente
		private $tipo_elemento; //Indica si la encuesta es de docente o de catedra
		private $obs_omitidas; //Almacena la cantidad de observaciones omitidas

		//constructor de clase
		function __construct($archivo_resultados){
			$this->set_obs_omitidas();
			$this->set_error();
			$this->set_preguntas();
			$this->set_observaciones();
			$this->set_nombre_elemento();
			$this->set_tipo_elemento();
			
			//si se pasó como parametro un nombre de archivo
			if($archivo_resultados){
				$this->leer_archivo_resultados($archivo_resultados);
				if($this->get_error()){
					echo "Ocurri&oacute; el siguiente error: ".$this->get_error();
					die;
				}	
			}else{
				$this->set_error("No se ha seleccionado ningun archivo o no pudo accederse al mismo.");
			}
		}

		public function set_error($error = FALSE){
			$this->error = $error;
		}
		public function set_preguntas($preguntas = array()){
			if(is_array($preguntas)){
				if(count($preguntas) > 0){
					$this->preguntas = $preguntas;		
				}else{
					$this->preguntas = array();
				}
			}
			
		}
		public function set_observaciones($observaciones = array()){
			if(is_array($observaciones)){
				if(count($observaciones) > 0){
					$this->observaciones = $observaciones;		
				}else{
					$this->observaciones = array();
				}
			}
			
		}
		public function set_nombre_elemento($nombre_elemento = "No definido"){
			$this->nombre_elemento = $nombre_elemento;
		}
		public function set_tipo_elemento($tipo_elemento = "No definido"){
			$this->tipo_elemento = $tipo_elemento;
		}
		
		public function set_obs_omitidas($cant = 0){
			$this->obs_omitidas = $cant;
		}
		public function get_error(){
			return $this->error;
		}
		public function get_preguntas(){
			return $this->preguntas;
		}
		public function get_observaciones(){
			return $this->observaciones;
		}
		public function get_nombre_elemento(){
			return $this->nombre_elemento;
		}
		public function get_tipo_elemento(){
			return $this->tipo_elemento;
		}
		
		public function get_obs_omitidas(){
			return $this->obs_omitidas;
		}

		
		private function agregar_observacion($obs){
			//patrones que se ignoran por no contener observaciones y/o comentarios relevantes
			$ignorar = array("/^[\.\-\,\ ]+$/","/no.*tu/i","/no.*ten/i","/no fue mi/i","/no opin/i","/sin coment/i","/^ningun./i","/^sin observ/i","/no hay observ/i");
			
			//Si la observacion tiene menos de 8 caracteres no es relevante
			if(strlen($obs) <= 8){
				//se contabiliza como observacion omitida
				$this->set_obs_omitidas($this->get_obs_omitidas() + 1);
				return FALSE;
			}
			//se compara la observacion con los patrones definidos como irrelevantes
			foreach($ignorar as $patron){
				//si la obsevacion contiene el patrón
				if( preg_match($patron,strtolower(trim($obs))) ){
					//me aseguro que la porcion encontrada no sea parte de una observacion larga
					if( strlen(trim($obs)) > 50){
						array_push($this->observaciones,$obs);	
						return TRUE;
					}else{
						//sino, se contabiliza como omitida
						$this->set_obs_omitidas($this->get_obs_omitidas() + 1);
						return FALSE;	
					}
				}
			}

			array_push($this->observaciones,trim($obs));
			return TRUE;	
			
			
		}

		

		/*private function leer_archivo_resultados($ubicacion){

			//si el archivo no existe o no ex válido
			if( ! is_file($ubicacion)){
				$this->set_error("No se ha podido encontrar el fichero o no es valido");
				return FALSE;
			}
			
			// variable que contiene el contenido del archivo en texto plano
			$archivo = fopen($ubicacion,"r");

			//inicializamos un contador
			$cant_respuestas = 0;
			
			//mientras pueda leer una linea del fichero
			while($registro = fgets($archivo) ){
				
				//obtengo campos por separado
				$campos = explode("|", $registro);
				
				//esta fila contiene los encabezados  (tiene que ver con el formato del archivo generado por SIU-KOLLA) 
				if($cant_respuestas > 0){
					//solo en el primer registro
					if($cant_respuestas == 1){
						//obtengo el nombre del docente/catedra
						$this->set_nombre_elemento($campos[1]);
						$this->set_tipo_elemento($campos[2]);
					}
					
					//registro la pregunta y opcion actuales
					$pregunta = $campos[4];
					$opcion = $campos[5];

					// En la Fac. Cs. Agrarias, el campo "Observaciones" es un cuadro de texto libre, por lo que no se
					// contabilizan los resultados, sino que se muestran tal y como se cargaron en la encuesta (siempre que 
					// pasen ciertos filtros) */
					//if( preg_match("/^observac.*/",strtolower(trim($pregunta))) ){
						//en este caso, $opcion es un comentario u observacion
					/*	$this->agregar_observacion($opcion);
						
					}else{
						//si todavía no existe la pregunta, la inicializo como indice del array
						if( ! array_key_exists($pregunta,$this->get_preguntas()) ){
							$this->preguntas[$pregunta] = array();	
						}
						$preg = $this->get_preguntas();
						//si no existe la opcion dentro de la pregunta definida, la agrego como indice e inicializo en uno
						if( ! array_key_exists($opcion, $preg[$pregunta]) ){
							$this->preguntas[$pregunta][$opcion] = 1;
						}else{
							//si ya está definida, solo sumo uno
							$this->preguntas[$pregunta][$opcion]++;
						}
					}
				}	
				$cant_respuestas++;	
			}

		}*/
	}


	// Si se recibió el archivo en el servidor, y se movio a la carpeta de temporales
	if( move_uploaded_file ( $_FILES['archivo_encuestas']['tmp_name'] , "../temporales/".$_FILES['archivo_encuestas']['name'] ) ){
		//proceso los resultados
		$datos = new ProcesadorResultados("../temporales/".$_FILES['archivo_encuestas']['name']);
		//y con los resultados ya procesados, genero el reporte de encuesta
		$reporte = new ReporteEncuesta($datos);	
	}else{
		echo "No se pudo subir el archivo de resultados: ".$datos->get_error();
		die;
	}
?>