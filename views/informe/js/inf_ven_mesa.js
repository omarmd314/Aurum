$(function() {
    $('#informes').addClass("active");
	listar();
    
    $('#start').bootstrapMaterialDatePicker({
        format: 'DD-MM-YYYY',
        time: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });

    $('#end').bootstrapMaterialDatePicker({
        useCurrent: false,
        format: 'DD-MM-YYYY',
        time: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });

$('#start,#end,#filtro_mesa').change( function() {
        console.log('listar -> ');
        listar();
    });

    /* $('#start,#end,#filtro_presentacion').change( function() {
        listar();
    }); */

     /* BOTON DATATABLES */
     var org_buildButton = $.fn.DataTable.Buttons.prototype._buildButton;
     $.fn.DataTable.Buttons.prototype._buildButton = function(config, collectionButton) {
     var button = org_buildButton.apply(this, arguments);
     $(document).one('init.dt', function(e, settings, json) {
         if (config.container && $(config.container).length) {
            $(button.inserter[0]).detach().appendTo(config.container)
         }
     })    
     return button;
     }
     // ================================================
});

/* $('#start').change( function() {
    console.log('start');
    listar();
});

$('#end').change( function() {
    console.log('end');
    listar();
});
 */

$('#filtro_mesa').change( function() {
    console.log('mesa');
   // combMesa();
   // listar();
});

/* $('#filtro_categoria').change( function() {
    console.log('categoria');
    combPro();
    listar();
});
    
$('#filtro_producto').change( function() {
    console.log('producto');
    combPre();
    listar();
}); */

var mesa = function () {
    $.ajax({
        type: "POST",
        url: $('#url').val()+"informe/Mesa",
        data: {
            cod: 'mesa'
        },
        dataType: "json",
        success: function(data){
            
            $('#filtro_mesa').append('<optgroup>');
            $.each(data, function (index, value) {
                $('#filtro_mesa').append("<option value='" + value.id_mesa + "'>" + value.id_mesa + "</option>").selectpicker('refresh');            
            });
            $('#filtro_mesa').append('</optgroup>');
            $('#filtro_mesa').prop('disabled', false);
            $('#filtro_mesa').selectpicker('refresh');
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(errorThrown + ' ' + textStatus);
        } 
    });
}


var combMesa = function () {
    $('#filtro_mesa').find('option').remove();
    $('#filtro_mesa').append("<option value='%' active>Mostrar todo</option>").selectpicker('refresh');
    $.ajax({
        type: "POST",
        url: $('#url').val()+"informe/combMesa",
        data: {
            cod: $("#filtro_mesa").selectpicker('val')
        },
        dataType: "json",
        success: function(data){
            $('#filtro_mesa').append('<optgroup>');
            $.each(data, function (index, value) {
                $('#filtro_mesa').append("<option value='" + value.id_mesa + "'>" + value.id_mesa + "</option>").selectpicker('refresh');            
            });
            $('#filtro_mesa').append('</optgroup>');
            $('#filtro_mesa').prop('disabled', false);
            $('#filtro_mesa').selectpicker('refresh');
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(errorThrown + ' ' + textStatus);
        } 
    });
    
}

/* var combPro = function(){
    $('#filtro_producto').find('option').remove();
    $('#filtro_producto').append("<option value='%' active>Mostrar todo</option>").selectpicker('refresh');
    $.ajax({
        type: "POST",
        url: $('#url').val()+"informe/combPro",
        data: {
            cod: $("#filtro_categoria").selectpicker('val')
        },
        dataType: "json",
        success: function(data){
            $('#filtro_producto').append('<optgroup>');
            $.each(data, function (index, value) {
                $('#filtro_producto').append("<option value='" + value.id_prod + "'>" + value.nombre + "</option>").selectpicker('refresh');            
            });
            $('#filtro_producto').append('</optgroup>');
            $('#filtro_producto').prop('disabled', false);
            $('#filtro_producto').selectpicker('refresh');
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(errorThrown + ' ' + textStatus);
        } 
    });
} */

/* var combPre = function(){
    $('#filtro_presentacion').find('option').remove();
    $('#filtro_presentacion').append("<option value='%' active>Mostrar todo</option>").selectpicker('refresh');
    $.ajax({
        type: "POST",
        url: $('#url').val()+"informe/combPre",
        data: {
            cod: $("#filtro_producto").selectpicker('val')
        },
        dataType: "json",
        success: function(data){
            $('#filtro_presentacion').append('<optgroup>');
            $.each(data, function (index, value) {
                $('#filtro_presentacion').append("<option value='" + value.id_pres + "'>" + value.presentacion + "</option>").selectpicker('refresh');            
            });
            $('#filtro_presentacion').append('</optgroup>');
            $('#filtro_presentacion').prop('disabled', false);
            $('#filtro_presentacion').selectpicker('refresh');
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(errorThrown + ' ' + textStatus);
        } 
    });
} */

var listar = function(){

    var moneda = $("#moneda").val();
	ifecha = $("#start").val();
    ffecha = $("#end").val();

    id_mesa = $("#filtro_mesa").selectpicker('val');
    /* id_catg = $("#filtro_categoria").selectpicker('val');
    id_prod = $("#filtro_producto").selectpicker('val');
    id_pres = $("#filtro_presentacion").selectpicker('val'); */

	var	table =	$('#table')
	.DataTable({
        buttons: [
            {
                extend: 'excel', title: 'rep_ventas_aprobadas', text:'Excel', className: 'btn btn-circle btn-lg btn-success waves-effect waves-dark', text: '<i class="mdi mdi-file-excel display-6" style="line-height: 10px;"></i>', titleAttr: 'Descargar Excel',
                container: '#btn-excel'
            }
        ],
		"destroy": true,
        "responsive" : true,
		"dom": "tip",
		"bSort": true,
		"ajax":{
			"method": "POST",
			//"url": $('#url').val()+"informe/venta_prod_list",
			"url": $('#url').val()+"informe/venta_por_mesa_list",
			"data": {
                ifecha: ifecha,
                ffecha: ffecha,
                id_mesa: id_mesa,
               /*  id_catg: id_catg,
                id_prod: id_prod,
                id_pres: id_pres */
            }
		},
		"columns":[
            /*
            {"data":"fecha_venta","render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data).format('DD-MM-Y');
            }},
            */
			{"data":"categoria"},
            {"data":"prod_nom"},
            {"data":"pres"},
            {"data":"precio"},
            {"data":"cant"},
            {"data":"mesa"},
            {"data":"fecha"},
            /* {
                "data": "cantidad_total",
                "render": function ( data, type, row) {
                    return '<div class="text-right">'+data+'</div>';
                }
            },
			{
                "data": "precio",
                "render": function ( data, type, row) {
                    return '<div class="text-right"> '+moneda+' '+formatNumber(data)+'</div>';
                }
            },
			{
                "data": "total",
                "render": function ( data, type, row) {
                    return '<div class="text-right"> '+moneda+' '+formatNumber(data)+'</div>';
                }
            } */
		],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };

            /* cantidad = api
                .column( 6 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            total = api
                .column( 8 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            operaciones = api
                .rows()
                .data()
                .count(); */

            /* $('.productos-total').text(moneda+' '+formatNumber(total));
            $('.productos-operaciones').text(cantidad); */
        }
	});
}