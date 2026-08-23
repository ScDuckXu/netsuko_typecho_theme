<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

</div>

<footer class="mt-12 border-t border-gray-200/50 dark:border-white/5 bg-white dark:bg-darkBg transition-colors duration-500">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6">
        <div class="flex flex-col gap-3 text-center text-sm text-gray-500 dark:text-gray-400 sm:flex-row sm:items-center sm:justify-between sm:text-left">
            <p class="font-medium">
                &copy; <?php echo date('Y'); ?> <a href="<?php $this->options->siteUrl(); ?>" class="hover:text-teal transition-colors"><?php $this->options->title(); ?></a>
                <?php if ($this->options->icpNum): ?>
                <span class="mx-1.5 text-gray-300 dark:text-gray-700">·</span>
                <a href="<?php echo netsukoUrl($this->options->icpUrl ? $this->options->icpUrl : 'https://beian.miit.gov.cn/'); ?>" target="_blank" rel="noopener noreferrer" class="hover:text-teal transition-colors">
                    <?php $this->options->icpNum(); ?>
                </a>
                <?php endif; ?>
            </p>

            <p class="text-gray-400 dark:text-gray-500">
                Powered by <a href="http://typecho.org" target="_blank" rel="noopener noreferrer" class="hover:text-teal transition-colors">Typecho</a>
                <span class="mx-1.5 text-gray-300 dark:text-gray-700">·</span>
                Theme <a href="https://github.com/ScDuckXu/netsuko_typecho_theme" target="_blank" rel="noopener noreferrer" class="hover:text-teal transition-colors">Netsuko</a>
                <span class="mx-1.5 text-gray-300 dark:text-gray-700">·</span>
                by <a href="https://duckxu.com" target="_blank" rel="noopener noreferrer" class="hover:text-teal transition-colors">Nazuki</a>
            </p>
        </div>

        <?php if ($this->options->rssFeed || $this->options->siteStatusUrl): ?>
            <nav class="mt-4 flex justify-center gap-4 text-xs text-gray-400 dark:text-gray-500 sm:justify-start" aria-label="页脚链接">
                <?php if ($this->options->rssFeed): ?>
                <a href="<?php echo netsukoUrl($this->options->rssFeed); ?>" target="_blank" rel="noopener noreferrer" class="hover:text-teal transition-colors flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M6.503 20.752c0 1.794-1.456 3.248-3.251 3.248-1.796 0-3.252-1.454-3.252-3.248 0-1.797 1.456-3.252 3.252-3.252 1.795.001 3.251 1.454 3.251 3.252zm-6.503-12.572v4.811c6.05.062 10.96 4.966 11.022 11.009h4.817c-.062-8.71-7.118-15.758-15.839-15.82zm0-8.18v4.831c10.555.062 19.121 8.627 19.183 19.171h4.814c-.062-13.213-10.776-23.931-23.997-24.002z"/></svg>
                    RSS Feed
                </a>
                <?php endif; ?>

                <?php if ($this->options->siteStatusUrl): ?>
                <a href="<?php echo netsukoUrl($this->options->siteStatusUrl); ?>" target="_blank" rel="noopener noreferrer" class="hover:text-teal transition-colors flex items-center gap-1.5">
                    <span class="inline-flex h-2 w-2 rounded-full bg-teal" aria-hidden="true"></span>
                    Status
                </a>
                <?php endif; ?>
            </nav>
            <?php endif; ?>

    </div>
</footer>

<?php $this->footer(); ?>

<button id="back-to-top" class="fixed bottom-8 right-8 z-50 p-3 bg-teal text-white rounded-full shadow-lg shadow-teal/30 opacity-0 pointer-events-none translate-y-5 transition-all duration-300 hover:bg-teal/90 hover:shadow-glow hover:-translate-y-1 focus:outline-none" aria-label="返回顶部">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
    </svg>
</button>

<?php $fancyboxAssets = netsukoFancyboxAssets(); ?>
<?php $contentAssets = netsukoContentAssets(); ?>
<?php if (netsukoPjaxEnabled()): ?>
<script>
    window.NetsukoPjaxConfig = {
        enabled: true,
        container: '#netsuko-pjax-container',
        excludes: <?php echo json_encode(netsukoPjaxExcludePaths(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>,
        assetVersion: <?php echo json_encode(netsukoThemeVersion(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>,
        fancybox: <?php echo json_encode($fancyboxAssets, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>,
        watermark: <?php echo json_encode(netsukoWatermarkConfig(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>,
        content: <?php echo json_encode($contentAssets, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
    };
</script>
<script src="<?php echo netsukoEscape(netsukoVersionedAssetUrl(netsukoPjaxScriptUrl())); ?>" defer></script>
<?php else: ?>
<script>
    window.NetsukoPjaxConfig = {
        enabled: false,
        assetVersion: <?php echo json_encode(netsukoThemeVersion(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>,
        fancybox: <?php echo json_encode($fancyboxAssets, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>,
        watermark: <?php echo json_encode(netsukoWatermarkConfig(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>,
        content: <?php echo json_encode($contentAssets, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
    };
</script>
<script src="<?php echo netsukoEscape(netsukoVersionedAssetUrl(netsukoPjaxScriptUrl())); ?>" defer></script>
<?php endif; ?>

</body>
</html>
