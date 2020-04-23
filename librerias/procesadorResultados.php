<?php 
	require_once("reporte_encuesta.php");
	
	class ProcesadorResultados{
		private $error; //contiente detalles de errores en caso de ocurrir
		private $preguntas;
		private $observaciones;
		private $obs_omitidas; //Almacena la cantidad de observaciones omitidas
		private $nombre_elemento; //Contiene un nombre de catedra o de docente
		private $tipo_elemento; //Contiene si es una catedra o un docente
		private $archivos_resultados;
		public $numero_encuestados;
		public $anio_encuesta;
		

		//constructor de clase
		function __construct(){

			//controlamos que todos los archivos se hayan subido y movido con exito
			$this->verificar_archivos();
			$this->set_error();
			$this->set_preguntas();
			$this->set_nombre_elemento();
			$this->set_observaciones();

			$this->numero_encuestados = 0;

			//recorro los archivos de resultados y los proceso uno a uno
			foreach($this->archivos_resultados as $nombre_archivo){
				if( ! $this->leer_archivo_resultados($nombre_archivo)){
					echo $this->get_error();
					die;
				}
			}
			$this->calcular_numero_encuestados();
			$this->anio_encuesta = (isset($_POST['anio_encuesta'])) ? $_POST['anio_encuesta'] : date('Y');

			
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
		public function set_obs_omitidas($cant = 0){
			$this->obs_omitidas = $cant;
		}
		
		public function set_nombre_elemento($nombre_elemento = null){
			$this->nombre_elemento = $nombre_elemento;
		}
		public function set_tipo_elemento($tipo_elemento = null){
			$this->tipo_elemento = $tipo_elemento;
		}
		
		public function get_error(){
			return $this->error;
		}
		public function get_preguntas(){
			return $this->preguntas;
		}
		
		public function get_nombre_elemento(){
			return $this->nombre_elemento;
		}
		public function get_tipo_elemento(){
			return $this->tipo_elemento;
		}
		public function get_observaciones(){
			return $this->observaciones;
		}
		
		public function get_obs_omitidas(){
			return $this->obs_omitidas;
		}
		
		private function agregar_observacion($obs){
			//var_dump($obs); die;
			//patrones que se ignoran por no contener observaciones y/o comentarios relevantes
			$ignorar = array("/^[\.\-\,\ ]+$/","/no.*tu/i","/no.*ten/i","/no fue mi/i","/no opin/i","/sin coment/i","/^ningun./i","/^sin observ/i","/no hay observ/i","/nada para agregar/i","/nada en especial/i","/sin opini/i","/^sin descripc/i");
			
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
			//var_dump($this->observaciones); die;	
			array_push($this->observaciones,trim($obs));
			return TRUE;	
		}

		private function leer_archivo_resultados($ubicacion){
			//si el archivo no existe o no ex válido
			if( ! is_file($ubicacion)){
				$this->set_error("Ocurrió un error al intentar leer el archivo de $categoria: $ubicacion, por lo cual los números procesados no reflejan la totalidad de respuestas. El reporte no se generará.");
				return FALSE;
			}
			
			//mantiene el número de linea actualmente leida desde el archivo
			$linea = 0;
					
			// variable que contiene el contenido del archivo en texto plano
			$archivo = fopen($ubicacion,"r");
			//mientras pueda leer una linea del fichero
			while($registro = fgets($archivo) ){
				
				//obtengo campos por separado
				$campos = explode("|", $registro);

				/*esta fila contiene los encabezados  (tiene que ver con el formato del archivo generado por SIU-KOLLA) */
				if($linea > 0){
					//solo en el primer registro
					if($linea == 1){
						//si no se asignó un nombre de elemento (solo sucede al leer el primer archivo)
						//var_dump($campos);
						if( ! $this->get_nombre_elemento() ){
							//obtengo el nombre del docente/catedra
							$this->set_nombre_elemento($campos[2]);
							$this->set_tipo_elemento($campos[0]);
						}
						$linea++;
					}
					$linea++;
					
					//registro la pregunta y opcion actuales
					$pregunta = $campos[5];
					$opcion = $campos[6];

					// En la Fac. Cs. Agrarias, el campo "Observaciones" es un cuadro de texto libre, por lo que no se
					// contabilizan los resultados, sino que se muestran tal y como se cargaron en la encuesta (siempre que 
					// pasen ciertos filtros) */
					if( preg_match("/^observac.*/",strtolower(trim($pregunta))) ){
						//en este caso, $opcion es un comentario u observacion
						$this->agregar_observacion($opcion);
						
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
					
				}else{
					$linea++;
				}	
				
			}
			return true;
		}

		//funcion que comprueba los archivos subidos y los mueve a la carpeta de temporales para trabajarlos
		private function verificar_archivos(){
			
			$archivos = array();
			for($i = 0; $i < count($_FILES['resultados']['name']); $i++){
				$archivos[$_FILES['resultados']['name'][$i]] = array(
					'type'     => $_FILES['resultados']['type'][$i],
					'tmp_name' => $_FILES['resultados']['tmp_name'][$i],
					'error'    => $_FILES['resultados']['error'][$i],
					'size'     => $_FILES['resultados']['size'][$i]
				);
			}
			
			//recorro todos los archivos recibidos
			foreach ($archivos as $nombre => $detalles) {
				//intento mover el archivo subido a la carpeta de temporales
				if( move_uploaded_file($detalles['tmp_name'], "../temporales/".$nombre.".txt" )){
					$this->archivos_resultados[] = "../temporales/".$nombre.".txt";
					//verifico si es un archivo de resultados docente o cátedra
					//if( preg_match("/file_doc_(.)*/i", $nombre) ){
					//	//creo un array con cada conjunto de archivos
					//	$this->archivos_resultados['docente'][] = "../temporales/".$nombre.".txt";
					//}else{
					//	$this->archivos_resultados['catedra'][] = "../temporales/".$nombre.".txt";
					//}
				}else{
					//configurar que hacer con los archivos que fallaron al mover
				}
			}
			//var_dump($this->archivos_resultados); die;
		}

		private function calcular_numero_encuestados(){
			$preg = $this->get_preguntas();
			
			foreach(current($preg) as $opcion => $cantidad){
				$this->numero_encuestados += $cantidad;
			}
		}
	}
	
	//proceso los resultados
	$datos = new ProcesadorResultados();
	//var_dump($datos);die;
	//y con los resultados ya procesados, genero el reporte de encuesta
	$reporte = new ReporteEncuesta($datos);	
?>