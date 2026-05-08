<section class="tool-section">
  <div class="tool-card">
    <form class="tool-form" id="jsonToCsvForm">
      <label for="jsonInput">JSON Input</label>
      <textarea id="jsonInput" rows="12" placeholder='Paste a JSON array or object here...'></textarea>

      <div class="tool-options" style="display:grid;gap:1rem;">
        <label for="delimiterSelect">Delimiter</label>
        <select id="delimiterSelect">
          <option value=",">Comma (,)</option>
          <option value=";">Semicolon (;)</option>
          <option value="\t">Tab</option>
        </select>

        <label class="checkbox-label" for="includeHeader">
          <input class="checkbox-input" type="checkbox" id="includeHeader" checked>
          <span class="checkbox-box"></span>
          <span>Include Header Row</span>
        </label>

        <label class="checkbox-label" for="trimValues">
          <input class="checkbox-input" type="checkbox" id="trimValues" checked>
          <span class="checkbox-box"></span>
          <span>Trim Values</span>
        </label>

        <label class="checkbox-label" for="flattenNested">
          <input class="checkbox-input" type="checkbox" id="flattenNested" checked>
          <span class="checkbox-box"></span>
          <span>Flatten Nested Objects</span>
        </label>

        <label class="checkbox-label" for="arraysToStrings">
          <input class="checkbox-input" type="checkbox" id="arraysToStrings" checked>
          <span class="checkbox-box"></span>
          <span>Convert Arrays to Strings</span>
        </label>
      </div>

      <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0.75rem;">
        <button type="submit" id="convertBtn">Convert</button>
        <button type="button" id="clearBtn">Clear</button>
      </div>
    </form>
  </div>

  <div class="tool-card tool-result" style="margin-top:1rem;">
    <div id="statusMessage" role="status" aria-live="polite"></div>

    <div id="summaryBlock" style="display:grid;gap:0.75rem;margin-top:1rem;text-align:left;">
      <div><strong>Rows:</strong> <span id="rowCount">0</span></div>
      <div><strong>Columns:</strong> <span id="columnCount">0</span></div>
      <div><strong>Detected Headers:</strong> <span id="headersList">None</span></div>
    </div>

    <div style="display:grid;gap:0.75rem;margin-top:1rem;text-align:left;">
      <label for="csvOutput">CSV Output</label>
      <textarea id="csvOutput" rows="12" readonly placeholder="Converted CSV will appear here..."></textarea>
    </div>

    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0.75rem;margin-top:1rem;">
      <button type="button" id="copyBtn" disabled>Copy CSV</button>
      <button type="button" id="downloadBtn" disabled>Download CSV</button>
    </div>
  </div>
</section>

<script>
(function () {
  const form = document.getElementById('jsonToCsvForm');
  const jsonInput = document.getElementById('jsonInput');
  const delimiterSelect = document.getElementById('delimiterSelect');
  const includeHeader = document.getElementById('includeHeader');
  const trimValues = document.getElementById('trimValues');
  const flattenNested = document.getElementById('flattenNested');
  const arraysToStrings = document.getElementById('arraysToStrings');
  const csvOutput = document.getElementById('csvOutput');
  const statusMessage = document.getElementById('statusMessage');
  const rowCount = document.getElementById('rowCount');
  const columnCount = document.getElementById('columnCount');
  const headersList = document.getElementById('headersList');
  const copyBtn = document.getElementById('copyBtn');
  const downloadBtn = document.getElementById('downloadBtn');
  const clearBtn = document.getElementById('clearBtn');

  let lastCsv = '';
  let lastFileName = 'converted.csv';

  function setStatus(message, isError) {
    statusMessage.textContent = message;
    statusMessage.style.color = isError ? '#c62828' : 'inherit';
  }

  function normalizeDelimiter(value) {
    return value === '\\t' ? '\t' : value;
  }

  function escapeCsvValue(value, delimiter) {
    const str = String(value);
    const needsQuotes = str.includes('"') || str.includes('\n') || str.includes('\r') || str.includes(delimiter);
    const escaped = str.replace(/"/g, '""');
    return needsQuotes ? '"' + escaped + '"' : escaped;
  }

  function isPlainObject(value) {
    return Object.prototype.toString.call(value) === '[object Object]';
  }

  function flattenObject(obj, prefix, out, trim, arraysAsStrings) {
    Object.keys(obj).forEach((key) => {
      const fullKey = prefix ? prefix + '.' + key : key;
      const value = obj[key];

      if (value === null || value === undefined) {
        out[fullKey] = '';
        return;
      }

      if (Array.isArray(value)) {
        if (arraysAsStrings) {
          const mapped = value.map((item) => {
            if (item === null || item === undefined) return '';
            if (isPlainObject(item) || Array.isArray(item)) return JSON.stringify(item);
            let v = String(item);
            return trim && typeof item === 'string' ? v.trim() : v;
          });
          out[fullKey] = mapped.join(', ');
        } else {
          out[fullKey] = JSON.stringify(value);
        }
        return;
      }

      if (isPlainObject(value)) {
        flattenObject(value, fullKey, out, trim, arraysAsStrings);
        return;
      }

      let finalValue = String(value);
      if (trim && typeof value === 'string') finalValue = finalValue.trim();
      out[fullKey] = finalValue;
    });
    return out;
  }

  function convertValue(value, trim, arraysAsStrings) {
    if (value === null || value === undefined) return '';
    if (Array.isArray(value)) {
      if (arraysAsStrings) {
        return value.map((item) => {
          if (item === null || item === undefined) return '';
          if (isPlainObject(item) || Array.isArray(item)) return JSON.stringify(item);
          let v = String(item);
          return trim && typeof item === 'string' ? v.trim() : v;
        }).join(', ');
      }
      return JSON.stringify(value);
    }
    if (isPlainObject(value)) return JSON.stringify(value);
    let result = String(value);
    if (trim && typeof value === 'string') result = result.trim();
    return result;
  }

  function uniqueHeaders(headers) {
    const seen = new Map();
    return headers.map((header) => {
      if (!seen.has(header)) {
        seen.set(header, 1);
        return header;
      }
      const count = seen.get(header) + 1;
      seen.set(header, count);
      return header + '_' + count;
    });
  }

  function convertToRows(parsed, options) {
    const items = Array.isArray(parsed) ? parsed : [parsed];
    const rows = [];
    const headers = [];

    items.forEach((item, index) => {
      let rowObj;
      if (isPlainObject(item)) {
        rowObj = options.flattenNested ? flattenObject(item, '', {}, options.trimValues, options.arraysToStrings) : Object.keys(item).reduce((acc, key) => {
          acc[key] = convertValue(item[key], options.trimValues, options.arraysToStrings);
          return acc;
        }, {});
      } else {
        rowObj = { value: convertValue(item, options.trimValues, options.arraysToStrings) };
      }

      Object.keys(rowObj).forEach((key) => {
        if (!headers.includes(key)) headers.push(key);
      });
      rows.push(rowObj);
    });

    const finalHeaders = uniqueHeaders(headers);
    const csvRows = [];

    if (options.includeHeader) {
      csvRows.push(finalHeaders.map((h) => escapeCsvValue(h, options.delimiter)).join(options.delimiter));
    }

    rows.forEach((row) => {
      const line = finalHeaders.map((header) => escapeCsvValue(row[header] !== undefined ? row[header] : '', options.delimiter)).join(options.delimiter);
      csvRows.push(line);
    });

    return { csv: csvRows.join('\n'), rows: rows.length, columns: finalHeaders.length, headers: finalHeaders };
  }

  function parseJsonInput() {
    const raw = jsonInput.value.trim();
    if (!raw) {
      throw new Error('Please paste JSON data to convert.');
    }
    return JSON.parse(raw);
  }

  function updateSummary(rows, columns, headers) {
    rowCount.textContent = String(rows);
    columnCount.textContent = String(columns);
    headersList.textContent = headers.length ? headers.join(', ') : 'None';
  }

  function setOutput(csv) {
    csvOutput.value = csv;
    lastCsv = csv;
    copyBtn.disabled = !csv;
    downloadBtn.disabled = !csv;
  }

  function convert() {
    try {
      const parsed = parseJsonInput();
      const options = {
        delimiter: normalizeDelimiter(delimiterSelect.value),
        includeHeader: includeHeader.checked,
        trimValues: trimValues.checked,
        flattenNested: flattenNested.checked,
        arraysToStrings: arraysToStrings.checked
      };

      if (!Array.isArray(parsed) && !isPlainObject(parsed)) {
        throw new Error('Input must be a JSON object or an array of objects.');
      }

      const result = convertToRows(parsed, options);
      setOutput(result.csv);
      updateSummary(result.rows, result.columns, result.headers);
      setStatus('Conversion completed successfully.', false);
      lastFileName = 'converted.csv';
    } catch (error) {
      setOutput('');
      updateSummary(0, 0, []);
      setStatus(error.message || 'Unable to convert JSON.', true);
    }
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    convert();
  });

  clearBtn.addEventListener('click', function () {
    jsonInput.value = '';
    csvOutput.value = '';
    rowCount.textContent = '0';
    columnCount.textContent = '0';
    headersList.textContent = 'None';
    setStatus('', false);
    lastCsv = '';
    copyBtn.disabled = true;
    downloadBtn.disabled = true;
    jsonInput.focus();
  });

  copyBtn.addEventListener('click', async function () {
    if (!lastCsv) return;
    try {
      await navigator.clipboard.writeText(lastCsv);
      setStatus('CSV copied to clipboard.', false);
    } catch (error) {
      setStatus('Unable to copy CSV to clipboard.', true);
    }
  });

  downloadBtn.addEventListener('click', function () {
    if (!lastCsv) return;
    const blob = new Blob([lastCsv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = lastFileName;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    setStatus('CSV download started.', false);
  });
})();
</script>