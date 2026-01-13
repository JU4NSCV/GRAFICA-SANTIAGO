<?php
defined('ABSPATH') || exit;

get_header();

function gs_reading_time($post_id = 0, $wpm = 200)
{
    $post_id = $post_id ?: get_the_ID();
    $content = (string) get_post_field('post_content', $post_id);
    $words   = str_word_count(wp_strip_all_tags($content));
    return max(1, (int) ceil($words / max(1, (int)$wpm)));
}

$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/productos/');
$blog_url = home_url('/blog/');
?>

<main id="primary" class="w-full">

    <!-- Fondo marca -->
    <div class="w-screen relative left-1/2 right-1/2 -ml-[50vw] -mr-[50vw]">
        <div class="max-w-7xl mx-auto px-4 py-8">

            <?php while (have_posts()) : the_post();
                $thumb = get_the_post_thumbnail_url(get_the_ID(), 'full');

                $cats = get_the_category();
                $cat  = !empty($cats) ? $cats[0] : null;

                $badge_text  = $cat ? strtoupper($cat->name) : 'BLOG';
                $badge_class = $cat ? 'bg-white/90 text-azulOsc' : 'bg-amarillo text-azulOsc';

                $read_min = gs_reading_time();
            ?>

                <!-- HERO -->
                <header class="rounded-[28px] overflow-hidden border border-azulOsc/10 shadow-soft bg-white">
                    <div class="relative min-h-[280px] md:min-h-[380px]">
                        <?php if ($thumb): ?>
                            <img src="<?php echo esc_url($thumb); ?>"
                                class="absolute inset-0 w-full h-full object-cover"
                                alt="<?php echo esc_attr(get_the_title()); ?>">
                        <?php else: ?>
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-800 to-blue-600"></div>
                        <?php endif; ?>

                        <!-- Overlay para legibilidad -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/35 to-transparent"></div>

                        <div class="absolute top-5 left-5 flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black shadow <?php echo esc_attr($badge_class); ?>">
                                <?php echo esc_html($badge_text); ?>
                            </span>

                            <?php if ($cat): ?>
                                <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-amarillo text-azulOsc shadow hover:brightness-95 transition">
                                    Ver más
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="absolute bottom-0 left-0 right-0 p-6 md:p-10 text-white">
                            <h1 class="text-2xl md:text-4xl font-black leading-tight !text-white drop-shadow">
                                <?php the_title(); ?>
                            </h1>

                            <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-white/90 font-semibold">
                                <span><?php echo esc_html(get_the_date('d M, Y')); ?></span>
                                <span class="h-1 w-1 rounded-full bg-white/60"></span>
                                <span><?php the_author(); ?></span>
                                <span class="h-1 w-1 rounded-full bg-white/60"></span>
                                <span><?php echo esc_html($read_min . ' min lectura'); ?></span>
                            </div>

                            <div class="mt-5 flex flex-wrap gap-3">
                                <a href="<?php echo esc_url($blog_url); ?>"
                                    class="px-5 py-3 rounded-2xl bg-white/90 text-azulOsc font-extrabold shadow hover:bg-white transition">
                                    ← Volver al blog
                                </a>
                                <a href="<?php echo esc_url($shop_url); ?>"
                                    class="px-5 py-3 rounded-2xl bg-amarillo text-azulOsc font-extrabold shadow hover:brightness-95 transition">
                                    Ir al catálogo →
                                </a>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- CUERPO -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-8">

                    <!-- Contenido -->
                    <article class="lg:col-span-8 bg-white rounded-[28px] border border-azulOsc/10 shadow-soft p-6 md:p-8">
                        <div class="gs-prose">
                            <?php the_content(); ?>
                        </div>

                        <!-- Tags + compartir -->
                        <div class="mt-8 pt-6 border-t border-azulOsc/10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-xs font-extrabold text-azulOsc/70">Tags:</span>
                                <?php
                                $tags = get_the_tags();
                                if ($tags) :
                                    foreach ($tags as $t) :
                                ?>
                                        <a href="<?php echo esc_url(get_tag_link($t->term_id)); ?>"
                                            class="px-3 py-2 rounded-full border border-azulOsc/15 bg-white text-azulOsc font-extrabold text-xs hover:bg-azulOsc hover:text-white transition">
                                            <?php echo esc_html($t->name); ?>
                                        </a>
                                    <?php endforeach;
                                else: ?>
                                    <span class="text-xs text-azulOsc/60">Sin tags</span>
                                <?php endif; ?>
                            </div>

                            <?php
                            $share_url   = rawurlencode(get_permalink());
                            $share_title = rawurlencode(get_the_title());
                            ?>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-extrabold text-azulOsc/70">Compartir:</span>
                                <a class="px-3 py-2 rounded-xl bg-azulOsc text-white font-extrabold text-xs hover:bg-blue-700 transition"
                                    target="_blank" rel="noopener"
                                    href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>">Facebook</a>
                                <a class="px-3 py-2 rounded-xl bg-amarillo text-azulOsc font-extrabold text-xs hover:brightness-95 transition"
                                    target="_blank" rel="noopener"
                                    href="https://wa.me/?text=<?php echo $share_title; ?>%20<?php echo $share_url; ?>">WhatsApp</a>
                            </div>
                        </div>
                    </article>

                    <!-- Sidebar bonito -->
                    <aside class="lg:col-span-4 space-y-6">

                        <div class="bg-white rounded-[28px] border border-azulOsc/10 shadow-soft p-6">
                            <p class="text-sm font-black text-azulOsc">Categorías</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <?php foreach (get_categories(['hide_empty' => true]) as $c): ?>
                                    <a href="<?php echo esc_url(get_category_link($c->term_id)); ?>"
                                        class="px-3 py-2 rounded-full border border-azulOsc/15 bg-white text-azulOsc font-extrabold text-xs hover:bg-azulOsc hover:text-white transition">
                                        <?php echo esc_html($c->name); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="bg-white rounded-[28px] border border-azulOsc/10 shadow-soft p-6">
                            <p class="text-sm font-black text-azulOsc">Más recientes</p>
                            <div class="mt-4 space-y-3">
                                <?php
                                $recent = new WP_Query([
                                    'post_type'      => 'post',
                                    'posts_per_page' => 4,
                                    'post__not_in'   => [get_the_ID()],
                                ]);
                                while ($recent->have_posts()) : $recent->the_post();
                                    $t = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                                ?>
                                    <a href="<?php the_permalink(); ?>"
                                        class="flex gap-3 items-center p-2 rounded-2xl hover:bg-blue-50 transition">
                                        <div class="w-16 h-16 rounded-2xl bg-gray-100 overflow-hidden shrink-0">
                                            <?php if ($t): ?><img src="<?php echo esc_url($t); ?>" class="w-full h-full object-cover" alt=""><?php endif; ?>
                                        </div>
                                        <div>
                                            <p class="text-xs font-black text-azulOsc line-clamp-2"><?php the_title(); ?></p>
                                            <p class="text-xs text-azulOsc/60 mt-1"><?php echo esc_html(get_the_date('d M')); ?></p>
                                        </div>
                                    </a>
                                <?php endwhile;
                                wp_reset_postdata(); ?>
                            </div>
                        </div>

                        <div class="rounded-[28px] p-6 text-white shadow-soft relative overflow-hidden"
                            style="background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);">
                            <p class="text-lg font-black">¿Buscas útiles o tecnología?</p>
                            <p class="mt-1 text-white/80 text-sm">Explora el catálogo y encuentra lo que necesitas.</p>
                            <a href="<?php echo esc_url($shop_url); ?>"
                                class="mt-4 inline-flex px-5 py-3 rounded-2xl bg-amarillo text-azulOsc font-extrabold shadow hover:brightness-95 transition">
                                Ir al catálogo →
                            </a>
                        </div>

                    </aside>

                </div>

            <?php endwhile; ?>

        </div>
    </div>
</main>

<?php get_footer(); ?>