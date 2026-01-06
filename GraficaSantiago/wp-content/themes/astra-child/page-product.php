<?php
/* Template Name: ProductView */
get_header();
?>


    <section class="max-w-screen-xl flex justify-center justify-items-center mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
            <img src="producto.jpg" class="rounded shadow">

            <div>
                <h2 class="text-3xl font-bold">Nombre del Producto</h2>
                <p class="text-xl mt-2 font-semibold text-green-600">$12.99</p>

                <p class="mt-4">
                    Descripción detallada del producto. Características, tamaños, etc.
                </p>
                <div class="mt-4 flex items-center gap-3">
                    <button class="border px-2">-</button>
                    <span>1</span>
                    <button class="border px-2">+</button>
                </div>

                <button class="mt-6 bg-blue-600 text-white px-6 py-2 rounded-lg">
                    Agregar al carrito
                </button>
                <h2 class="text-2xl my-4">Descripcion del producto</h2>
                <p>
                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Numquam sequi vitae at quaerat praesentium
                    excepturi corporis quis, ab cupiditate molestias accusantium aliquid. Neque perspiciatis libero
                    praesentium odio ex eveniet architecto!
                </p>
            </div>

        </div>
    </section>
    <section>
        <div class="max-w-screen-xl mx-auto p-4 ">
            <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Productos relacionados con este artículo</h2>
            </div>
            <div class="flex items-center space-x-2 pt-4">
                <button
                    class="flex-shrink-0 flex items-center justify-center w-10 h-10 border border-gray-300 rounded-full shadow-md hover:bg-gray-100 focus:outline-none z-10">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </button>

                <div class="flex-1 overflow-x-auto whitespace-nowrap py-2 justify-center">
                    <div
                        class="inline-block w-64 p-3 mr-4 align-top border border-gray-200 rounded-lg shadow-sm hover:shadow-lg transition duration-300 bg-white hover:scale-95">

                        <div class="h-40 flex items-center justify-center mb-3">
                            <div
                                class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-500 rounded-md">
                            </div>
                        </div>
                        <p class="text-lg text-gray-700 h-10 overflow-hidden mb-2 leading-tight">
                            Nombre
                        </p>
                        <div class="flex items-center text-xs mb-1">
                            <span class="text-yellow-500 text-lg">★★★★☆</span>
                            <span class="text-gray-500 ml-1">(2,954)</span>
                        </div>
                        <div class="flex items-end mb-2">
                            <p class="text-xs text-gray-500 line-through mr-2">
                                US$36.84
                            </p>
                            <p class="text-sm font-semibold text-red-700">
                                -5%
                            </p>
                        </div>
                        <p class="text-xl font-bold text-gray-900">
                            US$34<span class="text-sm align-top">99</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            PVP: US$36.84
                        </p>
                    </div>
                    <div
                        class="inline-block w-64 p-3 mr-4 align-top border border-gray-200 rounded-lg shadow-sm hover:shadow-lg transition duration-300 bg-white hover:scale-95">

                        <div class="h-40 flex items-center justify-center mb-3">
                            <div
                                class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-500 rounded-md">
                            </div>
                        </div>
                        <p class="text-lg text-gray-700 h-10 overflow-hidden mb-2 leading-tight">
                            Nombre
                        </p>
                        <div class="flex items-center text-xs mb-1">
                            <span class="text-yellow-500 text-lg">★★★★☆</span>
                            <span class="text-gray-500 ml-1">(2,954)</span>
                        </div>
                        <div class="flex items-end mb-2">
                            <p class="text-xs text-gray-500 line-through mr-2">
                                US$36.84
                            </p>
                            <p class="text-sm font-semibold text-red-700">
                                -5%
                            </p>
                        </div>
                        <p class="text-xl font-bold text-gray-900">
                            US$34<span class="text-sm align-top">99</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            PVP: US$36.84
                        </p>
                    </div>
                    <div
                        class="inline-block w-64 p-3 mr-4 align-top border border-gray-200 rounded-lg shadow-sm hover:shadow-lg transition duration-300 bg-white hover:scale-95">

                        <div class="h-40 flex items-center justify-center mb-3">
                            <div
                                class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-500 rounded-md">
                            </div>
                        </div>
                        <p class="text-lg text-gray-700 h-10 overflow-hidden mb-2 leading-tight">
                            Nombre
                        </p>
                        <div class="flex items-center text-xs mb-1">
                            <span class="text-yellow-500 text-lg">★★★★☆</span>
                            <span class="text-gray-500 ml-1">(2,954)</span>
                        </div>
                        <div class="flex items-end mb-2">
                            <p class="text-xs text-gray-500 line-through mr-2">
                                US$36.84
                            </p>
                            <p class="text-sm font-semibold text-red-700">
                                -5%
                            </p>
                        </div>
                        <p class="text-xl font-bold text-gray-900">
                            US$34<span class="text-sm align-top">99</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            PVP: US$36.84
                        </p>
                    </div>
                    <div
                        class="inline-block w-64 p-3 mr-4 align-top border border-gray-200 rounded-lg shadow-sm hover:shadow-lg transition duration-300 bg-white hover:scale-95">

                        <div class="h-40 flex items-center justify-center mb-3">
                            <div
                                class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-500 rounded-md">
                            </div>
                        </div>
                        <p class="text-lg text-gray-700 h-10 overflow-hidden mb-2 leading-tight">
                            Nombre
                        </p>
                        <div class="flex items-center text-xs mb-1">
                            <span class="text-yellow-500 text-lg">★★★★☆</span>
                            <span class="text-gray-500 ml-1">(2,954)</span>
                        </div>
                        <div class="flex items-end mb-2">
                            <p class="text-xs text-gray-500 line-through mr-2">
                                US$36.84
                            </p>
                            <p class="text-sm font-semibold text-red-700">
                                -5%
                            </p>
                        </div>
                        <p class="text-xl font-bold text-gray-900">
                            US$34<span class="text-sm align-top">99</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            PVP: US$36.84
                        </p>
                    </div>
                    <div
                        class="inline-block w-64 p-3 mr-4 align-top border border-gray-200 rounded-lg shadow-sm hover:shadow-lg transition duration-300 bg-white hover:scale-95">

                        <div class="h-40 flex items-center justify-center mb-3">
                            <div
                                class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-500 rounded-md">
                            </div>
                        </div>
                        <p class="text-lg text-gray-700 h-10 overflow-hidden mb-2 leading-tight">
                            Nombre
                        </p>
                        <div class="flex items-center text-xs mb-1">
                            <span class="text-yellow-500 text-lg">★★★★☆</span>
                            <span class="text-gray-500 ml-1">(2,954)</span>
                        </div>
                        <div class="flex items-end mb-2">
                            <p class="text-xs text-gray-500 line-through mr-2">
                                US$36.84
                            </p>
                            <p class="text-sm font-semibold text-red-700">
                                -5%
                            </p>
                        </div>
                        <p class="text-xl font-bold text-gray-900">
                            US$34<span class="text-sm align-top">99</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            PVP: US$36.84
                        </p>
                    </div>
                </div>

                <button
                    class="flex-shrink-0 flex items-center justify-center w-10 h-10 bg-white border border-gray-300 rounded-full shadow-md hover:bg-gray-100 focus:outline-none z-10">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </section>
    <section>
        <div class="p-10 bg-gray-100 max-w-screen-xl mx-auto">
            <h2 class="text-2xl font-semibold mb-6">Productos de categoría</h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">

                <div class="relative rounded-2xl overflow-hidden bg-white shadow-md group h-48">

                    <img src="https://via.placeholder.com/300"
                        class="w-full h-full object-cover transition-all duration-500 group-hover:blur-sm group-hover:scale-110">

                    <div
                        class="absolute inset-0 bg-black/70 text-white flex flex-col items-center justify-center
                    opacity-0 translate-y-5 transition-all duration-500 group-hover:opacity-100 group-hover:translate-y-0">

                        <h3 class="text-lg font-semibold">Producto A</h3>
                        <p class="text-sm mt-1">Descripción breve.</p>
                        <span class="mt-2 font-bold text-xl">$15.99</span>

                    </div>
                </div>

                <div class="relative rounded-2xl overflow-hidden bg-white shadow-md group h-48">
                    <img src="https://via.placeholder.com/300"
                        class="w-full h-full object-cover transition-all duration-500 group-hover:blur-sm group-hover:scale-110">
                    <div
                        class="absolute inset-0 bg-black/70 text-white flex flex-col items-center justify-center 
                    opacity-0 translate-y-5 transition-all duration-500 group-hover:opacity-100 group-hover:translate-y-0">
                        <h3 class="text-lg font-semibold">Producto B</h3>
                        <p class="text-sm mt-1">Descripción breve.</p>
                        <span class="mt-2 font-bold text-xl">$22.50</span>
                    </div>
                </div>

                <div class="relative rounded-2xl overflow-hidden bg-white shadow-md group h-48">
                    <img src="https://via.placeholder.com/300"
                        class="w-full h-full object-cover transition-all duration-500 group-hover:blur-sm group-hover:scale-110">
                    <div
                        class="absolute inset-0 bg-black/70 text-white flex flex-col items-center justify-center 
                    opacity-0 translate-y-5 transition-all duration-500 group-hover:opacity-100 group-hover:translate-y-0">
                        <h3 class="text-lg font-semibold">Producto C</h3>
                        <p class="text-sm mt-1">Descripción breve.</p>
                        <span class="mt-2 font-bold text-xl">$10.00</span>
                    </div>
                </div>

                <div class="relative rounded-2xl overflow-hidden bg-white shadow-md group h-48">
                    <img src="https://via.placeholder.com/300"
                        class="w-full h-full object-cover transition-all duration-500 group-hover:blur-sm group-hover:scale-110">
                    <div
                        class="absolute inset-0 bg-black/70 text-white flex flex-col items-center justify-center 
                    opacity-0 translate-y-5 transition-all duration-500 group-hover:opacity-100 group-hover:translate-y-0">
                        <h3 class="text-lg font-semibold">Producto D</h3>
                        <p class="text-sm mt-1">Descripción breve.</p>
                        <span class="mt-2 font-bold text-xl">$18.40</span>
                    </div>
                </div>

                <div class="relative rounded-2xl overflow-hidden bg-white shadow-md group h-48">
                    <img src="https://via.placeholder.com/300"
                        class="w-full h-full object-cover transition-all duration-500 group-hover:blur-sm group-hover:scale-110">
                    <div
                        class="absolute inset-0 bg-black/70 text-white flex flex-col items-center justify-center 
                    opacity-0 translate-y-5 transition-all duration-500 group-hover:opacity-100 group-hover:translate-y-0">
                        <h3 class="text-lg font-semibold">Producto E</h3>
                        <p class="text-sm mt-1">Descripción breve.</p>
                        <span class="mt-2 font-bold text-xl">$9.99</span>
                    </div>
                </div>

                <div class="relative rounded-2xl overflow-hidden bg-white shadow-md group h-48">
                    <img src="https://via.placeholder.com/300"
                        class="w-full h-full object-cover transition-all duration-500 group-hover:blur-sm group-hover:scale-110">
                    <div
                        class="absolute inset-0 bg-black/70 text-white flex flex-col items-center justify-center 
                    opacity-0 translate-y-5 transition-all duration-500 group-hover:opacity-100 group-hover:translate-y-0">
                        <h3 class="text-lg font-semibold">Producto F</h3>
                        <p class="text-sm mt-1">Descripción breve.</p>
                        <span class="mt-2 font-bold text-xl">$34.99</span>
                    </div>
                </div>

                <div class="relative rounded-2xl overflow-hidden bg-white shadow-md group h-48">
                    <img src="https://via.placeholder.com/300"
                        class="w-full h-full object-cover transition-all duration-500 group-hover:blur-sm group-hover:scale-110">
                    <div
                        class="absolute inset-0 bg-black/70 text-white flex flex-col items-center justify-center 
                    opacity-0 translate-y-5 transition-all duration-500 group-hover:opacity-100 group-hover:translate-y-0">
                        <h3 class="text-lg font-semibold">Producto G</h3>
                        <p class="text-sm mt-1">Descripción breve.</p>
                        <span class="mt-2 font-bold text-xl">$11.50</span>
                    </div>
                </div>

                <div class="relative rounded-2xl overflow-hidden bg-white shadow-md group h-48">
                    <img src="https://via.placeholder.com/300"
                        class="w-full h-full object-cover transition-all duration-500 group-hover:blur-sm group-hover:scale-110">
                    <div
                        class="absolute inset-0 bg-black/70 text-white flex flex-col items-center justify-center 
                    opacity-0 translate-y-5 transition-all duration-500 group-hover:opacity-100 group-hover:translate-y-0">
                        <h3 class="text-lg font-semibold">Producto H</h3>
                        <p class="text-sm mt-1">Descripción breve.</p>
                        <span class="mt-2 font-bold text-xl">$16.25</span>
                    </div>
                </div>

            </div>
        </div>


    </section>
    <section>
        <div class="p-8 bg-gray-100">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 max-w-6xl mx-auto">Puntuaciones y comentarios</h2>

            <div class="grid grid-cols-3 gap-6 max-w-6xl mx-auto">

                <div class="col-span-1 space-y-6">
                    <div class="p-6 bg-white border border-gray-300 rounded-lg shadow-md">
                        <p class="text-lg font-semibold text-gray-800">ESTRELLAS</p>
                        <div class="mt-4 h-24 flex items-center justify-center text-gray-400">
                            [Contenido de Estrellas]
                        </div>
                    </div>
                    <div class="p-6 bg-white border border-gray-300 rounded-lg shadow-md">
                        <p class="text-lg font-semibold text-gray-800 leading-tight">CATEGORÍAS <br> X ESTRELLAS</p>
                        <div class="mt-4 h-56 flex items-center justify-center text-gray-400">
                            [Contenido de Categorías]
                        </div>
                    </div>
                </div>
                <div class="col-span-2 space-y-6">
                    <div class="p-6 bg-white border border-gray-300 rounded-lg shadow-md">
                        <div class="mb-4 flex mx-5">
                            <img src="" alt=""> Perfil
                            <p>Nombre</p>
                        </div>
                        <p class="text-lg font-medium text-gray-700">Puntuacion</p>
                        <div class="mt-3 h-20 text-gray-500">
                            [Detalles del comentario, texto, usuario, fecha...]
                        </div>
                    </div>

                    <div class="p-6 bg-white border border-gray-300 rounded-lg shadow-md">
                        <p class="text-lg font-medium text-gray-700">COMENTARIO</p>
                        <div class="mt-3 h-20 text-gray-500">
                            [Detalles del comentario, texto, usuario, fecha...]
                        </div>
                    </div>

                    <div class="p-6 bg-white border border-gray-300 rounded-lg shadow-md">
                        <p class="text-lg font-medium text-gray-700">COMENTARIO</p>
                        <div class="mt-3 h-20 text-gray-500">
                            [Detalles del comentario, texto, usuario, fecha...]
                        </div>
                    </div>

                    <div class="p-6 bg-white border border-gray-300 rounded-lg shadow-md">
                        <p class="text-lg font-medium text-gray-700">COMENTARIO</p>
                        <div class="mt-3 h-20 text-gray-500">
                            [Detalles del comentario, texto, usuario, fecha...]
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>
<?php
get_footer();
?>