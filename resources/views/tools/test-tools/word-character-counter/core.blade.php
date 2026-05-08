<section class="tool-section">
  <div class="tool-card">
    <form class="tool-form" id="wordCounterForm">
      <label for="wcText">Enter text to analyze</label>
      <textarea id="wcText" name="text" rows="10" placeholder="Type or paste your text here..."></textarea>
      <div class="tool-actions">
        <button type="button" id="wcClearBtn">Clear</button>
      </div>
    </form>

    <div class="tool-result" aria-live="polite">
      <div id="wcSummary">Your text contains 0 words, 0 characters, and 0 sentences.</div>
      <div class="wc-stats" id="wcStats">
        <div class="wc-stat">
          <strong id="wcWords">0</strong>
          <span>Words</span>
        </div>
        <div class="wc-stat">
          <strong id="wcChars">0</strong>
          <span>Characters</span>
        </div>
        <div class="wc-stat">
          <strong id="wcCharsNoSpaces">0</strong>
          <span>Characters without spaces</span>
        </div>
        <div class="wc-stat">
          <strong id="wcSentences">0</strong>
          <span>Sentences</span>
        </div>
      </div>
    </div>
  </div>

  <style>
    .tool-section .tool-actions{display:flex;justify-content:flex-end}
    .tool-section .wc-stats{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0.75rem;margin-top:1rem;text-align:left}
    .tool-section .wc-stat{border:1px solid var(--border);border-radius:12px;padding:0.85rem 1rem;background:var(--input-bg)}
    .tool-section .wc-stat strong{display:block;font-size:1.35rem;line-height:1.2;margin-bottom:0.2rem;color:var(--text-color)}
    .tool-section .wc-stat span{display:block;font-size:0.92rem;color:var(--text-color)}
    .tool-section #wcSummary{font-weight:600;margin-bottom:0.75rem}
    @media (max-width:480px){
      .tool-section .wc-stats{grid-template-columns:1fr}
    }
  </style>

  <script>
    (function () {
      const textArea = document.getElementById('wcText');
      const clearBtn = document.getElementById('wcClearBtn');
      const wordsEl = document.getElementById('wcWords');
      const charsEl = document.getElementById('wcChars');
      const charsNoSpacesEl = document.getElementById('wcCharsNoSpaces');
      const sentencesEl = document.getElementById('wcSentences');
      const summaryEl = document.getElementById('wcSummary');

      function countWords(text) {
        const trimmed = text.trim();
        if (!trimmed) return 0;
        return trimmed.split(/\s+/).filter(Boolean).length;
      }

      function countSentences(text) {
        const matches = text.match(/[^.!?]+[.!?]+|[^.!?]+$/g);
        if (!matches) return 0;
        return matches.map(s => s.trim()).filter(Boolean).length;
      }

      function updateCounts() {
        const text = textArea.value;
        const words = countWords(text);
        const characters = text.length;
        const charactersNoSpaces = text.replace(/\s/g, '').length;
        const sentences = text.trim() ? countSentences(text) : 0;

        wordsEl.textContent = words;
        charsEl.textContent = characters;
        charsNoSpacesEl.textContent = charactersNoSpaces;
        sentencesEl.textContent = sentences;
        summaryEl.textContent = `Your text contains ${words} words, ${characters} characters, and ${sentences} sentences.`;
      }

      textArea.addEventListener('input', updateCounts);
      clearBtn.addEventListener('click', function () {
        textArea.value = '';
        updateCounts();
        textArea.focus();
      });

      updateCounts();
    })();
  </script>
</section>