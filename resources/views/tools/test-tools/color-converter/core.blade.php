<section class="tool-section">
  <div class="tool-card">
    <form class="tool-form" id="colorConverterForm">
      <label for="colorValue">Color value</label>
      <input id="colorValue" name="colorValue" type="text" placeholder="#ff0000, ff0000, #f00, rgb(255, 0, 0), 255,0,0" autocomplete="off" />

      <label for="conversionDirection">Conversion direction</label>
      <select id="conversionDirection" name="conversionDirection">
        <option value="hex-to-rgb">HEX to RGB</option>
        <option value="rgb-to-hex">RGB to HEX</option>
      </select>

      <label class="checkbox-label" for="autoDetect">
        <input class="checkbox-input" id="autoDetect" name="autoDetect" type="checkbox" />
        <span class="checkbox-box" aria-hidden="true"></span>
        <span>Auto-detect format</span>
      </label>

      <button type="submit">Convert</button>
    </form>

    <div class="tool-result" id="resultArea" aria-live="polite">
      <div id="resultMessage">Enter a HEX or RGB color to convert.</div>
      <div id="resultOutput" hidden>
        <div id="resultValue" style="font-weight:600;word-break:break-word;"></div>
        <div id="resultMeta" style="margin-top:0.35rem;"></div>
        <div style="display:flex;align-items:center;justify-content:center;gap:0.75rem;margin-top:0.75rem;flex-wrap:wrap;">
          <div id="colorSwatch" aria-label="Converted color preview" style="width:44px;height:44px;border-radius:10px;border:1px solid var(--input-border);background:transparent;"></div>
          <button type="button" id="copyButton">Copy result</button>
        </div>
      </div>
      <div id="errorMessage" style="color:#d93025;margin-top:0.75rem;" hidden></div>
    </div>

    <div style="margin-top:1rem;font-size:0.95rem;line-height:1.6;">
      <strong>Examples:</strong> #ff0000, ff0000, #f00, rgb(255, 0, 0), 255,0,0
      <br />
      <strong>HEX:</strong> 3 or 6 hex digits, with or without #
      <br />
      <strong>RGB:</strong> three values from 0 to 255, with or without rgb()
    </div>
  </div>

  <script>
    (function () {
      const form = document.getElementById('colorConverterForm');
      const colorValueInput = document.getElementById('colorValue');
      const conversionDirection = document.getElementById('conversionDirection');
      const autoDetect = document.getElementById('autoDetect');
      const resultMessage = document.getElementById('resultMessage');
      const resultOutput = document.getElementById('resultOutput');
      const resultValue = document.getElementById('resultValue');
      const resultMeta = document.getElementById('resultMeta');
      const colorSwatch = document.getElementById('colorSwatch');
      const copyButton = document.getElementById('copyButton');
      const errorMessage = document.getElementById('errorMessage');

      let lastResult = '';

      function showError(message) {
        errorMessage.textContent = message;
        errorMessage.hidden = false;
        resultOutput.hidden = true;
        resultMessage.textContent = 'Conversion failed.';
      }

      function clearError() {
        errorMessage.textContent = '';
        errorMessage.hidden = true;
      }

      function showResult(value, meta, swatchColor) {
        clearError();
        lastResult = value;
        resultValue.textContent = value;
        resultMeta.textContent = meta;
        colorSwatch.style.backgroundColor = swatchColor;
        resultOutput.hidden = false;
        resultMessage.textContent = 'Conversion successful.';
      }

      function normalizeHex(input) {
        let value = input.trim();
        if (value.startsWith('#')) value = value.slice(1);
        if (!/^[0-9a-fA-F]{3}$|^[0-9a-fA-F]{6}$/.test(value)) return null;
        if (value.length === 3) {
          value = value.split('').map(ch => ch + ch).join('');
        }
        return value.toUpperCase();
      }

      function parseRgb(input) {
        let value = input.trim().toLowerCase();
        if (value.startsWith('rgb(') && value.endsWith(')')) {
          value = value.slice(4, -1).trim();
        }
        const parts = value.split(',').map(part => part.trim());
        if (parts.length !== 3) return null;
        const nums = parts.map(part => {
          if (!/^\d+$/.test(part)) return NaN;
          return Number(part);
        });
        if (nums.some(num => !Number.isInteger(num) || num < 0 || num > 255)) return null;
        return nums;
      }

      function hexToRgb(hex) {
        const r = parseInt(hex.slice(0, 2), 16);
        const g = parseInt(hex.slice(2, 4), 16);
        const b = parseInt(hex.slice(4, 6), 16);
        return `rgb(${r}, ${g}, ${b})`;
      }

      function rgbToHex(rgb) {
        return (
          '#' +
          rgb
            .map(num => num.toString(16).padStart(2, '0'))
            .join('')
            .toUpperCase()
        );
      }

      function detectFormat(input) {
        const trimmed = input.trim();
        const hexLike = trimmed.startsWith('#') ? trimmed.slice(1) : trimmed;
        if (/^[0-9a-fA-F]{3}$|^[0-9a-fA-F]{6}$/.test(hexLike)) return 'hex';
        const rgb = parseRgb(trimmed);
        if (rgb) return 'rgb';
        return null;
      }

      function convert() {
        const input = colorValueInput.value.trim();
        if (!input) {
          showError('Please enter a color value.');
          return;
        }

        let mode = conversionDirection.value;
        if (autoDetect.checked) {
          const detected = detectFormat(input);
          if (!detected) {
            showError('Unable to detect the format. Enter a valid HEX or RGB value, or choose a direction manually.');
            return;
          }
          mode = detected === 'hex' ? 'hex-to-rgb' : 'rgb-to-hex';
        }

        if (mode === 'hex-to-rgb') {
          const hex = normalizeHex(input);
          if (!hex) {
            showError('Invalid HEX format. Use 3 or 6 hex digits, with or without a leading #.');
            return;
          }
          const rgb = hexToRgb(hex);
          showResult(rgb, 'Output format: RGB', rgb);
          return;
        }

        const rgb = parseRgb(input);
        if (!rgb) {
          showError('Invalid RGB format. Use three values from 0 to 255, with or without rgb().');
          return;
        }
        const hex = rgbToHex(rgb);
        showResult(hex, 'Output format: HEX', hex);
      }

      form.addEventListener('submit', function (e) {
        e.preventDefault();
        convert();
      });

      copyButton.addEventListener('click', async function () {
        if (!lastResult) return;
        try {
          await navigator.clipboard.writeText(lastResult);
          copyButton.textContent = 'Copied';
          setTimeout(() => {
            copyButton.textContent = 'Copy result';
          }, 1200);
        } catch (err) {
          showError('Copy failed. Please select and copy the result manually.');
        }
      });

      autoDetect.addEventListener('change', function () {
        if (autoDetect.checked) {
          conversionDirection.disabled = true;
        } else {
          conversionDirection.disabled = false;
        }
      });
    })();
  </script>
</section>