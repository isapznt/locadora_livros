<?php
$senha = "123";
$hash_admin = password_hash($senha, PASSWORD_DEFAULT);
$hash_usuario = password_hash($senha, PASSWORD_DEFAULT);

echo "Hash para admin: " . $hash_admin . "\n";
echo "Hash para usuario: " . $hash_usuario . "\n";