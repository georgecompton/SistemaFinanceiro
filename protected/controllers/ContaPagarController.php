<?php
    function __autoload($class_name){
            require_once '../models/'. $class_name . '.php';
    }

    $metodo = (isset($_GET['metodo']) ? $_GET['metodo'] : (isset($_POST['metodo']) ? $_POST['metodo'] : ''));
    if($metodo == ''):
        return;
    endif;
    
    if(function_exists($metodo)){
        call_user_func_array($metodo, $_POST);
    }else{
        echo 'O Método ' . $metodo . ' não existe!';
    }

    /*
     * Criação de Conta a Pagar
     */
    function create(){

        $_POST['valor'] = str_replace('R$', '', $_POST['valor']); 
        $_POST['valor'] = str_replace(',', '.', $_POST['valor']); 

        $contaPagar = new ContaPagar();
        $contaPagar->setDescricao($_POST['descricao']);
        $contaPagar->setDocumento($_POST['documento']);
        $contaPagar->setValor($_POST['valor']);
        $contaPagar->setDesconto($_POST['desconto']);
        $contaPagar->setdataPublicacao($_POST['dataPublicacao']);
        $contaPagar->setdataVencimento($_POST['dataVencimento']);
        $contaPagar->setStatus('PENDENTE');
        (!empty($_POST['duplicata']) ? $contaPagar->setDuplicata($_POST['duplicata']) : $contaPagar->setDuplicata(NULL));
        
        $contaPagar->insert();
        
        $dominio= $_SERVER['HTTP_HOST'];
        $url = "http://" . $dominio. $_SERVER['REQUEST_URI'];
        $url = str_replace('controllers/ContaPagarController.php', 'views/contaPagar/gerenciar.php', $url); 
        
        echo "<script>location.href='$url';</script>";
    }
    
    /*
     * Criação de Conta a Pagar
     */
    function update(){

        $_POST['valor'] = str_replace('R$', '', $_POST['valor']); 
        $_POST['valor'] = str_replace(',', '.', $_POST['valor']); 

        $contaPagar = new ContaPagar();
        $contaPagar->setDescricao($_POST['descricao']);
        $contaPagar->setDocumento($_POST['documento']);
        $contaPagar->setValor($_POST['valor']);
        $contaPagar->setDesconto($_POST['desconto']);
        $contaPagar->setdataPublicacao($_POST['dataPublicacao']);
        $contaPagar->setdataVencimento($_POST['dataVencimento']);
        (!empty($_POST['duplicata']) ? $contaPagar->setDuplicata($_POST['duplicata']) : $contaPagar->setDuplicata(NULL));
        
        $contaPagar->update($_POST['id']);
        
        $dominio= $_SERVER['HTTP_HOST'];
        $url = "http://" . $dominio. $_SERVER['REQUEST_URI'];
        $url = str_replace('controllers/ContaPagarController.php', 'views/contaPagar/gerenciar.php', $url); 
        
        echo "<script>location.href='$url';</script>";
    }
    
    /*
     * Fazer pagamento de Conta a Pagar
     */
    function makePayment(){
        
        
        $contaPagar = new ContaPagar();
        $conta = $contaPagar->find($_GET['id']);
        $desconto = ($conta->valor / 100) * $conta->desconto ; 
        
        $contaPagar->setValorPago($conta->valor - $desconto);
        $contaPagar->setStatus('PAGO');
        $contaPagar->setdataPagamento(date("Y-m-d"));
        $contaPagar->payment($_GET['id']);
        
        if(!empty($conta->duplicata)){
            $contaPagar = new ContaPagar();
            $contaPagar->setDescricao($conta->descricao);
            $contaPagar->setDocumento($conta->documento);
            $contaPagar->setValor($conta->valor);
            $contaPagar->setDesconto($conta->desconto);
            
            $mesAux = date('n', strtotime($conta->dataPublicacao));
            $format_meses = "+1 month";
            $dia = date('d', strtotime($conta->dataPublicacao));
            $mes = date('n', strtotime($conta->dataPublicacao . $format_meses));
            $ano = date('Y', strtotime($conta->dataPublicacao . $format_meses));
            
            $contaPagar->setdataPublicacao(date($ano.'-'.$mes.'-'.$dia));
            
            $mesAux = date('n', strtotime($conta->dataVencimento));
            $format_meses = "+1 month";
            $dia = date('d', strtotime($conta->dataVencimento));
            $mes = date('n', strtotime($conta->dataVencimento . $format_meses));
            $ano = date('Y', strtotime($conta->dataVencimento . $format_meses));

            $contaPagar->setdataVencimento(date($ano.'-'.$mes.'-'.$dia));
            $contaPagar->setStatus('PENDENTE');
            $contaPagar->setDuplicata($conta->duplicata);
            
            $contaPagar->insert();
        }
        
        $dominio= $_SERVER['HTTP_HOST'];
        $url = "http://" . $dominio. $_SERVER['REQUEST_URI'];
        $url = str_replace('controllers/ContaPagarController.php', 'views/contaPagar/gerenciar.php', $url); 
        
        echo "<script>location.href='$url';</script>";
    }
    
    
    /*
     * Deletar Conta a Pagar
     */
    function delete(){
        
        
        $contaPagar = new ContaPagar();
        $contaPagar->delete($_GET['id']);
        
        $dominio= $_SERVER['HTTP_HOST'];
        $url = "http://" . $dominio. $_SERVER['REQUEST_URI'];
        $url = str_replace('controllers/ContaPagarController.php', 'views/contaPagar/gerenciar.php', $url); 
        
        echo "<script>location.href='$url';</script>";
    }
    
    
?>