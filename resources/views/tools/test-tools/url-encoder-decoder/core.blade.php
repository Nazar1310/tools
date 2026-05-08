<section class="tool-section">
  <div class="tool-card">
    <form class="tool-form" id="urlToolForm">
      <label for="modeSelect">Mode</label>
      <select id="modeSelect" name="mode">
        <option value="encode">Encode</option>
        <option value="decode">Decode</option>
      </select>

      <label for="urlInput">URL or text</label>
      <textarea id="urlInput" name="input" rows="8" placeholder="Paste a full URL, query string, fragment, or plain text here..."></textarea>

      <button type="submit" id="actionButton">Encode</button>
      <button type="button" id="resetButton" style="display:none;">Reset</button>
    </form>

    <div class="tool-result" id="resultSection" aria-live="polite" style="margin-top:1rem;">
      <div id="statusMessage">Enter text and choose Encode or Decode to get started.</div>
      <div id="errorMessage" role="alert" style="display:none;"></div>
      <div id="outputWrapper" style="display:none; margin-top:0.75rem;">
        <label for="outputText">Result</label>
        <textarea id="outputText" rows="8" readonly></textarea>
        <div style="display:flex; gap:0.75rem; justify-content:center; flex-wrap:wrap; margin-top:0.75rem;">
          <button type="button" id="copyButton" style="display:none;">Copy result</button>
        </div>
      </div>
      <div id="counts" style="margin-top:0.75rem;"></div>
    </div>
  </div>

  <script>
    (function () {
      const form = document.getElementById('urlToolForm');
      const modeSelect = document.getElementById('modeSelect');
      const input = document.getElementById('urlInput');
      const actionButton = document.getElementById('actionButton');
      const resetButton = document.getElementById('resetButton');
      const statusMessage = document.getElementById('statusMessage');
      const errorMessage = document.getElementById('errorMessage');
      const outputWrapper = document.getElementById('outputWrapper');
      const outputText = document.getElementById('outputText');
      const copyButton = document.getElementById('copyButton');
      const counts = document.getElementById('counts');

      function setError(message) {
        errorMessage.textContent = message;
        errorMessage.style.display = 'block';
      }

      function clearError() {
        errorMessage.textContent = '';
        errorMessage.style.display = 'none';
      }

      function updateActionLabel() {
        actionButton.textContent = modeSelect.value === 'encode' ? 'Encode' : 'Decode';
      }

      function updateCounts() {
        const inputLength = input.value.length;
        const outputLength = outputText.value.length;
        counts.textContent = outputWrapper.style.display === 'none'
          ? `Input characters: ${inputLength}`
          : `Input characters: ${inputLength} · Output characters: ${outputLength}`;
      }

      function hasMalformedPercentEncoding(value) {
        for (let i = 0; i < value.length; i++) {
          if (value[i] === '%') {
            const next1 = value[i + 1];
            const next2 = value[i + 2];
            if (!next1 || !next2 || !/[0-9A-Fa-f]/.test(next1) || !/[0-9A-Fa-f]/.test(next2)) {
              return true;
            }
          }
        }
        return false;
      }

      function processValue() {
        clearError();
        const raw = input.value;
        const trimmed = raw.trim();

        if (!trimmed) {
          outputWrapper.style.display = 'none';
          copyButton.style.display = 'none';
          statusMessage.textContent = 'Enter text and choose Encode or Decode to get started.';
          setError('Please enter a URL or text first.');
          updateCounts();
          return;
        }

        try {
          let result = '';
          if (modeSelect.value === 'encode') {
            result = encodeURIComponent(raw);
            statusMessage.textContent = 'Encoded result generated successfully.';
          } else {
            if (hasMalformedPercentEncoding(raw)) {
              throw new Error('Invalid percent-encoding detected.');
            }
            result = decodeURIComponent(raw);
            statusMessage.textContent = 'Decoded result generated successfully.';
          }

          outputText.value = result;
          outputWrapper.style.display = 'block';
          copyButton.style.display = 'inline-block';
          updateCounts();
        } catch (error) {
          outputWrapper.style.display = 'none';
          copyButton.style.display = 'none';
          outputText.value = '';
          statusMessage.textContent = 'Unable to process the input.';
          setError(modeSelect.value === 'decode'
            ? 'The input contains invalid percent-encoding. Please check the text and try again.'
            : 'The input could not be processed.');
          updateCounts();
        }
      }

      async function copyResult() {
        const value = outputText.value;
        if (!value) return;
        try {
          await navigator.clipboard.writeText(value);
          statusMessage.textContent = 'Result copied to clipboard.';
        } catch (error) {
          outputText.focus();
          outputText.select();
          document.execCommand('copy');
          statusMessage.textContent = 'Result copied to clipboard.';
        }
      }

      form.addEventListener('submit', function (event) {
        event.preventDefault();
        processValue();
      });

      modeSelect.addEventListener('change', updateActionLabel);
      input.addEventListener('input', updateCounts);
      copyButton.addEventListener('click', copyResult);

      resetButton.addEventListener('click', function () {
        input.value = '';
        outputText.value = '';
        clearError();
        outputWrapper.style.display = 'none';
        copyButton.style.display = 'none';
        statusMessage.textContent = 'Enter text and choose Encode or Decode to get started.';
        updateCounts();
        updateActionLabel();
      });

      updateActionLabel();
      updateCounts();
    })();
  </script>
</section>