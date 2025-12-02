<?php

class BancoDeDados {

    private $pdo;

    public function __construct() {
        $dbPath = __DIR__ . '/../data/fitzone.db';
        $dbDir = dirname($dbPath);
        
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0777, true);
        }
        
        $dbExists = file_exists($dbPath);
        
        $dsn = "sqlite:{$dbPath}";
        $this->pdo = new \PDO($dsn, null, null, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
        
        // Se DB novo, importa schema
        if (!$dbExists) {
            $this->inicializarSchema();
        }
    }
    
    private function inicializarSchema() {
        $schemaPath = __DIR__ . '/../data/schema.sql';
        if (file_exists($schemaPath)) {
            $sql = file_get_contents($schemaPath);
            // SQLite executa statements separadamente
            $statements = explode(';', $sql);
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    try {
                        $this->pdo->exec($statement);
                    } catch (\PDOException $e) {
                        // Ignora erros de tabelas já existentes
                        if (strpos($e->getMessage(), 'already exists') === false) {
                            throw $e;
                        }
                    }
                }
            }
        }
    }

    // Retorna todos os registros da tabela
    public function ler($tabela) {
        $stmt = $this->pdo->query("SELECT * FROM `{$tabela}`");
        return $stmt->fetchAll();
    }

    // Inserção genérica: retorna ID
    public function inserir($tabela, $registro) {
        $colunas = array_keys($registro);
        $placeholders = array_map(fn($c) => ':' . $c, $colunas);
        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $tabela,
            implode(',', array_map(fn($c) => "`$c`", $colunas)),
            implode(',', $placeholders)
        );

        $stmt = $this->pdo->prepare($sql);
        foreach ($registro as $coluna => $valor) {
            $stmt->bindValue(':' . $coluna, $valor);
        }
        $stmt->execute();
        return (int)$this->pdo->lastInsertId();
    }

    // Atualização por id
    public function atualizar($tabela, $id, $novosDados) {
        $sets = [];
        foreach ($novosDados as $coluna => $_) {
            $sets[] = "`$coluna` = :$coluna";
        }
        $sql = sprintf(
            'UPDATE `%s` SET %s WHERE `id` = :id',
            $tabela,
            implode(', ', $sets)
        );
        $stmt = $this->pdo->prepare($sql);
        foreach ($novosDados as $coluna => $valor) {
            $stmt->bindValue(':' . $coluna, $valor);
        }
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    // Remoção por id
    public function deletar($tabela, $id) {
        $stmt = $this->pdo->prepare("DELETE FROM `{$tabela}` WHERE `id` = :id");
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    // Busca por id
    public function buscarPorId($tabela, $id) {
        $stmt = $this->pdo->prepare("SELECT * FROM `{$tabela}` WHERE `id` = :id");
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetch();
        return $res ?: null;
    }

    // Consulta genérica preparada (SELECT) retornando lista
    public function consultar($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $tipo = is_int($v) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
            $stmt->bindValue(is_string($k) ? $k : ($k + 1), $v, $tipo);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Consulta única (primeira linha) ou null
    public function consultarUnico($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $tipo = is_int($v) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
            $stmt->bindValue(is_string($k) ? $k : ($k + 1), $v, $tipo);
        }
        $stmt->execute();
        $res = $stmt->fetch();
        return $res ?: null;
    }
}
