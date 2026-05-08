<section class="tool-section">
  <div class="tool-card">
    <form class="tool-form" id="caseConverterForm">
      <label for="sourceText">Text to convert</label>
      <textarea id="sourceText" name="sourceText" rows="8" placeholder="Enter or paste your text here" required></textarea>

      <label for="caseType">Conversion type</label>
      <select id="caseType" name="caseType" required>
        <option value="uppercase">Uppercase</option>
        <option value="lowercase">Lowercase</option>
        <option value="title">Title Case</option>
        <option value="sentence">Sentence case</option>
      </select>

      <label class="checkbox-label" for="autoConvert">
        <input class="checkbox-input" type="checkbox" id="autoConvert" name="autoConvert">
        <span class="checkbox-box" aria-hidden="true"></span>
        <span>Auto-convert</span>
      </label>

      <div class="tool-actions" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:0.75rem;">
        <button type="submit" id="convertBtn">Convert</button>
        <button type="button" id="copyBtn">Copy result</button>
        <button type="button" id="clearBtn">Clear</button>
      </div>
    </form>

    <div class="tool-result" style="margin-top:1rem;">
      <label for="resultText">Converted text</label>
      <textarea id="resultText" rows="8" readonly placeholder="Your converted text will appear here"></textarea>
      <div id="stats" style="display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-top:0.75rem;font-size:0.95rem;">
        <span id="inputCount">Input characters: 0</span>
        <span id="outputCount">Output characters: 0</span>
        <span id="caseLabel">Selected case: Uppercase</span>
      </div>
      <div id="statusMessage" aria-live="polite" style="margin-top:0.75rem;min-height:1.5em;"></div>
    </div>
  </div>

  <script>
    (function () {
      const form = document.getElementById('caseConverterForm');
      const sourceText = document.getElementById('sourceText');
      const caseType = document.getElementById('caseType');
      const autoConvert = document.getElementById('autoConvert');
      const resultText = document.getElementById('resultText');
      const copyBtn = document.getElementById('copyBtn');
      const clearBtn = document.getElementById('clearBtn');
      const statusMessage = document.getElementById('statusMessage');
      const inputCount = document.getElementById('inputCount');
      const outputCount = document.getElementById('outputCount');
      const caseLabel = document.getElementById('caseLabel');

      function setStatus(message, isError) {
        statusMessage.textContent = message;
        statusMessage.style.color = isError ? 'var(--error, #b00020)' : 'var(--success, #1b7f3a)';
      }

      function clearStatus() {
        statusMessage.textContent = '';
      }

      function updateCounts() {
        inputCount.textContent = 'Input characters: ' + sourceText.value.length;
        outputCount.textContent = 'Output characters: ' + resultText.value.length;
      }

      function getCaseLabel(value) {
        switch (value) {
          case 'uppercase': return 'Uppercase';
          case 'lowercase': return 'Lowercase';
          case 'title': return 'Title Case';
          case 'sentence': return 'Sentence case';
          default: return 'Uppercase';
        }
      }

      function toTitleCase(text) {
        return text.replace(/\S+/g, function (word) {
          return word.replace(/(^[A-Za-zÀ-ÿ])|([A-Za-zÀ-ÿ](?=[^A-Za-zÀ-ÿ]*$))/g, function (match, p1) {
            return match.toUpperCase();
          }).replace(/([A-Za-zÀ-ÿ])([A-Za-zÀ-ÿ]*)/g, function (match, first, rest) {
            return first.toUpperCase() + rest.toLowerCase();
          });
        });
      }

      function toSentenceCase(text) {
        const lower = text.toLowerCase();
        let result = '';
        let capitalizeNext = true;
        for (let i = 0; i < lower.length; i++) {
          const char = lower[i];
          if (capitalizeNext && /[A-Za-zÀ-ÿ]/.test(char)) {
            result += char.toUpperCase();
            capitalizeNext = false;
          } else {
            result += char;
          }
          if (/[.!?]/.test(char)) {
            capitalizeNext = true;
          } else if (!/\s/.test(char) && /[A-Za-zÀ-ÿ]/.test(char)) {
            capitalizeNext = false;
          }
        }
        return result;
      }

      function convertText() {
        const text = sourceText.value;
        if (!text.trim()) {
          resultText.value = '';
          updateCounts();
          setStatus('Please enter some text to convert.', true);
          return;
        }

        let output = text;
        const type = caseType.value;

        if (type === 'uppercase') output = text.toUpperCase();
        else if (type === 'lowercase') output = text.toLowerCase();
        else if (type === 'title') output = toTitleCase(text);
        else if (type === 'sentence') output = toSentenceCase(text);

        resultText.value = output;
        updateCounts();
        clearStatus();
      }

      form.addEventListener('submit', function (e) {
        e.preventDefault();
        convertText();
      });

      sourceText.addEventListener('input', function () {
        updateCounts();
        if (autoConvert.checked) convertText();
      });

      caseType.addEventListener('change', function () {
        caseLabel.textContent = 'Selected case: ' + getCaseLabel(caseType.value);
        if (autoConvert.checked) convertText();
      });

      autoConvert.addEventListener('change', function () {
        if (autoConvert.checked) convertText();
      });

      copyBtn.addEventListener('click', async function () {
        const text = resultText.value;
        if (!text) {
          setStatus('Nothing to copy. Convert some text first.', true);
          return;
        }

        try {
          await navigator.clipboard.writeText(text);
          setStatus('Converted text copied to clipboard.');
        } catch (error) {
          setStatus('Copy failed. Your browser may have blocked clipboard access.', true);
        }
      });

      clearBtn.addEventListener('click', function () {
        sourceText.value = '';
        resultText.value = '';
        caseType.value = 'uppercase';
        autoConvert.checked = false;
        caseLabel.textContent = 'Selected case: Uppercase';
        updateCounts();
        clearStatus();
        sourceText.focus();
      });

      updateCounts();
      caseLabel.textContent = 'Selected case: Uppercase';
    })();
  </script>
</section>