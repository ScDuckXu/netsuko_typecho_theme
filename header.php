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

    <script src="https://cdn.tailwindcss.com"></script>
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

        <style>
        :root { --teal: #00d2ff; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background-color: rgba(156, 163, 175, 0.3); border-radius: 10px; }
        .dark ::-webkit-scrollbar-thumb { background-color: rgba(255, 255, 255, 0.15); }
        ::-webkit-scrollbar-thumb:hover { background-color: var(--teal); }
        
        .text-glow { text-shadow: 0 0 20px rgba(0, 210, 255, 0.4); }
        body { transition: background-color 0.5s ease, color 0.5s ease; }

    
        code, pre, pre code, .prose code, p code {
            white-space: pre-wrap !important;     
            word-wrap: break-word !important;     
            word-break: break-all !important;     
            overflow-wrap: anywhere !important; 
            display: inline-block !important;     
            max-width: 100% !important;         
        }
        
        pre code {
            display: block !important;
        }
        .post-content h1 { font-size: 1.875rem; font-weight: 700; margin: 2rem 0 1rem; color: var(--teal); font-family: 'Playfair Display', serif; }
        .post-content h2 { font-size: 1.5rem; font-weight: 700; margin: 1.75rem 0 1rem; color: var(--teal); font-family: 'Playfair Display', serif; }
        .post-content h3 { font-size: 1.25rem; font-weight: 600; margin: 1.5rem 0 0.75rem; color: var(--teal); }
        .post-content h4 { font-size: 1.125rem; font-weight: 600; margin: 1.25rem 0 0.5rem; }
        .post-content h5 { font-size: 1rem; font-weight: 600; margin: 1rem 0 0.5rem; }
        .post-content p { margin-bottom: 1.25rem; line-height: 1.8; }
        .post-content a { color: var(--teal); text-decoration: none; border-bottom: 1px dashed var(--teal); word-break: break-word; }
        .post-content a:hover { border-bottom-style: solid; text-shadow: 0 0 8px rgba(0, 210, 255, 0.4); }
        .post-content ul { list-style-type: disc !important; padding-left: 1.5rem; margin-bottom: 1.25rem; }
        .post-content ol { list-style-type: decimal !important; padding-left: 1.5rem; margin-bottom: 1.25rem; }
        .post-content li { display: list-item !important; margin-bottom: 0.5rem; }
        .post-content blockquote { border-left: 4px solid var(--teal); background: rgba(0, 210, 255, 0.1); padding: 1rem 1.5rem; margin: 1.5rem 0; border-radius: 0 8px 8px 0; }
        .post-content img { max-width: 100%; height: auto; border-radius: 12px; margin: 1.5rem auto; border: 1px solid rgba(255,255,255,0.1); }
        .post-content table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
        .post-content th, .post-content td { border: 1px solid rgba(156, 163, 175, 0.3); padding: 0.6rem 1rem; }
        .post-content th { background-color: rgba(0, 210, 255, 0.1); font-weight: 600; }
    </style>


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
