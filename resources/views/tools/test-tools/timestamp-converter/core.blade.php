<section class="tool-section">
  <div class="tool-card">
    <form class="tool-form" id="timestampConverterForm">
      <div>
        <label for="modeSelect">Conversion mode</label>
        <select id="modeSelect" name="mode">
          <option value="timestamp-to-date">Timestamp to Date</option>
          <option value="date-to-timestamp">Date to Timestamp</option>
        </select>
      </div>

      <div id="timestampInputGroup">
        <label for="timestampInput">Unix timestamp</label>
        <input id="timestampInput" name="timestamp" type="text" inputmode="numeric" placeholder="e.g. 1704067200 or 1704067200000" autocomplete="off">
      </div>

      <div id="dateInputGroup" hidden>
        <label for="dateInput">Date and time</label>
        <input id="dateInput" name="datetime" type="datetime-local">
      </div>

      <div id="timestampOptions">
        <label for="unitSelect">Timestamp unit</label>
        <select id="unitSelect" name="unit">
          <option value="auto">Auto-detect</option>
          <option value="seconds">Seconds</option>
          <option value="milliseconds">Milliseconds</option>
        </select>
      </div>

      <div id="dateOptions" hidden>
        <label for="timezoneSelect">Timezone interpretation</label>
        <select id="timezoneSelect" name="timezone">
          <option value="local">Local time</option>
          <option value="utc">UTC</option>
        </select>
      </div>

      <label class="checkbox-label" for="autoDetect">
        <input class="checkbox-input" id="autoDetect" type="checkbox" checked>
        <span class="checkbox-box"></span>
        <span>Auto-detect seconds or milliseconds</span>
      </label>

      <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:0.75rem;">
        <button type="submit" id="convertBtn">Convert</button>
        <button type="button" id="swapBtn">Swap</button>
        <button type="button" id="clearBtn">Clear</button>
      </div>
    </form>

    <div class="tool-result" id="resultBlock" aria-live="polite" style="margin-top:1rem;">
      <div id="resultMessage">Enter a value and click Convert to see the result.</div>
      <div id="resultDetails" hidden style="margin-top:0.75rem;text-align:left;display:grid;gap:0.5rem;"></div>
    </div>
  </div>

  <script>
    (function () {
      const form = document.getElementById('timestampConverterForm');
      const modeSelect = document.getElementById('modeSelect');
      const timestampInput = document.getElementById('timestampInput');
      const dateInput = document.getElementById('dateInput');
      const unitSelect = document.getElementById('unitSelect');
      const timezoneSelect = document.getElementById('timezoneSelect');
      const autoDetect = document.getElementById('autoDetect');
      const timestampInputGroup = document.getElementById('timestampInputGroup');
      const dateInputGroup = document.getElementById('dateInputGroup');
      const timestampOptions = document.getElementById('timestampOptions');
      const dateOptions = document.getElementById('dateOptions');
      const resultMessage = document.getElementById('resultMessage');
      const resultDetails = document.getElementById('resultDetails');
      const swapBtn = document.getElementById('swapBtn');
      const clearBtn = document.getElementById('clearBtn');

      function pad(num) {
        return String(num).padStart(2, '0');
      }

      function isValidDate(date) {
        return date instanceof Date && !Number.isNaN(date.getTime());
      }

      function setError(message) {
        resultMessage.textContent = message;
        resultDetails.hidden = true;
        resultDetails.innerHTML = '';
      }

      function setResult(main, details) {
        resultMessage.textContent = main;
        resultDetails.innerHTML = details.map(item => '<div><strong>' + item.label + ':</strong> ' + item.value + '</div>').join('');
        resultDetails.hidden = false;
      }

      function formatLocal(date) {
        return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate()) + ' ' + pad(date.getHours()) + ':' + pad(date.getMinutes()) + ':' + pad(date.getSeconds());
      }

      function formatUTC(date) {
        return date.getUTCFullYear() + '-' + pad(date.getUTCMonth() + 1) + '-' + pad(date.getUTCDate()) + ' ' + pad(date.getUTCHours()) + ':' + pad(date.getUTCMinutes()) + ':' + pad(date.getUTCSeconds());
      }

      function getWeekday(date, utc) {
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        return utc ? days[date.getUTCDay()] : days[date.getDay()];
      }

      function updateModeUI() {
        const timestampMode = modeSelect.value === 'timestamp-to-date';
        timestampInputGroup.hidden = !timestampMode;
        timestampOptions.hidden = !timestampMode;
        dateInputGroup.hidden = timestampMode;
        dateOptions.hidden = timestampMode;
      }

      function detectUnit(value) {
        const abs = Math.abs(value);
        if (abs >= 1e12) return 'milliseconds';
        return 'seconds';
      }

      function convertTimestampToDate() {
        const raw = timestampInput.value.trim();
        if (!raw) {
          setError('Please enter a Unix timestamp.');
          return;
        }
        if (!/^[+-]?\d+(\.\d+)?$/.test(raw)) {
          setError('The timestamp must be numeric.');
          return;
        }

        const numeric = Number(raw);
        if (!Number.isFinite(numeric)) {
          setError('The timestamp value is too large or invalid.');
          return;
        }

        let unit = unitSelect.value;
        if (autoDetect.checked || unit === 'auto') {
          unit = detectUnit(numeric);
        }

        let msValue = unit === 'seconds' ? numeric * 1000 : numeric;
        if (!Number.isFinite(msValue) || Math.abs(msValue) > 8.64e15) {
          setError('The resulting date is outside the supported JavaScript date range.');
          return;
        }

        const date = new Date(msValue);
        if (!isValidDate(date)) {
          setError('The timestamp could not be converted to a valid date.');
          return;
        }

        const utc = timezoneSelect.value === 'utc';
        const readable = utc ? formatUTC(date) + ' UTC' : formatLocal(date) + ' local time';
        const iso = date.toISOString();
        const seconds = Math.floor(date.getTime() / 1000);
        const milliseconds = date.getTime();

        setResult('Converted date: ' + readable, [
          { label: 'Readable date', value: readable },
          { label: 'ISO 8601', value: iso },
          { label: 'Unix seconds', value: String(seconds) },
          { label: 'Unix milliseconds', value: String(milliseconds) },
          { label: 'Weekday', value: getWeekday(date, utc) },
          { label: 'Timezone', value: utc ? 'UTC' : 'Local system time' }
        ]);

        dateInput.value = utc
          ? new Date(date.getTime() - date.getTimezoneOffset() * 60000).toISOString().slice(0, 16)
          : new Date(date.getTime()).toISOString().slice(0, 16);
      }

      function convertDateToTimestamp() {
        const raw = dateInput.value.trim();
        if (!raw) {
          setError('Please enter a date and time.');
          return;
        }

        let date;
        if (timezoneSelect.value === 'utc') {
          date = new Date(raw + 'Z');
        } else {
          date = new Date(raw);
        }

        if (!isValidDate(date)) {
          setError('The date/time value is invalid or could not be parsed.');
          return;
        }

        const ms = date.getTime();
        if (!Number.isFinite(ms) || Math.abs(ms) > 8.64e15) {
          setError('The resulting timestamp is outside the supported JavaScript date range.');
          return;
        }

        const seconds = Math.floor(ms / 1000);
        const milliseconds = ms;
        const displayDate = timezoneSelect.value === 'utc' ? formatUTC(date) + ' UTC' : formatLocal(date) + ' local time';

        setResult('Converted timestamp: ' + milliseconds + ' ms', [
          { label: 'Selected date/time', value: displayDate },
          { label: 'Unix milliseconds', value: String(milliseconds) },
          { label: 'Unix seconds', value: String(seconds) },
          { label: 'ISO 8601', value: date.toISOString() },
          { label: 'Timezone', value: timezoneSelect.value === 'utc' ? 'UTC' : 'Local system time' }
        ]);

        timestampInput.value = String(milliseconds);
      }

      form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (modeSelect.value === 'timestamp-to-date') {
          convertTimestampToDate();
        } else {
          convertDateToTimestamp();
        }
      });

      modeSelect.addEventListener('change', function () {
        updateModeUI();
      });

      autoDetect.addEventListener('change', function () {
        if (autoDetect.checked) {
          unitSelect.value = 'auto';
        } else if (unitSelect.value === 'auto') {
          unitSelect.value = 'seconds';
        }
      });

      unitSelect.addEventListener('change', function () {
        if (unitSelect.value === 'auto') {
          autoDetect.checked = true;
        } else {
          autoDetect.checked = false;
        }
      });

      swapBtn.addEventListener('click', function () {
        const currentMode = modeSelect.value;
        if (currentMode === 'timestamp-to-date') {
          const raw = timestampInput.value.trim();
          if (raw) {
            if (/^[+-]?\d+(\.\d+)?$/.test(raw)) {
              const numeric = Number(raw);
              const unit = autoDetect.checked || unitSelect.value === 'auto' ? detectUnit(numeric) : unitSelect.value;
              const msValue = unit === 'seconds' ? numeric * 1000 : numeric;
              if (Number.isFinite(msValue) && Math.abs(msValue) <= 8.64e15) {
                const d = new Date(msValue);
                if (isValidDate(d)) {
                  dateInput.value = new Date(d.getTime() - d.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
                }
              }
            }
          }
          modeSelect.value = 'date-to-timestamp';
        } else {
          const raw = dateInput.value.trim();
          if (raw) {
            const d = timezoneSelect.value === 'utc' ? new Date(raw + 'Z') : new Date(raw);
            if (isValidDate(d)) {
              timestampInput.value = String(d.getTime());
            }
          }
          modeSelect.value = 'timestamp-to-date';
        }
        updateModeUI();
      });

      clearBtn.addEventListener('click', function () {
        timestampInput.value = '';
        dateInput.value = '';
        unitSelect.value = 'auto';
        autoDetect.checked = true;
        timezoneSelect.value = 'local';
        modeSelect.value = 'timestamp-to-date';
        updateModeUI();
        setError('Enter a value and click Convert to see the result.');
      });

      updateModeUI();
    })();
  </script>
</section>