<?php
/**
 * 追番
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
$bangumi = netsukoBangumiPageData($this);
$profile = $bangumi['profile'];
$groups = $bangumi['groups'];
$activeKey = 'doing';
foreach ($groups as $group) {
    if (!empty($group['items'])) {
        $activeKey = $group['key'];
        break;
    }
}
$fallbackCover = netsukoThemeAssetUrl('img/bg_watermark.jpg');
?>

<main class="bangumi-page flex-grow w-full max-w-6xl mx-auto px-4 sm:px-6 py-12 md:py-20 z-10 relative">
    <header class="bangumi-hero">
        <div class="bangumi-profile">
            <?php if (!empty($profile['avatar'])): ?>
                <img src="<?php echo netsukoEscape($profile['avatar']); ?>" alt="<?php echo netsukoEscape($profile['nickname'] ?? $bangumi['username']); ?>" loading="lazy" decoding="async">
            <?php else: ?>
                <span class="bangumi-avatar-fallback" aria-hidden="true">BG</span>
            <?php endif; ?>
            <div>
                <p class="bangumi-kicker">Bangumi collection</p>
                <h1><?php $this->title(); ?></h1>
                <?php if (!empty($profile['sign'])): ?>
                    <p class="bangumi-sign"><?php echo netsukoEscape($profile['sign']); ?></p>
                <?php elseif ($this->fields->subtitle): ?>
                    <p class="bangumi-sign"><?php echo netsukoEscape($this->fields->subtitle); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($profile['url'])): ?>
            <a class="bangumi-profile-link" href="<?php echo netsukoEscape($profile['url']); ?>" target="_blank" rel="noopener noreferrer">
                <span><?php echo netsukoEscape($profile['nickname'] ?? $bangumi['username']); ?></span>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 5h5v5m0-5-8 8M19 13v5a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        <?php endif; ?>
    </header>

    <?php if ($bangumi['error']): ?>
        <div class="bangumi-notice <?php echo $bangumi['stale'] ? 'is-warning' : 'is-error'; ?>">
            <?php echo netsukoEscape($bangumi['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($groups)): ?>
        <div class="bangumi-toolbar">
            <div class="bangumi-tabs" role="tablist" aria-label="追番状态">
                <?php foreach ($groups as $group): ?>
                    <button
                        type="button"
                        role="tab"
                        id="bangumi-tab-<?php echo netsukoEscape($group['key']); ?>"
                        aria-controls="bangumi-panel-<?php echo netsukoEscape($group['key']); ?>"
                        aria-selected="<?php echo $group['key'] === $activeKey ? 'true' : 'false'; ?>"
                        data-bangumi-tab="<?php echo netsukoEscape($group['key']); ?>"
                    >
                        <span><?php echo netsukoEscape($group['label']); ?></span>
                        <strong><?php echo (int) $group['total']; ?></strong>
                    </button>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($bangumi['fetchedAt'])): ?>
                <time datetime="<?php echo date('c', (int) $bangumi['fetchedAt']); ?>">
                    更新于 <?php echo date('Y-m-d H:i', (int) $bangumi['fetchedAt']); ?>
                </time>
            <?php endif; ?>
        </div>

        <div class="bangumi-panels">
            <?php foreach ($groups as $group): ?>
                <section
                    class="bangumi-panel"
                    id="bangumi-panel-<?php echo netsukoEscape($group['key']); ?>"
                    role="tabpanel"
                    aria-labelledby="bangumi-tab-<?php echo netsukoEscape($group['key']); ?>"
                    data-bangumi-panel="<?php echo netsukoEscape($group['key']); ?>"
                    <?php if ($group['key'] !== $activeKey): ?>hidden<?php endif; ?>
                >
                    <?php if (!empty($group['items'])): ?>
                        <div class="bangumi-grid">
                            <?php foreach ($group['items'] as $item): ?>
                                <?php
                                $cover = $item['image'] !== '' ? $item['image'] : $fallbackCover;
                                $progressText = $item['totalEpisodes'] > 0
                                    ? $item['watched'] . ' / ' . $item['totalEpisodes']
                                    : ($item['watched'] > 0 ? '已看 ' . $item['watched'] . ' 话' : '进度未记录');
                                ?>
                                <article class="bangumi-card">
                                    <a class="bangumi-cover" href="<?php echo netsukoEscape($item['url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="查看 <?php echo netsukoEscape($item['title']); ?>">
                                        <img src="<?php echo netsukoEscape($cover); ?>" alt="<?php echo netsukoEscape($item['title']); ?>" loading="lazy" decoding="async">
                                        <?php if ($item['score'] > 0): ?>
                                            <span><?php echo number_format($item['score'], 1); ?></span>
                                        <?php endif; ?>
                                    </a>

                                    <div class="bangumi-card-body">
                                        <div class="bangumi-card-heading">
                                            <div>
                                                <h2><a href="<?php echo netsukoEscape($item['url']); ?>" target="_blank" rel="noopener noreferrer"><?php echo netsukoEscape($item['title']); ?></a></h2>
                                                <?php if ($item['originalTitle']): ?>
                                                    <p><?php echo netsukoEscape($item['originalTitle']); ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ($item['rate'] > 0): ?>
                                                <strong class="bangumi-user-rate"><?php echo (int) $item['rate']; ?></strong>
                                            <?php endif; ?>
                                        </div>

                                        <div class="bangumi-progress-row">
                                            <span><?php echo netsukoEscape($progressText); ?></span>
                                            <?php if ($item['date']): ?><time><?php echo netsukoEscape(substr($item['date'], 0, 4)); ?></time><?php endif; ?>
                                        </div>
                                        <div class="bangumi-progress" aria-hidden="true"><span style="width: <?php echo (int) $item['progress']; ?>%"></span></div>

                                        <?php if ($item['comment']): ?>
                                            <p class="bangumi-comment"><?php echo netsukoEscape($item['comment']); ?></p>
                                        <?php elseif ($item['summary']): ?>
                                            <p class="bangumi-summary"><?php echo netsukoEscape($item['summary']); ?></p>
                                        <?php endif; ?>

                                        <?php if (!empty($item['tags'])): ?>
                                            <div class="bangumi-tags">
                                                <?php foreach ($item['tags'] as $tag): ?><span><?php echo netsukoEscape($tag); ?></span><?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="bangumi-empty">这个状态下暂时没有公开动画收藏。</div>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php $this->need('comments.php'); ?>
</main>

<script data-netsuko-rerun="true">
    window.NetsukoTheme = window.NetsukoTheme || {};
    window.NetsukoTheme.initBangumi = function (root) {
        var pages = Array.prototype.slice.call((root || document).querySelectorAll('.bangumi-page'));
        pages.forEach(function (page) {
            if (page.dataset.netsukoBangumiReady === 'true') {
                return;
            }

            var tabs = Array.prototype.slice.call(page.querySelectorAll('[data-bangumi-tab]'));
            var panels = Array.prototype.slice.call(page.querySelectorAll('[data-bangumi-panel]'));
            if (!tabs.length || !panels.length) {
                return;
            }

            function activate(key) {
                tabs.forEach(function (tab) {
                    tab.setAttribute('aria-selected', tab.dataset.bangumiTab === key ? 'true' : 'false');
                });
                panels.forEach(function (panel) {
                    panel.hidden = panel.dataset.bangumiPanel !== key;
                });
            }

            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    activate(tab.dataset.bangumiTab);
                });
            });
            page.dataset.netsukoBangumiReady = 'true';
        });
    };
    window.NetsukoTheme.initBangumi(document);
</script>

<?php $this->need('footer.php'); ?>
