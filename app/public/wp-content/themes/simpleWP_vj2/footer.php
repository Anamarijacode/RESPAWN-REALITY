<footer class="footer">
    
    
    <div class="container">
        <div class="row">
            <?php for ($i = 1; $i <= 4; $i++) : ?>
                <div class="col-md-3">
                    <?php if (is_active_sidebar("footer-sidebar$i")) : ?>
                        <?php dynamic_sidebar("footer-sidebar$i"); ?>
                    <?php endif; ?>
                </div>
            <?php endfor; ?>
        </div>
    </div>
                    
</footer>

<?php wp_footer(); ?>
</body>
</html>
