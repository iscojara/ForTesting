<?php 
require "../config/Conexion.php";

class Consultas
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}
	//Implementar un método para listar los registros
	public function comprasfecha($fecha_inicio,$fecha_fin){
		$sql = "SELECT date(i.fecha_hora) as fecha,u.nombre as usuario,p.nombre as proveedor,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,i.total_compra,i.impuesto,i.estado FROM ingreso i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN usuario u ON i.idusuario=u.idusuario WHERE date(i.fecha_hora)>= '$fecha_inicio' AND date(i.fecha_hora)<= '$fecha_fin'" ;
		return ejecutarConsulta($sql);
	}
	//Implementar un método para listar los registros
	public function ventasfechacliente($fecha_inicio,$fecha_fin,$idcliente){
		$sql = "SELECT date(v.fecha_hora) as fecha,u.nombre as usuario,p.nombre as cliente,v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,v.total_venta,v.impuesto,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario WHERE date(v.fecha_hora)>= '$fecha_inicio' AND date(v.fecha_hora)<= '$fecha_fin' AND v.idcliente='$idcliente'" ;
		return ejecutarConsulta($sql);
	}
	public function totalcomprahoy(){
		$sql = "SELECT IFNULL(SUM(total_compra),0) AS total_compra FROM ingreso WHERE date(fecha_hora)=curdate()" ;
		return ejecutarConsulta($sql);
	}
	public function totalventahoy(){
		$sql = "SELECT IFNULL(SUM(total_venta),0) AS total_venta FROM venta WHERE date(fecha_hora)=curdate()" ;
		return ejecutarConsulta($sql);
	}
	public function comprasultimos_10dias(){
		$sql = "SELECT CONCAT(DAY(fecha_hora),'-',MONTH(fecha_hora)) as fecha,SUM(total_compra) as total FROM ingreso GROUP BY fecha_hora ORDER BY fecha_hora DESC limit 0,10";
		return ejecutarConsulta($sql);
	}
	public function ventasultimos_12meses(){
		$sql = "SELECT DATE_FORMAT(fecha_hora,'%M') as fecha,SUM(total_venta) as total FROM venta GROUP BY MONTH(fecha_hora) ORDER BY fecha_hora DESC limit 0,12";
		return ejecutarConsulta($sql);
	}
	public function grafico_circular(){
		$sql = "SELECT art.nombre,SUM(dv.cantidad) as total FROM articulo as art INNER join categoria as cat on art.idcategoria=cat.idcategoria inner JOIN detalle_venta as dv on dv.idarticulo=art.idarticulo GROUP BY art.idarticulo ORDER BY total desc LIMIT 5;";
		return ejecutarConsulta($sql);
	}
	public function circular_clientes(){
		$sql = "SELECT p.nombre,
		count(v.idcliente) as cantidad_compras
		 FROM venta as v
		INNER JOIN persona as p
		on v.idcliente=p.idpersona 
		GROUP BY idcliente
		ORDER by idcliente asc LIMIT 5;";
		return ejecutarConsulta($sql);
	}

}
?>