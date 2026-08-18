<?php
/**
 * 设备
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
$payload = netsukoDevicesPayload($this);
$groups = $payload['groups'];
$deviceCount = 0;
foreach ($groups as $group) {
    $deviceCount += count($group['items']);
}
$fancyboxAssets = netsukoFancyboxAssets();
?>

<link rel="stylesheet" href="<?php echo netsukoEscape(netsukoVersionedAssetUrl($fancyboxAssets['css'])); ?>" />

<main class="devices-page flex-grow w-full max-w-6xl mx-auto px-4 sm:px-6 py-12 md:py-20 z-10 relative">
    <header class="devices-hero mb-10 md:mb-14">
        <div>
            <p class="devices-kicker">Devices</p>
            <h1 class="text-3xl md:text-5xl font-semibold text-gray-900 dark:text-white mb-4"><?php $this->title() ?></h1>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl leading-relaxed">
                <?php echo $this->fields->subtitle ? netsukoEscape($this->fields->subtitle) : 'Daily drivers, tools, and small pieces of personal infrastructure.'; ?>
            </p>
        </div>
        <?php if ($deviceCount > 0): ?>
            <div class="devices-count" aria-label="设备数量">
                <strong><?php echo (int) $deviceCount; ?></strong>
                <span>items</span>
            </div>
        <?php endif; ?>
    </header>

    <?php if (!empty($groups)): ?>
        <nav class="devices-nav mb-10" aria-label="设备分组">
            <?php foreach ($groups as $index => $group): ?>
                <a href="#device-group-<?php echo (int) $index; ?>"><?php echo netsukoEscape($group['name']); ?></a>
            <?php endforeach; ?>
        </nav>

        <div class="space-y-12 md:space-y-16 mb-16">
            <?php foreach ($groups as $index => $group): ?>
                <section id="device-group-<?php echo (int) $index; ?>" class="devices-section">
                    <div class="devices-section-header">
                        <div>
                            <h2><?php echo netsukoEscape($group['name']); ?></h2>
                            <?php if (!empty($group['desc'])): ?>
                                <p><?php echo netsukoEscape($group['desc']); ?></p>
                            <?php endif; ?>
                        </div>
                        <span><?php echo count($group['items']); ?></span>
                    </div>

                    <div class="devices-grid">
                        <?php foreach ($group['items'] as $device): ?>
                            <?php
                            $name = (string) ($device['name'] ?? 'Device');
                            $role = (string) ($device['role'] ?? ($device['type'] ?? ''));
                            $status = (string) ($device['status'] ?? '使用中');
                            $image = (string) ($device['image'] ?? ($device['cover'] ?? ''));
                            $image = $image !== '' ? $image : Helper::options()->themeUrl . '/img/bg_watermark.jpg';
                            $url = (string) ($device['url'] ?? '');
                            $brand = (string) ($device['brand'] ?? '');
                            $model = (string) ($device['model'] ?? '');
                            $since = (string) ($device['since'] ?? ($device['date'] ?? ''));
                            $price = (string) ($device['price'] ?? '');
                            $note = (string) ($device['note'] ?? ($device['desc'] ?? ($device['description'] ?? '')));
                            $accent = netsukoColor($device['accent'] ?? '#39C5BB', '#39C5BB');
                            $tags = netsukoDeviceTags($device['tags'] ?? []);
                            $specs = netsukoDeviceSpecs($device['specs'] ?? []);
                            ?>
                            <article class="device-card" style="--device-accent: <?php echo $accent; ?>;">
                                <a class="device-media" href="<?php echo netsukoUrl($image); ?>" data-fancybox="devices" data-caption="<?php echo netsukoEscape($name); ?>">
                                    <img src="<?php echo netsukoUrl($image); ?>" alt="<?php echo netsukoEscape($name); ?>" loading="lazy">
                                    <span class="device-status"><?php echo netsukoEscape($status); ?></span>
                                </a>

                                <div class="device-body">
                                    <header class="device-title-row">
                                        <div>
                                            <?php if ($role): ?><p><?php echo netsukoEscape($role); ?></p><?php endif; ?>
                                            <h3>
                                                <?php if ($url): ?>
                                                    <a href="<?php echo netsukoUrl($url); ?>" target="_blank" rel="noopener noreferrer"><?php echo netsukoEscape($name); ?></a>
                                                <?php else: ?>
                                                    <?php echo netsukoEscape($name); ?>
                                                <?php endif; ?>
                                            </h3>
                                        </div>
                                    </header>

                                    <?php if ($brand || $model || $since || $price): ?>
                                        <dl class="device-meta" aria-label="设备信息">
                                            <?php if ($brand): ?><div><dt>品牌</dt><dd><?php echo netsukoEscape($brand); ?></dd></div><?php endif; ?>
                                            <?php if ($model): ?><div><dt>型号</dt><dd><?php echo netsukoEscape($model); ?></dd></div><?php endif; ?>
                                            <?php if ($since): ?><div><dt>入手</dt><dd><?php echo netsukoEscape($since); ?></dd></div><?php endif; ?>
                                            <?php if ($price): ?><div><dt>价格</dt><dd><?php echo netsukoEscape($price); ?></dd></div><?php endif; ?>
                                        </dl>
                                    <?php endif; ?>

                                    <?php if (!empty($specs) || $note || !empty($tags)): ?>
                                        <details class="device-details">
                                            <summary>
                                                <span>详细信息</span>
                                                <span class="device-details-count"><?php echo count($specs); ?> 项规格</span>
                                            </summary>
                                            <div class="device-details-content">
                                                <?php if (!empty($specs)): ?>
                                                    <dl class="device-specs" aria-label="详细规格">
                                                        <?php foreach ($specs as $spec): ?>
                                                            <div>
                                                                <dt><?php echo netsukoEscape($spec['label']); ?></dt>
                                                                <dd><?php echo netsukoEscape($spec['value']); ?></dd>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </dl>
                                                <?php endif; ?>

                                                <?php if ($note): ?>
                                                    <p class="device-note"><?php echo netsukoEscape($note); ?></p>
                                                <?php endif; ?>

                                                <?php if (!empty($tags)): ?>
                                                    <div class="device-tags" aria-label="设备标签">
                                                        <?php foreach ($tags as $tag): ?><span><?php echo netsukoEscape($tag); ?></span><?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </details>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="p-6 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-500/20 rounded-2xl text-red-600 dark:text-red-400 text-sm leading-relaxed mb-16">
            <strong>设备数据为空或 JSON 无法解析。</strong>
            请在这个独立页面的正文中填写合法的设备 JSON 数组。
            <?php if ($payload['error']): ?>错误信息：<?php echo netsukoEscape($payload['error']); ?><?php endif; ?>
            <pre class="mt-4 overflow-auto text-xs bg-white/70 dark:bg-black/20 p-4 rounded-xl text-gray-700 dark:text-gray-200"><code>[
  {
    "name": "主力设备",
    "desc": "每天都在使用的设备",
    "items": [
      {
        "name": "MacBook Pro",
        "role": "主力生产力",
        "status": "服役中",
        "image": "https://example.com/device.jpg",
        "brand": "Apple",
        "model": "M3 Pro",
        "since": "2025",
        "tags": ["macOS", "Work"],
        "specs": {"CPU": "M3 Pro", "Memory": "36GB"},
        "note": "短评或使用感受"
      }
    ]
  }
]</code></pre>
        </div>
    <?php endif; ?>

    <?php $this->need('comments.php'); ?>
</main>

<script src="<?php echo netsukoEscape(netsukoVersionedAssetUrl($fancyboxAssets['js'])); ?>"></script>
<script data-netsuko-rerun="true">
    window.NetsukoTheme = window.NetsukoTheme || {};
    window.NetsukoTheme.initDevices = function (attempt) {
        attempt = attempt || 0;
        if (!window.Fancybox) {
            if (attempt < 20) {
                window.setTimeout(function () {
                    window.NetsukoTheme.initDevices(attempt + 1);
                }, 100);
            }
            return;
        }

        if (typeof Fancybox.unbind === 'function') {
            Fancybox.unbind('[data-fancybox="devices"]');
        }

        Fancybox.bind('[data-fancybox="devices"]', {
            contentClick: 'toggleZoom',
            Toolbar: {
                display: {
                    left: ['infobar'],
                    middle: ['zoomIn', 'zoomOut', 'toggle1to1'],
                    right: ['download', 'close']
                }
            }
        });
    };
    window.NetsukoTheme.initDevices();
</script>

<?php $this->need('footer.php'); ?>
