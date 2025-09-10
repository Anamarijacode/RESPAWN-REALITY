<?php get_header(); ?>

<main>
    <div class="container mt-5">
        <h1 class="mb-4 text-neon">Sve Video Igre</h1>
        <?php if (have_posts()) : ?>
            <div class="row">
                <?php while (have_posts()) : the_post(); ?>
                    <div class="col-md-4">
                        <div class="card mb-4 shadow-lg">
                            <?php if (has_post_thumbnail()) : ?>
                                <img src="<?php the_post_thumbnail_url(); ?>" class="card-img-top" alt="<?php the_title_attribute(); ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php the_title(); ?></h5>
                                <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm">Pročitaj više</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p>Nema igara.</p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>