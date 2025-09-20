<?php
// A PRIMEIRA LINHA: Invoca o guardião. Segurança antes de tudo.
require_once '../auth/verifica_sessao.php';

// Inclui o cabeçalho do nosso layout
require_once '../components/header.php'; 
?>

<h1>Dashboard do Psicólogo</h1>
<p>Bem-vindo(a) de volta, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</p>
<p>Este é o seu centro de comando. Em breve, aqui você verá seus próximos agendamentos e pacientes recentes.</p>

<?php
// Inclui o rodapé do nosso layout
// Criaremos o footer.php no próximo passo, por enquanto o fechamento é manual.
?>
    </main>
</body>
</html>