<?php


/*
 * Archivo para guardar la factura de una cirugia (encabezado)
 */

 	if(!isset($_SESSION)){
		    session_start();
		} 
 
 //se reciben las variables
	$resolucionfacturador 		= $_POST['factura_resolucionfacturador'];
	$idFacturador 				= $_POST['factura_idFacturador'];
	$idResolucion 				= $_POST['factura_idResolucion'];
	$iva 						= $_POST['factura_iva'];
	$metodoPago 				= $_POST['factura_metodoPago'];
	$idPropietario 				= $_POST['factura_idPropietario'];
	$idCirugia				    = $_POST['factura_idCirugia'];
	$observaciones 				= $_POST['factura_observaciones'];
	$valorBruto 				= $_POST['factura_valorBruto'];
	$descuento 					= $_POST['factura_descuento'];
	$valorIva 					= $_POST['factura_valorIva'];
	$totalFactura 				= $_POST['factura_totalFactura'];
	
	$estado   				    = $_POST['factura_estado'];


 require_once("../../../Config/config.php");
 require_once("../../../Models/factura_model.php");
 
 $objetoFactura = new factura(); 
 
 
 $idUsuario	= $_SESSION['usuario_idUsuario'];
 $idSucursal = $_SESSION['sucursalActual_idSucursal'];
				
 //registrar el detalle del pago 
 $resultadoIdFactura =  $objetoFactura->guardarFacturaUnElemento($descuento, $iva, $valorIva, $valorBruto, $totalFactura, '0', $metodoPago, $observaciones, $estado, $idPropietario, $idFacturador, $idResolucion, $idUsuario, $idSucursal, $idCirugia, '1', 'Cirugia' );

	echo $resultadoIdFactura;		

?>