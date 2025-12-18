document.addEventListener('DOMContentLoaded',()=>{
  ['cold-col','morno-col','quente-col','ultra-quente-col'].forEach(id=>{
    new Sortable(document.getElementById(id),{
      group:'kanban',animation:200,ghostClass:'bg-[#0ea5e9]/20',onEnd:e=>{
        // Aqui pode enviar atualização de status via AJAX
      }
    });
  });
});