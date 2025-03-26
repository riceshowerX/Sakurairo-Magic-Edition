<?php
get_header();

// 公告板模块
if (iro_opt('bulletin_board') === '1') {
    $bulletin_text = esc_html(iro_opt('bulletin_text'));
    $show_icon = iro_opt('bulletin_board_icon', 'true');
    ?>
    <div class="notice" style="margin-top:60px">
        <?php if ($show_icon) : ?>
            <div class="notice-icon"><?= esc_html__('Notice', 'sakurairo') ?></div>
        <?php endif; ?>
        <div class="notice-content">
            <?= strlen($bulletin_text) > 142 
                ? "<div class='scrolling-text'>{$bulletin_text}</div>" 
                : $bulletin_text 
            ?>
        </div>
    </div>
    <?php
}

// 展览区模块
if (iro_opt('exhibition_area') === '1') {
    $layout = iro_opt('exhibition_area_style') === 'left_and_right' 
        ? 'feature_v2' 
        : 'feature';
    get_template_part("layouts/{$layout}");
}
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <h1 class="main-title">
            <i class="<?= esc_attr(iro_opt('post_area_icon', 'fa-regular fa-bookmark')) ?>" aria-hidden="true"></i>
            <br>
            <?= esc_html(iro_opt('post_area_title', '文章')) ?>
        </h1>

        <?php if (have_posts()) : ?>
            <?php if (is_home() && !is_front_page()) : ?>
                <header>
                    <h1 class="page-title screen-reader-text"><?= single_post_title('', false) ?></h1>
                </header>
            <?php endif; ?>
            <?php get_template_part('tpl/content', 'thumb') ?>
        <?php else : ?>
            <?php get_template_part('tpl/content', 'none') ?>
        <?php endif; ?>
    </main>

    <?php if (iro_opt('pagenav_style') === 'ajax') : ?>
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