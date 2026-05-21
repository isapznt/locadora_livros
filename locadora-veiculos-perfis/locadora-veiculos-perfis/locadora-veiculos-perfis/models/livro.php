<?php
namespace Models;
 
/**
 * Classe abstrata base para todos os tipos de livros
 */
abstract class Livro {
    protected string $titulo;
    protected string $autor;
    protected string $isbn;
    protected bool   $disponivel;
 
    public function __construct(string $titulo, string $autor, string $isbn) {
        $this->titulo    = $titulo;
        $this->autor     = $autor;
        $this->isbn      = $isbn;
        $this->disponivel = true;
    }
 
    /**
     * Calcula o valor do aluguel baseado na quantidade de dias
     */
    abstract public function calcularAluguel(int $dias): float;
 
    public function isDisponivel(): bool  { return $this->disponivel; }
    public function getTitulo(): string   { return $this->titulo; }
    public function getAutor(): string    { return $this->autor; }
    public function getIsbn(): string     { return $this->isbn; }
 
    public function setDisponivel(bool $disponivel): void {
        $this->disponivel = $disponivel;
    }
}