<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Sakurairo
 */

get_header();

// 缓存配置参数
$patternimg_enabled = iro_opt('patternimg');
$pagenav_style = iro_opt('pagenav_style');
$image_category = iro_opt('image_category');
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <?php if (have_posts()) : ?>
            <?php
            // 分类标题和描述
            $show_header = !$patternimg_enabled || !z_taxonomy_image_url();
            $category_description = category_description();
            ?>
            
            <?php if ($show_header) : ?>
                <header class="page-header">
                    <h1 class="cat-title"><?php single_cat_title('', true); ?></h1>
                    <?php if (!empty($category_description)) : ?>
                        <span class="cat-des"><?= $category_description ?></span>
                    <?php endif; ?>
                </header>
            <?php endif; ?>

            <?php
            // 文章循环
            while (have_posts()) : the_post();
                get_template_part('tpl/content', 'thumbcard');
            endwhile;
            ?>
            
            <div class="clearer"></div>
        <?php else : ?>
            <?php get_template_part('tpl/content', 'none'); ?>
        <?php endif; ?>
    </main>

    <?php if ($pagenav_style === 'ajax') : ?>
        <?php
        $is_image_category = !empty($image_category) && is_category(array_map('trim', explode(',', $image_category)));
        ?>
        <div id="pagination" class="<?= $is_image_category ? 'pagination-archive' : '' ?>">
            <?= next_posts_link(__(' Previous', 'sakurairo')) ?>
        </div>
        <div id="add_post">
            <span id="add_post_time" 
                  style="visibility: hidden;" 
                  title="<?= esc_attr(iro_opt('page_auto_load', '')) ?>">
            </span>
        </div>
    <?php else : ?>
        <nav class="navigator">
            <?= previous_posts_link('<i class="fa-solid fa-angle-left"></i>') ?>
            <?= next_posts_link('<i class="fa-solid fa-angle-right"></i>') ?>
        </nav>
    <?php endif; ?>
</div><!-- #primary -->

<?php get_footer(); ?>