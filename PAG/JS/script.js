function setActive(sectionId) {
    // Oculta todos los contenidos
    document.querySelectorAll('.info-content').forEach(el => {
        el.classList.add('hidden');
    });
    // Muestra el contenido activo
    document.getElementById(`content-${sectionId}`).classList.remove('hidden');

    // Simular el estilo de enlace activo (solo por estética)
    document.querySelectorAll('nav a').forEach(a => {
        a.classList.remove('text-blue-600', 'font-bold');
        a.classList.add('text-gray-600');
    });
    document.querySelector(`[onclick="setActive('${sectionId}')"]`).classList.add('text-blue-600', 'font-bold');
    document.querySelector(`[onclick="setActive('${sectionId}')"]`).classList.remove('text-gray-600');
}
// Inicializar con la sección 'detalles'
// PRESENTACION DE CATEGORIAS
// setActive('detalles'); // Ya está simulado con los estilos iniciales si se omite el script.

// 23 categorías (cámbialas si quieres)
const categories = [
    'Bisuteria',
    'Accesorios',
    'Hogar',
    'Comida',
    'Regalos',
    'Tarjetería',
    'Bolsos',
    'Tecnología',
    'Papelería',
    'Escolares',
    'Oficina',
    'Ase'
];



const scroller = document.getElementById('categoryScroller');
const btnPrev = document.getElementById('categoryPrev');
const btnNext = document.getElementById('categoryNext');
const dotsWrap = document.getElementById('categoryDots');

// 1) Crear las tarjetas
categories.forEach(name => {
    const card = document.createElement('article');
    card.className =
        'min-w-[220px] md:min-w-[260px] h-56 rounded-2xl relative overflow-hidden ' +
        'border border-slate-300' +
        'snap-start';
    card.innerHTML = `
      <div class="absolute inset-0 bg-gradient-to-t from-black-600/90 via-white to-transparent"></div>
      <div class="absolute bottom-6 left-1/2 -translate-x-1/2">
        <span class="inline-block text-black text-sm font-semibold uppercase tracking-[0.16em] px-4 py-6 rounded-full">
          ${name}
        </span>
      </div>
    `;
    scroller.appendChild(card);
});

let totalPages = 1;
let currentPage = 0;

// 2) Crear los dots según el ancho
function buildDots() {
    const viewportWidth = scroller.clientWidth;
    const totalWidth = scroller.scrollWidth;

    totalPages = Math.max(1, Math.ceil(totalWidth / viewportWidth));
    dotsWrap.innerHTML = '';

    for (let i = 0; i < totalPages; i++) {
        const dot = document.createElement('button');
        dot.className =
            'h-1.5 w-6 rounded-full bg-gray-400 transition-colors';
        dot.dataset.index = i;
        dot.addEventListener('click', () => scrollToPage(i));
        dotsWrap.appendChild(dot);
    }

    highlightDot();
}

function highlightDot() {
    const dots = dotsWrap.querySelectorAll('button');
    dots.forEach((dot, i) => {
        dot.className =
            'h-1.5 w-6 rounded-full transition-colors ' +
            (i === currentPage ? 'bg-gray-600' : 'bg-gray-400');
    });
}

function scrollToPage(page) {
    const viewportWidth = scroller.clientWidth;
    currentPage = Math.min(Math.max(page, 0), totalPages - 1);
    scroller.scrollTo({
        left: currentPage * viewportWidth,
        behavior: 'smooth'
    });
    highlightDot();
}

// 3) Flechas izquierda / derecha
btnPrev.addEventListener('click', () => {
    scrollToPage(currentPage - 1);
});

btnNext.addEventListener('click', () => {
    scrollToPage(currentPage + 1);
});

// 4) Actualizar página actual cuando el usuario haga scroll manual
scroller.addEventListener('scroll', () => {
    const viewportWidth = scroller.clientWidth;
    currentPage = Math.round(scroller.scrollLeft / viewportWidth);
    highlightDot();
});

// 5) Recalcular en resize y al cargar
window.addEventListener('resize', buildDots);
window.addEventListener('load', buildDots);


// PRODUCTOS POPULARES
  // Datos de ejemplo: cámbialos por tus productos reales
  const popularProducts = [
    {
      name: "Set resaltadores pastel x6",
      short: "Resaltadores tonos pastel",
      description:
        "Colores suaves, punta biselada. Ideal para estudio, oficina y organización de apuntes.",
      price: "$5.90",
      badge: "Más vendido"
    },
    {
      name: "Cuaderno A5 tapa dura",
      short: "Cuaderno rayado 80 hojas",
      description:
        "Tapa dura, papel de alta calidad, tamaño perfecto para la universidad o trabajo.",
      price: "$3.50",
      badge: "Top popular"
    },
    {
      name: "Lápices de colores x24",
      short: "Set artístico escolar",
      description:
        "24 colores intensos, mina resistente. Ideal para arte, proyectos y tareas escolares.",
      price: "$7.20",
      badge: "Recomendado"
    },
    {
      name: "Carpeta archivadora oficio",
      short: "Carpeta lomo ancho",
      description:
        "Para documentos tamaño oficio, con anillos metálicos resistentes y lomo rotulado.",
      price: "$4.10",
      badge: "Favorito oficinas"
    }
  ];

  const popularGrid = document.getElementById("popular-grid");

  popularProducts.forEach((product) => {
    const card = document.createElement("article");
    card.className =
      "bg-gray-100 rounded-xl shadow-lg transition hover:shadow-xl hover:scale-110 " +
      "flex flex-col relative overflow-hidden group";

    card.innerHTML = `
      <!-- Badge -->
      <span class="absolute top-3 left-3 z-10 bg-black text-white text-[10px] px-2 py-1 rounded-full uppercase tracking-[0.16em]">
        ${product.badge}
      </span>

      <!-- Imagen -->
      <div class="h-48 bg-gray-300 flex items-center justify-center text-sm text-gray-700 font-semibold rounded-t-xl">
        Imagen
      </div>

      <!-- Descripción corta (vista normal) -->
      <div class="p-4 bg-gray-200 flex flex-col items-center justify-center text-gray-800 rounded-b-xl">
        <h3 class="text-sm font-semibold text-center mb-1">${product.name}</h3>
        <p class="text-xs text-center">${product.short}</p>
      </div>

      <!-- Overlay descripción completa -->
      <div
        class="absolute inset-0 bg-gray-900/90 text-white p-4
               flex flex-col items-center justify-center text-center
               opacity-0 translate-y-full group-hover:opacity-100 group-hover:translate-y-0
               transition duration-300">

        <h3 class="text-sm font-semibold mb-2">${product.name}</h3>
        <p class="text-xs mb-3">${product.description}</p>
        <span class="font-bold mb-3 text-base">${product.price}</span>
        <button
          class="px-4 py-2 bg-white text-gray-900 rounded-full text-[11px] uppercase tracking-[0.16em]">
          Añadir al carrito
        </button>
      </div>
    `;

    popularGrid.appendChild(card);
  });


  //PRESENTACION DE PRODUCTOS
  // Array de productos (ejemplo). Cambia estos por los tuyos.
  const products = [
    {
      name: "Panna Dining Chair",
      price: "36.89",
      rating: 5
    },
    {
      name: "Plum Bar Chair",
      price: "56.00",
      rating: 0
    },
    {
      name: "Grouped Product",
      price: "124.89 – 157.00",
      rating: 0
    },
    {
      name: "Two Seater Sofa",
      price: "124.89",
      rating: 4
    }
  ];

  const productGrid = document.getElementById("product-grid");

  products.forEach((product) => {
    // calcular estrellas: ★ y ☆
    const filled = "★".repeat(product.rating);
    const empty = "☆".repeat(5 - product.rating);
    const stars = filled + empty;

    const card = document.createElement("article");
    card.className =
      "bg-gray-50 border border-gray-300 rounded-2xl overflow-hidden " +
      "flex flex-col hover:shadow-lg hover:-translate-y-1 transition";

    card.innerHTML = `
      <div class="relative bg-gray-200 flex items-center justify-center h-56">
        <div class="w-36 h-28 bg-gray-300 rounded-md flex items-center justify-center text-xs text-gray-600">
          IMG
        </div>
      </div>

      <div class="px-3 pt-4 pb-5 flex flex-col gap-1">
        <h3 class="text-sm text-gray-900 hover:underline cursor-pointer">
          ${product.name}
        </h3>

        <div class="flex items-center text-xs text-gray-400">
          <span>${stars}</span>
        </div>

        <div class="mt-1 text-sm">
          <span class="font-bold text-gray-900">$${product.price}</span>
        </div>
      </div>
    `;

    productGrid.appendChild(card);
  });


  // PRESENTACION DE CATALOGOS///////////
