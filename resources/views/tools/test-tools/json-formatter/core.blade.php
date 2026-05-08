<section class="tool-section">
  <div class="tool-card">
    <form class="tool-form" id="jsonFormatterForm">
      <label for="jsonInput">JSON Input</label>
      <textarea id="jsonInput" name="jsonInput" rows="12" placeholder="Paste or type JSON here"></textarea>

      <div class="tool-form" style="gap:0.75rem;">
        <label for="indentSize">Indentation Size</label>
        <select id="indentSize" name="indentSize">
          <option value="2" selected>2 spaces</option>
          <option value="4">4 spaces</option>
          <option value="tab">Tabs</option>
        </select>

        <label class="checkbox-label" for="autoFormat">
          <input type="checkbox" id="autoFormat" class="checkbox-input" />
          <span class="checkbox-box"></span>
          <span>Auto-format on paste/input</span>
        </label>

        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0.75rem;">
          <button type="submit" id="formatBtn">Format JSON</button>
          <button type="button" id="clearBtn">Clear</button>
        </div>
      </div>
    </form>

    <div class="tool-result" id="statusMessage" aria-live="polite" style="margin-top:1rem;"></div>

    <div class="tool-form" style="margin-top:1rem;">
      <label for="jsonOutput">Formatted Output</label>
      <textarea id="jsonOutput" name="jsonOutput" rows="12" readonly placeholder="Formatted JSON will appear here"></textarea>

      <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0.75rem;">
        <button type="button" id="copyBtn" disabled>Copy Formatted Output</button>
        <button type="button" id="selectBtn">Select Output</button>
      </div>
    </div>

    <div class="tool-result" id="summaryBox" style="margin-top:1rem;"></div>
  </div>

  <script>
    (function () {
      const form = document.getElementById('jsonFormatterForm');
      const input = document.getElementById('jsonInput');
      const output = document.getElementById('jsonOutput');
      const indentSize = document.getElementById('indentSize');
      const autoFormat = document.getElementById('autoFormat');
      const statusMessage = document.getElementById('statusMessage');
      const summaryBox = document.getElementById('summaryBox');
      const copyBtn = document.getElementById('copyBtn');
      const clearBtn = document.getElementById('clearBtn');
      const selectBtn = document.getElementById('selectBtn');

      let debounceTimer = null;
      let lastValidOutput = '';

      function setStatus(message, type) {
        statusMessage.textContent = message;
        statusMessage.style.color = type === 'error' ? '#b42318' : type === 'success' ? '#067647' : '';
      }

      function setSummary(value) {
        if (value === null || value === undefined) {
          summaryBox.textContent = '';
          return;
        }

        const type = Array.isArray(value) ? 'array' : typeof value;
        let summary = `Top-level type: ${type}`;

        if (Array.isArray(value)) {
          summary += ` · Items: ${value.length}`;
        } else if (value && typeof value === 'object') {
          summary += ` · Keys: ${Object.keys(value).length}`;
        }

        const formattedLength = lastValidOutput ? lastValidOutput.length : 0;
        summary += ` · Characters: ${formattedLength}`;

        summaryBox.textContent = summary;
      }

      function getIndent() {
        const value = indentSize.value;
        return value === 'tab' ? '\t' : Number(value) || 2;
      }

      function parseAndFormat() {
        const raw = input.value;
        if (!raw || !raw.trim()) {
          output.value = '';
          lastValidOutput = '';
          copyBtn.disabled = true;
          setStatus('Enter JSON to format.', '');
          setSummary(null);
          return false;
        }

        try {
          const parsed = JSON.parse(raw);
          const formatted = JSON.stringify(parsed, null, getIndent());
          output.value = formatted;
          lastValidOutput = formatted;
          copyBtn.disabled = false;
          setStatus('JSON is valid and formatted successfully.', 'success');
          setSummary(parsed);
          return true;
        } catch (error) {
          output.value = '';
          lastValidOutput = '';
          copyBtn.disabled = true;
          setSummary(null);
          const message = error && error.message ? error.message : 'Invalid JSON.';
          setStatus(`Invalid JSON: ${message}. Check for missing quotes, trailing commas, or mismatched brackets.`, 'error');
          return false;
        }
      }

      function scheduleAutoFormat() {
        if (!autoFormat.checked) return;
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
          parseAndFormat();
        }, 350);
      }

      form.addEventListener('submit', function (e) {
        e.preventDefault();
        parseAndFormat();
      });

      input.addEventListener('input', scheduleAutoFormat);
      input.addEventListener('paste', function () {
        scheduleAutoFormat();
      });

      indentSize.addEventListener('change', function () {
        if (autoFormat.checked && input.value.trim()) {
          parseAndFormat();
        }
      });

      autoFormat.addEventListener('change', function () {
        if (autoFormat.checked && input.value.trim()) {
          parseAndFormat();
        }
      });

      copyBtn.addEventListener('click', async function () {
        if (!lastValidOutput) {
          setStatus('No formatted JSON available to copy.', 'error');
          return;
        }

        try {
          await navigator.clipboard.writeText(lastValidOutput);
          setStatus('Copied to clipboard.', 'success');
        } catch (error) {
          try {
            output.focus();
            output.select();
            const successful = document.execCommand('copy');
            if (successful) {
              setStatus('Copied to clipboard.', 'success');
            } else {
              setStatus('Copy failed. Please copy the formatted JSON manually.', 'error');
            }
          } catch (fallbackError) {
            setStatus('Copy failed. Please copy the formatted JSON manually.', 'error');
          }
        }
      });

      clearBtn.addEventListener('click', function () {
        input.value = '';
        output.value = '';
        lastValidOutput = '';
        copyBtn.disabled = true;
        statusMessage.textContent = '';
        summaryBox.textContent = '';
        input.focus();
      });

      selectBtn.addEventListener('click', function () {
        if (!output.value) {
          setStatus('No formatted JSON available to select.', 'error');
          return;
        }
        output.focus();
        output.select();
        setStatus('Formatted JSON selected.', 'success');
      });

      copyBtn.disabled = true;
      setStatus('Enter JSON to format.', '');
    })();
  </script>
</section>