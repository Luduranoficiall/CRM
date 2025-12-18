const perguntas = [
  { pergunta: 'Qual é o faturamento atual da sua empresa?', name: 'faturamento', type: 'text', placeholder: 'Ex: 50.000' },
  { pergunta: 'Quanto pretende investir em tráfego pago?', name: 'investimento', type: 'text', placeholder: 'Ex: 3.000' },
  { pergunta: 'Qual o Instagram da sua empresa?', name: 'instagram', type: 'text', placeholder: '@empresa' },
  { pergunta: 'Qual é o ramo da sua empresa?', name: 'ramo', type: 'text', placeholder: 'Ex: Moda' },
  { pergunta: 'Você já faz tráfego pago atualmente?', name: 'faz_trafego', type: 'radio', options: ['Sim', 'Não'] },
  { pergunta: 'Qual seu objetivo principal com tráfego pago?', name: 'objetivo', type: 'text', placeholder: 'Ex: Vender mais' },
  { pergunta: 'Seu nome', name: 'nome', type: 'text', placeholder: 'Nome completo' },
  { pergunta: 'Seu email', name: 'email', type: 'email', placeholder: 'email@exemplo.com' },
  { pergunta: 'Seu telefone', name: 'telefone', type: 'text', placeholder: '(99) 99999-9999' }
];
let atual = 0;
let respostas = {};
function renderQuiz() {
  const q = perguntas[atual];
  let html = `<div class='mb-6 text-xl font-semibold text-[#1e3a8a]'>${q.pergunta}</div>`;
  if (q.type === 'radio') {
    html += q.options.map(opt => `<label class='block mb-2'><input type='radio' name='${q.name}' value='${opt}' class='mr-2'>${opt}</label>`).join('');
  } else {
    html += `<input type='${q.type}' name='${q.name}' placeholder='${q.placeholder}' class='w-full px-4 py-2 rounded-lg border border-[#0ea5e9] focus:outline-none focus:ring-2 focus:ring-[#1d4ed8] mb-4' required>`;
  }
  html += `<div class='flex items-center justify-between mt-4'>`;
  html += `<div class='w-2/3 h-2 bg-[#0ea5e9] rounded-full overflow-hidden'><div class='h-2 bg-[#1d4ed8] transition-all duration-500' style='width:${((atual+1)/perguntas.length)*100}%'></div></div>`;
  html += `<button id='next-btn' class='ml-4 px-6 py-2 bg-[#1e3a8a] text-white rounded-lg shadow hover:bg-[#0ea5e9] transition-all duration-300'>${atual < perguntas.length-1 ? 'Próximo' : 'Enviar'}</button>`;
  html += `</div>`;
  document.getElementById('quiz-container').innerHTML = html;
  document.getElementById('next-btn').onclick = proximo;
}
function proximo() {
  const q = perguntas[atual];
  let val, erro = '';
  if (q.type === 'radio') {
    val = document.querySelector(`input[name='${q.name}']:checked`);
    if (!val) erro = 'Selecione uma opção.';
    else respostas[q.name] = val.value;
  } else {
    val = document.querySelector(`input[name='${q.name}']`).value.trim();
    if (!val) erro = 'Preencha este campo.';
    if (q.name === 'email' && val) {
      const emailRegex = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
      if (!emailRegex.test(val)) erro = 'Email inválido.';
    }
    if (q.name === 'telefone' && val) {
      const telRegex = /^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/;
      if (!telRegex.test(val)) erro = 'Telefone inválido.';
    }
    if (!erro) respostas[q.name] = val;
  }
  if (erro) {
    let erroDiv = document.getElementById('erro-msg');
    if (!erroDiv) {
      erroDiv = document.createElement('div');
      erroDiv.id = 'erro-msg';
      erroDiv.className = 'mb-2 text-red-600 text-sm animate-pulse';
      document.getElementById('quiz-container').prepend(erroDiv);
    }
    erroDiv.textContent = erro;
    return;
  } else {
    const erroDiv = document.getElementById('erro-msg');
    if (erroDiv) erroDiv.remove();
  }
  atual++;
  if (atual < perguntas.length) {
    document.getElementById('quiz-container').classList.add('animate-fade-out');
    setTimeout(() => {
      document.getElementById('quiz-container').classList.remove('animate-fade-out');
      renderQuiz();
    }, 300);
  } else {
    enviarQuiz();
  }
}
function enviarQuiz() {
  document.getElementById('quiz-container').innerHTML = `<div class='flex flex-col items-center justify-center h-64'><div class='loader mb-4'></div><div class='text-[#1e3a8a] font-semibold'>Enviando...</div></div>`;
  fetch('/api/new-lead.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(respostas)
  })
  .then(res => res.json())
  .then(data => {
    document.getElementById('quiz-container').innerHTML = `<div class='text-center text-[#1e3a8a] animate-fade-in'><h2 class='text-2xl font-bold mb-4'>Obrigado!</h2><p>Recebemos suas respostas.<br>Em breve entraremos em contato.</p></div>`;
  })
  .catch(() => {
    document.getElementById('quiz-container').innerHTML = `<div class='text-center text-red-600 animate-fade-in'><h2 class='text-2xl font-bold mb-4'>Erro!</h2><p>Não foi possível enviar suas respostas.<br>Tente novamente mais tarde.</p></div>`;
  });
}
document.addEventListener('DOMContentLoaded', renderQuiz);

// Loader CSS
const style = document.createElement('style');
style.innerHTML = `.loader { border: 4px solid #e0e7ef; border-top: 4px solid #0ea5e9; border-radius: 50%; width: 40px; height: 40px; animation: spin 0.8s linear infinite; } @keyframes spin { 0% { transform: rotate(0deg);} 100% { transform: rotate(360deg);} }`;
document.head.appendChild(style);