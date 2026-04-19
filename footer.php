<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<footer class="mt-20 border-t border-gray-200/50 dark:border-white/5 bg-white dark:bg-darkBg transition-colors duration-500">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
        
        <div class="flex flex-wrap justify-center gap-x-8 gap-y-4 mb-8">
            
            <a href="<?php $this->options->siteUrl(); ?>" class="flex items-center gap-2 text-sm text-gray-500 hover:text-teal transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Home
            </a>

            <?php if ($this->options->footerLinks): ?>
                <?php 
                    $links = explode("\n", $this->options->footerLinks);
                    foreach ($links as $link): 
                        $link = trim($link);
                        if (empty($link)) continue;
                        $parts = explode('|', $link);
                        if (count($parts) >= 2):
                ?>
                    <a href="<?php echo htmlspecialchars(trim($parts[1])); ?>" target="_blank" class="flex items-center gap-2 text-sm text-gray-500 hover:text-teal transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        <?php echo htmlspecialchars(trim($parts[0])); ?>
                    </a>
                <?php endif; endforeach; ?>
            <?php endif; ?>

            <?php if ($this->options->githubUrl): ?>
            <a href="<?php $this->options->githubUrl(); ?>" target="_blank" class="flex items-center gap-2 text-sm text-gray-500 hover:text-teal transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"></path></svg>
                GitHub
            </a>
            <?php endif; ?>
        </div>

        <div class="text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                &copy; <?php echo date('Y'); ?> <a href="<?php $this->options->siteUrl(); ?>" class="hover:text-teal transition-colors"><?php $this->options->title(); ?></a>.
                
                <?php if ($this->options->icp): ?>
                <span class="mx-2 text-gray-300 dark:text-gray-700">|</span>
                <a href="<?php echo $this->options->icpUrl ? $this->options->icpUrl : 'https://beian.miit.gov.cn/'; ?>" target="_blank" rel="noopener noreferrer" class="hover:text-teal transition-colors">
                    <?php $this->options->icp(); ?>
                </a>
                <?php endif; ?>
            </p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
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
