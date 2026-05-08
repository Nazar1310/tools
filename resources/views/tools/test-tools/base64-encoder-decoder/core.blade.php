<section class="tool-section">
  <div class="tool-card">
    <form class="tool-form" id="base64ToolForm">
      <div>
        <label for="base64Mode">Conversion mode</label>
        <select id="base64Mode" name="mode">
          <option value="encode">Encode to Base64</option>
          <option value="decode">Decode from Base64</option>
        </select>
      </div>

      <label class="checkbox-label" for="autoConvert">
        <input class="checkbox-input" type="checkbox" id="autoConvert" checked>
        <span class="checkbox-box" aria-hidden="true"></span>
        <span>Auto-convert as you type</span>
      </label>

      <div>
        <label for="base64Input">Input</label>
        <textarea id="base64Input" rows="8" placeholder="Enter text to encode or Base64 to decode"></textarea>
        <div id="inputHint" style="font-size:0.9rem;opacity:0.8;margin-top:0.35rem;">Enter plain text to encode into Base64.</div>
      </div>

      <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:0.75rem;">
        <button type="button" id="convertBtn">Convert</button>
        <button type="button" id="copyBtn">Copy Output</button>
        <button type="button" id="clearBtn">Clear</button>
      </div>
    </form>

    <div class="tool-result" style="margin-top:1rem;">
      <div style="text-align:left;display:grid;gap:0.75rem;">
        <div>
          <label for="base64Output">Output</label>
          <textarea id="base64Output" rows="8" readonly placeholder="Converted result will appear here"></textarea>
        </div>
        <div id="statusMessage" aria-live="polite" style="font-size:0.95rem;"></div>
        <div id="lengthInfo" style="font-size:0.9rem;opacity:0.85;"></div>
      </div>
    </div>
  </div>

  <script>
    (function () {
      const form = document.getElementById('base64ToolForm');
      const modeEl = document.getElementById('base64Mode');
      const autoConvertEl = document.getElementById('autoConvert');
      const inputEl = document.getElementById('base64Input');
      const outputEl = document.getElementById('base64Output');
      const hintEl = document.getElementById('inputHint');
      const statusEl = document.getElementById('statusMessage');
      const lengthEl = document.getElementById('lengthInfo');
      const convertBtn = document.getElementById('convertBtn');
      const copyBtn = document.getElementById('copyBtn');
      const clearBtn = document.getElementById('clearBtn');

      function setStatus(message, type) {
        statusEl.textContent = message || '';
        statusEl.style.color = type === 'error' ? '#b42318' : type === 'success' ? '#067647' : '';
      }

      function updateHint() {
        hintEl.textContent = modeEl.value === 'encode'
          ? 'Enter plain text to encode into Base64.'
          : 'Enter a Base64 string to decode into readable text.';
      }

      function updateLengthInfo() {
        const inputLength = inputEl.value.length;
        const outputLength = outputEl.value.length;
        lengthEl.textContent = `Input length: ${inputLength} characters · Output length: ${outputLength} characters`;
      }

      function encodeBase64(text) {
        return btoa(unescape(encodeURIComponent(text)));
      }

      function decodeBase64(text) {
        const normalized = text.replace(/\s+/g, '');
        if (!normalized) {
          throw new Error('Please enter a Base64 string to decode.');
        }
        if (!/^[A-Za-z0-9+/]*={0,2}$/.test(normalized) || normalized.length % 4 === 1) {
          throw new Error('Invalid Base64 format.');
        }
        const decoded = atob(normalized);
        try {
          return decodeURIComponent(escape(decoded));
        } catch (e) {
          return decoded;
        }
      }

      function convert() {
        const input = inputEl.value;
        if (!input.trim()) {
          outputEl.value = '';
          setStatus('Please enter text to convert.', 'error');
          updateLengthInfo();
          return;
        }

        try {
          let result = '';
          if (modeEl.value === 'encode') {
            result = encodeBase64(input);
          } else {
            result = decodeBase64(input);
          }
          outputEl.value = result;
          setStatus('Conversion completed successfully.', 'success');
        } catch (error) {
          outputEl.value = '';
          setStatus(error.message || 'Conversion failed.', 'error');
        }
        updateLengthInfo();
      }

      async function copyOutput() {
        const text = outputEl.value;
        if (!text) {
          setStatus('There is no output to copy yet.', 'error');
          return;
        }
        try {
          await navigator.clipboard.writeText(text);
          setStatus('Output copied to clipboard.', 'success');
        } catch (error) {
          outputEl.focus();
          outputEl.select();
          const copied = document.execCommand('copy');
          if (copied) {
            setStatus('Output copied to clipboard.', 'success');
          } else {
            setStatus('Unable to copy automatically. Please copy the output manually.', 'error');
          }
        }
      }

      function clearAll() {
        inputEl.value = '';
        outputEl.value = '';
        setStatus('');
        updateLengthInfo();
        inputEl.focus();
      }

      modeEl.addEventListener('change', function () {
        updateHint();
        if (autoConvertEl.checked) convert();
      });

      inputEl.addEventListener('input', function () {
        if (autoConvertEl.checked) convert();
        else updateLengthInfo();
      });

      autoConvertEl.addEventListener('change', function () {
        if (autoConvertEl.checked) convert();
      });

      convertBtn.addEventListener('click', function () {
        convert();
      });

      copyBtn.addEventListener('click', function () {
        copyOutput();
      });

      clearBtn.addEventListener('click', function () {
        clearAll();
      });

      form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!autoConvertEl.checked) convert();
      });

      updateHint();
      updateLengthInfo();
    })();
  </script>
</section>