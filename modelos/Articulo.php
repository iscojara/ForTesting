<?php 
require "../config/Conexion.php";

class Articulo
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}
	//Implementamos el metodo para insertar registros
	public function insertar($idcategoria,$codigo,$nombre,$stock,$descripcion,$presentacion,$f_vencimiento,$imagen)
	{
		$sql = "INSERT INTO articulo(idcategoria,codigo,nombre,stock,descripcion,fecha_vencimiento,presentacion,imagen,condicion) VALUES ('$idcategoria','$codigo','$nombre','$stock','$descripcion','$f_vencimiento','$presentacion','$imagen','1')";
		return ejecutarConsulta($sql);
	}
	//Implementamos el metodo para editar registros
	public function editar($idarticulo,$idcategoria,$codigo,$nombre,$stock,$descripcion,$presentacion,$f_vencimiento,$imagen)
	{
		$sql = "UPDATE articulo SET idcategoria= '$idcategoria',codigo='$codigo',nombre='$nombre',stock='$stock',descripcion = '$descripcion',presentacion = '$presentacion',fecha_vencimiento = '$f_vencimiento',imagen='$imagen' WHERE idarticulo='$idarticulo'";
		return ejecutarConsulta($sql);
	}
	//Implementamos el metodo para desactivar registros
	public function desactivar($idarticulo)
	{
		$sql = "UPDATE articulo SET condicion= '0' WHERE idarticulo='$idarticulo'";
		return ejecutarConsulta($sql);
	}
	//Implementamos el metodo para activar registros
	public function activar($idarticulo)
	{
		$sql = "UPDATE articulo SET condicion= '1' WHERE idarticulo='$idarticulo'";
		return ejecutarConsulta($sql);
	}
	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idarticulo)
	{
		$sql="SELECT * FROM articulo WHERE idarticulo='$idarticulo'";
		return ejecutarConsultaSimpleFila($sql);
	}
	//Implementar un método para listar los registros
	public function listar(){
		$sql = "SELECT a.idarticulo,a.idcategoria,c.nombre as categoria,a.codigo,a.nombre,a.stock,a.descripcion,a.imagen,a.condicion,a.fecha_vencimiento,a.presentacion FROM articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria";
		return ejecutarConsulta($sql);
	}
	//Implementar un método para listar los registros activos
	public function listarActivos(){
		$sql = "SELECT a.idarticulo,a.idcategoria,c.nombre as categoria,a.codigo,a.nombre,a.stock,a.descripcion,a.imagen,a.condicion FROM articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria WHERE a.condicion='1'";
		return ejecutarConsulta($sql);
	}
	public function listarActivosVenta(){
		$sql = "SELECT a.idarticulo,a.idcategoria,c.nombre as categoria,a.codigo,a.nombre,
		a.stock,(SELECT precio_venta FROM detalle_ingreso WHERE idarticulo=a.idarticulo order by iddetalle_ingreso desc limit 0,1) as precio_venta,
		a.descripcion,a.imagen,a.condicion FROM articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria WHERE a.condicion='1'";
		return ejecutarConsulta($sql);
	}
	public function listarProductosVendidos(){
		$sql = "SELECT art.codigo,art.nombre,art.presentacion,cat.nombre as categoria,SUM(dv.cantidad) as total FROM articulo as art INNER join categoria as cat on art.idcategoria=cat.idcategoria inner JOIN detalle_venta as dv on dv.idarticulo=art.idarticulo GROUP BY art.idarticulo ORDER BY total desc;";
		return ejecutarConsulta($sql);
	}
	public function listarClientesCompras(){
		$sql = "SELECT p.nombre,p.telefono,p.email, count(v.idcliente) as cantidad_compras FROM venta as v INNER JOIN persona as p on v.idcliente=p.idpersona GROUP BY idcliente ORDER by idcliente asc;";
		return ejecutarConsulta($sql);
	}

}
?>