<section class="tool-section">
  <div class="tool-card">
    <form class="tool-form" id="uuidForm">
      <label for="quantityInput">
        Quantity
        <input id="quantityInput" name="quantity" type="number" min="1" max="1000" step="1" value="1" inputmode="numeric" required>
      </label>

      <label for="versionSelect">
        UUID version
        <select id="versionSelect" name="version">
          <option value="v4" selected>UUID v4</option>
        </select>
      </label>

      <label class="checkbox-label" for="uppercaseOutput">
        <input class="checkbox-input" id="uppercaseOutput" name="uppercase" type="checkbox">
        <span class="checkbox-box" aria-hidden="true"></span>
        <span>Uppercase output</span>
      </label>

      <label class="checkbox-label" for="quoteOutput">
        <input class="checkbox-input" id="quoteOutput" name="quotes" type="checkbox">
        <span class="checkbox-box" aria-hidden="true"></span>
        <span>Wrap in quotes</span>
      </label>

      <label class="checkbox-label" for="onePerLine">
        <input class="checkbox-input" id="onePerLine" name="onePerLine" type="checkbox" checked>
        <span class="checkbox-box" aria-hidden="true"></span>
        <span>One per line</span>
      </label>

      <label class="checkbox-label" for="autoGenerate">
        <input class="checkbox-input" id="autoGenerate" name="autoGenerate" type="checkbox" checked>
        <span class="checkbox-box" aria-hidden="true"></span>
        <span>Auto-generate on load</span>
      </label>

      <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0.75rem;">
        <button type="submit" id="generateBtn">Generate</button>
        <button type="button" id="copyBtn">Copy all</button>
      </div>
    </form>

    <div class="tool-result" id="statusMessage" aria-live="polite" style="margin-top:1rem;">Ready to generate UUID v4 values.</div>

    <div class="tool-result" style="margin-top:0.75rem;text-align:left;">
      <textarea id="resultOutput" rows="10" readonly placeholder="Generated UUIDs will appear here."></textarea>
    </div>

    <div class="tool-result" id="summaryBlock" style="margin-top:0.75rem;text-align:left;">
      <div id="summaryText">No UUIDs generated yet.</div>
      <div style="margin-top:0.35rem;">UUID v4 values are random and suitable for development and testing.</div>
    </div>
  </div>

  <script>
    (function () {
      const form = document.getElementById('uuidForm');
      const quantityInput = document.getElementById('quantityInput');
      const versionSelect = document.getElementById('versionSelect');
      const uppercaseOutput = document.getElementById('uppercaseOutput');
      const quoteOutput = document.getElementById('quoteOutput');
      const onePerLine = document.getElementById('onePerLine');
      const autoGenerate = document.getElementById('autoGenerate');
      const generateBtn = document.getElementById('generateBtn');
      const copyBtn = document.getElementById('copyBtn');
      const resultOutput = document.getElementById('resultOutput');
      const statusMessage = document.getElementById('statusMessage');
      const summaryText = document.getElementById('summaryText');

      const MAX_QUANTITY = 1000;
      let lastResults = [];

      function setStatus(message, isError) {
        statusMessage.textContent = message;
        statusMessage.style.color = isError ? 'var(--accent)' : 'inherit';
      }

      function validateQuantity() {
        const raw = quantityInput.value.trim();
        if (raw === '') return { ok: true, value: 1 };
        const num = Number(raw);
        if (!Number.isFinite(num) || !Number.isInteger(num)) {
          return { ok: false, message: 'Please enter a valid whole number for quantity.' };
        }
        if (num < 1) {
          return { ok: false, message: 'Quantity must be at least 1.' };
        }
        if (num > MAX_QUANTITY) {
          return { ok: true, value: MAX_QUANTITY, capped: true };
        }
        return { ok: true, value: num };
      }

      function uuidV4() {
        if (!window.crypto || !window.crypto.getRandomValues) {
          throw new Error('Secure UUID generation is not available in this browser.');
        }
        const bytes = new Uint8Array(16);
        window.crypto.getRandomValues(bytes);
        bytes[6] = (bytes[6] & 0x0f) | 0x40;
        bytes[8] = (bytes[8] & 0x3f) | 0x80;
        const hex = Array.from(bytes, b => b.toString(16).padStart(2, '0'));
        return (
          hex.slice(0, 4).join('') + '-' +
          hex.slice(4, 6).join('') + '-' +
          hex.slice(6, 8).join('') + '-' +
          hex.slice(8, 10).join('') + '-' +
          hex.slice(10, 16).join('')
        );
      }

      function formatUuid(value) {
        let out = value;
        if (uppercaseOutput.checked) out = out.toUpperCase();
        if (quoteOutput.checked) out = '"' + out + '"';
        return out;
      }

      function renderResults(results, quantity, capped) {
        lastResults = results.slice();
        const output = onePerLine.checked ? results.join('\n') : results.join(', ');
        resultOutput.value = output;
        summaryText.textContent = 'Generated ' + quantity + ' UUID' + (quantity === 1 ? '' : 's') + ' using UUID v4' + (capped ? ' (quantity capped at ' + MAX_QUANTITY + ')' : '') + '.';
        setStatus('UUIDs generated successfully.', false);
      }

      function generate() {
        if (!window.crypto || !window.crypto.getRandomValues) {
          setStatus('Secure UUID generation is unavailable in this browser.', true);
          resultOutput.value = '';
          summaryText.textContent = 'No UUIDs generated yet.';
          lastResults = [];
          return;
        }

        const validation = validateQuantity();
        if (!validation.ok) {
          setStatus(validation.message, true);
          return;
        }

        if (versionSelect.value !== 'v4') {
          setStatus('Only UUID v4 is supported.', true);
          return;
        }

        const quantity = validation.value;
        const results = [];
        try {
          for (let i = 0; i < quantity; i++) {
            results.push(formatUuid(uuidV4()));
          }
        } catch (error) {
          setStatus(error.message || 'Unable to generate UUIDs.', true);
          return;
        }

        renderResults(results, quantity, validation.capped);
      }

      async function copyAll() {
        if (!lastResults.length) {
          setStatus('Generate UUIDs before copying.', true);
          return;
        }
        const text = onePerLine.checked ? lastResults.join('\n') : lastResults.join(', ');
        try {
          await navigator.clipboard.writeText(text);
          setStatus('Copied UUIDs to clipboard.', false);
        } catch (error) {
          resultOutput.focus();
          resultOutput.select();
          setStatus('Copy failed. The UUIDs are selected so you can copy them manually.', true);
        }
      }

      form.addEventListener('submit', function (event) {
        event.preventDefault();
        generate();
      });

      copyBtn.addEventListener('click', copyAll);

      [uppercaseOutput, quoteOutput, onePerLine].forEach(function (input) {
        input.addEventListener('change', function () {
          if (lastResults.length) {
            renderResults(lastResults, lastResults.length, false);
          }
        });
      });

      if (autoGenerate.checked) {
        generate();
      }
    })();
  </script>
</section>