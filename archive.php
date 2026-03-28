<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>

<div class="w-full relative py-20 md:py-32 mb-12 flex items-center justify-center overflow-hidden border-b border-gray-200/50 dark:border-white/5 bg-gray-50 dark:bg-darkCard">
    <div class="relative z-10 text-center px-4 max-w-4xl mx-auto">
        <h1 class="text-3xl md:text-5xl font-semibold text-gray-900 dark:text-gray-100 mb-4 transition-all duration-500">
            <?php $this->archiveTitle([
                'category' => _t('分类： %s'),
                'search'   => _t('包含关键字 %s 的文章'),
                'tag'      => _t('标签： %s'),
                'author'   => _t('%s 发布的文章')
            ], '', ''); ?>
        </h1>
        <p class="text-gray-500 dark:text-gray-400">
            <?php if ($this->is('category')): ?><?php echo $this->getDescription(); ?><?php endif; ?>
        </p>
    </div>
</div>

<main class="flex-grow w-full max-w-6xl mx-auto px-4 sm:px-6 pb-12">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8" id="main" role="main">
        <div class="md:col-span-8 lg:col-span-9 space-y-8">
            
            <?php if ($this->have()): ?>
                <?php while ($this->next()): ?>
                    <article class="group flex flex-col sm:flex-row bg-white dark:bg-darkCard rounded-2xl overflow-hidden border border-gray-200/50 dark:border-white/5 hover:border-teal/30 hover:shadow-glow transition-all duration-300" itemscope itemtype="http://schema.org/BlogPosting">
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
                <div class="text-center py-20 bg-white dark:bg-darkCard rounded-2xl border border-gray-200/50 dark:border-white/5">
                    <h2 class="text-2xl text-gray-700 dark:text-gray-300 mb-4"><?php _e('空空如也'); ?></h2>
                    <p class="text-gray-500"><?php _e('没有找到相关内容，换个关键词搜搜看吧？'); ?></p>
                </div>
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
