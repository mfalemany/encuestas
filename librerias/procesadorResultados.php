<?php 
	require_once("reporte_encuesta.php");
	
	class ProcesadorResultados{
		private $error;
		private $preguntas;
		private $observaciones;
		private $nombre_elemento;
		private $tipo_elemento;
		private $cant_respuestas;
		private $obs_omitidas;

		function __construct($archivo_resultados){
			$this->set_obs_omitidas(0);
			$this->set_error(NULL);
			$this->set_preguntas(array());
			$this->set_observaciones(array());
			$this->set_nombre_elemento("");
			$this->set_tipo_elemento("");
			
			
			if($archivo_resultados){
				$this->leer_archivo_resultados($archivo_resultados);
			}else{
				$this->set_error("No se ha seleccionado ningun archivo o no pudo accederse al mismo.");
			}
		}

		public function set_error($error){
			$this->error = $error;
		}
		public function set_preguntas($preguntas){
			if(is_array($preguntas)){
				if(count($preguntas) > 0){
					$this->preguntas = $preguntas;		
				}else{
					$this->preguntas = array();
				}
			}
			
		}
		public function set_observaciones($observaciones){
			if(is_array($observaciones)){
				if(count($observaciones) > 0){
					$this->observaciones = $observaciones;		
				}else{
					$this->observaciones = array();
				}
			}
			
		}
		public function set_nombre_elemento($nombre_elemento){
			$this->nombre_elemento = $nombre_elemento;
		}
		public function set_tipo_elemento($tipo_elemento){
			$this->tipo_elemento = $tipo_elemento;
		}
		
		public function set_obs_omitidas($cant){
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
			$ignorar = array("/^[\.\-\,\ ]+$/","/no.*tu/i","/no.*ten/i","/no fue mi/i","/no opin/i","/sin coment/i","/^ningun./i","/^sin observ/i","/no hay observ/i");
			if(strlen($obs) <= 8){
				
				$this->set_obs_omitidas($this->get_obs_omitidas() + 1);
				return false;
			}
			foreach($ignorar as $patron){
				if( preg_match($patron,strtolower(trim($obs))) ){
					//me aseguro que la porcion encontrada no sea parte de una observacion larga
					if( strlen(trim($obs)) > 50){
						array_push($this->observaciones,$obs);	
						return true;
					}else{
						$this->set_obs_omitidas($this->get_obs_omitidas() + 1);
						return false;	
					}
				}
			}

			array_push($this->observaciones,trim($obs) );
			return true;	
			
			
		}

		private function leer_archivo_resultados($ubicacion){
			$archivo = fopen($ubicacion,"r");
			$cant_respuestas = 0;
			while($registro = fgets($archivo) ){
				
				//obtengo campos por separado
				$campos = explode("|", $registro);
				
				//esta fila contiene los encabezados 
				if($cant_respuestas > 0){
					//solo en el primer registro, obtengo 
					if($cant_respuestas == 1){
						//obtengo el nombre del docente/catedra
						$this->set_nombre_elemento($campos[1]);
						$this->set_tipo_elemento($campos[2]);
					}
				
					$pregunta = $campos[4];
					$opcion = $campos[5];
					if( strtolower($pregunta) == "observaciones:" ){
						//en este caso, $opcion es un comentario u observacion
						$this->agregar_observacion($opcion);
					}else{
						if( ! array_key_exists($pregunta,$this->get_preguntas()) ){
							$this->preguntas[$pregunta] = array();	
						}
						$preg = $this->get_preguntas();
						if( ! array_key_exists($opcion, $preg[$pregunta]) ){
							$this->preguntas[$pregunta][$opcion] = 1;
						}else{
							$this->preguntas[$pregunta][$opcion]++;
						}
					}
				}	
				$cant_respuestas++;	
			}
		}
	}
	
	
	
	if( move_uploaded_file ( $_FILES['archivo_encuestas']['tmp_name'] , "../temporales/".$_FILES['archivo_encuestas']['name'] ) ){
		$datos = new ProcesadorResultados("../temporales/".$_FILES['archivo_encuestas']['name']);
		
		$reporte = new ReporteEncuesta($datos);	
	}else{
		echo "No se pudo subir el archivo de resultados: ";
		die;
	}
?>