<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>

<div class="w-full relative py-16 md:py-24 mb-12 flex items-center justify-center overflow-hidden border-b border-gray-200/50 dark:border-white/5 bg-gray-50 dark:bg-darkCard">
    <div class="relative z-10 text-center px-4 max-w-3xl mx-auto">
        <p class="text-sm uppercase tracking-wider text-teal mb-3">Netsuko</p>
        <h1 class="text-3xl md:text-5xl font-semibold text-gray-900 dark:text-gray-100 mb-3">
            <?php $this->archiveTitle(['category' => _t('%s')], '', ''); ?>
        </h1>
        <?php if ($this->getDescription()): ?>
            <p class="text-gray-500 dark:text-gray-400"><?php echo netsukoEscape($this->getDescription()); ?></p>
        <?php else: ?>
            <p class="text-gray-500 dark:text-gray-400">记录一些短句、想法和正在发生的事。</p>
        <?php endif; ?>
    </div>
</div>

<main class="flex-grow w-full max-w-6xl mx-auto px-4 sm:px-6 pb-12">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8" id="main" role="main">
        <div class="md:col-span-8 lg:col-span-9 min-w-0">
            <?php if ($this->have()): ?>
                <div class="netsuko-shuoshuo-list">
                    <?php while ($this->next()): ?>
                        <article class="netsuko-shuoshuo-item" itemscope itemtype="http://schema.org/Article">
                            <div class="netsuko-shuoshuo-meta">
                                <time datetime="<?php $this->date('c'); ?>"><?php $this->date('Y年m月d日 H:i'); ?></time>
                                <a href="<?php $this->permalink(); ?>" itemprop="url">查看原文</a>
                            </div>

                            <?php if (trim((string) $this->title)): ?>
                                <h2 class="netsuko-shuoshuo-title" itemprop="headline"><?php $this->title(); ?></h2>
                            <?php endif; ?>

                            <div class="post-content prose prose-teal dark:prose-invert max-w-none <?php echo netsukoOption('postFont', 'sans') == 'sans' ? 'font-sans' : 'font-serif'; ?>" itemprop="articleBody">
                                <?php echo netsukoRenderPostContent($this); ?>
                            </div>

                            <div class="netsuko-shuoshuo-footer">
                                <span><?php $this->category(','); ?></span>
                                <a href="<?php $this->permalink(); ?>#comments"><?php $this->commentsNum('暂无评论', '1 条评论', '%d 条评论'); ?></a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-20 bg-white dark:bg-darkCard rounded-xl border border-gray-200/50 dark:border-white/5">
                    <h2 class="text-2xl text-gray-700 dark:text-gray-300 mb-3">还没有说说</h2>
                    <p class="text-gray-500 dark:text-gray-400">在后台文章编辑页点击“撰写说说”开始记录。</p>
                </div>
            <?php endif; ?>

            <div class="flex justify-between items-center py-6 font-medium">
                <?php $this->pageNav('&laquo; Prev', 'Next &raquo;', 3, '...', ['wrapTag' => 'ul', 'wrapClass' => 'pagination flex gap-4', 'itemTag' => 'li', 'currentClass' => 'current']); ?>
            </div>
        </div>

        <aside class="md:col-span-4 lg:col-span-3">
            <?php $this->need('sidebar.php'); ?>
        </aside>
    </div>
</main>

<?php $this->need('footer.php'); ?>
