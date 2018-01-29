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
     * Criação de Conta a Receber
     */
    function create(){

        $_POST['valor'] = str_replace('R$', '', $_POST['valor']); 
        $_POST['valor'] = str_replace(',', '.', $_POST['valor']); 

        $contaReceber = new ContaReceber();
        $contaReceber->setDescricao($_POST['descricao']);
        $contaReceber->setDocumento($_POST['documento']);
        $contaReceber->setValor($_POST['valor']);
        $contaReceber->setDesconto($_POST['desconto']);
        $contaReceber->setdataPublicacao($_POST['dataPublicacao']);
        $contaReceber->setdataVencimento($_POST['dataVencimento']);
        $contaReceber->setStatus('PENDENTE');
        (!empty($_POST['duplicata']) ? $contaReceber->setDuplicata($_POST['duplicata']) : $contaReceber->setDuplicata(NULL));
        
        $contaReceber->insert();
        
        $dominio= $_SERVER['HTTP_HOST'];
        $url = "http://" . $dominio. $_SERVER['REQUEST_URI'];
        $url = str_replace('controllers/ContaReceberController.php', 'views/contaReceber/gerenciar.php', $url); 
        
        echo "<script>location.href='$url';</script>";
    }
    
    /*
     * Criação de Conta a Pagar
     */
    function update(){

        $_POST['valor'] = str_replace('R$', '', $_POST['valor']); 
        $_POST['valor'] = str_replace(',', '.', $_POST['valor']); 
        
        $contaReceber = new ContaReceber();
        $contaReceber->setDescricao($_POST['descricao']);
        $contaReceber->setDocumento($_POST['documento']);
        $contaReceber->setValor($_POST['valor']);
        $contaReceber->setDesconto($_POST['desconto']);
        $contaReceber->setdataPublicacao($_POST['dataPublicacao']);
        $contaReceber->setdataVencimento($_POST['dataVencimento']);
        (!empty($_POST['duplicata']) ? $contaReceber->setDuplicata($_POST['duplicata']) : $contaReceber->setDuplicata(NULL));
        
        $contaReceber->update($_POST['id']);
        
        $dominio= $_SERVER['HTTP_HOST'];
        $url = "http://" . $dominio. $_SERVER['REQUEST_URI'];
        $url = str_replace('controllers/ContaReceberController.php', 'views/contaReceber/gerenciar.php', $url); 
        
        echo "<script>location.href='$url';</script>";
    }
    
    /*
     * Fazer pagamento de Conta a Pagar
     */
    function makePayment(){
        
        
        $contaReceber = new ContaReceber();
        $conta = $contaReceber->find($_GET['id']);
        $desconto = ($conta->valor / 100) * $conta->desconto ; 
        
        $contaReceber->setValorRecebido($conta->valor - $desconto);
        $contaReceber->setStatus('RECEBIDO');
        $contaReceber->setdataPagamento(date("Y-m-d"));
        $contaReceber->payment($_GET['id']);
        
        if(!empty($conta->duplicata)){
            $contaReceber = new ContaReceber();
            $contaReceber->setDescricao($conta->descricao);
            $contaReceber->setDocumento($conta->documento);
            $contaReceber->setValor($conta->valor);
            $contaReceber->setDesconto($conta->desconto);
            
            $mesAux = date('n', strtotime($conta->dataPublicacao));
            $format_meses = "+1 month";
            $dia = date('d', strtotime($conta->dataPublicacao));
            $mes = date('n', strtotime($conta->dataPublicacao . $format_meses));
            $ano = date('Y', strtotime($conta->dataPublicacao . $format_meses));
            
            $contaReceber->setdataPublicacao(date($ano.'-'.$mes.'-'.$dia));
            
            $mesAux = date('n', strtotime($conta->dataVencimento));
            $format_meses = "+1 month";
            $dia = date('d', strtotime($conta->dataVencimento));
            $mes = date('n', strtotime($conta->dataVencimento . $format_meses));
            $ano = date('Y', strtotime($conta->dataVencimento . $format_meses));

            $contaReceber->setdataVencimento(date($ano.'-'.$mes.'-'.$dia));
            $contaReceber->setStatus('PENDENTE');
            $contaReceber->setDuplicata($conta->duplicata);
            
            $contaReceber->insert();
        }
        
        $dominio= $_SERVER['HTTP_HOST'];
        $url = "http://" . $dominio. $_SERVER['REQUEST_URI'];
        $url = str_replace('controllers/ContaReceberController.php', 'views/contaReceber/gerenciar.php', $url); 
        
        echo "<script>location.href='$url';</script>";
    }
    
    
    /*
     * Deletar Conta a Pagar
     */
    function delete(){
        
        
        $contaReceber = new ContaReceber();
        $contaReceber->delete($_GET['id']);
        
        $dominio= $_SERVER['HTTP_HOST'];
        $url = "http://" . $dominio. $_SERVER['REQUEST_URI'];
        $url = str_replace('controllers/ContaReceberController.php', 'views/contaReceber/gerenciar.php', $url); 
        
        echo "<script>location.href='$url';</script>";
    }
    
    
?>