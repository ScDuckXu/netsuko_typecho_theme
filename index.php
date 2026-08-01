<?php
/**
 * 悦己绮世，自由如风。https://github.com/ScDuckXu/netsuko_typecho_theme
 * @package Netsuko
 * @author Nazuki_Etsuko
 * @version 1.2.3
 * @link https://duckxu.com
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>

<?php 
$bannerUrl = (string) $this->options->indexBanner;
$bannerDarkUrl = (string) $this->options->indexBannerDark;
$bannerHeight = (int) netsukoOption('indexBannerHeight', '300');
$bannerHeight = max(160, min(800, $bannerHeight));
$bannerOpacity = max(0, min(100, (int) netsukoOption('bannerOpacity', '50'))) / 100;
$mottoBanner = netsukoOption('mottoBanner', netsukoOption('motto', '永远相信美好的事情即将发生'));
$mottoFont = netsukoOption('mottoFont', 'playfair');
$mottoQuotes = netsukoOption('mottoQuotes', 'show');

// 获取自定义颜色设置
$mottoColorLight = netsukoColor(netsukoOption('mottoColorLight', '#1f2937'), '#1f2937');
$mottoColorDark = netsukoColor(netsukoOption('mottoColorDark', '#ffffff'), '#ffffff');

$hasBanner = $bannerUrl || $bannerDarkUrl;
?>

<style>
    /* Banner 背景逻辑 */
    <?php if($hasBanner): ?>
    #home-banner {
        background-image: url('<?php echo netsukoCssUrl($bannerUrl ? $bannerUrl : $bannerDarkUrl); ?>');
    }
    <?php if($bannerDarkUrl): ?>
    html.dark #home-banner {
        background-image: url('<?php echo netsukoCssUrl($bannerDarkUrl); ?>') !important;
    }
    <?php endif; ?>
    <?php endif; ?>

    /* 座右铭配色逻辑 */
    #home-motto {
        color: <?php echo $mottoColorLight; ?>;
    }
    html.dark #home-motto {
        color: <?php echo $mottoColorDark; ?> !important;
    }
</style>

<div id="home-banner" class="w-full relative mb-12 flex items-center justify-center overflow-hidden border-b border-gray-200/50 dark:border-white/5 <?php echo $hasBanner ? 'bg-cover bg-center' : 'bg-white/30 dark:bg-darkCard/30 backdrop-blur-md'; ?>" 
     style="min-height: <?php echo $bannerHeight; ?>px;">
    
    <?php if($hasBanner): ?>
        <div class="absolute inset-0 z-0 pointer-events-none" style="background-color: rgba(0,0,0, <?php echo $bannerOpacity; ?>);"></div>
    <?php else: ?>
        <div class="absolute inset-0 z-0 opacity-20 pointer-events-none" style="background: radial-gradient(circle at center, var(--teal) 0%, transparent 60%); filter: blur(40px);"></div>
    <?php endif; ?>

    <div class="relative z-10 text-center px-4">
        <h1 id="home-motto" class="text-3xl md:text-5xl <?php echo $mottoFont == 'sans' ? 'font-sans not-italic' : 'font-playfair italic'; ?> font-semibold text-glow transition-all duration-500">
            <?php echo $mottoQuotes == 'show' ? '"' : ''; ?><?php echo netsukoEscape($mottoBanner); ?><?php echo $mottoQuotes == 'show' ? '"' : ''; ?>
        </h1>
    </div>
</div>

<main class="flex-grow w-full max-w-6xl mx-auto px-4 sm:px-6 pb-12">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8" id="main" role="main">
        <div class="md:col-span-8 lg:col-span-9 space-y-8">
            <?php
            $stickyPosts = ((int) $this->_currentPage <= 1) ? netsukoStickyPosts() : [];
            $stickyCids = array_map('intval', array_column($stickyPosts, 'cid'));
            foreach ($stickyPosts as $stickyPost) {
                netsukoRenderPostCardFromArray($stickyPost, true);
            }
            ?>

            <?php if ($this->have()): ?>
                <?php while ($this->next()): ?>
                    <?php if (in_array((int) $this->cid, $stickyCids, true)) continue; ?>
                    <article class="bg-white dark:bg-darkCard rounded-2xl border border-gray-200/50 dark:border-white/5 shadow-sm overflow-hidden transition-all duration-500 hover:scale-[1.02] hover:border-teal/50 hover:shadow-glow flex flex-col sm:flex-row group" itemscope itemtype="http://schema.org/BlogPosting">
                        <div class="w-full sm:w-1/3 h-48 sm:h-auto bg-cover bg-center transition-transform duration-500 group-hover:scale-105" style="background-image: url('<?php echo netsukoCssUrl(getPostThumb($this)); ?>');"></div>
                        
                        <div class="p-6 md:p-8 sm:w-2/3 flex flex-col justify-center relative z-10 bg-white dark:bg-darkCard">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 group-hover:text-teal transition-colors mb-2">
                                <a itemprop="url" href="<?php $this->permalink() ?>"><?php if ($this->hidden): ?><?php _e('此内容被密码保护'); ?><?php else: ?><?php $this->title(); ?><?php endif; ?></a>
                            </h2>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-4 flex items-center gap-4">
                                <time datetime="<?php $this->date('c'); ?>"><?php $this->date(); ?></time>
                                <span><?php $this->category(','); ?></span>
                            </div>
                            <div class="post-content text-gray-600 dark:text-gray-300 leading-relaxed text-sm line-clamp-3">
                                <?php 
                                    if ($this->hidden) {
                                        _e('请输入密码访问');
                                    } elseif ($this->fields->custom_excerpt) {
                                        echo netsukoEscape($this->fields->custom_excerpt);
                                    } else {
                                        $this->excerpt(30, '...'); 
                                    }
                                ?>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-20 text-gray-500"><?php _e('没有找到内容'); ?></div>
            <?php endif; ?>

            <div class="flex justify-between items-center py-4 font-medium">
                <?php $this->pageNav('&laquo; Prev', 'Next &raquo;', 3, '...', ['wrapTag' => 'ul', 'wrapClass' => 'pagination flex gap-4', 'itemTag' => 'li', 'currentClass' => 'current']); ?>
            </div>
        </div>
        
        <aside class="md:col-span-4 lg:col-span-3">
            <?php $this->need('sidebar.php'); ?>
        </aside>
    </div>
</main>

<?php $this->need('footer.php'); ?>
