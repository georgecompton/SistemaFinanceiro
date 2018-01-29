<?php

require_once 'Conta.php';

class ContaPagar extends Conta
{
    protected $table = 'tbcontapagar';
    private $valorPago  = 0;
    
    const PENDENTE = 0;
    const PAGO  = 1;
    
    public function setValorPago($valorPago){
            $this->valorPago = $valorPago;
    }

    public function getValorPago(){
            return $this->valorPago;
    }
    
    public function insert(){

        $sql  = "INSERT INTO $this->table (descricao, documento, dataPublicacao, dataVencimento, "
                . "desconto, valor, status, duplicata) VALUES (:descricao, :documento, :dataPublicacao, "
                . ":dataVencimento, :desconto,  :valor, :status, :duplicata)";
        $stmt = Persistence::prepare($sql);
        $stmt->bindValue(':descricao', $this->getDescricao());
        $stmt->bindValue(':documento', $this->getDocumento());
        $stmt->bindValue(':dataPublicacao', $this->getDataPublicacao());
        $stmt->bindValue(':dataVencimento', $this->getDataVencimento());
        $stmt->bindValue(':desconto', $this->getDesconto());
        $stmt->bindValue(':valor', $this->getValor());
        $stmt->bindValue(':status', $this->getStatus());
        $stmt->bindValue(':duplicata', $this->getDuplicata());
        
        return $stmt->execute(); 
    }

    public function update($id){

        $sql  = "UPDATE $this->table SET descricao = :descricao, documento = :documento, "
                . " dataPublicacao = :dataPublicacao, dataVencimento = :dataVencimento, desconto = :desconto, "
                . " valor = :valor, duplicata = :duplicata WHERE id = :id";
        $stmt = Persistence::prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':descricao', $this->getDescricao());
        $stmt->bindValue(':documento', $this->getDocumento());
        $stmt->bindValue(':dataPublicacao', $this->getDataPublicacao());
        $stmt->bindValue(':dataVencimento', $this->getDataVencimento());
        $stmt->bindValue(':desconto', $this->getDesconto());
        $stmt->bindValue(':valor', $this->getValor());
        $stmt->bindValue(':duplicata', $this->getDuplicata());
        
        return $stmt->execute();

    }
    
    public function payment($id){

        $sql  = "UPDATE $this->table SET valorPago = :valorPago, dataPagamento = :dataPagamento, "
                . " status = :status WHERE id = :id";
        $stmt = Persistence::prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':valorPago', $this->getValorPago());
        $stmt->bindValue(':dataPagamento', $this->getDataPagamento());
        $stmt->bindValue(':status', $this->getStatus());
        
        return $stmt->execute();
    }
}

?>
