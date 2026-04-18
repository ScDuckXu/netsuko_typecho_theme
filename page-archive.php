<?php
/**
 * 时间轴归档
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>

<main class="flex-grow w-full max-w-4xl mx-auto px-4 sm:px-6 py-12 md:py-20 z-10 relative">
    
    <div class="text-center mb-16">
        <h1 class="text-4xl md:text-5xl font-playfair italic font-semibold text-gray-900 dark:text-white text-glow mb-4"><?php $this->title() ?></h1>
        <p class="text-gray-500 dark:text-gray-400">Footprints in the digital void.</p>
    </div>

    <div class="bg-white dark:bg-darkCard rounded-3xl border border-gray-200/50 dark:border-white/5 shadow-sm p-8 md:p-12">
        <?php
        // 翻页机制
        $stat = \Widget\Stat::alloc();
        $pageSize = 50; // 每页显示的数量
        $currentPage = $this->request->get('page', 1); // 获取当前页码，默认为 1
        $totalPosts = $stat->publishedPostsNum;
        $totalPages = ceil($totalPosts / $pageSize); // 计算总页数

        // 按页码和每页数量查询
        $this->widget('Widget_Contents_Post_Recent', 'pageSize=' . $pageSize . '&page=' . $currentPage)->to($archives);
        
        $year = 0; 
        $month = 0; 
        $isFirstGroup = true; 
        
        $output = '<div class="relative border-l-2 border-gray-100 dark:border-white/10 ml-3 md:ml-6 space-y-10">';
        
        while($archives->next()){
            $year_tmp = date('Y', $archives->created);
            $month_tmp = date('m', $archives->created);
            
            // 判断是否跨月或跨年
            if ($year != $year_tmp || $month != $month_tmp) {
                
                if (!$isFirstGroup) {
                    $output .= '</div></div>';
                }
                
                $year = $year_tmp;
                $month = $month_tmp;
                $isFirstGroup = false;
                
                // 开启新的月份组
                $output .= '<div class="relative">';

                $output .= '<div class="absolute -left-[29px] w-14 h-14 bg-white dark:bg-darkCard rounded-full flex items-center justify-center border-2 border-teal shadow-glow z-10">';
                $output .= '<span class="text-sm font-bold text-teal text-center leading-tight">' . $year . '<br><span class="text-xs">' . $month . '</span></span>';
                $output .= '</div>';
                
                // 开启这个月下的文章列表
                $output .= '<div class="pl-16 pt-2 space-y-6">';
            }
            
            // 文章单项
            $output .= '<article class="group relative">';
            $output .= '<div class="absolute -left-[45px] top-2 w-2 h-2 rounded-full bg-gray-200 dark:bg-gray-700 group-hover:bg-teal transition-colors"></div>';
            $output .= '<time class="text-xs text-gray-400 font-mono tracking-wider block mb-1">' . date('M d, Y', $archives->created) . '</time>';
            $output .= '<a href="' . $archives->permalink . '" class="text-lg font-medium text-gray-800 dark:text-gray-200 group-hover:text-teal transition-colors block">' . $archives->title . '</a>';
            $output .= '</article>';
        }
        
        if (!$isFirstGroup) {
            $output .= '</div></div>';
        }
        
        $output .= '</div>'; 
        
        echo $output;
        ?>
    </div> <?php if ($totalPages > 1): ?>
    <div class="mt-12 flex justify-center items-center gap-4 text-sm font-medium">
        <?php if ($currentPage > 1): ?>
            <a href="?page=<?php echo $currentPage - 1; ?>" class="px-5 py-2.5 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-full text-gray-700 dark:text-gray-300 hover:text-teal hover:border-teal/50 transition-all duration-300">
                &laquo; 更新的
            </a>
        <?php endif; ?>
        
        <span class="text-gray-400">
            <?php echo $currentPage; ?> / <?php echo $totalPages; ?>
        </span>

        <?php if ($currentPage < $totalPages): ?>
            <a href="?page=<?php echo $currentPage + 1; ?>" class="px-5 py-2.5 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-full text-gray-700 dark:text-gray-300 hover:text-teal hover:border-teal/50 transition-all duration-300">
                更早的 &raquo;
            </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</main>

<?php $this->need('footer.php'); ?>