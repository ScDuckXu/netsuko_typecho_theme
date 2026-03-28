<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

    <footer class="mt-16 py-10 border-t border-gray-200/50 dark:border-white/5 bg-white/50 dark:bg-darkBg/50 backdrop-blur-md transition-colors duration-500 text-center relative z-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col items-center justify-center gap-3">
            
            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium tracking-wide">
                &copy; <?php echo date('Y'); ?> <a href="<?php $this->options->siteUrl(); ?>" class="hover:text-teal transition-colors"><?php $this->options->title(); ?></a>.
            </p>
            
            <p class="text-xs text-gray-400 dark:text-gray-500">
                Powered by <a href="https://www.typecho.org" target="_blank" rel="noopener" class="hover:text-teal transition-colors">Typecho</a> <span class="mx-1">|</span> Theme <span class="text-teal font-medium"><a href="https://github.com/ScDuckXu/netsuko_typecho_theme" target="_blank" rel="noopener" class="hover:text-teal transition-colors">Netsuko</a></span> by <a href="https://duckxu.com" target="_blank" rel="noopener" class="hover:text-teal transition-colors">DuckXu</a>
            </p>
            
            <?php if ($this->options->icpNum): ?>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                <a href="https://beian.miit.gov.cn/" target="_blank" rel="noopener" class="hover:text-teal transition-colors">
                    <?php $this->options->icpNum(); ?>
                </a>
            </p>
            <?php endif; ?>
            
        </div>
    </footer>

    <?php $this->footer(); ?>
        <button id="back-to-top" class="fixed bottom-8 right-8 z-50 p-3 bg-teal text-white rounded-full shadow-lg shadow-teal/30 opacity-0 pointer-events-none translate-y-5 transition-all duration-300 hover:bg-teal/90 hover:shadow-glow hover:-translate-y-1 focus:outline-none" aria-label="返回顶部">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </button>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const backToTopBtn = document.getElementById('back-to-top');
            
            // 监听滚动事件，控制按钮的显示和隐藏
            window.addEventListener('scroll', function() {
                if (window.scrollY > 400) {
                    // 页面向下滑动超过400px时显示
                    backToTopBtn.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-5');
                    backToTopBtn.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');
                } else {
                    // 否则隐藏并带有向下的位移动画
                    backToTopBtn.classList.add('opacity-0', 'pointer-events-none', 'translate-y-5');
                    backToTopBtn.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                }
            });

            // 点击平滑滚动到顶部
            backToTopBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>

</body>
</html>
