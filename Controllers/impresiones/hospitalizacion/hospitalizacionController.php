<?php


/*
 * Controlador para la impresion de una hospitalizacion
 */
 
 	if(!isset($_SESSION)){
		    session_start();
		}
 
 ob_start();
 
 	$idHospitalizacion	= $_GET['id2'];
 
 //se importa el archivo de la libreria que genera el pdf
 require_once("libs/TCPDF-master/tcpdf.php");
 
 
 //se consulta la información de la cuenta y la sucursal para mostrar el encabezado
 require_once("Models/cuenta_model.php");
 
 $objetoCuenta	= new cuenta(); 
 $datosCuenta	= $objetoCuenta->consultarInformacionCuenta();
 
 //se consulta la información de la hospitalizacion
 require_once("Models/hospitalizacion_model.php");
 
 $objetoHospitalizacion	= new hospitalizacion(); 
 $datoshospitalizacion	= $objetoHospitalizacion->listarUnaHospitalizacion($idHospitalizacion);
 

 
 //se consulta la informacion del paciente y del propietario
 require_once("Models/nuevoPacientePropietario_model.php");
 
 $objetoPropietarioPaciente	= new nuevo(); 
 $datosPropietarioPaciente = $objetoPropietarioPaciente->consultarDatosPropietarioPaciente($datoshospitalizacion[0]['idMascota']);
 

 
//funcion para calcular el tiempo de vida con php
function tiempoTranscurridoFechas($fechaInicio){
	    $fecha1 = new DateTime($fechaInicio);
	    $fecha2 = new DateTime();
	    $fecha = $fecha1->diff($fecha2);
	    $tiempo = "";
	         
	    //años
	    if($fecha->y > 0)
	    {
	        $tiempo .= $fecha->y;
	             
	        if($fecha->y == 1)
	            $tiempo .= " año, ";
	        else
	            $tiempo .= " años, ";
	    }
	         
	    //meses
	    if($fecha->m > 0)
	    {
	        $tiempo .= $fecha->m;
	             
	        if($fecha->m == 1)
	            $tiempo .= " mes, ";
	        else
	            $tiempo .= " meses, ";
	    }
	         
	    //dias
	    if($fecha->d > 0)
	    {
	        $tiempo .= $fecha->d;
	             
	        if($fecha->d == 1)
	            $tiempo .= " día ";
	        else
	            $tiempo .= " días ";
	    }
	         
	    return $tiempo;
	}//fin funcion tiempo de vida php 
 
 
 // Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

	 public $header_identificativoNit;
	 public $header_nombre;
	 public $header_telefono1;
	 public $header_telefono2;
	 public $header_celular;
	 public $header_direccion;
	 public $header_email;
	 public $header_urlLogo;

	///inicializar variables
	public function setDataHeader($header_identificativoNit, $header_nombre, $header_telefono1, $header_telefono2, $header_celular, $header_direccion, 
									$header_email, $header_urlLogo){
										
    	$this->header_identificativoNit = $header_identificativoNit;
		$this->header_nombre 			= $header_nombre;
		$this->header_telefono1 		= $header_telefono1;
		$this->header_telefono2 		= $header_telefono2;
		$this->header_celular 			= $header_celular;
		$this->header_direccion 		= $header_direccion;
		$this->header_email 			= $header_email;
		$this->header_urlLogo 			= $header_urlLogo;
    }


    //Page header
    public function Header() {   	
		
        if ($this->header_xobjid === false) {
			// start a new XObject Template
			$this->header_xobjid = $this->startTemplate($this->w, $this->tMargin);
			$headerfont = $this->getHeaderFont();
			$headerdata = $this->getHeaderData();
			$this->y = $this->header_margin;
			if ($this->rtl) {
				$this->x = $this->w - $this->original_rMargin;
			} else {
				$this->x = $this->original_lMargin;
			}

			
			//saber si existe una imagen
			if($this->header_urlLogo != ""){
				
				
					$imgtype = TCPDF_IMAGES::getImageFileType('Public/uploads/'.$_SESSION['BDC_carpeta'].'/logoClinica/'.$this->header_urlLogo);
					if (($imgtype == 'eps') OR ($imgtype == 'ai')) {
						$this->ImageEps('Public/uploads/'.$_SESSION['BDC_carpeta'].'/logoClinica/'.$this->header_urlLogo, '', '', $headerdata['logo_width']);
					} elseif ($imgtype == 'svg') {
						$this->ImageSVG('Public/uploads/'.$_SESSION['BDC_carpeta'].'/logoClinica/'.$this->header_urlLogo, '', '', $headerdata['logo_width']);
					} else {
						$this->Image('Public/uploads/'.$_SESSION['BDC_carpeta'].'/logoClinica/'.$this->header_urlLogo, '', '', '20','20');
					}
					$imgy = $this->getImageRBY();
				
				
				
			}else{
				$imgy = $this->y;
			}//fin if para saber si se muestra un logo
			
			 
			
			$cell_height = $this->getCellHeight($headerfont[2] / $this->k);
			// set starting margin for text data cell
			if ($this->getRTL()) {
				$header_x = $this->original_rMargin + ($headerdata['logo_width'] * 1.1);
			} else {
				$header_x = $this->original_lMargin + ($headerdata['logo_width'] * 1.1);
			}
			$cw = $this->w - $this->original_lMargin - $this->original_rMargin - ($headerdata['logo_width'] * 1.1);
			$this->SetTextColorArray($this->header_text_color);
			
			// header title
			$this->SetFont($headerfont[0], 'B', $headerfont[2] + 1);
			$this->SetX($header_x);
			$this->Cell($cw, $cell_height, $this->header_nombre, 0, 1, '', 0, '', 0);
			// header string
			$this->SetFont($headerfont[0], $headerfont[1], $headerfont[2]);
			$this->SetX($header_x);
			$this->MultiCell($cw, $cell_height, $this->header_telefono1.' - '.$this->header_telefono2.' - '.$this->header_celular   , 0, '', 0, 1, '', '', true, 0, false, true, 0, 'T', false);
			
			$this->SetFont($headerfont[0], $headerfont[1], $headerfont[2]);
			$this->SetX($header_x);
			$this->MultiCell($cw, $cell_height, $this->header_direccion , 0, '', 0, 1, '', '', true, 0, false, true, 0, 'T', false);
			
			$this->SetFont($headerfont[0], $headerfont[1], $headerfont[2]);
			$this->SetX($header_x);
			$this->MultiCell($cw, $cell_height, $this->header_email , 0, '', 0, 1, '', '', true, 0, false, true, 0, 'T', false);

			
			// print an ending header line
			$this->SetLineStyle(array('width' => 0.85 / $this->k, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $headerdata['line_color']));
			$this->SetY((2.835 / $this->k) + max($imgy, $this->y));
			if ($this->rtl) {
				$this->SetX($this->original_rMargin);
			} else {
				$this->SetX($this->original_lMargin);
			}
			$this->Cell(($this->w - $this->original_lMargin - $this->original_rMargin), 0, '', 'T', 0, 'C');
			$this->endTemplate();
		}
		// print header template
		$x = 0;
		$dx = 0;
		if (!$this->header_xobj_autoreset AND $this->booklet AND (($this->page % 2) == 0)) {
			// adjust margins for booklet mode
			$dx = ($this->original_lMargin - $this->original_rMargin);
		}
		if ($this->rtl) {
			$x = $this->w + $dx;
		} else {
			$x = 0 + $dx;
		}
		$this->printTemplate($this->header_xobjid, $x, 0, 0, 0, '', '', false);
		if ($this->header_xobj_autoreset) {
			// reset header xobject template at each page
			$this->header_xobjid = false;
		}
    }//fin metodo header
    
    //page footer
    public function footer(){
    	$cur_y = $this->y;
		$this->SetTextColorArray($this->footer_text_color);
		//set style for cell border
		$line_width = (0.85 / $this->k);
		$this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $this->footer_line_color));
		//print document barcode
		$barcode = $this->getBarcode();
		if (!empty($barcode)) {
			$this->Ln($line_width);
			$barcode_width = round(($this->w - $this->original_lMargin - $this->original_rMargin) / 3);
			$style = array(
				'position' => $this->rtl?'R':'L',
				'align' => $this->rtl?'R':'L',
				'stretch' => false,
				'fitwidth' => true,
				'cellfitalign' => '',
				'border' => false,
				'padding' => 0,
				'fgcolor' => array(0,0,0),
				'bgcolor' => false,
				'text' => false
			);
			$this->write1DBarcode($barcode, 'C128', '', $cur_y + $line_width, '', (($this->footer_margin / 3) - $line_width), 0.3, $style, '');
		}
		$w_page = isset($this->l['w_page']) ? $this->l['w_page'].' ' : '';
		if (empty($this->pagegroups)) {
			$pagenumtxt = $w_page.$this->getAliasNumPage().' / '.$this->getAliasNbPages();
		} else {
			$pagenumtxt = $w_page.$this->getPageNumGroupAlias().' / '.$this->getPageGroupAlias();
		}
		$this->SetY($cur_y);
		//Print page number
		if ($this->getRTL()) {
			$this->SetX($this->original_rMargin);
			$this->Cell(0, 0, $pagenumtxt, 'T', 0, 'L');
			
			$this->SetX($this->original_rMargin);
			$this->Cell(0, 0, $pagenumtxt, 'T', 0, 'L');
		} else {
			$this->SetX($this->original_lMargin);
			$this->Cell(0, 0, $this->getAliasRightShift().$pagenumtxt, 'T', 0, 'R');
			
			$this->SetX($this->original_rMargin);
			$this->Cell(17, 0, $this->getAliasRightShift().'www.nanuvet.com', 'T', 0, 'R');
		}
		

    } //fin metodo footer 


}//fin clase para personalizar el header y el footer
 

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->setDataHeader($datosCuenta['identificativoNit'], $datosCuenta['nombre'], $datosCuenta['telefono1'], $datosCuenta['telefono2'], $datosCuenta['celular'], $datosCuenta['direccion'], $datosCuenta['email'], $datosCuenta['urlLogo']);


// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('NanuVet');
$pdf->SetTitle('NanuVet::Hospitalización '.$idHospitalizacion);
$pdf->SetSubject('NanuVet');


// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
/*if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}*/

// ---------------------------------------------------------

// set font
$pdf->SetFont('dejavusans', '', 10);

// add a page
$pdf->AddPage();

// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

// create some HTML content
$html = '
<br/>
<div>
	<table border=0>
		<tr>
			<td>
				<b>Propietario:</b>
		
				'.$datosPropietarioPaciente[0]['nombrePropietario'].' '.$datosPropietarioPaciente[0]['apellido'].' ('.$datosPropietarioPaciente[0]['identificacion'].')'.'
			</td>
		</tr>
	</table>

<br/>


	<table border=0>
		<tr>
			<td width="70px">
				<b>Paciente</b>
			</td>
			<td width="170px">
				'.$datosPropietarioPaciente[0]['nombrePaciente'].'
			</td>

			<td width="70px">
				<b>Especie:</b>
			</td>
			<td width="150px">
				'.$datosPropietarioPaciente[0]['nombreEspecie'].'
			</td>

			<td width="70px">
				<b>Raza:</b>
			</td>
			<td width="150px">
				'.$datosPropietarioPaciente[0]['nombreRaza'].'
			</td>
		</tr>
	</table>
	
	<table border=0>
		<tr>
			<td width="70px">
				<b>Sexo:</b>
			</td>
			<td width="170px">
				'.$datosPropietarioPaciente[0]['sexo'].'
			</td>

			<td width="70px">
				<b>Edad:</b>
			</td>
			<td width="150px">
				'.tiempoTranscurridoFechas($datosPropietarioPaciente[0]['fechaNacimiento']).'
			</td>

			<td>
				<b>Esterilizado:</b>
			</td>
			<td>
				'.$datosPropietarioPaciente[0]['esterilizado'].'
			</td>

		</tr>
	</table>
</div>


<h1 style="text-align:center;"><u>Hospitalización</u></h1>

<div>
	<table>

		<thead>
			<tr>
				<th><b>Fecha y hora de ingreso</b></th>
				<th><b>Lugar</b></th>
				<th><b>Fecha y hora de salida</b></th>
				
			</tr>
		</thead>
		
		<tbody>
			<tr>
				<td>'.$datoshospitalizacion[0]['fechaIngreso'].' '.$datoshospitalizacion[0]['horaIngreso'].' </td>	
				<td>'.$datoshospitalizacion[0]['nombreSucursal'].' </td>	
				<td>'.$datoshospitalizacion[0]['fechaAlta'].' '.$datoshospitalizacion[0]['horaAlta'].' </td>	
				
			</tr>
		</tbody>
	
	</table>
</div>


<h3>Motivo de hospitalización:</h3>
	<div>
		'.$datoshospitalizacion[0]['motivoHospitalizacion'].' 
	</div>';


if($datoshospitalizacion[0]['observacion'] != ''){
	$html .= '<h3>Observaciones:</h3>
		<div>
			'.$datoshospitalizacion[0]['observacion'].' 
		</div>';	
}




//consultar las cirugias que se realizaron cirugias durante la hospitalizacion
$datosCirugiaHospitalizacion	= $objetoHospitalizacion->cirugiasHospitalizacion($idHospitalizacion);

if(sizeof($datosCirugiaHospitalizacion)>0){
	
	$html .='<h2 style="text-align:center;"><u>Intervenciones quirurgicas durante la hospitalización</u></h2>';
	
}

foreach ($datosCirugiaHospitalizacion as $datosCirugiaHospitalizacion1) {
			
	//se importa el modelo que corresponde
	require_once("Models/cirugias_model.php");	
	
	$objetoCirugias	= new cirugias();
	
	$datosCirugia 	= 	$objetoCirugias->listarUnaCirugia($datosCirugiaHospitalizacion1['idCirugia']);

	 //se consultan los diagnosticos de la cirugia
	 $diagnosticosCirugia	= $objetoCirugias->consultarDxsCirugia($datosCirugia[0]['idCirugia']);	
	 
	 $html .=' <h3>&nbsp;</h3>
<div style="text-align: center;">
	<table>
		<tr>
			<td>Fecha: </td>
			<td>'.$datosCirugia[0]['fecha'].'</td>
			<td>Médico: </td>
			<td>'.$datosCirugia[0]['nombreUsuario'].' '.$datosCirugia[0]['apellido'].'</td>
		</tr>
	</table>
</div>

	<table border=0>
	
		<tr>
			<td><b>Código</b></td>
			<td><b>Nombre</b></td>
			<td><b>Observación</b></td>					
		</tr>
		
	';
	foreach ($diagnosticosCirugia as $diagnosticosCirugia1) {
		
		$html	.='	<tr>
						<td>'.$diagnosticosCirugia1['codigo'].'</td>
						<td>'.$diagnosticosCirugia1['nombre'].'</td>
						<td>'.$diagnosticosCirugia1['observacionItemDX'].'</td>
					</tr>';		
		
	}//fin foreach
	
	
	$html	.='
	
	</table>';

	if($datosCirugia[0]['motivo'] != ''){
		$html	.='<h3>Motivo de la intervención:</h3>
			<div>
				'.$datosCirugia[0]['motivo'].' 
			</div>	';	
	}
	

	if($datosCirugia[0]['observaciones'] != ''){
		$html .= '<h3>Observaciónes:</h3>
			<div>
				'.$datosCirugia[0]['observaciones'].' 
			</div>';
	}

$html .= '
<h3>Anestesia</h3>
	<div>
		'.$datosCirugia[0]['tipoAnestesia'].' 
	</div>';
	
	if($datosCirugia[0]['complicaciones'] != ''){
		$html .= '<h3>Complicaciones:</h3>
			<div>
				'.$datosCirugia[0]['complicaciones'].' 
			</div>';
	}

	if($datosCirugia[0]['planASeguir'] != ''){
		$html .= '<h3>Plan y recomendaciones a seguir:</h3>
			<div>
				'.$datosCirugia[0]['planASeguir'].' 
			</div>';
	}
	 
	 
	$html .= '<hr>'; 
	 	
	
}//fin foreach si existen cirugias hospitalizacion




//consultar las cirugias que se realizaron cirugias durante la hospitalizacion
$datosConsultaHospitalizacion	= $objetoHospitalizacion->consultasHospitalizacion($idHospitalizacion);

if(sizeof($datosCirugiaHospitalizacion)>0){
	
	$html .='<h2 style="text-align:center;"><u>Consultas durante la hospitalización</u></h2>';
	
}

foreach ($datosConsultaHospitalizacion as $datosConsultaHospitalizacion1) {

 //se consulta la información de la consulta
 require_once("Models/consultas_model.php");
 
 $objetoConsulta	= new consultas(); 
 $datosConsulta	= $objetoConsulta->listarUnaConsulta($datosConsultaHospitalizacion1['idConsulta']);	
 
 //se consultan los diagnosticos de la consulta
 $diagnosticosConsulta	= $objetoConsulta->consultarDxsConsulta($datosConsulta[0]['idConsulta']); 
//se consulta los items del examen fisico
 $datosItemsExamenFisico	= $objetoConsulta->consultarItemsExamenFisico($datosConsulta[0]['idExamenFisico']);


$html .='<div style="text-align: center;">
	<table>
		<tr>
			<td>Fecha: </td>
			<td>'.$datosConsulta[0]['fecha'].'</td>
			<td>Médico: </td>
			<td>'.$datosConsulta[0]['nombreUsuario'].' '.$datosConsulta[0]['apellido'].'</td>
		</tr>
	</table>
</div>

	<h3>Diagnósticos</h3>
	<table border=0>
	
		<tr>
			<td><b>Código</b></td>
			<td><b>Nombre</b></td>
			<td><b>Observación</b></td>					
		</tr>
		
	';
	foreach ($diagnosticosConsulta as $diagnosticosConsulta1) {
		
		$html	.='	<tr>
						<td>'.$diagnosticosConsulta1['codigo'].'</td>
						<td>'.$diagnosticosConsulta1['nombre'].'</td>
						<td>'.$diagnosticosConsulta1['observacionItemDX'].'</td>
					</tr>';		
		
	}//fin foreach
	
	
	$html	.='
	
	</table>';

	if($datosConsulta[0]['motivo'] != ''){

		$html .= '<h3>Motivo de consulta:</h3>
		<div>
			'.$datosConsulta[0]['motivo'].' 
		</div>';
		
	}

	if($datosConsulta[0]['observaciones'] != ''){

		$html .= '<h3>Observación de consulta:</h3>
			<div>
				'.$datosConsulta[0]['observaciones'].' 
			</div>';
		
	}

	if($datosConsulta[0]['planASeguir'] != ''){
		$html .= '<h3>Plan a seguir:</h3>
			<div>
				'.$datosConsulta[0]['planASeguir'].' 
			</div>';			
	}	
	


if( ($datosConsulta[0]['peso'] != "") or ($datosConsulta[0]['medidaCm'] != "") or ($datosConsulta[0]['temperatura'] != "") or ($datosConsulta[0]['observacionesExamenFisico'] != "") or (sizeof($datosItemsExamenFisico) > 0) ){
	

$html	.='
	<h2 style="text-align:center;">Exámen fisico</h2>
	<table border=0>
		<tr>
			<td>
				<b>Peso:</b>
			</td>
			<td>
				'.$datosConsulta[0]['peso'].'
			</td>
			<td>
				<b>Altura:</b>
			</td>
			<td>
				'.$datosConsulta[0]['medidaCm'].'
			</td>
			<td>
				<b>Temperatura:</b>
			</td>
			<td>
				'.$datosConsulta[0]['temperatura'].'
			</td>
		</tr>
	</table>
	<br/>';
	
	if($datosConsulta[0]['observacionesExamenFisico'] != ''){

		$html .= '<h3>Observaciones exámen fisico</h3>
		<div>
			'.$datosConsulta[0]['observacionesExamenFisico'].' 
		</div>';
		
	}	
	


	if( (sizeof($datosItemsExamenFisico) > 0) ){		
	
			$html	.='<div>
				<table>
					<tr>
						<td>
							<b>Nombre</b>
						</td>
						<td width="300px">
							<b>Observación</b>
						</td>
						<td>
							<b>Estado</b>
						</td>
					</tr>
				';
				foreach ($datosItemsExamenFisico as $datosItemsExamenFisico1) {
					
					$html	.='	<tr>
									<td>
										'.$datosItemsExamenFisico1['nombre'].'
									</td>
									<td width="300px">
										'.$datosItemsExamenFisico1['observacion'].'
									</td>
									<td>
										'.$datosItemsExamenFisico1['estadoRevision'].'
									</td>
								</tr>';
					
				}//fin foreach			
				
				$html	.='	
				</table>
			</div>';

	}//fin if paea saber si existen items de examen fisico

}//fin if si tiene examen fisico

	$html .= '<hr>';

	
}//fin foreach de las consultas




//consultar las cirugias que se realizaron cirugias durante la hospitalizacion
$datosExamenHospitalizacion	= $objetoHospitalizacion->examenesHospitalizacion($idHospitalizacion);

if(sizeof($datosExamenHospitalizacion)>0){
	
	$html .='<h2 style="text-align:center;"><u>Examenes durante la hospitalización</u></h2>';
	
}


foreach ($datosExamenHospitalizacion as $datosExamenHospitalizacion1) {
	
 //se consulta la información del examen
 require_once("Models/examenes_model.php");
 
 $objetoExamen	= new examenes(); 
 $datosExamen	= $objetoExamen->listarUnExamenEncabezado($datosExamenHospitalizacion1['idExamen']);
 
 //se consultan los detalles del examen
 $detalleExamen	= $objetoExamen->consultarDetalleExamenEncabezado($datosExamen[0]['idExamen']);
 
 
 
$html .= '<div style="text-align: center;">
	<table>
		<tr>
			<td>Fecha: </td>
			<td>'.$datosExamen[0]['fecha'].'</td>
			<td>Médico: </td>
			<td>'.$datosExamen[0]['nombreUsuario'].' '.$datosExamen[0]['apellido'].'</td>
		</tr>
	</table>
</div>';


if($datosExamen[0]['observaciones'] != ''){

$html .= '<h3>Observaciones:</h3>
	<div>
		'.$datosExamen[0]['observaciones'].' 
	</div>';
	
}



$html .='<h3>Exámenes</h3>

	<div>
		
	';
	foreach ($detalleExamen as $detalleExamen1) {
		
		$resultadoEx = $objetoExamen->consultarResultadoExamen($detalleExamen1['idExamenDetalle']);
		
		$html	.='	
		
	<table border=0>
	
		<tr>
			<td><b>Código</b></td>
			<td><b>Nombre</b></td>
			<td><b>Observación</b></td>	
			<td></td>				
		</tr>		
		
		
					<tr>
						<td>'.$detalleExamen1['codigo'].'</td>
						<td>'.$detalleExamen1['nombre'].'</td>
						<td colspan="2">'.$detalleExamen1['observacion'].'</td>
						<td></td>
					</tr>';
		//si el item tiene resultado
	if(sizeof($resultadoEx) > 0){
		
		$html .= '
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td style="text-align: center;" colspan="4"><strong>Resultado</strong></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><b>Fecha</b></td>
					<td><b>observaciones</b></td>
					<td><b>En general</b></td>
					<td><b>Registrado por:</b></td>
				</tr>				
				<tr>
					<td>'.$resultadoEx[0]['fecha'].'</td>
					<td>'.$resultadoEx[0]['observaciones'].'</td>
					<td>'.$resultadoEx[0]['general'].'</td>
					<td>'.$resultadoEx[0]['nombreUsuario'].' '.$resultadoEx[0]['apellido'].'</td>
				</tr>
				
		';
		
	}else{
		$html .= '
		
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
		
				<tr>
					<td style="text-align: center;" colspan="4"><strong>Aún no se registra el resultado</strong></td>
				</tr>';
	}	
					
	
	$html .='
					
	</table>
	
	<br/>
	<hr/>
					';		
		
	}//fin foreach
 

 	$html .= '<hr/>';
	
}//fin foreach examenes hospitalizacion




//consultar las cirugias que se realizaron cirugias durante la hospitalizacion
$datosFormulaHospitalizacion	= $objetoHospitalizacion->formulasHospitalizacion($idHospitalizacion);

if(sizeof($datosFormulaHospitalizacion)>0){
	
	$html .='<h2 style="text-align:center;"><u>Formulas durante la hospitalización</u></h2>';
	
}


foreach ($datosFormulaHospitalizacion as $datosFormulaHospitalizacion1) {

 //se consulta la información de la formula
 require_once("Models/formulas_model.php");
 
 $objetoFormulas	= new formulas(); 
 $datosFormula	= $objetoFormulas->listarUnaFormula($datosFormulaHospitalizacion1['idFormula']);
 
 //se consultan los detalles de la formula
 $detalleFormula	= $objetoFormulas->consultarDetalleFormula($datosFormula[0]['idFormula']); 

 
$html .= '<div style="text-align: center;">
	<table>
		<tr>
			<td>Fecha: </td>
			<td>'.$datosFormula[0]['fecha'].'</td>
			<td>Médico: </td>
			<td>'.$datosFormula[0]['nombreUsuario'].' '.$datosFormula[0]['apellido'].'</td>
		</tr>
	</table>
</div>';

	if($datosFormula[0]['observaciones'] != ''){

		$html .= '<h3>Observaciones:</h3>
			<div>
				'.$datosFormula[0]['observaciones'].' 
			</div>';		
		
	}



	$html.= '<h3>Medicamentos</h3>

	<div>
		
	';
	foreach ($detalleFormula as $detalleFormula1) {
		
		
		
		$html	.='	
		
	<table border=0>
	
		<tr>
			<td><b>Nombre</b></td>
			<td><b>Cantidad</b></td>
			<td><b>Frecuencia</b></td>
			<td><b>Dosificación</b></td>
			<td style="text-align:center;"><b>Vía</b></td>
			<td><b>Observación</b></td>	
			<td></td>				
		</tr>		
		
		
					<tr>
						<td>'.$detalleFormula1['nombre'].'</td>
						<td>'.$detalleFormula1['cantidad'].'</td>
						<td>'.$detalleFormula1['frecuencia'].'</td>
						<td>'.$detalleFormula1['dosificacion'].'</td>
						<td  style="text-align:center;">'.$detalleFormula1['via'].'</td>
						<td>'.$detalleFormula1['observacion'].'</td>
						<td></td>
					</tr>';

					
	
	$html .='
					
	</table>
	
	<br/>
	<hr/>
					';		
		
	}//fin foreach 
 
 
	
}//fin foreach












// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');


//Close and output PDF document
$pdf->Output('NanuVet hospitalización '.$idHospitalizacion.'.pdf', 'I');

?>
