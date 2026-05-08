<section class="tool-section">
  <div class="tool-card">
    <form class="tool-form" id="passwordGeneratorForm">
      <label for="passwordLength">
        Password length
        <input id="passwordLength" name="passwordLength" type="number" min="4" max="128" value="16" inputmode="numeric" required>
      </label>

      <div class="tool-options" role="group" aria-label="Character options">
        <label class="checkbox-label" for="includeUppercase">
          <input class="checkbox-input" id="includeUppercase" name="includeUppercase" type="checkbox" checked>
          <span class="checkbox-box" aria-hidden="true"></span>
          <span>Include uppercase letters</span>
        </label>

        <label class="checkbox-label" for="includeLowercase">
          <input class="checkbox-input" id="includeLowercase" name="includeLowercase" type="checkbox" checked>
          <span class="checkbox-box" aria-hidden="true"></span>
          <span>Include lowercase letters</span>
        </label>

        <label class="checkbox-label" for="includeNumbers">
          <input class="checkbox-input" id="includeNumbers" name="includeNumbers" type="checkbox" checked>
          <span class="checkbox-box" aria-hidden="true"></span>
          <span>Include numbers</span>
        </label>

        <label class="checkbox-label" for="includeSymbols">
          <input class="checkbox-input" id="includeSymbols" name="includeSymbols" type="checkbox">
          <span class="checkbox-box" aria-hidden="true"></span>
          <span>Include symbols</span>
        </label>

        <label class="checkbox-label" for="excludeAmbiguous">
          <input class="checkbox-input" id="excludeAmbiguous" name="excludeAmbiguous" type="checkbox">
          <span class="checkbox-box" aria-hidden="true"></span>
          <span>Exclude ambiguous characters</span>
        </label>
      </div>

      <button type="submit">Generate password</button>
    </form>

    <div class="tool-result" id="passwordResult" aria-live="polite">
      <div id="statusMessage">Generate a secure password to see the result here.</div>
      <div style="margin-top:1rem;">
        <label for="generatedPassword" style="text-align:left;">
          Generated password
          <textarea id="generatedPassword" rows="3" readonly spellcheck="false" placeholder="Your password will appear here"></textarea>
        </label>
      </div>
      <div style="display:grid;gap:0.75rem;margin-top:1rem;text-align:left;">
        <div><strong>Length:</strong> <span id="resultLength">16</span></div>
        <div><strong>Categories:</strong> <span id="resultCategories">Uppercase, lowercase, numbers</span></div>
        <div><strong>Ambiguous characters:</strong> <span id="resultAmbiguous">Included</span></div>
        <div><strong>Strength:</strong> <span id="resultStrength">Medium</span></div>
      </div>
      <button type="button" id="copyPasswordButton" style="margin-top:1rem;">Copy password</button>
    </div>
  </div>

  <script>
    (function () {
      const form = document.getElementById('passwordGeneratorForm');
      const lengthInput = document.getElementById('passwordLength');
      const uppercaseInput = document.getElementById('includeUppercase');
      const lowercaseInput = document.getElementById('includeLowercase');
      const numbersInput = document.getElementById('includeNumbers');
      const symbolsInput = document.getElementById('includeSymbols');
      const ambiguousInput = document.getElementById('excludeAmbiguous');
      const passwordOutput = document.getElementById('generatedPassword');
      const statusMessage = document.getElementById('statusMessage');
      const resultLength = document.getElementById('resultLength');
      const resultCategories = document.getElementById('resultCategories');
      const resultAmbiguous = document.getElementById('resultAmbiguous');
      const resultStrength = document.getElementById('resultStrength');
      const copyButton = document.getElementById('copyPasswordButton');

      const sets = {
        uppercase: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        lowercase: 'abcdefghijklmnopqrstuvwxyz',
        numbers: '0123456789',
        symbols: '!@#$%^&*()-_=+[]{}|;:,.<>?/~'
      };

      const ambiguousChars = new Set(['O', '0', 'I', 'l', '1']);

      function clampLength(value) {
        const parsed = Number.parseInt(value, 10);
        if (Number.isNaN(parsed)) return 16;
        return Math.min(128, Math.max(4, parsed));
      }

      function getFilteredSet(chars, excludeAmbiguous) {
        if (!excludeAmbiguous) return chars;
        return Array.from(chars).filter((ch) => !ambiguousChars.has(ch)).join('');
      }

      function getSelectedCategories(excludeAmbiguous) {
        const selected = [];
        const poolParts = [];

        if (uppercaseInput.checked) {
          const chars = getFilteredSet(sets.uppercase, excludeAmbiguous);
          if (chars) {
            selected.push({ name: 'Uppercase', chars });
            poolParts.push(chars);
          }
        }
        if (lowercaseInput.checked) {
          const chars = getFilteredSet(sets.lowercase, excludeAmbiguous);
          if (chars) {
            selected.push({ name: 'Lowercase', chars });
            poolParts.push(chars);
          }
        }
        if (numbersInput.checked) {
          const chars = getFilteredSet(sets.numbers, excludeAmbiguous);
          if (chars) {
            selected.push({ name: 'Numbers', chars });
            poolParts.push(chars);
          }
        }
        if (symbolsInput.checked) {
          const chars = getFilteredSet(sets.symbols, excludeAmbiguous);
          if (chars) {
            selected.push({ name: 'Symbols', chars });
            poolParts.push(chars);
          }
        }

        return { selected, pool: poolParts.join('') };
      }

      function randomInt(max) {
        const array = new Uint32Array(1);
        if (window.crypto && window.crypto.getRandomValues) {
          window.crypto.getRandomValues(array);
          return array[0] % max;
        }
        return Math.floor(Math.random() * max);
      }

      function shuffle(array) {
        for (let i = array.length - 1; i > 0; i--) {
          const j = randomInt(i + 1);
          [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
      }

      function generatePassword(length, selectedCategories, pool) {
        const chars = [];
        selectedCategories.forEach((category) => {
          chars.push(category.chars[randomInt(category.chars.length)]);
        });

        while (chars.length < length) {
          chars.push(pool[randomInt(pool.length)]);
        }

        return shuffle(chars).join('');
      }

      function getStrengthLabel(length, categoryCount) {
        if (length >= 20 && categoryCount >= 4) return 'Very strong';
        if (length >= 16 && categoryCount >= 3) return 'Strong';
        if (length >= 12 && categoryCount >= 2) return 'Medium';
        return 'Basic';
      }

      function updateResult(password, length, selectedCategories, excludeAmbiguous) {
        passwordOutput.value = password;
        resultLength.textContent = String(length);
        resultCategories.textContent = selectedCategories.map((item) => item.name).join(', ');
        resultAmbiguous.textContent = excludeAmbiguous ? 'Excluded' : 'Included';
        resultStrength.textContent = getStrengthLabel(length, selectedCategories.length);
      }

      function setStatus(message, isError) {
        statusMessage.textContent = message;
        statusMessage.style.color = isError ? 'var(--accent)' : '';
      }

      form.addEventListener('submit', function (event) {
        event.preventDefault();

        const length = clampLength(lengthInput.value);
        lengthInput.value = String(length);

        const excludeAmbiguous = ambiguousInput.checked;
        const { selected, pool } = getSelectedCategories(excludeAmbiguous);

        if (selected.length === 0) {
          passwordOutput.value = '';
          setStatus('Please select at least one character type.', true);
          return;
        }

        if (!pool) {
          passwordOutput.value = '';
          setStatus('The selected options do not contain any usable characters. Please change your settings.', true);
          return;
        }

        if (length < selected.length) {
          passwordOutput.value = '';
          setStatus('Password length must be at least the number of selected character types.', true);
          return;
        }

        const password = generatePassword(length, selected, pool);
        updateResult(password, length, selected, excludeAmbiguous);
        setStatus('Password generated successfully.');
      });

      copyButton.addEventListener('click', async function () {
        const value = passwordOutput.value.trim();
        if (!value) {
          setStatus('Generate a password before copying.', true);
          return;
        }

        try {
          await navigator.clipboard.writeText(value);
          setStatus('Password copied to clipboard.');
        } catch (error) {
          passwordOutput.focus();
          passwordOutput.select();
          const copied = document.execCommand && document.execCommand('copy');
          if (copied) {
            setStatus('Password copied to clipboard.');
          } else {
            setStatus('Copying was blocked by the browser. Please copy it manually.', true);
          }
        }
      });

      updateResult('', 16, [
        { name: 'Uppercase' },
        { name: 'Lowercase' },
        { name: 'Numbers' }
      ], false);
    })();
  </script>
</section>