<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Sakurairo
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <?php if (have_posts()) : 
            the_post();
            get_template_part('tpl/content', 'single');
            get_template_part('layouts/sidebox');
            get_template_part('layouts/post', 'nextprev');
        endif; ?>
    </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>