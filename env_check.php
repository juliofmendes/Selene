<?php
echo "<h1>Verificação das Variáveis de Ambiente</h1>";
echo "<p>Este script verifica os valores que o PHP está recebendo do servidor.</p>";
echo "<hr>";

$db_host = getenv('DB_HOST');
$db_name = getenv('DB_NAME');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');
$base_url = getenv('BASE_URL');

echo "<strong>DB_HOST:</strong> " . ($db_host ? htmlspecialchars($db_host) : '<em>NÃO DEFINIDO</em>') . "<br>";
echo "<strong>DB_NAME:</strong> " . ($db_name ? htmlspecialchars($db_name) : '<em>NÃO DEFINIDO</em>') . "<br>";
echo "<strong>DB_USER:</strong> " . ($db_user ? htmlspecialchars($db_user) : '<em>NÃO DEFINIDO</em>') . "<br>";
echo "<strong>DB_PASS:</strong> " . ($db_pass ? '******** (Definido, mas oculto por segurança)' : '<em>NÃO DEFINIDO</em>') . "<br>";
echo "<strong>BASE_URL:</strong> " . ($base_url ? htmlspecialchars($base_url) : '<em>NÃO DEFINIDO</em>') . "<br>";

echo "<hr>";
echo "<p>Se algum valor acima estiver como 'NÃO DEFINIDO', significa que o arquivo .htaccess não está sendo processado corretamente para esta pasta.</p>";
?>