<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>

<?php $thumbUrl = getPostThumb($this); ?>
<div class="w-full relative py-20 md:py-32 mb-12 flex items-center justify-center overflow-hidden border-b border-gray-200/50 dark:border-white/5 bg-cover bg-center" style="background-image: url('<?php echo $thumbUrl; ?>');">
    <div class="absolute inset-0 z-0 bg-black/50 pointer-events-none"></div>
    
    <div class="relative z-10 text-center px-4 max-w-4xl mx-auto mt-8">
        <h1 class="text-3xl md:text-5xl font-semibold text-white text-glow mb-6 leading-tight transition-all duration-500">
            <?php $this->title() ?>
        </h1>
        
        <div class="flex items-center justify-center gap-4 md:gap-6 text-sm text-gray-200 flex-wrap">
            <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <a href="<?php $this->author->permalink(); ?>" class="hover:text-teal transition-colors"><?php $this->author(); ?></a>
            </span>
            <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <time datetime="<?php $this->date('c'); ?>"><?php $this->date(); ?></time>
            </span>
            <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                <?php $this->category(','); ?>
            </span>
        </div>
    </div>
</div>

<main class="flex-grow w-full max-w-6xl mx-auto px-4 sm:px-6 pb-12">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8" id="main" role="main">
        <div class="md:col-span-8 lg:col-span-9 space-y-8">
            <article class="bg-white dark:bg-darkCard rounded-2xl border border-gray-200/50 dark:border-white/5 shadow-sm p-6 md:p-10" itemscope itemtype="http://schema.org/BlogPosting">
                
                <div class="post-content prose prose-teal dark:prose-invert max-w-none <?php echo $this->options->postFont == 'sans' ? 'font-sans' : 'font-serif'; ?>">
                    <?php $this->content(); ?>
                </div>
                
                <?php if(count($this->tags)): ?>
                <div class="mt-10 pt-6 border-t border-gray-100 dark:border-white/5 flex flex-wrap gap-2">
                    <?php foreach($this->tags as $tag): ?>
                        <a href="<?php echo $tag['permalink']; ?>" class="px-3 py-1 bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300 text-sm rounded-full hover:text-teal hover:bg-teal/10 transition-colors"># <?php echo $tag['name']; ?></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </article>

            <?php $this->need('comments.php'); ?>
        </div>
        
        <aside class="md:col-span-4 lg:col-span-3">
            <?php $this->need('sidebar.php'); ?>
        </aside>
    </div>
</main>

<?php $this->need('footer.php'); ?>
