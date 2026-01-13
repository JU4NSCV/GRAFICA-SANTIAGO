<?php
defined('ABSPATH') || exit;
get_header();
?>

<main class="w-full">
    <div class="w-screen relative left-1/2 right-1/2 -ml-[50vw] -mr-[50vw]"
        style="background: linear-gradient(180deg, var(--gs-crema,#FBEDD8) 0%, #fff 55%, #fff 100%);">
        <div class="max-w-7xl mx-auto px-4 py-8">

            <header class="rounded-[28px] bg-gradient-to-r from-blue-800 to-blue-600 text-white p-8 md:p-10 shadow-soft">
                <h1 class="text-3xl md:text-4xl font-black !text-white">Nuestra trayectoria</h1>
                <p class="mt-2 text-white/90 max-w-2xl">Historia, hitos y crecimiento de Mega Santiago.</p>
            </header>

            <section class="mt-6 bg-white rounded-[28px] border border-azulOsc/10 shadow-soft p-6 md:p-10">
                <div class="gs-prose">
                    <?php
                    while (have_posts()) : the_post();
                        the_content();
                    endwhile;
                    ?>
                </div>
            </section>

        </div>
    </div>
</main>

<?php get_footer(); ?>