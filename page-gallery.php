<?php
/**
 * 画廊
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
$fancyboxAssets = netsukoFancyboxAssets();
?>

<link rel="stylesheet" href="<?php echo netsukoEscape(netsukoVersionedAssetUrl($fancyboxAssets['css'])); ?>" />

<main class="flex-grow w-full max-w-6xl mx-auto px-4 sm:px-6 py-12 md:py-20 z-10 relative">
    
    <header class="mb-12 text-center">
        <h1 class="text-3xl md:text-5xl font-playfair italic font-semibold text-gray-900 dark:text-white text-glow mb-4"><?php $this->title() ?></h1>
        <p class="text-gray-500 dark:text-gray-400">
            <?php echo $this->fields->subtitle ? netsukoEscape($this->fields->subtitle) : 'Capture the moment.'; ?>
        </p>
    </header>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-16">
        <?php
        $jsonStr = trim($this->text);
        $photos = json_decode($jsonStr, true);
        $validPhotos = [];
        foreach (is_array($photos) ? $photos : [] as $photo) {
            if (!is_array($photo)) {
                continue;
            }

            $rawSrc = trim((string) ($photo['src'] ?? ''));
            $scheme = strtolower((string) parse_url($rawSrc, PHP_URL_SCHEME));
            if (!filter_var($rawSrc, FILTER_VALIDATE_URL) || !in_array($scheme, ['http', 'https'], true)) {
                continue;
            }

            $validPhotos[] = [
                'src' => netsukoUrl($rawSrc),
                'title' => netsukoEscape($photo['title'] ?? 'Untitled'),
                'desc' => netsukoEscape($photo['desc'] ?? ''),
                'date' => netsukoEscape($photo['date'] ?? '')
            ];
        }

        if (!empty($validPhotos)) {
            foreach ($validPhotos as $photo) {
                $src = $photo['src'];
                $title = $photo['title'];
                $desc = $photo['desc'];
                $date = $photo['date'];

                echo '<div class="group bg-white dark:bg-darkCard rounded-2xl border border-gray-100 dark:border-white/5 overflow-hidden hover:border-teal/50 hover:-translate-y-1 hover:shadow-glow transition-all duration-300 flex flex-col">';
                
                // 图片部分：增加 a 标签包裹和 data-fancybox 属性
                echo '<div class="relative overflow-hidden aspect-[4/3] bg-gray-50 dark:bg-darkBg cursor-pointer">';
                echo '<a href="'.$src.'" data-fancybox="gallery" data-caption="'.$title.($desc ? ' - '.$desc : '').'">';
                echo '<img src="'.$src.'" alt="'.$title.'" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">';
                echo '</a>';
                echo '</div>';
                
                echo '<div class="p-5 flex flex-col flex-grow">';
                echo '<div class="flex justify-between items-start mb-2 gap-2">';
                echo '<h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 group-hover:text-teal transition-colors line-clamp-1">'.$title.'</h3>';
                if ($date) {
                    echo '<span class="text-xs text-gray-400 font-mono tracking-wider whitespace-nowrap pt-1">'.$date.'</span>';
                }
                echo '</div>';
                
                if ($desc) {
                    echo '<p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mt-auto">'.$desc.'</p>';
                }
                echo '</div></div>';
            }
        } else {
            echo '<div class="col-span-full p-6 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-500/20 rounded-2xl text-red-600 dark:text-red-400 text-sm">';
            echo '<strong>画廊数据加载失败：</strong> 请确保正文是 JSON 数组，并至少包含一个有效的 HTTP(S) 图片地址。';
            $example = "[\n  {\n    \"src\": \"https://example.com/photo.jpg\",\n    \"title\": \"落日余晖\",\n    \"desc\": \"在海边散步时拍下的美丽夕阳。\",\n    \"date\": \"2025-10-01\"\n  }\n]";
            echo '<pre class="mt-4 whitespace-pre-wrap break-words"><code>' . netsukoEscape($example) . '</code></pre>';
            echo '</div>';
        }
        ?>
    </div>

    <?php $this->need('comments.php'); ?>

</main>

<script src="<?php echo netsukoEscape(netsukoVersionedAssetUrl($fancyboxAssets['js'])); ?>"></script>
<script data-netsuko-rerun="true">
    window.NetsukoTheme = window.NetsukoTheme || {};
    window.NetsukoTheme.initGallery = function (attempt) {
        attempt = attempt || 0;
        if (!window.Fancybox) {
            if (attempt < 20) {
                window.setTimeout(function () {
                    window.NetsukoTheme.initGallery(attempt + 1);
                }, 100);
            }
            return;
        }

        if (typeof Fancybox.unbind === 'function') {
            Fancybox.unbind('[data-fancybox="gallery"]');
        }

        Fancybox.bind('[data-fancybox="gallery"]', {
            // 在这里可以配置 Fancybox 的动画、缩放等选项
            Toolbar: {
                display: {
                    left: ["infobar"],
                    middle: ["zoomIn", "zoomOut", "toggle1to1"],
                    right: ["slideshow", "download", "close"],
                },
            },
        });
    };
    window.NetsukoTheme.initGallery();
</script>

<?php $this->need('footer.php'); ?>
