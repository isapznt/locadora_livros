# Funcionamento do Sistema de Locadora de Veículos com PHP e Bootstrap

Este documento descreve o funcionamento do sistema de locadora de veículos desenvolvido em PHP, utilizando Bootstrap para a interface, com autenticação de usuários, gerenciamento de veículos (carros e motos) e persistência de dados em arquivos JSON. O foco principal é explicar o funcionamento geral do sistema, com ênfase especial nos perfis de acesso (admin e usuário).

## 1. Visão Geral do Sistema

O sistema de locadora de veículos é uma aplicação web que permite:
- Autenticação de usuários com dois perfis: **admin** (administrador) e **usuário** (usuário comum).
- Gerenciamento de veículos, incluindo cadastro, aluguel, devolução e exclusão.
- Cálculo de previsão de aluguel com base no tipo de veículo (carro ou moto) e número de dias.
- Interface responsiva baseada no framework Bootstrap, com ícones do Bootstrap Icons.

Os dados são armazenados em dois arquivos JSON:
- `usuarios.json`: Contém informações de usuários (username, senha criptografada e perfil).
- `veiculos.json`: Armazena os veículos cadastrados (tipo, modelo, placa e status de disponibilidade).

## 2. Estrutura do Sistema

### 2.1. Arquitetura
O sistema utiliza:
- **PHP**: Para lógica de negócios, autenticação e manipulação de dados.
- **Bootstrap**: Para estilização e layout responsivo da interface.
- **Bootstrap Icons**: Para ícones na interface (como o ícone de usuário e seta no botão "Sair").
- **Composer**: Para autoloading de classes (via PSR-4).
- **JSON**: Para persistência de dados em arquivos (`usuarios.json` e `veiculos.json`).

A estrutura de pastas é organizada da seguinte forma:
```
locadora-veiculos-perfis/
├── config/
│   └── config.php
├── data/
│   ├── usuarios.json
│   └── veiculos.json
├── interfaces/
│   └── Locavel.php
├── models/
│   ├── Veiculo.php
│   ├── Carro.php
│   └── Moto.php
├── public/
│   ├── index.php
│   └── login.php
├── services/
│   ├── Auth.php
│   └── Locadora.php
├── views/
│   └── template.php
├── composer.json
├── composer.lock
└── vendor/
```

### 2.2. Componentes Principais
- **Interfaces**: Define a interface `Locavel` para veículos locáveis (métodos `alugar()`, `devolver()` e `isDisponivel()`).
- **Models**: Classes `Veiculo` (abstrata), `Carro` e `Moto` para representar os veículos, com cálculo de aluguel baseado em diárias constantes (`DIARIA_CARRO` = R$ 100,00 e `DIARIA_MOTO` = R$ 50,00).
- **Services**: Classes `Auth` (autenticação e gerenciamento de usuários) e `Locadora` (gerenciamento de veículos).
- **Views**: Template principal em `template.php` para renderizar a interface, e `login.php` para autenticação.
- **Controllers**: Lógica em `index.php` para processar requisições e carregar o template.

## 3. Funcionamento Geral

### 3.1. Autenticação
O sistema requer autenticação para acessar a página principal (`index.php`). O login é feito via `login.php`, que:
- Utiliza a classe `Services\Auth` para verificar credenciais (username e senha) contra `usuarios.json`.
- Armazena informações do usuário logado em `$_SESSION['auth']`, incluindo `username` e `perfil`.
- Suporta dois perfis:
  - **Admin**: Pode adicionar, alugar, devolver e deletar veículos, além de calcular previsões de aluguel.
  - **Usuário**: Pode apenas visualizar veículos e calcular previsões de aluguel, sem acesso às ações administrativas.

Exemplo de `usuarios.json`:
```json
[
    {
        "username": "admin",
        "password": "$2y$10$hash_gerado_aqui...",
        "perfil": "admin"
    },
    {
        "username": "usuario",
        "password": "$2y$10$hash_gerado_aqui...",
        "perfil": "usuario"
    }
]
```
As senhas são criptografadas com `password_hash()` e verificadas com `password_verify()`.

### 3.2. Gerenciamento de Veículos
A classe `Services\Locadora` gerencia veículos (carros e motos) armazenados em `veiculos.json`. Cada veículo tem:
- **Tipo**: "Carro" ou "Moto".
- **Modelo**: Nome do veículo (ex.: "Sandero", "Ninja").
- **Placa**: Identificador único (ex.: "FMA-6680").
- **Disponível**: Status booleano (`true` para disponível, `false` para alugado).

Exemplo de `veiculos.json`:
```json
[
    {
        "tipo": "Carro",
        "modelo": "Sandero",
        "placa": "FMA-6680",
        "disponivel": false
    },
    {
        "tipo": "Moto",
        "modelo": "Ninja",
        "placa": "FMA-6600",
        "disponivel": true
    }
]
```

#### Funcionalidades
- **Adicionar Veículo**: Somente admin pode adicionar novos veículos via formulário em `index.php`, enviando modelo, placa e tipo.
- **Alugar Veículo**: Aluga um veículo disponível, especificando dias (padrão = 1), calculando o valor com base na diária (`DIARIA_CARRO` ou `DIARIA_MOTO`).
- **Devolver Veículo**: Devolve um veículo alugado, alterando seu status para disponível.
- **Deletar Veículo**: Remove um veículo da locadora, somente para admin.
- **Calcular Previsão de Aluguel**: Calcula o custo estimado de aluguel com base no tipo de veículo e dias, disponível para ambos os perfis.

## 4. Perfis de Acesso

### 4.1. Perfil Admin
- **Acesso**: Usuário com `perfil: "admin"` (ex.: username "admin", senha "123").
- **Permissões**:
  - Todas as funcionalidades do perfil usuário.
  - Adicionar novos veículos via formulário em `index.php`.
  - Alugar, devolver e deletar veículos diretamente na tabela de veículos.
- **Interface**:
  - Na barra superior, exibe "Bem-vindo, Admin" (destacado em negrito/fundo branco).
  - Mostra seções adicionais na página principal, como o formulário "Adicionar Novo Veículo" e botões "Deletar", "Devolver" e "Alugar" na tabela de veículos.

### 4.2. Perfil Usuário
- **Acesso**: Usuário com `perfil: "usuario"` (ex.: username "usuario", senha "123").
- **Permissões**:
  - Visualizar a lista de veículos cadastrados.
  - Calcular previsão de aluguel via formulário.
  - Não tem acesso às ações administrativas (adicionar, alugar, devolver ou deletar veículos).
- **Interface**:
  - Na barra superior, exibe "Bem-vindo, Usuário" (destacado em negrito/fundo branco).
  - Não exibe o formulário "Adicionar Novo Veículo" nem os botões de ações na tabela de veículos.

### 4.3. Controle de Permissões
- A classe `Services\Auth` verifica permissões usando métodos como `isAdmin()` e `verificarLogin()`.
- Em `index.php`, ações administrativas (adicionar, alugar, devolver, deletar) são restritas a admins via:
  ```php
  if (!Auth::isAdmin()) {
      $mensagem = "Você não tem permissão para realizar esta ação.";
      goto renderizar;
  }
  ```
- O template `template.php` usa `Auth::isAdmin()` para condicionar a exibição de seções e botões.

## 5. Interface do Usuário

### 5.1. Login (`login.php`)
- Exibe um formulário simples com campos para username e senha, estilizado com Bootstrap.
- Após login bem-sucedido, redireciona para `index.php`.
- Exibe mensagens de erro (ex.: "Usuário ou senha inválidos") em caso de falha.

### 5.2. Página Principal (`index.php` e `template.php`)
- **Barra Superior**:
  - Mostra "Sistema de Locadora de Veículos" à esquerda.
  - Exibe "Bem-vindo, [username]" (com ícone de usuário e username destacado) e botão "Sair" (com ícone de seta) à direita, em uma barra preta com texto branco.
- **Seções**:
  - **Adicionar Novo Veículo**: Formulário visível apenas para admins, com campos para modelo, placa e tipo.
  - **Calcular Previsão de Aluguel**: Formulário disponível para ambos os perfis, com seleção de tipo (carro/moto) e dias.
  - **Veículos Cadastrados**: Tabela com colunas Tipo, Modelo, Placa, Status e Ações (somente para admins, com botões "Deletar", "Devolver" e "Alugar" com campo de dias).
- **Estilização**: Usa Bootstrap para layout responsivo, com Bootstrap Icons para ícones (usuário e seta no "Sair").

## 6. Fluxo de Funcionamento

### 6.1. Autenticação
1. O usuário acessa `login.php`.
2. Insere username e senha, que são validados pela classe `Auth` contra `usuarios.json`.
3. Se válido, salva os dados na sessão (`$_SESSION['auth']`) e redireciona para `index.php`.

### 6.2. Navegação e Ações
1. Em `index.php`, o sistema instancia `Locadora` e verifica o perfil do usuário.
2. Admins veem todas as funcionalidades; usuários veem apenas visualização e cálculo de previsão.
3. Ações como alugar, devolver e deletar veículos atualizam `veiculos.json` via `Locadora`.
4. Mensagens de sucesso/erro são exibidas na página via `$mensagem`.

## 7. Considerações Técnicas
- **Persistência**: Dados são salvos em `usuarios.json` e `veiculos.json` usando `json_encode()` e `file_put_contents()`.
- **Segurança**: Senhas são criptografadas com `password_hash()` e verificadas com `password_verify()`. A interface usa `htmlspecialchars()` para evitar XSS.
- **Responsividade**: O layout é responsivo com Bootstrap, ajustando-se a dispositivos móveis via media queries.
- **Manutenção**: O sistema usa autoloading do Composer para gerenciar classes, facilitando a expansão.

## 8. Exemplo de Uso
- **Login como Admin**:
  - Acesse `login.php`, insira "admin" e "123".
  - Veja a página principal com formulário para adicionar veículos, tabela com botões de ações, e previsão de aluguel.
- **Login como Usuário**:
  - Acesse `login.php`, insira "usuario" e "123".
  - Veja apenas a lista de veículos e o formulário de previsão, sem botões de ações.
