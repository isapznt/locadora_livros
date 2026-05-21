📚 Sistema de Locadora de Livros

Sistema desenvolvido em PHP utilizando Programação Orientada a Objetos (POO), autenticação de usuários e interface responsiva com Bootstrap.

O projeto permite realizar o gerenciamento de livros, incluindo cadastro, aluguel, devolução e exclusão, além de controle de acesso por perfil de usuário.



🚀 Funcionalidades

👤 Autenticação
- Login de usuários
- Controle de sessão
- Logout
- Perfis:
  - Admin
  - Usuário comum

📚 Gerenciamento de Livros
- Adicionar livros
- Listar livros
- Alugar livros
- Devolver livros
- Excluir livros
- Controle de disponibilidade

💾 Persistência de Dados
- Armazenamento em arquivos JSON
- Salvamento automático

🎨 Interface
- Bootstrap 5
- Bootstrap Icons
- Layout responsivo
- Alertas visuais
- Tabelas estilizadas



 🛠️ Tecnologias Utilizadas

- PHP 7.4+
- Composer
- Bootstrap 5
- Bootstrap Icons
- JSON
- Programação Orientada a Objetos (POO)


 📂 Estrutura do Projeto

```txt
locadora-livros/
├── config/
│   └── config.php
├── data/
│   ├── livros.json
│   └── usuarios.json
├── interfaces/
│   └── Locavel.php
├── models/
│   ├── Livro.php
│   ├── Romance.php
│   └── Tecnologia.php
├── public/
│   ├── index.php
│   └── login.php
├── services/
│   ├── Auth.php
│   └── Biblioteca.php
├── views/
│   └── template.php
├── composer.json
└── vendor/
