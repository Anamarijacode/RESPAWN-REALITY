<?php get_header(); ?>

<main>
    <?php while (have_posts()) : the_post(); ?>
        <?php if (has_post_thumbnail()) : ?>
            <div class="post-thumbnail"><?php the_post_thumbnail(); ?></div>
        <?php endif; ?>
        <h1><?php the_title(); ?></h1>
        <div class="content"><?php the_content(); ?></div>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
