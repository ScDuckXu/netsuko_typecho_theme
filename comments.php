<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<div id="comments" class="mt-12 bg-white dark:bg-darkCard rounded-2xl border border-gray-200/50 dark:border-white/5 shadow-sm p-6 md:p-10">
    <?php $this->comments()->to($comments); ?>
    
    <?php if ($comments->have()): ?>
        <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-8 flex items-center gap-2">
            <svg class="w-6 h-6 text-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
            <?php $this->commentsNum(_t('暂无评论'), _t('仅有 1 条评论'), _t('已有 %d 条评论')); ?>
        </h3>
        
        <?php $comments->listComments(); ?>
        
        <div class="mt-8 flex justify-center">
            <?php $comments->pageNav('&laquo;', '&raquo;', 3, '...', array('wrapTag' => 'ul', 'wrapClass' => 'flex gap-2', 'itemTag' => 'li', 'currentClass' => 'text-teal font-bold')); ?>
        </div>
        
    <?php endif; ?>

    <?php if($this->allow('comment')): ?>
        <div id="<?php $this->respondId(); ?>" class="respond mt-12 pt-8 border-t border-gray-100 dark:border-white/5">
            <div class="cancel-comment-reply mb-4 text-sm text-red-500 hover:underline">
                <?php $comments->cancelReply(); ?>
            </div>
        
            <h3 id="response" class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">
                <?php _e('添加新评论'); ?>
            </h3>
        
            <form method="post" action="<?php $this->commentUrl() ?>" id="comment-form" role="form" class="space-y-5">
                <?php if($this->user->hasLogin()): ?>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <?php _e('登录身份: '); ?>
                        <a href="<?php $this->options->profileUrl(); ?>" class="text-teal hover:underline font-medium"><?php $this->user->screenName(); ?></a>. 
                        <a href="<?php $this->options->logoutUrl(); ?>" class="text-gray-500 hover:text-red-500 hover:underline transition-colors" title="Logout"><?php _e('退出'); ?> &raquo;</a>
                    </p>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="author" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"><?php _e('称呼'); ?> <span class="text-red-500">*</span></label>
                            <input type="text" name="author" id="author" class="w-full px-4 py-2 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal/50 focus:border-teal text-gray-900 dark:text-gray-100 transition-colors" value="<?php $this->remember('author'); ?>" required />
                        </div>
                        <div>
                            <label for="mail" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"><?php _e('邮箱'); ?> <?php if ($this->options->commentsRequireMail): ?><span class="text-red-500">*</span><?php endif; ?></label>
                            <input type="email" name="mail" id="mail" class="w-full px-4 py-2 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal/50 focus:border-teal text-gray-900 dark:text-gray-100 transition-colors" value="<?php $this->remember('mail'); ?>" <?php if ($this->options->commentsRequireMail): ?>required<?php endif; ?> />
                        </div>
                        <div>
                            <label for="url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"><?php _e('网站'); ?> <?php if ($this->options->commentsRequireURL): ?><span class="text-red-500">*</span><?php endif; ?></label>
                            <input type="url" name="url" id="url" class="w-full px-4 py-2 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal/50 focus:border-teal text-gray-900 dark:text-gray-100 transition-colors" placeholder="https://" value="<?php $this->remember('url'); ?>" <?php if ($this->options->commentsRequireURL): ?>required<?php endif; ?> />
                        </div>
                    </div>
                <?php endif; ?>
        
                <div>
                    <label for="textarea" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"><?php _e('内容'); ?> <span class="text-red-500">*</span></label>
                    <textarea rows="5" name="text" id="textarea" class="w-full px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal/50 focus:border-teal text-gray-900 dark:text-gray-100 transition-colors resize-y" required><?php $this->remember('text'); ?></textarea>
                </div>
        
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-teal text-white font-medium rounded-lg hover:bg-teal/90 transition-colors shadow-sm shadow-teal/30">
                        <?php _e('提交评论'); ?>
                    </button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="mt-8 text-center text-gray-500 dark:text-gray-400 py-6 bg-gray-50 dark:bg-white/5 rounded-xl border border-gray-100 dark:border-white/5">
            <?php _e('评论已关闭'); ?>
        </div>
    <?php endif; ?>
</div>
