<?php
require_once '../auth/verifica_sessao.php';
autorizar(['psicologo', 'psicologo_autonomo']);
require_once '../config.php';
require_once '../components/header.php';
?>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Minha Agenda</h1>
        <a href="<?php echo BASE_URL; ?>/dashboard/psicologo.php" class="button">&larr; Voltar ao Dashboard</a>
    </div>

    <div class="card">
        <div id='calendario-pessoal'></div>
    </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const calendarioEl = document.getElementById('calendario-pessoal');
    const calendario = new FullCalendar.Calendar(calendarioEl, {
      initialView: 'timeGridWeek', // Visão semanal é mais útil para o psicólogo
      locale: 'pt-br',
      allDaySlot: false,
      slotMinTime: '08:00:00',
      slotMaxTime: '21:00:00',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      events: '<?php echo BASE_URL; ?>/api/agendamentos.php',
      eventClick: function(info) {
        info.jsEvent.preventDefault();
        // Psicólogos não editam agendamentos, eles vão para o dossiê.
        // A URL para o dossiê do paciente precisa ser adicionada na API.
        // Por agora, mantemos o link de edição.
        if (info.event.url) {
          window.location.href = info.event.url;
        }
      }
    });
    calendario.render();
  });
</script>

<?php require_once '../components/footer.php'; ?>