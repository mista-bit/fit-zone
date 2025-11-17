<?php
require_once "C:/xampp/htdocs/projetofinal/db.php";
require_once "C:/xampp/htdocs/projetofinal/repository/UsuarioRepository.php";

$repo = new UsuarioRepository($conexao);

$idCriado = $repo->criar(
    "Davi",
    "davi@gmail.com",
    "123",
    "admin"
);

echo "Usuário criado! ID = " . $idCriado;
