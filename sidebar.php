<aside>
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
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"></path></svg>
                </a>
            <?php endif; ?>
            <?php if ($this->options->socialTwitter): ?>
                <a href="<?php $this->options->socialTwitter(); ?>" target="_blank" class="p-2 rounded-xl bg-gray-50 dark:bg-white/5 text-gray-600 dark:text-gray-400 hover:text-teal hover:bg-teal/10 transition-colors" aria-label="Twitter">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                </a>
            <?php endif; ?>
            <?php if ($this->options->socialTelegram): ?>
                <a href="<?php $this->options->socialTelegram(); ?>" target="_blank" class="p-2 rounded-xl bg-gray-50 dark:bg-white/5 text-gray-600 dark:text-gray-400 hover:text-teal hover:bg-teal/10 transition-colors" aria-label="Telegram">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.295-.6.295-.002 0-.003 0-.005 0l.213-3.054 5.56-5.022c.24-.213-.054-.334-.373-.121l-6.869 4.326-2.96-.924c-.64-.203-.658-.64.135-.954l11.566-4.458c.538-.196 1.006.128.832.94z"/></svg>
                </a>
            <?php endif; ?>
            <?php if ($this->options->socialEmail): ?>
                <a href="mailto:<?php $this->options->socialEmail(); ?>" class="p-2 rounded-xl bg-gray-50 dark:bg-white/5 text-gray-600 dark:text-gray-400 hover:text-teal hover:bg-teal/10 transition-colors" aria-label="Email">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </a>
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
