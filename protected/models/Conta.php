<?php

require_once '../controllers/ControllerGeneric.php';

abstract class Conta extends ControllerGeneric{
    
    private $descricao;
    private $documento;
    private $valor = 0;
    private $desconto = 0;
    private $dataPublicacao;
    private $dataVencimento;
    private $dataPagamento;
    private $status;
    private $duplicata;
    
    
    
    public function setDescricao($descricao){
            $this->descricao = $descricao;
    }

    public function getDescricao(){
            return $this->descricao;
    }
    
    public function setDocumento($documento){
            $this->documento = $documento;
    }

    public function getDocumento(){
            return $this->documento;
    }
    
    public function setValor($valor){
            $this->valor = $valor;
    }

    public function getValor(){
            return $this->valor;
    }
    
    public function setDesconto($desconto){
            $this->desconto = $desconto;
    }

    public function getDesconto(){
            return $this->desconto;
    }
    
    public function setdataPublicacao($dataPagamento){
            $this->dataPublicacao = $dataPagamento;
    }

    public function getDataPublicacao(){
            return $this->dataPublicacao;
    }
    
    public function setdataVencimento($dataVencimento){
            $this->dataVencimento = $dataVencimento;
    }

    public function getDataVencimento(){
            return $this->dataVencimento;
    }
    
    public function setdataPagamento($dataPagamento){
            $this->dataPagamento = $dataPagamento;
    }

    public function getDataPagamento(){
            return $this->dataPagamento;
    }
    
    public function setStatus($status){
            $this->status = $status;
    }

    public function getStatus(){
            return $this->status;
    }
    
    public function setDuplicata($duplicata){
            $this->duplicata = $duplicata;
    }

    public function getDuplicata(){
            return $this->duplicata;
    }
}
?>