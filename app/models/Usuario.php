<?php
class Usuario {
    public $id;
    public $nome;
    public $email;
    public $senha;
    public $dataCadastro;
    public $tipoUsuario;

    public function __construct($id = null, $nome = "", $email = "", $senha = "", $dataCadastro = "", $tipoUsuario = "") {
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->dataCadastro = $dataCadastro;
        $this->tipoUsuario = $tipoUsuario;
    }
}
?>
