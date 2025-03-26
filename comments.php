<?php
/**
 * COMMENTS TEMPLATE
 */

if (post_password_required()) {
    return;
}

// 缓存常用配置
$comment_area_style = iro_opt('comment_area');
$show_robot_checkbox = iro_opt('not_robot');
$private_message_enabled = iro_opt('comment_private_message');
$mail_notify_enabled = iro_opt('mail_notify');
$smilies_list = iro_opt('smilies_list');
$vision_resource_basepath = iro_opt('vision_resource_basepath', 'https://s.nmxc.ltd/sakurairo_vision/@2.7/');
?>

<?php if (comments_open()) : ?>
    <section id="comments" class="comments">
        <!-- 评论折叠容器 -->
        <div class="commentwrap comments-hidden<?= $comment_area_style === 'fold' ? ' comments-fold' : '' ?>">
            <div class="notification">
                <i class="fa-regular fa-comment"></i><?= esc_html__('view comments', 'sakurairo') ?>
                <span class="noticom"><?= comments_number('NOTHING', '1 ' . esc_html__('comment', 'sakurairo'), '%' . esc_html__('comments', 'sakurairo')) ?></span>
            </div>
        </div>

        <div class="comments-main">
            <h3 id="comments-list-title"><?= esc_html__('Comments', 'sakurairo') ?> <span class="noticom"><?= comments_number('NOTHING', '1 ' . esc_html__('comment', 'sakurairo'), '%' . esc_html__('comments', 'sakurairo')) ?></span></h3>
            <div id="loading-comments"><span></span></div>

            <?php if (have_comments()) : ?>
                <ul class="commentwrap">
                    <?php wp_list_comments([
                        'type' => 'comment',
                        'callback' => 'akina_comment_format',
                        'reverse_top_level' => get_option('comment_order') === 'asc' ? null : true
                    ]); ?>
                </ul>
                <nav id="comments-navi"><?= paginate_comments_links(['prev_text' => '« Older', 'next_text' => 'Newer »', 'echo' => false]) ?></nav>
            <?php else : ?>
                <div class="commentwrap">
                    <div class="notification-hidden">
                        <i class="fa-regular fa-comment"></i><?= esc_html__('no comment', 'sakurairo') ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php
            // 构建表情面板
            $smilies_panel = '';
            if ($smilies_list) {
                $smilies_config = [
                    'bilibili' => ['title' => 'bilibili~', 'class' => 'bili-bar', 'container' => 'bili-container', 'func' => 'push_bili_smilies'],
                    'tieba' => ['title' => 'Tieba', 'class' => 'tieba-bar', 'container' => 'tieba-container', 'func' => 'push_tieba_smilies'],
                    'yanwenzi' => ['title' => '(=・ω・=)', 'class' => 'menhera-bar', 'container' => 'menhera-container', 'func' => 'push_emoji_panel'],
                    'custom' => ['title' => iro_opt('smilies_name'), 'class' => 'custom-bar', 'container' => 'custom-container', 'func' => 'push_custom_smilies']
                ];

                $tabs = $panels = [];
                foreach ($smilies_config as $key => $config) {
                    if (in_array($key, $smilies_list)) {
                        $active = $key === $smilies_list[0];
                        $tabs[] = sprintf(
                            '<th class="%s%s">%s</th>',
                            esc_attr($config['class']),
                            $active ? ' on-hover' : '',
                            esc_html($config['title'])
                        );
                        $panels[] = sprintf(
                            '<div class="%s motion-container" style="display:%s">%s</div>',
                            esc_attr($config['container']),
                            $active ? 'block' : 'none',
                            call_user_func($config['func'])
                        );
                    }
                }

                $smilies_panel = sprintf(
                    '<p id="emotion-toggle" class="no-select">
                        <span class="emotion-toggle-off">%s</span>
                        <span class="emotion-toggle-on">%s</span>
                     </p>
                     <div class="emotion-box no-select">
                        <table class="motion-switcher-table"><tr>%s</tr></table>
                        %s
                     </div>',
                    esc_html__('Click me OωO', 'sakurairo'),
                    esc_html__('Woooooow ヾ(≧∇≦*)ゝ', 'sakurairo'),
                    implode('', $tabs),
                    implode('', $panels)
                );
            }

            // 构建评论表单参数
            $args = [
                'id_form' => 'commentform',
                'id_submit' => 'submit',
                'title_reply' => '',
                'title_reply_to' => '<div class="graybar"><i class="fa-regular fa-comment"></i>' . esc_html__('Leave a Reply to', 'sakurairo') . ' %s</div>',
                'cancel_reply_link' => esc_html__('Cancel Reply', 'sakurairo'),
                'label_submit' => esc_html__('BiuBiuBiu~', 'sakurairo'),
                'comment_field' => sprintf(
                    '<p style="font-style:italic">
                        <a href="%s" target="_blank"><i class="fa-brands fa-markdown" style="color:var(--article-theme-highlight,var(--theme-skin-matching));"></i></a> 
                        %s
                    </p>
                    <div class="comment-textarea">
                        <textarea placeholder="%s" name="comment" class="commentbody" id="comment" rows="5" tabindex="4"></textarea>
                        <label class="input-label">%s</label>
                    </div>
                    <div id="upload-img-show"></div>
                    %s',
                    esc_url('https://segmentfault.com/markdown'),
                    esc_html__('Markdown Supported while', 'sakurairo') . ' <i class="fa-solid fa-code"></i> ' . esc_html__('Forbidden', 'sakurairo'),
                    esc_attr__('You are a surprise that I will only meet once in my life', 'sakurairo'),
                    esc_attr__('You are a surprise that I will only meet once in my life', 'sakurairo'),
                    $smilies_panel
                ),
                'fields' => [
                    'avatar' => sprintf(
                        '<div class="cmt-info-container">
                            <div class="comment-user-avatar">
                                <img alt="%s" src="%s">
                                <div class="socila-check qq-check"><i class="fa-brands fa-qq"></i></div>
                                <div class="socila-check gravatar-check"><i class="fa-solid fa-face-kiss-wink-heart"></i></div>
                            </div>',
                        esc_attr__('comment_user_avatar', 'sakurairo'),
                        esc_url($vision_resource_basepath . 'basic/avatar.jpeg')
                    ),
                    'author' => sprintf(
                        '<div class="popup cmt-popup cmt-author">
                            <input type="text" placeholder="%s" name="author" id="author" value="%s" size="22" autocomplete="off" tabindex="1" %s/>
                            <span class="popuptext">%s</span>
                        </div>',
                        esc_attr(sprintf(
                            '%s %s',
                            esc_html__('Nickname or QQ number', 'sakurairo'),
                            $req ? '(' . esc_html__('Name*', 'sakurairo') . ')' : ''
                        )),
                        esc_attr($comment_author),
                        $req ? 'aria-required="true"' : '',
                        esc_html__('Auto pull nickname and avatar with a QQ num. entered', 'sakurairo')
                    ),
                    'email' => sprintf(
                        '<div class="popup cmt-popup">
                            <input type="text" placeholder="%s" name="email" id="email" value="%s" size="22" tabindex="1" autocomplete="off" %s/>
                            <span class="popuptext">%s</span>
                        </div>',
                        esc_attr(sprintf(
                            '%s %s',
                            esc_html__('email', 'sakurairo'),
                            $req ? '(' . esc_html__('Must*', 'sakurairo') . ')' : ''
                        )),
                        esc_attr($comment_author_email),
                        $req ? 'aria-required="true"' : '',
                        esc_html__('You will receive notification by email', 'sakurairo')
                    ),
                    'url' => sprintf(
                        '<div class="popup cmt-popup">
                            <input type="text" placeholder="%s" name="url" id="url" value="%s" size="22" autocomplete="off" tabindex="1"/>
                            <span class="popuptext">%s</span>
                        </div>
                        </div>
                        %s
                        %s
                        %s',
                        esc_attr__('Site', 'sakurairo'),
                        esc_attr($comment_author_url),
                        esc_html__('Advertisement is forbidden 😀', 'sakurairo'),
                        $show_robot_checkbox ? '<label class="siren-checkbox-label"><input class="siren-checkbox-radio" type="checkbox" name="no-robot"><span class="siren-no-robot-checkbox siren-checkbox-radioInput"></span>' . esc_html__('I\'m not a robot', 'sakurairo') . '</label>' : '',
                        $private_message_enabled ? '<label class="siren-checkbox-label"><input class="siren-checkbox-radio" type="checkbox" name="is-private"><span class="siren-is-private-checkbox siren-checkbox-radioInput"></span>' . esc_html__('Comment in private', 'sakurairo') . '</label>' : '',
                        $mail_notify_enabled ? '<label class="siren-checkbox-label"><input class="siren-checkbox-radio" type="checkbox" name="mail-notify"><span class="siren-mail-notify-checkbox siren-checkbox-radioInput"></span>' . esc_html__('Comment reply notify', 'sakurairo') . '</label>' : ''
                    ),
                    'qq' => '<input type="text" placeholder="QQ" name="new_field_qq" id="qq" value="' . esc_attr($comment_author_url) . '" style="display:none" autocomplete="off"/>'
                ]
            ];

            comment_form($args);
            ?>
        </div>
    </section>
<?php endif; ?>