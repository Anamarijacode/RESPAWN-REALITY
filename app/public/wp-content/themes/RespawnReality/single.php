<?php get_header(); ?>

<main>
    <div class="container mt-5">
        <?php while (have_posts()) : the_post(); ?>
            <h1 class="mb-4 text-neon"><?php the_title(); ?></h1>
            
                <img src="<?php echo get_post_meta(get_the_ID(), 'game_image', true) ?>" style="max-width: 100%; height: auto;" >
            
            <div class="content"><?php the_content(); ?></div>
            <div class="post-categories">
                <p>Žanr/ovi: <?php the_category(', '); ?></p>
            </div>
            <div class="comments-section mt-5">
                <?php if (comments_open() || get_comments_number()) : ?>
                    <?php comments_template(); ?>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>