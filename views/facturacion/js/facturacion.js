$(function() {
    moment.locale('es');
    lisTab1();
    $('#start-1').bootstrapMaterialDatePicker({
        format: 'DD-MM-YYYY',
        time: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });
    $('#end-1').bootstrapMaterialDatePicker({
        useCurrent: false,
        format: 'DD-MM-YYYY',
        time: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });
    $('#start-2').bootstrapMaterialDatePicker({
        format: 'DD-MM-YYYY',
        time: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });
    $('#end-2').bootstrapMaterialDatePicker({
        useCurrent: false,
        format: 'DD-MM-YYYY',
        time: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });
    $('#start-3').bootstrapMaterialDatePicker({
        format: 'DD-MM-YYYY',
        time: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });
    $('#end-3').bootstrapMaterialDatePicker({
        useCurrent: false,
        format: 'DD-MM-YYYY',
        time: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });

    $('#start-4').bootstrapMaterialDatePicker({
        format: 'DD-MM-YYYY',
        time: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });
    $('#end-4').bootstrapMaterialDatePicker({
        useCurrent: false,
        format: 'DD-MM-YYYY',
        time: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });

    $('#start-5').bootstrapMaterialDatePicker({
        format: 'DD-MM-YYYY',
        time: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });
    $('#end-5').bootstrapMaterialDatePicker({
        useCurrent: false,
        format: 'DD-MM-YYYY',
        time: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });

    $('#start-7').bootstrapMaterialDatePicker({
        format: 'DD-MM-YYYY',
        time: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });
    $('#end-7').bootstrapMaterialDatePicker({
        useCurrent: false,
        format: 'DD-MM-YYYY',
        time: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });


    $('#fecha-rd').bootstrapMaterialDatePicker({
        useCurrent: false,
        format: 'DD-MM-YYYY',
        time: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });


    $('#fecha-rd').change( function() {
        lisTab5();
    });
    $('#start-1, #end-1, #tipo_doc, #est_doc').change( function() {
        lisTab1();
    });
    $('#start-2, #end-2').change( function() {
        lisTab2();
    });
    $('#start-3, #end-3').change( function() {
        lisTab3();
    });
    $('#start-4, #end-4').change( function() {
        lisTab4();
    });
    $('#start-5, #end-5').change( function() {
        lisTab5();
    });

    $('#start-7, #end-7').change( function() {
        lisTab7();
    });



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
});

var lisTab1 = function(){

    var moneda = $("#moneda").val();
    ifecha = $("#start-1").val();
    ffecha = $("#end-1").val();
    tdoc = $("#tipo_doc").selectpicker('val');
    estado = $("#est_doc").selectpicker('val');

    var total_env = 0,
        total_no_env = 0,
        total_anul = 0;

    $.ajax({
        type: "POST",
        url: $('#url').val()+"facturacion/Datos1",
        data: {
            ifecha: ifecha,
            ffecha: ffecha,
            tdoc: tdoc,
            estado: estado
        },
        dataType: "json",
        success: function(item){
            if (item.data.length != 0) {
                $.each(item.data, function(i, campo) {
                    if(campo.estado == 'a' && campo.enviado_sunat == '1'){
                        total_env++;
                    }else if(campo.estado == 'i'){
                        total_anul++;
                    } else {
                        total_no_env++;
                    }
                });
            }

            $('#total_enviados').text(total_env);
            $('#total_noenviados').text(total_no_env);
            $('#total_anulados').text(total_anul);
        }
    });

    var table = $('#table-1')
    .DataTable({
        buttons: [
            {
                extend: 'excel',
                title: 'rep_comprobantes_electronicos',
                text:'Excel',                
                className: 'btn btn-circle btn-lg btn-success waves-effect waves-dark',
                text: '<i class="mdi mdi-file-excel display-6" style="line-height: 10px;"></i>',
                titleAttr: 'Descargar Excel',
                container: '#btn-excel-01',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4 ]
                }
            }
        ],
        "destroy": true,
        "responsive": true,
        "dom": "tip",
        "bSort": true,
        "order": [[0,"desc"]],
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"facturacion/Datos1",
            "data": {
                ifecha: ifecha,
                ffecha: ffecha,
                tdoc: tdoc,
                estado: estado
            }
        },
        "columns":[
            {"data":null,"render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data.fec_ven).format('DD-MM-Y')
                        +'<br><i class="ti-time"></i> '+moment(data.fec_ven).format('h:mm A');
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<div>'+data.desc_td+'<br>'+data.ser_doc+'-'+data.nro_doc+'</div>';
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<div class="mayus">'+data.Cliente.dni+''+data.Cliente.ruc+'<br>'+data.Cliente.nombre+'</div>';
            }},
            {"data":null,"render": function ( data, type, row) {
                return '<div class="text-left bold m-b-0"> '+moneda+' '+formatNumber(data.total)+'</div>';
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.estado == 'a' && data.enviado_sunat == '1'){
                    return '<span class="label label-primary">ENVIADO A SUNAT</span></a>';
                }else if(data.estado == 'i'){
                    return '<span class="label label-danger">ANULADO</span></a></div>';
                } else {
                    return '<span class="label label-warning">SIN ENVIAR</span></a></div>';
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.hash_cpe != ''){
                    return '<center><a href="'+$("#entorno").val()+'/'+data.name_file_sunat+'.XML" target="_blank"><img src="public/images/xml_cpe.svg" style="max-width: 30px;"/></a></center>';
                } else{
                    return '<center>-</center>';
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.hash_cdr != ''){
                    return '<center><a href="'+$("#entorno").val()+'/R-'+data.name_file_sunat+'.XML" target="_blank"><img src="public/images/xml_cdr.svg" style="max-width: 30px;"/></a></center>';
                } else {
                    return '<center>-</center>';
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<center><a href="'+$("#url").val()+'informe/venta_all_imp/'+data.id_ven+'" target="_blank"><img src="public/images/pdf.svg" style="max-width: 30px;"/></a></center>';
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<center><a href="#" onclick="send_mail('+data.id_ven+',\''+$("#entorno").val()+'/'+data.name_file_sunat+'.XML\');"><img src="public/images/email.svg" style="max-width: 30px;"/></a></center>';
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.enviado_sunat == '1'){
                    var clase = 'ti-check text-success';
                    var opcion = '';
                    var opcion2 = '';
                    if(data.estado == 'a' && data.id_tdoc == 2){ //  MODIFICAR ESTO PARA NOTA DE CREDITO =============
                        
                        var tipo_doc = '1';
                        var nro_doc = `${data.ser_doc}-${data.nro_doc}`;
                        var razon_social = data.Cliente.nombre;
                        var direccion =  data.Cliente.direccion;
                        var ruc =  data.Cliente.ruc;
                        var id_venta = id_venta;
                        var params = ` '${data.id_ven}','${tipo_doc}', '${nro_doc}','${ruc}', '${razon_social}', '${direccion}' `;


                       var opcion = `<div class="dropdown-divider"></div><button  type="button" class="btn btn-secondary btn-block" onclick="mostrar_mdl_factura(${params});"><i class="mdi mdi-note-text text-success"></i> Nota de credito</button>`+
                                     '<div class="dropdown-divider"></div><button type="button" class="btn btn-secondary btn-block" onclick="ComunicacionBaja(1,'+data.id_ven+',\''+data.ser_doc+'-'+data.nro_doc+'\');"><i class="mdi mdi-close-circle text-danger"></i> Anular comprobante</button>';
                    } else if(data.estado == 'a' && data.id_tdoc == 1) {
                        var tipo_doc = '3', nro_doc = '\''+data.ser_doc + '-' + data.nro_doc+'\'';
                        var dni_cli = '\''+data.Cliente.dni+'\'', nombre_cli = '\''+data.Cliente.nombre+'\'';
                        var id_venta = data.id_ven;
                        var opcion = '<div class="dropdown-divider"></div><button type="button" class="btn btn-secondary btn-block" onclick="mostrar_mdl_boleta('+id_venta+','+tipo_doc+','+ nro_doc +','+dni_cli+','+nombre_cli+');"><i class="mdi mdi-note-text text-success"></i> Nota de credito</button>'+
                                     '<div class="dropdown-divider"></div><button type="button" class="btn btn-secondary btn-block" onclick="ComunicacionBaja(3,'+data.id_ven+',\''+data.ser_doc+'-'+data.nro_doc+'\');"><i class="mdi mdi-close-circle text-danger"></i> Anular comprobante</button>';
                                        // NOTA DE CREDITO
                                            }
                }else{
                    var clase = 'ti-close text-danger';
                    var opcion = '<div class="dropdown-divider"></div><button type="button" class="btn btn-secondary btn-block" onclick="invoice('+data.id_ven+',\''+data.ser_doc+'-'+data.nro_doc+'\');"><i class="mdi mdi-check-circle text-success"></i> Enviar comprobante</button>';
                    if($('#cod_rol_usu').val() == 1 || $('#cod_rol_usu').val() == 2){
                        var opcion2 = '<button type="button" class="btn btn-warning btn-block" onclick="reenvio('+data.id_ven+',\''+data.ser_doc+'-'+data.nro_doc+'\');">Enviado anteriormente</button>'
                    }else{
                        var opcion2 = '';
                    }
                }
                return '<div class="text-center"><div class="btn-group">'
                +'<button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
                +'<i class="'+clase+'" style="font-height: bold; font-size: 20px;"></i></button>'
                    +'<div class="dropdown-menu dropdown-menu-right p-4" style="width: 300px; font-size: 14px">'
                        +'<h6 class="dropdown-header text-center p-0">'+data.desc_td+'</h6>'
                        +'<h4 class="dropdown-header text-center p-t-0 p-b-20">'+data.ser_doc+'-'+data.nro_doc+'</h4>'
                        +'<p class="m-b-2 text-left">Código Respuesta: <span class="label label-primary">'+data.code_respuesta_sunat+'</span></p>'
                        +'<p class="m-b-0">Descripción: <span class="text-muted">'+data.descripcion_sunat_cdr+'</span></p>'
                        +opcion
                        +opcion2
                    +'</div>'
                +'</div></div>';
            }}
        ]
    });
};

var lisTab2 = function(){

    ifecha = $("#start-2").val();
    ffecha = $("#end-2").val();

    var table = $('#table-2')
    .DataTable({
        buttons: [
            {
                extend: 'excel',
                title: 'rep_baja_facturas',
                text:'Excel',                
                className: 'btn btn-circle btn-lg btn-success waves-effect waves-dark',
                text: '<i class="mdi mdi-file-excel display-6" style="line-height: 10px;"></i>',
                titleAttr: 'Descargar Excel',
                container: '#btn-excel-02',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4 ]
                }
            }
        ],
        "destroy": true,
        "responsive": true,
        "dom": "tip",
        "bSort": true,
        "order": [[0,"desc"]],
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"facturacion/Datos2",
            "data": {
                ifecha: ifecha,
                ffecha: ffecha
            }
        },
        "columns":[
            {"data":null,"render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data.fecha_baja).format('DD-MM-Y');
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<i class="mdi mdi-pound"></i> '+data.correlativo;
            }},
            {"data":null,"render": function ( data, type, row ) {
                return data.serie_doc+'-'+data.num_doc;
            }},
            {"data": "nombre_baja"},
            {"data":null,"render": function ( data, type, row ) {
                if(data.estado == 'a' && data.enviado_sunat == '1'){
                    return '<span class="label label-primary">ENVIADO A SUNAT</span></a>';
                }else if(data.estado == 'i'){
                    return '<span class="label label-danger">ANULADO</span></a></div>';
                } else {
                    return '<span class="label label-warning">SIN ENVIAR</span></a></div>';
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.hash_cpe != ''){
                    return '<center><a href="'+$("#entorno").val()+'/'+data.name_file_sunat+'.XML" target="_blank"><img src="public/images/xml_cpe.svg" style="max-width: 30px;"/></a></center>';
                } else{
                    return '<center>-</center>';
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.hash_cdr != ''){
                    return '<center><a href="'+$("#entorno").val()+'/R-'+data.name_file_sunat+'.XML" target="_blank"><img src="public/images/xml_cdr.svg" style="max-width: 30px;"/></a></center>';
                } else {
                    return '<center>-</center>';
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<div class="text-center"><div class="btn-group">'
                +'<button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
                +'<i class="ti-check text-success" style="font-height: bold; font-size: 20px;"></i></button>'
                    +'<div class="dropdown-menu dropdown-menu-right p-4" style="width: 300px; font-size: 14px">'
                        +'<h6 class="dropdown-header text-center p-0">COMUNICACION DE BAJA</h6>'
                        +'<h4 class="dropdown-header text-center p-l-10 p-b-20">'+data.serie_doc+'-'+data.num_doc+'</h4>'
                        +'<p class="m-b-2 text-left">Código Respuesta: <span class="label label-primary">'+data.code_respuesta_sunat+'</span></p>'
                        +'<p class="m-b-0">Descripción: <span class="text-muted">'+data.descripcion_sunat_cdr+'</span></p>'
                    +'</div>'
                +'</div></div>';
            }}
        ]
    });
};

var lisTab3 = function(){

    ifecha = $("#start-3").val();
    ffecha = $("#end-3").val();

    var table = $('#table-3')
    .DataTable({
        buttons: [
            {
                extend: 'excel',
                title: 'rep_baja_boletas',
                text:'Excel',                
                className: 'btn btn-circle btn-lg btn-success waves-effect waves-dark',
                text: '<i class="mdi mdi-file-excel display-6" style="line-height: 10px;"></i>',
                titleAttr: 'Descargar Excel',
                container: '#btn-excel-03',
                exportOptions: {
                    columns: [ 0, 1, 2, 3 ]
                }
            }
        ],
        "destroy": true,
        "responsive": true,
        "dom": "tip",
        "bSort": true,
        "order": [[0,"desc"]],
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"facturacion/Datos3",
            "data": {
                ifecha: ifecha,
                ffecha: ffecha
            }
        },
        "columns":[
            {"data":null,"render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data.fecha_baja).format('DD-MM-Y');
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<i class="mdi mdi-pound"></i> '+data.correlativo;
            }},
            {"data":null,"render": function ( data, type, row ) {
                return data.serie_doc+'-'+data.num_doc;
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.estado == 'a' && data.enviado_sunat == '1'){
                    return '<span class="label label-primary">ENVIADO A SUNAT</span></a>';
                }else if(data.estado == 'i'){
                    return '<span class="label label-danger">ANULADO</span></a></div>';
                } else {
                    return '<span class="label label-warning">SIN ENVIAR</span></a></div>';
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.hash_cpe != ''){
                    return '<center><a href="'+$("#entorno").val()+'/'+data.name_file_sunat+'.XML" target="_blank"><img src="public/images/xml_cpe.svg" style="max-width: 30px;"/></a></center>';
                } else{
                    return '<center>-</center>';
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.hash_cdr != ''){
                    return '<center><a href="'+$("#entorno").val()+'/R-'+data.name_file_sunat+'.XML" target="_blank"><img src="public/images/xml_cdr.svg" style="max-width: 30px;"/></a></center>';
                } else {
                    return '<center>-</center>';
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<div class="text-center"><div class="btn-group">'
                +'<button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
                +'<i class="ti-check text-success" style="font-height: bold; font-size: 20px;"></i></button>'
                    +'<div class="dropdown-menu dropdown-menu-right p-4" style="width: 300px; font-size: 14px">'
                        +'<h6 class="dropdown-header text-center p-0">COMUNICACION DE BAJA</h6>'
                        +'<h4 class="dropdown-header text-center p-l-10 p-b-20">'+data.serie_doc+'-'+data.num_doc+'</h4>'
                        +'<p class="m-b-2 text-left">Código Respuesta: <span class="label label-primary">'+data.code_respuesta_sunat+'</span></p>'
                        +'<p class="m-b-0">Descripción: <span class="text-muted">'+data.descripcion_sunat_cdr+'</span></p>'
                    +'</div>'
                +'</div></div>';
            }}
        ]
    });
};


var lisTab7 = function(){

    

    ifecha = $("#start-7").val();
    ffecha = $("#end-7").val();

    var table = $('#table-7').DataTable({
        buttons: [
            {
                extend: 'excel',
                title: 'rep_nota_credito',
                text:'Excel',                
                className: 'btn btn-circle btn-lg btn-success waves-effect waves-dark',
                text: '<i class="mdi mdi-file-excel display-6" style="line-height: 10px;"></i>',
                titleAttr: 'Descargar Excel',
                container: '#btn-excel-02',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4 ]
                }
            }
        ],
        "destroy": true,
        "responsive": true,
        "dom": "tip",
        "bSort": true,
        "order": [[0,"desc"]],
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"facturacion/Datos6",
            "data": {
                ifecha: ifecha,
                ffecha: ffecha
            }
        },
        "columns":[
            {"data":null,"render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data.fecha_nota).format('DD-MM-Y');
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<i class="mdi mdi-pound"></i> '+ ((data.tipo_doc === '01')? 'FC01-':'BC01-') + data.correlativo;
            }},
            {"data":null,"render": function ( data, type, row ) {
                return data.serie_doc+'-'+data.num_doc;
            }},
            {"data": "desc_nota"},
            {"data":null,"render": function ( data, type, row ) {
                if(data.estado == 'a' && data.enviado_sunat == '1'){
                    return '<span class="label label-primary">ENVIADO A SUNAT</span></a>';
                }else if(data.estado == 'i'){
                    return '<span class="label label-danger">ANULADO</span></a></div>';
                } else {
                    return '<span class="label label-warning">SIN ENVIAR</span></a></div>';
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.hash_cpe != ''){
                    return '<center><a href="'+$("#entorno").val()+'/'+data.name_file_sunat+'.XML" target="_blank"><img src="public/images/xml_cpe.svg" style="max-width: 30px;"/></a></center>';
                } else{
                    return '<center>-</center>';
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.hash_cdr != ''){
                    return '<center><a href="'+$("#entorno").val()+'/R-'+data.name_file_sunat+'.XML" target="_blank"><img src="public/images/xml_cdr.svg" style="max-width: 30px;"/></a></center>';
                } else {
                    return '<center>-</center>';
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<div class="text-center"><div class="btn-group">'
                +'<button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
                +'<i class="ti-check text-success" style="font-height: bold; font-size: 20px;"></i></button>'
                    +'<div class="dropdown-menu dropdown-menu-right p-4" style="width: 300px; font-size: 14px">'
                        +'<h6 class="dropdown-header text-center p-0">NOTA DE CREDITO</h6>'
                        +'<h4 class="dropdown-header text-center p-l-10 p-b-20">'+(((data.tipo_doc === '01')? 'FC01-':'BC01-') + data.correlativo) +'</h4>'
                        +'<p class="m-b-2 text-left">Código Respuesta: <span class="label label-primary">'+data.code_respuesta_sunat+'</span></p>'
                        +'<p class="m-b-0">Descripción: <span class="text-muted">'+data.descripcion_sunat_cdr+'</span></p>'
                    +'</div>'
                +'</div></div>';
            }}
        ]
    });
  };



var lisTab4 = function(){
    
    ifecha = $("#start-4").val();
    ffecha = $("#end-4").val();
    
    var table = $('#table-4')
    .DataTable({
        buttons: [
            {
                extend: 'excel',
                title: 'rep_resumen_boletas',
                text:'Excel',                
                className: 'btn btn-circle btn-lg btn-success waves-effect waves-dark',
                text: '<i class="mdi mdi-file-excel display-6" style="line-height: 10px;"></i>',
                titleAttr: 'Descargar Excel',
                container: '#btn-excel-04',
                exportOptions: {
                    columns: [ 0, 1, 3 ]
                }
            }
        ],
        "destroy": true,
        "responsive": true,
        "dom": "tip",
        "bSort": true,
        "order": [[0,"desc"]],
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"facturacion/Datos4",
            "data": {
                ifecha: ifecha,
                ffecha: ffecha
            }
        },
        "columns":[
            {"data":null,"render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data.fecha_resumen).format('DD-MM-Y');
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data.fecha_referencia).format('DD-MM-Y');
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<button class="btn btn-info btn-sm" onclick="lisTab6('+data.id_resumen+');"><i class="ti-eye"></i> Ver Boletas</button>';
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.estado == 'a' && data.enviado_sunat == '1'){
                    return '<span class="label label-primary">ENVIADO A SUNAT</span></a>';
                }else if(data.estado == 'i'){
                    return '<span class="label label-danger">ANULADO</span></a></div>';
                } else {
                    return '<span class="label label-warning">SIN ENVIAR</span></a></div>';
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.hash_cpe != null && data.enviado_sunat == '1'){
                    return '<center><a href="'+$("#entorno").val()+'/'+data.name_file_sunat+'.XML" target="_blank"><img src="public/images/xml_cpe.svg" style="max-width: 30px;"/></a></center>';
                } else {
                    return '<center>-</center>';
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.hash_cdr != null){
                    return '<center><a href="'+$("#entorno").val()+'/R-'+data.name_file_sunat+'.XML" target="_blank"><img src="public/images/xml_cdr.svg" style="max-width: 30px;"/></a></center>';
                } else {
                    return '<center>-</center>';
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.code_respuesta_sunat=='0'){
                    var clase = 'ti-check text-success';
                    var opcion = '';
                }else{
                    var clase = 'ti-close text-danger';
                    var opcion = '<div class="dropdown-divider"></div><button type="button" class="btn btn-secondary btn-block" onclick="resumen_boletas(\''+data.fecha_referencia+'\');"><i class="mdi mdi-check-circle text-success"></i> Enviar resumen diario</button>';
                }
                return '<div class="text-center"><div class="btn-group">'
                +'<button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
                +'<i class="'+clase+'" style="font-height: bold; font-size: 20px;"></i></button>'
                +'<div class="dropdown-menu dropdown-menu-right p-4" style="width: 300px; font-size: 14px">'
                +'<h6 class="dropdown-header text-center p-0">RESUMEN DIARIO</h6><br>'
                //+'<h4 class="dropdown-header text-center p-l-10 p-b-20">'+data.nombre_resumen+'</h4>'
                //+'<p class="m-b-2 text-left">Ticket: <span class="label label-primary">'+data.nro_ticket+'</span></p>'
                +'<p class="m-b-2 text-left">Código Respuesta: <span class="label label-primary">'+data.code_respuesta_sunat+'</span></p>'
                +'<p class="m-b-0">Descripción: <span class="text-muted">'+data.descripcion_sunat_cdr+'</span></p>'
                +opcion
                +'</div>'
                +'</div></div>';
            }}
        ]
    });
};

var lisTab5 = function(){
    
    var moneda = $("#moneda").val();
    ifecha = $("#fecha-rd").val();
    
    var table = $('#table-5')
    .DataTable({
        "destroy": true,
        "responsive": true,
        "dom": "tip",
        "bSort": true,
        "order": [[0,"desc"]],
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"facturacion/Datos5",
            "data": {
                ifecha: ifecha
            }
        },
        "columns":[
            {"data":null,"render": function ( data, type, row ) {
                return moment(data.fec_ven).format('DD-MM-Y');
            }},
            {"data": "desc_td"},
            {"data": "ser_doc"},
            {"data": "nro_doc"},
            {"data":null,"render": function ( data, type, row ) {
                console.log(data);
                return '<div class="text-left bold m-b-0"> '+moneda+' '+formatNumber(data.total)+'</div>';
            }}
        ]
    });
};

var lisTab6 = function(cod){

    var moneda = $("#moneda").val();
    $('#mdl-detalle').modal('show');

    var table = $('#table-6')
    .DataTable({
        "destroy": true,
        "responsive": true,
        "dom": "tip",
        "bSort": true,
        "order": [[0,"desc"]],
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"facturacion/Detalle",
            "data": {
                cod: cod
            }
        },
        "columns":[
            {"data":null,"render": function ( data, type, row ) {
                return moment(data.fec_ven).format('DD-MM-Y');
            }},
            {"data": "desc_td"},
            {"data":null,"render": function ( data, type, row ) {
                return data.ser_doc+'-'+data.nro_doc;
            }},
            {"data":null,"render": function ( data, type, row ) {
                return data.Cliente.dni+' - '+data.Cliente.nombre;
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<div class="text-right bold m-b-0"> '+moneda+' '+formatNumber(data.total)+'</div>';
            }}
        ]
    });
};

var invoice = function(cod_ven,doc){
    var text1 = '¿Realmente deseas enviar el documento  N°: '+doc+' a SUNAT? <br>Lo enviaremos inmediatamente!..';
    Swal.fire({
        title: 'Debes Confirmar!',
        html: text1,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#34d16e',
        confirmButtonText: 'Si, Adelante!',
        cancelButtonText: "No!",
        showLoaderOnConfirm: true,
        preConfirm: function() {
          return new Promise(function(resolve) {
             $.ajax({
                url: $('#url').val()+'facturacion/Invoice',
                type: 'POST',
                data: {cod_ven: cod_ven},
                dataType: 'json'
             })
             .done(function(response){
                if(response['enviado_sunat'] == '1'){
                    Swal.fire({
                        title: 'Proceso Terminado',
                        text: response['mensaje'],
                        icon: 'success',
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "OK"
                    });
                }else{
                    Swal.fire({
                        title: 'Proceso No Culminado',
                        text: response['mensaje'],
                        icon: 'error',
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "OK"
                    });
                }
                lisTab1();
             })
             .fail(function(){
                Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
             });
          });
        },
        allowOutsideClick: false              
    });
    contadorSunatSinEnviar();
}

var reenvio = function(cod_ven,doc){
    var text1 = 'El documento  N°: '+doc+' ya ha sido enviado anteriormente a SUNAT <br>Actualizaremos los datos en el sistema!..';
    //var text2 = 'El documento N°: '+doc+' fué enviado correctamente a SUNAT.';
    Swal.fire({
        title: 'Debes Confirmar!',
        html: text1,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#34d16e',
        confirmButtonText: 'Si, Adelante!',
        cancelButtonText: "No!",
        showLoaderOnConfirm: true,
        preConfirm: function() {
          return new Promise(function(resolve) {
             $.ajax({
                url: $('#url').val()+'facturacion/reenvio',
                type: 'POST',
                data: {cod_ven: cod_ven},
                dataType: 'json'
             })
             .done(function(response){
                Swal.fire({
                    title: 'Proceso Terminado',
                    text: 'El documento  N°: '+doc+' a sido actualizado',
                    icon: 'success',
                    confirmButtonColor: "#34d16e",   
                    confirmButtonText: "OK"
                })
                lisTab1();
             })
             .fail(function(){
                Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
             });
          });
        },
        allowOutsideClick: false              
    });
    contadorSunatSinEnviar();
}

var ComunicacionBaja = function(tipo_doc,cod_ven,doc){
    var text1 = '¿Realmente deseas dar de baja al documento  N°: '+doc+' a SUNAT? <br>Lo enviaremos inmediatamente!..';
    //var text2 = 'El documento N°: '+doc+' fué enviado correctamente a SUNAT.';
    Swal.fire({
        title: 'Debes Confirmar!',
        html: text1,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#34d16e',
        confirmButtonText: 'Si, Adelante!',
        cancelButtonText: "No!",
        showLoaderOnConfirm: true,
        preConfirm: function() {
          return new Promise(function(resolve) {
             $.ajax({
                url: $('#url').val()+'facturacion/ComunicacionBaja',
                type: 'POST',
                data: {
                    cod_ven: cod_ven,
                    tipo_doc : tipo_doc
                },
                dataType: 'json'
             })
             .done(function(response){
                if(response['enviado_sunat'] == '1'){
                    Swal.fire({
                        title: 'Proceso Terminado',
                        text: response['mensaje'],
                        icon: 'success',
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "OK"
                    });
                }else{
                    Swal.fire({
                        title: 'Proceso No Culminado',
                        text: response['mensaje'],
                        icon: 'error',
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "OK"
                    });
                }
                lisTab1();
             })
             .fail(function(){
                Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
             });
          });
        },
        allowOutsideClick: false              
    });

}

var resumen_boletas = function(fecha){
    Swal.fire({
        title: 'Necesitamos de tu Confirmación',
        html: 'Está seguro de crear el resumen?<br>Los cambios no se podrán revertir!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#34d16e',
        confirmButtonText: 'Si, Adelante!',
        cancelButtonText: "No!",
        showLoaderOnConfirm: true,
        preConfirm: function() {
          return new Promise(function(resolve) {
             $.ajax({
                url: $('#url').val()+'facturacion/Resumen_boletas',
                type: 'POST',
                data: {fecha: fecha},
                dataType: 'json'
             })
             .done(function(response){
                if(response['enviado_sunat'] == '1'){
                    Swal.fire({
                        title: 'Proceso Terminado',
                        text: response['mensaje'],
                        icon: 'success',
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "OK"
                    });
                }else{
                    Swal.fire({
                        title: 'Proceso No Culminado',
                        text: response['mensaje'],
                        icon: 'error',
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "OK"
                    });
                }
                lisTab4();
                lisTab5();
             })
             .fail(function(){
                Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
             });
          });
        },
        allowOutsideClick: false              
    });
}

var send_mail = function(id_venta,documento_cliente){
    var html_confirm = '<div>Se procederá a enviar el siguiente documento:</div><br>\
    Ingrese correo electronico del cliente</div><br>\
    <form><input class="form-control text-center w-100" type="text" id="correo_cliente" autocomplete="off"/></form><br>\
    <div><span class="text-success" style="font-size: 17px;">¿Está Usted de Acuerdo?</span></div>';
    Swal.fire({
        title: 'Debes Confirmar!',
        html: html_confirm,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#34d16e',
        confirmButtonText: 'Si, Adelante!',
        cancelButtonText: "No!",
        showLoaderOnConfirm: true,
        preConfirm: function() {
          return new Promise(function(resolve) {
             $.ajax({
                url: $('#url').val()+'facturacion/send_mailer',
                type: 'POST',
                data: {
                    correo_cliente : $('#correo_cliente').val(),
                    documento_cliente : documento_cliente,
                    id_venta : id_venta
                },
                dataType: 'json'
             })
             .done(function(response){
                if(response == '1'){
                    Swal.fire({
                        title: 'Proceso Terminado',
                        text: 'Correo enviado correctamente',
                        icon: 'success',
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "OK"
                    });
                }else{
                    Swal.fire({
                        title: 'Proceso No Culminado',
                        text: 'El correo no existe',
                        icon: 'error',
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "OK"
                    });
                }
             })
             .fail(function(){
                Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
             });
          });
        },
        allowOutsideClick: false              
    });
}

/*
var detalle = function(cod,doc,num){
    var moneda = $("#moneda").val();
    $('#mdl-detalle').modal('show');
    $.ajax({
      type: "post",
      dataType: "json",
      data: {
          cod: cod
      },
      url: '?c=Facturacion&a=Detalle',
      success: function (data){
        $.each(data, function(i, item) {
            var calc = item.precio * item.cantidad;
            $('#lista_p')
            .append(
              $('<tr/>')
                .append($('<td/>').html(item.cantidad))
                .append($('<td/>').html(item.Producto.nombre_prod+' <span class="label label-warning">'+item.Producto.pres_prod+'</span>'))
                .append($('<td/>').html(moneda+' '+formatNumber(item.precio)))
                .append($('<td class="text-right"/>').html(moneda+' '+formatNumber(calc)))
                );
            });
        }
    });
};
*/

$('.tab1').on('click', function() { 
    lisTab1();
});

$('.tab2').on('click', function() { 
    lisTab2();
});

$('.tab3').on('click', function() { 
    lisTab3();
});

$('.tab4').on('click', function() { 
    lisTab4();
});

$('.tab5').on('click', function() { 
    lisTab7();
});

$('.btn-res').click( function() {
    resumen_boletas($("#fecha-rd").val());
});

$('.btn-nvo').click( function() {
    $('#mdl-nvo-resumen').modal('show');
    lisTab5();
});

$('#btn-enviar-nc-boleta').click( function() {
    if($('#desc_ndc_bol').val().trim()==='')
    {
        alert("Tienes que ingresar una descripcion");

    } else {
        enviar_nota();
    }
    
});

$('#btn-enviar-nc-factura').click( function() {
    if($('#desc_nota_fac').val().trim()==='')
    {
        alert("Tienes que ingresar una descripcion");

    } else {
        enviar_nota_factura();
    }
    
});



//BOTON NOTA DE CREDITO
$('#btn_ncredito_fac').click( function() {
    $('#id_cliente').val('');
    $('#tipo_cliente').val(1);
    $('.modal-title').text('Nuevo Cliente');
    $('#mdl-nc-factura').modal('show');
    $("#td_dni").attr('checked', true);
    $("#td_ruc").attr('checked', false);
    $(".dni").prop('disabled', false);
    $(".ruc").prop('disabled', true);
    $(".block01").css("display","block");
    $(".block02").css("display","none");
    $(".block03").css("display","block");
    $(".block04").css("display","block");
    $(".block05").css("display","block");
    $(".block06").css("display","block");
    $(".block07").css("display","none");
});


mostrar_mdl_factura = function(id_venta, tipo_doc, serie_doc, ruc, razon_social,direc) {
    console.log("modal_nota_de_credito_factura");

    $('#id_factura').val(id_venta);
    $('#tipo_doc_fac').val(tipo_doc);
    $('#serie_nota_fac').val(serie_doc);

    $('#nro_doc_fac').val(serie_doc);
    $('#ruc_nota_fac').val(ruc);
    $('#razon_social').val(razon_social);
    $('#ndc_dir_fac').val(direc);

    $('.modal-title').text('Nueva Nota - Factura');
    $('#mdl-nc-factura').modal('show');

     $.ajax({
       url: $('#url').val()+'api/numero_nota_credito',
       type: "POST",
       data: {"tipo_doc" :  tipo_doc},
       dataType: 'json',
       beforeSend: function () {
             console.log(tipo_doc);
       },
       success: function(resp){
            if(resp.error == 0){
                console.log(resp.msj);
            } else {
                console.log(resp);
                $('#serie_nota_fac').val(resp.numero);
            }
            
        }
});
   
}

mostrar_mdl_boleta = function(id_venta,tipo_doc, serie_doc, dni, nombre) {
    console.log("modal_nota_de_credito_boleta");
    // $('#id_cliente').val('');
    $('#nro_documento_bol').val(serie_doc);
    $('#ndc_dni_bol').val(dni);
    $('#ndc_nombre_bol').val(nombre);
    $('#id_boleta').val(id_venta);
    $('#tipo_doc_bol').val(tipo_doc);

    $('#mdl-blt-titulo').text('Nueva Nota - Boleta');
    $('#mdl-nc-boleta').modal('show');


    $.ajax({
       url: $('#url').val()+'api/numero_nota_credito',
       type: "POST",
       data: {"tipo_doc" :  tipo_doc},
       dataType: 'json',
       success: function(resp){
            if(resp.error == 0){
                console.log(resp.msj);
            } else {
                console.log(resp);
                $('#nro_ndc_bol').val(resp.numero);
            }
            
        }
});

}


enviar_nota_factura = function () {

    var id_venta = $('#id_factura').val();
    var tipo_nota = $('#tipo_nota_fac').val();
    var desc_nota = $('#desc_nota_fac').val();
    var serie_doc = $('#nro_doc_fac').val();
    var tipo_doc = $('#tipo_doc_fac').val();


    Swal.fire({
        title: 'Necesitamos de tu Confirmación',
        html: `Está seguro de enviar la nota de venta de la factura <b> ${serie_doc}</b>? <br>Los cambios no se podrán revertir!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#34d16e',
        confirmButtonText: 'Si, Adelante!',
        cancelButtonText: "No!",
        showLoaderOnConfirm: true,
        preConfirm: function() {
          return new Promise(function(resolve) {
             $.ajax({
                url: $('#url').val()+'facturacion/NotaCredito',
                type: 'POST',
                data: {"id_venta": id_venta,
                       "tipo_nota": tipo_nota,
                       "tipo_doc" : tipo_doc,
                       "desc_nota": desc_nota},
                dataType: 'json',
                beforeSend: function () {
                    console.log(`${id_venta} ${tipo_nota} ${tipo_doc} ${desc_nota}`);
                }
             })
             .done(function(response){
                if(response['enviado_sunat'] == '1'){
                    $('#mdl-nc-factura').modal('hide');

                    Swal.fire({
                        title: 'Proceso Terminado',
                        text: response['mensaje'],
                        icon: 'success',
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "OK"
                    });
                }else{
                    Swal.fire({
                        title: 'Proceso No Culminado',
                        text: response['mensaje'],
                        icon: 'error',
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "OK"
                    });
                }
                lisTab1();
             })
             .fail(function(){
                Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
             });
          });
        },
        allowOutsideClick: false              
    });
}

enviar_nota = function () {

    var id_venta = $('#id_boleta').val();
    var tipo_nota = $('#tipo_nota_bol').val();
    var desc_nota = $('#desc_ndc_bol').val();
    var serie_doc = $('#nro_documento_bol').val();
    var tipo_doc = $('#tipo_doc_bol').val();


    Swal.fire({
        title: 'Necesitamos de tu Confirmación',
        html: `Está seguro de enviar la nota de venta de la boleta <b> ${serie_doc}</b>? <br>Los cambios no se podrán revertir!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#34d16e',
        confirmButtonText: 'Si, Adelante!',
        cancelButtonText: "No!",
        showLoaderOnConfirm: true,
        preConfirm: function() {
          return new Promise(function(resolve) {
             $.ajax({
                url: $('#url').val()+'facturacion/NotaCredito',
                type: 'POST',
                data: {"id_venta": id_venta,
                       "tipo_nota": tipo_nota,
                       "tipo_doc" : tipo_doc,
                       "desc_nota": desc_nota},
                dataType: 'json',
                beforeSend: function () {
                    console.log(`${id_venta} ${tipo_nota} ${tipo_doc} ${desc_nota}`);
                }
             })
             .done(function(response){
                if(response['enviado_sunat'] == '1'){
                    $('#mdl-nc-boleta').modal('hide');

                    Swal.fire({
                        title: 'Proceso Terminado',
                        text: response['mensaje'],
                        icon: 'success',
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "OK"
                    });
                }else{
                    Swal.fire({
                        title: 'Proceso No Culminado',
                        text: response['mensaje'],
                        icon: 'error',
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "OK"
                    });
                }
                lisTab1();
             })
             .fail(function(){
                Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
             });
          });
        },
        allowOutsideClick: false              
    });
}