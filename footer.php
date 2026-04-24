<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<footer class="mt-20 border-t border-gray-200/50 dark:border-white/5 bg-white dark:bg-darkBg transition-colors duration-500">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-12">    
        <div class="text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                &copy; <?php echo date('Y'); ?> <a href="<?php $this->options->siteUrl(); ?>" class="hover:text-teal transition-colors"><?php $this->options->title(); ?></a>.
                
                <?php if ($this->options->icpNum): ?>
                <span class="mx-2 text-gray-300 dark:text-gray-700">|</span>
                <a href="<?php echo $this->options->icpUrl ? $this->options->icpUrl : 'https://beian.miit.gov.cn/'; ?>" target="_blank" rel="noopener noreferrer" class="hover:text-teal transition-colors">
                    <?php $this->options->icpNum(); ?>
                </a>
                <?php endif; ?>
            </p>

            <?php if ($this->options->rssFeed || $this->options->siteStatusUrl): ?>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2 flex justify-center items-center gap-3">
                <?php if ($this->options->rssFeed): ?>
                <a href="<?php $this->options->rssFeed(); ?>" target="_blank" class="hover:text-teal transition-colors flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M6.503 20.752c0 1.794-1.456 3.248-3.251 3.248-1.796 0-3.252-1.454-3.252-3.248 0-1.797 1.456-3.252 3.252-3.252 1.795.001 3.251 1.454 3.251 3.252zm-6.503-12.572v4.811c6.05.062 10.96 4.966 11.022 11.009h4.817c-.062-8.71-7.118-15.758-15.839-15.82zm0-8.18v4.831c10.555.062 19.121 8.627 19.183 19.171h4.814c-.062-13.213-10.776-23.931-23.997-24.002z"/></svg>
                    RSS Feed
                </a>
                <?php endif; ?>

                <?php if ($this->options->rssFeed && $this->options->siteStatusUrl): ?>
                <span class="text-gray-200 dark:text-gray-800">/</span>
                <?php endif; ?>

                <?php if ($this->options->siteStatusUrl): ?>
                <a href="<?php $this->options->siteStatusUrl(); ?>" target="_blank" class="hover:text-teal transition-colors flex items-center gap-1.5">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-teal"></span>
                    </span>
                    Status
                </a>
                <?php endif; ?>
            </p>
            <?php endif; ?>

            <p class="text-xs text-gray-400 dark:text-gray-500 mt-4">
                Powered by <a href="http://typecho.org" target="_blank" class="hover:text-teal transition-colors">Typecho</a> 
                & Theme <a href="https://github.com/ScDuckXu/netsuko_typecho_theme" target="_blank" class="hover:text-teal transition-colors">Netsuko</a> by <a href="https://duckxu.com" target="_blank" class="hover:text-teal transition-colors">DuckXu</a>
            </p>
        </div>

    </div>
</footer>

<script>
<?php $this->footer(); ?>

<button id="back-to-top" class="fixed bottom-8 right-8 z-50 p-3 bg-teal text-white rounded-full shadow-lg shadow-teal/30 opacity-0 pointer-events-none translate-y-5 transition-all duration-300 hover:bg-teal/90 hover:shadow-glow hover:-translate-y-1 focus:outline-none" aria-label="返回顶部">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
    </svg>
</button>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const backToTopBtn = document.getElementById('back-to-top');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 400) {
                backToTopBtn.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-5');
                backToTopBtn.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');
            } else {
                backToTopBtn.classList.add('opacity-0', 'pointer-events-none', 'translate-y-5');
                backToTopBtn.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
            }
        });
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
</script>

</body>
</html>