# 🏋️‍♂️ FitZone

**FitZone** é uma plataforma completa de gestão de academias desenvolvida para centralizar e otimizar a administração de usuários, planos de treinamento, treinos personalizados e profissionais qualificados. 


---

## 🚀 Tecnologias

* **PHP** (PDO)
* **SQLite** (persistência de dados)
* **HTML / CSS / JavaScript**
* **Tailwind**

---

## 🔐 Acesso Padrão ao Sistema

### **Administrador**
- **Email:** `admin@fitzone.com`
- **Senha:** `admin123`

### **Personal Trainer**
- **Email:** `personal@fitzone.com`
- **Senha:** `personal123`

### **Aluno**
- **Email:** `aluno@fitzone.com`
- **Senha:** `aluno123`

---

## 🗄️ Persistência de Dados

A aplicação utiliza **SQLite** com o schema em `data/schema.sql`. A classe `BancoDeDados` utiliza PDO e oferece métodos genéricos:

- `ler($tabela)` – SELECT *
- `inserir($tabela, $registro)` – INSERT dinâmico
- `atualizar($tabela, $id, $dados)` – UPDATE por id
- `deletar($tabela, $id)` – DELETE por id
- `buscarPorId($tabela, $id)` – SELECT único
- `consultar($sql, $params)` – SELECT preparado (lista)
- `consultarUnico($sql, $params)` – SELECT preparado (único)

O banco de dados será criado automaticamente em `data/fitzone.db` na primeira execução.

---

## 🧩 Funcionalidades

* Cadastro e login de usuários
* Perfis de usuário: **Aluno**, **Personal Trainer**, **Admin** 
* Gerenciamento de **planos de academia**
* Associação de **alunos a planos e personais**
* Registro e visualização de **treinos personalizados**
* **Gerenciamento de exercícios** (adicionar, editar, excluir)
* **Painel administrativo completo**
* **Edição de dados físicos** (altura e peso) pelos alunos
* **Cálculo automático de IMC**
  
---

## 👨‍💻 Desenvolvido por

* **Davi Medeiros Dantas Soares**
* **João Gabriel Lacerda de Oliveira**
* **Maria Júlia Amaral**
* **Pedro Henrique de Almeida Araújo**
* **Mikael Abdias de Lima Santos**

```Projeto de conclusão da disciplina Programação Web (P2), ministrada pelo professor Daniel Brandão.```
