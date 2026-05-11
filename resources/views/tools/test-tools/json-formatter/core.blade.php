<section class="tool-section">
  <div class="tool-card">
    <form class="tool-form" id="jsonFormatterForm">
      <label for="jsonInput">
        <span>JSON Input</span>
        <textarea id="jsonInput" rows="12" placeholder='Paste or type JSON here, for example: {"name":"John","age":30}' aria-describedby="jsonStatus jsonError"></textarea>
      </label>

      <div class="json-toolbar">
        <label for="indentSize">
          <span>Indentation Size</span>
          <select id="indentSize">
            <option value="2" selected>2 spaces</option>
            <option value="4">4 spaces</option>
          </select>
        </label>

        <div class="json-actions">
          <button type="submit" id="formatBtn">Format JSON</button>
          <button type="button" id="minifyBtn" class="secondary-btn">Minify JSON</button>
          <button type="button" id="copyBtn" class="secondary-btn" disabled>Copy Result</button>
          <button type="button" id="clearBtn" class="secondary-btn">Clear</button>
        </div>
      </div>
    </form>

    <div class="tool-result json-status-wrap" aria-live="polite">
      <div id="jsonStatus" class="json-status"></div>
      <div id="jsonError" class="json-error"></div>
    </div>

    <div class="json-counts" id="jsonCounts">
      <div><strong>Input:</strong> <span id="inputChars">0</span> chars, <span id="inputLines">0</span> lines</div>
      <div><strong>Output:</strong> <span id="outputChars">0</span> chars, <span id="outputLines">0</span> lines</div>
    </div>

    <div class="tool-form">
      <label for="jsonOutput">
        <span>Result</span>
        <textarea id="jsonOutput" rows="12" readonly placeholder="Formatted or minified JSON will appear here"></textarea>
      </label>
    </div>
  </div>

  <style>
    .tool-section .json-toolbar{display:grid;grid-template-columns:minmax(180px,220px) 1fr;gap:1rem;align-items:end}
    .tool-section .json-actions{display:grid;grid-template-columns:repeat(4,1fr);gap:0.75rem}
    .tool-section .secondary-btn{background-color:var(--input-bg);color:var(--text-color);border:1px solid var(--input-border)}
    .tool-section .secondary-btn:hover{background-color:var(--card-bg)}
    .tool-section button:disabled{opacity:.6;cursor:not-allowed}
    .tool-section .json-status-wrap{text-align:left;margin-top:1rem}
    .tool-section .json-status{font-weight:600}
    .tool-section .json-status.valid{color:#1f9d55}
    .tool-section .json-status.invalid{color:#d64545}
    .tool-section .json-error{margin-top:0.35rem;color:#d64545;word-break:break-word}
    .tool-section .json-counts{display:grid;grid-template-columns:repeat(2,1fr);gap:0.75rem;margin:1rem 0;font-size:.95rem}
    .tool-section #jsonInput.invalid-input{border-color:#d64545}
    .tool-section #jsonOutput{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace}
    .tool-section #jsonInput{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace}
    @media (max-width:700px){
      .tool-section .json-toolbar{grid-template-columns:1fr}
      .tool-section .json-actions{grid-template-columns:repeat(2,1fr)}
      .tool-section .json-counts{grid-template-columns:1fr}
    }
  </style>

  <script>
    (function () {
      var form = document.getElementById('jsonFormatterForm');
      var jsonInput = document.getElementById('jsonInput');
      var indentSize = document.getElementById('indentSize');
      var jsonOutput = document.getElementById('jsonOutput');
      var minifyBtn = document.getElementById('minifyBtn');
      var copyBtn = document.getElementById('copyBtn');
      var clearBtn = document.getElementById('clearBtn');
      var jsonStatus = document.getElementById('jsonStatus');
      var jsonError = document.getElementById('jsonError');
      var inputChars = document.getElementById('inputChars');
      var inputLines = document.getElementById('inputLines');
      var outputChars = document.getElementById('outputChars');
      var outputLines = document.getElementById('outputLines');

      function countLines(text) {
        if (!text) return 0;
        return text.split(/\r\n|\r|\n/).length;
      }

      function updateCounts() {
        var inputText = jsonInput.value || '';
        var outputText = jsonOutput.value || '';
        inputChars.textContent = inputText.length;
        inputLines.textContent = countLines(inputText);
        outputChars.textContent = outputText.length;
        outputLines.textContent = countLines(outputText);
      }

      function setStatus(type, message, errorMessage) {
        jsonStatus.className = 'json-status' + (type ? ' ' + type : '');
        jsonStatus.textContent = message || '';
        jsonError.textContent = errorMessage || '';
      }

      function clearErrorState() {
        jsonInput.classList.remove('invalid-input');
      }

      function setInvalid(message, errorMessage) {
        jsonInput.classList.add('invalid-input');
        setStatus('invalid', message, errorMessage);
      }

      function setValid(message) {
        clearErrorState();
        setStatus('valid', message, '');
      }

      function getIndent() {
        var value = parseInt(indentSize.value, 10);
        return isFinite(value) && value > 0 ? value : 2;
      }

      function processJson(mode) {
        var raw = jsonInput.value;

        if (!raw || raw.trim() === '') {
          jsonOutput.value = '';
          setInvalid('Invalid JSON', 'Please enter JSON to format or minify.');
          copyBtn.disabled = true;
          updateCounts();
          return;
        }

        try {
          var parsed = JSON.parse(raw);
          var result = mode === 'minify' ? JSON.stringify(parsed) : JSON.stringify(parsed, null, getIndent());
          jsonOutput.value = result;
          setValid('Valid JSON');
          copyBtn.disabled = false;
        } catch (error) {
          jsonOutput.value = '';
          setInvalid('Invalid JSON', error && error.message ? error.message : 'Unable to parse JSON.');
          copyBtn.disabled = true;
        }

        updateCounts();
      }

      form.addEventListener('submit', function (event) {
        event.preventDefault();
        processJson('format');
      });

      minifyBtn.addEventListener('click', function () {
        processJson('minify');
      });

      copyBtn.addEventListener('click', async function () {
        var text = jsonOutput.value;
        if (!text) {
          setStatus('invalid', 'No result available', 'Format or minify JSON before copying.');
          copyBtn.disabled = true;
          return;
        }

        try {
          if (navigator.clipboard && navigator.clipboard.writeText) {
            await navigator.clipboard.writeText(text);
          } else {
            jsonOutput.focus();
            jsonOutput.select();
            document.execCommand('copy');
          }
          setStatus('valid', 'Result copied', '');
        } catch (error) {
          setStatus('invalid', 'Copy failed', 'Your browser could not copy the result automatically.');
        }
      });

      clearBtn.addEventListener('click', function () {
        jsonInput.value = '';
        jsonOutput.value = '';
        indentSize.value = '2';
        clearErrorState();
        setStatus('', '', '');
        copyBtn.disabled = true;
        updateCounts();
        jsonInput.focus();
      });

      jsonInput.addEventListener('input', function () {
        clearErrorState();
        if (!jsonInput.value.trim()) {
          jsonOutput.value = '';
          copyBtn.disabled = true;
          setStatus('', '', '');
        }
        updateCounts();
      });

      updateCounts();
    })();
  </script>
</section>