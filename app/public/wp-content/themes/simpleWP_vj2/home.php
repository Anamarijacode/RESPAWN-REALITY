<?php get_header(); ?>

<main>
    <h1><?php wp_title(); ?></h1>

    <?php if (have_posts()) : ?>
        <div>
            <?php while (have_posts()) : the_post(); ?>
                <article>
                    <!-- Istaknuta slika -->
                    <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>">
                            <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>">
                        </a>
                    <?php endif; ?>

                    <!-- Naslov objave -->
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

                    <!-- Datum i autor -->
                    <div class="post-meta">
                        <span class="post-date"><?php echo get_the_date(); ?></span>
                        <span class="post-author"><?php the_author_link(); ?></span>
                    </div>

                    <!-- Sažetak objave -->
                    <div class="post-excerpt">
                        <?php echo get_the_excerpt(); ?>
                        <a href="<?php the_permalink(); ?>">Pročitaj više</a>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <!-- Navigacijski linkovi -->
        <div class="pagination">
            <?php 
            posts_nav_link(
                ' | ', // Separator između "Prethodna" i "Sljedeća" veza
                'Prethodna stranica', // Tekst za prethodnu stranicu
                'Sljedeća stranica' // Tekst za sljedeću stranicu
            ); 
            ?>
        </div>

    <?php else : ?>
        <p>Nema dostupnih objava.</p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
