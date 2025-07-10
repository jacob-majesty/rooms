document.addEventListener('DOMContentLoaded', function() {
    // Inicialização do calendário
    if (document.getElementById('calendar')) {
        initCalendar();
    }

    // Validação do formulário de login
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            if (!email.includes('@')) {
                alert('Por favor, insira um e-mail válido');
                e.preventDefault();
            }
        });
    }
});

function initCalendar() {
    // Implementação básica do calendário
    const calendar = document.getElementById('calendar');
    const currentDate = new Date();
    const monthNames = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
                        "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];

    // Cabeçalho do calendário
    let html = `
        <div class="calendar-header">
            <h3>${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}</h3>
        </div>
        <div class="calendar-grid">
    `;

    // Dias da semana
    const daysOfWeek = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
    daysOfWeek.forEach(day => {
        html += `<div class="calendar-day-header">${day}</div>`;
    });

    // Dias do mês
    // ... (implementação completa dos dias)

    calendar.innerHTML = html + '</div>';

    // Event listeners para os dias
    document.querySelectorAll('.day-available').forEach(day => {
        day.addEventListener('click', function() {
            alert('Dia selecionado: ' + this.dataset.date);
        });
    });
}