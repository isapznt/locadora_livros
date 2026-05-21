<?php
namespace Models;
use Interfaces\Locavel;
 
/**
 * Classe que representa um Livro Raro na locadora (tarifa diferenciada)
 */
class LivroRaro extends Livro implements Locavel {
 
    public function calcularAluguel(int $dias): float {
        return $dias * DIARIA_RARO;
    }
 
    public function alugar(): string {
        if ($this->disponivel) {
            $this->disponivel = false;
            return "Livro raro '{$this->titulo}' alugado com sucesso!";
        }
        return "Livro raro '{$this->titulo}' não está disponível.";
    }
 
    public function devolver(): string {
        if (!$this->disponivel) {
            $this->disponivel = true;
            return "Livro raro '{$this->titulo}' devolvido com sucesso!";
        }
        return "Livro raro '{$this->titulo}' já está na locadora.";
    }
}