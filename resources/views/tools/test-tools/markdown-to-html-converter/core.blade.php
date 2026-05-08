<section class="tool-section">
  <div class="tool-card">
    <div class="tool-form">
      <div class="tool-workspace" style="display:grid;gap:1rem;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));align-items:start;">
        <div class="tool-panel">
          <label for="md-input">Markdown input</label>
          <textarea id="md-input" rows="18" placeholder="Paste or type Markdown here..."># Sample Markdown

This is **bold**, *italic*, and `inline code`.

- Item one
- Item two
- Item three

[Example link](https://example.com)

> A blockquote example.

| Name | Value |
| --- | --- |
| One | 1 |
| Two | 2 |</textarea>
        </div>

        <div class="tool-panel">
          <label for="html-output">HTML output</label>
          <textarea id="html-output" rows="18" readonly placeholder="Generated HTML will appear here..."></textarea>
        </div>
      </div>

      <div class="tool-controls" style="display:grid;gap:0.75rem;">
        <div style="display:flex;flex-wrap:wrap;gap:0.75rem;">
          <button type="button" id="convert-btn">Convert</button>
          <button type="button" id="copy-btn">Copy output</button>
          <button type="button" id="clear-btn">Clear</button>
        </div>

        <div style="display:grid;gap:0.75rem;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));">
          <label class="checkbox-label" for="auto-convert">
            <input class="checkbox-input" type="checkbox" id="auto-convert" checked>
            <span class="checkbox-box"></span>
            <span>Auto-convert on input</span>
          </label>

          <label for="output-format">
            Output format
            <select id="output-format">
              <option value="fragment" selected>HTML fragment</option>
              <option value="document">Full HTML document</option>
            </select>
          </label>

          <label class="checkbox-label" for="preserve-breaks">
            <input class="checkbox-input" type="checkbox" id="preserve-breaks">
            <span class="checkbox-box"></span>
            <span>Preserve line breaks</span>
          </label>

          <label class="checkbox-label" for="sanitize-output">
            <input class="checkbox-input" type="checkbox" id="sanitize-output" checked>
            <span class="checkbox-box"></span>
            <span>Sanitize output</span>
          </label>
        </div>
      </div>

      <div class="tool-result" style="text-align:left;">
        <div id="status-message">Enter Markdown to see HTML output.</div>
        <div id="stats" style="margin-top:0.5rem;">Characters: 0 | Lines: 0</div>
      </div>

      <div class="tool-preview" style="display:grid;gap:0.5rem;">
        <label for="preview-frame">Rendered preview</label>
        <iframe id="preview-frame" title="Rendered HTML preview" style="width:100%;min-height:320px;border:1px solid var(--input-border);border-radius:8px;background:var(--input-bg);"></iframe>
      </div>
    </div>
  </div>

  <script>
    (function () {
      const mdInput = document.getElementById('md-input');
      const htmlOutput = document.getElementById('html-output');
      const previewFrame = document.getElementById('preview-frame');
      const convertBtn = document.getElementById('convert-btn');
      const copyBtn = document.getElementById('copy-btn');
      const clearBtn = document.getElementById('clear-btn');
      const autoConvert = document.getElementById('auto-convert');
      const outputFormat = document.getElementById('output-format');
      const preserveBreaks = document.getElementById('preserve-breaks');
      const sanitizeOutput = document.getElementById('sanitize-output');
      const statusMessage = document.getElementById('status-message');
      const stats = document.getElementById('stats');

      const escapeHtml = (str) => str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');

      const escapeAttr = (str) => escapeHtml(str).replace(/`/g, '&#96;');

      const sanitizeHtml = (html) => {
        const template = document.createElement('template');
        template.innerHTML = html;
        const blocked = new Set(['script', 'iframe', 'object', 'embed', 'link', 'meta', 'style']);
        const walk = (node) => {
          [...node.children].forEach((el) => {
            if (blocked.has(el.tagName.toLowerCase())) {
              el.remove();
              return;
            }
            [...el.attributes].forEach((attr) => {
              const name = attr.name.toLowerCase();
              const value = attr.value.toLowerCase();
              if (name.startsWith('on') || value.startsWith('javascript:')) el.removeAttribute(attr.name);
            });
            walk(el);
          });
        };
        walk(template.content);
        return template.innerHTML;
      };

      const parseInline = (text, allowRawHtml) => {
        let out = allowRawHtml ? text : escapeHtml(text);
        out = out.replace(/!\[([^\]]*)\]\(([^)\s]+)(?:\s+"([^"]*)")?\)/g, (_, alt, src, title) => {
          const t = title ? ` title="${escapeAttr(title)}"` : '';
          return `<img src="${escapeAttr(src)}" alt="${escapeAttr(alt)}"${t}>`;
        });
        out = out.replace(/\[([^\]]+)\]\(([^)\s]+)(?:\s+"([^"]*)")?\)/g, (_, label, href, title) => {
          const t = title ? ` title="${escapeAttr(title)}"` : '';
          return `<a href="${escapeAttr(href)}"${t} target="_blank" rel="noopener noreferrer">${label}</a>`;
        });
        out = out.replace(/`([^`]+)`/g, '<code>$1</code>');
        out = out.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
        out = out.replace(/__([^_]+)__/g, '<strong>$1</strong>');
        out = out.replace(/\*([^*]+)\*/g, '<em>$1</em>');
        out = out.replace(/_([^_]+)_/g, '<em>$1</em>');
        if (preserveBreaks.checked) out = out.replace(/\n/g, '<br>');
        return out;
      };

      const convertMarkdown = (input) => {
        const allowRawHtml = !sanitizeOutput.checked;
        const lines = input.replace(/\r\n?/g, '\n').split('\n');
        const blocks = [];
        let i = 0;

        const isHr = (line) => /^(\s*([-*_])\s*){3,}$/.test(line);
        const isHeading = (line) => /^(#{1,6})\s+(.+)$/.test(line);
        const isBlockquote = (line) => /^\s*>\s?/.test(line);
        const isUl = (line) => /^\s*[-*+]\s+/.test(line);
        const isOl = (line) => /^\s*\d+\.\s+/.test(line);
        const isTableSep = (line) => /^\s*\|?(?:\s*:?-{3,}:?\s*\|)+\s*:?-{3,}:?\s*\|?\s*$/.test(line);

        while (i < lines.length) {
          let line = lines[i];
          if (!line.trim()) { i++; continue; }

          if (isHr(line)) { blocks.push('<hr>'); i++; continue; }

          const heading = line.match(/^(#{1,6})\s+(.+)$/);
          if (heading) {
            const level = heading[1].length;
            blocks.push(`<h${level}>${parseInline(heading[2].trim(), allowRawHtml)}</h${level}>`);
            i++;
            continue;
          }

          if (isBlockquote(line)) {
            const quoteLines = [];
            while (i < lines.length && isBlockquote(lines[i])) {
              quoteLines.push(lines[i].replace(/^\s*>\s?/, ''));
              i++;
            }
            blocks.push(`<blockquote>${convertMarkdown(quoteLines.join('\n'))}</blockquote>`);
            continue;
          }

          if (isUl(line) || isOl(line)) {
            const ordered = isOl(line);
            const items = [];
            while (i < lines.length && (ordered ? isOl(lines[i]) : isUl(lines[i]))) {
              items.push(lines[i].replace(/^\s*(?:[-*+]|\d+\.)\s+/, ''));
              i++;
            }
            const tag = ordered ? 'ol' : 'ul';
            blocks.push(`<${tag}>${items.map(item => `<li>${parseInline(item.trim(), allowRawHtml)}</li>`).join('')}</${tag}>`);
            continue;
          }

          if (line.trim().startsWith('')) {
            const fence = line.trim();
            const codeLines = [];
            i++;
            while (i < lines.length && lines[i].trim() !== fence) {
              codeLines.push(lines[i]);
              i++;
            }
            if (i < lines.length) i++;
            blocks.push(`<pre><code>${escapeHtml(codeLines.join('\n'))}</code></pre>`);
            continue;
          }

          if (i + 1 < lines.length && lines[i].includes('|') && isTableSep(lines[i + 1])) {
            const headers = lines[i].split('|').map(s => s.trim()).filter(Boolean);
            i += 2;
            const rows = [];
            while (i < lines.length && lines[i].includes('|') && lines[i].trim()) {
              rows.push(lines[i].split('|').map(s => s.trim()).filter(Boolean));
              i++;
            }
            const thead = `<thead><tr>${headers.map(h => `<th>${parseInline(h, allowRawHtml)}</th>`).join('')}</tr></thead>`;
            const tbody = `<tbody>${rows.map(row => `<tr>${row.map(cell => `<td>${parseInline(cell, allowRawHtml)}</td>`).join('')}</tr>`).join('')}</tbody>`;
            blocks.push(`<table>${thead}${tbody}</table>`);
            continue;
          }

          const para = [];
          while (i < lines.length && lines[i].trim() && !isHr(lines[i]) && !isHeading(lines[i]) && !isBlockquote(lines[i]) && !isUl(lines[i]) && !isOl(lines[i]) && !lines[i].trim().startsWith('')) {
            if (i + 1 < lines.length && lines[i].includes('|') && isTableSep(lines[i + 1])) break;
            para.push(lines[i]);
            i++;
          }
          blocks.push(`<p>${parseInline(para.join('\n').trim(), allowRawHtml)}</p>`);
        }

        let html = blocks.join('\n');
        if (sanitizeOutput.checked) html = sanitizeHtml(html);
        if (outputFormat.value === 'document') {
          html = `<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Markdown Output</title></head><body>${html}</body></html>`;
        }
        return html;
      };

      const updateStats = () => {
        const text = mdInput.value || '';
        const chars = text.length;
        const lines = text ? text.replace(/\r\n?/g, '\n').split('\n').length : 0;
        stats.textContent = `Characters: ${chars} | Lines: ${lines}`;
      };

      const updatePreview = (html) => {
        const doc = previewFrame.contentDocument || previewFrame.contentWindow.document;
        doc.open();
        doc.write(html || '<!doctype html><html><body></body></html>');
        doc.close();
      };

      const convert = () => {
        const input = mdInput.value.trim();
        updateStats();
        if (!input) {
          htmlOutput.value = '';
          updatePreview('<!doctype html><html><body></body></html>');
          statusMessage.textContent = 'Enter Markdown to see HTML output.';
          return;
        }
        const html = convertMarkdown(mdInput.value);
        htmlOutput.value = html;
        updatePreview(html);
        statusMessage.textContent = sanitizeOutput.checked ? 'Conversion complete. Output sanitized.' : 'Conversion complete.';
      };

      let timer = null;
      const scheduleConvert = () => {
        if (!autoConvert.checked) return;
        clearTimeout(timer);
        timer = setTimeout(convert, 150);
      };

      mdInput.addEventListener('input', () => {
        updateStats();
        scheduleConvert();
      });

      [outputFormat, preserveBreaks, sanitizeOutput].forEach(el => el.addEventListener('change', () => {
        if (autoConvert.checked) convert();
      }));

      autoConvert.addEventListener('change', () => {
        if (autoConvert.checked) convert();
      });

      convertBtn.addEventListener('click', convert);

      copyBtn.addEventListener('click', async () => {
        const text = htmlOutput.value;
        if (!text) {
          statusMessage.textContent = 'Nothing to copy yet.';
          return;
        }
        try {
          await navigator.clipboard.writeText(text);
          statusMessage.textContent = 'HTML copied to clipboard.';
        } catch (e) {
          htmlOutput.focus();
          htmlOutput.select();
          const ok = document.execCommand('copy');
          statusMessage.textContent = ok ? 'HTML copied to clipboard.' : 'Copy failed. Please copy manually.';
        }
      });

      clearBtn.addEventListener('click', () => {
        mdInput.value = '';
        htmlOutput.value = '';
        updateStats();
        updatePreview('<!doctype html><html><body></body></html>');
        statusMessage.textContent = 'Enter Markdown to see HTML output.';
        mdInput.focus();
      });

      updateStats();
      convert();
    })();
  </script>
</section>
