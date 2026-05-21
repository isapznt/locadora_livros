<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';
 
session_start();
 
use Services\Auth;
 
$mensagem = '';
$auth     = new Auth();
 
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
        $mensagem = 'Usuário ou senha inválidos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Locadora de Livros</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lato:wght@300;400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --cream:  #f5f0e8;
            --brown:  #3b2a1a;
            --gold:   #c8a84b;
            --rust:   #8b3a2a;
        }
        body {
            background-color: var(--cream);
            font-family: 'Lato', sans-serif;
            font-weight: 300;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border: 1px solid #d9cfc0;
            border-top: 4px solid var(--gold);
            border-radius: 2px;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 32px rgba(59,42,26,.10);
        }
        .brand-title {
            font-family: 'Playfair Display', serif;
            color: var(--brown);
            font-size: 1.75rem;
            margin-bottom: .25rem;
        }
        .brand-sub {
            color: var(--gold);
            font-size: .8rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            margin-bottom: 1.75rem;
        }
        .form-label { color: var(--brown); font-weight: 400; font-size: .9rem; }
        .form-control {
            border-radius: 2px;
            border-color: #d9cfc0;
            background: var(--cream);
            color: var(--brown);
        }
        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 .2rem rgba(200,168,75,.2);
            background: #fff;
        }
        .btn-login {
            background: var(--brown);
            color: var(--cream);
            border: none;
            border-radius: 2px;
            letter-spacing: .08em;
            font-weight: 400;
            transition: background .2s;
        }
        .btn-login:hover { background: var(--rust); color: #fff; }
        .alert-danger { border-radius: 2px; font-size: .9rem; }
        .hint { font-size: .78rem; color: #9a8a78; margin-top: 1.25rem; line-height: 1.6; }
        .hint strong { color: var(--brown); }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-title"><i class="bi bi-book-half me-2" style="color:var(--gold)"></i>Livraria Locus</div>
        <div class="brand-sub">Sistema de Locação de Livros</div>
 
        <?php if ($mensagem): ?>
            <div class="alert alert-danger py-2"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>
 
        <form method="post" novalidate>
            <div class="mb-3">
                <label class="form-label">Usuário</label>
                <input type="text" name="username" class="form-control" placeholder="Digite seu usuário" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Senha</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-login w-100 mt-1 py-2">Entrar</button>
        </form>
 
        <p class="hint">
            <strong>Admin:</strong> usuário <code>admin</code> / senha <code>admin123</code><br>
            <strong>Usuário:</strong> usuário <code>usuario</code> / senha <code>user123</code>
        </p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>