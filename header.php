<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE html>
<html lang="zh-CN" class="antialiased">

<head>
    <meta charset="<?php $this->options->charset(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php $this->archiveTitle(array(
            'category'  =>  _t('分类 %s 下的文章'),
            'search'    =>  _t('包含关键字 %s 的文章'),
            'tag'       =>  _t('标签 %s 下的文章'),
            'author'    =>  _t('%s 发布的文章')
        ), '', ' - '); ?><?php $this->options->title(); ?></title>

    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link rel="stylesheet" href="<?php $this->options->themeUrl('style.css'); ?>?v=2.0">

    <script>
        tailwind.config = {
            darkMode: 'class', 
            theme: {
                extend: {
                    colors: {
                        teal: '#00d2ff', 
                        darkBg: '#121418', 
                        darkCard: '#1a1d24', 
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                        playfair: ['"Playfair Display"', 'serif'],
                    },
                    boxShadow: {
                        glow: '0 0 20px rgba(0, 210, 255, 0.15)', 
                    }
                }
            }
        }
    </script>



    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        function toggleTheme() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                html.classList.add('dark');
                localStorage.theme = 'dark';
            }
        }
    </script>


        <?php if ($this->options->customHeadCode): ?>
        <?php $this->options->customHeadCode(); ?>
    <?php endif; ?>

    <?php $this->header(); ?>
</head>


<body class="min-h-screen flex flex-col font-sans selection:bg-teal/30 selection:text-teal pt-16">


    <header id="header" class="fixed top-0 left-0 right-0 z-50 bg-white/70 dark:bg-darkBg/70 backdrop-blur-xl border-b border-gray-200/50 dark:border-white/5 transition-colors duration-500">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="<?php $this->options->siteUrl(); ?>" class="text-xl font-semibold tracking-tight hover:text-teal transition-colors">
                    <?php $this->options->title() ?>
                </a>
            </div>
            
            <div class="hidden md:flex items-center gap-6">
                <nav class="flex items-center gap-6 text-sm font-medium">
                    <a href="<?php $this->options->siteUrl(); ?>" class="hover:text-teal transition-colors <?php if($this->is('index')): ?>text-teal<?php endif; ?>"><?php _e('Home'); ?></a>
                    <?php \Widget\Contents\Page\Rows::alloc()->to($pages); ?>
                    <?php while ($pages->next()): ?>
                        <a href="<?php $pages->permalink(); ?>" class="hover:text-teal transition-colors <?php if($this->is('page', $pages->slug)): ?>text-teal<?php endif; ?>"><?php $pages->title(); ?></a>
                    <?php endwhile; ?>
                </nav>
                
                <button onclick="toggleTheme()" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-white/10 transition-colors duration-300 group theme-toggle-btn" aria-label="Toggle Dark Mode">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300 group-hover:text-teal transition-colors duration-300 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300 group-hover:text-teal transition-colors duration-300 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </button>
            </div>

            <div class="flex md:hidden items-center gap-4">
                <button onclick="toggleTheme()" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-white/10 transition-colors duration-300 group" aria-label="Toggle Dark Mode">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300 group-hover:text-teal dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300 group-hover:text-teal hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </button>
                <button id="mobile-menu-open" class="text-gray-600 dark:text-gray-300 hover:text-teal transition-colors" aria-label="Open Menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>
        </div>
    </header>

    <div id="mobile-drawer" class="fixed inset-0 z-[60] hidden">
        <div id="drawer-overlay" class="absolute inset-0 bg-black/20 dark:bg-black/50 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>
        <div id="drawer-panel" class="absolute top-0 right-0 bottom-0 w-64 bg-white dark:bg-darkCard border-l border-gray-200 dark:border-white/5 shadow-2xl transform translate-x-full transition-transform duration-300 ease-out flex flex-col">
            <div class="h-16 flex items-center justify-end px-4 border-b border-gray-100 dark:border-white/5">
                <button id="mobile-menu-close" class="p-2 text-gray-500 hover:text-teal transition-colors rounded-full hover:bg-gray-100 dark:hover:bg-white/5" aria-label="Close Menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <nav class="flex-grow py-6 px-6 flex flex-col gap-4 text-base font-medium overflow-y-auto">
                <a href="<?php $this->options->siteUrl(); ?>" class="py-2 border-b border-gray-100 dark:border-white/5 text-gray-800 dark:text-gray-200 hover:text-teal transition-colors <?php if($this->is('index')): ?>text-teal border-teal dark:border-teal<?php endif; ?>"><?php _e('Home'); ?></a>
                <?php \Widget\Contents\Page\Rows::alloc()->to($pages); ?>
                <?php while ($pages->next()): ?>
                    <a href="<?php $pages->permalink(); ?>" class="py-2 border-b border-gray-100 dark:border-white/5 text-gray-800 dark:text-gray-200 hover:text-teal transition-colors <?php if($this->is('page', $pages->slug)): ?>text-teal border-teal dark:border-teal<?php endif; ?>"><?php $pages->title(); ?></a>
                <?php endwhile; ?>
            </nav>
        </div>
    </div>

    <script>
        const drawer = document.getElementById('mobile-drawer');
        const overlay = document.getElementById('drawer-overlay');
        const panel = document.getElementById('drawer-panel');
        const openBtn = document.getElementById('mobile-menu-open');
        const closeBtn = document.getElementById('mobile-menu-close');

        function openDrawer() {
            drawer.classList.remove('hidden');
            void drawer.offsetWidth; 
            overlay.classList.remove('opacity-0');
            panel.classList.remove('translate-x-full');
            document.body.style.overflow = 'hidden'; 
        }

        function closeDrawer() {
            overlay.classList.add('opacity-0');
            panel.classList.add('translate-x-full');
            document.body.style.overflow = '';
            setTimeout(() => drawer.classList.add('hidden'), 300);
        }

        openBtn.addEventListener('click', openDrawer);
        closeBtn.addEventListener('click', closeDrawer);
        overlay.addEventListener('click', closeDrawer);
    </script>

</body>
</html>
