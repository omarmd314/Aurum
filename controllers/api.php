<?php 
Session::init(); 
$ver = (Session::get('rol') == 1 OR Session::get('rol') == 2 OR Session::get('rol') == 3) ? '' :  header('location: ' . URL . 'err/danger'); 
?>
<?php

class Api extends Controller {

	function __construct() {
		parent::__construct();
		Auth::handleLogin();
	}
    function dni($dni) 
	{	
        if(strlen($dni) == '8') : 
        print_r(json_encode($this->model->dni(API_TOKEN,$dni)));
        else :
            echo 'null';
        endif;
	}
    function ruc($ruc) 
	{	
        if(strlen($ruc) == '11') : 
        print_r(json_encode($this->model->ruc(API_TOKEN,$ruc)));
        else :
            echo 'null';
        endif;
	}
    function liberarbloqueo(){
        // echo 'hola';
        print_r(json_encode($this->model->liberarbloqueo()));
    }

    function numero_nota_credito()
    {
        $tipo_doc = filter_input(INPUT_POST, 'tipo_doc', FILTER_VALIDATE_INT);


       // $nro = $this->model->generar_numero_nota_credito($tipo_doc);
       // print_r(json_encode(["error"=>$tipo_doc]));

        if($tipo_doc === false || $tipo_doc === null)
        {
            print_r(json_encode(['error' => 0, 'msj' => 'Error el valor no paso el filtro']));

        } else {

            $nro = $this->model->generar_numero_nota_credito($tipo_doc);
            print_r(json_encode($nro));
        
         }   
    }

	
}