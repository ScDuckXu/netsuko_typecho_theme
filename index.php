<?php
/**
 * 使用Tailwind CSS书写。https://github.com/ScDuckXu/netsuko_typecho_theme
 * 
 * @package Netsuko
 * @author DuckXu
 * @version 1.0.3
 * @link https://duckxu.com
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>

<?php $bannerUrl = $this->options->indexBanner; ?>
<div class="w-full relative py-20 md:py-32 mb-12 flex items-center justify-center overflow-hidden border-b border-gray-200/50 dark:border-white/5 <?php echo $bannerUrl ? 'bg-cover bg-center' : 'bg-white/30 dark:bg-darkCard/30 backdrop-blur-md'; ?>" <?php if($bannerUrl) echo 'style="background-image: url(\''.$bannerUrl.'\');"'; ?>>
    
    <?php if($bannerUrl): ?>
        <div class="absolute inset-0 z-0 bg-black/50 pointer-events-none"></div>
    <?php else: ?>
        <div class="absolute inset-0 z-0 opacity-20 pointer-events-none" style="background: radial-gradient(circle at center, var(--teal) 0%, transparent 60%); filter: blur(40px);"></div>
    <?php endif; ?>

    <div class="relative z-10 text-center px-4">
        <h1 class="text-3xl md:text-5xl <?php echo $this->options->mottoFont == 'sans' ? 'font-sans not-italic' : 'font-playfair italic'; ?> font-semibold <?php echo $bannerUrl ? 'text-white' : 'text-gray-900 dark:text-white'; ?> text-glow transition-all duration-500">
            <?php echo $this->options->mottoQuotes == 'show' ? '"' : ''; ?><?php $this->options->motto(); ?><?php echo $this->options->mottoQuotes == 'show' ? '"' : ''; ?>
        </h1>
    </div>
</div>

<main class="flex-grow w-full max-w-6xl mx-auto px-4 sm:px-6 pb-12">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8" id="main" role="main">
        <div class="md:col-span-8 lg:col-span-9 space-y-8">
            <?php if ($this->have()): ?>
                <?php while ($this->next()): ?>
                    <article class="bg-white dark:bg-darkCard rounded-2xl border border-gray-200/50 dark:border-white/5 shadow-sm overflow-hidden transition-all duration-500 hover:scale-[1.02] hover:border-teal/50 hover:shadow-glow flex flex-col sm:flex-row group" itemscope itemtype="http://schema.org/BlogPosting">
                        <div class="w-full sm:w-1/3 h-48 sm:h-auto bg-cover bg-center transition-transform duration-500 group-hover:scale-105" style="background-image: url('<?php echo getPostThumb($this); ?>');"></div>
                        
                        <div class="p-6 md:p-8 sm:w-2/3 flex flex-col justify-center relative z-10 bg-white dark:bg-darkCard">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 group-hover:text-teal transition-colors mb-2">
                                <a itemprop="url" href="<?php $this->permalink() ?>"><?php $this->title() ?></a>
                            </h2>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-4 flex items-center gap-4">
                                <time datetime="<?php $this->date('c'); ?>"><?php $this->date(); ?></time>
                                <span><?php $this->category(','); ?></span>
                            </div>
                            <div class="post-content text-gray-600 dark:text-gray-300 leading-relaxed text-sm line-clamp-3">
                                <?php 
                                    // 摘要判断逻辑：有自定义摘要则输出自定义，没有则截取前30字
                                    if ($this->fields->custom_excerpt) {
                                        echo $this->fields->custom_excerpt;
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
                <?php $this->pageNav('&laquo; Prev', 'Next &raquo;', 3, '...', ['wrapTag' => 'ul', 'wrapClass' => 'flex gap-4', 'itemTag' => 'li', 'currentClass' => 'text-teal border-b-2 border-teal']); ?>
            </div>
        </div>
        
        <aside class="md:col-span-4 lg:col-span-3">
            <?php $this->need('sidebar.php'); ?>
        </aside>
    </div>
</main>

<?php $this->need('footer.php'); ?>
