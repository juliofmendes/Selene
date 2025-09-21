<?php
require_once '../auth/verifica_sessao.php';
autorizar(['secretaria', 'admin']);
require_once '../config.php';
require_once '../components/header.php';
?>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Agenda da Clínica</h1>
        <a href="<?php echo BASE_URL; ?>/agendamentos/novo.php" class="button">Novo Agendamento</a>
    </div>

    <?php if (isset($_GET['sucesso_agendamento'])): ?>
        <div class="alert-sucesso">Novo agendamento criado com sucesso!</div>
    <?php elseif (isset($_GET['sucesso_edicao'])): ?>
        <div class="alert-sucesso">Agendamento atualizado com sucesso!</div>
    <?php endif; ?>

    <div class="card">
        <div id='calendario'></div>
    </div>
</div>

<script>
  // 3. O script que inicializa e configura o calendário
  document.addEventListener('DOMContentLoaded', function() {
    const calendarioEl = document.getElementById('calendario');
    const calendario = new FullCalendar.Calendar(calendarioEl, {
      initialView: 'dayGridMonth', // Visão inicial mensal
      locale: 'pt-br', // Define o idioma para português do Brasil
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay' // Botões para mudar de visão
      },
      // 4. Conecta o calendário à nossa API
      events: '<?php echo BASE_URL; ?>/api/agendamentos.php',
      // Torna os eventos clicáveis, usando a URL que definimos na API
      eventClick: function(info) {
        info.jsEvent.preventDefault(); // Previne o comportamento padrão do link
        if (info.event.url) {
          window.location.href = info.event.url; // Redireciona para a página de edição
        }
      }
    });
    calendario.render();
  });
</script>

<?php require_once '../components/footer.php'; ?>