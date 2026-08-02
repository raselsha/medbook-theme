<?php
defined('ABSPATH') || exit;
$clinic_name = get_option('mdbk_clinic_name', get_bloginfo('name'));
?>
<footer class="medbook-footer">
    <div class="medbook-footer-inner">
        <span>&copy; <?php echo esc_html(gmdate('Y')); ?> <?php echo esc_html($clinic_name); ?></span>
        <nav class="medbook-footer-links">
            <?php
            if (has_nav_menu('medbook-primary')) {
                wp_nav_menu(array(
                    'theme_location' => 'medbook-primary',
                    'container'      => false,
                    'menu_class'     => 'medbook-footer-menu',
                    'fallback_cb'    => false,
                ));
            }
            ?>
        </nav>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
