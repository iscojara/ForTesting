var tabla;

// funcion que se ejecuta al inicio
function init(){
	mostrarform(false);
	listar();

	$("#formulario").on("submit", function(e){
		guardaryeditar(e);
	});
	$.post("../ajax/ingreso.php?op=selectProveedor", function(e){
			$("#idproveedor").html(e);
			$("#idproveedor").selectpicker('refresh');
	});

}
//funcion limpiar
function limpiar()
{
	$("#idproveedor").val("");
	$("#proveedor").val("");
	$("#serie_comprobante").val("");
	$("#num_comprobante").val("");
	$("#impuesto").val("0");

	$("#total_compra").val("");
	$(".filas").remove();
	$("#total").html("0");

	//Obtenemos la fecha actual
	var now = new Date();
	var day = ("0"+(now.getDate())).slice(-2);
	var month = ("0"+(now.getMonth()+1)).slice(-2);
	var today = now.getFullYear()+"-"+(month)+"-"+(day);
	$("#fecha_hora").val(today);

	//Marcamos el primer documento
	$("#tipo_comprobante").val("Boleta");
	$("#tipo_comprobante").selectpicker('refresh');
}
//funcion mostrar formulario
function mostrarform(flag){
	limpiar();
	if(flag)
	{
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		//$("#btnGuardar").prop("disabled",false);
		$("#btnagregar").hide();
		listarArticulos();

		$("#btnGuardar").hide();
		$("#btnCancelar").show();
		detalles=0;
		$("#btnAgregarArt").show();
	}
	else
	{
		$("#listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").show();	
	}	
}

// Funcion cancelarForm
function cancelarform(){
	limpiar();
	mostrarform(false);
}
//Funcion Listar
function listar()
{
	tabla = $('#tblistado').dataTable({
		"aProcessing": true, //Activamos el procesamiento del datatables
		"aServerSide": true, // Paginación y filtrado realizados por el servidor
		dom: 'Bfrtip',//Definimos los elementos del control de tabla
		buttons:[
					'copyHtml5',
					'excelHtml5',
					'csvHtml5',
					'pdf'
				],
		"ajax":
				{
					url:'../ajax/ingreso.php?op=listar',
					type: "get",
					dataType: 'json',
					error: function(e){
						console.log(e.responseText);
					}
				},
		"bDestroy": true,
		"iDisplayLength": 5, //paginación
		"order" : [[0,"desc"]] // Ordenar (columna,orden)
	}).DataTable();
}
//Funcion Listar
function listarArticulos()
{
	tabla = $('#tblarticulos').dataTable({
		"aProcessing": true, //Activamos el procesamiento del datatables
		"aServerSide": true, // Paginación y filtrado realizados por el servidor
		dom: 'Bfrtip',//Definimos los elementos del control de tabla
		buttons:[
				],
		"ajax":
				{
					url:'../ajax/ingreso.php?op=listarArticulos',
					type: "get",
					dataType: 'json',
					error: function(e){
						console.log(e.responseText);
					}
				},
		"bDestroy": true,
		"iDisplayLength": 5, //paginación
		"order" : [[0,"desc"]] // Ordenar (columna,orden)
	}).DataTable();
}
// funcion para guardar y editar
function guardaryeditar(e)
{
	e.preventDefault();//No se activará la acción predeterminada
	//$("#btnGuardar").prop("disabled",true);
	var formData =  new FormData($("#formulario")[0]);

	$.ajax({
		url: "../ajax/ingreso.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function(datos)
		{
			bootbox.alert(datos);
			mostrarform(false);
			listar();
		}

	});
	limpiar();
}

function mostrar(idingreso)
{
	$.post("../ajax/ingreso.php?op=mostrar",{idingreso: idingreso}, function(data,status)
	{
		data = JSON.parse(data);
		mostrarform(true);

		$("#idproveedor").val(data.idproveedor);
		$("#idproveedor").selectpicker('refresh');
		$("#tipo_comprobante").val(data.tipo_comprobante);
		$("#tipo_comprobante").selectpicker('refresh');
		$("#serie_comprobante").val(data.serie_comprobante);
		$("#num_comprobante").val(data.num_comprobante);
		$("#fecha_hora").val(data.fecha);
		$("#impuesto").val(data.impuesto);
		$("#idingreso").val(data.idingreso);

		//Ocultar y mostrar los botones
		$("#btnGuardar").hide();
		$("#btnCancelar").show();
		$("#btnAgregarArt").hide();
		
	});
	$.post("../ajax/ingreso.php?op=listarDetalle&id="+idingreso, function(e){
			$("#detalles").html(e);
	});
}

// Funcion para desactivar registros
function anular(idingreso)
{
	bootbox.confirm("¿Está Seguro de desactivar el Ingreso?",function(result){
		if(result)
		{
			$.post("../ajax/ingreso.php?op=anular",{idingreso: idingreso}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}
//Declaracion de variables necesarias para trabajar con las compras y sus detalles
var impuesto=18;
var cont=0;
var detalles=0;
$("#btnGuardar").hide();
$("#tipo_comprobante").change(marcarImpuesto);

function marcarImpuesto(){
	var tipo_comprobante=$("#tipo_comprobante option:selected").text();
	if(tipo_comprobante=='Factura')
	{
		$("#impuesto").val(impuesto);
	}
	else
	{
		$("#impuesto").val("0");
	}
}
var articulos_agregados = [];
function verificarProducto(idarticulo) {
  var indice = articulos_agregados.indexOf(idarticulo);

  if (indice !== -1) {
    return 1;
  }
  articulos_agregados.push(idarticulo);
  return 0;
}

function agregarDetalle(idarticulo,articulo){
	var cantidad =1;
	var precio_compra =1;
	var precio_venta =1;
	if(idarticulo=="")
	{
		alert("Error al ingresar el detalle, revisar los datos");		
	}
	if(verificarProducto(idarticulo) != 0)
	{
		bootbox.alert("Producto ya agregado");		
	}
	else
	{
		var subtotal=cantidad*precio_compra;
		var fila='<tr class="filas" id="fila'+cont+'">'+
		'<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle('+cont+')">X</button></td>'+
		'<td><input type="hidden" name="idarticulo[]" value="'+idarticulo+'">'+articulo+'</td>'+
		'<td><input type="number" name="cantidad[]" id="cantidad[]" value="'+cantidad+'"></td>'+
		'<td><input type="number" name="precio_compra[]" id="precio_compra[]" value="'+precio_compra+'"></td>'+
		'<td><input type="number" name="precio_venta[]" id="precio_venta[]" value="'+precio_venta+'"></td>'+
		'<td><span name="subtotal" id="subtotal'+cont+'">'+subtotal+'</span></td>'+
		'<td><button type="button" onclick="modificarSubototales()" class="btn btn-info"><i class="fa fa-refresh"></i></button></td>'+
		'</tr>';
		cont ++;
		detalles=detalles+1;
		$('#detalles').append(fila);
		modificarSubototales();
	}
}
function modificarSubototales()
{
	var cant = document.getElementsByName("cantidad[]");
	var prec = document.getElementsByName("precio_compra[]");
	var sub = document.getElementsByName("subtotal");

	for (var i = 0; i < cant.length; i++) {
		var inpC =cant[i];	
		var inpP =prec[i];
		var inpS =sub[i];

		inpS.value=inpC.value * inpP.value;
		document.getElementsByName("subtotal")[i].innerHTML = inpS.value;
	}
	calcularTotales();
}
function calcularTotales(){
	var sub = document.getElementsByName("subtotal");
	var total = 0.0;
	for (var i = 0; i < sub.length; i++) {
		total +=document.getElementsByName("subtotal")[i].value;
	}
	$("#total").html("S/." + total);
	$("#total_compra").val(total);
	evaluar();
}
function evaluar(){
	if(detalles>0)
	{
		$("#btnGuardar").show();
	}
	else
	{
		$("#btnGuardar").hide();
		cont=0;
	}
}
function eliminarDetalle(indice){
	$("#fila"+indice).remove();
	calcularTotales();
	detalles=detalles-1;
}

init();