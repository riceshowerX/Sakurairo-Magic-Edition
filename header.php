<?php
/**
 * The header for our theme.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package Sakurairo
 */

if (!defined('ABSPATH')) exit;

// 基础路径配置优化
$core_lib_basepath = iro_opt('core_library_basepath') 
    ? get_template_directory_uri() 
    : sprintf('%s%s', iro_opt('lib_cdn_path', 'https://fastly.jsdelivr.net/gh/mirai-mamori/Sakurairo@'), IRO_VERSION);

header('X-Frame-Options: SAMEORIGIN');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="theme-color">
    
    <?php if (iro_opt('iro_meta')): 
        $meta_description = iro_opt('iro_meta_description');
        $meta_keywords = iro_opt('iro_meta_keywords');

        // 动态生成元数据
        if (is_singular()) {
            $tags = get_the_tags();
            $meta_keywords = $tags ? implode(',', wp_list_pluck($tags, 'name')) : $meta_keywords;
            $meta_description = trim(mb_strimwidth(preg_replace('/\s+/', ' ', strip_tags($post->post_content)), 0, 240, '…'));
        } elseif (is_category()) {
            $categories = get_the_category();
            $meta_keywords = $categories ? implode(',', wp_list_pluck($categories, 'name')) : $meta_keywords;
            $meta_description = trim(category_description()) ?: $meta_description;
        }
    ?>
        <meta name="description" content="<?= esc_attr($meta_description); ?>">
        <meta name="keywords" content="<?= esc_attr($meta_keywords); ?>">
    <?php endif; ?>

    <link rel="shortcut icon" href="<?= esc_url(iro_opt('favicon_link', '')); ?>">
    <link rel="stylesheet" href="https://s4.zstatic.net/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    
    <?php 
    // 合并Google Fonts请求
    $gfonts_add = iro_opt('gfonts_add_name');
    if (!empty($gfonts_add)) {
        $gfonts_add = '|' . ltrim($gfonts_add, '|');
    }
    ?>
    <link href="https://<?= esc_attr(iro_opt('gfonts_api', 'fonts.googleapis.com')); ?>/css?family=Noto+Serif+SC|Noto+Sans+SC|Dela+Gothic+One|Fira+Code<?= $gfonts_add ?>&display=swap" rel="stylesheet">
    
    <?php if (is_home()): ?>
        <link id="entry-content-css" rel="prefetch" as="style" href="<?= esc_url("{$core_lib_basepath}/css/theme/" . (iro_opt('entry_content_style') == 'sakurairo' ? 'sakura' : 'github') . ".css?ver=" . IRO_VERSION) ?>">
        <link rel="prefetch" as="script" href="<?= esc_url("{$core_lib_basepath}/js/page.js?ver=" . IRO_VERSION) ?>">
    <?php endif; ?>

    <?php wp_head(); ?>
    
    <?php if (iro_opt('google_analytics_id')): ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= esc_attr(iro_opt('google_analytics_id')); ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?= esc_attr(iro_opt('google_analytics_id')); ?>');
        </script>
    <?php endif; ?>

    <?= iro_opt("site_header_insert"); ?>

    <?php if (iro_opt('poi_pjax') && iro_opt("pjax_keep_loading")): ?>
        <script>
            document.addEventListener("pjax:complete", () => {
                <?= addslashes(iro_opt("pjax_keep_loading")) ?>
                    .split(/[\s,]+/)
                    .filter(Boolean)
                    .forEach(src => {
                        const elem = document.createElement(src.endsWith('.js') ? 'script' : 'link');
                        elem[src.endsWith('.js') ? 'src' : 'href'] = src;
                        elem.rel = 'stylesheet';
                        elem.async = true;
                        document[src.endsWith('.js') ? 'body' : 'head'].appendChild(elem);
                    });
        </script>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 平滑滚动修复
            document.querySelectorAll('a[href^="#"]').forEach(link => {
                link.addEventListener('click', e => {
                    e.preventDefault();
                    document.getElementById(link.hash.slice(1))?.scrollIntoView({ 
                        behavior: 'smooth' 
                    });
                });
            });
        });
    </script>

    <?php 
    // 修复nav.js加载问题，添加defer属性
    ?>
    <script src="<?= "{$core_lib_basepath}/js/nav.js" ?>" defer></script>
</head>

<body <?php body_class(); ?>>
    <?php if (iro_opt('preload_animation', 'true')): ?>
        <div id="preload">
            <li data-id="3" class="active">
                <div id="preloader_3"></div>
            </li>
        </div>
    <?php endif; ?>

    <div class="scrollbar" id="bar"></div>
    
    <header class="site-header no-select" role="banner">
        <?php if ($show_branding = iro_opt('iro_logo') || !empty(iro_opt('nav_text_logo')['text'])): ?>
            <div class="site-branding">
                <a href="<?= esc_url(home_url('/')); ?>">
                    <?php if (iro_opt('iro_logo')): ?>
                        <div class="site-title-logo">
                            <img 
                                src="<?= esc_url(iro_opt('iro_logo')); ?>"
                                alt="<?= esc_attr(get_bloginfo('name')); ?>"
                                loading="lazy"
                                decoding="async"
                            >
                        </div>
                    <?php endif; ?>
                    <?php if (!empty(iro_opt('nav_text_logo')['text'])): ?>
                        <div class="site-title"><?= esc_html(iro_opt('nav_text_logo')['text']); ?></div>
                    <?php endif; ?>
                </a>
            </div>
        <?php endif; ?>

        <div class="nav-search-wrapper">
            <nav><?php wp_nav_menu(['depth' => 2, 'theme_location' => 'primary']); ?></nav>
            
            <?php 
            $show_search = iro_opt('nav_menu_search');
            $show_bg_switch = iro_opt('cover_random_graphs_switch');
            if ($show_search || $show_bg_switch): ?>
                <div class="nav-search-divider"></div>
            <?php endif; ?>

            <?php if ($show_search): ?>
                <div class="searchbox js-toggle-search" role="search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span class="screen-reader-text"><?= esc_html__('Search', 'sakurairo'); ?></span>
                </div>
            <?php endif; ?>

            <?php if (iro_opt('cover_switch') && $show_bg_switch): ?>
                <div class="bg-switch" id="bg-next" style="display:none">
                    <i class="fa-solid fa-dice"></i>
                    <span class="screen-reader-text"><?= esc_html__('Random Background', 'sakurairo'); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <?php if (iro_opt('nav_menu_user_avatar')): ?>
            <div class="user-menu-wrapper"><?php header_user_menu(); ?></div>
        <?php endif; ?>
    </header>

    <div class="openNav no-select">
        <div class="iconflat no-select"></div>
    </div>

    <section id="main-container">
        <?php if (iro_opt('cover_switch')): ?>
            <div class="headertop <?= esc_attr(iro_opt('random_graphs_filter')); ?>">
                <?php get_template_part('layouts/imgbox'); ?>
            </div>
        <?php endif; ?>

        <div id="page" class="site wrapper">
            <?php
            $cover_type = get_post_meta(get_the_ID(), 'cover_type', true);
            $use_as_thumb = get_post_meta(get_the_ID(), 'use_as_thumb', true);

            if ($use_as_thumb !== 'only') {
                if (in_array($cover_type, ['hls', 'normal'])) {
                    the_video_headPattern($cover_type === 'hls');
                } else {
                    the_headPattern();
                }
            } else {
                the_headPattern();
            }
            ?>
            <div id="content" class="site-content">