var tabla;

// funcion que se ejecuta al inicio
function init(){
	mostrarform(false);
	listar();

	$("#formulario").on("submit", function(e){
		guardaryeditar(e);
	})
	//Cargamos los items al select categoria
	$.post("../ajax/articulo.php?op=selectCategoria",function(r){
		$("#idcategoria").html(r);
		$("#idcategoria").selectpicker('refresh');
	});
	$("#imagenmuestra").hide();
}
//funcion limpiar
function limpiar()
{
	$("#codigo").val("");
	$("#nombre").val("");
	$("#descripcion").val("");
	$("#stock").val("");
	$("#presentacion").val("");
	// $("#f_vencimiento").val("");
	$("#imagenmuestra").attr("src","");
	$("#imagenactual").val("");
	$("#print").hide();
	$("#idarticulo").val("");
}
//funcion mostrar formulario
function mostrarform(flag){
	limpiar();
	if(flag)
	{
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		$("#btnGuardar").prop("disabled",false);
		$("#btnagregar").hide();
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
					url:'../ajax/articulo.php?op=listar',
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
	$("#btnGuardar").prop("disabled",true);
	var formData =  new FormData($("#formulario")[0]);

	$.ajax({
		url: "../ajax/articulo.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function(datos)
		{
			bootbox.alert(datos);
			mostrarform(false);
			tabla.ajax.reload();
		}

	});
	limpiar();
}

function mostrar(idarticulo)
{
	$.post("../ajax/articulo.php?op=mostrar",{idarticulo: idarticulo}, function(data,status)
	{
		data = JSON.parse(data);
		mostrarform(true);

		$("#idcategoria").val(data.idcategoria);
		$("#idcategoria").selectpicker('refresh');
		$("#codigo").val(data.codigo);
		$("#nombre").val(data.nombre);
		$("#stock").val(data.stock);
		$("#presentacion").val(data.presentacion);
		$("#f_vencimiento").val(data.fecha_vencimiento);
		$("#descripcion").val(data.descripcion);
		$("#imagenmuestra").show();
		$("#imagenmuestra").attr("src","../files/articulos/"+data.imagen);
		$("#imagenactual").val(data.imagen);
		$("#idarticulo").val(data.idarticulo);
		generarbarcode();
	})
}

// Funcion para desactivar registros
function desactivar(idarticulo)
{
	bootbox.confirm("¿Está Seguro de desactivar el Artculo?",function(result){
		if(result)
		{
			$.post("../ajax/articulo.php?op=desactivar",{idarticulo: idarticulo}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}
// Funcion para activar registros
function activar(idarticulo)
{
	bootbox.confirm("¿Está Seguro de activar el Articulo?",function(result){
		if(result)
		{
			$.post("../ajax/articulo.php?op=activar",{idarticulo: idarticulo}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}
//Funcion para generar codigo de barras
function generarbarcode(){
	codigo= $("#codigo").val();
	JsBarcode("#barcode",codigo);
	$("#print").show();
}
//Funcion para imprimir codigo de barras
function imprimir(){
	$("#print").printArea();
}

init();