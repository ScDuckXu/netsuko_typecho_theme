<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>

<?php $thumbUrl = getPostThumb($this); ?>
<div class="w-full relative py-20 md:py-32 mb-12 flex items-center justify-center overflow-hidden border-b border-gray-200/50 dark:border-white/5 bg-cover bg-center" style="background-image: url('<?php echo $thumbUrl; ?>');">
    <div class="absolute inset-0 z-0 bg-black/50 pointer-events-none"></div>
    
    <div class="relative z-10 text-center px-4 max-w-4xl mx-auto mt-8">
        <h1 class="text-3xl md:text-5xl font-semibold text-white text-glow mb-4 transition-all duration-500">
            <?php $this->title() ?>
        </h1>
    </div>
</div>

<main class="flex-grow w-full max-w-6xl mx-auto px-4 sm:px-6 pb-12">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8" id="main" role="main">
        <div class="md:col-span-8 lg:col-span-9 space-y-8">
            <article class="bg-white dark:bg-darkCard rounded-2xl border border-gray-200/50 dark:border-white/5 shadow-sm p-6 md:p-10" itemscope itemtype="http://schema.org/BlogPosting">
                <div class="post-content prose prose-teal dark:prose-invert max-w-none <?php echo $this->options->postFont == 'sans' ? 'font-sans' : 'font-serif'; ?>">
                    <?php $this->content(); ?>
                </div>
            </article>

            <?php $this->need('comments.php'); ?>
        </div>
        
        <aside class="md:col-span-4 lg:col-span-3">
            <?php $this->need('sidebar.php'); ?>
        </aside>
    </div>
</main>

<?php $this->need('footer.php'); ?>
