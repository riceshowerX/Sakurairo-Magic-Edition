<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Sakura
 */
$reception_background = iro_opt('reception_background');
?>
  </div><!-- #content -->
  <?php comments_template('', true); ?>
</div><!-- #page Pjax container-->
<footer id="colophon" class="site-footer" role="contentinfo">
    <div class="site-info" theme-info="Sakurairo v<?= esc_html(IRO_VERSION); ?>">
        <div class="footertext">
            <div class="img-preload">
                <img alt="<?= esc_attr__('loading_svg', 'sakurairo'); ?>" 
                     src="<?= esc_url(iro_opt('load_nextpage_svg')); ?>">
            </div>
            <?php if (iro_opt('footer_sakura', 'true')): ?>
                <div class="sakura-icon">
                    <svg width="30px" height="30px" viewBox="0 0 1049 1024" 
                         xmlns="http://www.w3.org/2000/svg" class="sakura-svg">
                        <!-- SVG paths remain unchanged -->
                    </svg>
                </div>
            <?php endif; ?>
            <p style="color: #666666;"><?= iro_opt('footer_info', ''); ?></p>
        </div>
        <div class="footer-device function_area">
            <?php if (iro_opt('footer_yiyan')): ?>
                <p id="footer_yiyan"></p>
            <?php endif; ?>
            <span style="color: #b9b9b9;">
                <?php if (iro_opt('footer_load_occupancy', 'true')): ?>
                    <?= sprintf(
                        esc_html__('Load Time %.3f seconds | %d Query | RAM Usage %.2f MB ', 'sakurairo'),
                        timer_stop(0, 3),
                        get_num_queries(),
                        memory_get_peak_usage() / 1024 / 1024
                    ); ?>
                <?php endif; ?>
                <?php if (iro_opt('footer_upyun', 'true')): ?>
                    <?= sprintf(
                        __('本网站由 %s 提供 CDN 加速 / 云存储 服务', 'sakurairo'),
                        '<a href="https://www.upyun.com/?utm_source=lianmeng&utm_medium=referral" target="_blank">'
                        . '<img alt="upyun-logo" src="https://s.nmxc.ltd/sakurairo_vision/@2.7/options/upyun_logo.webp" style="display:inline-block;vertical-align:middle;width:60px;height:30px;"/></a>'
                    ); ?>
                <?php endif; ?>
                <br>
                <a href="https://github.com/mirai-mamori/Sakurairo" target="_blank" id="site-info">Theme Sakurairo</a>
                <a href="https://docs.fuukei.org/" target="_blank" id="site-info"> by Fuukei</a>
            </span>
        </div>
    </div><!-- .site-info -->
</footer><!-- #colophon -->
</section><!-- #section -->
<!-- m-nav-center -->
<div id="mo-nav">
    <?php if (iro_opt('mobile_menu_user_avatar', 'true')): ?>
        <div class="m-avatar">
            <?= wp_get_current_user()->exists() 
                ? '<img alt="m-avatar" src="' . esc_url(get_avatar_url(get_current_user_id(), ['size' => 64])) . '">' 
                : (iro_opt('unlisted_avatar') 
                    ? '<img alt="m-avatar" src="' . esc_url(iro_opt('unlisted_avatar')) . '">' 
                    : '<i class="fa-solid fa-circle-user fa-2x"></i>'); ?>
        </div>
    <?php endif; ?>
    <?php if (wp_is_mobile() && iro_opt('mobile_menu_user_avatar', 'true')) m_user_menu(); ?>
    <div class="m-search">
        <form class="m-search-form" method="get" action="<?= esc_url(home_url()); ?>" role="search">
            <input class="m-search-input" type="search" name="s" 
                   placeholder="<?= esc_attr__('Search...', 'sakurairo'); ?>" required>
        </form>
    </div>
    <?php wp_nav_menu(['depth' => 2, 'theme_location' => 'primary', 'container' => false]); ?>
</div><!-- m-nav-center end -->
<button id="moblieGoTop" title="<?= esc_attr__('Go to top', 'sakurairo'); ?>">
    <i class="fa-solid fa-caret-up fa-lg"></i>
</button>
<button id="changskin" title="<?= esc_attr__('Control Panel', 'sakurairo'); ?>">
    <i class="fa-solid fa-compass-drafting fa-lg fa-flip"></i>
</button>
<!-- search start -->
<form class="js-search search-form search-form--modal" method="get" action="<?= esc_url(home_url()); ?>" role="search">
    <div class="search-form__inner">
        <?php if (iro_opt('live_search')): ?>
            <div class="micro">
                <input id="search-input" class="text-input" type="search" name="s" 
                       placeholder="<?= esc_attr__('Want to find something?', 'sakurairo'); ?>" required>
            </div>
            <div class="ins-section-wrapper">
                <a id="Ty" href="#"></a>
                <div class="ins-section-container" id="PostlistBox"></div>
            </div>
        <?php else: ?>
            <div class="micro">
                <p class="micro mb-"><?= esc_html__('Want to find something?', 'sakurairo'); ?></p>
                <input class="text-input" type="search" name="s" 
                       placeholder="<?= esc_attr__('Search', 'sakurairo'); ?>" required>
            </div>
        <?php endif; ?>
    </div>
    <div class="search_close"></div>
</form>
<!-- search end -->
<?php wp_footer(); ?>
<div class="skin-menu no-select">
    <?php if (iro_opt('sakura_widget')): ?>
        <aside id="iro-widget" class="widget-area" role="complementary">
            <div class="sakura_widget"><?= dynamic_sidebar('sakura_widget'); ?></div>
        </aside>
    <?php endif; ?>
    <div class="theme-controls row-container">
        <?php if (iro_opt('widget_daynight', 'true')): ?>
            <ul class="menu-list">
                <li id="white-bg" title="<?= esc_attr__('Light Mode', 'sakurairo'); ?>">
                    <i class="fa-regular fa-sun"></i>
                </li>
                <li id="dark-bg" title="<?= esc_attr__('Dark Mode', 'sakurairo'); ?>">
                    <i class="fa-regular fa-moon"></i>
                </li>
            </ul>
        <?php endif; ?>
        <?php if (is_array($reception_background) && in_array(1, $reception_background)): ?>
            <ul class="menu-list" title="<?= esc_attr__('Toggle Page Background Image', 'sakurairo'); ?>">
                <?php foreach ([
                    ['heart_shaped', 'fa-heart', 'diy1-bg'],
                    ['star_shaped', 'fa-star', 'diy2-bg'],
                    ['square_shaped', 'fa-delicious', 'diy3-bg'],
                    ['lemon_shaped', 'fa-lemon', 'diy4-bg']
                ] as $bg) {
                    if (!empty($reception_background[$bg[0]])) {
                        printf('<li id="%s"><i class="fa-regular %s"></i></li>', 
                            esc_attr($bg[2]), esc_attr($bg[1]));
                    }
                } ?>
            </ul>
        <?php endif; ?>
        <?php if (iro_opt('widget_font', 'true')): ?>  
            <div class="font-family-controls row-container">
                <button type="button" class="control-btn-serif selected" 
                        title="<?= esc_attr__('Switch To Font A', 'sakurairo'); ?>" data-name="serif">
                    <i class="fa-solid fa-font fa-lg"></i>
                </button>
                <button type="button" class="control-btn-sans-serif" 
                        title="<?= esc_attr__('Switch To Font B', 'sakurairo'); ?>" data-name="sans-serif">
                    <i class="fa-solid fa-bold fa-lg"></i>
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php if (iro_opt('aplayer_server') != 'off'): ?>
    <div id="aplayer-float" class="aplayer" 
         data-id="<?= esc_attr(iro_opt('aplayer_playlistid', '')); ?>"
         data-server="<?= esc_attr(iro_opt('aplayer_server')); ?>"
         data-preload="<?= esc_attr(iro_opt('aplayer_preload')); ?>"
         data-type="playlist"
         data-fixed="true"
         data-order="<?= esc_attr(iro_opt('aplayer_order')); ?>"
         data-volume="<?= esc_attr(iro_opt('aplayer_volume', '')); ?>"
         data-theme="<?= esc_attr(iro_opt('theme_skin')); ?>">
    </div>
<?php endif; ?>
<?php if (iro_opt('wave_effects', 'true')): ?>
    <?php $shared_lib_basepath = iro_opt('shared_library_basepath') 
        ? get_template_directory_uri() 
        : sprintf('%s%s', iro_opt('lib_cdn_path', 'https://fastly.jsdelivr.net/gh/mirai-mamori/Sakurairo@'), IRO_VERSION); ?>
    <link rel="stylesheet" href="<?= esc_url($shared_lib_basepath . '/css/wave.css'); ?>">
<?php endif; ?>
<?= iro_opt('footer_addition', ''); ?>
</body>
<?php if (iro_opt('particles_effects', 'true')): ?>
    <style>
        #particles-js {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
    </style>
    <div id="particles-js"></div>
    <script type="application/json" id="particles-js-cfg"><?= iro_opt('particles_json', ''); ?></script>
<?php endif; ?>
</html>