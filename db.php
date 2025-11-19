<?php

class BancoDeDados {

    private $path;

    public function __construct() {
        $this->path = __DIR__ . "/banco/";

        if (!is_dir($this->path)) {
            mkdir($this->path, 0777, true);
        }
    }

   
    public function ler($tabela) {
        $arquivo = $this->path . $tabela . ".json";

        if (!file_exists($arquivo)) {
            file_put_contents($arquivo, "[]");
        }

        return json_decode(file_get_contents($arquivo), true);
    }

   
    public function salvar($tabela, $dados) {
        $arquivo = $this->path . $tabela . ".json";
        file_put_contents($arquivo, json_encode($dados, JSON_PRETTY_PRINT));
    }

    
    public function inserir($tabela, $registro) {
        $dados = $this->ler($tabela);

        $registro["id"] = count($dados) > 0 
            ? $dados[count($dados) - 1]["id"] + 1
            : 1;

        $dados[] = $registro;
        $this->salvar($tabela, $dados);

        return $registro["id"];
    }

    
    public function atualizar($tabela, $id, $novosDados) {
        $dados = $this->ler($tabela);

        foreach ($dados as &$item) {
            if ($item["id"] == $id) {
                $item = array_merge($item, $novosDados);
                break;
            }
        }

        $this->salvar($tabela, $dados);
    }

   
    public function deletar($tabela, $id) {
        $dados = $this->ler($tabela);

        $dados = array_filter($dados, function($item) use ($id) {
            return $item["id"] != $id;
        });

        $dados = array_values($dados);

        $this->salvar($tabela, $dados);
    }


    public function buscarPorId($tabela, $id) {
        $dados = $this->ler($tabela);

        foreach ($dados as $item) {
            if ($item["id"] == $id) return $item;
        }

        return null;
    }
}
