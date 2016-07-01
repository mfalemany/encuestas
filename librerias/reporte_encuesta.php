<?php 
	require_once "fpdf/fpdf.php";
	class ReporteEncuesta extends FPDF{
		private $imagen_encabezado;

		function __construct($datos){
			parent::__construct();
			$this->set_imagen_encabezado("../assets/img/encabezado.jpg");
			$this->SetTopMargin(50);
			$this->generar($datos);
		}

		private function set_fuente($familia,$tamano,$color_texto,$color_fondo,$variantes,$color_borde){
			$this->setFont($familia,$variantes,$tamano);
			$this->setTextColor($color_texto[0],$color_texto[1],$color_texto[2]);
			$this->SetFillColor($color_fondo[0],$color_fondo[1],$color_fondo[2]);
			$this->SetDrawColor($color_borde[0],$color_borde[1],$color_borde[2]);
		}

		
		public function set_imagen_encabezado($imagen){
			$this->imagen_encabezado = $imagen;
		}

		public function get_imagen_encabezado(){
			return $this->imagen_encabezado;
		}

		public function Header(){
			$imagen = $this->get_imagen_encabezado();
			$extension = explode(".",$imagen);
			$this->Image($imagen,20,15,0,0, strtoupper(end($extension)));

		}

		public function Footer(){
		    $this->SetY(-15);
			$this->SetFont('Arial','I',8);
			$this->Cell(0,10,'Pagina '.$this->PageNo(),0,0,'C');
		}

		function generar($datos){
			$this->AddPage();
			
			//imprimo el año actual

			$this->set_fuente("Arial",18,array(0,0,0),array(0,0,0),'BU',array(0,0,0));
			$this->setXY(8,20);
			$this->Cell(0,16,"REPORTE DE ENCUESTAS",0,1,'C',false);
			$this->setXY(86,33);
			$this->Cell(30,5,date("Y"),0,1,'C',false);

			//se muestra el total de respuestas registradas
			$this->setXY(170,66);
			$this->set_fuente("Courier",8,array(255,25,25),array(255,255,255),'',array(0,0,0));
			$this->Cell(0,5,"Respuestas: ".$datos->numero_encuestados,1,0,'C',true);
			

			$this->setXY(10,50);

			/* ========== ENCABEZADO DE LA ENCUESTA: CATEDRA Y/O DOCENTE SI CORRESPONDE ========== */
			$this->set_fuente("Arial",11,array(255,255,255),array(51,122,183),'B',array(0,0,0));
			//Acá se muestra si es encuesta de cátedra o de docente
			$this->Cell(0,6,'Objetivo de la encuesta: '.ucwords(strtolower($datos->get_tipo_elemento())),1,1,'C',true);
			
			//en el caso de catedra, esta linea me elimina el codigo de materia que aparece junto al nombre de la catedra
			$this->set_fuente("Arial",17,array(10,10,10),array(255,255,255),'B',array(255,255,255));
			
			$nom_elem = explode("(",$datos->get_nombre_elemento());
			$this->Cell(0,15,strtoupper( $nom_elem[0]  ),0,1,'C',false);
			
			
			foreach ($datos->get_preguntas() as $pregunta => $opciones) {
				//fuente
				$this->set_fuente("Arial",8,array(10,10,10),array(203,235,248),'B',array(0,0,0));
				$this->MultiCell(0,6,$pregunta,1,'',true);
				
				//Vuelvo a los valores de relleno, borde y texto normales
				$this->set_fuente("Times",8,array(0,0,0),array(255,255,255),'B',array(0,0,0));
				
				$total = 0;
				
				//calculo primero el total, para poder determinar los porcentajes que representa cada pregunta
				foreach ($opciones as $opcion => $cantidad) {
					$total += $cantidad;
				}

				//para cada opcion, muestro la cantidad y su porcentaje correspondiente
				foreach ($opciones as $opcion => $cantidad) {
					$this->SetFont('Arial','',8);
					//cálculo de porcentaje
					$porcentaje = $cantidad / $total * 100;
					$this->Cell(160,5,$opcion                  ,1,0,'L',false);
					$this->Cell(10,5,$cantidad                ,1,0,'C',false);
					$this->Cell(20,5,round($porcentaje,2)."%",1,1,'C',false);
				}
				$this->Ln();
			}
			//fuente
			$this->set_fuente("Times",13,array(255,255,255),array(57,96,111),'B',array(0,0,0));
			
			//Impresión del título "Observaciones"
			$this->Cell(0,9,"OBSERVACIONES",1,1,'C',true);
			
			//fuente
			$this->set_fuente("Times",11,array(0,0,0),array(57,96,111),'B',array(0,0,0));
			
			//Se muestra cada observación
			foreach ($datos->get_observaciones() as $key => $observacion) {
				$this->SetFont('Arial','',9);
				$this->MultiCell(0,6,ucfirst(strtolower($observacion)),1,1,'',false);
			}
			$this->Ln();

			//fuente
			$this->set_fuente("Times",9,array(0,0,0),array(57,96,111),'',array(0,0,0));
			
			//Se imprime la cantidad de observaciones que se omitieron
			$this->MultiCell(0,4,"Importante: Se omitieron ".$datos->get_obs_omitidas()." respuestas automaticamente por ser del tipo \"No opino\",\"Sin comentarios\",\"-----\" o respuestas solo con espacios en blanco.",0,1,'',false);
			
			//Se imprime el PDF
			//$this->Output("D",$datos->get_nombre_elemento().".pdf");
			$this->Output("I",$datos->get_nombre_elemento().".pdf");
			//var_dump($datos);
		}
	}
?>