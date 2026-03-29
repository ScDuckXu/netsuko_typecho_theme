<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

function themeConfig($form)
{
    $authorAvatar = new \Typecho\Widget\Helper\Form\Element\Text('authorAvatar', null, 'https://cravatar.cn/avatar/default?d=mp', _t('作者头像 URL'), _t('侧栏名片的圆角头像'));
    $form->addInput($authorAvatar);

    $authorName = new \Typecho\Widget\Helper\Form\Element\Text('authorName', null, 'Netsuko', _t('作者显示姓名'), _t('侧边栏名片的名字'));
    $form->addInput($authorName);

    $motto = new \Typecho\Widget\Helper\Form\Element\Text('motto', null, '你跳向自己的厄运是改变不了世界的', _t('全站座右铭'), _t('显示在首页 Banner 和侧栏名片中'));
    $form->addInput($motto);

    $indexBanner = new \Typecho\Widget\Helper\Form\Element\Text(
        'indexBanner',
        NULL,
        NULL,
        _t('首页 Banner 图片'),
        _t('填入图片 URL。如果留空，则默认显示纯色背景。')
    );
    $form->addInput($indexBanner);

    $defaultThumb = new \Typecho\Widget\Helper\Form\Element\Text(
        'defaultThumb',
        NULL,
        NULL,
        _t('默认文章缩略图'),
        _t('填入图片 URL。当文章没有设置“自定义头图”，且正文中也没有任何图片时，显示这张图片。')
    );
    $form->addInput($defaultThumb);

    $githubUrl = new \Typecho\Widget\Helper\Form\Element\Text('githubUrl', null, '', _t('GitHub 链接'), _t('社交图标链接'));
    $form->addInput($githubUrl);
    
    $postFont = new \Typecho\Widget\Helper\Form\Element\Radio('postFont',
        array('sans' => _t('无衬线体 (Sans-serif)'), 'serif' => _t('衬线体 (Serif)')),
        'sans', _t('文章正文字体'), _t('选择正文阅读字体。'));
    $form->addInput($postFont);

    $mottoFont = new \Typecho\Widget\Helper\Form\Element\Radio('mottoFont',
        array('playfair' => _t('衬线体 (Playfair Display)'), 'sans' => _t('无衬线体 (默认字体)')),
        'playfair', _t('座右铭字体'), _t('关于页和部分区域的座右铭字体风格。'));
    $form->addInput($mottoFont);
    
    $mottoQuotes = new \Typecho\Widget\Helper\Form\Element\Radio('mottoQuotes',
        array('show' => _t('显示双引号'), 'hide' => _t('隐藏双引号')),
        'show', _t('座右铭双引号装饰'), _t('在座右铭两侧包裹双引号。'));
    $form->addInput($mottoQuotes);

    $socialTwitter = new \Typecho\Widget\Helper\Form\Element\Text('socialTwitter', NULL, NULL, _t('Twitter/X 链接'), _t('填写完整 URL，留空则不显示该图标。'));
    $form->addInput($socialTwitter);
    
    $socialTelegram = new \Typecho\Widget\Helper\Form\Element\Text('socialTelegram', NULL, NULL, _t('Telegram 链接'), _t('填写完整 URL，留空则不显示该图标。'));
    $form->addInput($socialTelegram);

    $socialEmail = new \Typecho\Widget\Helper\Form\Element\Text('socialEmail', NULL, NULL, _t('Email 邮箱地址'), _t('填写邮箱地址，留空则不显示该图标。'));
    $form->addInput($socialEmail);

    $sidebarLinks = new \Typecho\Widget\Helper\Form\Element\Textarea('sidebarLinks', NULL, NULL, _t('侧边栏自定义超链接'), _t('一行一个链接，格式为：名称|链接。例如：<br><code>链接名称|https://example.com</code><br>留空则不显示。'));
    $form->addInput($sidebarLinks);

    $icpNum = new \Typecho\Widget\Helper\Form\Element\Text('icpNum', NULL, NULL, _t('ICP 备案号'), _t('例如：XICP备xxxxxx号。填写后会自动在页脚显示并链接到工信部，留空则隐藏。'));
    $form->addInput($icpNum);
}


function themeFields($layout) {
    $thumb = new \Typecho\Widget\Helper\Form\Element\Text(
        'thumb', 
        NULL, 
        NULL, 
        _t('自定义头图/缩略图 (可选)'), 
        _t('填入图片 URL。留空时系统将尝试抓取文章内的第一张图片作为封面图。无图片时，使用后台设置的默认展示图。')
    );
    $layout->addItem($thumb);

    $custom_excerpt = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'custom_excerpt', 
        NULL, 
        NULL, 
        _t('自定义摘要'), 
        _t('输入这篇文章的精简摘要。留空时首页将自动截取文章正文的前 30 个字。')
    );
    $layout->addItem($custom_excerpt);
}


function postMeta(\Widget\Archive $archive, string $metaType = 'archive') {
    echo '<div class="flex items-center gap-4 text-xs md:text-sm text-gray-500 dark:text-gray-400 flex-wrap">';

    echo '<span class="flex items-center gap-1.5">';
    echo '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>';
    echo '<time datetime="' . $archive->date('c') . '">' . $archive->date() . '</time>';
    echo '</span>';

    echo '<span class="flex items-center gap-1.5 hover:text-teal transition-colors">';
    echo '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>';
    $archive->category(',');
    echo '</span>';

    echo '<span class="flex items-center gap-1.5 hover:text-teal transition-colors">';
    echo '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>';
    echo '<a href="' . $archive->permalink . '#comments">';
    $archive->commentsNum('暂无评论', '1 条评论', '%d 条评论');
    echo '</a></span>';

    echo '</div>';
}


function getOS($agent) {
    if (preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent)) return 'Win 10/11';
    if (preg_match('/win/i', $agent) && preg_match('/nt 6.3/i', $agent)) return 'Win 8.1';
    if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent)) return 'Win 8';
    if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent)) return 'Win 7';
    if (preg_match('/mac/i', $agent) && preg_match('/os x/i', $agent)) return 'macOS';
    if (preg_match('/linux/i', $agent)) return 'Linux';
    if (preg_match('/android/i', $agent)) return 'Android';
    if (preg_match('/iphone/i', $agent)) return 'iOS';
    if (preg_match('/ipad/i', $agent)) return 'iPadOS';
    return 'Unknown OS';
}

function threadedComments($comments, $options) {
    // 限制嵌套缩进防溢出：0层无缩进；1-2层正常缩进；3层及以上取消左外边距，只保留左侧指示线
    if ($comments->levels == 0) {
        $commentLevelClass = ' mt-8';
    } elseif ($comments->levels > 0 && $comments->levels <= 2) {
        $commentLevelClass = ' ml-6 md:ml-10 mt-6 border-l-2 border-teal/20 pl-4 md:pl-6';
    } else {
        $commentLevelClass = ' mt-6 border-l-2 border-teal/20 pl-4 md:pl-6'; 
    }

    ?>
    <li id="li-<?php $comments->theId(); ?>" class="comment-body<?php echo $commentLevelClass; ?> list-none">
        
        <div id="<?php $comments->theId(); ?>" class="block w-full">
            
            <div class="flex gap-4 group">
                <div class="flex-shrink-0 mt-1">
                    <?php $comments->gravatar('48', 'w-10 h-10 md:w-12 md:h-12 rounded-2xl object-cover shadow-sm border border-gray-100 dark:border-white/5 transition-transform duration-300 group-hover:scale-105 group-hover:shadow-glow'); ?>
                </div>
                <div class="flex-grow w-full overflow-hidden">
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-semibold text-gray-800 dark:text-gray-200 text-sm md:text-base"><?php $comments->author(); ?></span>
                            
                            <span class="text-[10px] md:text-xs px-2 py-0.5 bg-gray-100 dark:bg-white/10 text-gray-500 dark:text-gray-400 rounded-md flex items-center gap-1 border border-gray-200/50 dark:border-white/5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <?php echo getOS($comments->agent); ?>
                            </span>
                            
                            <?php if ($comments->authorId == $comments->ownerId): ?>
                                <span class="text-[10px] px-1.5 py-0.5 bg-teal text-white rounded shadow-sm shadow-teal/30">Author</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed mb-2 break-words">
                        <?php $comments->content(); ?>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-gray-400">
                        <time datetime="<?php $comments->date('c'); ?>"><?php $comments->date('Y-m-d H:i'); ?></time>
                        <span class="text-teal hover:underline cursor-pointer transition-colors">
                            <?php $comments->reply('回复'); ?>
                        </span>
                    </div>
                </div>
            </div>
            </div>
        
        <?php if ($comments->children): ?>
            <div class="comment-children">
                <?php $comments->threadedComments($options); ?>
            </div>
        <?php endif; ?>
    </li>
    <?php
}



function getPostThumb($obj) {
    // 读取文章独立设置
    $thumb = $obj->fields->thumb;
    if (!empty($thumb)) {
        return $thumb;
    }
    
    // 抓取文章正文里的第一张图片
    preg_match_all( "/<[img|IMG].*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/", $obj->content, $matches );
    if(isset($matches[1][0])){
        return $matches[1][0];
    }
    
    // 默认文章缩略图
    $defaultThumb = \Typecho\Widget::widget('Widget_Options')->defaultThumb;
    if (!empty($defaultThumb)) {
        return $defaultThumb;
    }
    
    // 保底图
    return Helper::options()->themeUrl . '/img/bg_watermark.jpg';

}
