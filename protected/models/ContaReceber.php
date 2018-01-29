<?php

require_once 'Conta.php';

class ContaReceber extends Conta
{
    protected $table = 'tbcontareceber';
    private $valorRecebido  = 0;
    
    const PENDENTE = 0;
    const RECEBIDO  = 1;
    
    public function setValorRecebido($valorPago){
            $this->valorRecebido = $valorPago;
    }

    public function getValorRecebido(){
            return $this->valorRecebido;
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

        $sql  = "UPDATE $this->table SET valorRecebido = :valorRecebido, dataPagamento = :dataPagamento, "
                . " status = :status WHERE id = :id";
        $stmt = Persistence::prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':valorRecebido', $this->getValorRecebido());
        $stmt->bindValue(':dataPagamento', $this->getDataPagamento());
        $stmt->bindValue(':status', $this->getStatus());
        
        return $stmt->execute();
    }
}

?>
