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
            $this->usuarios = is_array($conteudo) && count($conteudo) > 0
                ? $conteudo
                : $this->criarUsuariosPadrao();
        } else {
            $this->usuarios = $this->criarUsuariosPadrao();
        }
    }
 
    private function criarUsuariosPadrao(): array {
        $usuarios = [
            ['username' => 'admin',   'password' => password_hash('admin123', PASSWORD_DEFAULT), 'perfil' => 'admin'],
            ['username' => 'usuario', 'password' => password_hash('user123',  PASSWORD_DEFAULT), 'perfil' => 'usuario'],
        ];
        $this->usuarios = $usuarios;
        $this->salvarUsuarios();
        return $usuarios;
    }
 
    private function salvarUsuarios(): void {
        $dir = dirname(ARQUIVO_USUARIOS);
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        file_put_contents(ARQUIVO_USUARIOS, json_encode($this->usuarios, JSON_PRETTY_PRINT));
    }
 
    public function login(string $username, string $password): bool {
        foreach ($this->usuarios as $usuario) {
            if ($usuario['username'] === $username &&
                password_verify($password, $usuario['password'])) {
                $_SESSION['auth'] = [
                    'logado'   => true,
                    'username' => $username,
                    'perfil'   => $usuario['perfil'],
                ];
                return true;
            }
        }
        return false;
    }
 
    public function logout(): void { session_destroy(); }
 
    public static function verificarLogin(): bool {
        return isset($_SESSION['auth']) && $_SESSION['auth']['logado'] === true;
    }
 
    public static function isPerfil(string $perfil): bool {
        return isset($_SESSION['auth']) && $_SESSION['auth']['perfil'] === $perfil;
    }
 
    public static function isAdmin(): bool { return self::isPerfil('admin'); }
 
    public static function getUsuario(): ?array { return $_SESSION['auth'] ?? null; }
}
 