<?php Session::init(); $ver = (Session::get('rol') == 1 OR Session::get('rol') == 2 OR Session::get('rol') == 3) ? '' :  header('location: ' . URL . 'err/danger'); ?>
<?php
require_once 'api_fact/config/config.php';
require_once 'api_fact/model/api.php';

class ApiSunat
{
    public function __construct() {
        $this->db = new Database(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_CHARSET);
    }

    public function sendDocSunaht($cod_ven,$num) {

        $api = new ApiModel();
        $documento = $api->getCabecera($cod_ven);
        $empresa = $api->getEmpresa();

        if ($documento->enviado_sunat == '1'):
            $respuesta = array('enviado_sunat' => '0', 'mensaje' => 'El documento N°'.$documento->serie_doc.'-'.$documento->nro_doc.' ya ha sido enviado a SUNAT');
        else:

            $item = 0;
            $total_ope_gravadas = 0;
            $total_igv_gravadas = 0;
            $total_ope_exoneradas = 0;
            $total_igv_exoneradas = 0;
            $productos = array();

            $deco = json_decode(json_encode($documento->Detalle), true);
            $resultado_detalle = array();
            if($documento->comision_delivery > 0){
                $dely_pr = array("codigo_producto" => "DELIV001",
                    "nombre_producto" => "DELIVERY",
                    "codigo_afectacion" => ($deco[0]['codigo_afectacion'] == 10)? "10": "20",
                    "cantidad" => "1.00" ,
                    "valor_unitario" => ($deco[0]['codigo_afectacion'] == 10)? number_format(($documento->comision_delivery/(1+ 0.18)), 6, '.', '') : number_format(($documento->comision_delivery), 6, '.', ''),
                    "precio_unitario" => number_format($documento->comision_delivery, 2, '.', '') ,
                    "valor_venta" => ($deco[0]['codigo_afectacion'] == 10)? number_format(($documento->comision_delivery/(1+ 0.18)), 2, '.', '') : number_format(($documento->comision_delivery), 2, '.', ''),
                    "total_igv" => ($deco[0]['codigo_afectacion'] == 10)? number_format($documento->comision_delivery-($documento->comision_delivery/(1 + 0.18)), 2, '.', '') : "0");

                $resultado_detalle = array_merge($deco, array($dely_pr));
            }else{
                $resultado_detalle = $deco;
            }
           
            foreach ($resultado_detalle as $detalle ){
                $item = $item + 1;

                if($detalle['codigo_afectacion'] == '10'){
                    $total_ope_gravadas = $total_ope_gravadas + $detalle['valor_venta'];
                    $total_igv_gravadas = $total_igv_gravadas + $detalle['total_igv'];
                    $total_ope_exoneradas = $total_ope_exoneradas + 0;
                    $total_igv_exoneradas = $total_igv_exoneradas + 0;
                } else{
                    $total_ope_gravadas = $total_ope_gravadas + 0;
                    $total_igv_gravadas = $total_igv_gravadas + 0;
                    $total_ope_exoneradas = $total_ope_exoneradas + $detalle['valor_venta'];
                    $total_igv_exoneradas = $total_igv_exoneradas + $detalle['total_igv'];
                }

                $productos[] = [
                    "txtITEM" => $item,
                    //"CODIGO_PRODUCTO_SUNAT" => $value['cod_sunat'], #codigo clase de producto
                    "txtUNIDAD_MEDIDA_DET" => "NIU", #dejarlo en NIU la Unidad de Medida
                    "txtCANTIDAD_DET" => $detalle['cantidad'],
                    "txtPRECIO_DET" => number_format($detalle['precio_unitario'], 6, '.', ''), #TOTAL DEL PRODUCTO
                    "txtPRECIO_TIPO_CODIGO" => "01", #Dejarlo como esta
                    "txtIGV" => number_format($detalle['total_igv'], 2, '.', ''),
                    "txtISC" => "0",  #Dejarlo como esta
                    "txtIMPORTE_DET" => number_format($detalle['valor_venta'], 2, '.', ''),
                    "txtCOD_TIPO_OPERACION" => $detalle['codigo_afectacion'], #Dejarlo como esta
                    "txtCODIGO_DET" => $detalle['codigo_producto'],
                    "txtDESCRIPCION_DET" => $detalle['nombre_producto'],
                    "txtPRECIO_SIN_IGV_DET" => number_format($detalle['valor_unitario'], 6, '.', '')
                ];
            }


            $doc = ($documento->tipo_comprobante == '01') ? 'factura' : 'boleta';

            $ruta = ROOT_WS_SUNAT . $doc . ".php";
            /* $ruta = ROOT_WS_SUNAT . "${doc}.php"; */

            //CALCULOS INICIO
                if($total_ope_gravadas > 0){

                   $total_gravadas = (($documento->total + $documento->comision_delivery) - $documento->descuento_monto) / 1.18;
                    // 83.50 / 1.18 = 70.76
                    $total_exoneradas = "0";

                    $sub_tot = number_format(((($documento->total + $documento->comision_delivery) - $documento->descuento_monto) - ((($documento->total + $documento->comision_delivery) - $documento->descuento_monto) / 1.18)), 2, '.', '');

                    $sub_tot1 = number_format(((($documento->total + $documento->comision_delivery) - $documento->descuento_monto) / 1.18), 2, '.', '');
                    $totales = number_format((($documento->total + $documento->comision_delivery) - $documento->descuento_monto), 2, '.', '');
                    $totales_2 = $totales;
                    $dscto_global = number_format(($documento->descuento_monto / 1.18), 2, '.', '');
                    $total_descuento = "0";
                } else {

                    $sub_tot = number_format(0, 2, '.', '');
                    $sub_tot1 = number_format(((($documento->total + $documento->comision_delivery))), 0, '.', '');
                    $totales = number_format((($documento->total + $documento->comision_delivery) ), 0, '.', '');
                    $totales_2 = number_format((($documento->total + $documento->comision_delivery)- $documento->descuento_monto), 0, '.', '');
                    $total_gravadas = "0";
                    $total_exoneradas = ($documento->total + $documento->comision_delivery);
                    $dscto_global = number_format(($documento->descuento_monto), 2, '.', '');
                    $total_descuento = number_format(($documento->descuento_monto), 0, '.', '');
                }
   
                $sub_total = ($total_ope_gravadas > 0) ? $total_gravadas : $total_exoneradas;
                $dscto_subtotal = $sub_total; // 95.76 // 70.00
                $dscto_porcentaje = $dscto_global / $dscto_subtotal; // 0.26% // 1.00

            //CALCULOS FIN
            $data = [
                //Cabecera del documento
                "tipo_proceso" => FAE_ENTORNO,
                "pass_firma" => $empresa->clavecertificado,
                "tipo_operacion" => "0101",
                "total_gravadas" => number_format($total_gravadas, 2, '.', ''), #subtotal
                "total_inafecta" => 0,
                "total_exoneradas" => number_format($total_exoneradas, 2, '.', ''),
                "total_gratuitas" => 0,
                "total_exportacion" => "0",
                //
                "total_descuento" => $total_descuento,
                "dscto_global" => number_format($dscto_global, 2, '.', ''),
                "dscto_porcentaje" => number_format($dscto_porcentaje, 5, '.', ''),// dscto_global / dscto_subtotal
                "dscto_subtotal" =>  number_format($dscto_subtotal, 2, '.', ''),// sub_total + dscto_global
                //
                "sub_total" => $sub_tot1, #subtotal

                "porcentaje_igv" => "18.00", #El IGV debe ser 18, si lo tienes en 0.18, conviértelo a 18
                "total_igv" =>  $sub_tot,
                "total_isc" => "0",
                "total_otr_imp" => "0",
                "total" => $totales,
                "total_2" => $totales_2,
                "total_letras" => "",
                "nro_guia_remision" => "",
                "cod_guia_remision" => "",
                "nro_otr_comprobante" => "",
                "serie_comprobante" => $documento->serie_doc, //Para boletas la serie debe comenzar por la letra B, seguido de tres dígitos
                "numero_comprobante" => $documento->nro_doc,
                "fecha_comprobante" => $documento->fecha,
                "fecha_vto_comprobante" => $documento->fecha,
                "cod_tipo_documento" => $documento->tipo_comprobante, #codigo de tipodocumento de sunat
                "cod_moneda" => "PEN", #sigla de la moneda
                //Datos del cliente
                "cliente_numerodocumento" => $documento->numero_documento,
                "cliente_nombre" => $documento->razon_social,
                "cliente_tipodocumento" => $documento->tipo_documento, //1: DNI codigo de tipodocumento de sunat
                "cliente_direccion" => $documento->direccion,
                "cliente_pais" => "PE",
                "cliente_ciudad" => "Lima",
                "cliente_codigoubigeo" => "",
                "cliente_departamento" => "",
                "cliente_provincia" => "",
                "cliente_distrito" => "",
                //data de la empresa emisora o contribuyente que entrega el documento electrónico.
                "emisor" => [
                    "ruc" => $empresa->ruc,
                    "tipo_doc" => "6",
                    "nom_comercial" => $empresa->nombre_comercial,
                    "razon_social" => $empresa->razon_social,
                    "codigo_ubigeo" => $empresa->ubigeo,
                    "direccion" => $empresa->direccion_fiscal,
                    "direccion_departamento" => $empresa->departamento,
                    "direccion_provincia" => $empresa->provincia,
                    "direccion_distrito" => $empresa->distrito,
                    "direccion_codigopais" => "PE",
                    "usuariosol" => $empresa->usuariosol,
                    "clavesol" => $empresa->clavesol
                ],
                //items del documento
                "detalle" => $productos
            ];

            //Invocamos el servicio
            $token = ''; //en caso quieras utilizar algún token generado desde tu sistema
            //codificamos la data

            $data_json = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $ruta);
            curl_setopt(
                    $ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Token token="' . $token . '"',
                'Content-Type: application/json',
                    )
            );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $rs = json_decode(curl_exec($ch));
            curl_close($ch);

            $enviado_sunat = (@$rs->respuesta == 'OK') ? '1' : '0';
            $code_respuesta_sunat = @$rs->cod_sunat;
            $descripcion_sunat_cdr = @$rs->msj_sunat;
            $name_file_sunat = @$rs->file;
            $hash_cdr = @$rs->hash_cdr;
            $hash_cpe = @$rs->hash_cpe;

            $stm = $this->db->prepare("UPDATE tm_venta SET enviado_sunat =  '".$enviado_sunat."', code_respuesta_sunat = '".$code_respuesta_sunat."', descripcion_sunat_cdr = '".$descripcion_sunat_cdr."', name_file_sunat = '".$name_file_sunat."', hash_cdr = '".$hash_cdr."', hash_cpe = '".$hash_cpe."' WHERE id_venta = ?");
                $stm->execute(array($cod_ven));

            $respuesta = array('enviado_sunat' => $enviado_sunat, 'mensaje' => $descripcion_sunat_cdr);

        endif;   

        if($num == 1){
            echo json_encode($respuesta);
        }   
    }

    public function postNotaCredito($post){
        
        date_default_timezone_set($_SESSION["zona_horaria"]);
        setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
        
        $id_venta = $post['id_venta'];
        $tipo_nota = $post['tipo_nota'];
        $desc_nota = $post['desc_nota'];
        $tipo_doc = '0'.$post['tipo_doc'];

        $ruta = ROOT_WS_SUNAT. 'notacredito.php';

        $fecha_nota = date('Y-m-d');
        $fecha_referencia = date('Y-m-d');

        $api = new ApiModel();
        $empresa = $api->getEmpresa();
        $correlativo = $api->generar_numero_de_nota($tipo_doc);



         if (empty($correlativo->numero)) {
            throw new Exception("El Nro de la nota no existe", 1);
        }

        $doc = $api->getCabecera($id_venta);
        $doc_detalle = json_decode(json_encode($doc->Detalle), true);


        $cabecera = [];
        $cabecera['fecha_registro'] = date('Y-m-d H:i:s');
        $cabecera['fecha_nota'] = $fecha_nota;
        $cabecera['fecha_referencia'] = $fecha_referencia;
        $cabecera['tipo_nota'] = $tipo_nota;
        $cabecera['tipo_doc'] = $doc->tipo_comprobante;
        $cabecera['serie_doc'] = $doc->serie_doc;
        $cabecera['num_doc'] = $doc->nro_doc;
        $cabecera['desc_nota'] = $desc_nota;
        $cabecera['correlativo'] = $correlativo->numero;
        $cabecera['estado'] = 'a';

        $item = 0;

        foreach ($doc_detalle as $detalle) {
            $item = $item + 1;
            $productos[] = [
                "txtITEM" => $item,
                //"CODIGO_PRODUCTO_SUNAT" => $value['cod_sunat'], #codigo clase de producto
                "txtUNIDAD_MEDIDA_DET" => "NIU", #dejarlo en NIU la Unidad de Medida
                "txtCANTIDAD_DET" => $detalle['cantidad'],
                "txtPRECIO_DET" => number_format($detalle['precio_unitario'], 6, '.', ''), #TOTAL DEL PRODUCTO
                "txtSUB_TOTAL_DET" => "",
                "txtPRECIO_TIPO_CODIGO" => "01", #Dejarlo como esta
                "txtIGV" => number_format($detalle['total_igv'], 2, '.', ''),
                "txtISC" => "0",  #Dejarlo como esta
                "txtIMPORTE_DET" => number_format($detalle['valor_venta'], 2, '.', ''),
                "txtCOD_TIPO_OPERACION" => $detalle['codigo_afectacion'], #Dejarlo como esta
                "txtCODIGO_DET" => $detalle['codigo_producto'],
                "txtDESCRIPCION_DET" => $detalle['nombre_producto'],
                "txtPRECIO_SIN_IGV_DET" => number_format($detalle['valor_unitario'], 6, '.', '')
            ];
        }

        $data = [
            "tipo_proceso" => FAE_ENTORNO,
            "pass_firma" => $empresa->clavecertificado,

            //Cabecera del documento
            "total_gravadas" => 0.0,
            "porcentaje_igv" => "18.00",
            "total_igv" => 0.0,
            "total" => $doc->total,
            "total_exoneradas"=>number_format($doc->total, 2, '.', '') ,
            "serie_comprobante" => ($doc->tipo_comprobante=='03')? 'BC01' : 'FC01',
            "numero_comprobante" => $correlativo->numero,
            "fecha_comprobante" => date('Y-m-d'),
            "cod_tipo_documento" => "07",
            "cod_moneda" => "PEN",
            "tipo_comprobante_modifica" => $doc->tipo_comprobante,
            "nro_documento_modifica" => $doc->serie_doc.'-'.$doc->nro_doc,
            "cod_tipo_motivo" => $tipo_nota,
            "descripcion_motivo" => $desc_nota,
            //Datos del cliente
            "cliente_numerodocumento" => $doc->numero_documento,
            "cliente_nombre" => $doc->razon_social,
            "cliente_tipodocumento" => $doc->tipo_documento, //1: DNI
            //data de la empresa emisora o contribuyente que entrega el documento electrónico.
            "emisor" => [
                "ruc" => $empresa->ruc,
                "tipo_doc" => "6",
                "nom_comercial" => $empresa->nombre_comercial,
                "razon_social" => $empresa->razon_social,
                "codigo_ubigeo" => $empresa->ubigeo,
                "direccion" => $empresa->direccion_fiscal,
                "direccion_departamento" => $empresa->departamento,
                "direccion_provincia" => $empresa->provincia,
                "direccion_distrito" => $empresa->distrito,
                "direccion_codigopais" => "PE",
                "usuariosol" => $empresa->usuariosol,
                "clavesol" => $empresa->clavesol],

            "detalle" => $productos];

        $token = ''; 
        $data_json = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ruta);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Authorization: Token token="' . $token . '"','Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $rs = json_decode(curl_exec($ch));
        curl_close($ch);

        $enviado_sunat = (@$rs->respuesta == 'OK') ? '1' : '0';
        $code_respuesta_sunat = @$rs->cod_sunat;
        $descripcion_sunat_cdr = @$rs->msj_sunat;
        $name_file_sunat = @$rs->file;
        $hash_cdr = @$rs->hash_cdr;
        $hash_cpe = @$rs->hash_cpe;

         if($enviado_sunat == 1){
                $rpta = $api->registrar_nota($cabecera);
                $dato = [];
                $dato['id_nota'] = $rpta->idnota;

                $dato['enviado_sunat'] = $enviado_sunat;
                $dato['hash_cpe'] = $hash_cpe;
                $dato['hash_cdr'] = $hash_cdr;
                $dato['code_respuesta_sunat'] = $code_respuesta_sunat;
                $dato['descripcion_sunat_cdr'] = $descripcion_sunat_cdr;
                $dato['name_file_sunat'] = $name_file_sunat;
                $api->actualizar_cdr_nota($dato);
            }


        $respuesta = ['enviado_sunat' => $enviado_sunat, 'mensaje' => $descripcion_sunat_cdr];

        echo json_encode($respuesta);
    }


    public function postComunicacionBaja($post) {

        date_default_timezone_set($_SESSION["zona_horaria"]);
        setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
        $cod_ven = $post['cod_ven'];
        $tipo_doc = $post['tipo_doc'];
        
        $api = new ApiModel();
        $empresa = $api->getEmpresa();
        $comunicacion = $api->generar_numero_baja($tipo_doc);
        if (empty($comunicacion->numero)) {
            throw new Exception("El Nro de la Comunicación de baja no existe", 1);
        }

        $fecha_referencia = date('Y-m-d');
        $fecha_baja = date('Y-m-d');

        $documento = $api->buscar_documento($cod_ven);

        $cabecera = array();
        $cabecera['fecha_registro'] = date('Y-m-d H:i:s');
        $cabecera['fecha_baja'] = $fecha_baja;
        $cabecera['fecha_referencia'] = $fecha_referencia;
        $cabecera['tipo_doc'] = $documento->tipo_comprobante;
        $cabecera['serie_doc'] = $documento->serie_doc;
        $cabecera['num_doc'] = $documento->nro_doc;
        if($tipo_doc == 1){
            $cabecera['nombre_baja'] = 'ERROR DE CLIENTE';
        }
        $cabecera['correlativo'] = $comunicacion->numero;
        $cabecera['estado'] = 'a';

        if ($documento->estado == 'i'):
            $respuesta = array('enviado_sunat' => '0', 'mensaje' => 'El documento N°'.$documento->serie_doc.'-'.$documento->nro_doc.' ya ha sido enviado a SUNAT');
        else:

            $detalle = array();
            if($tipo_doc == 1){
                $ruta = ROOT_WS_SUNAT . "baja.php";
                $codigo = "RA";
                $fecha_text_opc = "fecha_baja";
                $detalle[] = [
                    "ITEM" => "1",
                    "TIPO_COMPROBANTE" => $documento->tipo_comprobante,
                    "SERIE" => $documento->serie_doc,
                    "NUMERO" => $documento->nro_doc,
                    "MOTIVO" => "ERROR DE CLIENTE" #Motivo baja
                ];
            }

            if($tipo_doc == 3){
                $ruta = ROOT_WS_SUNAT . "resumen_boletas.php";
                $codigo = "RC";
                $fecha_text_opc = "fecha_documento";
                $detalle[] = [
                    "ITEM" => "1",
                    "TIPO_COMPROBANTE" => $documento->tipo_comprobante,
                    "NRO_COMPROBANTE" => $documento->serie_doc."-".$documento->nro_doc,
                    "NRO_DOCUMENTO" => $documento->dni,
                    "TIPO_DOCUMENTO" => $documento->tipo_documento,
                    "NRO_COMPROBANTE_REF" => "0",
                    "TIPO_COMPROBANTE_REF" => "0",
                    "STATUS" => "3",// 3 ANULADOS- 1:NUEVOS
                    "COD_MONEDA" => $documento->tipo_moneda,
                    "TOTAL" => number_format($documento->total_facturado, 2, '.', ''),
                    "GRAVADA" => number_format($documento->total_gravadas, 2, '.', ''),
                    "IGV" => number_format($documento->total_igv, 2, '.', ''),
                    "EXONERADO" => number_format($documento->total_facturado, 2, '.', ''),
                    "INAFECTO" => "0",
                    "EXPORTACION" => "0",
                    "GRATUITAS" => "0",
                    "MONTO_CARGO_X_ASIG" => "0",
                    "CARGO_X_ASIGNACION" => "0",
                    "ISC" => "0",
                    "OTROS" => "0"
                ];
            }

            $data = [
                "tipo_proceso" => FAE_ENTORNO,
                "pass_firma" => $empresa->clavecertificado,
                //Cabecera del documento
                "codigo" => $codigo,
                "serie" => date('Ymd'),
                "secuencia" => $comunicacion->numero,
                "fecha_referencia" => $fecha_referencia,
                $fecha_text_opc => $fecha_baja,
                //data de la empresa emisora o contribuyente que entrega el documento electrónico.
                "emisor" => [
                    "ruc" => $empresa->ruc,
                    "tipo_doc" => "6",
                    "nom_comercial" => $empresa->nombre_comercial,
                    "razon_social" => $empresa->razon_social,
                    "codigo_ubigeo" => $empresa->ubigeo,
                    "direccion" => $empresa->direccion_fiscal,
                    "direccion_departamento" => $empresa->departamento,
                    "direccion_provincia" => $empresa->provincia,
                    "direccion_distrito" => $empresa->distrito,
                    "direccion_codigopais" => "PE",
                    "usuariosol" => $empresa->usuariosol,
                    "clavesol" => $empresa->clavesol
                ],
                //items
                "detalle" => $detalle
            ];

            $token = ''; //en caso quieras utilizar algún token generado desde tu sistema
            $data_json = json_encode($data);

            #print_r($data_json); exit;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $ruta);
            curl_setopt(
                    $ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Token token="' . $token . '"',
                'Content-Type: application/json',
                    )
            );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $rsp = json_decode(curl_exec($ch));
            curl_close($ch);

            $enviado_sunat = (@$rsp->respuesta == 'OK') ? '1' : '0';
            $code_respuesta_sunat = @$rsp->cod_sunat;
            $descripcion_sunat_cdr = @$rsp->msj_sunat;
            $name_file_sunat = @$rsp->file;
            $hash_cdr = @$rsp->hash_cdr;
            $hash_cpe = @$rsp->hash_cpe;

            if($enviado_sunat == 1){
                $rpta = $api->registrar_baja($cabecera,$cod_ven);   
                $dato = array();
                $dato['id_comunicacion'] = $rpta->idcomunicacion;                
                $dato['hash_cpe'] = $hash_cpe;
                $dato['hash_cdr'] = $hash_cdr;
                $dato['code_respuesta_sunat'] = $code_respuesta_sunat;
                $dato['descripcion_sunat_cdr'] = $descripcion_sunat_cdr;
                $dato['name_file_sunat'] = $name_file_sunat;
                $rpta = $api->actualizar_cdr_baja($dato);
            }

            $respuesta = array('enviado_sunat' => $enviado_sunat, 'mensaje' => $descripcion_sunat_cdr);
            
        endif;

        echo json_encode($respuesta);
            
    }

    public function postResumenDiario($post) {

        date_default_timezone_set($_SESSION["zona_horaria"]);
        setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
        
        $api = new ApiModel();
        $empresa = $api->getEmpresa();
        $resumen = $api->generar_numero_resumen();
        if (empty($resumen->numero)) {
            throw new Exception("El Nro de Resumen no existe", 1);
        }

        $fecha_referencia = date('Y-m-d');
        $fecha_resumen = date('Y-m-d');
        $fecha = date('Y-m-d',strtotime($post['fecha']));

        $boletas = $api->buscar_boletas($fecha);

        if (empty($boletas)):
            $respuesta = array('enviado_sunat' => '0', 'mensaje' => 'No existe boletas en la fecha consultada');
        else:

            $cabecera = array();
            $cabecera['fecha_registro'] = date('Y-m-d H:i:s');
            $cabecera['fecha_resumen'] = $fecha_resumen;
            //$cabecera['fecha_referencia'] = $fecha_referencia;
            $cabecera['fecha_referencia'] = $fecha;
            $cabecera['correlativo'] = $resumen->numero;
            $cabecera['estado'] = 'a';

            $detalleResumen = array();
            foreach ($boletas as $boleta)
            {
                $detalleResumen[] = array(
                    'id_resumen' => '',
                    'id_venta' => $boleta->id_venta,
                    'status_code' => $boleta->status_code
                );
            }

            $ruta = ROOT_WS_SUNAT . "resumen_boletas.php";

            $contador = 1;
            $detalle = [];
            foreach ($boletas as $boleta){
                $detalle[] = [
                    "ITEM" => $contador,
                    "TIPO_COMPROBANTE" => $boleta->tipo_comprobante,
                    "NRO_COMPROBANTE" => $boleta->serie_doc."-".$boleta->nro_doc,
                    "NRO_DOCUMENTO" => $boleta->dni,
                    "TIPO_DOCUMENTO" => $boleta->tipo_documento,
                    "NRO_COMPROBANTE_REF" => "0",
                    "TIPO_COMPROBANTE_REF" => "0",
                    "STATUS" => "1",//3-resumen dirio de boletas
                    "COD_MONEDA" => $boleta->tipo_moneda,
                    "TOTAL" => number_format($boleta->total_facturado, 2, '.', ''),
                    "GRAVADA" => number_format($boleta->total_gravadas, 2, '.', ''),
                    "IGV" => number_format($boleta->total_igv, 2, '.', ''),
                    //"EXONERADO" => "0",
                    "EXONERADO" => number_format($boleta->total_facturado, 2, '.', ''),
                    "INAFECTO" => "0",
                    "EXPORTACION" => "0",
                    "GRATUITAS" => "0",
                    "MONTO_CARGO_X_ASIG" => "0",
                    "CARGO_X_ASIGNACION" => "0",
                    "ISC" => "0",
                    "OTROS" => "0"
                ];
                $contador++;
            }

            $mirame=FAE_ENTORNO;

            $data = array(
                "tipo_proceso" => FAE_ENTORNO,
                "pass_firma" => $empresa->clavecertificado,
                //Cabecera del documento
                "codigo" => "RC",
                "serie" => date('Ymd'),
                "secuencia" => $resumen->numero,
                "fecha_referencia" => $fecha,//./cbc:ReferenceDate Fecha de emisión de los documentos (yyyy-mm-dd)
                "fecha_documento" => $fecha_referencia,//./cbc:IssueDate Fecha de generación del resumen (yyyy-mm-dd)
            
                //data de la empresa emisora o contribuyente que entrega el documento electrónico.
                "emisor" => array(
                    "ruc" => $empresa->ruc,
                    "tipo_doc" => "6",
                    "nom_comercial" => $empresa->nombre_comercial,
                    "razon_social" => $empresa->razon_social,
                    "codigo_ubigeo" => $empresa->ubigeo,
                    "direccion" => $empresa->direccion_fiscal,
                    "direccion_departamento" => $empresa->departamento,
                    "direccion_provincia" => $empresa->provincia,
                    "direccion_distrito" => $empresa->distrito,
                    "direccion_codigopais" => "PE",
                    "usuariosol" => $empresa->usuariosol,
                    "clavesol" => $empresa->clavesol
                ),
                "detalle" => $detalle
            );

            $token = ''; 
            $data_json = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $ruta);
            curl_setopt(
                    $ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Token token="' . $token . '"',
                'Content-Type: application/json',
                    )
            );

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $rsp = json_decode(curl_exec($ch));
            curl_close($ch);

            $enviado_sunat = (@$rsp->respuesta == 'OK') ? '1' : '0';
            $code_respuesta_sunat = @$rsp->cod_sunat;
            $descripcion_sunat_cdr = @$rsp->msj_sunat;
            $name_file_sunat = @$rsp->file;
            $hash_cdr = @$rsp->hash_cdr;
            $hash_cpe = @$rsp->hash_cpe;

            if($enviado_sunat == 1){
                $rpta = $api->registrar_resumen($cabecera,$detalleResumen);   
                $dato = array();
                $dato['id_resumen'] = $rpta->idresumen;                
                $dato['hash_cpe'] = $hash_cpe;
                $dato['hash_cdr'] = $hash_cdr;
                $dato['code_respuesta_sunat'] = $code_respuesta_sunat;
                $dato['descripcion_sunat_cdr'] = $descripcion_sunat_cdr;
                $dato['name_file_sunat'] = $name_file_sunat;
                $rpta = $api->actualizar_cdr_resumen($dato);
            }

            $respuesta = array('enviado_sunat' => $enviado_sunat, 'mensaje' => $descripcion_sunat_cdr);

        endif; 

        echo json_encode($respuesta);

    }

}
?>
