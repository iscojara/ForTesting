<?php 
require_once "../modelos/Articulo.php";

$articulo = new Articulo();

$idarticulo = isset($_POST["idarticulo"])? limpiarCadena($_POST["idarticulo"]):"";
$idcategoria = isset($_POST["idcategoria"])? limpiarCadena($_POST["idcategoria"]):"";
$codigo= isset($_POST["codigo"])? limpiarCadena($_POST["codigo"]):"";
$nombre= isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$stock= isset($_POST["stock"])? limpiarCadena($_POST["stock"]):"";
$descripcion = isset($_POST["descripcion"])? limpiarCadena($_POST["descripcion"]):"";
$presentacion= isset($_POST["presentacion"])? limpiarCadena($_POST["presentacion"]):"";
$f_vencimiento = isset($_POST["f_vencimiento"])? limpiarCadena($_POST["f_vencimiento"]):"";
$imagen= isset($_POST["imagen"])? limpiarCadena($_POST["imagen"]):"";


switch ($_GET["op"]) {
	case 'guardaryeditar':
		// code...
		if(!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name']))
		{
			$imagen =$_POST["imagenactual"];
		}
		else{
			$ext = explode(".", $_FILES['imagen']['name']);
			if($_FILES['imagen']['type']== "image/jpg" || $_FILES['imagen']['type']== "image/png" || $_FILES['imagen']['type']== "image/jpeg")
			{
				$imagen = round(microtime(true)).'.'.end($ext);
				move_uploaded_file($_FILES['imagen']['tmp_name'], "../files/articulos/".$imagen);
			}
		}
		if(empty($idarticulo)){
			$rspta=$articulo->insertar($idcategoria,$codigo,$nombre,$stock,$descripcion,$presentacion,$f_vencimiento,$imagen);
			echo $rspta ? "Articulo registrado": "Articulo no se pudo registrar";
		}
		else{
			$rspta=$articulo->editar($idarticulo,$idcategoria,$codigo,$nombre,$stock,$descripcion,$presentacion,$f_vencimiento,$imagen);
			echo $rspta ? "Articulo actualizado": "Articulo no se pudo actualizar";
		}
	break;
	case 'desactivar':
		$rspta=$articulo->desactivar($idarticulo);
		echo $rspta ? "Articulo desactivado": "Articulo no se pudo desactivar";
	break;
	case 'activar':
		$rspta=$articulo->activar($idarticulo);
		echo $rspta ? "Articulo activado": "Articulo no se pudo activar";
	break;
	case 'mostrar':
		$rspta=$articulo->mostrar($idarticulo);
		//Codificar el resultado utilizando json
		echo json_encode($rspta);
	break;
	case 'listar':
		$rspta=$articulo->listar();
		//Vamos a declarar un array
		$data = Array();
		while ($reg=$rspta->fetch_object()) {
			// code...
			$data[] = array(
				"0"=>($reg-> condicion)?'<button class="btn btn-warning" onclick="mostrar('.$reg->idarticulo.')"><i class="fa fa-pencil"></i></button>'.
					' <button class="btn btn-danger" onclick="desactivar('.$reg->idarticulo.')"><i class="fa fa-close"></i></button>':
					'<button class="btn btn-warning" onclick="mostrar('.$reg->idarticulo.')"><i class="fa fa-pencil"></i></button>'.
					' <button class="btn btn-primary" onclick="activar('.$reg->idarticulo.')"><i class="fa fa-check"></i></button>',
				"1"=>$reg->nombre,
				"2"=>$reg->categoria,
				"3"=>$reg->fecha_vencimiento,
				"4"=>$reg->presentacion,
				"5"=>$reg->stock,
				"6"=>"<img src='../files/articulos/".$reg->imagen."' height='50px' width='50px' >",
				"7"=>($reg->condicion)? '<span class="label bg-green">Activado</span>':'<span class="label bg-red">Desactivado</span>'
			);

		}
		$results =array(
			"sEcho"=>1, // Informacion para el datatables
			"iTotalRecords"=>count($data),//enviamos el total de registros al datatable
			"iTotalDisplayRecords"=>count($data),// enviamos el total registros a visualizar
			"aaData"=>$data
		);
		echo json_encode($results);
	break;
	case "selectCategoria":
		require_once "../modelos/Categoria.php";
		$categoria = new Categoria();

		$rspta = $categoria->select();

		while($reg = $rspta->fetch_object()){
			echo '<option value='.$reg->idcategoria.'>'.$reg->nombre.'</option>';
		}
	break;
}

?>