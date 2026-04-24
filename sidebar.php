<aside class="space-y-6 sticky top-20">
    <div class="mb-6">
        <form method="post" action="<?php $this->options->siteUrl(); ?>" class="relative group" role="search">
            <input type="text" name="s" class="w-full px-4 py-2.5 bg-white dark:bg-darkCard border border-gray-100 dark:border-white/5 rounded-2xl shadow-sm focus:outline-none focus:border-teal/50 focus:ring-2 focus:ring-teal/20 text-sm text-gray-900 dark:text-gray-100 transition-all placeholder-gray-400" placeholder="<?php _e('搜索...'); ?>" required />
            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-teal transition-colors" aria-label="搜索">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
        </form>
    </div>

    <div class="bg-white dark:bg-darkCard rounded-3xl p-6 border border-gray-100 dark:border-white/5 shadow-sm hover:shadow-glow transition-shadow duration-500 mb-8">
        <div class="flex items-center gap-4 mb-6">
            <img src="<?php $this->options->authorAvatar(); ?>" alt="Avatar" class="w-16 h-16 rounded-2xl object-cover" />
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white"><?php $this->options->authorName(); ?></h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?php echo $this->options->mottoQuotes == 'show' ? '"' : ''; ?><?php $this->options->motto(); ?><?php echo $this->options->mottoQuotes == 'show' ? '"' : ''; ?></p>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-3 mb-6">
            <?php if ($this->options->githubUrl): ?>
                <a href="<?php $this->options->githubUrl(); ?>" target="_blank" class="p-2 rounded-xl bg-gray-50 dark:bg-white/5 text-gray-600 dark:text-gray-400 hover:text-teal hover:bg-teal/10 transition-colors" aria-label="GitHub">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>
                </a>
            <?php endif; ?>

            <?php if ($this->options->socialBilibili): ?>
                <a href="<?php $this->options->socialBilibili(); ?>" target="_blank" class="p-2 rounded-xl bg-gray-50 dark:bg-white/5 text-gray-600 dark:text-gray-400 hover:text-teal hover:bg-teal/10 transition-colors" aria-label="Bilibili">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.813 4.653h.854c1.51.054 2.769.578 3.773 1.574 1.004.995 1.524 2.249 1.56 3.76v7.36c-.036 1.51-.556 2.769-1.56 3.765-1.004.995-2.263 1.519-3.773 1.573H5.334c-1.51-.054-2.769-.578-3.773-1.573-1.004-.996-1.524-2.254-1.56-3.765V10.01c.036-1.51.556-2.765 1.56-3.76 1.004-.996 2.263-1.52 3.773-1.574h.774l-1.174-1.12a1.233 1.233 0 0 1-.373-.906c0-.356.124-.658.373-.907l.027-.027c.267-.24.58-.36.94-.36a1.238 1.238 0 0 1 .92.387l3.627 3.44c.066.08.113.147.14.2h3.36c.093-.08.154-.14.18-.18l3.626-3.44a1.233 1.233 0 0 1 .92-.387c.36 0 .673.12.94.36l.027.027c.249.249.373.551.373.907 0 .355-.124.657-.373.906l-1.16 1.12zM5.334 7.427c-.73.026-1.334.28-1.814.76-.48.48-.733 1.084-.76 1.813v7.36c.026.73.28 1.334.76 1.814.48.48 1.084.733 1.814.76h13.332c.73-.027 1.334-.28 1.814-.76.48-.48.733-1.084.76-1.814V10c-.027-.73-.28-1.333-.76-1.813-.48-.48-1.084-.734-1.814-.76H5.334zm1.12 2.893c.31.027.567.147.773.36.207.213.313.467.32.76v1.813a1.16 1.16 0 0 1-.32.773c-.206.214-.463.333-.773.36-.311-.027-.568-.146-.773-.36a1.166 1.166 0 0 1-.32-.773V11.44c.007-.293.113-.547.32-.76.205-.213.462-.333.773-.36zm11.093 0c.307.027.564.147.773.36.21.213.317.467.32.76v1.813c-.003.31-.11.567-.32.773-.21.207-.466.327-.773.36-.31-.027-.567-.146-.773-.36a1.163 1.163 0 0 1-.32-.773V11.44c.006-.293.113-.547.32-.76.206-.213.463-.333.773-.36z"/></svg>
                </a>
            <?php endif; ?>

            <?php if ($this->options->socialDiscord): ?>
                <a href="<?php $this->options->socialDiscord(); ?>" target="_blank" class="p-2 rounded-xl bg-gray-50 dark:bg-white/5 text-gray-600 dark:text-gray-400 hover:text-teal hover:bg-teal/10 transition-colors" aria-label="Discord">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.419-2.157 2.419zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.419-2.157 2.419z"/></svg>
                </a>
            <?php endif; ?>

            <?php if ($this->options->socialWechat): ?>
                <div class="relative group/qr">
                    <button class="p-2 rounded-xl bg-gray-50 dark:bg-white/5 text-gray-600 dark:text-gray-400 hover:text-teal hover:bg-teal/10 transition-colors" aria-label="WeChat">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8.225 3c-4.321 0-7.825 3.037-7.825 6.784 0 2.115 1.115 3.999 2.853 5.253-.166.582-.693 2.12-.733 2.215-.044.103.003.212.105.253.035.014.073.018.11.011.135-.027 1.831-.418 2.585-.624.893.307 1.871.476 2.905.476.242 0 .479-.011.713-.031-.247-.63-.384-1.313-.384-2.022 0-3.411 3.111-6.175 6.949-6.175.228 0 .452.011.673.031C15.19 5.378 12.046 3 8.225 3zm-2.88 3.731c.454 0 .823.369.823.823 0 .454-.369.823-.823.823-.454 0-.823-.369-.823-.823 0-.454.369-.823.823-.823zm5.761 0c.454 0 .823.369.823.823 0 .454-.369.823-.823.823-.454 0-.823-.369-.823-.823 0-.454.369-.823.823-.823zm7.042 3.858c-3.327 0-6.023 2.399-6.023 5.358s2.696 5.358 6.023 5.358c.795 0 1.547-.138 2.234-.383.58.158 1.884.459 1.987.48.104.021.21-.035.251-.137a.203.203 0 0 0 .01-.115c-.03-.105-.436-1.288-.564-1.736 1.337-.965 2.195-2.414 2.195-4.041.001-2.959-2.695-5.358-6.023-5.358zm-2.209 3.275c.349 0 .633.284.633.633s-.284.633-.633.633-.633-.284-.633-.633.284-.633.633-.633zm4.417 0c.349 0 .633.284.633.633s-.284.633-.633.633-.633-.284-.633-.633.284-.633.633-.633z"/></svg>
                    </button>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 p-2 bg-white dark:bg-darkCard rounded-xl shadow-xl border border-gray-100 dark:border-white/10 opacity-0 invisible group-hover/qr:opacity-100 group-hover/qr:visible transition-all duration-300 z-50">
                        <img src="<?php $this->options->socialWechat(); ?>" alt="WeChat QR" class="w-32 h-32 max-w-none rounded-lg" />
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($this->options->sidebarLinks): ?>
            <div class="space-y-3 pt-4 border-t border-gray-100 dark:border-white/5 text-sm">
                <?php 
                    $links = explode("\n", $this->options->sidebarLinks);
                    foreach ($links as $link): 
                        $link = trim($link);
                        if (empty($link)) continue;
                        $parts = explode('|', $link);
                        if (count($parts) >= 2):
                ?>
                    <a href="<?php echo trim($parts[1]); ?>" target="_blank" class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-teal transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        <?php echo htmlspecialchars(trim($parts[0])); ?>
                    </a>
                <?php endif; endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</aside>