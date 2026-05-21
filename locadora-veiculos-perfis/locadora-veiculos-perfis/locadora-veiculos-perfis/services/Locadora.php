<?php
namespace Services;
use Models\{Livro, LivroComum, LivroRaro};
 
/**
 * Classe responsável por gerenciar as operações da locadora de livros
 */
class Locadora {
    private array $livros = [];
 
    public function __construct() {
        $this->carregarLivros();
    }
 
    private function carregarLivros(): void {
        if (file_exists(ARQUIVO_JSON)) {
            $dados = json_decode(file_get_contents(ARQUIVO_JSON), true);
            if (!is_array($dados)) return;
            foreach ($dados as $dado) {
                $livro = ($dado['tipo'] === 'Raro')
                    ? new LivroRaro($dado['titulo'], $dado['autor'], $dado['isbn'])
                    : new LivroComum($dado['titulo'], $dado['autor'], $dado['isbn']);
                $livro->setDisponivel($dado['disponivel']);
                $this->livros[] = $livro;
            }
        }
    }
 
    private function salvarLivros(): void {
        $dados = [];
        foreach ($this->livros as $livro) {
            $dados[] = [
                'tipo'       => ($livro instanceof LivroRaro) ? 'Raro' : 'Comum',
                'titulo'     => $livro->getTitulo(),
                'autor'      => $livro->getAutor(),
                'isbn'       => $livro->getIsbn(),
                'disponivel' => $livro->isDisponivel(),
            ];
        }
        $dir = dirname(ARQUIVO_JSON);
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        file_put_contents(ARQUIVO_JSON, json_encode($dados, JSON_PRETTY_PRINT));
    }
 
    public function adicionarLivro(Livro $livro): void {
        $this->livros[] = $livro;
        $this->salvarLivros();
    }
 
    public function deletarLivro(string $titulo, string $isbn): string {
        foreach ($this->livros as $key => $livro) {
            if ($livro->getTitulo() === $titulo && $livro->getIsbn() === $isbn) {
                unset($this->livros[$key]);
                $this->livros = array_values($this->livros);
                $this->salvarLivros();
                return "Livro '{$titulo}' removido com sucesso!";
            }
        }
        return "Livro não encontrado.";
    }
 
    public function alugarLivro(string $titulo, string $isbn, int $dias = 1): string {
        foreach ($this->livros as $livro) {
            if ($livro->getTitulo() === $titulo && $livro->getIsbn() === $isbn && $livro->isDisponivel()) {
                $valor    = $livro->calcularAluguel($dias);
                $mensagem = $livro->alugar();
                $this->salvarLivros();
                return $mensagem . " Valor: R$ " . number_format($valor, 2, ',', '.');
            }
        }
        return "Livro não disponível para aluguel.";
    }
 
    public function devolverLivro(string $titulo, string $isbn): string {
        foreach ($this->livros as $livro) {
            if ($livro->getTitulo() === $titulo && $livro->getIsbn() === $isbn && !$livro->isDisponivel()) {
                $mensagem = $livro->devolver();
                $this->salvarLivros();
                return $mensagem;
            }
        }
        return "Livro não encontrado ou já está disponível.";
    }
 
    public function listarLivros(): array { return $this->livros; }
 
    public function calcularPrevisaoAluguel(string $tipo, int $dias): float {
        return ($tipo === 'Raro')
            ? (new LivroRaro('', '', ''))->calcularAluguel($dias)
            : (new LivroComum('', '', ''))->calcularAluguel($dias);
    }
}