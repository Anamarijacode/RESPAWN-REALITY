<?php get_header(); ?>

<div class="hero-section text-center text-white" id="hero">
    <h1 class="display-1 text-neon"> <?php bloginfo('name'); ?> </h1>
    <p class="lead"> <?php bloginfo('description'); ?> </p>
    <a href="#content" class="btn btn-outline-light btn-lg mt-3">Saznajte više</a>
</div>
<!-- Sadržaj Sekcija -->
<div id="content" class="container mt-5">
    <h2 class="mb-4 text-neon objava">Najnovije objave</h2>

    <?php if (have_posts()) : ?>
        <div class="row">
            <?php while (have_posts()) : the_post(); ?>
                <div class="col-md-4">
                    <div class="card mb-4 shadow-lg">
                        <?php if (has_post_thumbnail()) : ?>
                            <img src="<?php the_post_thumbnail_url(); ?>" class="card-img-top" alt="<?php the_title_attribute(); ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"> <?php the_title(); ?> </h5>
                            <p class="card-text"> <?php echo wp_trim_words(get_the_content(), 15); ?> </p>
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm">Pročitaj više</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <p class="text-center text-muted">Trenutno nema dostupnih postova.</p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>