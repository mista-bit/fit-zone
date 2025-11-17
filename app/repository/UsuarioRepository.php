<?php
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioRepository {
    private $conexao;

    public function __construct($conexao) {
        $this->conexao = $conexao;
    }

    public function criar($nome, $email, $senha, $tipoUsuario) {
        $sql = "INSERT INTO usuario (nome, email, senha, dataCadastro, tipoUsuario) VALUES (?, ?, ?, NOW(), ?)";
        $stmt = mysqli_prepare($this->conexao, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $nome, $email, $senha, $tipoUsuario);
        mysqli_stmt_execute($stmt);
        $id = mysqli_insert_id($this->conexao);
        mysqli_stmt_close($stmt);
        return $id;
    }

    public function listarTodos() {
        $sql = "SELECT * FROM usuario";
        $resultado = mysqli_query($this->conexao, $sql);
        $usuarios = [];
        while ($row = mysqli_fetch_assoc($resultado)) {
            $usuarios[] = $row;
        }
        return $usuarios;
    }
}
?>
