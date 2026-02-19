<?php
/*
Plugin Name: Машина на времето - (BG + EN) - Sidebar
Description: Най-важните събития след Христа от българската и световната Wikipedia [mashina_vreme]. Редактирал и приспособил за WORDPRESS - Крум Крумов.
Version: 6.9
Author: Крум Крумов; Стефан Георгиев
*/

if (!defined('ABSPATH')) {
    exit;
}

function mashina_za_vreme_shortcode() {
    ob_start();
    ?>
    <style>
        :root {
            --light-bg: #f8fafc;
            --card-bg: #ffffff;
            --accent-gold: #1151d3;
            --accent-blue: #202f5b;
            --text-main: #1e293b;
            --text-dim: #64748b;
            --input-bg: #f1f5f9;
            --border-color: rgba(0, 0, 0, 0.06);
        }

        .tm-premium-container { max-width: 100%; margin: 0 auto; padding: 20px; background: var(--card-bg); border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 5px 10px -3px rgba(0, 0, 0, 0.02); font-family: 'Inter', system-ui, -apple-system, sans-serif; color: var(--text-main); border: 1px solid var(--border-color); position: relative; overflow: hidden; }
        .tm-content-wrapper { position: relative; z-index: 1; }
        .tm-title { text-align: center; font-size: 20px; font-weight: 800; margin-bottom: 5px; background: linear-gradient(to right, var(--text-main), var(--accent-gold)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; letter-spacing: -0.5px; line-height: 1.2; }
        .tm-subtitle { text-align: center; color: var(--text-dim); font-size: 9px; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600; }
        .tm-search-group { display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; }
        .tm-input-light { width: 100%; background: var(--input-bg); border: 1px solid var(--border-color); padding: 10px 15px; color: var(--text-main); font-size: 14px; outline: none; border-radius: 12px; transition: all 0.3s ease; }
        .tm-btn-gold { width: 100%; background: var(--accent-gold); color: #fff; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .tm-btn-gold:hover { transform: scale(1.02); box-shadow: 0 6px 15px rgba(17, 81, 211, 0.3); background: #0d3aa3; }
        .tm-summary-card { background: #eef5ff; border: 1px solid rgba(17, 81, 211, 0.15); padding: 15px; border-radius: 12px; margin-bottom: 15px; line-height: 1.6; font-size: 13px; position: relative; color: #1a2d5f; }
        .tm-section-title { color: var(--text-main); font-size: 14px; font-weight: 700; margin-bottom: 12px; margin-top: 20px; display: flex; align-items: center; gap: 8px; }
        .tm-section-title::before { content: ""; width: 6px; height: 6px; background: var(--accent-gold); border-radius: 50%; }
        .tm-section-title.tm-en::before { background: var(--accent-blue); }
        .tm-events-stack { display: flex; flex-direction: column; gap: 8px; }
        .tm-event-item { background: #fff; padding: 12px 14px; border-radius: 10px; border: 1px solid var(--border-color); transition: all 0.2s ease; font-size: 12px; line-height: 1.5; }
        .tm-event-item:hover { border-color: var(--accent-gold); transform: translateX(4px); background: var(--light-bg); }
        .tm-event-item.tm-en:hover { border-color: var(--accent-blue); }
        .tm-event-item a { color: var(--accent-gold); text-decoration: none; font-weight: 600; }
        .tm-event-item.tm-en a { color: var(--accent-blue); }
        .tm-scroll { max-height: 400px; overflow-y: auto; padding-right: 8px; }
        .tm-scroll::-webkit-scrollbar { width: 4px; }
        .tm-scroll::-webkit-scrollbar-track { background: transparent; }
        .tm-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .tm-scroll::-webkit-scrollbar-thumb:hover { background: var(--accent-gold); }
        #tm-loader { text-align: center; color: var(--accent-gold); margin: 15px 0; font-weight: bold; font-size: 12px; }
        #tm-error { display: none; color: #dc2626; text-align: center; padding: 15px; font-weight: 500; font-size: 12px; }
        @media (max-width: 350px) { .tm-premium-container { padding: 15px; } .tm-title { font-size: 18px; } .tm-event-item { font-size: 11px; padding: 10px 12px; } }
    </style>

    <div class="tm-premium-container" role="region" aria-label="Машина на времето">
        <div class="tm-content-wrapper">
            <div class="tm-header">
                <div class="tm-title">МАШИНА НА ВРЕМЕТО</div>
                <div class="tm-subtitle">България + Свят</div>
            </div>

            <div class="tm-search-group">
                <input type="text" id="yearInput" placeholder="Въведете година (напр. 1990, 44 BC)..." class="tm-input-light" aria-label="Година">
                <button id="tm-btn" class="tm-btn-gold">ИЗСЛЕДВАЙ</button>
                <div class="tm-subtitle" style="margin-top:6px; margin-bottom:0;">Напиши година и разбери какви събития са се случили</div>
            </div>

            <div id="tm-loader" style="display:none;">⌛ Зареждане...</div>
            <div id="tm-result" style="display:none;">
                <div id="tm-summary-bg" class="tm-summary-card" style="display:none;"></div>
                <div class="tm-subtitle" style="margin-bottom: 10px;">Данни от Уикипедия</div>

                <div class="tm-scroll">
                    <div id="tm-bg-section" style="display:none;">
                        <h3 class="tm-section-title">🇧🇬 България</h3>
                        <div id="tm-list-bg" class="tm-events-stack"></div>
                    </div>

                    <div id="tm-en-section" style="display:none;">
                        <h3 class="tm-section-title tm-en">🌍 Свят</h3>
                        <div id="tm-list-en" class="tm-events-stack"></div>
                    </div>
                </div>
            </div>

            <div id="tm-error"></div>
        </div>
    </div>

    <script>
    (function(){
        const DOMPURIFY_URL = 'https://cdn.jsdelivr.net/npm/dompurify@2.4.0/dist/purify.min.js';
        const LOAD_TIMEOUT = 7000;

        function loadScript(url, timeout = LOAD_TIMEOUT) {
            return new Promise((resolve, reject) => {
                const s = document.createElement('script');
                s.src = url;
                s.async = true;
                s.crossOrigin = 'anonymous';
                let settled = false;
                const timer = setTimeout(() => {
                    if (!settled) {
                        settled = true;
                        s.onerror = s.onload = null;
                        reject(new Error('timeout'));
                    }
                }, timeout);
                s.onload = () => { if (settled) return; settled = true; clearTimeout(timer); resolve(); };
                s.onerror = (e) => { if (settled) return; settled = true; clearTimeout(timer); reject(e || new Error('Failed to load script')); };
                document.head.appendChild(s);
            });
        }

        function sanitizeFallback(html) { const tmp = document.createElement('div'); tmp.textContent = html; return tmp.innerHTML; }

        async function init() {
            let sanitizer;
            try {
                if (!window.DOMPurify) await loadScript(DOMPURIFY_URL);
                if (window.DOMPurify && typeof window.DOMPurify.sanitize === 'function') {
                    sanitizer = (html, cfg) => window.DOMPurify.sanitize(html, cfg);
                } else throw new Error('DOMPurify not available');
            } catch (err) {
                sanitizer = (html) => sanitizeFallback(html);
            }

            const btn = document.getElementById('tm-btn');
            const yearInput = document.getElementById('yearInput');
            const loader = document.getElementById('tm-loader');
            const errorDiv = document.getElementById('tm-error');
            const resultDiv = document.getElementById('tm-result');
            const summaryBgDiv = document.getElementById('tm-summary-bg');
            const bgSection = document.getElementById('tm-bg-section');
            const enSection = document.getElementById('tm-en-section');
            const listBgDiv = document.getElementById('tm-list-bg');
            const listEnDiv = document.getElementById('tm-list-en');

            function showError(msg) { errorDiv.innerText = msg; errorDiv.style.display = 'block'; resultDiv.style.display = 'none'; }
            function clearError() { errorDiv.style.display = 'none'; errorDiv.innerText = ''; }

            function normalizeTitle(raw) {
                if (!raw) return '';
                raw = raw.trim();
                const m = raw.match(/^\s*([+-]?\d+)\s*(bc|bce|ad|ce)?\s*$/i);
                if (m) {
                    const num = parseInt(m[1], 10);
                    const era = (m[2] || '').toLowerCase();
                    if (era === 'bc' || era === 'bce' || num <= 0) return Math.abs(num) + ' BC';
                    return String(Math.abs(num));
                }
                return raw;
            }

            function makeAbsoluteWikiLinks(el, source) {
                const anchors = el.querySelectorAll('a[href]');
                anchors.forEach(a => {
                    try {
                        const href = a.getAttribute('href');
                        if (!href) return;
                        if (href.startsWith('/wiki/')) {
                            const prefix = (source === 'en') ? 'https://en.wikipedia.org' : 'https://bg.wikipedia.org';
                            a.setAttribute('href', prefix + href);
                        } else if (href.startsWith('//')) a.setAttribute('href', 'https:' + href);
                        a.setAttribute('target', '_blank');
                        a.setAttribute('rel', 'noopener noreferrer');
                    } catch (e) {}
                });
            }

            function findNearestHeadingInContainer(node, container) {
                let cur = node;
                while (cur && cur !== container) {
                    let prev = cur.previousElementSibling;
                    while (prev) {
                        if (/^H[1-6]$/i.test(prev.tagName)) return prev;
                        const inner = prev.querySelectorAll('h1,h2,h3,h4,h5,h6');
                        if (inner && inner.length) return inner[inner.length - 1];
                        prev = prev.previousElementSibling;
                    }
                    cur = cur.parentElement;
                }
                return null;
            }

            const excludedHeadingKeywordsBg = ['роден','родени','починал','умрял','смърт','починали','на��рада','нобел','лауреат','библиография','източници','външни','филми','век'];
            const excludedHeadingKeywordsEn = ['births','born','deaths','died','awards','prize','laureates','references','bibliography','external','films','century','animation','architecture','literature','poetry','science','television','video games','music','genre','quantum','sustainable','birding','weather','works entering'];
            const excludeAnyBg = /(\bроден\b|\bродена\b|\bродени\b|\bрод\.|\bпочинал\b|\bпочина\b|\bумря\b|\bумрял\b|†|\bсмърт\b|\bумрели\b|\bнаграда\b|\bнобел\b|\bлауреат\b|\bбиблиография\b|\bизточници\b|\bфилм\b)/iu;
            const excludeAnyEn = /(\bborn\b|\bbirths\b|\bdeaths\b|\bdied\b|†|\baward\b|\bprize\b|\blaureate\b|\breferences\b|\bbibliography\b|\bfilm\b)/iu;
            
            const paginationLike = /^<<\s*\d{1,4}\s*\w+\s*>>$|^(?:\d{2}\s*)+$|^(?:0[1-9]|1[0-2]|[0-9]{2})(?:\s+(?:0[1-9]|1[0-2]|[0-9]{2})){2,}$/i;
            const categoryLike = /^(?:By |Works |Video |Quantum |Sustainable |Birding |Weather |Religion |Sovereign |Antigua |Bosnia |Saint |São |Trinidad |Greece |Ghana |Apple |Wikipedia |Assassination|^[A-Z][a-z]+\s+(?:and\s+)?[A-Z][a-z]+(?:\s+and\s+[A-Z][a-z]+)*$)/i;
            const semicolonList = /^[A-Za-z\s]+;\s*[A-Za-z\s]+(?:;\s*[A-Za-z\s]+){2,}$/;
            
            const simpleNameWithYearBg = /^[А-ЯЁІЇЄ][а-яёіїє]+\s+[А-ЯЁІЇЄ][а-яёіїє]+(?:,\s*[^(]*)?(?:\s*\(р\.\s*\d{4}\s*г\.\))?$/u;
            const simpleNameWithYearEn = /^[A-Z][a-z]+\s+[A-Z][a-z]+(?:,\s*[^(]*)?(?:\s*\(b\.\s*\d{4}\))?$/;

            function isInExcludedSection(node, container, source) {
                const heading = findNearestHeadingInContainer(node, container);
                if (!heading) return false;
                const htext = (heading.textContent || '').toLowerCase();
                const keywords = (source === 'en') ? excludedHeadingKeywordsEn : excludedHeadingKeywordsBg;
                for (const kw of keywords) if (htext.includes(kw)) return true;
                return false;
            }

            function isExcludedByContent(text, source) {
                if (!text) return false;
                
                if (paginationLike.test(text.trim())) return true;
                if (categoryLike.test(text.trim())) return true;
                if (semicolonList.test(text.trim())) return true;
                
                if (source === 'en') {
                    if (simpleNameWithYearEn.test(text.trim())) return true;
                } else {
                    if (simpleNameWithYearBg.test(text.trim())) return true;
                }
                
                const words = text.trim().split(/\s+/);
                if (words.length <= 3) return true;
                
                if (/^(?:[А-ЯЁІЇЄA-Z][а-яёіїєa-z]+(?:\s+[А-ЯЁІЇЄA-Z][а-яёіїєa-z]+)?)(?:\s*;\s*(?:[А-ЯЁІЇЄA-Z][а-яёіїєa-z]+(?:\s+[А-ЯЁІЇЄA-Z][а-яёіїєa-z]+)?)){2,}$/u.test(text)) return true;
                
                if (source === 'en') {
                    if (excludeAnyEn.test(text)) return true;
                } else {
                    if (excludeAnyBg.test(text)) return true;
                }
                if (/\([^\)]*(род|роден|родена|born|died|b\.|d\.)[^\)]*\)/iu.test(text)) return true;
                return false;
            }

            function extractItems(htmlString, limit = 40, source = 'bg', title = '') {
                const temp = document.createElement('div');
                temp.innerHTML = htmlString || '';
                temp.querySelectorAll('img, .mw-editsection, .toc, .infobox, .metadata').forEach(n => n.remove());

                const headings = temp.querySelectorAll('h1, h2, h3, h4, h5, h6');
                let searchContainer = temp;
                const eventKeywordsBg = ['събит', 'събити', 'событи', 'събитие'];
                const eventKeywordsEn = ['events'];
                const eventKeywords = (source === 'bg') ? eventKeywordsBg : eventKeywordsEn;
                
                for (const h of headings) {
                    const txt = (h.textContent || '').toLowerCase();
                    let isEventSection = false;
                    for (const kw of eventKeywords) {
                        if (txt.includes(kw)) {
                            isEventSection = true;
                            break;
                        }
                    }
                    if (isEventSection) {
                        const level = parseInt(h.tagName.slice(1), 10);
                        const frag = document.createDocumentFragment();
                        let sib = h.nextElementSibling;
                        while (sib) {
                            const sibLevel = (sib.tagName && sib.tagName.match(/^H([1-6])$/i)) ? parseInt(sib.tagName.slice(1), 10) : null;
                            if (sibLevel && sibLevel <= level) break;
                            frag.appendChild(sib.cloneNode(true));
                            sib = sib.nextElementSibling;
                        }
                        const container = document.createElement('div');
                        container.appendChild(frag);
                        if (container.innerHTML.trim()) { 
                            searchContainer = container; 
                            break; 
                        }
                    }
                }

                const candidates = [];
                const seen = new Set();

                function makeCandidate(node) {
                    const clone = node.cloneNode(true);
                    clone.querySelectorAll('ul, ol').forEach(list => {
                        const items = Array.from(list.querySelectorAll('li')).map(li => li.textContent.trim()).filter(Boolean);
                        if (items.length) {
                            const txt = items.join('; ');
                            const placeholder = document.createElement('span');
                            placeholder.textContent = txt;
                            if (list.parentNode) list.parentNode.insertBefore(placeholder, list);
                        }
                        if (list.parentNode) list.parentNode.removeChild(list);
                    });
                    makeAbsoluteWikiLinks(clone, source);
                    let innerHtml = clone.innerHTML || '';
                    try {
                        innerHtml = sanitizer(innerHtml, {
                            ALLOWED_TAGS: ['a','b','strong','i','em','sup','sub','span','br','small'],
                            ALLOWED_ATTR: ['href','title','target','rel','class']
                        });
                    } catch (e) {
                        innerHtml = sanitizer(innerHtml);
                    }
                    const textOnly = (clone.textContent || '').trim();
                    return { html: innerHtml.trim(), text: textOnly };
                }

                const liNodes = Array.from(searchContainer.querySelectorAll('li'));
                for (const li of liNodes) {
                    if (isInExcludedSection(li, searchContainer, source)) continue;
                    const c = makeCandidate(li);
                    if (!c || !c.text) continue;
                    if (isExcludedByContent(c.text, source)) continue;
                    const tkey = c.text.toLowerCase().replace(/\s+/g, ' ').trim();
                    if (tkey.length < 20) continue;
                    if (tkey.includes('календар') || tkey.includes('категория')) continue;
                    if (seen.has(tkey)) continue;
                    seen.add(tkey);
                    candidates.push({ html: c.html, text: c.text });
                    if (candidates.length >= limit) break;
                }

                let final = candidates.map(x => x);
                if (title) {
                    const tnorm = String(title).replace(/\s+/g, '').toLowerCase();
                    final.sort((a, b) => {
                        const aHas = a.text.toLowerCase().includes(tnorm) ? 1 : 0;
                        const bHas = b.text.toLowerCase().includes(tnorm) ? 1 : 0;
                        return bHas - aHas;
                    });
                }

                const out = [];
                const outSeen = new Set();
                for (const it of final) {
                    const key = it.text.toLowerCase();
                    if (!outSeen.has(key)) {
                        outSeen.add(key);
                        out.push(it.html);
                        if (out.length >= limit) break;
                    }
                }
                return out;
            }

            async function getImportantEvents() {
                clearError();
                const raw = yearInput.value;
                const title = normalizeTitle(raw);
                if (!title) {
                    showError('⚠️ Моля, въведете валидна година (напр. 1990, 44 BC)');
                    return;
                }

                loader.style.display = 'block';
                btn.disabled = true;
                resultDiv.style.display = 'none';
                summaryBgDiv.style.display = 'none';
                bgSection.style.display = 'none';
                enSection.style.display = 'none';

                try {
                    const summaryUrl = 'https://bg.wikipedia.org/api/rest_v1/page/summary/' + encodeURIComponent(title);
                    const parseBgUrl = 'https://bg.wikipedia.org/w/api.php?action=parse&page=' + encodeURIComponent(title) + '&prop=text&format=json&origin=*';
                    const parseEnUrl = 'https://en.wikipedia.org/w/api.php?action=parse&page=' + encodeURIComponent(title) + '&prop=text&format=json&origin=*';

                    const [summaryResp, bgParseResp, enParseResp] = await Promise.allSettled([
                        fetch(summaryUrl).then(r => r.ok ? r.json() : Promise.reject(r.status)),
                        fetch(parseBgUrl).then(r => r.ok ? r.json() : Promise.reject(r.status)),
                        fetch(parseEnUrl).then(r => r.ok ? r.json() : Promise.reject(r.status))
                    ]);

                    const out = { title: title, summary: '', bg: [], en: [] };

                    if (summaryResp.status === 'fulfilled' && summaryResp.value) {
                        const json = summaryResp.value;
                        if (json.extract) {
                            let extract = json.extract.replace(/^[0-9]+[^.]+е (обикновена|високосна) година[^.]*\.\s*/iu, '');
                            out.summary = extract.trim();
                        }
                    }

                    if (bgParseResp.status === 'fulfilled' && bgParseResp.value) {
                        const json = bgParseResp.value;
                        let html = '';
                        if (json.parse && json.parse.text) html = (typeof json.parse.text === 'object' && json.parse.text['*']) ? json.parse.text['*'] : json.parse.text;
                        else if (json.text) html = json.text;
                        if (html) out.bg = extractItems(html, 999, 'bg', title);
                    }

                    if (enParseResp.status === 'fulfilled' && enParseResp.value) {
                        const json = enParseResp.value;
                        let html = '';
                        if (json.parse && json.parse.text) html = (typeof json.parse.text === 'object' && json.parse.text['*']) ? json.parse.text['*'] : json.parse.text;
                        else if (json.text) html = json.text;
                        if (html) out.en = extractItems(html, 999, 'en', title);
                    }

                    if ((out.summary && out.summary.length) || (out.bg && out.bg.length) || (out.en && out.en.length)) {
                        renderResult(out);
                    } else {
                        showError('❌ Няма събитии намерени в Wikipedia за тази година.');
                    }

                } catch (err) {
                    console.error(err);
                    showError('❌ Грешка при зареждане');
                } finally {
                    loader.style.display = 'none';
                    btn.disabled = false;
                }
            }

            function renderResult(data) {
                clearError();
                if (data.summary) { summaryBgDiv.innerText = data.summary; summaryBgDiv.style.display = 'block'; } else summaryBgDiv.style.display = 'none';

                if (Array.isArray(data.bg) && data.bg.length > 0) {
                    listBgDiv.innerHTML = data.bg.map(i => '<div class="tm-event-item">' + i + '</div>').join('');
                    bgSection.style.display = 'block';
                } else { listBgDiv.innerHTML = ''; bgSection.style.display = 'none'; }

                if (Array.isArray(data.en) && data.en.length > 0) {
                    listEnDiv.innerHTML = data.en.map(i => '<div class="tm-event-item tm-en">' + i + '</div>').join('');
                    enSection.style.display = 'block';
                } else { listEnDiv.innerHTML = ''; enSection.style.display = 'none'; }

                if (summaryBgDiv.style.display === 'block' || bgSection.style.display === 'block' || enSection.style.display === 'block') {
                    resultDiv.style.display = 'block';
                } else {
                    showError('❌ Няма събитии намерени в Wikipedia за тази година.');
                }
            }

            btn.addEventListener('click', getImportantEvents);
            yearInput.addEventListener('keyup', function(e){ if (e.key === 'Enter') getImportantEvents(); });
        } // init

        init();
    })();
    </script>
    <?php
    return ob_get_clean();
}

add_shortcode("mashina_vreme", "mashina_za_vreme_shortcode");