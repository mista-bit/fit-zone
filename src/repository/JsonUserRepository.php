<?php
namespace App\Repository;

use App\Model\User;
use App\Model\Aluno;
use App\Model\Personal;

class JsonUserRepository implements UserRepositoryInterface
{
    private string $file;

    public function __construct(string $filePath)
    {
        // Caminho do arquivo JSON
        $this->file = $filePath;

        // Se o arquivo não existir, cria ele vazio
        if (!file_exists($this->file)) {
            file_put_contents($this->file, json_encode([]));
        }
    }

    /**
     * Lê os dados do JSON com lock de leitura
     */
    private function readData(): array
    {
        $fp = fopen($this->file, 'r');
        if (!$fp) {
            throw new \RuntimeException("Não foi possível abrir o arquivo para leitura");
        }

        // Lock compartilhado (somente leitura)
        flock($fp, LOCK_SH);

        // Lê tudo do arquivo
        $contents = '';
        while (!feof($fp)) {
            $contents .= fgets($fp);
        }

        // Libera lock e fecha
        flock($fp, LOCK_UN);
        fclose($fp);

        // Decodifica JSON
        $data = json_decode($contents, true);

        return is_array($data) ? $data : [];
    }

    /**
     * Escreve os dados no JSON com lock exclusivo
     */
    private function writeData(array $data): void
    {
        $fp = fopen($this->file, 'c+');
        if (!$fp) {
            throw new \RuntimeException("Não foi possível abrir o arquivo para escrita");
        }

        // Lock exclusivo (escrita)
        flock($fp, LOCK_EX);

        // Limpa arquivo e grava novo conteúdo
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        fflush($fp);

        // Libera lock e fecha
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    /**
     * Converte array em objeto User/Aluno/Personal
     */
    private function hydrate(array $r): User
    {
        $type = $r['type'] ?? 'aluno';

        if ($type === 'personal') {
            return new Personal(
                (int)$r['id'],
                (string)$r['name'],
                (string)$r['email']
            );
        }

        // Converte aluno
        return Aluno::fromArray($r);
    }

    /**
     * Retorna todos os usuários
     */
    public function all(): array
    {
        $rows = $this->readData();
        $users = [];

        foreach ($rows as $r) {
            $users[] = $this->hydrate($r);
        }

        return $users;
    }

    /**
     * Busca usuário por ID
     */
    public function find(int $id): ?User
    {
        $rows = $this->readData();

        foreach ($rows as $r) {
            if ((int)$r['id'] === $id) {
                return $this->hydrate($r);
            }
        }

        return null;
    }

    /**
     * Cria um novo usuário (aluno ou personal)
     */
    public function create(array $data): User
    {
        $rows = $this->readData();

        // Gera ID simples (máximo + 1)
        $maxId = 0;
        foreach ($rows as $r) {
            $maxId = max($maxId, (int)$r['id']);
        }
        $nextId = $maxId + 1;

        $type = $data['type'] ?? 'aluno';

        // Criação de personal
        if ($type === 'personal') {
            $user = new Personal($nextId, $data['name'] ?? '', $data['email'] ?? '');
        }
        // Criação de aluno
        else {
            $treino = [];

            // Treino pode vir como string ou array
            if (!empty($data['treino'])) {
                if (is_string($data['treino'])) {
                    $treino = array_filter(array_map('trim', explode(',', $data['treino'])));
                } elseif (is_array($data['treino'])) {
                    $treino = array_values($data['treino']);
                }
            }

            $user = new Aluno($nextId, $data['name'] ?? '', $data['email'] ?? '', $treino);
        }

        // Salva no arquivo
        $rows[] = $user->toArray();
        $this->writeData($rows);

        return $user;
    }

    /**
     * Atualiza um usuário por ID
     */
    public function update(int $id, array $data): ?User
    {
        $rows = $this->readData();

        foreach ($rows as $index => $r) {
            if ((int)$r['id'] === $id) {

                // Atualiza campos básicos
                $name  = $data['name']  ?? $r['name'];
                $email = $data['email'] ?? $r['email'];
                $type  = $r['type'] ?? 'aluno';

                // Atualiza treino se for aluno
                $treino = $r['treino'] ?? [];

                if ($type === 'aluno' && array_key_exists('treino', $data)) {
                    if (is_string($data['treino'])) {
                        $treino = array_filter(array_map('trim', explode(',', $data['treino'])));
                    } elseif (is_array($data['treino'])) {
                        $treino = array_values($data['treino']);
                    }
                }

                // Atualiza o array salvo
                $rows[$index] = [
                    'id'    => $id,
                    'name'  => $name,
                    'email' => $email,
                    'type'  => $type,
                ];

                if ($type === 'aluno') {
                    $rows[$index]['treino'] = $treino;
                }

                // Salva alterações
                $this->writeData($rows);

                return $this->hydrate($rows[$index]);
            }
        }

        return null;
    }

    /**
     * Apaga usuário por ID
     */
    public function delete(int $id): bool
    {
        $rows = $this->readData();
        $found = false;

        // Procura índice do usuário
        foreach ($rows as $index => $r) {
            if ((int)$r['id'] === $id) {
                $found = true;
                array_splice($rows, $index, 1);
                break;
            }
        }

        // Se achou, salva a lista atualizada
        if ($found) {
            $this->writeData($rows);
            return true;
        }

        return false;
    }
}
