<?php
require_once __DIR__ . '/../repository/UsuarioRepository.php';

class UsuarioService {
    private $usuarioRepository;

    public function __construct($conexao) {
        $this->usuarioRepository = new UsuarioRepository($conexao);
    }

    public function criarUsuario($nome, $email, $senha, $tipoUsuario) {
        if ($nome != "" && $email != "" && $senha != "" && $tipoUsuario != "") {
            return $this->usuarioRepository->criar($nome, $email, $senha, $tipoUsuario);
        } else {
            return false;
        }
    }

    public function listarUsuarios() {
        return $this->usuarioRepository->listarTodos();
    }
}
?>
