<?php
// --- APENAS CREDENCIAIS ---
$db_host = "localhost";
$db_name = "u481416738_Selene";
$db_user = "u481416738_SeleneAdm";
$db_pass = "3pHjgnpS|";

// --- APENAS O TESTE ---
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    // Se chegar aqui, a conexão foi um sucesso.
    die("<h1>SUCESSO!</h1> <p>A conexão com o banco de dados '" . htmlspecialchars($db_name) . "' foi estabelecida com sucesso.</p>");
} catch (PDOException $e) {
    // Se a conexão falhar, o script morre aqui.
    die("<h1>FALHA NA CONEXÃO.</h1> <p>O servidor retornou o seguinte erro: " . $e->getMessage() . "</p>");
}
?>