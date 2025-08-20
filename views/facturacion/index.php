<?php
date_default_timezone_set($_SESSION["zona_horaria"]);
setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
$fecha = date("d-m-Y");
$fechaa = date("m-Y");
?>
<input type="hidden" id="url" value="<?php echo URL; ?>"/>
<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>"/>
<input type="hidden" id="cod_rol_usu" value="<?php echo Session::get('rol'); ?>"/>
<input type="hidden" id="entorno" value="<?php echo ROOT_UBL21.Session::get('ruc'); ?>"/>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h3 class="m-b-0 m-t-0">Facturaci&oacute;n Electr&oacute;nica</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Varias operaciones</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs customtab" role="tablist">
                <li class="nav-item tab1"> <a class="nav-link active" data-toggle="tab" href="#tab1" role="tab"><span class="hidden-sm-up">CP</span> <span class="hidden-xs-down">Comprobantes de Pagos</span></a></li>
                <li class="nav-item tab2"> <a class="nav-link" data-toggle="tab" href="#tab2" role="tab"><span class="hidden-sm-up">BF</span> <span class="hidden-xs-down">Baja de Facturas</span></a></li>
                <li class="nav-item tab3"> <a class="nav-link" data-toggle="tab" href="#tab3" role="tab"><span class="hidden-sm-up">BB</span> <span class="hidden-xs-down">Baja de Boletas</span></a></li>
                <li class="nav-item tab4"> <a class="nav-link" data-toggle="tab" href="#tab4" role="tab"><span class="hidden-sm-up">RC</span> <span class="hidden-xs-down">Resumen de Boletas</span></a> </li>
                <!-- NOTA DE CREDITO --> 
                <li class="nav-item tab5"> <a class="nav-link" data-toggle="tab" href="#tab5" role="tab"><span class="hidden-sm-up">NC</span> <span class="hidden-xs-down">Notas de Credito</span></a></li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active" id="tab1" role="tabpanel">
                    <div class="card-body p-b-0">
                        <div class="message-box contact-box">
                            <h2 class="add-ct-btn">
                                <span style="text-align:right;" id="btn-excel-01"></span>
                            </h2>
                            <br>
                            <div class="row floating-labels">
                                <div class="col-lg-3">
                                    <div class="form-group m-b-40">
                                        <div class="input-group">
                                            <input type="text" class="form-control font-14 text-center" name="start-1" id="start-1" value="<?php echo $fecha; ?>" autocomplete="off"/>
                                            <span class="input-group-text bg-gris">al</span>
                                            <input type="text" class="form-control font-14 text-center" name="end-1" id="end-1" value="<?php echo $fecha; ?>" autocomplete="off"/>
                                        </div>
                                        <label>Rango de fechas</label>
                                    </div>
                                </div>
                                <div class="col-sm-6 offset-lg-5 col-lg-2">
                                    <div class="form-group m-b-40">
                                        <select class="selectpicker form-control" name="tipo_doc" id="tipo_doc" data-style="form-control btn-default" data-live-search="true" autocomplete="off" data-size="5">
                                            <option value="%" active>Mostrar Todo</option>
                                            <optgroup>
                                                <option value="1">BOLETA DE VENTA</option>
                                                <option value="2">FACTURA</option>
                                            </optgroup>
                                        </select>
                                        <span class="bar"></span>
                                        <label for="tipo_doc">Tipo Comprobante</label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-2">
                                    <div class="form-group m-b-40">
                                        <select class="selectpicker form-control" name="est_doc" id="est_doc" data-style="form-control btn-default" data-live-search="true" autocomplete="off" data-size="5">
                                            <option value="%" active>Mostrar Todo</option>
                                            <optgroup>
                                                <option value="1">ENVIADO A SUNAT</option>
                                                <option value="2">SIN ENVIAR</option>
                                                <option value="3">ANULADO</option>
                                            </optgroup>
                                        </select>
                                        <span class="bar"></span>
                                        <label for="est_doc">Estado</label>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center m-b-20">
                                <div class="row">
                                    <div class="col-4">
                                        <h2 class="font-medium text-warning m-b-0 font-30" id="total_enviados"></h2>
                                        <h6 class="font-bold m-b-10">Enviados</h6>                            
                                    </div>
                                    <div class="col-4">
                                        <h2 class="font-medium text-warning m-b-0 font-30" id="total_noenviados"></h2>
                                        <h6 class="font-bold m-b-10">No Enviados</h6>
                                    </div>
                                    <div class="col-4">
                                        <h2 class="font-medium text-warning m-b-0 font-30" id="total_anulados"></h2>
                                        <h6 class="font-bold m-b-10">Anulados</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive b-t m-b-10" style="min-height: 300px;">
                            <table id="table-1" class="table table-hover table-condensed stylish-table" width="100%">
                                <thead class="table-head">
                                    <tr>
                                        <th width="10%">Fecha</th>
                                        <th width="15%">Comprobante</th>
                                        <th width="25%">Cliente</th>
                                        <th width="10%">Total</th>
                                        <th width="15%">Estado</th>
                                        <th width="5%">XML</th>
                                        <th width="5%">CDR</th>
                                        <th width="5%">PDF</th>
                                        <th width="5%">Correo</th>
                                        <th width="5%">Sunat</th>
                                    </tr>
                                </thead>
                                <tbody class="tb-st"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab2" role="tabpanel">
                    <div class="card-body p-b-0">
                        <div class="message-box contact-box">
                            <h2 class="add-ct-btn">
                                <span style="text-align:right;" id="btn-excel-02"></span>
                            </h2>
                            <br>
                            <div class="row floating-labels">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="text" class="form-control font-14 text-center" name="start-2" id="start-2" value="<?php echo '01-'.$fechaa; ?>" autocomplete="off"/>
                                            <span class="input-group-text bg-gris">al</span>
                                            <input type="text" class="form-control font-14 text-center" name="end-2" id="end-2" value="<?php echo $fecha; ?>" autocomplete="off"/>
                                        </div>
                                        <label>Rango de fechas</label>
                                    </div>
                                </div>
                                <div class="offset-sm-6 col-sm-3">
                                    <div class="form-group"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive b-t m-b-10" style="min-height: 300px;">
                            <table id="table-2" class="table table-hover table-condensed stylish-table" width="100%">
                                <thead class="table-head">
                                    <tr>
                                        <th width="15%">Fecha Baja</th>
                                        <th width="10%">Correlativo</th>
                                        <th width="15%">Num.Doc</th>
                                        <th width="25%">Motivo</th>
                                        <th width="15%">Estado</th>
                                        <th width="5%">XML</th>
                                        <th width="5%">CDR</th>
                                        <th width="10%">Sunat</th>
                                    </tr>
                                </thead>
                                <tbody class="tb-st"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab3" role="tabpanel">
                    <div class="card-body p-b-0">
                        <div class="message-box contact-box">
                            <h2 class="add-ct-btn">
                                <span style="text-align:right;" id="btn-excel-03"></span>
                            </h2>
                            <br>
                            <div class="row floating-labels">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="text" class="form-control font-14 text-center" name="start-3" id="start-3" value="<?php echo '01-'.$fechaa; ?>" autocomplete="off"/>
                                            <span class="input-group-text bg-gris">al</span>
                                            <input type="text" class="form-control font-14 text-center" name="end-3" id="end-3" value="<?php echo $fecha; ?>" autocomplete="off"/>
                                        </div>
                                        <label>Rango de fechas</label>
                                    </div>
                                </div>
                                <div class="offset-sm-6 col-sm-3">
                                    <div class="form-group">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive b-t m-b-10" style="min-height: 300px;">
                            <table id="table-3" class="table table-hover table-condensed stylish-table" width="100%">
                                <thead class="table-head">
                                    <tr>
                                        <th width="20%">Fecha Baja</th>
                                        <th width="20%">Correlativo</th>
                                        <th width="20%">Num.Doc</th>
                                        <th width="20%">Estado</th>
                                        <th width="5%">XML</th>
                                        <th width="5%">CDR</th>
                                        <th width="10%">Sunat</th>
                                    </tr>
                                </thead>
                                <tbody class="tb-st"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab4" role="tabpanel">
                    <div class="card-body p-b-0">
                        <div class="message-box contact-box">
                            <h2 class="add-ct-btn">
                                <span style="text-align:right;" id="btn-excel-04"></span>
                            </h2>
                            <br>
                            <div class="row floating-labels">
                                <div class="col-sm-3">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <input type="text" class="form-control font-14 text-center" name="start-4" id="start-4" value="<?php echo '01-'.$fechaa; ?>" autocomplete="off"/>
                                                    <span class="input-group-text bg-gris">al</span>
                                                    <input type="text" class="form-control font-14 text-center" name="end-4" id="end-4" value="<?php echo $fecha; ?>" autocomplete="off"/>
                                                </div>
                                                <label>Rango de fechas</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="offset-sm-6 col-sm-3">
                                    <div class="form-group">
                                        <button class="btn btn-success btn-block btn-nvo">CREAR NUEVO RESUMEN DE BOLETAS</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive b-t m-b-10" style="min-height: 300px;">
                            <table id="table-4" class="table table-hover table-condensed stylish-table" width="100%">
                                <thead class="table-head">
                                    <tr>
                                        <th width="15%">Fecha Resumen</th>
                                        <th width="15%">Fecha Documento</th>
                                        <th width="30%">Documentos</th>
                                        <th width="20%">Estado</th>
                                        <th width="5%">XML</th>
                                        <th width="5%">CDR</th>
                                        <th width="10%">Sunat</th>
                                    </tr>
                                </thead>
                                <tbody class="tb-st"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--NOTA DE CREDITO -->
                <div class="tab-pane" id="tab5" role="tabpanel">
                    <div class="card-body p-b-0">
                        <div class="message-box contact-box">
                            <h2 class="add-ct-btn">
                                <span style="text-align:right;" id="btn-excel-03"></span>
                            </h2>
                            <br>
                            <div class="row floating-labels">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="text" class="form-control font-14 text-center" name="start-7" id="start-7" value="<?php echo '01-'.$fechaa; ?>" autocomplete="off"/>
                                            <span class="input-group-text bg-gris">al</span>
                                            <input type="text" class="form-control font-14 text-center" name="end-7" id="end-7" value="<?php echo $fecha; ?>" autocomplete="off"/>
                                        </div>
                                        <label>Rango de fechas</label>
                                    </div>
                                </div>
                                <div class="offset-sm-6 col-sm-3">
                                    <div class="form-group">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive b-t m-b-10" style="min-height: 300px;">
                            <table id="table-7" class="table table-hover table-condensed stylish-table" width="100%">
                                <thead class="table-head">
                                    <tr>
                                        <th width="10%">Fecha</th>
                                        <th width="10%">Correlativo</th>
                                        <th width="15%">Num.Doc</th>
                                        <th width="35%">Motivo</th>
                                        <th width="15%">Estado</th>
                                        <th width="5%">XML</th>
                                        <th width="5%">CDR</th>
                                        <th width="5%">Sunat</th>
                                    </tr>
                                </thead>
                                <tbody class="tb-st"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--NOTA DE CREDITO -->
            </div>
        </div>
    </div>
</div>

<div class="modal inmodal" id="mdl-nvo-resumen" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <h4 class="modal-title">Listar Comprobantes</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
            </div>
            <div class="modal-body p-l-0 p-r-0">
                <div class="card-body p-b-0">
                    <div class="message-box contact-box">
                        <div class="row floating-labels">
                            <div class="col-sm-3">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group m-b-10">
                                            <input type="text" class="form-control font-14 text-center" name="fecha-rd" id="fecha-rd" value="<?php echo $fecha; ?>" autocomplete="off"/>
                                            <span class="bar"></span>
                                            <label for="fecha-rd">Selecciona una fecha</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive b-t m-b-0">
                    <table id="table-5" class="table table-hover table-condensed stylish-table" width="100%">
                        <thead class="table-head">
                            <tr>
                                <th width="20%">Fecha</th>
                                <th width="35%">Tipo</th>
                                <th width="15%">Serie</th>
                                <th width="15%">N&uacute;mero</th>
                                <th width="15%">Total</th>
                            </tr>
                        </thead>
                        <tbody class="tb-st"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success btn-res">Crear</button>
            </div>
        </div>
    </div>
</div>

<div class="modal inmodal" id="mdl-detalle" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <h4 class="modal-title">Resumen</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
            </div>
            <div class="modal-body p-l-0 p-r-0">
                <div class="table-responsive">
                    <table id="table-6" class="table table-hover table-condensed" width="100%">
                        <thead>
                            <tr>
                                <th width="15%">Fecha</th>
                                <th width="20%">Tipo</th>
                                <th width="15%">Serie-N&uacute;mero</th>
                                <th width="35%">Cliente</th>
                                <th class="text-right" width="15%">Total</th>
                            </tr>
                        </thead>
                        <tbody class="tb-st"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL NOTA DE CREDITO FACTURA -->
<div class="modal inmodal" id="mdl-nc-factura" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content animated bounceInRight">

        <input type="hidden" name="id_factura" id="id_factura">
        <input type="hidden" name="tipo_doc_fac" id="tipo_doc_fac">
            
            <div class="modal-header justify-center">
                <h4 class="modal-title"></h4>
                <!-- <div class="ml-auto m-t-10">
                    <input name="tipo_cli" type="radio" value="1" id="td_dni" class="with-gap radio-col-light-green"/>
                    <label for="td_dni"><?php echo Session::get('diAcr'); ?></label>
                    <input name="tipo_cli" type="radio" value="2" id="td_ruc" class="with-gap radio-col-light-green"/>
                    <label for="td_ruc"><?php echo Session::get('tribAcr'); ?></label>
                </div> -->
            </div>
            <div class="modal-body p-0 floating-labels">
                <div class="row" style="margin-left: 0px; margin-right: 0px;">
                    <!-- Column -->
                    <div class="col-lg-6">
                        <div class="row card-body bg-header p-0">
                            <div class="col-md-12 p-l-10 p-t-10 b-t b-b m-b-40 bg-light-inverse" style="display: flex;">
                                <h6 class="font-medium">Comprobante</h6>
                            </div> 
                            <div class="col-md-6 block05" style="display: block;">
                                <div class="form-group m-b-40">
                                    <input type="text" class="form-control bg-transparent dni input-mayus" name="serie_nota_fac" id="serie_nota_fac" value="FC01-00001" autocomplete="off" data-mask="99-99-9999"/>
                                    <span class="bar"></span>
                                    <label for="serie_nota_fac">Serie</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ent m-b-40">
                                    <input type="text" class="form-control bg-transparent" name="nro_doc_fac" id="nro_doc_fac" value="F003-00000112" autocomplete="off"/>
                                    <span class="bar"></span>
                                    <label for="nro_doc_fac">N° de Documento</label>
                                </div>  
                            </div>

                            <div  class="col-md-12 block06" style="display: block;">
                        <div class="form-group m-b-40">
                            <select class="selectpicker form-control" name="tipo_nota_fac" id="tipo_nota_fac" data-style="form-control btn-default"  autocomplete="off" data-size="5">
                                <!-- <option value="%" active>Mostrar Todo</option> -->
                                <optgroup>
                                    <option value="01" selected>Anulación de la operación</option>
                                    <option value="02">Anulación por error en el RUC</option>
                                    <option value="03">Corrección por error en la descripción</option>
                                    <option value="04">Descuento global</option>
                                    <option value="05">Descuento por ítem</option>
                                    <option value="06">Devolución total</option>
                                    <option value="07">Devolución por ítem</option>
                                    <option value="08">Bonificación</option>
                                    <option value="09">Disminución en el valor</option>
                                    <option value="10">Otros Conceptos</option>
                                    <option value="11">Ajustes de operaciones de exportación</option>
                                    <option value="12">Ajustes afectos al IVAP</option>
                                    <option value="13">Ajustes - montos y/o fechas de pago</option>
                                </optgroup>
                            </select>
                            <span class="bar"></span>
                            <label for="filtro_tipo_ins">Tipo</label>
                        </div>
                    </div>
                            <!-- <div class="col-md-12 block06" style="display: block;">
                                <div class="form-group m-b-40">
                                    <input type="text" class="form-control bg-transparent dni" name="tipo_nota_fac" id="tipo_nota_fac" value="" autocomplete="off"/>
                                    <span class="bar"></span>
                                    <label for="tipo_nota">Tipo de nota</label>
                                </div>
                            </div> -->
                            <div class="col-md-12">
                                <div class="form-group m-b-40">
                                    <input type="text" class="form-control bg-transparent" name="desc_nota_fac" id="desc_nota_fac" value="" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="desc_nota_fac">Descripcion</label>
                                </div>
                            </div>
                            <!-- <div class="col-md-12">
                                <div class="form-group m-b-40">
                                    <input type="text" class="form-control bg-transparent input-mayus" name="referencia" id="referencia" value="" autocomplete="off"/>
                                    <span class="bar"></span>
                                    <label for="referencia">Referencia</label>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <!-- Column -->
                    <div class="col-lg-6 b-r">
                        <div class="row card-body p-0">
                            <div class="col-md-12 p-l-10 p-t-10 b-t b-b m-b-40 bg-light-info" style="display: flex;">
                                <h6 class="font-medium">Cliente</h6>
                            </div>                       
                            <!-- <div class="col-md-6 block01" style="display: block;">
                                <div class="form-group ent m-b-40">
                                    <input type="text" class="form-control dni" name="dni" id="dni" minlength="<?php echo Session::get('diCar'); ?>" maxlength="<?php echo Session::get('diCar'); ?>" value="" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="dni" class="c-dni"><?php echo Session::get('diAcr'); ?></label>
                                </div>
                            </div> -->
                            <div class="col-md-6 block02" style="display: block;">
                                <div class="form-group ent m-b-40">
                                    <input type="text" class="form-control ruc" name="ruc_nota_fac" id="ruc_nota_fac" minlength="11" maxlength="11" value="" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="ruc_nota_fac" class="c-ruc">RUC</label>
                                </div>
                            </div>                            
                            <div class="col-md-12 block07" style="display: block;">
                                <div class="form-group letNumMayMin m-b-40">
                                    <input type="text" class="form-control ruc input-mayus" name="razon_social" id="razon_social" value="" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="razon_social">Razon Social</label>
                                </div>
                            </div>
                            <div class="col-md-12 block03" style="display: block;">
                                <div class="form-group letMayMin m-b-40">
                                    <input type="text" class="form-control dni input-mayus" name="ndc_dir_fac" id="ndc_dir_fac" value="" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="nombres">Direccion</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" id="btn-enviar-nc-factura" class="btn btn-success">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<!-- FIN MODAL NOTA DE CREDITO FACTURA -->

<!-- MODAL NOTA DE CREDITO BOLETA -->
<div class="modal inmodal" id="mdl-nc-boleta" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content animated bounceInRight">
        <!--<form id="form" method="post" enctype="multipart/form-data"> -->
        <input type="hidden" name="id_boleta" id="id_boleta">
        <input type="hidden" name="tipo_doc_bol" id="tipo_doc_bol">
            <div class="modal-header justify-center">
                <h4 id="mdl-blt-titulo" class="modal-title"></h4>
                <!-- <div class="ml-auto m-t-10">
                    <input name="tipo_cli" type="radio" value="1" id="td_dni" class="with-gap radio-col-light-green"/>
                    <label for="td_dni"><?php echo Session::get('diAcr'); ?></label>
                    <input name="tipo_cli" type="radio" value="2" id="td_ruc" class="with-gap radio-col-light-green"/>
                    <label for="td_ruc"><?php echo Session::get('tribAcr'); ?></label>
                </div> -->
            </div>
            <div class="modal-body p-0 floating-labels">
                <div class="row" style="margin-left: 0px; margin-right: 0px;">
                    <!-- Column -->
                    <div class="col-lg-6">
                        <div class="row card-body bg-header p-0">
                            <div class="col-md-12 p-l-10 p-t-10 b-t b-b m-b-40 bg-light-inverse" style="display: flex;">
                                <h6 class="font-medium">Comprobante</h6>
                            </div> 
                            <div class="col-md-6 block05" style="display: block;">
                                <div class="form-group m-b-40">
                                    <input type="text" class="form-control bg-transparent dni input-mayus" name="nro_ndc_bol" id="nro_ndc_bol" value="serie" disabled autocomplete="off"/>
                                    <span class="bar"></span>
                                    <label for="nro_ndc_bol">Serie</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ent m-b-40">
                                <input type="text" class="form-control bg-transparent" name="nro_documento_bol" id="nro_documento_bol" value="B003-00001898" disabled autocomplete="off"/>
                                    <span class="bar"></span>
                                    <label for="nro_documento_bol">N° de Documento de referencia</label>
                                </div>
                            </div>
                            <!-- <div class="col-md-12 block06" style="display: block;">
                                <div class="form-group m-b-40">
                                    <input type="text" class="form-control bg-transparent dni" name="correo" id="correo" value="" autocomplete="off"/>
                                    <span class="bar"></span>
                                    <label for="correo">Correo electr&oacute;nico</label>
                                </div>
                            </div> -->
                           
                            <div  class="col-md-12 block06" style="display: block;">
                        <div class="form-group m-b-40">
                            <select class="selectpicker form-control" name="tipo_nota_bol" id="tipo_nota_bol" data-style="form-control btn-default"  autocomplete="off" data-size="5">
                                <!-- <option value="%" active>Mostrar Todo</option> -->
                                <optgroup>
                                    <option value="01" selected>Anulación de la operación</option>
                                    <option value="02">Anulación por error en el RUC</option>
                                    <option value="03">Corrección por error en la descripción</option>
                                    <option value="04">Descuento global</option>
                                    <option value="05">Descuento por ítem</option>
                                    <option value="06">Devolución total</option>
                                    <option value="07">Devolución por ítem</option>
                                    <option value="08">Bonificación</option>
                                    <option value="09">Disminución en el valor</option>
                                    <option value="10">Otros Conceptos</option>
                                    <option value="11">Ajustes de operaciones de exportación</option>
                                    <option value="12">Ajustes afectos al IVAP</option>
                                    <option value="13">Ajustes - montos y/o fechas de pago</option>
                                </optgroup>
                            </select>
                            <span class="bar"></span>
                            <label for="tipo_nota_bol">Tipo</label>
                        </div>
                    </div>

                            <div class="col-md-12">
                                <div class="form-group m-b-40">
                                    <input type="text" class="form-control bg-transparent" name="desc_ndc_bol" id="desc_ndc_bol" value="" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="direccion">Descripción</label>
                                </div>
                            </div>
                            <!-- <div class="col-md-12">
                                <div class="form-group m-b-40">
                                    <input type="text" class="form-control bg-transparent input-mayus" name="referencia" id="referencia" value="" autocomplete="off"/>
                                    <span class="bar"></span>
                                    <label for="referencia">Referencia</label>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <!-- Column -->
                    <!-- <div class="col-lg-6 b-r"> -->
                    <div class="col-lg-6">
                        <div class="row card-body p-0">
                            <div class="col-md-12 p-l-10 p-t-10 b-t b-b m-b-40 bg-light-info" style="display: flex;">
                                <h6 class="font-medium">Cliente</h6>
                            </div>                       
                            <div class="col-md-6 block01" style="display: block;">
                                <div class="form-group ent m-b-40">
                                    <input type="text" disabled class="form-control dni" name="ndc_dni_bol" id="ndc_dni_bol" minlength="8" maxlength="8" value="" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="ndc_dni_bol" class="c-dni">DNI</label>
                                </div>
                            </div>
                            <!-- <div class="col-md-6 block02" style="display: none;">
                                <div class="form-group ent m-b-40">
                                    <input type="text" class="form-control ruc" name="ruc" id="ruc" minlength="<?php echo Session::get('tribCar'); ?>" maxlength="<?php echo Session::get('tribCar'); ?>" value="" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="ruc" class="c-ruc"><?php echo Session::get('tribAcr'); ?></label>
                                </div>
                            </div>                             -->
                            <!-- <div class="col-md-12 block07" style="display: none;">
                                <div class="form-group letNumMayMin m-b-40">
                                    <input type="text" class="form-control ruc input-mayus" name="razon_social" id="razon_social" value="" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="razon_social">Raz&oacute;n Social</label>
                                </div>
                            </div> -->
                            <div class="col-md-12 block03" style="display: block;">
                                <div class="form-group letMayMin m-b-40">
                                    <input type="text" disabled class="form-control dni input-mayus" name="ndc_nombre_bol" id="ndc_nombre_bol" value="Omar Esteban Meza Diaz" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="ndc_nombre_bol">Nombre completo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" id="btn-enviar-nc-boleta"  class="btn btn-success">Aceptar</button>
            </div>
      <!--  </form> -->
        </div>
    </div>
</div>

<!-- FIN MODAL NOTA DE CREDITO BOLETA -->