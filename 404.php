<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>

<main class="flex-grow flex items-center justify-center w-full px-4 py-20">
    <div class="text-center max-w-lg">
        <h1 class="text-8xl md:text-9xl font-bold text-teal text-glow mb-6">404</h1>
        <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
            <?php _e('404'); ?>
        </h2>
        <p class="text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
            <?php _e('你寻找的页面消失了。检查一下链接，或者直接回到主页吧。'); ?>
        </p>
        
        <form method="post" action="<?php $this->options->siteUrl(); ?>" class="mb-8 relative max-w-sm mx-auto">
            <input type="text" name="s" class="w-full px-5 py-3 bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-full focus:outline-none focus:ring-2 focus:ring-teal/50 focus:border-teal text-gray-900 dark:text-gray-100 shadow-sm" placeholder="<?php _e('搜索看看...'); ?>" autofocus />
            <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 text-gray-400 hover:text-teal transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
        </form>

        <a href="<?php $this->options->siteUrl(); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-teal text-white font-medium rounded-full hover:bg-teal/90 transition-colors shadow-sm shadow-teal/30">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <?php _e('返回首页'); ?>
        </a>
    </div>
</main>

<?php $this->need('footer.php'); ?>
