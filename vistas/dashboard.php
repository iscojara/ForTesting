<?php
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION['nombre'])) {
    header('Location: login.html');
} else {

    require 'header.php';

    if ($_SESSION['escritorio'] == 1) {
        require_once "../modelos/Consultas.php";
        $consulta = new Consultas();

        //primer grafico
        $clientes5 = $consulta->circular_clientes();
        $labels_clientes = '';
        $totales_clientes = '';
        while ($reg = $clientes5->fetch_object()) {
            $labels_clientes = $labels_clientes . '"' . $reg->nombre . '",';
            $totales_clientes = $totales_clientes . '"' . $reg->cantidad_compras . '",';
        }
        //Quitamos la ultima coma
        $totales_clientes = substr($totales_clientes, 0, -1);
        $labels_clientes = substr($labels_clientes, 0, -1);



        //Segundo grafico
        $ventas12 = $consulta->grafico_circular();
        $labels = '';
        $totalesv = '';
        while ($regfechav = $ventas12->fetch_object()) {
            $labels = $labels . '"' . $regfechav->nombre . '",';
            $totalesv = $totalesv . '"' . $regfechav->total . '",';
        }
        //Quitamos la ultima coma
        $labels = substr($labels, 0, -1);
        $totalesv = substr($totalesv, 0, -1);

?>
        <!--Contenido-->
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h1 class="box-title">Dashboard</h1>
                                <div class="box-tools pull-right">
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <!-- centro -->
                            <div class="panel-body">
                                <div class="panel-body">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="box box-primary">
                                            <div class="box-header with-border">
                                                Gráfico de Productos más vendidos
                                            </div>
                                            <div class="box-body">
                                                <canvas id="circular_productos" width="300" height="200"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="box box-primary">
                                            <div class="box-header with-border">
                                                Tabla de Productos más vendidos
                                            </div>
                                            <div class="panel-body table-responsive" id="listadoregistros">
                                                <table id="tblistado" class="table table-striped table-bordered table-condensed table-hover">
                                                    <thead>
                                                        <th>Codigo</th>
                                                        <th>Nombre</th>
                                                        <th>Categoria</th>
                                                        <th>Presentacion</th>
                                                        <th>Total</th>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                    <tfoot>
                                                        <th>Codigo</th>
                                                        <th>Nombre</th>
                                                        <th>Categoria</th>
                                                        <th>Presentacion</th>
                                                        <th>Total</th>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--Fin centro -->
                            </div><!-- /.box -->
                            <!-- /.box-header -->
                            <!-- centro -->
                            <div class="panel-body">
                                <div class="panel-body">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="box box-primary">
                                            <div class="box-header with-border">
                                                Gráfico de Clientes que más compran
                                            </div>
                                            <div class="box-body">
                                                <canvas id="circular_clientes" width="300" height="200"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="box box-primary">
                                            <div class="box-header with-border">
                                                Tabla de Clientes que más compran
                                            </div>
                                            <div class="panel-body table-responsive" id="listadoClientes">
                                                <table id="tblclientes" class="table table-striped table-bordered table-condensed table-hover">
                                                    <thead>
                                                        <th>Nombre</th>
                                                        <th>Telefono</th>
                                                        <th>Email</th>
                                                        <th>Cantidad Compras</th>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                    <tfoot>
                                                        <th>Nombre</th>
                                                        <th>Telefono</th>
                                                        <th>Email</th>
                                                        <th>Cantidad Compras</th>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--Fin centro -->
                            </div><!-- /.box -->
                        </div><!-- /.col -->
                    </div><!-- /.row -->
            </section><!-- /.content -->

        </div><!-- /.content-wrapper -->
        <!--Fin-Contenido-->
    <?php
    } else {
        require 'noacceso.php';
    }
    require 'footer.php';
    ?>
    <script type="text/javascript" src="../public/js/chart.min.js"></script>
    <script type="text/javascript" src="../public/js/Chart.bundle.min.js"></script>
    <script>
        listar();

        function listar() {
            tabla = $('#tblistado').dataTable({
                "aProcessing": true, //Activamos el procesamiento del datatables
                "aServerSide": true, // Paginación y filtrado realizados por el servidor
                dom: 'Bfrtip', //Definimos los elementos del control de tabla
                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdf'
                ],
                "ajax": {
                    url: '../ajax/articulo.php?op=productosVendidos',
                    type: "get",
                    dataType: 'json',
                    error: function(e) {
                        console.log(e.responseText);
                    }
                },
                "bDestroy": true,
                "iDisplayLength": 5, //paginación
                "order": [
                    
                ] // Ordenar (columna,orden)
            }).DataTable();
            tabla = $('#tblclientes').dataTable({
                "aProcessing": true, //Activamos el procesamiento del datatables
                "aServerSide": true, // Paginación y filtrado realizados por el servidor
                dom: 'Bfrtip', //Definimos los elementos del control de tabla
                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdf'
                ],
                "ajax": {
                    url: '../ajax/articulo.php?op=clientesCompras',
                    type: "get",
                    dataType: 'json',
                    error: function(e) {
                        console.log(e.responseText);
                    }
                },
                "bDestroy": true,
                "iDisplayLength": 5, //paginación
                "order": [
                    
                ] // Ordenar (columna,orden)
            }).DataTable();
        }
        // grafico de barras  -> .getContext('2d')
        var oilCanvas = document.getElementById("circular_productos");

        Chart.defaults.global.defaultFontFamily = "Lato";
        Chart.defaults.global.defaultFontSize = 18;

        var oilData = {
            labels: [
                <?php echo $labels; ?>
            ],
            datasets: [{
                data: [<?php echo $totalesv; ?>],
                backgroundColor: [
                    "#9c7cad",
                    "#f7c3d2",
                    "#fdfd95",
                    "#afc3d2",
                    "#7799cc",
                ]
            }]
        };

        var pieChart = new Chart(oilCanvas, {
            type: 'pie',
            data: oilData
        });
        // segundo grafico de barras
        var oilCanvas = document.getElementById("circular_clientes");

        Chart.defaults.global.defaultFontFamily = "Lato";
        Chart.defaults.global.defaultFontSize = 18;

        var oilData = {
            labels: [
                <?php echo $labels_clientes; ?>
            ],
            datasets: [{
                data: [<?php echo $totales_clientes; ?>],
                backgroundColor: [
                    "#ff9aa2",
                    "#c7ceea",
                    "#e2f0cc",
                    "#d9d9d9",
                    "#feb7b1",
                ]
            }]
        };

        var pieChart = new Chart(oilCanvas, {
            type: 'pie',
            data: oilData
        });
    </script>
<?php
}
ob_end_flush();
?>