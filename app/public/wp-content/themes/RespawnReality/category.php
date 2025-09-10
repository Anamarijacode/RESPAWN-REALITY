<?php get_header(); ?>

<main>
    <div class="container mt-5">
        <h1 class="mb-4 text-neon"><?php single_cat_title(); ?></h1>
        <?php
$category = get_queried_object();
echo do_shortcode('[videoigre_kategorije category="' . $category->slug . '"]');
?>

    </div>
</main>

<?php get_footer(); ?>