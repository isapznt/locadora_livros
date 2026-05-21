<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';
 
session_start();
 
use Services\{Locadora, Auth};
use Models\{LivroComum, LivroRaro};
 
// Verificar login
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
 
$locadora = new Locadora();
$mensagem = '';
$usuario  = Auth::getUsuario();
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Somente admin pode realizar ações de gestão
    if (isset($_POST['adicionar'], $_POST['deletar']) || isset($_POST['alugar']) || isset($_POST['devolver']) || isset($_POST['deletar'])) {
        if (!Auth::isAdmin()) {
            $mensagem = "Você não tem permissão para realizar esta ação.";
            goto renderizar;
        }
    }
 
    if (isset($_POST['adicionar'])) {
        $titulo = trim($_POST['titulo']);
        $autor  = trim($_POST['autor']);
        $isbn   = trim($_POST['isbn']);
        $tipo   = $_POST['tipo'];
 
        $livro = ($tipo === 'Raro')
            ? new LivroRaro($titulo, $autor, $isbn)
            : new LivroComum($titulo, $autor, $isbn);
 
        $locadora->adicionarLivro($livro);
        $mensagem = "Livro adicionado com sucesso!";
 
    } elseif (isset($_POST['alugar'])) {
        $dias     = isset($_POST['dias']) ? max(1, (int)$_POST['dias']) : 1;
        $mensagem = $locadora->alugarLivro($_POST['titulo'], $_POST['isbn'], $dias);
 
    } elseif (isset($_POST['devolver'])) {
        $mensagem = $locadora->devolverLivro($_POST['titulo'], $_POST['isbn']);
 
    } elseif (isset($_POST['deletar'])) {
        $mensagem = $locadora->deletarLivro($_POST['titulo'], $_POST['isbn']);
 
    } elseif (isset($_POST['calcular'])) {
        $dias  = max(1, (int)$_POST['dias_calculo']);
        $tipo  = $_POST['tipo_calculo'];
        $valor = $locadora->calcularPrevisaoAluguel($tipo, $dias);
        $mensagem = "Previsão de valor ({$tipo}) para {$dias} dia(s): R$ " . number_format($valor, 2, ',', '.');
    }
}
 
renderizar:
require_once __DIR__ . '/../views/template.php';
 