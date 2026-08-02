<?php
/**
 * Generic page template — used by any page that isn't one of the four
 * page-templates/*.php pages (e.g. the per-doctor pages the plugin
 * auto-generates, each with its own shortcode + doctor="ID" attribute
 * already in its content, plus any other plain page). Just the page's own
 * content — the_content() already executes any shortcode inside it.
 */
defined('ABSPATH') || exit;
get_header();
?>
<main class="medbook-page">
    <?php while (have_posts()) : the_post(); ?>
        <h1 class="medbook-page-title"><?php the_title(); ?></h1>
        <div class="medbook-page-content"><?php the_content(); ?></div>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
