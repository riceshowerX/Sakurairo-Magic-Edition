<?php
get_header();

// 缓存作者信息
$author_id = get_the_author_meta('ID');
$post_count = count_user_posts($author_id, 'post');
$author_name = get_the_author();
$author_description = get_the_author_meta('description');
$pagenav_style = iro_opt('pagenav_style');
?>

<div class="author_info">
    <div class="avatar" data-post-count="<?= esc_attr($post_count) ?>">
        <?= get_avatar($author_id) ?>
    </div>
    <div class="author-center">
        <h3><?= esc_html($author_name) ?></h3>
        <div class="description">
            <?= !empty($author_description) 
                ? nl2br(esc_html($author_description)) 
                : esc_html__('No personal profile set yet', 'sakurairo') 
            ?>
        </div>
    </div>
</div>

<style>
.author_info .avatar::after {
    content: attr(data-post-count) " \f044";
    font-family: 'FontAwesome';
    position: absolute;
    right: -8px;
    bottom: 16px;
    background-color: #fff;
    padding: 5px;
    border-radius: 5px;
    font-size: 12px;
    color: var(--theme-skin-matching, #505050);
    box-shadow: 0 1px 30px -4px #e8e8e8;
    background: rgba(255, 255, 255, 0.7);
    padding: 2px 8px;
    transition: all 0.6s ease-in-out;
    border-radius: 16px;
    border: 1px solid #fff;
    backdrop-filter: saturate(180%) blur(10px);
    -webkit-backdrop-filter: saturate(180%) blur(10px);
}
</style>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <?php if (have_posts()) : ?>
            <?php get_template_part('tpl/content', 'thumb') ?>
            <div class="clearer"></div>
        <?php else : ?>
            <?php get_template_part('tpl/content', 'none') ?>
        <?php endif; ?>
    </main>

    <?php if ($pagenav_style === 'ajax') : ?>
        <div id="pagination"><?= next_posts_link(__(' Previous', 'sakurairo')) ?></div>
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
</div>

<?php get_footer(); ?>