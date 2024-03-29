<?php

/*
 * Archivo que se ocupa de la presentación inicial de los personalizados
 */

     if( isset($_SESSION['usuario_idUsuario']) and $_SESSION['usuario_idUsuario'] != '' ){ 
	
		//para evitar errores de cabecera se llama al controlador
		if(isset($_GET['id1'])){
			
			if(is_file("Controllers/personalizados/".$_GET['id1']."/".$_GET['id1']."Controller.php")){
				//se importa el controlador de apoyo para los personalizados
				require_once ("Controllers/personalizados/".$_GET['id1']."/".$_GET['id1']."Controller.php");
			}
			
						
			
		}

		

		//se importa el layout del menu
        require_once("Views/Layout/menu.phtml");
		
		//se importa el menu lateral
		require_once ("Views/Layout/menuPersonalizados.phtml");

		//si se elige una opción del menu lateral
		if(isset($_GET['id1'])){
			
			//saber si tiene permisos de acceder a los personalizados
			if(!in_array("1", $_SESSION['permisos_usuario'] )){
						
				require_once("Views/Layout/sinPermiso.phtml");	
				
			}else{
				
				if(is_file("Views/personalizados/".$_GET['id1']."/".$_GET['id1'].".phtml")){
					//se importa la vista
					require_once("Views/personalizados/".$_GET['id1']."/".$_GET['id1'].".phtml");
				}else{
					//si no existe el archivo se muestra el inicio de los personalizados
					require_once("Views/personalizados/personalizados.phtml");	
				}
				
			}
	
		}else{
					//saber si tiene permisos de acceder a los personalizados
					if(!in_array("1", $_SESSION['permisos_usuario'] )){
						
						require_once("Views/Layout/sinPermiso.phtml");	
						
					}else{
						//se importa la vista de los personalizados
						require_once("Views/personalizados/personalizados.phtml");	
					}
			
		} 
			
		
		//se importa el footer
        require_once("Views/Layout/footer.phtml");	


    }else{
        header('Location: '.Config::ruta());
		exit();
    }  
 
 ?>
