<?php
/**
 * 友链
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>

<main class="flex-grow w-full max-w-4xl mx-auto px-4 sm:px-6 py-12 md:py-20 z-10 relative">
    
    <header class="mb-12">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4"><?php $this->title() ?></h1>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-16">
        <?php
        // 获取原生未经过 Markdown 解析的文本内容
        $jsonStr = trim($this->text);
        $links = json_decode($jsonStr, true);
        
        if (is_array($links) && !empty($links)) {
            foreach ($links as $link) {
                $name = htmlspecialchars($link['name'] ?? 'Unknown');
                $url = htmlspecialchars($link['url'] ?? '#');
                $desc = htmlspecialchars($link['desc'] ?? '');
                $avatar = htmlspecialchars($link['avatar'] ?? 'https://ui-avatars.com/api/?name='.urlencode($name).'&background=random');
                
                echo '<a href="'.$url.'" target="_blank" rel="noopener noreferrer" class="flex items-center gap-4 p-5 rounded-2xl bg-white dark:bg-darkCard border border-gray-100 dark:border-white/5 hover:border-teal/50 hover:-translate-y-1 hover:shadow-glow transition-all duration-300 group">';
                echo '<img src="'.$avatar.'" alt="'.$name.'" class="w-14 h-14 rounded-full object-cover border-2 border-transparent group-hover:border-teal/50 transition-all">';
                echo '<div class="flex-1 overflow-hidden">';
                echo '<h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 group-hover:text-teal transition-colors truncate">'.$name.'</h3>';
                if ($desc) {
                    echo '<p class="text-sm text-gray-500 dark:text-gray-400 truncate mt-1">'.$desc.'</p>';
                }
                echo '</div></a>';
            }
        } else {
            echo '<div class="col-span-full p-6 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-500/20 rounded-2xl text-red-600 dark:text-red-400 text-sm">';
            echo '<strong>友链数据加载失败：</strong> 请确保在编辑器正文中填写了合法的 JSON 数组，例如：<br><br>';
            echo '<code>[<br>  {<br>    "name": "Typecho",<br>    "url": "http://typecho.org",<br>    "desc": "PHP Blog Engine",<br>    "avatar": "https://..."<br>  }<br>]</code>';
            echo '</div>';
        }
        ?>
    </div>

    <?php $this->need('comments.php'); ?>

</main>

<?php $this->need('footer.php'); ?>
