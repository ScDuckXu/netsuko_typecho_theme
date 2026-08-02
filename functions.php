<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

\Widget\Feedback::pluginHandle()->comment = 'netsukoVerifyCommentCaptcha';
\Widget\Feedback::pluginHandle()->finishComment = 'netsukoHandleCommentMailNotification';
\Widget\Comments\Edit::pluginHandle()->finishComment = 'netsukoHandleCommentMailNotification';
\Widget\Comments\Edit::pluginHandle()->mark = 'netsukoHandleCommentStatusMailNotification';
register_shutdown_function('netsukoMaybeRunAutoBackup');

function themeConfig($form)
{
    netsukoConfigAdminAssets($form);
    netsukoConfigVersionChecker($form);
    netsukoConfigBackupTools($form);
    netsukoConfigSection($form, '基础资料', '作者信息、阅读字体与默认文章缩略图。');

    // 基础设置
    
    $authorName = new \Typecho\Widget\Helper\Form\Element\Text('authorName', null, 'Netsuko', _t('作者显示姓名'), _t('侧边栏名片的名字'));
    $form->addInput($authorName);

    $authorAvatar = new \Typecho\Widget\Helper\Form\Element\Text('authorAvatar', null, 'https://cravatar.cn/avatar/default?d=mp', _t('作者头像 URL'), _t('侧栏名片的圆角头像'));
    $form->addInput($authorAvatar);

    $postFont = new \Typecho\Widget\Helper\Form\Element\Radio('postFont',
        array('sans' => _t('无衬线体 (Sans-serif)'), 'serif' => _t('衬线体 (Serif)')),
        'sans', _t('文章正文字体'), _t('选择正文阅读字体。'));
    $form->addInput($postFont);

    $defaultThumb = new \Typecho\Widget\Helper\Form\Element\Text(
        'defaultThumb',
        NULL,
        NULL,
        _t('默认文章缩略图'),
        _t('填入图片 URL。当文章没有设置“自定义头图”，且正文中也没有任何图片时，显示这张图片。')
    );
    $form->addInput($defaultThumb);

    netsukoConfigSection($form, '主题配色', '选择全站强调色预设。页面背景继续使用中性色，避免配色过度单一。');

    $colorScheme = new \Typecho\Widget\Helper\Form\Element\Select(
        'colorScheme',
        array(
            'miku' => _t('初音青 (#39C5BB)'),
            'sakura' => _t('樱花粉 (#E85D8F)'),
            'amber' => _t('琥珀金 (#D68A24)'),
            'iris' => _t('鸢尾蓝 (#5B7CDB)'),
            'forest' => _t('森林绿 (#2F9E67)')
        ),
        'miku',
        _t('预制主题配色'),
        _t('控制链接、按钮、边框、进度条和光晕等全站强调色。')
    );
    $form->addInput($colorScheme);

    netsukoConfigSection($form, '静态资源', '选择 Tailwind CSS 与 Fancybox 的加载来源。默认使用主题内置本地资源。');

    $assetSourceOptions = array(
        'local' => _t('本地主题文件'),
        'custom' => _t('自建 CDN（预留）'),
        'jsdelivr' => _t('jsDelivr'),
        'github' => _t('GitHub 源')
    );

    $tailwindAssetSource = new \Typecho\Widget\Helper\Form\Element\Select(
        'tailwindAssetSource',
        $assetSourceOptions,
        'local',
        _t('Tailwind CSS 来源'),
        _t('建议保持本地。自建 CDN 选择后需填写下方 CSS/JS URL 方可生效。')
    );
    $form->addInput($tailwindAssetSource);

    $tailwindCustomUrl = new \Typecho\Widget\Helper\Form\Element\Text(
        'tailwindCustomUrl',
        NULL,
        NULL,
        _t('自建 Tailwind CSS URL'),
        _t('预留给你自己的 CDN，留空时自动回退本地文件。')
    );
    $form->addInput($tailwindCustomUrl);

    $fancyboxAssetSource = new \Typecho\Widget\Helper\Form\Element\Select(
        'fancyboxAssetSource',
        $assetSourceOptions,
        'local',
        _t('Fancybox 来源'),
        _t('画廊页使用。自建 CDN 选择后需填写下方 CSS/JS URL 方可生效。')
    );
    $form->addInput($fancyboxAssetSource);

    $fancyboxCustomCssUrl = new \Typecho\Widget\Helper\Form\Element\Text(
        'fancyboxCustomCssUrl',
        NULL,
        NULL,
        _t('自建 Fancybox CSS URL'),
        _t('预留给你自己的 CDN，留空时自动回退本地文件。')
    );
    $form->addInput($fancyboxCustomCssUrl);

    $fancyboxCustomJsUrl = new \Typecho\Widget\Helper\Form\Element\Text(
        'fancyboxCustomJsUrl',
        NULL,
        NULL,
        _t('自建 Fancybox JS URL'),
        _t('预留给你自己的 CDN，留空时自动回退本地文件。')
    );
    $form->addInput($fancyboxCustomJsUrl);

    netsukoConfigSection($form, '内容增强', '控制正文图片懒加载、代码高亮与 LaTeX 渲染。相关资源均默认从主题本地加载。');

    $lazyLoadEnabled = new \Typecho\Widget\Helper\Form\Element\Radio(
        'lazyLoadEnabled',
        array('on' => _t('开启 LazyLoad'), 'off' => _t('关闭 LazyLoad')),
        'on',
        _t('正文媒体懒加载'),
        _t('开启后会为文章正文中的图片与 iframe 添加原生 loading="lazy"，并在 PJAX 后重新初始化。')
    );
    $form->addInput($lazyLoadEnabled);

    $codeHighlightEnabled = new \Typecho\Widget\Helper\Form\Element\Radio(
        'codeHighlightEnabled',
        array('on' => _t('开启代码高亮'), 'off' => _t('关闭代码高亮')),
        'on',
        _t('代码高亮'),
        _t('使用主题内置 Highlight.js 高亮正文代码块，并提供复制按钮。')
    );
    $form->addInput($codeHighlightEnabled);

    $latexDefaultEnabled = new \Typecho\Widget\Helper\Form\Element\Radio(
        'latexDefaultEnabled',
        array('off' => _t('默认关闭'), 'on' => _t('默认开启')),
        'off',
        _t('LaTeX 默认状态'),
        _t('默认关闭更稳妥。可在每篇文章或独立页的自定义字段中单独修改。')
    );
    $form->addInput($latexDefaultEnabled);

    netsukoConfigSection($form, 'PJAX 全局无刷新', '控制站内链接的无刷新切换。后台、动作地址和表单提交会自动避开。');

    $pjaxEnabled = new \Typecho\Widget\Helper\Form\Element\Radio(
        'pjaxEnabled',
        array('on' => _t('开启 PJAX'), 'off' => _t('关闭 PJAX')),
        'on',
        _t('PJAX 无刷新'),
        _t('开启后会拦截普通站内链接，仅替换主题内容区域，并在失败时自动回退为普通跳转。')
    );
    $form->addInput($pjaxEnabled);

    $pjaxExcludePaths = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'pjaxExcludePaths',
        NULL,
        "/admin/\n/action/\n/install.php\n/index.php/action/",
        _t('PJAX 排除路径'),
        _t('一行一个路径片段。链接中包含这些片段时不会使用 PJAX，适合后台、动作地址、下载页或特殊页面。')
    );
    $form->addInput($pjaxExcludePaths);

    netsukoConfigSection($form, '追番页面', '读取 Bangumi 公开个人资料和动画收藏，并使用本地缓存降低外部接口压力。');

    $bangumiProfile = new \Typecho\Widget\Helper\Form\Element\Text(
        'bangumiProfile',
        NULL,
        NULL,
        _t('Bangumi 个人主页或用户名'),
        _t('例如 https://bgm.tv/user/username 或 username。创建独立页面并选择“追番”模板后生效。')
    );
    $form->addInput($bangumiProfile);

    $bangumiItemsPerStatus = new \Typecho\Widget\Helper\Form\Element\Text(
        'bangumiItemsPerStatus',
        NULL,
        '24',
        _t('每种状态显示数量'),
        _t('范围 1-50，默认 24。分别读取想看、看过、在看、搁置和抛弃。')
    );
    $form->addInput($bangumiItemsPerStatus);

    $bangumiCacheHours = new \Typecho\Widget\Helper\Form\Element\Text(
        'bangumiCacheHours',
        NULL,
        '6',
        _t('Bangumi 缓存时间（小时）'),
        _t('范围 1-168，默认 6。接口暂时不可用时会继续显示上一次成功缓存。')
    );
    $form->addInput($bangumiCacheHours);

    // Banner与座右铭设置
    netsukoConfigSection($form, '首页 Banner 与座右铭', '控制首页座右铭样式和 Banner 展示效果。');

    $mottoBanner = new \Typecho\Widget\Helper\Form\Element\Text('mottoBanner', null, '永远相信美好的事情即将发生', _t('Banner 文本'), _t('显示在首页 Banner 中，默认与座右铭一致'));
    $form->addInput($mottoBanner);

    $motto = new \Typecho\Widget\Helper\Form\Element\Text('motto', null, '永远相信美好的事情即将发生', _t('座右铭'), _t('显示在侧栏名片中'));
    $form->addInput($motto);

    $mottoFont = new \Typecho\Widget\Helper\Form\Element\Radio('mottoFont',
        array('playfair' => _t('衬线体 (Playfair Display)'), 'sans' => _t('无衬线体 (默认字体)')),
        'playfair', _t('座右铭字体'), _t('关于页和部分区域的座右铭字体风格。'));
    $form->addInput($mottoFont);
    
    $mottoQuotes = new \Typecho\Widget\Helper\Form\Element\Radio('mottoQuotes',
        array('show' => _t('显示双引号'), 'hide' => _t('隐藏双引号')),
        'show', _t('座右铭双引号装饰'), _t('在座右铭两侧包裹双引号。'));
    $form->addInput($mottoQuotes);

    $mottoColorLight = new \Typecho\Widget\Helper\Form\Element\Text(
        'mottoColorLight',
        NULL,
        '#1f2937',
        _t('首页座右铭颜色 (日间)'),
        _t('填入 HEX 颜色代码（如 #39C5BB）。可根据 Banner 图像自行修改到合适颜色。')
    );
    $form->addInput($mottoColorLight);

    $mottoColorDark = new \Typecho\Widget\Helper\Form\Element\Text(
        'mottoColorDark',
        NULL,
        '#ffffff',
        _t('首页座右铭颜色 (夜间)'),
        _t('填入夜间模式下的 HEX 颜色代码。')
    );
    $form->addInput($mottoColorDark);

    $indexBanner = new \Typecho\Widget\Helper\Form\Element\Text(
        'indexBanner',
        NULL,
        NULL,
        _t('首页 Banner 图片 (日间/默认)'),
        _t('填入图片 URL。如果只用一张图，请填在这里。')
    );
    $form->addInput($indexBanner);

    $indexBannerDark = new \Typecho\Widget\Helper\Form\Element\Text(
        'indexBannerDark',
        NULL,
        NULL,
        _t('首页 Banner 图片 (夜间模式)'),
        _t('填入夜间模式下的图片 URL。如果留空，夜间模式将沿用上面的默认图。')
    );
    $form->addInput($indexBannerDark);

    $indexBannerHeight = new \Typecho\Widget\Helper\Form\Element\Text(
        'indexBannerHeight',
        NULL,
        '300',
        _t('首页 Banner 高度 (px)'),
        _t('填入纯数字（如 300 或 450），单位为像素。默认值为 300。')
    );
    $form->addInput($indexBannerHeight);

    $bannerOpacity = new \Typecho\Widget\Helper\Form\Element\Text(
        'bannerOpacity', 
        NULL, 
        '50', 
        _t('首页 Banner 遮罩透明度'), 
        _t('填入 0-100 的数字。数字越大遮罩越深（越黑），50 代表 50% 透明度。')
    );
    $form->addInput($bannerOpacity);

    // 侧边栏与自定义链接
    netsukoConfigSection($form, '社交链接与侧边栏', '维护个人主页、社交账号和侧边栏自定义链接。');

    $githubUrl = new \Typecho\Widget\Helper\Form\Element\Text('githubUrl', null, '', _t('GitHub 链接'), _t('填写完整 URL，留空则不显示该图标。'));
    $form->addInput($githubUrl);

    $socialTwitter = new \Typecho\Widget\Helper\Form\Element\Text('socialTwitter', NULL, NULL, _t('Twitter/X 链接'), _t('填写完整 URL，留空则不显示该图标。'));
    $form->addInput($socialTwitter);
    
    $socialTelegram = new \Typecho\Widget\Helper\Form\Element\Text('socialTelegram', NULL, NULL, _t('Telegram 链接'), _t('填写完整 URL，留空则不显示该图标。'));
    $form->addInput($socialTelegram);

    $socialEmail = new \Typecho\Widget\Helper\Form\Element\Text('socialEmail', NULL, NULL, _t('Email 邮箱地址'), _t('填写邮箱地址，留空则不显示该图标。'));
    $form->addInput($socialEmail);
    
    $socialDiscord = new \Typecho\Widget\Helper\Form\Element\Text('socialDiscord', NULL, NULL, _t('Discord 链接'), _t('填写 Discord 邀请链接。'));
    $form->addInput($socialDiscord);

    $socialInstagram = new \Typecho\Widget\Helper\Form\Element\Text('socialInstagram', NULL, NULL, _t('Instagram 链接'), _t('填写完整 URL。'));
    $form->addInput($socialInstagram);

    $socialBilibili = new \Typecho\Widget\Helper\Form\Element\Text('socialBilibili', NULL, NULL, _t('Bilibili 链接'), _t('填写个人主页 URL。'));
    $form->addInput($socialBilibili);

    $socialWechat = new \Typecho\Widget\Helper\Form\Element\Text('socialWechat', NULL, NULL, _t('微信二维码 URL'), _t('填入你的微信二维码图片链接。'));
    $form->addInput($socialWechat);

    $sidebarLinks = new \Typecho\Widget\Helper\Form\Element\Textarea('sidebarLinks', NULL, NULL, _t('侧边栏自定义超链接'), _t('一行一个链接，格式为：名称|链接。例如：<br><code>链接名称|https://example.com</code><br>留空则不显示。'));
    $form->addInput($sidebarLinks);

    //页脚部分
    netsukoConfigSection($form, '页脚与站点信息', '备案、RSS 与状态页等链接。');

    $icpNum = new \Typecho\Widget\Helper\Form\Element\Text('icpNum', NULL, NULL, _t('ICP 备案号'), _t('例如：XICP备xxxxxx号。填写后会自动在页脚显示并链接到工信部，留空则隐藏。'));
    $form->addInput($icpNum);

    $icpUrl = new \Typecho\Widget\Helper\Form\Element\Text('icpUrl', NULL, 'https://beian.miit.gov.cn/', _t('ICP备案链接地址'), _t('默认指向工信部官网。'));
    $form->addInput($icpUrl);

    $rssFeed = new \Typecho\Widget\Helper\Form\Element\Text('rssFeed', NULL, NULL, _t('RSS Feed URL'), _t('填入你的 RSS 订阅链接（通常是 /feed），留空则不显示。'));
    $form->addInput($rssFeed);

    $siteStatusUrl = new \Typecho\Widget\Helper\Form\Element\Text('siteStatusUrl', NULL, NULL, _t('Status 页面 URL'), _t('填入你的监控页或状态页链接，留空则页脚不显示 Status 按钮。'));
    $form->addInput($siteStatusUrl);

    netsukoConfigSection($form, '自定义代码', '放置额外的 Head 代码。');

    $customHeadCode = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'customHeadCode', 
        NULL, 
        NULL, 
        _t('自定义头部代码 (CSS/JS)'), 
        _t('在这里填入你的自定义 CSS (需包含 &lt;style&gt; 标签) 或 JS 脚本 (需包含 &lt;script&gt; 标签)，代码会输出在 &lt;head&gt; 标签结束前。')
    );
    $form->addInput($customHeadCode);

    netsukoConfigSection($form, '评论与安全', '访客评论验证码与后续评论通知。');

    $commentCaptchaMode = new \Typecho\Widget\Helper\Form\Element\Radio(
        'commentCaptchaMode',
        array(
            'off' => _t('关闭验证码'),
            'turnstile' => _t('Cloudflare Turnstile'),
            'local' => _t('本地算术验证码')
        ),
        'off',
        _t('评论验证码'),
        _t('选择访客评论提交时使用的验证码方式。已登录作者无需验证码。')
    );
    $form->addInput($commentCaptchaMode);

    $turnstileSiteKey = new \Typecho\Widget\Helper\Form\Element\Text(
        'turnstileSiteKey',
        NULL,
        NULL,
        _t('Turnstile Site Key'),
        _t('Cloudflare Turnstile 的站点密钥，选择 Turnstile 时必填。')
    );
    $form->addInput($turnstileSiteKey);

    $turnstileSecretKey = new \Typecho\Widget\Helper\Form\Element\Text(
        'turnstileSecretKey',
        NULL,
        NULL,
        _t('Turnstile Secret Key'),
        _t('Cloudflare Turnstile 的服务端密钥，选择 Turnstile 时必填。')
    );
    $form->addInput($turnstileSecretKey);

    $commentPaginationEnabled = new \Typecho\Widget\Helper\Form\Element\Radio(
        'commentPaginationEnabled',
        array('on' => _t('开启评论分页'), 'off' => _t('沿用 Typecho 全局设置')),
        'on',
        _t('评论分页'),
        _t('建议开启。评论较多时只渲染当前页，明显降低移动端首屏压力。')
    );
    $form->addInput($commentPaginationEnabled);

    $commentPaginationSize = new \Typecho\Widget\Helper\Form\Element\Text(
        'commentPaginationSize',
        NULL,
        '20',
        _t('每页评论数'),
        _t('建议 10-30。过大仍会增加移动端渲染压力。')
    );
    $form->addInput($commentPaginationSize);

    $commentPaginationDisplay = new \Typecho\Widget\Helper\Form\Element\Radio(
        'commentPaginationDisplay',
        array('last' => _t('默认显示最后一页'), 'first' => _t('默认显示第一页')),
        'last',
        _t('默认评论页'),
        _t('最后一页更适合新评论优先可见；第一页更适合从头阅读长讨论。')
    );
    $form->addInput($commentPaginationDisplay);

    netsukoConfigSection($form, '评论邮件提醒', '通过 SMTP 发送评论与回复通知，可自定义标题和正文模板。');

    $commentMailEnabled = new \Typecho\Widget\Helper\Form\Element\Radio(
        'commentMailEnabled',
        array('off' => _t('关闭'), 'on' => _t('开启')),
        'off',
        _t('评论邮件提醒'),
        _t('开启后根据下方规则向博主和评论者发送通知。')
    );
    $form->addInput($commentMailEnabled);

    $commentMailOwnerStatuses = new \Typecho\Widget\Helper\Form\Element\Checkbox(
        'commentMailOwnerStatuses',
        array(
            'none' => _t('不发送任何状态通知'),
            'approved' => _t('提醒已通过评论'),
            'waiting' => _t('提醒待审核评论'),
            'spam' => _t('提醒垃圾评论')
        ),
        array('approved', 'waiting'),
        _t('博主提醒设置'),
        _t('该选项仅针对博主，访客只发送已通过的评论。')
    );
    $form->addInput($commentMailOwnerStatuses);

    $commentMailNotifyOwner = new \Typecho\Widget\Helper\Form\Element\Radio(
        'commentMailNotifyOwner',
        array('off' => _t('关闭'), 'on' => _t('开启')),
        'on',
        _t('有评论及回复时通知博主'),
        _t('新评论、新回复或后台审核状态符合上方设置时，向文章作者邮箱发送邮件。')
    );
    $form->addInput($commentMailNotifyOwner);

    $commentMailNotifyReplied = new \Typecho\Widget\Helper\Form\Element\Radio(
        'commentMailNotifyReplied',
        array('off' => _t('关闭'), 'on' => _t('开启')),
        'on',
        _t('评论被回复时通知评论者'),
        _t('仅当新回复为已通过状态时通知被回复的评论者。')
    );
    $form->addInput($commentMailNotifyReplied);

    $commentMailNotifySelf = new \Typecho\Widget\Helper\Form\Element\Radio(
        'commentMailNotifySelf',
        array('off' => _t('关闭'), 'on' => _t('开启')),
        'off',
        _t('自己回复自己的评论时也通知'),
        _t('同时针对博主和访客。关闭后，同邮箱或同用户 ID 的自我回复不再发送通知。')
    );
    $form->addInput($commentMailNotifySelf);

    $commentMailLogEnabled = new \Typecho\Widget\Helper\Form\Element\Radio(
        'commentMailLogEnabled',
        array('off' => _t('关闭'), 'on' => _t('开启')),
        'off',
        _t('记录邮件发送日志'),
        _t('日志写入 usr/uploads/netsuko-mail.log。发送失败不会阻止评论提交。')
    );
    $form->addInput($commentMailLogEnabled);

    netsukoConfigSection($form, 'SMTP 发件设置', '配置 SMTP 服务器、认证与发件人信息。');

    $commentMailSmtpHost = new \Typecho\Widget\Helper\Form\Element\Text('commentMailSmtpHost', NULL, NULL, _t('SMTP 服务器'), _t('例如 smtp.example.com。'));
    $form->addInput($commentMailSmtpHost);

    $commentMailSmtpPort = new \Typecho\Widget\Helper\Form\Element\Text('commentMailSmtpPort', NULL, '465', _t('SMTP 端口'), _t('常见端口：465(SSL)、587(STARTTLS)、25(None/STARTTLS)。'));
    $form->addInput($commentMailSmtpPort);

    $commentMailSmtpSecure = new \Typecho\Widget\Helper\Form\Element\Radio(
        'commentMailSmtpSecure',
        array('ssl' => _t('SSL/TLS'), 'tls' => _t('STARTTLS'), 'none' => _t('无加密')),
        'ssl',
        _t('SMTP 加密方式'),
        _t('请按邮件服务商提供的配置选择。')
    );
    $form->addInput($commentMailSmtpSecure);

    $commentMailSmtpAuth = new \Typecho\Widget\Helper\Form\Element\Radio(
        'commentMailSmtpAuth',
        array('on' => _t('需要登录认证'), 'off' => _t('无需认证')),
        'on',
        _t('SMTP 认证'),
        _t('大多数邮件服务商需要开启认证。')
    );
    $form->addInput($commentMailSmtpAuth);

    $commentMailSmtpUser = new \Typecho\Widget\Helper\Form\Element\Text('commentMailSmtpUser', NULL, NULL, _t('SMTP 用户名'), _t('通常是完整邮箱地址。'));
    $form->addInput($commentMailSmtpUser);

    $commentMailSmtpPass = new \Typecho\Widget\Helper\Form\Element\Password('commentMailSmtpPass', NULL, NULL, _t('SMTP 密码/授权码'), _t('推荐使用邮箱服务商提供的应用授权码。'));
    $form->addInput($commentMailSmtpPass);

    $commentMailFromName = new \Typecho\Widget\Helper\Form\Element\Text('commentMailFromName', NULL, NULL, _t('发件人名称'), _t('留空时使用站点标题。'));
    $form->addInput($commentMailFromName);

    $commentMailFromEmail = new \Typecho\Widget\Helper\Form\Element\Text('commentMailFromEmail', NULL, NULL, _t('发件人邮箱'), _t('通常需要与 SMTP 用户名一致或同域。'));
    $form->addInput($commentMailFromEmail);

    $commentMailReplyTo = new \Typecho\Widget\Helper\Form\Element\Text('commentMailReplyTo', NULL, NULL, _t('回复到邮箱'), _t('留空时使用发件人邮箱。'));
    $form->addInput($commentMailReplyTo);

    $commentMailTimeout = new \Typecho\Widget\Helper\Form\Element\Text('commentMailTimeout', NULL, '10', _t('SMTP 超时秒数'), _t('建议 5-30 秒。'));
    $form->addInput($commentMailTimeout);

    $autoBackupStatus = netsukoRuntimeOption('netsukoAutoBackupLastStatus', '尚未执行');
    netsukoConfigSection(
        $form,
        '自动备份邮件',
        '按设置间隔生成 Typecho 兼容数据备份，并通过上方 SMTP 配置发送到指定邮箱。最近状态：' . $autoBackupStatus
    );

    $autoBackupEnabled = new \Typecho\Widget\Helper\Form\Element\Radio(
        'autoBackupEnabled',
        array('off' => _t('关闭'), 'on' => _t('开启')),
        'off',
        _t('自动备份'),
        _t('启用后由前台或后台访问触发检查，到达间隔才执行，不依赖服务器 Cron。')
    );
    $form->addInput($autoBackupEnabled);

    $autoBackupIntervalHours = new \Typecho\Widget\Helper\Form\Element\Text(
        'autoBackupIntervalHours',
        NULL,
        '24',
        _t('备份间隔（小时）'),
        _t('最小 1 小时。建议生产环境设置为 24、72 或 168。')
    );
    $form->addInput($autoBackupIntervalHours);

    $autoBackupRecipients = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'autoBackupRecipients',
        NULL,
        NULL,
        _t('备份收件邮箱'),
        _t('一行一个邮箱地址，或用英文逗号分隔。留空则不会发送。')
    );
    $form->addInput($autoBackupRecipients);

    $autoBackupSubject = new \Typecho\Widget\Helper\Form\Element\Text(
        'autoBackupSubject',
        NULL,
        '[{site}] Typecho 自动备份 {date}',
        _t('备份邮件标题'),
        _t('支持变量：{site}、{date}、{time}、{file}、{size}。')
    );
    $form->addInput($autoBackupSubject);

    $autoBackupTemplate = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'autoBackupTemplate',
        NULL,
        netsukoDefaultBackupMailTemplate(),
        _t('备份邮件正文模板'),
        _t('支持 HTML 与变量：{site}、{date}、{time}、{file}、{size}。')
    );
    $form->addInput($autoBackupTemplate);

    netsukoConfigSection($form, '邮件模板', '标题和正文支持变量：{site}、{title}、{author}、{mail}、{status}、{text}、{permalink}、{parent_author}、{parent_text}、{time}。');

    $commentMailOwnerSubject = new \Typecho\Widget\Helper\Form\Element\Text(
        'commentMailOwnerSubject',
        NULL,
        '[{title}] 一文有新的评论',
        _t('博主接收邮件标题'),
        _t('用于发送给文章作者。')
    );
    $form->addInput($commentMailOwnerSubject);

    $commentMailVisitorSubject = new \Typecho\Widget\Helper\Form\Element\Text(
        'commentMailVisitorSubject',
        NULL,
        '您在悦绮录 [{title}] 的评论有新的回复',
        _t('访客接收邮件标题'),
        _t('用于发送给被回复的评论者。')
    );
    $form->addInput($commentMailVisitorSubject);

    $commentMailOwnerTemplate = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'commentMailOwnerTemplate',
        NULL,
        netsukoDefaultOwnerMailTemplate(),
        _t('博主接收邮件正文模板'),
        _t('支持 HTML；变量会自动转义并替换。')
    );
    $form->addInput($commentMailOwnerTemplate);

    $commentMailVisitorTemplate = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'commentMailVisitorTemplate',
        NULL,
        netsukoDefaultVisitorMailTemplate(),
        _t('访客接收邮件正文模板'),
        _t('支持 HTML；变量会自动转义并替换。')
    );
    $form->addInput($commentMailVisitorTemplate);
}

function netsukoConfigAdminAssets($form): void {
    $style = new \Typecho\Widget\Helper\Layout('style');
    $style->html(<<<'CSS'
.netsuko-config-section,
.netsuko-version-card,
.netsuko-backup-card {
    margin: 24px 0 14px;
    padding: 18px 20px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #f8fafc;
}
.netsuko-config-section h3,
.netsuko-version-card h3,
.netsuko-backup-card h3 {
    margin: 0 0 6px;
    font-size: 16px;
    line-height: 1.4;
    color: #111827;
}
.netsuko-config-section p,
.netsuko-version-card p,
.netsuko-backup-card p {
    margin: 0;
    color: #6b7280;
    line-height: 1.7;
}
.netsuko-version-card {
    background: #f0fdfa;
    border-color: rgba(20, 184, 166, 0.28);
}
.netsuko-backup-card {
    background: #fff7ed;
    border-color: rgba(249, 115, 22, 0.22);
}
.netsuko-version-row,
.netsuko-backup-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
    margin-top: 12px;
}
.netsuko-version-pill,
.netsuko-backup-pill {
    display: inline-flex;
    align-items: center;
    min-height: 28px;
    padding: 0 10px;
    border-radius: 999px;
    background: #ffffff;
    border: 1px solid rgba(20, 184, 166, 0.22);
    color: #0f766e;
    font-size: 13px;
}
.netsuko-backup-pill {
    border-color: rgba(249, 115, 22, 0.22);
    color: #c2410c;
}
.netsuko-version-status,
.netsuko-backup-status {
    margin-top: 12px;
    color: #374151;
}
.netsuko-backup-check {
    display: inline-flex;
    gap: 6px;
    align-items: center;
    color: #4b5563;
}
.netsuko-backup-file {
    display: none;
}
.netsuko-version-links {
    display: none;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 12px;
}
.netsuko-version-links a {
    color: #0f766e;
    text-decoration: none;
}
.netsuko-version-links a:hover {
    text-decoration: underline;
}
CSS);
    $form->addItem($style);
}

function netsukoConfigSection($form, string $title, string $description): void {
    $section = new \Typecho\Widget\Helper\Layout('div', ['class' => 'netsuko-config-section']);
    $section->html(
        '<h3>' . netsukoEscape($title) . '</h3>' .
        '<p>' . netsukoEscape($description) . '</p>'
    );
    $form->addItem($section);
}

function netsukoThemeVersion(): string {
    $indexFile = __DIR__ . '/index.php';
    if (is_file($indexFile) && preg_match('/@version\s+([^\s]+)/', (string) file_get_contents($indexFile), $matches)) {
        return trim($matches[1]);
    }

    return '0.0.0';
}

function netsukoVersionedAssetUrl(string $url): string {
    if (preg_match('/[?&]v=/i', $url)) {
        return $url;
    }

    $version = rawurlencode(netsukoThemeVersion());
    $fragment = '';
    $fragmentPosition = strpos($url, '#');

    if ($fragmentPosition !== false) {
        $fragment = substr($url, $fragmentPosition);
        $url = substr($url, 0, $fragmentPosition);
    }

    $separator = strpos($url, '?') === false ? '?' : '&';

    return $url . $separator . 'v=' . $version . $fragment;
}

function netsukoConfigBackupTools($form): void {
    $version = netsukoThemeVersion();
    $fields = [
        'authorName',
        'authorAvatar',
        'postFont',
        'defaultThumb',
        'colorScheme',
        'tailwindAssetSource',
        'tailwindCustomUrl',
        'fancyboxAssetSource',
        'fancyboxCustomCssUrl',
        'fancyboxCustomJsUrl',
        'lazyLoadEnabled',
        'codeHighlightEnabled',
        'latexDefaultEnabled',
        'pjaxEnabled',
        'pjaxExcludePaths',
        'bangumiProfile',
        'bangumiItemsPerStatus',
        'bangumiCacheHours',
        'mottoBanner',
        'motto',
        'mottoFont',
        'mottoQuotes',
        'mottoColorLight',
        'mottoColorDark',
        'indexBanner',
        'indexBannerDark',
        'indexBannerHeight',
        'bannerOpacity',
        'githubUrl',
        'socialTwitter',
        'socialTelegram',
        'socialEmail',
        'socialDiscord',
        'socialInstagram',
        'socialBilibili',
        'socialWechat',
        'sidebarLinks',
        'icpNum',
        'icpUrl',
        'rssFeed',
        'siteStatusUrl',
        'customHeadCode',
        'commentCaptchaMode',
        'turnstileSiteKey',
        'turnstileSecretKey',
        'commentPaginationEnabled',
        'commentPaginationSize',
        'commentPaginationDisplay',
        'commentMailEnabled',
        'commentMailOwnerStatuses',
        'commentMailNotifyOwner',
        'commentMailNotifyReplied',
        'commentMailNotifySelf',
        'commentMailLogEnabled',
        'commentMailSmtpHost',
        'commentMailSmtpPort',
        'commentMailSmtpSecure',
        'commentMailSmtpAuth',
        'commentMailSmtpUser',
        'commentMailSmtpPass',
        'commentMailFromName',
        'commentMailFromEmail',
        'commentMailReplyTo',
        'commentMailTimeout',
        'autoBackupEnabled',
        'autoBackupIntervalHours',
        'autoBackupRecipients',
        'autoBackupSubject',
        'autoBackupTemplate',
        'commentMailOwnerSubject',
        'commentMailVisitorSubject',
        'commentMailOwnerTemplate',
        'commentMailVisitorTemplate'
    ];
    $fieldsJson = json_encode($fields, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    $card = new \Typecho\Widget\Helper\Layout('div', [
        'class' => 'netsuko-backup-card',
        'data-theme-version' => $version,
        'data-fields' => netsukoEscape($fieldsJson)
    ]);
    $card->html(
        '<h3>配置备份</h3>' .
        '<p>导出为 JSON，导入后需要点击页面底部保存设置。</p>' .
        '<div class="netsuko-backup-row">' .
            '<span class="netsuko-backup-pill">仅处理当前设置表单</span>' .
            '<button type="button" class="btn" id="netsuko-export-config">导出当前配置</button>' .
            '<button type="button" class="btn" id="netsuko-import-config">导入配置 JSON</button>' .
            '<label class="netsuko-backup-check"><input type="checkbox" id="netsuko-include-secrets"> 包含敏感密钥</label>' .
            '<input type="file" class="netsuko-backup-file" id="netsuko-config-file" accept="application/json,.json">' .
        '</div>' .
        '<div class="netsuko-backup-status" id="netsuko-backup-status">默认不会导出 Turnstile Secret Key。</div>' .
        '<script>
(function () {
    var card = document.querySelector(".netsuko-backup-card");
    var exportButton = document.getElementById("netsuko-export-config");
    var importButton = document.getElementById("netsuko-import-config");
    var includeSecrets = document.getElementById("netsuko-include-secrets");
    var fileInput = document.getElementById("netsuko-config-file");
    var status = document.getElementById("netsuko-backup-status");
    var form = card ? card.closest("form") : null;
    if (!card || !form || !exportButton || !importButton || !fileInput || !status) {
        return;
    }

    var fields = [];
    try {
        fields = JSON.parse(card.getAttribute("data-fields") || "[]");
    } catch (error) {
        status.textContent = "配置字段读取失败。";
        return;
    }

    function fieldSelector(name) {
        return "[name=\"" + name + "\"]";
    }

    function readField(name) {
        var controls = Array.prototype.slice.call(form.querySelectorAll(fieldSelector(name)));
        if (!controls.length) {
            return null;
        }

        if (controls[0].type === "radio") {
            var checked = controls.find(function (control) { return control.checked; });
            return checked ? checked.value : "";
        }

        if (controls[0].type === "checkbox" && controls.length > 1) {
            return controls.filter(function (control) { return control.checked; }).map(function (control) { return control.value; });
        }

        if (controls[0].type === "checkbox") {
            return controls[0].checked;
        }

        return controls[0].value || "";
    }

    function writeField(name, value) {
        var controls = Array.prototype.slice.call(form.querySelectorAll(fieldSelector(name)));
        if (!controls.length) {
            return false;
        }

        if (controls[0].type === "radio") {
            controls.forEach(function (control) {
                control.checked = String(control.value) === String(value);
                control.dispatchEvent(new Event("change", { bubbles: true }));
            });
            return true;
        }

        if (controls[0].type === "checkbox" && controls.length > 1) {
            var values = Array.isArray(value) ? value.map(String) : [String(value)];
            controls.forEach(function (control) {
                control.checked = values.indexOf(String(control.value)) !== -1;
                control.dispatchEvent(new Event("change", { bubbles: true }));
            });
            return true;
        }

        if (controls[0].type === "checkbox") {
            controls[0].checked = Boolean(value);
            controls[0].dispatchEvent(new Event("change", { bubbles: true }));
            return true;
        }

        controls[0].value = value == null ? "" : String(value);
        controls[0].dispatchEvent(new Event("input", { bubbles: true }));
        controls[0].dispatchEvent(new Event("change", { bubbles: true }));
        return true;
    }

    function downloadJson(data) {
        var blob = new Blob([JSON.stringify(data, null, 2)], { type: "application/json" });
        var url = URL.createObjectURL(blob);
        var link = document.createElement("a");
        var date = new Date().toISOString().slice(0, 10);
        link.href = url;
        link.download = "netsuko-config-" + date + ".json";
        document.body.appendChild(link);
        link.click();
        link.remove();
        URL.revokeObjectURL(url);
    }

    exportButton.addEventListener("click", function () {
        var settings = {};
        fields.forEach(function (name) {
            if ((name === "turnstileSecretKey" || name === "commentMailSmtpPass") && (!includeSecrets || !includeSecrets.checked)) {
                return;
            }
            settings[name] = readField(name);
        });

        downloadJson({
            theme: "netsuko",
            version: card.getAttribute("data-theme-version") || "",
            exportedAt: new Date().toISOString(),
            settings: settings
        });

        status.textContent = includeSecrets && includeSecrets.checked
            ? "已导出配置，请妥善保存包含密钥的文件。"
            : "已导出配置，敏感密钥未包含在文件中。";
    });

    importButton.addEventListener("click", function () {
        fileInput.value = "";
        fileInput.click();
    });

    fileInput.addEventListener("change", function () {
        var file = fileInput.files && fileInput.files[0];
        if (!file) {
            return;
        }

        file.text()
            .then(function (text) {
                var parsed = JSON.parse(text);
                var settings = parsed && parsed.settings ? parsed.settings : parsed;
                if (!settings || typeof settings !== "object") {
                    throw new Error("文件内容不是可识别的配置 JSON");
                }

                var changed = 0;
                fields.forEach(function (name) {
                    if (Object.prototype.hasOwnProperty.call(settings, name) && writeField(name, settings[name])) {
                        changed++;
                    }
                });

                status.textContent = "已导入 " + changed + " 项到表单，请检查后点击保存设置。";
            })
            .catch(function (error) {
                status.textContent = "导入失败：" + error.message;
            });
    });
})();
</script>'
    );
    $form->addItem($card);
}

function netsukoConfigVersionChecker($form): void {
    $version = netsukoThemeVersion();
    $releaseUrl = 'https://github.com/ScDuckXu/netsuko_typecho_theme/releases/latest';

    $card = new \Typecho\Widget\Helper\Layout('div', [
        'class' => 'netsuko-version-card',
        'data-current-version' => $version
    ]);
    $card->html(
        '<h3>Netsuko 版本更新检查</h3>' .
        '<p>此检查不会修改服务器上的任何文件，仅检查更新，需要手动下载覆盖。</p>' .
        '<div class="netsuko-version-row">' .
            '<span class="netsuko-version-pill">当前版本：' . netsukoEscape($version) . '</span>' .
            '<button type="button" class="btn" id="netsuko-check-update">检查最新版本</button>' .
        '</div>' .
        '<div class="netsuko-version-status" id="netsuko-version-status">点击按钮检查 GitHub 最新 Release。</div>' .
        '<div class="netsuko-version-links" id="netsuko-version-links">' .
            '<a href="' . netsukoEscape($releaseUrl) . '" target="_blank" rel="noopener noreferrer" id="netsuko-release-link">打开 Release</a>' .
            '<a href="#" target="_blank" rel="noopener noreferrer" id="netsuko-download-link">下载更新包</a>' .
        '</div>' .
        '<script>
(function () {
    var button = document.getElementById("netsuko-check-update");
    var status = document.getElementById("netsuko-version-status");
    var links = document.getElementById("netsuko-version-links");
    var releaseLink = document.getElementById("netsuko-release-link");
    var downloadLink = document.getElementById("netsuko-download-link");
    var card = button ? button.closest(".netsuko-version-card") : null;
    if (!button || !status || !card) {
        return;
    }

    var current = card.getAttribute("data-current-version") || "0.0.0";
    var endpoint = "https://api.github.com/repos/ScDuckXu/netsuko_typecho_theme/releases/latest";

    function normalize(version) {
        return String(version || "").replace(/^v/i, "").split(".").map(function (part) {
            var number = parseInt(part, 10);
            return isNaN(number) ? 0 : number;
        });
    }

    function compare(a, b) {
        var left = normalize(a);
        var right = normalize(b);
        var length = Math.max(left.length, right.length);
        for (var i = 0; i < length; i++) {
            var av = left[i] || 0;
            var bv = right[i] || 0;
            if (av > bv) return 1;
            if (av < bv) return -1;
        }
        return 0;
    }

    button.addEventListener("click", function () {
        button.disabled = true;
        status.textContent = "正在查询 GitHub Releases...";
        if (links) {
            links.style.display = "none";
        }

        fetch(endpoint, { headers: { "Accept": "application/vnd.github+json" } })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error("GitHub 返回 " + response.status);
                }
                return response.json();
            })
            .then(function (release) {
                var latest = release.tag_name || "";
                var asset = (release.assets || []).find(function (item) {
                    return item.name === "netsuko.zip";
                });
                var downloadUrl = asset ? asset.browser_download_url : release.html_url;
                var result = compare(current, latest);

                if (releaseLink) {
                    releaseLink.href = release.html_url || "https://github.com/ScDuckXu/netsuko_typecho_theme/releases/latest";
                }
                if (downloadLink) {
                    downloadLink.href = downloadUrl;
                    downloadLink.textContent = asset ? "下载更新包" : "打开 Release 下载";
                }
                if (links) {
                    links.style.display = "flex";
                }

                if (result < 0) {
                    status.textContent = "发现新版本 " + latest + "。建议先备份主题配置，再前往 Release 手动更新。";
                } else if (result === 0) {
                    status.textContent = "当前已经是最新版本：" + latest + "。";
                } else {
                    status.textContent = "当前版本 " + current + " 高于 GitHub 最新 Release " + latest + "。";
                }
            })
            .catch(function (error) {
                status.textContent = "检查失败：" + error.message + "。你仍可手动打开 GitHub Releases 查看。";
                if (links) {
                    links.style.display = "flex";
                }
            })
            .finally(function () {
                button.disabled = false;
            });
    });
})();
</script>'
    );
    $form->addItem($card);
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

    $stickyPost = new \Typecho\Widget\Helper\Form\Element\Radio(
        'stickyPost',
        array('off' => _t('不置顶'), 'on' => _t('首页置顶')),
        'off',
        _t('首页置顶'),
        _t('开启后会在首页第一页顶部显示红标【置顶】，多篇置顶文章按发布时间从新到旧排序。')
    );
    $layout->addItem($stickyPost);

    $subtitle = new \Typecho\Widget\Helper\Form\Element\Text(
        'subtitle', 
        NULL, 
        NULL, 
        _t('页面副标题'), 
        _t('显示在页面大标题下方的说明文字。留空则在部分模板下显示默认文案。')
    );
    $layout->addItem($subtitle);

    $enableLatex = new \Typecho\Widget\Helper\Form\Element\Radio(
        'enableLatex',
        array(
            'default' => _t('跟随主题默认'),
            'on' => _t('启用 LaTeX'),
            'off' => _t('禁用 LaTeX')
        ),
        'default',
        _t('LaTeX 解析'),
        _t('仅影响当前文章或独立页。启用后支持 $...$、$$...$$、\\(...\\) 与 \\[...\\]。')
    );
    $layout->addItem($enableLatex);
}

function netsukoEscape($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function netsukoOption(string $name, $default = '') {
    $options = \Typecho\Widget::widget('Widget_Options');
    $value = $options->{$name};
    return $value === null || $value === '' ? $default : $value;
}

function netsukoThemePalette(): string {
    $palette = (string) netsukoOption('colorScheme', 'miku');
    return in_array($palette, ['miku', 'sakura', 'amber', 'iris', 'forest'], true) ? $palette : 'miku';
}

function netsukoUrl($value, string $fallback = '#') {
    $url = trim((string) $value);
    if ($url === '') {
        return netsukoEscape($fallback);
    }

    if (preg_match('/^[a-z][a-z0-9+.-]*:/i', $url)) {
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        if (!in_array($scheme, ['http', 'https', 'mailto'], true)) {
            return netsukoEscape($fallback);
        }
    }

    return netsukoEscape($url);
}

function netsukoBangumiUsername($value): string {
    $value = trim(strip_tags((string) $value));
    if ($value === '') {
        return '';
    }

    if (preg_match('~(?:https?://)?(?:www\.)?(?:bgm\.tv|bangumi\.tv|chii\.in)/user/([^/?#\s<]+)~iu', $value, $matches)) {
        $value = rawurldecode($matches[1]);
    } else {
        $value = preg_replace('/\s+/u', '', $value);
    }

    return preg_match('/^[\p{L}\p{N}_.-]{1,64}$/u', $value) ? $value : '';
}

function netsukoBangumiStates(): array {
    return [
        3 => ['key' => 'doing', 'label' => '在看'],
        2 => ['key' => 'collect', 'label' => '看过'],
        1 => ['key' => 'wish', 'label' => '想看'],
        4 => ['key' => 'on_hold', 'label' => '搁置'],
        5 => ['key' => 'dropped', 'label' => '抛弃']
    ];
}

function netsukoBangumiPageData($archive): array {
    $configured = trim((string) netsukoOption('bangumiProfile', ''));
    $source = $configured !== '' ? $configured : (string) ($archive->text ?? '');
    $username = netsukoBangumiUsername($source);
    if ($username === '') {
        return [
            'username' => '',
            'profile' => [],
            'groups' => [],
            'fetchedAt' => 0,
            'stale' => false,
            'error' => 'Bangumi 个人主页或用户名尚未配置'
        ];
    }

    $limit = max(1, min(50, (int) netsukoOption('bangumiItemsPerStatus', '24')));
    $cacheHours = max(1, min(168, (int) netsukoOption('bangumiCacheHours', '6')));
    $cachePath = netsukoUploadPath('netsuko-bangumi-' . substr(hash('sha256', strtolower($username) . ':' . $limit), 0, 20) . '.json');
    $cached = netsukoReadBangumiCache($cachePath, $username);
    $cacheFresh = $cached && (int) ($cached['fetchedAt'] ?? 0) >= time() - ($cacheHours * 3600);

    if ($cacheFresh) {
        $cached['stale'] = false;
        $cached['error'] = '';
        return $cached;
    }

    try {
        $payload = netsukoFetchBangumiPayload($username, $limit);
        netsukoWriteBangumiCache($cachePath, $payload);
        $payload['stale'] = false;
        $payload['error'] = '';
        return $payload;
    } catch (\Throwable $e) {
        if ($cached) {
            $cached['stale'] = true;
            $cached['error'] = 'Bangumi 暂时无法更新，正在显示上一次成功缓存';
            return $cached;
        }

        return [
            'username' => $username,
            'profile' => [],
            'groups' => [],
            'fetchedAt' => 0,
            'stale' => false,
            'error' => 'Bangumi 数据加载失败：' . $e->getMessage()
        ];
    }
}

function netsukoReadBangumiCache(string $path, string $username): ?array {
    if (!@is_file($path) || !@is_readable($path)) {
        return null;
    }

    $contents = @file_get_contents($path);
    if (!is_string($contents)) {
        return null;
    }

    $decoded = json_decode($contents, true);
    if (!is_array($decoded) || (string) ($decoded['username'] ?? '') !== $username || !isset($decoded['groups']) || !is_array($decoded['groups'])) {
        return null;
    }

    return $decoded;
}

function netsukoWriteBangumiCache(string $path, array $payload): bool {
    $encoded = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($encoded === false) {
        return false;
    }

    return @file_put_contents($path, $encoded, LOCK_EX) !== false;
}

function netsukoFetchBangumiPayload(string $username, int $limit): array {
    $encodedUser = rawurlencode($username);
    $urls = [
        'profile' => 'https://api.bgm.tv/v0/users/' . $encodedUser
    ];

    foreach (netsukoBangumiStates() as $type => $state) {
        $urls[$state['key']] = 'https://api.bgm.tv/v0/users/' . $encodedUser . '/collections?subject_type=2&type=' . $type . '&limit=' . $limit . '&offset=0';
    }

    $responses = netsukoHttpGetJsonBatch($urls, 8);
    foreach ($urls as $key => $_) {
        if (empty($responses[$key]['ok'])) {
            throw new \RuntimeException((string) ($responses[$key]['error'] ?? '接口请求失败'));
        }
    }

    $profileData = $responses['profile']['data'];
    $profile = [
        'username' => (string) ($profileData['username'] ?? $username),
        'nickname' => trim((string) ($profileData['nickname'] ?? '')) ?: $username,
        'avatar' => netsukoBangumiHttpsUrl($profileData['avatar']['large'] ?? ($profileData['avatar']['medium'] ?? '')),
        'sign' => trim((string) ($profileData['sign'] ?? '')),
        'url' => 'https://bgm.tv/user/' . rawurlencode($username)
    ];

    $groups = [];
    foreach (netsukoBangumiStates() as $state) {
        $response = $responses[$state['key']]['data'];
        $items = [];
        foreach (is_array($response['data'] ?? null) ? $response['data'] : [] as $item) {
            $normalized = netsukoNormalizeBangumiItem($item);
            if ($normalized) {
                $items[] = $normalized;
            }
        }
        $groups[] = [
            'key' => $state['key'],
            'label' => $state['label'],
            'total' => max(count($items), (int) ($response['total'] ?? 0)),
            'items' => $items
        ];
    }

    return [
        'username' => $username,
        'profile' => $profile,
        'groups' => $groups,
        'fetchedAt' => time()
    ];
}

function netsukoNormalizeBangumiItem($item): ?array {
    if (!is_array($item) || !empty($item['private']) || !is_array($item['subject'] ?? null)) {
        return null;
    }

    $subject = $item['subject'];
    $id = (int) ($subject['id'] ?? ($item['subject_id'] ?? 0));
    if ($id <= 0) {
        return null;
    }

    $title = trim((string) ($subject['name_cn'] ?? '')) ?: trim((string) ($subject['name'] ?? ''));
    if ($title === '') {
        return null;
    }

    $originalTitle = trim((string) ($subject['name'] ?? ''));
    $watched = max(0, (int) ($item['ep_status'] ?? 0));
    $total = max(0, (int) ($subject['total_episodes'] ?? ($subject['eps'] ?? 0)));
    $progress = $total > 0 ? min(100, (int) round(($watched / $total) * 100)) : 0;
    $images = is_array($subject['images'] ?? null) ? $subject['images'] : [];
    $rating = is_array($subject['rating'] ?? null) ? $subject['rating'] : [];
    $rawScore = is_numeric($subject['score'] ?? null) ? $subject['score'] : ($rating['score'] ?? 0);
    $rawRank = is_numeric($subject['rank'] ?? null) ? $subject['rank'] : ($rating['rank'] ?? 0);
    $summary = trim((string) ($subject['short_summary'] ?? ''));
    if ($summary === '') {
        $summary = trim((string) ($subject['summary'] ?? ''));
    }
    $tags = [];
    foreach (is_array($item['tags'] ?? null) ? $item['tags'] : [] as $tag) {
        $tag = trim((string) $tag);
        if ($tag !== '') {
            $tags[] = $tag;
        }
        if (count($tags) >= 3) {
            break;
        }
    }

    return [
        'id' => $id,
        'url' => 'https://bgm.tv/subject/' . $id,
        'title' => $title,
        'originalTitle' => $originalTitle !== $title ? $originalTitle : '',
        'image' => netsukoBangumiHttpsUrl($images['large'] ?? ($images['common'] ?? ($images['medium'] ?? ''))),
        'summary' => $summary,
        'date' => trim((string) ($subject['date'] ?? '')),
        'score' => max(0, min(10, (float) $rawScore)),
        'rank' => max(0, (int) $rawRank),
        'watched' => $watched,
        'totalEpisodes' => $total,
        'progress' => $progress,
        'rate' => max(0, min(10, (int) ($item['rate'] ?? 0))),
        'comment' => trim((string) ($item['comment'] ?? '')),
        'tags' => $tags
    ];
}

function netsukoBangumiHttpsUrl($value): string {
    $url = trim((string) $value);
    if (strpos($url, '//') === 0) {
        $url = 'https:' . $url;
    }
    if (stripos($url, 'http://') === 0) {
        $url = 'https://' . substr($url, 7);
    }
    return filter_var($url, FILTER_VALIDATE_URL) && strtolower((string) parse_url($url, PHP_URL_SCHEME)) === 'https' ? $url : '';
}

function netsukoHttpGetJsonBatch(array $urls, int $timeout = 8): array {
    $timeout = max(2, min(20, $timeout));
    if (!function_exists('curl_multi_init')) {
        $results = [];
        foreach ($urls as $key => $url) {
            $results[$key] = netsukoHttpGetJson((string) $url, $timeout);
        }
        return $results;
    }

    $multi = curl_multi_init();
    $handles = [];
    foreach ($urls as $key => $url) {
        $handle = curl_init((string) $url);
        curl_setopt_array($handle, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => min(4, $timeout),
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
            CURLOPT_USERAGENT => 'Netsuko-Typecho/' . netsukoThemeVersion() . ' (+https://github.com/ScDuckXu/netsuko_typecho_theme)'
        ]);
        curl_multi_add_handle($multi, $handle);
        $handles[$key] = $handle;
    }

    do {
        $status = curl_multi_exec($multi, $active);
    } while ($status === CURLM_CALL_MULTI_PERFORM);

    while ($active && $status === CURLM_OK) {
        $selected = curl_multi_select($multi, 1.0);
        if ($selected === -1) {
            usleep(10000);
        }

        do {
            $status = curl_multi_exec($multi, $active);
        } while ($status === CURLM_CALL_MULTI_PERFORM);
    }

    $results = [];
    foreach ($handles as $key => $handle) {
        $body = (string) curl_multi_getcontent($handle);
        $code = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        $error = curl_error($handle);
        $decoded = $body !== '' ? json_decode($body, true) : null;
        $ok = $error === '' && $code >= 200 && $code < 300 && is_array($decoded);
        $results[$key] = [
            'ok' => $ok,
            'data' => $ok ? $decoded : [],
            'error' => $error !== '' ? $error : ($code > 0 ? 'Bangumi 返回 HTTP ' . $code : 'Bangumi 未返回有效数据')
        ];
        curl_multi_remove_handle($multi, $handle);
        curl_close($handle);
    }
    curl_multi_close($multi);

    return $results;
}

function netsukoHttpGetJson(string $url, int $timeout): array {
    if (!filter_var($url, FILTER_VALIDATE_URL) || strtolower((string) parse_url($url, PHP_URL_SCHEME)) !== 'https') {
        return ['ok' => false, 'data' => [], 'error' => '无效的 HTTPS 接口地址'];
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => $timeout,
            'ignore_errors' => true,
            'header' => "Accept: application/json\r\nUser-Agent: Netsuko-Typecho/" . netsukoThemeVersion() . " (+https://github.com/ScDuckXu/netsuko_typecho_theme)\r\n"
        ]
    ]);
    $body = @file_get_contents($url, false, $context);
    $code = 0;
    foreach ($http_response_header ?? [] as $header) {
        if (preg_match('#^HTTP/\S+\s+(\d{3})#i', $header, $matches)) {
            $code = (int) $matches[1];
        }
    }
    $decoded = is_string($body) ? json_decode($body, true) : null;
    $ok = $code >= 200 && $code < 300 && is_array($decoded);
    return [
        'ok' => $ok,
        'data' => $ok ? $decoded : [],
        'error' => $code > 0 ? 'Bangumi 返回 HTTP ' . $code : 'Bangumi 请求失败'
    ];
}

function netsukoDevicesPayload($archive): array {
    $source = trim((string) ($archive->text ?? ''));
    if ($source === '') {
        return [
            'groups' => [],
            'error' => '页面正文为空'
        ];
    }

    $parsed = json_decode($source, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsed)) {
        return [
            'groups' => [],
            'error' => json_last_error_msg()
        ];
    }

    return [
        'groups' => netsukoNormalizeDeviceGroups($parsed),
        'error' => ''
    ];
}

function netsukoNormalizeDeviceGroups(array $data): array {
    if (isset($data['groups']) && is_array($data['groups'])) {
        $groups = $data['groups'];
    } elseif (isset($data['devices']) && is_array($data['devices'])) {
        $groups = [[
            'name' => $data['title'] ?? 'Devices',
            'desc' => $data['desc'] ?? ($data['description'] ?? ''),
            'items' => $data['devices']
        ]];
    } elseif (netsukoArrayIsList($data) && isset($data[0]) && is_array($data[0]) && (isset($data[0]['items']) || isset($data[0]['devices']))) {
        $groups = $data;
    } else {
        $groups = [[
            'name' => 'Devices',
            'desc' => '',
            'items' => $data
        ]];
    }

    $normalized = [];
    foreach ($groups as $group) {
        if (!is_array($group)) {
            continue;
        }

        $items = $group['items'] ?? ($group['devices'] ?? []);
        if (!is_array($items)) {
            continue;
        }

        $devices = [];
        foreach ($items as $item) {
            if (is_array($item) && trim((string) ($item['name'] ?? '')) !== '') {
                $devices[] = $item;
            }
        }

        if (empty($devices)) {
            continue;
        }

        $normalized[] = [
            'name' => (string) ($group['name'] ?? ($group['group'] ?? ($group['title'] ?? 'Devices'))),
            'desc' => (string) ($group['desc'] ?? ($group['description'] ?? '')),
            'items' => $devices
        ];
    }

    return $normalized;
}

function netsukoArrayIsList(array $value): bool {
    $index = 0;
    foreach ($value as $key => $_) {
        if ($key !== $index++) {
            return false;
        }
    }
    return true;
}

function netsukoDeviceTags($tags): array {
    if (is_string($tags)) {
        $tags = preg_split('/[,，|]+/u', $tags, -1, PREG_SPLIT_NO_EMPTY);
    }

    if (!is_array($tags)) {
        return [];
    }

    return array_values(array_filter(array_map(static function ($tag) {
        return trim((string) $tag);
    }, $tags), static function ($tag) {
        return $tag !== '';
    }));
}

function netsukoDeviceSpecs($specs): array {
    if (!is_array($specs)) {
        return [];
    }

    $result = [];
    foreach ($specs as $key => $value) {
        if (is_array($value)) {
            $label = trim((string) ($value['label'] ?? ($value['name'] ?? $key)));
            $text = trim((string) ($value['value'] ?? ($value['text'] ?? '')));
        } else {
            $label = trim((string) $key);
            $text = trim((string) $value);
        }

        if ($label !== '' && $text !== '') {
            $result[] = ['label' => $label, 'value' => $text];
        }
    }

    return $result;
}

function netsukoStickyPosts(int $limit = 20): array {
    $db = \Typecho\Db::get();
    $options = \Typecho\Widget::widget('Widget_Options');
    $rows = $db->fetchAll(
        $db->select('table.contents.*')
            ->from('table.contents')
            ->join('table.fields', 'table.contents.cid = table.fields.cid')
            ->where('table.contents.type = ?', 'post')
            ->where('table.contents.status = ?', 'publish')
            ->where('table.contents.created < ?', $options->time)
            ->where('table.fields.name = ?', 'stickyPost')
            ->where('table.fields.str_value = ?', 'on')
            ->order('table.contents.created', \Typecho\Db::SORT_DESC)
            ->limit($limit)
    );

    foreach ($rows as &$row) {
        $row['fields'] = netsukoContentFields((int) $row['cid']);
    }
    unset($row);

    return $rows;
}

function netsukoContentFields(int $cid): array {
    if ($cid <= 0) {
        return [];
    }

    $db = \Typecho\Db::get();
    $rows = $db->fetchAll($db->select()->from('table.fields')->where('cid = ?', $cid));
    $fields = [];

    foreach ($rows as $row) {
        $type = (string) ($row['type'] ?? 'str');
        $fields[$row['name']] = $type === 'json'
            ? json_decode((string) $row['str_value'], true)
            : ($row[$type . '_value'] ?? null);
    }

    return $fields;
}

function netsukoContentIsProtected(array $content): bool {
    $password = (string) ($content['password'] ?? '');
    if ($password === '') {
        return false;
    }

    $cid = (int) ($content['cid'] ?? 0);
    if ($cid > 0 && $password === (string) \Typecho\Cookie::get('protectPassword_' . $cid)) {
        return false;
    }

    $user = \Widget\User::alloc();
    if ((int) ($content['authorId'] ?? 0) === (int) $user->uid || $user->pass('editor', true)) {
        return false;
    }

    return true;
}

function netsukoContentTitle(array $content): string {
    return netsukoContentIsProtected($content)
        ? (string) _t('此内容被密码保护')
        : (string) ($content['title'] ?? '');
}

function netsukoContentThumb(array $content): string {
    $fields = $content['fields'] ?? [];
    if (!netsukoContentIsProtected($content) && !empty($fields['thumb'])) {
        return (string) $fields['thumb'];
    }

    if (!netsukoContentIsProtected($content) && preg_match_all('/<img\b[^>]*?\bsrc=[\'"]([^\'"]+)[\'"][^>]*>/i', (string) ($content['text'] ?? ''), $matches) && isset($matches[1][0])) {
        return $matches[1][0];
    }

    $defaultThumb = \Typecho\Widget::widget('Widget_Options')->defaultThumb;
    if (!empty($defaultThumb)) {
        return (string) $defaultThumb;
    }

    return Helper::options()->themeUrl . '/img/bg_watermark.jpg';
}

function netsukoContentExcerpt(array $content, int $length = 90): string {
    if (netsukoContentIsProtected($content)) {
        return (string) _t('请输入密码访问');
    }

    $fields = $content['fields'] ?? [];
    if (!empty($fields['custom_excerpt'])) {
        return (string) $fields['custom_excerpt'];
    }

    $text = strip_tags((string) ($content['text'] ?? ''));
    $text = preg_replace('/\s+/u', ' ', $text);
    $text = trim((string) $text);
    if (function_exists('mb_substr')) {
        return mb_strlen($text, 'UTF-8') > $length ? mb_substr($text, 0, $length, 'UTF-8') . '...' : $text;
    }

    return strlen($text) > $length ? substr($text, 0, $length) . '...' : $text;
}

function netsukoRenderPostCardFromArray(array $post, bool $sticky = false): void {
    $permalink = netsukoContentPermalink($post);
    $title = netsukoEscape(netsukoContentTitle($post));
    $thumb = netsukoCssUrl(netsukoContentThumb($post));
    $created = (int) ($post['created'] ?? time());
    $excerpt = netsukoEscape(netsukoContentExcerpt($post, 90));
    ?>
    <article class="bg-white dark:bg-darkCard rounded-2xl border border-gray-200/50 dark:border-white/5 shadow-sm overflow-hidden transition-all duration-500 hover:scale-[1.02] hover:border-teal/50 hover:shadow-glow flex flex-col sm:flex-row group" itemscope itemtype="http://schema.org/BlogPosting">
        <div class="w-full sm:w-1/3 h-48 sm:h-auto bg-cover bg-center transition-transform duration-500 group-hover:scale-105" style="background-image: url('<?php echo $thumb; ?>');"></div>

        <div class="p-6 md:p-8 sm:w-2/3 flex flex-col justify-center relative z-10 bg-white dark:bg-darkCard">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 group-hover:text-teal transition-colors mb-2 flex flex-wrap items-center gap-2">
                <?php if ($sticky): ?><span class="netsuko-sticky-badge">置顶</span><?php endif; ?>
                <a itemprop="url" href="<?php echo netsukoUrl($permalink); ?>"><?php echo $title; ?></a>
            </h2>
            <div class="text-sm text-gray-500 dark:text-gray-400 mb-4 flex items-center gap-4">
                <time datetime="<?php echo date('c', $created); ?>"><?php echo date('Y-m-d', $created); ?></time>
            </div>
            <div class="post-content text-gray-600 dark:text-gray-300 leading-relaxed text-sm line-clamp-3">
                <?php echo $excerpt; ?>
            </div>
        </div>
    </article>
    <?php
}

function netsukoCssUrl($value, string $fallback = '') {
    $url = trim((string) $value);
    if ($url === '') {
        $url = $fallback;
    }

    $url = preg_replace('/[\x00-\x1F\x7F\'"()\\\\]/', '', $url);
    return netsukoUrl($url, $fallback);
}

function netsukoColor($value, string $fallback) {
    $color = trim((string) $value);
    if (preg_match('/^#(?:[0-9a-f]{3}|[0-9a-f]{6})$/i', $color)) {
        return $color;
    }

    return $fallback;
}

function netsukoMailto($value) {
    $email = trim((string) $value);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return '#';
    }

    return 'mailto:' . netsukoEscape($email);
}

function netsukoThemeAssetUrl(string $path): string {
    $options = \Typecho\Widget::widget('Widget_Options');
    return rtrim((string) $options->themeUrl, '/') . '/' . ltrim($path, '/');
}

function netsukoAssetExternalUrl($value, string $fallback): string {
    $url = trim((string) $value);
    if ($url !== '' && preg_match('/^https?:\/\//i', $url)) {
        return $url;
    }

    return $fallback;
}

function netsukoTailwindCssUrl(): string {
    $options = \Typecho\Widget::widget('Widget_Options');
    $source = (string) ($options->tailwindAssetSource ?: 'local');
    $local = netsukoThemeAssetUrl('assets/css/tailwind.css');

    switch ($source) {
        case 'custom':
            return netsukoAssetExternalUrl($options->tailwindCustomUrl, $local);
        case 'jsdelivr':
            return 'https://cdn.jsdelivr.net/gh/ScDuckXu/netsuko_typecho_theme@main/assets/css/tailwind.css';
        case 'github':
            return 'https://cdn.statically.io/gh/ScDuckXu/netsuko_typecho_theme@main/assets/css/tailwind.css';
        case 'local':
        default:
            return $local;
    }
}

function netsukoFancyboxAssets(): array {
    $options = \Typecho\Widget::widget('Widget_Options');
    $source = (string) ($options->fancyboxAssetSource ?: 'local');
    $local = [
        'css' => netsukoThemeAssetUrl('assets/vendor/fancybox/fancybox.css'),
        'js' => netsukoThemeAssetUrl('assets/vendor/fancybox/fancybox.umd.js')
    ];

    switch ($source) {
        case 'custom':
            return [
                'css' => netsukoAssetExternalUrl($options->fancyboxCustomCssUrl, $local['css']),
                'js' => netsukoAssetExternalUrl($options->fancyboxCustomJsUrl, $local['js'])
            ];
        case 'jsdelivr':
            return [
                'css' => 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css',
                'js' => 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js'
            ];
        case 'github':
            return [
                'css' => 'https://cdn.statically.io/gh/fancyapps/ui@main/dist/fancybox/fancybox.css',
                'js' => 'https://cdn.statically.io/gh/fancyapps/ui@main/dist/fancybox/fancybox.umd.js'
            ];
        case 'local':
        default:
            return $local;
    }
}

function netsukoPjaxEnabled(): bool {
    $options = \Typecho\Widget::widget('Widget_Options');
    return (string) ($options->pjaxEnabled ?: 'on') === 'on';
}

function netsukoPjaxExcludePaths(): array {
    $options = \Typecho\Widget::widget('Widget_Options');
    $raw = trim((string) ($options->pjaxExcludePaths ?: "/admin/\n/action/\n/install.php\n/index.php/action/"));
    $paths = preg_split('/\r\n|\r|\n/', $raw);
    $paths = array_map('trim', is_array($paths) ? $paths : []);
    $paths = array_values(array_filter($paths, static function ($path) {
        return $path !== '';
    }));

    return $paths;
}

function netsukoPjaxScriptUrl(): string {
    return netsukoThemeAssetUrl('assets/js/netsuko-pjax.js');
}

function netsukoLazyLoadEnabled(): bool {
    $options = \Typecho\Widget::widget('Widget_Options');
    return (string) ($options->lazyLoadEnabled ?: 'on') === 'on';
}

function netsukoCodeHighlightEnabled(): bool {
    $options = \Typecho\Widget::widget('Widget_Options');
    return (string) ($options->codeHighlightEnabled ?: 'on') === 'on';
}

function netsukoContentAssets(): array {
    return [
        'lazyLoad' => netsukoLazyLoadEnabled(),
        'highlight' => [
            'enabled' => netsukoCodeHighlightEnabled(),
            'js' => netsukoThemeAssetUrl('assets/vendor/highlight/highlight.min.js')
        ],
        'latex' => [
            'css' => netsukoThemeAssetUrl('assets/vendor/katex/katex.min.css'),
            'js' => netsukoThemeAssetUrl('assets/vendor/katex/katex.min.js'),
            'autoRenderJs' => netsukoThemeAssetUrl('assets/vendor/katex/contrib/auto-render.min.js')
        ]
    ];
}

function netsukoLatexEnabled($archive): bool {
    $options = \Typecho\Widget::widget('Widget_Options');
    $field = 'default';

    if (isset($archive->fields) && isset($archive->fields->enableLatex)) {
        $field = (string) $archive->fields->enableLatex;
    }

    if ($field === 'on') {
        return true;
    }

    if ($field === 'off') {
        return false;
    }

    return (string) ($options->latexDefaultEnabled ?: 'off') === 'on';
}

function netsukoRenderPostContent($archive): string {
    ob_start();
    $archive->content();
    $html = (string) ob_get_clean();

    return netsukoPreparePostContent($html);
}

function netsukoPreparePostContent(string $html): string {
    if (!netsukoLazyLoadEnabled()) {
        return $html;
    }

    return netsukoApplyNativeLazyLoad($html);
}

function netsukoApplyNativeLazyLoad(string $html): string {
    if ($html === '' || !class_exists('DOMDocument')) {
        return $html;
    }

    $document = new \DOMDocument('1.0', 'UTF-8');
    $previousErrors = libxml_use_internal_errors(true);
    $loaded = $document->loadHTML(
        '<?xml encoding="UTF-8"><div id="netsuko-content-root">' . $html . '</div>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();
    libxml_use_internal_errors($previousErrors);

    if (!$loaded) {
        return $html;
    }

    $root = $document->getElementById('netsuko-content-root');
    if (!$root) {
        return $html;
    }

    foreach (['img', 'iframe'] as $tag) {
        foreach ($root->getElementsByTagName($tag) as $node) {
            if ($node->hasAttribute('data-no-lazy')) {
                continue;
            }

            if (!$node->hasAttribute('loading')) {
                $node->setAttribute('loading', 'lazy');
            }
            if ($tag === 'img' && !$node->hasAttribute('decoding')) {
                $node->setAttribute('decoding', 'async');
            }

            $classes = preg_split('/\s+/u', trim($node->getAttribute('class')), -1, PREG_SPLIT_NO_EMPTY) ?: [];
            if (!in_array('netsuko-lazy-media', $classes, true)) {
                $classes[] = 'netsuko-lazy-media';
                $node->setAttribute('class', implode(' ', $classes));
            }
        }
    }

    $result = '';
    foreach ($root->childNodes as $child) {
        $result .= $document->saveHTML($child);
    }

    return $result;
}

function netsukoApplyCommentPagination(): void {
    $options = \Typecho\Widget::widget('Widget_Options');
    if ((string) ($options->commentPaginationEnabled ?: 'on') !== 'on') {
        return;
    }

    $size = (int) ($options->commentPaginationSize ?: 20);
    $size = max(5, min(50, $size));
    $display = (string) ($options->commentPaginationDisplay ?: 'last');

    $options->commentsPageBreak = 1;
    $options->commentsPageSize = $size;
    $options->commentsPageDisplay = $display === 'first' ? 'first' : 'last';
}

function netsukoLinkify($html) {
    return preg_replace_callback(
        '~(?<!["\'>=])(https?://[^\s<]+)~i',
        function ($matches) {
            $url = rtrim($matches[1], '.,;:!?)]}');
            $tail = substr($matches[1], strlen($url));
            $safeUrl = netsukoUrl($url);
            return '<a href="' . $safeUrl . '" target="_blank" rel="noopener noreferrer nofollow">' . netsukoEscape($url) . '</a>' . netsukoEscape($tail);
        },
        $html
    );
}

function netsukoCaptchaMode(): string {
    $options = \Typecho\Widget::widget('Widget_Options');
    $mode = (string) ($options->commentCaptchaMode ?: 'off');

    return in_array($mode, ['off', 'turnstile', 'local'], true) ? $mode : 'off';
}

function netsukoCommentCaptchaRequired($archive = null): bool {
    if (\Widget\User::alloc()->hasLogin()) {
        return false;
    }

    if ($archive && method_exists($archive, 'allow') && !$archive->allow('comment')) {
        return false;
    }

    return netsukoCaptchaMode() !== 'off';
}

function netsukoCaptchaSecret(): string {
    $options = \Typecho\Widget::widget('Widget_Options');
    return defined('__TYPECHO_SECURE_KEY__') ? __TYPECHO_SECURE_KEY__ : (string) $options->title;
}

function netsukoCaptchaSign(string $payload): string {
    return hash_hmac('sha256', $payload, netsukoCaptchaSecret());
}

function netsukoLocalCaptchaChallenge(): array {
    $left = random_int(2, 9);
    $right = random_int(2, 9);
    $expires = time() + 600;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $payload = implode('|', [$left, $right, $expires, hash('sha256', $ip)]);
    $token = base64_encode($payload . '|' . netsukoCaptchaSign($payload));

    return [$left, $right, $token];
}

function netsukoVerifyLocalCaptchaToken(string $token, int $answer): bool {
    $decoded = base64_decode($token, true);
    if ($decoded === false) {
        return false;
    }

    $parts = explode('|', $decoded);
    if (count($parts) !== 5) {
        return false;
    }

    [$left, $right, $expires, $ipHash, $signature] = $parts;
    $payload = implode('|', [$left, $right, $expires, $ipHash]);
    if (!hash_equals(netsukoCaptchaSign($payload), $signature)) {
        return false;
    }

    if ((int) $expires < time()) {
        return false;
    }

    $currentIpHash = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '');
    if (!hash_equals($currentIpHash, $ipHash)) {
        return false;
    }

    return ((int) $left + (int) $right) === $answer;
}

function netsukoRenderCommentCaptcha($archive): void {
    if (!netsukoCommentCaptchaRequired($archive)) {
        return;
    }

    $mode = netsukoCaptchaMode();
    $options = \Typecho\Widget::widget('Widget_Options');
    ?>
    <div class="netsuko-captcha rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 p-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
            <?php _e('验证'); ?> <span class="text-red-500">*</span>
        </label>

        <?php if ($mode === 'turnstile'): ?>
            <?php if ($options->turnstileSiteKey): ?>
                <div class="cf-turnstile" data-sitekey="<?php echo netsukoEscape($options->turnstileSiteKey); ?>" data-theme="auto"></div>
            <?php else: ?>
                <p class="text-sm text-red-500"><?php _e('Turnstile Site Key 尚未配置。'); ?></p>
            <?php endif; ?>
        <?php elseif ($mode === 'local'): ?>
            <?php [$left, $right, $token] = netsukoLocalCaptchaChallenge(); ?>
            <div class="flex flex-col sm:flex-row sm:items-center gap-3" data-netsuko-captcha-mode="local">
                <span class="text-sm text-gray-600 dark:text-gray-300" data-netsuko-local-question>
                    <?php echo netsukoEscape($left . ' + ' . $right . ' ='); ?>
                </span>
                <input type="hidden" name="netsuko_local_captcha_token" value="<?php echo netsukoEscape($token); ?>" />
                <input
                    type="text"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    name="netsuko_local_captcha"
                    class="w-full sm:w-32 px-4 py-2 bg-white dark:bg-darkCard border border-gray-200 dark:border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal/50 focus:border-teal text-gray-900 dark:text-gray-100 transition-colors"
                    autocomplete="off"
                    required
                />
            </div>
        <?php endif; ?>
    </div>
    <?php
}

function netsukoCommentCaptchaFooter($archive): void {
    if (!netsukoCommentCaptchaRequired($archive) || netsukoCaptchaMode() !== 'turnstile') {
        return;
    }

    $options = \Typecho\Widget::widget('Widget_Options');
    if (!$options->turnstileSiteKey) {
        return;
    }

    echo '<script>window.netsukoTurnstileLoaded=function(){if(window.NetsukoTheme&&typeof window.NetsukoTheme.initTurnstile==="function"){window.NetsukoTheme.initTurnstile(document);}};</script>' . PHP_EOL;
    echo '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js?onload=netsukoTurnstileLoaded&amp;render=explicit" async defer data-netsuko-turnstile-api="true"></script>' . PHP_EOL;
}

function netsukoVerifyCommentCaptcha($comment, $content = null) {
    if (!netsukoCommentCaptchaRequired($content)) {
        return $comment;
    }

    $mode = netsukoCaptchaMode();
    if ($mode === 'local') {
        $input = trim((string) ($_POST['netsuko_local_captcha'] ?? ''));
        $token = trim((string) ($_POST['netsuko_local_captcha_token'] ?? ''));

        if ($input === '' || !ctype_digit($input) || !netsukoVerifyLocalCaptchaToken($token, (int) $input)) {
            throw new \Typecho\Exception(_t('验证码不正确，请重新输入。'));
        }

        return $comment;
    }

    if ($mode === 'turnstile') {
        $options = \Typecho\Widget::widget('Widget_Options');
        $secret = trim((string) $options->turnstileSecretKey);
        $token = trim((string) ($_POST['cf-turnstile-response'] ?? ''));

        if ($secret === '') {
            throw new \Typecho\Exception(_t('Turnstile Secret Key 尚未配置。'));
        }

        if ($token === '') {
            throw new \Typecho\Exception(_t('请先完成人机验证。'));
        }

        if (!netsukoVerifyTurnstileToken($secret, $token)) {
            throw new \Typecho\Exception(_t('人机验证失败，请重试。'));
        }
    }

    return $comment;
}

function netsukoVerifyTurnstileToken(string $secret, string $token): bool {
    $params = [
        'secret' => $secret,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
    ];
    $payload = http_build_query($params);
    $endpoint = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    if (function_exists('curl_init')) {
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 8
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
    } else {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $payload,
                'timeout' => 8
            ]
        ]);

        $response = @file_get_contents($endpoint, false, $context);
    }
    if ($response === false) {
        return false;
    }

    $data = json_decode($response, true);
    return is_array($data) && !empty($data['success']);
}

function netsukoDefaultOwnerMailTemplate(): string {
    return <<<HTML
<p>文章 <strong>{title}</strong> 收到新的评论。</p>
<p><strong>评论者：</strong>{author} ({mail})</p>
<p><strong>状态：</strong>{status}</p>
<blockquote>{text}</blockquote>
<p><a href="{permalink}">查看评论</a></p>
HTML;
}

function netsukoDefaultVisitorMailTemplate(): string {
    return <<<HTML
<p>您在 <strong>{title}</strong> 的评论有新的回复。</p>
<p><strong>{author}</strong> 回复：</p>
<blockquote>{text}</blockquote>
<p><strong>您原来的评论：</strong></p>
<blockquote>{parent_text}</blockquote>
<p><a href="{permalink}">查看回复</a></p>
HTML;
}

function netsukoMailEnabled(): bool {
    $options = \Typecho\Widget::widget('Widget_Options');
    return (string) ($options->commentMailEnabled ?: 'off') === 'on';
}

function netsukoHandleCommentMailNotification($commentWidget): void {
    if (!netsukoMailEnabled()) {
        return;
    }

    try {
        $comment = netsukoMailCurrentComment($commentWidget);
        if (!$comment || ($comment['type'] ?? 'comment') !== 'comment') {
            return;
        }

        netsukoSendCommentNotifications($comment, 'created');
    } catch (\Throwable $e) {
        netsukoMailLog('error', 'comment notification failed: ' . $e->getMessage());
    }
}

function netsukoHandleCommentStatusMailNotification($comment, $widget, $status): void {
    if (!netsukoMailEnabled() || !is_array($comment)) {
        return;
    }

    try {
        if (($comment['status'] ?? '') === $status || ($comment['type'] ?? 'comment') !== 'comment') {
            return;
        }

        $comment['status'] = $status;
        netsukoSendCommentNotifications($comment, 'status');
    } catch (\Throwable $e) {
        netsukoMailLog('error', 'comment status notification failed: ' . $e->getMessage());
    }
}

function netsukoMailCurrentComment($widget): ?array {
    if (!$widget || empty($widget->coid)) {
        return null;
    }

    $db = \Typecho\Db::get();
    $comment = $db->fetchRow($db->select()->from('table.comments')->where('coid = ?', $widget->coid)->limit(1));
    return is_array($comment) ? $comment : null;
}

function netsukoSendCommentNotifications(array $comment, string $event): void {
    $options = \Typecho\Widget::widget('Widget_Options');
    $content = netsukoMailContent((int) ($comment['cid'] ?? 0));
    if (!$content) {
        netsukoMailLog('warning', 'skip mail: content not found for comment #' . ($comment['coid'] ?? 'unknown'));
        return;
    }

    $parent = !empty($comment['parent']) ? netsukoMailComment((int) $comment['parent']) : null;
    $context = netsukoMailContext($comment, $content, $parent);

    if ((string) ($options->commentMailNotifyOwner ?: 'on') === 'on' && netsukoOwnerStatusAllowed((string) ($comment['status'] ?? ''))) {
        $owner = netsukoMailOwner($content);
        if ($owner && !empty($owner['mail']) && netsukoShouldSendSelfMail($comment, $owner)) {
            netsukoSendTemplateMail(
                $owner['mail'],
                $owner['name'] ?: $options->title,
                (string) ($options->commentMailOwnerSubject ?: '[{title}] 一文有新的评论'),
                (string) ($options->commentMailOwnerTemplate ?: netsukoDefaultOwnerMailTemplate()),
                $context,
                'owner'
            );
        }
    }

    if ((string) ($options->commentMailNotifyReplied ?: 'on') === 'on' && ($comment['status'] ?? '') === 'approved' && $parent) {
        $recipient = [
            'mail' => $parent['mail'] ?? '',
            'name' => $parent['author'] ?? ''
        ];

        if (!empty($recipient['mail']) && netsukoShouldSendSelfMail($comment, $recipient, $parent)) {
            netsukoSendTemplateMail(
                $recipient['mail'],
                $recipient['name'] ?: $parent['author'],
                (string) ($options->commentMailVisitorSubject ?: '您在 [{title}] 的评论有新的回复'),
                (string) ($options->commentMailVisitorTemplate ?: netsukoDefaultVisitorMailTemplate()),
                $context,
                'visitor'
            );
        }
    }
}

function netsukoOwnerStatusAllowed(string $status): bool {
    $options = \Typecho\Widget::widget('Widget_Options');
    $allowed = $options->commentMailOwnerStatuses;
    if (!is_array($allowed)) {
        $allowed = $allowed ? [$allowed] : ['approved', 'waiting'];
    }

    if (in_array('none', $allowed, true)) {
        return false;
    }

    return in_array($status, $allowed, true);
}

function netsukoShouldSendSelfMail(array $comment, array $recipient, ?array $parent = null): bool {
    $options = \Typecho\Widget::widget('Widget_Options');
    if ((string) ($options->commentMailNotifySelf ?: 'off') === 'on') {
        return true;
    }

    $commentMail = strtolower(trim((string) ($comment['mail'] ?? '')));
    $recipientMail = strtolower(trim((string) ($recipient['mail'] ?? '')));
    if ($commentMail !== '' && $recipientMail !== '' && $commentMail === $recipientMail) {
        return false;
    }

    if (!empty($comment['authorId']) && !empty($recipient['uid']) && (int) $comment['authorId'] === (int) $recipient['uid']) {
        return false;
    }

    if ($parent && !empty($comment['authorId']) && !empty($parent['authorId']) && (int) $comment['authorId'] === (int) $parent['authorId']) {
        return false;
    }

    return true;
}

function netsukoSendTemplateMail(string $toEmail, string $toName, string $subjectTemplate, string $bodyTemplate, array $context, string $target): void {
    $toEmail = trim($toEmail);
    if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
        netsukoMailLog('warning', 'skip invalid recipient for ' . $target . ': ' . $toEmail);
        return;
    }

    $subject = netsukoRenderMailTemplate($subjectTemplate, $context, false);
    $body = netsukoRenderMailTemplate($bodyTemplate, $context, true);
    netsukoSmtpSend($toEmail, $toName, $subject, $body);
    netsukoMailLog('info', 'sent ' . $target . ' mail to ' . $toEmail . ' for comment #' . ($context['coid'] ?? 'unknown'));
}

function netsukoMailContent(int $cid): ?array {
    if ($cid <= 0) {
        return null;
    }

    $db = \Typecho\Db::get();
    $content = $db->fetchRow($db->select()->from('table.contents')->where('cid = ?', $cid)->limit(1));
    return is_array($content) ? $content : null;
}

function netsukoMailComment(int $coid): ?array {
    if ($coid <= 0) {
        return null;
    }

    $db = \Typecho\Db::get();
    $comment = $db->fetchRow($db->select()->from('table.comments')->where('coid = ?', $coid)->limit(1));
    return is_array($comment) ? $comment : null;
}

function netsukoMailOwner(array $content): ?array {
    $ownerId = (int) ($content['authorId'] ?? 0);
    if ($ownerId <= 0) {
        return null;
    }

    $db = \Typecho\Db::get();
    $user = $db->fetchRow($db->select('screenName', 'name', 'mail')->from('table.users')->where('uid = ?', $ownerId)->limit(1));
    if (!is_array($user)) {
        return null;
    }

    return [
        'uid' => $ownerId,
        'name' => $user['screenName'] ?: $user['name'],
        'mail' => $user['mail']
    ];
}

function netsukoMailContext(array $comment, array $content, ?array $parent = null): array {
    $options = \Typecho\Widget::widget('Widget_Options');
    $title = (string) ($content['title'] ?? '');
    $permalink = netsukoContentPermalink($content);
    $coid = (int) ($comment['coid'] ?? 0);

    return [
        'site' => (string) $options->title,
        'title' => $title,
        'author' => (string) ($comment['author'] ?? ''),
        'mail' => (string) ($comment['mail'] ?? ''),
        'status' => netsukoMailStatusLabel((string) ($comment['status'] ?? '')),
        'text' => netsukoMailPlainText((string) ($comment['text'] ?? '')),
        'permalink' => $permalink . ($coid > 0 ? '#comment-' . $coid : ''),
        'parent_author' => (string) ($parent['author'] ?? ''),
        'parent_text' => netsukoMailPlainText((string) ($parent['text'] ?? '')),
        'time' => date('Y-m-d H:i:s', (int) ($comment['created'] ?? time())),
        'coid' => (string) $coid
    ];
}

function netsukoMailStatusLabel(string $status): string {
    $labels = [
        'approved' => '已通过',
        'waiting' => '待审核',
        'spam' => '垃圾评论'
    ];

    return $labels[$status] ?? $status;
}

function netsukoMailPlainText(string $text): string {
    $text = strip_tags($text);
    $text = preg_replace('/\s+/u', ' ', $text);
    return trim((string) $text);
}

function netsukoRenderMailTemplate(string $template, array $context, bool $html): string {
    $replace = [];
    foreach ($context as $key => $value) {
        $replace['{' . $key . '}'] = $html
            ? nl2br(netsukoEscape($value))
            : trim(strip_tags((string) $value));
    }

    return strtr($template, $replace);
}

function netsukoContentPermalink(array $content): string {
    $options = \Typecho\Widget::widget('Widget_Options');
    $type = (string) ($content['type'] ?? 'post');
    $slug = (string) ($content['slug'] ?? '');
    $cid = (int) ($content['cid'] ?? 0);
    $created = (int) ($content['created'] ?? time());
    $date = getdate($created);
    $params = [
        'cid' => $cid,
        'slug' => $slug !== '' ? rawurlencode($slug) : (string) $cid,
        'directory' => '',
        'year' => (string) $date['year'],
        'month' => str_pad((string) $date['mon'], 2, '0', STR_PAD_LEFT),
        'day' => str_pad((string) $date['mday'], 2, '0', STR_PAD_LEFT)
    ];

    $path = \Typecho\Router::url($type, $params);
    if ($path !== '#') {
        return \Typecho\Common::url($path, $options->index);
    }

    if ($type === 'page' && $slug !== '') {
        return rtrim((string) $options->siteUrl, '/') . '/' . rawurlencode($slug) . '.html';
    }

    return rtrim((string) $options->siteUrl, '/') . '/index.php/archives/' . $cid . '/';
}

function netsukoSmtpConfig(): array {
    $options = \Typecho\Widget::widget('Widget_Options');
    $host = trim((string) $options->commentMailSmtpHost);
    $port = (int) ($options->commentMailSmtpPort ?: 465);
    $secure = (string) ($options->commentMailSmtpSecure ?: 'ssl');
    $auth = (string) ($options->commentMailSmtpAuth ?: 'on') === 'on';
    $fromEmail = trim((string) $options->commentMailFromEmail);
    $fromName = trim((string) ($options->commentMailFromName ?: $options->title));
    $replyTo = trim((string) ($options->commentMailReplyTo ?: $fromEmail));
    $timeout = max(5, min(30, (int) ($options->commentMailTimeout ?: 10)));

    return [
        'host' => $host,
        'port' => $port,
        'secure' => in_array($secure, ['ssl', 'tls', 'none'], true) ? $secure : 'ssl',
        'auth' => $auth,
        'user' => trim((string) $options->commentMailSmtpUser),
        'pass' => (string) $options->commentMailSmtpPass,
        'fromEmail' => $fromEmail,
        'fromName' => $fromName,
        'replyTo' => $replyTo,
        'timeout' => $timeout
    ];
}

function netsukoSmtpSend(string $toEmail, string $toName, string $subject, string $html, array $attachments = []): void {
    $config = netsukoSmtpConfig();
    if ($config['host'] === '' || $config['fromEmail'] === '' || !filter_var($config['fromEmail'], FILTER_VALIDATE_EMAIL)) {
        throw new \RuntimeException('SMTP host or sender email is not configured');
    }
    if ($config['auth'] && ($config['user'] === '' || $config['pass'] === '')) {
        throw new \RuntimeException('SMTP username or password is not configured');
    }

    $transport = $config['secure'] === 'ssl' ? 'ssl://' : '';
    $socket = @stream_socket_client(
        $transport . $config['host'] . ':' . $config['port'],
        $errno,
        $errstr,
        $config['timeout'],
        STREAM_CLIENT_CONNECT
    );

    if (!$socket) {
        throw new \RuntimeException('SMTP connect failed: ' . $errstr . ' (' . $errno . ')');
    }

    stream_set_timeout($socket, $config['timeout']);

    try {
        netsukoSmtpExpect($socket, [220]);
        netsukoSmtpCommand($socket, 'EHLO ' . netsukoSmtpHostname(), [250]);

        if ($config['secure'] === 'tls') {
            netsukoSmtpCommand($socket, 'STARTTLS', [220]);
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new \RuntimeException('SMTP STARTTLS negotiation failed');
            }
            netsukoSmtpCommand($socket, 'EHLO ' . netsukoSmtpHostname(), [250]);
        }

        if ($config['auth']) {
            netsukoSmtpCommand($socket, 'AUTH LOGIN', [334]);
            netsukoSmtpCommand($socket, base64_encode($config['user']), [334]);
            netsukoSmtpCommand($socket, base64_encode($config['pass']), [235]);
        }

        netsukoSmtpCommand($socket, 'MAIL FROM:<' . $config['fromEmail'] . '>', [250]);
        netsukoSmtpCommand($socket, 'RCPT TO:<' . $toEmail . '>', [250, 251]);
        netsukoSmtpCommand($socket, 'DATA', [354]);
        $hasFileAttachment = array_filter($attachments, static function ($attachment) {
            return !empty($attachment['path']);
        });
        if ($hasFileAttachment) {
            netsukoSmtpWriteFileMessage($socket, $toEmail, $toName, $subject, $html, $config, $attachments);
        } else {
            netsukoSmtpWrite($socket, netsukoBuildMailMessage($toEmail, $toName, $subject, $html, $config, $attachments) . "\r\n.");
        }
        netsukoSmtpExpect($socket, [250]);
        netsukoSmtpCommand($socket, 'QUIT', [221]);
    } finally {
        fclose($socket);
    }
}

function netsukoBuildMailMessage(string $toEmail, string $toName, string $subject, string $html, array $config, array $attachments = []): string {
    $headers = [
        'Date: ' . date(DATE_RFC2822),
        'From: ' . netsukoMailAddress($config['fromEmail'], $config['fromName']),
        'To: ' . netsukoMailAddress($toEmail, $toName),
        'Subject: ' . netsukoMailHeaderEncode($subject),
        'MIME-Version: 1.0',
        'Message-ID: <' . bin2hex(random_bytes(16)) . '@' . netsukoSmtpHostname() . '>'
    ];

    if (!empty($config['replyTo']) && filter_var($config['replyTo'], FILTER_VALIDATE_EMAIL)) {
        $headers[] = 'Reply-To: ' . netsukoMailAddress($config['replyTo'], $config['fromName']);
    }

    if (empty($attachments)) {
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'Content-Transfer-Encoding: base64';
        return implode("\r\n", $headers) . "\r\n\r\n" . chunk_split(base64_encode($html));
    }

    $boundary = '=_netsuko_' . bin2hex(random_bytes(12));
    $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';

    $body = '--' . $boundary . "\r\n";
    $body .= "Content-Type: text/html; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
    $body .= chunk_split(base64_encode($html)) . "\r\n";

    foreach ($attachments as $attachment) {
        $filename = preg_replace('/[^A-Za-z0-9._-]+/', '-', (string) ($attachment['filename'] ?? 'attachment.dat'));
        $filename = trim($filename, '-_') ?: 'attachment.dat';
        $contentType = preg_match('/^[A-Za-z0-9.+-]+\/[A-Za-z0-9.+-]+$/', (string) ($attachment['contentType'] ?? ''))
            ? (string) $attachment['contentType']
            : 'application/octet-stream';
        $data = (string) ($attachment['data'] ?? '');

        $body .= '--' . $boundary . "\r\n";
        $body .= 'Content-Type: ' . $contentType . '; name="' . $filename . '"' . "\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n";
        $body .= 'Content-Disposition: attachment; filename="' . $filename . '"' . "\r\n\r\n";
        $body .= chunk_split(base64_encode($data)) . "\r\n";
    }

    $body .= '--' . $boundary . '--';
    return implode("\r\n", $headers) . "\r\n\r\n" . $body;
}

function netsukoSmtpWriteFileMessage($socket, string $toEmail, string $toName, string $subject, string $html, array $config, array $attachments): void {
    $boundary = '=_netsuko_' . bin2hex(random_bytes(12));
    $headers = [
        'Date: ' . date(DATE_RFC2822),
        'From: ' . netsukoMailAddress($config['fromEmail'], $config['fromName']),
        'To: ' . netsukoMailAddress($toEmail, $toName),
        'Subject: ' . netsukoMailHeaderEncode($subject),
        'MIME-Version: 1.0',
        'Message-ID: <' . bin2hex(random_bytes(16)) . '@' . netsukoSmtpHostname() . '>',
        'Content-Type: multipart/mixed; boundary="' . $boundary . '"'
    ];

    if (!empty($config['replyTo']) && filter_var($config['replyTo'], FILTER_VALIDATE_EMAIL)) {
        array_splice($headers, 5, 0, 'Reply-To: ' . netsukoMailAddress($config['replyTo'], $config['fromName']));
    }

    netsukoSmtpWriteRaw($socket, implode("\r\n", $headers) . "\r\n\r\n");
    netsukoSmtpWriteRaw($socket, '--' . $boundary . "\r\n");
    netsukoSmtpWriteRaw($socket, "Content-Type: text/html; charset=UTF-8\r\n");
    netsukoSmtpWriteRaw($socket, "Content-Transfer-Encoding: base64\r\n\r\n");
    netsukoSmtpWriteRaw($socket, chunk_split(base64_encode($html)) . "\r\n");

    foreach ($attachments as $attachment) {
        $path = (string) ($attachment['path'] ?? '');
        if ($path === '' || !is_file($path) || !is_readable($path)) {
            throw new \RuntimeException('Mail attachment is not readable');
        }

        $filename = preg_replace('/[^A-Za-z0-9._-]+/', '-', (string) ($attachment['filename'] ?? basename($path)));
        $filename = trim((string) $filename, '-_') ?: 'attachment.dat';
        $contentType = preg_match('/^[A-Za-z0-9.+-]+\/[A-Za-z0-9.+-]+$/', (string) ($attachment['contentType'] ?? ''))
            ? (string) $attachment['contentType']
            : 'application/octet-stream';

        netsukoSmtpWriteRaw($socket, '--' . $boundary . "\r\n");
        netsukoSmtpWriteRaw($socket, 'Content-Type: ' . $contentType . '; name="' . $filename . '"' . "\r\n");
        netsukoSmtpWriteRaw($socket, "Content-Transfer-Encoding: base64\r\n");
        netsukoSmtpWriteRaw($socket, 'Content-Disposition: attachment; filename="' . $filename . '"' . "\r\n\r\n");

        $handle = fopen($path, 'rb');
        if (!$handle) {
            throw new \RuntimeException('Mail attachment could not be opened');
        }
        try {
            while (!feof($handle)) {
                $chunk = fread($handle, 7296);
                if ($chunk === false) {
                    throw new \RuntimeException('Mail attachment could not be read');
                }
                if ($chunk !== '') {
                    netsukoSmtpWriteRaw($socket, chunk_split(base64_encode($chunk), 76, "\r\n"));
                }
            }
        } finally {
            fclose($handle);
        }
        netsukoSmtpWriteRaw($socket, "\r\n");
    }

    netsukoSmtpWriteRaw($socket, '--' . $boundary . "--\r\n.\r\n");
}

function netsukoMailAddress(string $email, string $name = ''): string {
    $email = trim($email);
    $name = trim($name);
    if ($name === '') {
        return '<' . $email . '>';
    }

    return netsukoMailHeaderEncode($name) . ' <' . $email . '>';
}

function netsukoMailHeaderEncode(string $value): string {
    $value = trim(str_replace(["\r", "\n"], '', $value));
    if ($value === '') {
        return '';
    }

    return '=?UTF-8?B?' . base64_encode($value) . '?=';
}

function netsukoSmtpHostname(): string {
    $host = $_SERVER['SERVER_NAME'] ?? 'localhost';
    return preg_match('/^[a-z0-9.-]+$/i', $host) ? $host : 'localhost';
}

function netsukoSmtpCommand($socket, string $command, array $expect): string {
    netsukoSmtpWrite($socket, $command);
    return netsukoSmtpExpect($socket, $expect);
}

function netsukoSmtpWrite($socket, string $line): void {
    netsukoSmtpWriteRaw($socket, $line . "\r\n");
}

function netsukoSmtpWriteRaw($socket, string $data): void {
    $length = strlen($data);
    $written = 0;
    while ($written < $length) {
        $result = fwrite($socket, substr($data, $written));
        if ($result === false || $result === 0) {
            throw new \RuntimeException('SMTP write failed');
        }
        $written += $result;
    }
}

function netsukoSmtpExpect($socket, array $expect): string {
    $response = '';
    while (($line = fgets($socket, 515)) !== false) {
        $response .= $line;
        if (isset($line[3]) && $line[3] === ' ') {
            break;
        }
    }

    if ($response === '') {
        throw new \RuntimeException('SMTP server returned empty response');
    }

    $code = (int) substr($response, 0, 3);
    if (!in_array($code, $expect, true)) {
        throw new \RuntimeException('SMTP unexpected response: ' . trim($response));
    }

    return $response;
}

function netsukoMailLog(string $level, string $message): void {
    try {
        $options = \Typecho\Widget::widget('Widget_Options');
        if ((string) ($options->commentMailLogEnabled ?: 'off') !== 'on') {
            return;
        }

        $dir = (defined('__TYPECHO_UPLOAD_ROOT_DIR__') ? __TYPECHO_UPLOAD_ROOT_DIR__ : __TYPECHO_ROOT_DIR__)
            . (defined('__TYPECHO_UPLOAD_DIR__') ? __TYPECHO_UPLOAD_DIR__ : '/usr/uploads');
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            return;
        }

        $line = '[' . date('Y-m-d H:i:s') . '] [' . strtoupper($level) . '] ' . $message . PHP_EOL;
        @file_put_contents($dir . '/netsuko-mail.log', $line, FILE_APPEND | LOCK_EX);
    } catch (\Throwable $e) {
        // Logging must never interrupt comments.
    }
}

function netsukoDefaultBackupMailTemplate(): string {
    return <<<HTML
<p>{site} 已生成一份 Typecho 自动备份。</p>
<p><strong>生成时间：</strong>{time}</p>
<p><strong>备份文件：</strong>{file}</p>
<p><strong>文件大小：</strong>{size}</p>
<p>附件为 Typecho 兼容备份文件，请妥善保存。</p>
HTML;
}

function netsukoMaybeRunAutoBackup(): void {
    try {
        $options = \Typecho\Widget::widget('Widget_Options');
        if ((string) ($options->autoBackupEnabled ?: 'off') !== 'on') {
            return;
        }

        $recipients = netsukoBackupRecipients((string) $options->autoBackupRecipients);
        if (empty($recipients)) {
            return;
        }

        $interval = max(1, (int) ($options->autoBackupIntervalHours ?: 24)) * 3600;
        $lastRun = (int) netsukoRuntimeOption('netsukoAutoBackupLastRun', '0');
        $nextRetry = (int) netsukoRuntimeOption('netsukoAutoBackupNextRetry', '0');
        if ($nextRetry > time()) {
            return;
        }
        if ($lastRun > 0 && time() - $lastRun < $interval) {
            return;
        }

        $lockPath = netsukoUploadPath('netsuko-auto-backup.lock');
        $lock = @fopen($lockPath, 'c+');
        if (!$lock) {
            return;
        }

        try {
            if (!flock($lock, LOCK_EX | LOCK_NB)) {
                return;
            }

            $lastRun = (int) netsukoRuntimeOption('netsukoAutoBackupLastRun', '0');
            $nextRetry = (int) netsukoRuntimeOption('netsukoAutoBackupNextRetry', '0');
            if ($nextRetry > time()) {
                return;
            }
            if ($lastRun > 0 && time() - $lastRun < $interval) {
                return;
            }

            $failureCount = (int) netsukoRuntimeOption('netsukoAutoBackupFailureCount', '0');
            if ($failureCount >= 3) {
                $failureCount = 0;
            }

            $backup = null;
            try {
                $backup = netsukoBuildTypechoBackup();
                netsukoSendAutoBackupMail($backup, $recipients);
                netsukoSetRuntimeOption('netsukoAutoBackupLastRun', (string) time());
                netsukoSetRuntimeOption('netsukoAutoBackupFailureCount', '0');
                netsukoSetRuntimeOption('netsukoAutoBackupNextRetry', '0');
                netsukoSetRuntimeOption('netsukoAutoBackupLastStatus', 'sent ' . $backup['filename'] . ' at ' . date('Y-m-d H:i:s'));
                netsukoBackupLog('info', 'sent ' . $backup['filename'] . ' to ' . implode(', ', $recipients));
            } catch (\Throwable $e) {
                $failureCount++;
                $delay = $failureCount === 1 ? 60 : ($failureCount === 2 ? 300 : $interval);
                netsukoSetRuntimeOption('netsukoAutoBackupFailureCount', (string) $failureCount);
                netsukoSetRuntimeOption('netsukoAutoBackupNextRetry', (string) (time() + $delay));
                netsukoSetRuntimeOption(
                    'netsukoAutoBackupLastStatus',
                    'failed attempt ' . $failureCount . '/3 at ' . date('Y-m-d H:i:s') . ': ' . $e->getMessage()
                );
                netsukoBackupLog('error', 'attempt ' . $failureCount . '/3: ' . $e->getMessage());
            } finally {
                if (is_array($backup) && !empty($backup['path']) && is_file($backup['path'])) {
                    @unlink($backup['path']);
                }
            }
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    } catch (\Throwable $e) {
        netsukoSetRuntimeOption('netsukoAutoBackupLastStatus', 'failed at ' . date('Y-m-d H:i:s') . ': ' . $e->getMessage());
        netsukoBackupLog('error', $e->getMessage());
    }
}

function netsukoBackupRecipients(string $value): array {
    $parts = preg_split('/[\s,;]+/', $value, -1, PREG_SPLIT_NO_EMPTY);
    $emails = [];

    foreach ($parts ?: [] as $part) {
        $email = trim($part);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emails[strtolower($email)] = $email;
        }
    }

    return array_values($emails);
}

function netsukoBuildTypechoBackup(): array {
    $types = [
        'contents' => 1,
        'comments' => 2,
        'metas' => 3,
        'relationships' => 4,
        'users' => 5,
        'fields' => 6
    ];
    $fields = [
        'contents' => [
            'cid', 'title', 'slug', 'created', 'modified', 'text', 'order', 'authorId',
            'template', 'type', 'status', 'password', 'commentsNum', 'allowComment', 'allowPing', 'allowFeed', 'parent'
        ],
        'comments' => [
            'coid', 'cid', 'created', 'author', 'authorId', 'ownerId',
            'mail', 'url', 'ip', 'agent', 'text', 'type', 'status', 'parent'
        ],
        'metas' => [
            'mid', 'name', 'slug', 'type', 'description', 'count', 'order', 'parent'
        ],
        'relationships' => ['cid', 'mid'],
        'users' => [
            'uid', 'name', 'password', 'mail', 'url', 'screenName',
            'created', 'activated', 'logged', 'group', 'authCode'
        ],
        'fields' => [
            'cid', 'name', 'type', 'str_value', 'int_value', 'float_value'
        ]
    ];

    $db = \Typecho\Db::get();
    $header = str_replace('XXXX', '0001', '%TYPECHO_BACKUP_XXXX%');
    $options = \Typecho\Widget::widget('Widget_Options');
    $host = parse_url((string) $options->siteUrl, PHP_URL_HOST) ?: 'typecho';
    $filename = date('Ymd') . '_' . preg_replace('/[^A-Za-z0-9.-]+/', '-', $host) . '_netsuko_auto.dat';
    $path = tempnam(sys_get_temp_dir(), 'netsuko-backup-');
    if ($path === false) {
        throw new \RuntimeException('Could not create temporary backup file');
    }

    $handle = fopen($path, 'wb');
    if (!$handle) {
        @unlink($path);
        throw new \RuntimeException('Could not open temporary backup file');
    }

    try {
        netsukoWriteFileData($handle, $header);
        foreach ($types as $type => $typeId) {
            $page = 1;
            do {
                $rows = $db->fetchAll($db->select()->from('table.' . $type)->page($page, 20));
                $page++;

                foreach ($rows as $row) {
                    netsukoWriteFileData(
                        $handle,
                        netsukoBuildTypechoBackupBuffer($typeId, netsukoFilterBackupFields($row, $fields[$type]))
                    );
                }
            } while (count($rows) === 20);
        }
        netsukoWriteFileData($handle, $header);
    } catch (\Throwable $e) {
        fclose($handle);
        @unlink($path);
        throw $e;
    }
    fclose($handle);

    $size = filesize($path);
    if ($size === false) {
        @unlink($path);
        throw new \RuntimeException('Could not determine backup file size');
    }

    return [
        'filename' => $filename,
        'path' => $path,
        'size' => $size
    ];
}

function netsukoWriteFileData($handle, string $data): void {
    $length = strlen($data);
    $written = 0;
    while ($written < $length) {
        $result = fwrite($handle, substr($data, $written));
        if ($result === false || $result === 0) {
            throw new \RuntimeException('Could not write backup data');
        }
        $written += $result;
    }
}

function netsukoFilterBackupFields(array $row, array $allowed): array {
    $result = [];
    foreach ($allowed as $key) {
        $result[$key] = array_key_exists($key, $row) ? $row[$key] : null;
    }
    return $result;
}

function netsukoBuildTypechoBackupBuffer(int $type, array $data): string {
    $body = '';
    $schema = [];

    foreach ($data as $key => $value) {
        $schema[$key] = null === $value ? null : strlen((string) $value);
        if (null !== $value) {
            $body .= (string) $value;
        }
    }

    return \Typecho\Common::buildBackupBuffer((string) $type, json_encode($schema), $body);
}

function netsukoSendAutoBackupMail(array $backup, array $recipients): void {
    $options = \Typecho\Widget::widget('Widget_Options');
    $context = [
        'site' => (string) $options->title,
        'date' => date('Y-m-d'),
        'time' => date('Y-m-d H:i:s'),
        'file' => (string) $backup['filename'],
        'size' => netsukoFormatBytes((int) $backup['size'])
    ];
    $subject = netsukoRenderMailTemplate(
        (string) ($options->autoBackupSubject ?: '[{site}] Typecho 自动备份 {date}'),
        $context,
        false
    );
    $body = netsukoRenderMailTemplate(
        (string) ($options->autoBackupTemplate ?: netsukoDefaultBackupMailTemplate()),
        $context,
        true
    );
    $attachment = [
        'filename' => (string) $backup['filename'],
        'contentType' => 'application/octet-stream',
        'path' => (string) $backup['path']
    ];

    foreach ($recipients as $email) {
        netsukoSmtpSend($email, '', $subject, $body, [$attachment]);
    }
}

function netsukoRuntimeOption(string $name, string $default = ''): string {
    $db = \Typecho\Db::get();
    $row = $db->fetchRow($db->select('value')->from('table.options')->where('name = ? AND user = ?', $name, 0)->limit(1));
    return is_array($row) && array_key_exists('value', $row) ? (string) $row['value'] : $default;
}

function netsukoSetRuntimeOption(string $name, string $value): void {
    $db = \Typecho\Db::get();
    $exists = $db->fetchRow($db->select('name')->from('table.options')->where('name = ? AND user = ?', $name, 0)->limit(1));
    if ($exists) {
        $db->query($db->update('table.options')->rows(['value' => $value])->where('name = ? AND user = ?', $name, 0));
        return;
    }

    $db->query($db->insert('table.options')->rows(['name' => $name, 'user' => 0, 'value' => $value]));
}

function netsukoUploadPath(string $filename): string {
    $dir = (defined('__TYPECHO_UPLOAD_ROOT_DIR__') ? __TYPECHO_UPLOAD_ROOT_DIR__ : __TYPECHO_ROOT_DIR__)
        . (defined('__TYPECHO_UPLOAD_DIR__') ? __TYPECHO_UPLOAD_DIR__ : '/usr/uploads');
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    return rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . $filename;
}

function netsukoBackupLog(string $level, string $message): void {
    $line = '[' . date('Y-m-d H:i:s') . '] [' . strtoupper($level) . '] ' . $message . PHP_EOL;
    @file_put_contents(netsukoUploadPath('netsuko-backup.log'), $line, FILE_APPEND | LOCK_EX);
}

function netsukoFormatBytes(int $bytes): string {
    $units = ['B', 'KB', 'MB', 'GB'];
    $size = max(0, $bytes);
    $unit = 0;

    while ($size >= 1024 && $unit < count($units) - 1) {
        $size /= 1024;
        $unit++;
    }

    return ($unit === 0 ? (string) $size : number_format($size, 2)) . ' ' . $units[$unit];
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
    $displayLevel = min((int) $comments->levels, 2);

    if ($displayLevel == 0) {
        $commentLevelClass = ' mt-8';
    } elseif ($displayLevel == 1) {
        $commentLevelClass = ' comment-nested mt-6';
    } else {
        $commentLevelClass = ' comment-nested comment-nested-limit mt-6';
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
                    <div class="comment-content text-sm text-gray-600 dark:text-gray-300 leading-relaxed mb-2 break-words">
                        <?php ob_start(); $comments->content(); echo netsukoLinkify(ob_get_clean()); ?>
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
            <div class="comment-children<?php echo $comments->levels >= 1 ? ' comment-children-collapsed' : ''; ?>">
                <?php $comments->threadedComments($options); ?>
            </div>
        <?php endif; ?>
    </li>
    <?php
}



function getPostThumb($obj) {
    if ($obj->hidden) {
        $defaultThumb = \Typecho\Widget::widget('Widget_Options')->defaultThumb;
        return !empty($defaultThumb)
            ? $defaultThumb
            : Helper::options()->themeUrl . '/img/bg_watermark.jpg';
    }

    // 读取文章独立设置
    $thumb = $obj->fields->thumb;
    if (!empty($thumb)) {
        return $thumb;
    }
    
    // 抓取文章正文里的第一张图片
    preg_match_all('/<img\b[^>]*?\bsrc=[\'"]([^\'"]+)[\'"][^>]*>/i', $obj->content, $matches);
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
