# 🏋️‍♂️ FitZone

**FitZone** é uma plataforma completa de gestão de academias desenvolvida para centralizar e otimizar a administração de usuários, planos de treinamento, treinos personalizados e profissionais qualificados. 


---

## 🚀 Tecnologias

* **PHP** (PDO)
* **MySQL** (persistência principal – removido armazenamento JSON)
* **HTML / CSS / JavaScript**
* **Tailwind**

---

## 🗄️ Persistência de Dados

Originalmente alguns dados eram mantidos em arquivos JSON locais. A aplicação foi refatorada para usar exclusivamente MySQL com o schema em `data/schema.sql`. A classe `BancoDeDados` agora utiliza PDO e oferece métodos genéricos:

- `ler($tabela)` – SELECT *
- `inserir($tabela, $registro)` – INSERT dinâmico
- `atualizar($tabela, $id, $dados)` – UPDATE por id
- `deletar($tabela, $id)` – DELETE por id
- `buscarPorId($tabela, $id)` – SELECT único
- `consultar($sql, $params)` – SELECT preparado (lista)
- `consultarUnico($sql, $params)` – SELECT preparado (único)

Configuração padrão (XAMPP): host `127.0.0.1`, usuário `root`, senha vazia, banco `fitzone`.

Para criar o banco:
```sql
CREATE DATABASE IF NOT EXISTS fitzone;
USE fitzone;
SOURCE data/schema.sql;
```

---

## 🧩 Funcionalidades

* Cadastro e login de usuários
* Perfis de usuário: **Aluno**, **Personal Trainer**, **Admin** 
* Gerenciamento de **planos de academia**
* Associação de **alunos a planos e personais**
* Registro e visualização de **treinos personalizados**
* Simulação de módulo de **pagamentos**
  
---

## 👨‍💻 Desenvolvido por

* **Davi Medeiros Dantas Soares**
* **João Gabriel Lacerda de Oliveira**
* **Maria Júlia Amaral**
* **Pedro Henrique de Almeida Araújo**
* **Mikael Abdias de Lima Santos**

```Projeto de conclusão da disciplina Programação Web (P2), ministrada pelo professor Daniel Brandão.```
