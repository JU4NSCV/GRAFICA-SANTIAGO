<?php
/* Template Name: Catálogo */
get_header();
?>

    <section class="mt-10 px-6 max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row gap-6">

            <!-- SIDEBAR (izquierda) -->
            <aside class="w-full md:w-72 flex-shrink-0">
                <!-- Móvil: control colapsable -->
                <div class="md:hidden mb-4">
                    <button id="toggleSidebar"
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-100 border border-gray-300 rounded-2xl text-sm">
                        <span>Filtrar por categoría</span>
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                </div>

                <!-- Panel (oculto en móvil por defecto) -->
                <div id="sidebarPanel" class="hidden md:block bg-gray-100 border border-gray-300 rounded-2xl p-4">
                    <h4 class="text-xs uppercase tracking-[0.18em] font-semibold mb-3">Categorías</h4>
                    <ul id="categoryList" class="space-y-2 text-sm"></ul>

                    <div class="mt-6">
                        <h4 class="text-xs uppercase tracking-[0.18em] font-semibold mb-3">Filtrar</h4>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm">
                                <input id="inStockCheckbox" type="checkbox" class="form-checkbox h-4 w-4" />
                                <span>Solo en stock</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <input id="hasDiscountCheckbox" type="checkbox" class="form-checkbox h-4 w-4" />
                                <span>Con descuento</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Móvil: panel expandible (contenido idéntico) -->
                <div id="sidebarPanelMobile"
                    class="md:hidden bg-gray-100 border border-gray-300 rounded-2xl p-4 hidden">
                    <h4 class="text-xs uppercase tracking-[0.18em] font-semibold mb-3">Categorías</h4>
                    <ul id="categoryListMobile" class="space-y-2 text-sm"></ul>

                    <div class="mt-6">
                        <h4 class="text-xs uppercase tracking-[0.18em] font-semibold mb-3">Filtrar</h4>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm">
                                <input id="inStockCheckboxMobile" type="checkbox" class="form-checkbox h-4 w-4" />
                                <span>Solo en stock</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <input id="hasDiscountCheckboxMobile" type="checkbox" class="form-checkbox h-4 w-4" />
                                <span>Con descuento</span>
                            </label>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- CONTENIDO PRINCIPAL (derecha) -->
            <main class="flex-1">
                <!-- Barra de control (buscar, ordenar) -->
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3 mb-6">
                    <div class="flex gap-3 w-full md:w-2/3">
                        <input id="catalogSearch" type="search" placeholder="Buscar producto, marca o referencia..."
                            class="flex-1 px-4 py-3 border border-gray-300 rounded-2xl bg-white text-sm focus:outline-none" />
                        <select id="sortSelect" class="px-4 py-3 border border-gray-300 rounded-2xl bg-white text-sm">
                            <option value="default">Ordenar: Relevancia</option>
                            <option value="price-asc">Precio: menor a mayor</option>
                            <option value="price-desc">Precio: mayor a menor</option>
                            <option value="popular">Más vendidos</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-3">
                        <p id="resultsInfo" class="text-sm text-gray-600">Mostrando <span id="resultsCount">0</span>
                            productos</p>
                        <button id="clearFilters"
                            class="px-3 py-2 border border-gray-300 rounded-2xl text-sm">Limpiar</button>
                    </div>
                </div>

                <!-- GRID DE PRODUCTOS -->
                <div id="catalogGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"></div>

                <!-- PAGINACIÓN SIMPLE -->
                <div class="mt-6 flex justify-center items-center gap-3">
                    <button id="prevPage" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">Anterior</button>
                    <div id="pageNumbers" class="flex items-center gap-2"></div>
                    <button id="nextPage" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">Siguiente</button>
                </div>
            </main>
        </div>
    </section>

   <?php
get_footer();
?>
