<?php
if (!defined('ABSPATH')) exit;
$placeholder = get_theme_mod('gs_header_search_placeholder', 'Buscar productos, categorías...');
?>

<form class="w-full" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
  <div class="flex items-center gap-3 bg-white/95 backdrop-blur
              border-2 border-yellow-400/80 rounded-2xl px-4 h-14 shadow-sm
              focus-within:ring-2 focus-within:ring-yellow-300/60 transition">

    <!-- Icono -->
    <svg xmlns="http://www.w3.org/2000/svg"
         class="w-5 h-5 text-blue-900/60 shrink-0"
         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round"
            d="m21 21-4.3-4.3m1.8-5.2a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/>
    </svg>

    <!-- Input -->
    <input
      type="search"
      name="s"
      placeholder="<?php echo esc_attr($placeholder); ?>"
      class="flex-1 bg-transparent text-blue-900 placeholder:text-slate-400 outline-none text-sm"
    />

    <input type="hidden" name="post_type" value="product" />

    <!-- Botón (amarillo marca) -->
    <button type="submit"
      class="inline-flex items-center justify-center gap-2 px-4 md:px-6 h-10 rounded-xl
             bg-yellow-400 text-blue-900 font-black border-2 border-yellow-400
             hover:bg-blue-900 hover:text-white hover:border-blue-900 transition">
      
      <!-- Icono mobile -->
      <span class="md:hidden">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="m21 21-4.3-4.3m1.8-5.2a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/>
        </svg>
      </span>

      <!-- Texto desktop -->
      <span class="hidden md:inline">Buscar</span>
    </button>
  </div>
</form>
