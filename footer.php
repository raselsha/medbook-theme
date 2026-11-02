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
<?php $medbook_sales = medbook_demo_sales(); if ($medbook_sales && $medbook_sales['whatsapp_url']) : ?>
    <a class="medbook-whatsapp-float" href="<?php echo esc_url($medbook_sales['whatsapp_url']); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('Chat on WhatsApp', 'medbook'); ?>">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a9 9 0 0 0-7.8 13.4L3 21l4.7-1.2A9 9 0 1 0 12 3Z"></path><path d="M8.5 8.6c.3-.7.6-.7.9-.7h.6c.2 0 .4 0 .6.5l.7 1.7c.1.3 0 .5-.1.7l-.5.6c-.1.2-.2.4 0 .7.5.8 1.5 1.8 2.3 2.2.3.2.5.2.7 0l.6-.6c.2-.2.4-.2.7-.1l1.7.8c.4.2.4.4.4.6-.1.9-1.3 1.7-2.2 1.7-1.8 0-4.3-1.4-5.9-3-1.6-1.6-2.6-3.7-2.5-5.4 0-.6.2-1.2.5-1.7Z" fill="currentColor" stroke="none"></path></svg>
    </a>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>
