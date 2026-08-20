(function () {
    'use strict';

    var theme = window.NetsukoTheme = window.NetsukoTheme || {};
    var config = window.NetsukoPjaxConfig || {};
    var containerSelector = config.container || '#netsuko-pjax-container';
    var activeRequest = null;
    var loadedExternalScripts = {};
    var transitionTimer = null;
    var readingProgressFrame = null;
    var motionObserver = null;
    var fancyboxAssetsPromise = null;
    var highlightAssetsPromise = null;
    var katexAssetsPromise = null;
    var turnstileApiPromise = null;
    var currentPjaxHref = window.location.href;
    var drawerCloseTimer = null;
    var scrollStateFrame = null;
    var mottoRequestId = 0;
    var mottoTypingTimer = null;
    var watermarkDownloadReady = false;
    var lateLifecycleListeners = [];
    var postLightboxSelector = '.post-content a.netsuko-image-lightbox[data-fancybox]';

    function $(selector, root) {
        return (root || document).querySelector(selector);
    }

    function $all(selector, root) {
        return Array.prototype.slice.call((root || document).querySelectorAll(selector));
    }

    $all('script[src]').forEach(function (script) {
        loadedExternalScripts[script.src] = true;
    });

    function samePageUrl(left, right) {
        return left.origin === right.origin &&
            left.pathname.replace(/\/+$/, '') === right.pathname.replace(/\/+$/, '') &&
            left.search === right.search;
    }

    function normalizeNavigationPath(pathname) {
        var path = pathname.replace(/\/+$/, '') || '/';
        return path === '/index.php' ? '/' : path;
    }

    function sameNavigationUrl(left, right) {
        return left.origin === right.origin &&
            normalizeNavigationPath(left.pathname) === normalizeNavigationPath(right.pathname);
    }

    function getExcludeText(url) {
        return url.pathname + url.search + url.hash;
    }

    function isExcluded(url) {
        var excludes = Array.isArray(config.excludes) ? config.excludes : [];
        var text = getExcludeText(url);

        return excludes.some(function (item) {
            return item && text.indexOf(item) !== -1;
        });
    }

    function shouldHandleLink(link, event) {
        if (!config.enabled || !link || event.defaultPrevented) {
            return false;
        }

        var eventTarget = event.target;
        if (eventTarget && eventTarget.closest && eventTarget.closest('.fancybox__container, [data-fancybox-close], [data-fancybox-button], [data-panzoom-action], .f-button')) {
            return false;
        }

        if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return false;
        }

        if (link.target && link.target.toLowerCase() !== '_self') {
            return false;
        }

        if (link.hasAttribute('download') || link.closest('[data-no-pjax], [data-fancybox], .netsuko-image-lightbox')) {
            return false;
        }

        var rawHref = link.getAttribute('href') || '';
        if (!rawHref || rawHref.charAt(0) === '#' || /^(mailto:|tel:|javascript:)/i.test(rawHref)) {
            return false;
        }

        if (link.hasAttribute('data-fancybox') || link.classList.contains('netsuko-image-lightbox') || isImageHref(rawHref) || isImageHref(link.href)) {
            return false;
        }

        var url = new URL(link.href, window.location.href);
        if (url.origin !== window.location.origin || isExcluded(url)) {
            return false;
        }

        if (samePageUrl(url, new URL(window.location.href))) {
            return false;
        }

        return true;
    }

    function setLoading(isLoading) {
        ensureProgressIndicators();
        window.clearTimeout(transitionTimer);

        if (isLoading && document.documentElement.classList.contains('with-fancybox')) {
            return;
        }

        document.documentElement.classList.toggle('netsuko-pjax-loading', isLoading);
        document.body.classList.toggle('netsuko-pjax-loading', isLoading);

        if (isLoading) {
            document.documentElement.classList.remove('netsuko-pjax-complete', 'netsuko-page-enter');
            document.documentElement.dataset.netsukoPjaxState = 'loading';
            return;
        }

        document.documentElement.classList.add('netsuko-pjax-complete');
        document.documentElement.dataset.netsukoPjaxState = 'ready';
        transitionTimer = window.setTimeout(function () {
            document.documentElement.classList.remove('netsuko-pjax-complete');
        }, 520);
    }

    function watermarkConfig() {
        var value = config.watermark || {};
        return {
            enabled: value.enabled === true,
            text: String(value.text || '').trim() || 'Netsuko',
            position: ['bottom-right', 'bottom-left', 'top-right', 'top-left'].indexOf(value.position) !== -1 ? value.position : 'bottom-right',
            opacity: Math.max(0.08, Math.min(0.5, Number(value.opacity) || 0.2))
        };
    }

    function watermarkPosition(position) {
        return {
            'bottom-right': { x: 1, y: 1, anchor: 'end', baseline: 'ideographic' },
            'bottom-left': { x: 0, y: 1, anchor: 'start', baseline: 'ideographic' },
            'top-right': { x: 1, y: 0, anchor: 'end', baseline: 'hanging' },
            'top-left': { x: 0, y: 0, anchor: 'start', baseline: 'hanging' }
        }[position];
    }

    function makeWatermarkSvg(configValue) {
        var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        var text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        var position = watermarkPosition(configValue.position);
        svg.setAttribute('viewBox', '0 0 100 100');
        svg.setAttribute('preserveAspectRatio', 'none');
        svg.setAttribute('aria-hidden', 'true');
        svg.classList.add('netsuko-watermark-overlay');
        text.textContent = configValue.text;
        text.setAttribute('x', position.x ? '96' : '4');
        text.setAttribute('y', position.y ? '96' : '4');
        text.setAttribute('text-anchor', position.anchor);
        text.setAttribute('dominant-baseline', position.baseline);
        text.setAttribute('font-family', 'system-ui, sans-serif');
        text.setAttribute('font-size', '4');
        text.setAttribute('font-weight', '600');
        text.setAttribute('letter-spacing', '0');
        text.setAttribute('fill', '#ffffff');
        text.setAttribute('stroke', '#000000');
        text.setAttribute('stroke-width', '0.7');
        text.setAttribute('paint-order', 'stroke');
        text.setAttribute('opacity', String(configValue.opacity));
        svg.appendChild(text);
        return svg;
    }

    function watermarkTarget(image) {
        var link = image.closest('[data-fancybox]');
        if (link) {
            return link;
        }
        return image.parentElement && image.parentElement.tagName.toLowerCase() === 'picture'
            ? image.parentElement
            : image.parentElement;
    }

    theme.initWatermark = function (root) {
        var configValue = watermarkConfig();
        var scope = root || document;
        if (!configValue.enabled) {
            $all('.netsuko-watermark-overlay', scope).forEach(function (overlay) { overlay.remove(); });
            return;
        }

        $all('.post-content img, [data-fancybox] img', scope).forEach(function (image) {
            if (image.closest('[data-no-watermark], [data-no-lightbox]')) {
                return;
            }
            var target = watermarkTarget(image);
            if (!target || target.closest('.netsuko-watermark-skip')) {
                return;
            }
            if (target.dataset.netsukoWatermarkReady === 'true') {
                return;
            }
            target.classList.add('netsuko-watermark-target');
            target.dataset.netsukoWatermarkReady = 'true';
            target.appendChild(makeWatermarkSvg(configValue));
        });

        if (watermarkDownloadReady) {
            return;
        }
        watermarkDownloadReady = true;
        document.addEventListener('click', function (event) {
            var button = event.target && event.target.closest ? event.target.closest('[data-fancybox-download], .f-button[title="Download"], .f-button[aria-label="Download"]') : null;
            if (!button || !watermarkConfig().enabled) {
                return;
            }
            var instance = window.Fancybox && typeof window.Fancybox.getInstance === 'function' ? window.Fancybox.getInstance() : null;
            var slide = instance && instance.getSlide ? instance.getSlide() : null;
            var source = slide && (slide.src || (slide.triggerEl && slide.triggerEl.href));
            if (!source || !/^https?:/i.test(source)) {
                return;
            }
            event.preventDefault();
            event.stopImmediatePropagation();
            downloadWatermarkedImage(source, slide && slide.triggerEl, watermarkConfig());
        }, true);
    };

    function downloadOriginalImage(source, trigger) {
        var link = document.createElement('a');
        link.href = source;
        link.download = ((trigger && trigger.querySelector('img')?.alt) || 'netsuko-image') + '.png';
        link.rel = 'noopener';
        document.body.appendChild(link);
        link.click();
        link.remove();
    }

    function downloadWatermarkedImage(source, trigger, configValue) {
        var image = new Image();
        image.crossOrigin = 'anonymous';
        image.onload = function () {
            var canvas = document.createElement('canvas');
            var context = canvas.getContext('2d');
            var width = image.naturalWidth;
            var height = image.naturalHeight;
            var fontSize = Math.max(14, Math.round(Math.min(width, height) * 0.035));
            var pad = Math.max(12, Math.round(fontSize * 0.8));
            var position = watermarkPosition(configValue.position);
            canvas.width = width;
            canvas.height = height;
            context.drawImage(image, 0, 0, width, height);
            context.font = '600 ' + fontSize + 'px system-ui, sans-serif';
            context.textBaseline = position.baseline;
            context.textAlign = position.anchor;
            context.lineWidth = Math.max(2, fontSize * 0.16);
            context.strokeStyle = 'rgba(0, 0, 0, ' + Math.min(0.8, configValue.opacity * 2.5) + ')';
            context.fillStyle = 'rgba(255, 255, 255, ' + configValue.opacity + ')';
            var x = position.x ? width - pad : pad;
            var y = position.y ? height - pad : pad;
            context.strokeText(configValue.text, x, y);
            context.fillText(configValue.text, x, y);
            canvas.toBlob(function (blob) {
                if (!blob) {
                    downloadOriginalImage(source, trigger);
                    return;
                }
                var url = URL.createObjectURL(blob);
                var link = document.createElement('a');
                link.href = url;
                link.download = ((trigger && trigger.querySelector('img')?.alt) || 'netsuko-image') + '-watermarked.png';
                document.body.appendChild(link);
                link.click();
                link.remove();
                window.setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
            }, 'image/png');
        };
        image.onerror = function () {
            downloadOriginalImage(source, trigger);
        };
        image.src = source;
    }

    function resetProgressIndicators() {
        var pjaxProgress = $('#netsuko-pjax-progress');
        var readingProgress = $('#netsuko-reading-progress');

        if (pjaxProgress) {
            pjaxProgress.style.removeProperty('opacity');
            pjaxProgress.style.removeProperty('transform');
        }

        if (readingProgress) {
            var readingBar = $('span', readingProgress);
            if (readingBar) {
                readingBar.style.removeProperty('transform');
            }
        }

        document.documentElement.classList.remove('netsuko-pjax-loading', 'netsuko-pjax-complete');
        document.body.classList.remove('netsuko-pjax-loading');
        document.documentElement.dataset.netsukoPjaxState = 'ready';
    }

    function setFancyboxOpen(isOpen) {
        document.documentElement.classList.toggle('netsuko-fancybox-open', isOpen);
        document.body.classList.toggle('netsuko-fancybox-open', isOpen);

        if (isOpen) {
            window.clearTimeout(transitionTimer);
            resetProgressIndicators();
        } else {
            scheduleReadingProgress();
        }
    }

    function ensureProgressIndicators() {
        if (!$('#netsuko-pjax-progress')) {
            var pjaxProgress = document.createElement('div');
            pjaxProgress.id = 'netsuko-pjax-progress';
            pjaxProgress.setAttribute('aria-hidden', 'true');
            document.body.appendChild(pjaxProgress);
        }

        if (!$('#netsuko-reading-progress')) {
            var readingProgress = document.createElement('div');
            var readingProgressBar = document.createElement('span');
            readingProgress.id = 'netsuko-reading-progress';
            readingProgress.setAttribute('aria-hidden', 'true');
            readingProgress.appendChild(readingProgressBar);
            document.body.appendChild(readingProgress);
        }
    }

    function updateReadingProgress() {
        var bar = $('#netsuko-reading-progress span');
        var content = $('[data-netsuko-reading]');
        if (!bar || !content) {
            document.documentElement.classList.remove('netsuko-reading-active');
            if (bar) {
                bar.style.transform = 'scaleX(0)';
            }
            return;
        }

        var rect = content.getBoundingClientRect();
        var top = rect.top + window.scrollY;
        var start = Math.max(0, top - 64);
        var end = top + content.offsetHeight - window.innerHeight;
        var distance = Math.max(0, end - start);
        var progress = distance > 0 ? Math.min(1, Math.max(0, (window.scrollY - start) / distance)) : 0;
        var isActive = distance > 160;

        document.documentElement.classList.toggle('netsuko-reading-active', isActive);
        bar.style.transform = 'scaleX(' + progress.toFixed(4) + ')';
    }

    function scheduleReadingProgress() {
        if (readingProgressFrame) {
            window.cancelAnimationFrame(readingProgressFrame);
        }

        readingProgressFrame = window.requestAnimationFrame(function () {
            readingProgressFrame = null;
            updateReadingProgress();
        });
    }

    function restartPageEnter() {
        document.documentElement.classList.remove('netsuko-page-enter');
        void document.documentElement.offsetWidth;
        document.documentElement.classList.add('netsuko-page-enter');
    }

    function isInViewport(node) {
        var rect = node.getBoundingClientRect();
        return rect.bottom >= 0 && rect.top <= (window.innerHeight || document.documentElement.clientHeight);
    }

    function revealVisibleMotionTargets(targets) {
        targets.forEach(function (node) {
            if (!node.classList.contains('is-visible') && isInViewport(node)) {
                node.classList.add('is-visible');
            }
        });
    }

    function prepareMotionTargets(root) {
        var scope = root || document;
        var targets = $all([
            '#home-banner',
            '#main > div > article',
            '#main > aside',
            '#respond',
            '.pagination'
        ].join(','), scope);

        if (motionObserver) {
            motionObserver.disconnect();
        }

        motionObserver = 'IntersectionObserver' in window ? new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('is-visible');
                motionObserver.unobserve(entry.target);
            });
        }, { rootMargin: '0px 0px -8% 0px', threshold: 0 }) : null;

        targets.forEach(function (node, index) {
            node.dataset.netsukoMotion = 'true';
            node.style.setProperty('--netsuko-motion-delay', Math.min(index * 55, 220) + 'ms');

            if (isInViewport(node)) {
                node.classList.add('is-visible');
            }

            if (motionObserver) {
                if (!node.classList.contains('is-visible')) {
                    motionObserver.observe(node);
                }
            } else {
                node.classList.add('is-visible');
            }
        });

        window.setTimeout(function () {
            revealVisibleMotionTargets(targets);
        }, 120);
    }

    function initMotionEvents() {
        if (document.documentElement.dataset.netsukoMotionEventsReady === 'true') {
            return;
        }

        document.documentElement.dataset.netsukoMotionEventsReady = 'true';
        window.addEventListener('scroll', function () {
            scheduleReadingProgress();
            scheduleScrollStateSave();
        }, { passive: true });
        window.addEventListener('resize', scheduleReadingProgress);
    }

    function renderMotto(node, text, animate) {
        if (!node) {
            return;
        }

        var quote = node.dataset.mottoQuotes === 'show' ? '"' : '';
        var output = quote + String(text || '') + quote;
        window.clearInterval(mottoTypingTimer);
        node.classList.toggle('netsuko-motto-typing', Boolean(animate));

        if (!animate) {
            node.textContent = output;
            return;
        }

        node.textContent = '';
        var chars = Array.from(output);
        var index = 0;
        mottoTypingTimer = window.setInterval(function () {
            node.textContent += chars[index] || '';
            index += 1;
            if (index >= chars.length) {
                window.clearInterval(mottoTypingTimer);
                mottoTypingTimer = null;
                node.classList.remove('netsuko-motto-typing');
            }
        }, 72);
    }

    theme.initMotto = function (root) {
        var node = $('#home-motto', root || document);
        if (!node) {
            return;
        }

        var fallback = node.dataset.mottoText || node.textContent.replace(/^"|"$/g, '').trim();
        var animate = node.dataset.mottoTyping === 'on';
        var requestId = ++mottoRequestId;
        renderMotto(node, fallback, animate);

        if (node.dataset.mottoSource !== 'hitokoto') {
            return;
        }

        var endpoint = config.motto && config.motto.endpoint;
        if (!endpoint || typeof window.fetch !== 'function') {
            return;
        }

        var controller = typeof window.AbortController === 'function' ? new AbortController() : null;
        var timeout = window.setTimeout(function () {
            if (controller) {
                controller.abort();
            }
        }, 4200);

        window.fetch(endpoint, {
            headers: { 'Accept': 'application/json' },
            cache: 'no-store',
            signal: controller ? controller.signal : undefined
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('Hitokoto request failed');
            }
            return response.json();
        }).then(function (data) {
            var text = data && typeof data.hitokoto === 'string' ? data.hitokoto.trim() : '';
            if (!text || requestId !== mottoRequestId || !node.isConnected) {
                return;
            }
            node.title = data.from ? '来源：' + data.from : '来源：一言';
            renderMotto(node, text, animate);
        }).catch(function () {
            // Keep the server-rendered fallback when the API is unavailable.
        }).finally(function () {
            window.clearTimeout(timeout);
        });
    };

    function withAssetVersion(url) {
        if (!url || !config.assetVersion || /[?&]v=/.test(url)) {
            return url;
        }

        return url + (url.indexOf('?') === -1 ? '?' : '&') + 'v=' + encodeURIComponent(config.assetVersion);
    }

    function ensureStylesheet(url) {
        if (!url) {
            return Promise.resolve();
        }

        var href = withAssetVersion(url);
        var exists = $all('link[rel="stylesheet"]').some(function (link) {
            return link.href === href || link.href === url;
        });

        if (exists) {
            return Promise.resolve();
        }

        return new Promise(function (resolve) {
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = href;
            link.dataset.netsukoAsset = 'true';
            link.onload = resolve;
            link.onerror = resolve;
            document.head.appendChild(link);
        });
    }

    function ensureScript(url, test) {
        if (!url || (typeof test === 'function' && test())) {
            return Promise.resolve();
        }

        var src = withAssetVersion(url);
        var existing = $all('script[src]').filter(function (script) {
            return script.src === src || script.src === url;
        })[0];

        if (existing) {
            return new Promise(function (resolve) {
                if (typeof test === 'function' && test()) {
                    resolve();
                    return;
                }

                existing.addEventListener('load', resolve, { once: true });
                existing.addEventListener('error', resolve, { once: true });
                window.setTimeout(resolve, 1200);
            });
        }

        return new Promise(function (resolve) {
            var script = document.createElement('script');
            script.src = src;
            script.async = true;
            script.dataset.netsukoAsset = 'true';
            script.onload = resolve;
            script.onerror = resolve;
            document.body.appendChild(script);
        });
    }

    function ensureFancyboxAssets() {
        if (window.Fancybox) {
            return Promise.resolve();
        }

        if (fancyboxAssetsPromise) {
            return fancyboxAssetsPromise;
        }

        var assets = config.fancybox || {};
        ensureStylesheet(assets.css);
        fancyboxAssetsPromise = ensureScript(assets.js, function () {
            return !!window.Fancybox;
        }).then(function () {
            return window.Fancybox || null;
        });

        return fancyboxAssetsPromise;
    }

    function ensureHighlightAssets() {
        var content = config.content || {};
        var assets = content.highlight || {};
        if (!assets.enabled) {
            return Promise.resolve(null);
        }

        if (window.hljs) {
            return Promise.resolve(window.hljs);
        }

        if (highlightAssetsPromise) {
            return highlightAssetsPromise;
        }

        highlightAssetsPromise = ensureScript(assets.js, function () {
            return !!window.hljs;
        }).then(function () {
            return window.hljs || null;
        });

        return highlightAssetsPromise;
    }

    function ensureKatexAssets() {
        var content = config.content || {};
        var assets = content.latex || {};

        if (window.katex && window.renderMathInElement) {
            return Promise.resolve(window.katex);
        }

        if (katexAssetsPromise) {
            return katexAssetsPromise;
        }

        katexAssetsPromise = ensureStylesheet(assets.css)
            .then(function () {
                return ensureScript(assets.js, function () {
                    return !!window.katex;
                });
            })
            .then(function () {
                return ensureScript(assets.autoRenderJs, function () {
                    return !!window.renderMathInElement;
                });
            })
            .then(function () {
                return window.katex || null;
            });

        return katexAssetsPromise;
    }

    function imageUrlFromElement(image) {
        return image.currentSrc || image.getAttribute('src') || image.src || '';
    }

    function isImageHref(href) {
        return /^data:image\//i.test(href) ||
            /\.(?:avif|gif|jpe?g|png|webp|bmp|svg)(?:[?#].*)?$/i.test(href);
    }

    function imageCaption(image) {
        var figureCaption = image.closest('figure') ? $('figcaption', image.closest('figure')) : null;
        return image.getAttribute('data-caption') ||
            image.getAttribute('alt') ||
            image.getAttribute('title') ||
            (figureCaption ? figureCaption.textContent.trim() : '');
    }

    function preparePostLightbox(root) {
        var scope = root || document;
        var images = $all('.post-content img', scope);
        var prepared = 0;

        images.forEach(function (image) {
            if (image.dataset.netsukoLightboxReady === 'true' || image.closest('[data-no-lightbox]')) {
                return;
            }

            var href = imageUrlFromElement(image);
            if (!href) {
                return;
            }

            var mediaNode = image.parentElement && image.parentElement.tagName.toLowerCase() === 'picture'
                ? image.parentElement
                : image;
            var parentNode = mediaNode.parentNode;
            var link = parentNode && parentNode.tagName && parentNode.tagName.toLowerCase() === 'a'
                ? parentNode
                : null;

            if (link && !isImageHref(link.href) && !link.hasAttribute('data-fancybox')) {
                return;
            }

            if (!link) {
                link = document.createElement('a');
                link.href = href;
                parentNode.insertBefore(link, mediaNode);
                link.appendChild(mediaNode);
            }

            link.href = link.href || href;
            link.dataset.fancybox = link.dataset.fancybox || 'post-images';
            link.dataset.noPjax = 'true';
            link.dataset.caption = link.dataset.caption || imageCaption(image);
            link.classList.add('netsuko-image-lightbox');
            link.setAttribute('aria-label', link.dataset.caption || image.getAttribute('alt') || 'View full-size image');
            image.dataset.netsukoLightboxReady = 'true';
            prepared++;
        });

        return prepared;
    }

    theme.initPostLightbox = function (root) {
        if (!preparePostLightbox(root || document)) {
            return;
        }

        ensureFancyboxAssets().then(function () {
            if (!window.Fancybox || typeof window.Fancybox.bind !== 'function') {
                return;
            }

            if (typeof window.Fancybox.unbind === 'function') {
                window.Fancybox.unbind(postLightboxSelector);
            }

            window.Fancybox.bind(postLightboxSelector, {
                contentClick: 'toggleZoom',
                backdropClick: 'close',
                on: {
                    init: function () {
                        setFancyboxOpen(true);
                    },
                    destroy: function () {
                        setFancyboxOpen(false);
                    }
                },
                Toolbar: {
                    display: {
                        left: ['infobar'],
                        middle: ['zoomIn', 'zoomOut', 'toggle1to1'],
                        right: ['download', 'close']
                    }
                }
            });
        });
    };

    theme.initLazyLoad = function (root) {
        var content = config.content || {};
        if (!content.lazyLoad) {
            return;
        }

        $all('.post-content img, .post-content iframe', root || document).forEach(function (media) {
            if (media.closest('[data-no-lazy]')) {
                return;
            }

            if (!media.hasAttribute('loading')) {
                media.setAttribute('loading', 'lazy');
            }

            if (media.tagName && media.tagName.toLowerCase() === 'img' && !media.hasAttribute('decoding')) {
                media.setAttribute('decoding', 'async');
            }

            media.classList.add('netsuko-lazy-media');

            if (media.complete || media.tagName.toLowerCase() === 'iframe') {
                media.classList.add('is-loaded');
                media.classList.remove('is-loading');
                return;
            }

            if (media.dataset.netsukoLazyReady === 'true') {
                return;
            }

            media.dataset.netsukoLazyReady = 'true';
            media.classList.add('is-loading');
            media.addEventListener('load', function () {
                media.classList.add('is-loaded');
                media.classList.remove('is-loading');
            }, { once: true });
            media.addEventListener('error', function () {
                media.classList.add('is-loaded');
                media.classList.remove('is-loading');
            }, { once: true });
        });
    };

    function copyTextFallback(text) {
        var textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.setAttribute('readonly', 'readonly');
        textarea.style.position = 'fixed';
        textarea.style.top = '-9999px';
        textarea.style.left = '-9999px';
        document.body.appendChild(textarea);
        textarea.select();

        var ok = false;
        try {
            ok = document.execCommand('copy');
        } catch (error) {
            ok = false;
        }

        document.body.removeChild(textarea);
        return ok;
    }

    function addCodeCopyButton(wrapper, code) {
        if (wrapper.dataset.netsukoCodeCopyReady === 'true') {
            return;
        }

        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'netsuko-code-copy';
        button.textContent = '复制';
        button.setAttribute('aria-label', '复制代码');

        button.addEventListener('click', function () {
            var text = code.textContent || '';
            var done = function (ok) {
                button.textContent = ok ? '已复制' : '失败';
                window.setTimeout(function () {
                    button.textContent = '复制';
                }, 1400);
            };

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function () {
                    done(true);
                }).catch(function () {
                    done(false);
                });
                return;
            }

            done(copyTextFallback(text));
        });

        wrapper.dataset.netsukoCodeCopyReady = 'true';
        wrapper.appendChild(button);
    }

    theme.initCodeHighlight = function (root) {
        var content = config.content || {};
        var assets = content.highlight || {};
        if (!assets.enabled) {
            return;
        }

        var blocks = $all('.post-content pre code', root || document);
        if (!blocks.length) {
            return;
        }

        ensureHighlightAssets().then(function (hljs) {
            blocks.forEach(function (code) {
                var pre = code.closest('pre');
                if (!pre) {
                    return;
                }

                pre.classList.add('netsuko-code-block');

                if (hljs && code.dataset.netsukoHighlighted !== 'true') {
                    try {
                        hljs.highlightElement(code);
                        code.dataset.netsukoHighlighted = 'true';
                    } catch (error) {
                        code.dataset.netsukoHighlighted = 'failed';
                    }
                }

                addCodeCopyButton(pre, code);
            });
        });
    };

    theme.initLatex = function (root) {
        var scope = root || document;
        var targets = $all('.post-content[data-netsuko-latex="on"]', scope);
        if (!targets.length && scope.matches && scope.matches('.post-content[data-netsuko-latex="on"]')) {
            targets = [scope];
        }

        if (!targets.length) {
            return;
        }

        ensureKatexAssets().then(function () {
            if (!window.renderMathInElement) {
                return;
            }

            targets.forEach(function (target) {
                if (target.dataset.netsukoLatexReady === 'true') {
                    return;
                }

                window.renderMathInElement(target, {
                    delimiters: [
                        { left: '$$', right: '$$', display: true },
                        { left: '\\[', right: '\\]', display: true },
                        { left: '\\(', right: '\\)', display: false },
                        { left: '$', right: '$', display: false }
                    ],
                    ignoredTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code'],
                    throwOnError: false
                });
                target.dataset.netsukoLatexReady = 'true';
            });
        });
    };

    function closeDrawer() {
        var drawer = $('#mobile-drawer');
        var overlay = $('#drawer-overlay');
        var panel = $('#drawer-panel');
        if (!drawer || !overlay || !panel) {
            return;
        }

        overlay.classList.add('opacity-0');
        panel.classList.add('translate-x-full');
        document.body.style.overflow = '';
        window.clearTimeout(drawerCloseTimer);
        drawerCloseTimer = window.setTimeout(function () {
            drawer.classList.add('hidden');
            drawerCloseTimer = null;
        }, 300);
    }

    theme.initMobileDrawer = function () {
        var drawer = $('#mobile-drawer');
        var overlay = $('#drawer-overlay');
        var panel = $('#drawer-panel');
        var openBtn = $('#mobile-menu-open');
        var closeBtn = $('#mobile-menu-close');
        if (!drawer || !overlay || !panel || !openBtn || !closeBtn) {
            return;
        }

        if (openBtn.dataset.netsukoDrawerReady === 'true') {
            return;
        }

        openBtn.dataset.netsukoDrawerReady = 'true';
        closeBtn.dataset.netsukoDrawerReady = 'true';
        overlay.dataset.netsukoDrawerReady = 'true';

        openBtn.addEventListener('click', function () {
            window.clearTimeout(drawerCloseTimer);
            drawerCloseTimer = null;
            drawer.classList.remove('hidden');
            void drawer.offsetWidth;
            overlay.classList.remove('opacity-0');
            panel.classList.remove('translate-x-full');
            document.body.style.overflow = 'hidden';
        });
        closeBtn.addEventListener('click', closeDrawer);
        overlay.addEventListener('click', closeDrawer);
    };

    theme.initBackToTop = function () {
        var button = $('#back-to-top');
        if (!button || button.dataset.netsukoBackTopReady === 'true') {
            return;
        }

        var update = function () {
            if (window.scrollY > 400) {
                button.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-5');
                button.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');
            } else {
                button.classList.add('opacity-0', 'pointer-events-none', 'translate-y-5');
                button.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
            }
        };

        button.dataset.netsukoBackTopReady = 'true';
        window.addEventListener('scroll', update, { passive: true });
        button.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        update();
    };

    function removeTurnstileWidget(node) {
        var widgetId = node.dataset.netsukoTurnstileWidgetId || '';

        if (widgetId && window.turnstile && typeof window.turnstile.remove === 'function') {
            try {
                window.turnstile.remove(widgetId);
            } catch (error) {
                // Clear the host even when Turnstile has already discarded the widget.
            }
        }

        node.removeAttribute('data-netsuko-turnstile-widget-id');
        node.removeAttribute('data-netsuko-turnstile-ready');
        node.innerHTML = '';
        return true;
    }

    function renderTurnstile(root) {
        if (!window.turnstile || typeof window.turnstile.render !== 'function') {
            return;
        }

        $all('.cf-turnstile', root || document).forEach(function (node) {
            if (node.dataset.netsukoTurnstileWidgetId) {
                return;
            }

            try {
                var widgetId = window.turnstile.render(node, {
                    sitekey: node.dataset.sitekey,
                    theme: node.dataset.theme || 'auto'
                });
                node.dataset.netsukoTurnstileWidgetId = widgetId;
                node.dataset.netsukoTurnstileReady = 'true';
            } catch (error) {
                node.removeAttribute('data-netsuko-turnstile-widget-id');
                node.removeAttribute('data-netsuko-turnstile-ready');
            }
        });
    }

    function ensureTurnstileApi(root) {
        if (window.turnstile && typeof window.turnstile.render === 'function') {
            renderTurnstile(root);
            return;
        }

        if (!turnstileApiPromise) {
            window.netsukoTurnstileLoaded = function () {
                renderTurnstile(document);
            };
            turnstileApiPromise = ensureScript(
                'https://challenges.cloudflare.com/turnstile/v0/api.js?onload=netsukoTurnstileLoaded&render=explicit',
                function () {
                    return !!window.turnstile;
                }
            );
        }

        turnstileApiPromise.then(function () {
            renderTurnstile(root);
        });
    }

    function wrapCommentReplyForTurnstile(methodName) {
        if (!window.TypechoComment || typeof window.TypechoComment[methodName] !== 'function') {
            return;
        }

        var original = window.TypechoComment[methodName];
        if (original.netsukoTurnstileWrapped === true) {
            return;
        }

        var wrapped = function () {
            $all('.cf-turnstile').forEach(removeTurnstileWidget);
            var result = original.apply(this, arguments);
            window.setTimeout(function () {
                theme.initTurnstile(document);
            }, 0);
            return result;
        };
        wrapped.netsukoTurnstileWrapped = true;
        window.TypechoComment[methodName] = wrapped;
    }

    theme.initTurnstile = function (root) {
        wrapCommentReplyForTurnstile('reply');
        wrapCommentReplyForTurnstile('cancelReply');

        var scope = root || document;
        if (!$('.cf-turnstile', scope)) {
            return;
        }

        ensureTurnstileApi(scope);
    };

    function runLifecycle(root, currentHref) {
        theme.initMobileDrawer();
        theme.initBackToTop();
        theme.initSidebar(root);
        if (typeof theme.initCommentForm === 'function') {
            theme.initCommentForm();
        }
        if (typeof theme.initGallery === 'function') {
            theme.initGallery();
        }
        if (typeof theme.initDevices === 'function') {
            theme.initDevices();
        }
        if (typeof theme.initMotto === 'function') {
            theme.initMotto(root);
        }
        if (typeof theme.initBangumi === 'function') {
            theme.initBangumi(root);
        }
        theme.initLazyLoad(root);
        theme.initCodeHighlight(root);
        theme.initLatex(root);
        theme.initPostLightbox(root);
        theme.initWatermark(root);
        theme.initTurnstile(root);
        theme.initMotion(root);
        updateActiveNavigation(currentHref);
        document.dispatchEvent(new CustomEvent('netsuko:pjax:ready', { detail: { root: root || document, url: currentHref || window.location.href } }));
    }

    theme.initSidebar = function (root) {
        var scope = root || document;
        var sidebars = $all('.netsuko-sidebar', scope);
        if (!sidebars.length || window.innerWidth < 768) {
            return;
        }

        sidebars.forEach(function (sidebar) {
            var modules = $all('[data-sidebar-module]', sidebar);
            var mainColumn = document.querySelector('#main > div:first-child');
            if (!modules.length || !mainColumn) {
                return;
            }

            modules.forEach(function (module) {
                module.style.display = '';
            });

            // The server-side limit keeps the module count predictable. This pass
            // removes the first card that would extend past the article column.
            var mainBottom = mainColumn.getBoundingClientRect().bottom + window.scrollY;
            var hidden = false;
            modules.forEach(function (module) {
                if (hidden) {
                    module.style.display = 'none';
                    return;
                }
                var moduleBottom = module.getBoundingClientRect().bottom + window.scrollY;
                if (moduleBottom > mainBottom + 2) {
                    module.style.display = 'none';
                    hidden = true;
                }
            });
        });
    };

    var sidebarResizeTimer = null;
    window.addEventListener('resize', function () {
        if (sidebarResizeTimer) {
            window.clearTimeout(sidebarResizeTimer);
        }
        sidebarResizeTimer = window.setTimeout(function () {
            if (window.scrollY < 4) {
                theme.initSidebar(document);
            }
        }, 120);
    });

    theme.initMotion = function (root) {
        ensureProgressIndicators();
        initMotionEvents();
        restartPageEnter();
        prepareMotionTargets(root || document);
        scheduleReadingProgress();
    };

    function copyHeadAssets(newDocument) {
        var selector = 'link[rel="stylesheet"], style';
        $all(selector, newDocument.head).forEach(function (node) {
            var key = node.tagName.toLowerCase() + ':' + (node.getAttribute('href') || node.textContent);
            var exists = $all(selector, document.head).some(function (current) {
                return key === current.tagName.toLowerCase() + ':' + (current.getAttribute('href') || current.textContent);
            });

            if (!exists) {
                document.head.appendChild(document.importNode(node, true));
            }
        });
    }

    function executeHeadScripts(newDocument, isCurrentNavigation) {
        var chain = Promise.resolve();
        $all('script', newDocument.head).forEach(function (script) {
            chain = chain.then(function () {
                if (!isCurrentNavigation()) {
                    throw new DOMException('Navigation aborted', 'AbortError');
                }
                return executeScript(script);
            });
        });

        return chain;
    }

    function clearLateLifecycleListeners() {
        lateLifecycleListeners.forEach(function (item) {
            item.target.removeEventListener(item.type, item.listener, item.options);
        });
        lateLifecycleListeners = [];
    }

    function invokeLateDomReadyListener(listener) {
        var originalWindowAdd = window.addEventListener;
        var originalDocumentAdd = document.addEventListener;
        var track = function (target, originalAdd, type, callback, options) {
            originalAdd.call(target, type, callback, options);
            lateLifecycleListeners.push({
                target: target,
                type: type,
                listener: callback,
                options: options
            });
        };

        window.addEventListener = function (type, callback, options) {
            track(window, originalWindowAdd, type, callback, options);
        };
        document.addEventListener = function (type, callback, options) {
            track(document, originalDocumentAdd, type, callback, options);
        };

        try {
            var event = new Event('DOMContentLoaded');
            if (typeof listener === 'function') {
                listener.call(document, event);
            } else if (listener && typeof listener.handleEvent === 'function') {
                listener.handleEvent(event);
            }
        } finally {
            window.addEventListener = originalWindowAdd;
            document.addEventListener = originalDocumentAdd;
        }
    }

    function executeScript(script) {
        return new Promise(function (resolve) {
            var fresh = document.createElement('script');

            Array.prototype.slice.call(script.attributes).forEach(function (attr) {
                fresh.setAttribute(attr.name, attr.value);
            });

            if (script.src) {
                if (loadedExternalScripts[script.src]) {
                    resolve();
                    return;
                }

                loadedExternalScripts[script.src] = true;
                fresh.async = false;
                fresh.onload = resolve;
                fresh.onerror = resolve;
                fresh.src = script.src;
                document.body.appendChild(fresh);
                return;
            }

            var originalDocumentAdd = document.addEventListener;
            document.addEventListener = function (type, listener, options) {
                if (type === 'DOMContentLoaded') {
                    invokeLateDomReadyListener(listener);
                    return;
                }
                originalDocumentAdd.call(document, type, listener, options);
            };

            try {
                fresh.text = script.textContent;
                document.body.appendChild(fresh);
                document.body.removeChild(fresh);
            } finally {
                document.addEventListener = originalDocumentAdd;
            }
            resolve();
        });
    }

    function executeContainerScripts(container, isCurrentNavigation) {
        var chain = Promise.resolve();
        $all('script', container).forEach(function (script) {
            chain = chain.then(function () {
                if (!isCurrentNavigation()) {
                    throw new DOMException('Navigation aborted', 'AbortError');
                }
                return executeScript(script);
            });
        });

        return chain;
    }

    function updateActiveNavigation(currentHref) {
        var current = new URL(currentHref || window.location.href, window.location.href);
        $all('#header nav a, #mobile-drawer nav a').forEach(function (link) {
            var url = new URL(link.href, window.location.href);
            var isActive = sameNavigationUrl(url, current);
            link.classList.toggle('text-teal', isActive);
            link.classList.toggle('border-teal', isActive && link.closest('#mobile-drawer'));
            link.classList.toggle('dark:border-teal', isActive && link.closest('#mobile-drawer'));
            link.classList.toggle('netsuko-nav-active', isActive);
            if (isActive) {
                link.setAttribute('aria-current', 'page');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    }

    function updateDocument(newDocument, currentHref, isCurrentNavigation) {
        var oldContainer = $(containerSelector);
        var newContainer = $(containerSelector, newDocument);
        if (!oldContainer || !newContainer) {
            return Promise.reject(new Error('PJAX container is missing'));
        }
        if (!isCurrentNavigation()) {
            return Promise.reject(new DOMException('Navigation aborted', 'AbortError'));
        }

        document.title = newDocument.title;
        copyHeadAssets(newDocument);
        clearLateLifecycleListeners();
        oldContainer.innerHTML = newContainer.innerHTML;

        return executeHeadScripts(newDocument, isCurrentNavigation).then(function () {
            if (!isCurrentNavigation()) {
                throw new DOMException('Navigation aborted', 'AbortError');
            }
            return executeContainerScripts(oldContainer, isCurrentNavigation);
        }).then(function () {
            if (!isCurrentNavigation()) {
                throw new DOMException('Navigation aborted', 'AbortError');
            }
            runLifecycle(oldContainer, currentHref);
        });
    }

    function saveCurrentScrollPosition() {
        if (!config.enabled || !window.history.replaceState) {
            return;
        }

        var state = Object.assign({}, window.history.state || {}, {
            netsukoPjax: true,
            scrollX: window.scrollX,
            scrollY: window.scrollY
        });
        window.history.replaceState(state, '', window.location.href);
    }

    function scheduleScrollStateSave() {
        if (scrollStateFrame) {
            return;
        }

        scrollStateFrame = window.requestAnimationFrame(function () {
            scrollStateFrame = null;
            saveCurrentScrollPosition();
        });
    }

    function scrollAfterNavigation(url, position) {
        if (position && Number.isFinite(position.scrollY)) {
            window.scrollTo({
                left: Number.isFinite(position.scrollX) ? position.scrollX : 0,
                top: position.scrollY,
                behavior: 'auto'
            });
            return;
        }

        if (url.hash) {
            var target = document.getElementById(decodeURIComponent(url.hash.slice(1)));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                return;
            }
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function navigate(href, options) {
        var url = new URL(href, window.location.href);
        var request;
        options = options || {};

        if (!options.fromPopState) {
            saveCurrentScrollPosition();
        }
        closeDrawer();
        setLoading(true);
        document.dispatchEvent(new CustomEvent('netsuko:pjax:start', { detail: { url: url.href } }));

        if (activeRequest) {
            activeRequest.abort();
        }

        request = new AbortController();
        activeRequest = request;
        return fetch(url.href, {
            credentials: 'same-origin',
            signal: request.signal,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-Netsuko-PJAX': 'true'
            }
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('Unexpected response: ' + response.status);
            }
            return response.text();
        }).then(function (html) {
            if (request.signal.aborted || activeRequest !== request) {
                throw new DOMException('Navigation aborted', 'AbortError');
            }
            var newDocument = new DOMParser().parseFromString(html, 'text/html');
            return updateDocument(newDocument, url.href, function () {
                return !request.signal.aborted && activeRequest === request;
            });
        }).then(function () {
            if (request.signal.aborted || activeRequest !== request) {
                throw new DOMException('Navigation aborted', 'AbortError');
            }
            if (!options.fromPopState) {
                window.history.pushState({ netsukoPjax: true, scrollX: 0, scrollY: 0 }, '', url.href);
            }
            currentPjaxHref = url.href;
            updateActiveNavigation(url.href);
            scrollAfterNavigation(url, options.scrollPosition);
            document.dispatchEvent(new CustomEvent('netsuko:pjax:complete', { detail: { url: url.href } }));
        }).catch(function (error) {
            if (error.name === 'AbortError') {
                return;
            }
            window.location.href = url.href;
        }).finally(function () {
            if (activeRequest === request) {
                setLoading(false);
                activeRequest = null;
            }
        });
    }

    function initPjax() {
        if (!config.enabled || document.documentElement.dataset.netsukoPjaxReady === 'true') {
            return;
        }

        document.documentElement.dataset.netsukoPjaxReady = 'true';
        if ('scrollRestoration' in window.history) {
            window.history.scrollRestoration = 'manual';
        }
        window.history.replaceState({
            netsukoPjax: true,
            scrollX: window.scrollX,
            scrollY: window.scrollY
        }, '', window.location.href);

        document.addEventListener('click', function (event) {
            var link = event.target.closest('a');
            if (!shouldHandleLink(link, event)) {
                return;
            }

            event.preventDefault();
            updateActiveNavigation(link.href);
            navigate(link.href);
        });

        window.addEventListener('popstate', function (event) {
            var nextUrl = new URL(window.location.href, window.location.href);
            var previousUrl = new URL(currentPjaxHref, window.location.href);

            if (samePageUrl(nextUrl, previousUrl)) {
                currentPjaxHref = nextUrl.href;
                updateActiveNavigation(nextUrl.href);
                if (nextUrl.hash) {
                    scrollAfterNavigation(nextUrl, null);
                } else if (event.state && event.state.netsukoPjax) {
                    scrollAfterNavigation(nextUrl, event.state);
                }
                return;
            }

            navigate(window.location.href, {
                fromPopState: true,
                scrollPosition: event.state && event.state.netsukoPjax ? event.state : null
            });
        });

    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            runLifecycle(document);
            initPjax();
        });
    } else {
        runLifecycle(document);
        initPjax();
    }
})();
