<?php
/**
 * Generic fallback — this theme has no archive/single templates of its own
 * (the public site is entirely the four page-templates/*.php pages); this
 * only ever renders for something outside that (search results, a 404, a
 * stray post). Keeps the site's header/nav/footer intact instead of an
 * unstyled default loop.
 */
defined('ABSPATH') || exit;
get_header();
?>
<main class="medbook-page">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <h1 class="medbook-page-title"><?php the_title(); ?></h1>
            <div class="medbook-page-content"><?php the_content(); ?></div>
        <?php endwhile; ?>
    <?php else : ?>
        <h1 class="medbook-page-title"><?php esc_html_e('Nothing found', 'medbook'); ?></h1>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
