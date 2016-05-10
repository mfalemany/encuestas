<?php 
	require_once "../fpdf/fpdf.php";
	class ReporteEncuesta extends FPDF{
		private $imagen_encabezado;

		function __construct($datos){
			parent::__construct();
			$this->set_imagen_encabezado("../encabezado.jpg");
			$this->SetTopMargin(50);
			$this->generar($datos);
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
			/* ========== ENCABEZADO DE LA ENCUESTA: CATEDRA Y/O DOCENTE SI CORRESPONDE ========== */
			$this->SetFont('Arial','B',11);
			$this->setTextColor(255,255,255);
			$this->SetFillColor(79,162,88);
			//Cell(float w, float h, string txt, mixed border, int ln, string align, boolean fill, mixed link)
			$this->setXY(10,50);

			//Acá se muestra si es encuesta de cátedra o de docente
			$this->Cell(0,6,'Objetivo de la encuesta: '.ucwords(strtolower($datos->get_tipo_elemento())),1,1,'C',true);
			
			//en el caso de catedra, esta linea me elimina el codigo de materia que aparece junto al nombre de la catedra
			$this->SetFont('Arial','BU',15);
			$this->setTextColor(10,10,10);
			$nom_elem = explode("(",$datos->get_nombre_elemento());
			$this->Cell(0,15,strtoupper( $nom_elem[0]  ),0,1,'C',false);
			
			
			foreach ($datos->get_preguntas() as $pregunta => $opciones) {
				//fuente
				$this->SetFont('Arial','B',9);
				$this->setTextColor(20,20,20);
				//color de relleno
				$this->SetFillColor(235,255,238);
				//color del borde
				$this->SetDrawColor(20,20,20);

				$this->MultiCell(0,6,$pregunta,1,'',true);
				//Vuelvo a los valores de relleno, borde y texto normales
				$this->setTextColor(0,0,0);
				$this->SetFillColor(255,255,255);
				$this->SetDrawColor(0,0,0);

				$this->SetFillColor(255,255,255);
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
					$this->Cell(160,6,$opcion                  ,1,0,'L',false);
					$this->Cell(10,6,$cantidad                ,1,0,'C',false);
					$this->Cell(20 ,6,round($porcentaje,2)."%",1,1,'C',false);
				}
				$this->Ln();
			}
			$this->Ln();
			$this->SetFont('Arial','B',11);
			$this->SetFillColor(82,82,82);
			$this->setTextColor(255,255,255);

			//Impresión del título "Observaciones"
			$this->Cell(0,9,"OBSERVACIONES",1,1,'C',true);
			$this->Ln();
			$this->setTextColor(0,0,0);
			//Se muestra cada observación
			foreach ($datos->get_observaciones() as $key => $observacion) {
				$this->SetFont('Arial','',8);
				$this->MultiCell(0,7,ucfirst(strtolower($observacion)),1,1,'',false);
			}
			$this->Ln();

			$this->SetFont('Arial','B',8);
			//Se imprime la cantidad de observaciones que se omitieron
			$this->MultiCell(0,5,"Importante: Se omitieron ".$datos->get_obs_omitidas()." respuestas automaticamente por ser del tipo \"No opino\",\"Sin comentarios\",\"-----\" o respuestas solo con espacios en blanco.",0,1,'',false);
			
			//Se imprime el PDF
			//$this->Output("D",$datos->get_nombre_elemento().".pdf");
			$this->Output("I",$datos->get_nombre_elemento().".pdf");
			//var_dump($datos);
		}
	}
?>