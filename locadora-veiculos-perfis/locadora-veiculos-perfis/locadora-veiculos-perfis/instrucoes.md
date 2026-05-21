# Desenvolvimento do Sistema de Locadora de Veículos com PHP e Bootstrap

Este documento descreve a sequência de criação para o desenvolvimento de um sistema de locadora de veículos usando PHP, com autenticação de usuários (admin e usuário), gerenciamento de veículos (carros e motos), e interface baseada em Bootstrap e Bootstrap Icons.

## 1. Estrutura Inicial do Projeto

### Passo 1: Configuração do Ambiente
- **Ferramentas necessárias**:
  - Servidor web (Apache/Nginx) com PHP 7.4 ou superior.
  - Composer para gerenciar dependências.
  - Editor de código (VS Code, PhpStorm, etc.).

### Passo 2: Estrutura de Pastas
Crie a seguinte estrutura de diretórios no diretório raiz do projeto (por exemplo, `C:\Apache\htdocs\mini-exercicios\locadora-veiculos-perfis\`):
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
└── vendor/ (gerado pelo Composer)
```

### Passo 3: Configuração do Composer
- Crie o arquivo `composer.json` na raiz do projeto:
  ```json
  {
      "name": "locadora/veiculos",
      "description": "Sistema de Locadora de Veículos",
      "type": "project",
      "autoload": {
          "psr-4": {
              "Interfaces\\": "interfaces/",
              "Models\\": "models/",
              "Services\\": "services/"
          }
      },
      "require": {
          "php": ">=7.4"
      }
  }
  ```
- No terminal, navegue até `locadora-veiculos-perfis/` e execute:
  ```bash
  composer install
  ```
  Isso cria o diretório `vendor/` e o arquivo `vendor/autoload.php` para autoload das classes.

## 2. Criação dos Arquivos Principais

### Passo 4: Configuração (`config.php`)
Crie o arquivo `config/config.php` com as constantes do sistema:
```php
<?php
// Arquivo de configuração com constantes do sistema
define('ARQUIVO_JSON', __DIR__ . '/../data/veiculos.json');
define('ARQUIVO_USUARIOS', __DIR__ . '/../data/usuarios.json');
define('DIARIA_CARRO', 100.00);
define('DIARIA_MOTO', 50.00);
```

### Passo 5: Arquivos JSON de Dados
- Crie o diretório `data/` e os arquivos `usuarios.json` e `veiculos.json`:
  - **`usuarios.json`**:
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
    *Nota*: Gere os hashes para a senha "123" usando `password_hash("123", PASSWORD_DEFAULT)` no PHP.
  - **`veiculos.json`**:
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

### Passo 6: Interfaces e Modelos
Crie os arquivos nas pastas correspondentes:

- **`interfaces/Locavel.php`**:
  ```php
  <?php
  namespace Interfaces;

  /**
   * Interface que define os métodos necessários para um veículo ser locável
   */
  interface Locavel {
      public function alugar(): string;
      public function devolver(): string;
      public function isDisponivel(): bool;
  }
  ```

- **`models/Veiculo.php`**:
  ```php
  <?php
  namespace Models;

  /**
   * Classe abstrata base para todos os tipos de veículos
   */
  abstract class Veiculo {
      protected string $modelo;
      protected string $placa;
      protected bool $disponivel;

      public function __construct(string $modelo, string $placa) {
          $this->modelo = $modelo;
          $this->placa = $placa;
          $this->disponivel = true;
      }

      /**
       * Calcula o valor do aluguel baseado na quantidade de dias
       */
      abstract public function calcularAluguel(int $dias): float;

      public function isDisponivel(): bool {
          return $this->disponivel;
      }

      public function getModelo(): string {
          return $this->modelo;
      }

      public function getPlaca(): string {
          return $this->placa;
      }

      public function setDisponivel(bool $disponivel): void {
          $this->disponivel = $disponivel;
      }
  }
  ```

- **`models/Carro.php`**:
  ```php
  <?php
  namespace Models;
  use Interfaces\Locavel;

  /**
   * Classe que representa um Carro no sistema
   */
  class Carro extends Veiculo implements Locavel {
      public function calcularAluguel(int $dias): float {
          return $dias * DIARIA_CARRO;
      }

      public function alugar(): string {
          if ($this->disponivel) {
              $this->disponivel = false;
              return "Carro '{$this->modelo}' alugado com sucesso!";
          }
          return "Carro '{$this->modelo}' não está disponível.";
      }

      public function devolver(): string {
          if (!$this->disponivel) {
              $this->disponivel = true;
              return "Carro '{$this->modelo}' devolvido com sucesso!";
          }
          return "Carro '{$this->modelo}' já está na locadora.";
      }
  }
  ```

- **`models/Moto.php`**:
  ```php
  <?php
  namespace Models;
  use Interfaces\Locavel;

  /**
   * Classe que representa uma Moto no sistema
   */
  class Moto extends Veiculo implements Locavel {
      public function calcularAluguel(int $dias): float {
          return $dias * DIARIA_MOTO;
      }

      public function alugar(): string {
          if ($this->disponivel) {
              $this->disponivel = false;
              return "Moto '{$this->modelo}' alugada com sucesso!";
          }
          return "Moto '{$this->modelo}' não está disponível.";
      }

      public function devolver(): string {
          if (!$this->disponivel) {
              $this->disponivel = true;
              return "Moto '{$this->modelo}' devolvida com sucesso!";
          }
          return "Moto '{$this->modelo}' já está na locadora.";
      }
  }
  ```

### Passo 7: Serviços
Crie os arquivos nas pastas correspondentes:

- **`services/Auth.php`**:
  ```php
  <?php
  namespace Services;

  class Auth {
      private array $usuarios = [];
      
      public function __construct() {
          $this->carregarUsuarios();
      }
      
      private function carregarUsuarios(): void {
          if (file_exists(ARQUIVO_USUARIOS)) {
              $conteudo = json_decode(file_get_contents(ARQUIVO_USUARIOS), true);
              $this->usuarios = is_array($conteudo) ? $conteudo : [];
          } else {
              $this->usuarios = [
                  [
                      'username' => 'admin',
                      'password' => password_hash('admin123', PASSWORD_DEFAULT),
                      'perfil' => 'admin'
                  ],
                  [
                      'username' => 'usuario',
                      'password' => password_hash('user123', PASSWORD_DEFAULT),
                      'perfil' => 'usuario'
                  ]
              ];
              $this->salvarUsuarios();
          }
      }
      
      private function salvarUsuarios(): void {
          $dir = dirname(ARQUIVO_USUARIOS);
          if (!is_dir($dir)) {
              mkdir($dir, 0777, true);
          }
          file_put_contents(ARQUIVO_USUARIOS, json_encode($this->usuarios, JSON_PRETTY_PRINT));
      }
      
      public function login(string $username, string $password): bool {
          foreach ($this->usuarios as $usuario) {
              if ($usuario['username'] === $username && 
                  password_verify($password, $usuario['password'])) {
                  $_SESSION['auth'] = [
                      'logado' => true,
                      'username' => $username,
                      'perfil' => $usuario['perfil']
                  ];
                  return true;
              }
          }
          return false;
      }
      
      public function logout(): void {
          session_destroy();
      }
      
      public static function verificarLogin(): bool {
          return isset($_SESSION['auth']) && $_SESSION['auth']['logado'] === true;
      }
      
      public static function isPerfil(string $perfil): bool {
          return isset($_SESSION['auth']) && $_SESSION['auth']['perfil'] === $perfil;
      }
      
      public static function isAdmin(): bool {
          return self::isPerfil('admin');
      }
      
      public static function getUsuario(): ?array {
          return $_SESSION['auth'] ?? null;
      }
  }
  ```

- **`services/Locadora.php`**:
  ```php
  <?php
  namespace Services;
  use Models\{Veiculo, Carro, Moto};

  /**
   * Classe responsável por gerenciar as operações da locadora
   */
  class Locadora {
      private array $veiculos = [];

      public function __construct() {
          $this->carregarVeiculos();
      }

      /**
       * Carrega os veículos do arquivo JSON
       */
      private function carregarVeiculos(): void {
          if (file_exists(ARQUIVO_JSON)) {
              $dados = json_decode(file_get_contents(ARQUIVO_JSON), true);
              foreach ($dados as $dado) {
                  if ($dado['tipo'] === 'Carro') {
                      $veiculo = new Carro($dado['modelo'], $dado['placa']);
                  } else {
                      $veiculo = new Moto($dado['modelo'], $dado['placa']);
                  }
                  $veiculo->setDisponivel($dado['disponivel']);
                  $this->veiculos[] = $veiculo;
              }
          }
      }

      /**
       * Salva os veículos no arquivo JSON
       */
      private function salvarVeiculos(): void {
          $dados = [];
          foreach ($this->veiculos as $veiculo) {
              $dados[] = [
                  'tipo' => ($veiculo instanceof Carro) ? 'Carro' : 'Moto',
                  'modelo' => $veiculo->getModelo(),
                  'placa' => $veiculo->getPlaca(),
                  'disponivel' => $veiculo->isDisponivel()
              ];
          }
          
          $dir = dirname(ARQUIVO_JSON);
          if (!is_dir($dir)) {
              mkdir($dir, 0777, true);
          }
          
          file_put_contents(ARQUIVO_JSON, json_encode($dados, JSON_PRETTY_PRINT));
      }

      /**
       * Adiciona um novo veículo à locadora
       */
      public function adicionarVeiculo(Veiculo $veiculo): void {
          $this->veiculos[] = $veiculo;
          $this->salvarVeiculos();
      }

      /**
       * Remove um veículo da locadora
       */
      public function deletarVeiculo(string $modelo, string $placa): string {
          foreach ($this->veiculos as $key => $veiculo) {
              if ($veiculo->getModelo() === $modelo && $veiculo->getPlaca() === $placa) {
                  unset($this->veiculos[$key]);
                  $this->veiculos = array_values($this->veiculos);
                  $this->salvarVeiculos();
                  return "Veículo '{$modelo}' removido com sucesso!";
              }
          }
          return "Veículo não encontrado.";
      }

      /**
       * Aluga um veículo por um número específico de dias
       */
      public function alugarVeiculo(string $modelo, int $dias = 1): string {
          foreach ($this->veiculos as $veiculo) {
              if ($veiculo->getModelo() === $modelo && $veiculo->isDisponivel()) {
                  $valorAluguel = $veiculo->calcularAluguel($dias);
                  $mensagem = $veiculo->alugar();
                  $this->salvarVeiculos();
                  return $mensagem . " Valor do aluguel: R$ " . number_format($valorAluguel, 2, ',', '.');
              }
          }
          return "Veículo não disponível.";
      }

      /**
       * Devolve um veículo alugado
       */
      public function devolverVeiculo(string $modelo): string {
          foreach ($this->veiculos as $veiculo) {
              if ($veiculo->getModelo() === $modelo && !$veiculo->isDisponivel()) {
                  $mensagem = $veiculo->devolver();
                  $this->salvarVeiculos();
                  return $mensagem;
              }
          }
          return "Veículo não encontrado ou já está disponível.";
      }

      /**
       * Retorna a lista de todos os veículos
       */
      public function listarVeiculos(): array {
          return $this->veiculos;
      }

      /**
       * Calcula uma previsão de valor do aluguel
       */
      public function calcularPrevisaoAluguel(string $tipo, int $dias): float {
          if ($tipo === 'Carro') {
              return (new Carro('', ''))->calcularAluguel($dias);
          }
          return (new Moto('', ''))->calcularAluguel($dias);
      }
  }
  ```

### Passo 8: Páginas Públicas
Crie os arquivos nas pastas correspondentes:

- **`public/index.php`**:
  ```php
  <?php
  require_once __DIR__ . '/../vendor/autoload.php';
  require_once __DIR__ . '/../config/config.php';

  session_start();

  use Services\{Locadora, Auth};
  use Models\{Carro, Moto};

  // Verificar se está logado
  if (!Auth::verificarLogin()) {
      header('Location: login.php');
      exit;
  }

  // Processar logout
  if (isset($_GET['logout'])) {
      (new Auth())->logout();
      header('Location: login.php');
      exit;
  }

  // Instancia a Locadora
  $locadora = new Locadora();
  $mensagem = '';
  $usuario = Auth::getUsuario();

  // Processar requisições
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Verificar permissões para ações administrativas
      if (isset($_POST['adicionar']) || isset($_POST['deletar']) || 
          isset($_POST['alugar']) || isset($_POST['devolver'])) {
          if (!Auth::isAdmin()) {
              $mensagem = "Você não tem permissão para realizar esta ação.";
              goto renderizar;
          }
      }

      if (isset($_POST['adicionar'])) {
          $modelo = $_POST['modelo'];
          $placa = $_POST['placa'];
          $tipo = $_POST['tipo'];

          $veiculo = ($tipo == 'Carro') ? new Carro($modelo, $placa) : new Moto($modelo, $placa);
          $locadora->adicionarVeiculo($veiculo);
          $mensagem = "Veículo adicionado com sucesso!";
      } elseif (isset($_POST['alugar'])) {
          $dias = isset($_POST['dias']) ? (int)$_POST['dias'] : 1;
          $mensagem = $locadora->alugarVeiculo($_POST['modelo'], $dias);
      } elseif (isset($_POST['devolver'])) {
          $mensagem = $locadora->devolverVeiculo($_POST['modelo']);
      } elseif (isset($_POST['deletar'])) {
          $mensagem = $locadora->deletarVeiculo($_POST['modelo'], $_POST['placa']);
      } elseif (isset($_POST['calcular'])) {
          $dias = (int)$_POST['dias_calculo'];
          $tipo = $_POST['tipo_calculo'];
          $valor = $locadora->calcularPrevisaoAluguel($tipo, $dias);
          $mensagem = "Previsão de valor para {$dias} dias: R$ " . number_format($valor, 2, ',', '.');
      }
  }

  renderizar:
  // Inclui o template da view
  require_once __DIR__ . '/../views/template.php';
  ```

- **`public/login.php`**:
  ```php
  <?php
  require_once __DIR__ . '/../vendor/autoload.php';
  require_once __DIR__ . '/../config/config.php';

  session_start();

  use Services\Auth;

  $mensagem = '';
  $auth = new Auth();

  // Se já estiver logado, redireciona para index
  if (Auth::verificarLogin()) {
      header('Location: index.php');
      exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = $_POST['username'] ?? '';
      $password = $_POST['password'] ?? '';
      
      if ($auth->login($username, $password)) {
          header('Location: index.php');
          exit;
      } else {
          $mensagem = 'Usuário ou senha inválidos';
      }
  }
  ?>

  <!DOCTYPE html>
  <html lang="pt-BR">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Login - Sistema de Locadora</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <style>
          .login-container {
              max-width: 400px;
              margin: 100px auto;
          }
      </style>
  </head>
  <body class="bg-light">
      <div class="login-container">
          <div class="card">
              <div class="card-header">
                  <h4 class="mb-0">Login</h4>
              </div>
              <div class="card-body">
                  <?php if ($mensagem): ?>
                      <div class="alert alert-danger"><?= htmlspecialchars($mensagem) ?></div>
                  <?php endif; ?>
                  
                  <form method="post" class="needs-validation" novalidate>
                      <div class="mb-3">
                          <label class="form-label">Usuário</label>
                          <input type="text" name="username" class="form-control" required>
                      </div>
                      <div class="mb-3">
                          <label class="form-label">Senha</label>
                          <input type="password" name="password" class="form-control" required>
                      </div>
                      <button type="submit" class="btn btn-primary w-100">Entrar</button>
                  </form>
              </div>
          </div>
      </div>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
  </html>
  ```

### Passo 9: Template Principal (`views/template.php`)
Crie o arquivo `views/template.php` com o layout final:
```php
<?php
require_once __DIR__ . '/../services/Auth.php';

use Services\Auth;

session_start(); // Certifique-se de iniciar a sessão

$usuario = Auth::getUsuario(); // Obtém os dados do usuário logado

/**
 * Template principal do sistema de locadora de veículos
 * Recebe as variáveis $locadora, $mensagem e $usuario do controller (index.php)
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Locadora de Veículos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .action-wrapper {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            justify-content: flex-start;
        }
        .btn-group-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        .rent-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            order: 2;
        }
        .delete-btn {
            order: 1;
        }
        .days-input {
            width: 60px !important;
            padding: 0.25rem 0.5rem;
            text-align: center;
        }
        @media (max-width: 768px) {
            .action-wrapper {
                flex-direction: column;
                align-items: stretch;
            }
            .btn-group-actions {
                flex-direction: column;
            }
            .rent-group {
                order: 1;
                width: 100%;
            }
            .delete-btn {
                order: 2;
                width: 100%;
            }
            .days-input {
                width: 100% !important;
            }
        }
        /* Estilos para a barra de usuário */
        .user-info {
            background-color: #000; /* Cor preta, como na imagem */
            padding: 0.5rem 1rem;
            border-radius: 4px;
            color: #fff; /* Texto branco */
        }
        .user-icon i {
            color: #fff; /* Ícone branco */
        }
        .welcome-text {
            margin-right: 1rem;
            font-size: 1rem;
        }
        .welcome-text strong {
            background-color: #fff; /* Fundo branco para o username, como na imagem */
            color: #000; /* Texto preto */
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            display: inline-block;
        }
        .btn-outline-danger {
            border-color: #fff; /* Borda branca para o botão Sair */
            color: #fff; /* Texto branco */
        }
        .btn-outline-danger:hover {
            background-color: #fff; /* Fundo branco ao passar o mouse */
            color: #dc3545; /* Cor do texto ao hover (vermelho Bootstrap danger) */
            border-color: #fff;
        }
    </style>
</head>
<body class="container py-4">
    <div class="container py-4">
        <!-- Barra superior com informações do usuário -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1>Sistema de Locadora de Veículos</h1>
                    <div class="d-flex align-items-center gap-3 user-info">
                        <!-- Ícone de usuário usando Bootstrap Icons -->
                        <span class="user-icon">
                            <i class="bi bi-person-circle" style="font-size: 24px;"></i>
                        </span>
                        <!-- Texto "Bem-vindo, [username]" -->
                        <span class="welcome-text">Bem-vindo, <strong><?= htmlspecialchars($usuario['username']) ?></strong></span>
                        <!-- Botão Sair com ícone usando Bootstrap Icons -->
                        <a href="?logout=1" class="btn btn-outline-danger d-flex align-items-center gap-1">
                            <i class="bi bi-box-arrow-right"></i>
                            Sair
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($mensagem): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($mensagem) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="row same-height-row">
            <?php if (Auth::isAdmin()): ?>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="mb-0">Adicionar Novo Veículo</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label class="form-label">Modelo</label>
                                <input type="text" name="modelo" class="form-control" required>
                                <div class="invalid-feedback">Informe um modelo válido.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Placa</label>
                                <input type="text" name="placa" class="form-control" required>
                                <div class="invalid-feedback">Informe uma placa válida.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipo</label>
                                <select name="tipo" class="form-select" required>
                                    <option value="Carro">Carro</option>
                                    <option value="Moto">Moto</option>
                                </select>
                            </div>
                            <button type="submit" name="adicionar" class="btn btn-primary w-100">Adicionar Veículo</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <div class="col-<?= Auth::isAdmin() ? 'md-6' : '12' ?>">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="mb-0">Calcular Previsão de Aluguel</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label class="form-label">Tipo de Veículo</label>
                                <select name="tipo_calculo" class="form-select" required>
                                    <option value="Carro">Carro</option>
                                    <option value="Moto">Moto</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Quantidade de Dias</label>
                                <input type="number" name="dias_calculo" class="form-control" value="1" required>
                            </div>
                            <button type="submit" name="calcular" class="btn btn-info w-100">Calcular Previsão</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Veículos Cadastrados</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Modelo</th>
                                        <th>Placa</th>
                                        <th>Status</th>
                                        <?php if (Auth::isAdmin()): ?>
                                        <th>Ações</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($locadora->listarVeiculos() as $veiculo): ?>
                                    <tr>
                                        <td><?= $veiculo instanceof \Models\Carro ? 'Carro' : 'Moto' ?></td>
                                        <td><?= htmlspecialchars($veiculo->getModelo()) ?></td>
                                        <td><?= htmlspecialchars($veiculo->getPlaca()) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $veiculo->isDisponivel() ? 'success' : 'warning' ?>">
                                                <?= $veiculo->isDisponivel() ? 'Disponível' : 'Alugado' ?>
                                            </span>
                                        </td>
                                        <?php if (Auth::isAdmin()): ?>
                                        <td>
                                            <div class="action-wrapper">
                                                <form method="post" class="btn-group-actions">
                                                    <input type="hidden" name="modelo" value="<?= htmlspecialchars($veiculo->getModelo()) ?>">
                                                    <input type="hidden" name="placa" value="<?= htmlspecialchars($veiculo->getPlaca()) ?>">
                                                    
                                                    <!-- Botão Deletar (sempre disponível para admin) -->
                                                    <button type="submit" name="deletar" class="btn btn-danger btn-sm delete-btn">Deletar</button>
                                                    
                                                    <!-- Botões condicionais baseados no status do veículo -->
                                                    <div class="rent-group">
                                                        <?php if (!$veiculo->isDisponivel()): ?>
                                                            <!-- Veículo alugado: Botão Devolver -->
                                                            <button type="submit" name="devolver" class="btn btn-warning btn-sm">Devolver</button>
                                                        <?php else: ?>
                                                            <!-- Veículo disponível: Campo de dias e Botão Alugar -->
                                                            <input type="number" name="dias" class="form-control days-input" value="1" min="1" required>
                                                            <button type="submit" name="alugar" class="btn btn-primary btn-sm">Alugar</button>
                                                        <?php endif; ?>
                                                    </div>
                                                </form>
                                            </div>
                                        </td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

## 3. Teste e Validação
- Acesse `http://localhost/mini-exercicios/locadora-veiculos-perfis/public/login.php` no navegador.
- Faça login com:
  - Usuário "admin" e senha "123" (perfil admin, com acesso total).
  - Usuário "usuario" e senha "123" (perfil usuário, sem acesso a ações administrativas).
- Teste as funcionalidades:
  - Adicionar novos veículos (somente admin).
  - Alugar veículos disponíveis (com número de dias).
  - Devolver veículos alugados.
  - Deletar veículos (somente admin).
  - Calcular previsão de aluguel.
- Verifique se as mudanças são salvas em `veiculos.json` e `usuarios.json`.

## 4. Estado Final do Projeto
O sistema final inclui:
- Autenticação com dois perfis: "admin" (com acesso total) e "usuario" (somente visualização).
- Gerenciamento de veículos (carros e motos) com aluguel, devolução e exclusão.
- Interface amigável com Bootstrap, incluindo ícones do Bootstrap Icons na barra superior.
- Persistência de dados em arquivos JSON (`usuarios.json` e `veiculos.json`).
- Layout responsivo e estilizado, com barra superior preta, username destacado, e botões funcionais na tabela de veículos.

