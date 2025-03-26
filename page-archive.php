<?php
/**
 * Template Name: 文章模版
 */

get_header();

while (have_posts()) : the_post();
    $has_patternimg = iro_opt('patternimg');
    $post_thumbnail_id = get_post_thumbnail_id(get_the_ID());
    ?>

    <article <?php post_class("post-item"); ?>>
        <?php the_content('', true); ?>
        <div id="archives-temp">
            <?php if (!$has_patternimg || !$post_thumbnail_id) : ?>
                <h2>
                    <i class="fa-solid fa-calendar-day"></i>
                    <?php the_title(); ?>
                </h2>
            <?php endif; ?>
            
            <div id="archives-content">
                <?php
                $the_query = new WP_Query([
                    'posts_per_page' => -1,
                    'ignore_sticky_posts' => 1
                ]);

                $year = $mon = 0;
                $output = '';

                while ($the_query->have_posts()) : $the_query->the_post();
                    $year_current = get_the_time('Y');
                    $mon_current = get_the_time('n');

                    // 处理月份分组闭合
                    if ($mon !== $mon_current && $mon !== 0) {
                        $output .= '</div></div>';
                    }

                    // 处理年份分组
                    if ($year !== $year_current) {
                        $year = $year_current;
                        $output .= "<div class='archive-title' id='arti-$year-$mon_current'>";
                        $output .= "<h3>$year-$mon_current</h3>";
                        $output .= "<div class='archives archives-$mon_current' id='monlist' data-date='$year-$mon_current'>";
                    } elseif ($mon !== $mon_current) {
                        $mon = $mon_current;
                        $output .= "<div class='archive-title' id='arti-$year-$mon'>";
                        $output .= "<h3>$year-$mon</h3>";
                        $output .= "<div class='archives archives-$mon' id='monlist' data-date='$year-$mon'>";
                    }

                    // 构建文章条目
                    $output .= sprintf(
                        '<span class="ar-circle"></span>
                        <div class="arrow-left-ar"></div>
                        <div class="brick">
                            <a href="%1$s">
                                <span class="time">
                                    <i class="fa-regular fa-clock"></i>%2$s
                                </span>%3$s
                                <em>(%4$s)</em>
                            </a>
                        </div>',
                        esc_url(get_permalink()),
                        esc_html(get_the_time('n-d')),
                        esc_html(get_the_title()),
                        esc_html(get_comments_number('0', '1', '%'))
                    );
                endwhile;

                wp_reset_postdata();
                $output .= '</div></div>'; // 关闭最后一个分组
                echo $output;
                ?>
            </div>
        </div>
    </article>

<?php endwhile;

get_footer();