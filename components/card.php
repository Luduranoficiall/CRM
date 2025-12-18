<?php
function renderCard($lead) {
  $tags = json_decode($lead['tags_ai'], true);
  $tagsHtml = '';
  if ($tags) {
    foreach ($tags as $tag) {
      $tagsHtml .= "<span class='px-2 py-1 bg-[#0ea5e9] text-white rounded mr-2 text-xs'>".htmlspecialchars($tag)."</span>";
    }
  }
  return "<div class='bg-white rounded-xl shadow-lg p-4 mb-4 border-l-4 border-[#1d4ed8]'>
    <div class='font-bold text-[#1e3a8a] text-lg'>".htmlspecialchars($lead['nome'])."</div>
    <div class='text-[#0ea5e9] mb-2'>Faturamento: ".htmlspecialchars($lead['faturamento_categoria'])."</div>
    <div class='text-[#1d4ed8] mb-2'>Investimento: ".htmlspecialchars($lead['invest_categoria'])."</div>
    <div class='mb-2'>".$tagsHtml."</div>
    <a href='/admin/lead.php?id=".$lead['id']."' class='text-[#1e3a8a] underline'>Ver detalhes</a>
  </div>";
}
?>