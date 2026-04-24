<?php
/**
 * 关于
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>

<main class="flex-grow w-full max-w-4xl mx-auto px-4 sm:px-6 py-12 md:py-20 z-10 relative">
    
    <div class="bg-white dark:bg-darkCard rounded-3xl border border-gray-200/50 dark:border-white/5 shadow-sm overflow-hidden transition-all duration-500 hover:shadow-glow mb-12">
        
        <div class="h-48 md:h-64 relative bg-darkBg overflow-hidden flex justify-center items-center">
            <div class="absolute inset-0 opacity-40 pointer-events-none" style="background: radial-gradient(circle at center, var(--teal) 0%, transparent 70%); filter: blur(40px);"></div>
            <h1 class="text-4xl md:text-5xl <?php echo $this->options->mottoFont == 'sans' ? 'font-sans' : 'font-playfair italic'; ?> font-semibold text-white text-glow z-10 text-center px-4 transition-colors duration-500">
                <?php if ($this->fields->subtitle): ?>
                    <?php echo $this->fields->subtitle; ?>
                <?php else: ?>
                    <?php echo $this->options->mottoQuotes == 'show' ? '"' : ''; ?><?php $this->options->motto(); ?><?php echo $this->options->mottoQuotes == 'show' ? '"' : ''; ?>
                <?php endif; ?>
            </h1>
        </div>

        <div class="relative px-6 md:px-12 pb-12">
            <div class="absolute -top-16 left-6 md:left-12">
                <img src="<?php $this->options->authorAvatar(); ?>" alt="Avatar" class="w-32 h-32 rounded-3xl object-cover border-4 border-white dark:border-darkCard shadow-xl bg-white dark:bg-darkCard transition-all duration-500" />
            </div>
            
            <div class="pt-20">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 transition-colors duration-500"><?php $this->options->authorName(); ?></h2>
                
                <div class="flex flex-wrap items-center gap-4 mb-8">
                    <?php if ($this->options->githubUrl): ?>
                    <a href="<?php $this->options->githubUrl(); ?>" target="_blank" class="flex items-center gap-2 text-sm text-gray-500 hover:text-teal transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"></path></svg>
                        GitHub
                    </a>
                    <?php endif; ?>

                    <?php if ($this->options->socialTwitter): ?>
                    <a href="<?php $this->options->socialTwitter(); ?>" target="_blank" class="flex items-center gap-2 text-sm text-gray-500 hover:text-teal transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        Twitter
                    </a>
                    <?php endif; ?>

                    <?php if ($this->options->socialTelegram): ?>
                    <a href="<?php $this->options->socialTelegram(); ?>" target="_blank" class="flex items-center gap-2 text-sm text-gray-500 hover:text-teal transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.295-.6.295-.002 0-.003 0-.005 0l.213-3.054 5.56-5.022c.24-.213-.054-.334-.373-.121l-6.869 4.326-2.96-.924c-.64-.203-.658-.64.135-.954l11.566-4.458c.538-.196 1.006.128.832.94z"/></svg>
                        Telegram
                    </a>
                    <?php endif; ?>

                    <?php if ($this->options->socialEmail): ?>
                    <a href="mailto:<?php $this->options->socialEmail(); ?>" class="flex items-center gap-2 text-sm text-gray-500 hover:text-teal transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Email
                    </a>
                    <?php endif; ?>
                </div>

                <div class="post-content prose prose-teal dark:prose-invert max-w-none <?php echo $this->options->postFont == 'serif' ? 'font-serif' : 'font-sans'; ?> text-gray-700 dark:text-gray-300 leading-relaxed transition-colors duration-500">
                    <?php $this->content(); ?>
                </div>
            </div>
        </div>
    </div>

    <?php $this->need('comments.php'); ?>

</main>

<?php $this->need('footer.php'); ?>
