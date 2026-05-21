<?php
namespace Models;
use Interfaces\Locavel;
 
/**
 * Classe que representa um Livro Comum na locadora
 */
class LivroComum extends Livro implements Locavel {
 
    public function calcularAluguel(int $dias): float {
        return $dias * DIARIA_COMUM;
    }
 
    public function alugar(): string {
        if ($this->disponivel) {
            $this->disponivel = false;
            return "Livro '{$this->titulo}' alugado com sucesso!";
        }
        return "Livro '{$this->titulo}' não está disponível.";
    }
 
    public function devolver(): string {
        if (!$this->disponivel) {
            $this->disponivel = true;
            return "Livro '{$this->titulo}' devolvido com sucesso!";
        }
        return "Livro '{$this->titulo}' já está na locadora.";
    }
}
 