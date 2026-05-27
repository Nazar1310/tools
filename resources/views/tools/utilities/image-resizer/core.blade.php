<section class="tool-section">
  <div class="tool-card">
    <form class="tool-form" id="imageResizerForm">
      <div class="tool-grid">
        <div class="tool-panel">
          <div class="upload-area" id="dropZone" tabindex="0" role="button" aria-label="Upload image">
            <input type="file" id="imageFile" accept="image/*" hidden>
            <div class="upload-copy">
              <strong>Upload an image</strong>
              <span>Drag and drop or choose a JPG, PNG, WebP, or GIF file.</span>
            </div>
            <button type="button" class="secondary-button" id="chooseFileBtn">Choose File</button>
          </div>
          <div class="file-meta" id="fileMeta" aria-live="polite"></div>

          <div class="field-group">
            <label for="presetSize">Preset size</label>
            <select id="presetSize">
              <option value="custom">Custom</option>
              <option value="1080x1080">Instagram Post 1080 × 1080</option>
              <option value="1080x1350">Instagram Portrait 1080 × 1350</option>
              <option value="1200x628">Facebook Link 1200 × 628</option>
              <option value="1280x720">YouTube Thumbnail 1280 × 720</option>
              <option value="1920x1080">HD 1920 × 1080</option>
              <option value="800x800">Square 800 × 800</option>
              <option value="1600x900">Web Banner 1600 × 900</option>
            </select>
          </div>

          <div class="dimension-grid">
            <label>
              Width (px)
              <input type="number" id="widthInput" min="1" step="1" placeholder="Width" disabled>
            </label>
            <label>
              Height (px)
              <input type="number" id="heightInput" min="1" step="1" placeholder="Height" disabled>
            </label>
          </div>

          <label class="checkbox-label">
            <input type="checkbox" class="checkbox-input" id="keepRatio" checked>
            <span class="checkbox-box"></span>
            <span>Keep aspect ratio</span>
          </label>

          <div class="field-group">
            <label for="resizeMode">Resize mode</label>
            <select id="resizeMode">
              <option value="fit">Fit within dimensions</option>
              <option value="exact">Exact dimensions</option>
            </select>
          </div>

          <div class="field-group">
            <label for="outputFormat">Output format</label>
            <select id="outputFormat">
              <option value="original">Same as original</option>
              <option value="image/jpeg">JPG</option>
              <option value="image/png">PNG</option>
              <option value="image/webp">WebP</option>
            </select>
          </div>

          <div class="field-group" id="qualityGroup">
            <label for="qualityInput">Quality: <span id="qualityValue">85</span></label>
            <input type="range" id="qualityInput" min="1" max="100" value="85">
          </div>

          <div class="action-row">
            <button type="submit" id="resizeBtn" disabled>Resize Image</button>
            <button type="button" class="secondary-button" id="resetBtn">Reset</button>
          </div>

          <div class="error-message" id="errorMessage" aria-live="polite"></div>
        </div>

        <div class="tool-panel tool-result" id="resultPanel">
          <div class="result-placeholder" id="resultPlaceholder">
            Your resized image will appear here after processing.
          </div>
          <div class="result-content" id="resultContent" hidden>
            <div class="preview-wrap">
              <img id="resultPreview" alt="Resized preview">
            </div>
            <div class="result-summary">
              <div class="result-main" id="resultMain"></div>
              <div class="result-details" id="resultDetails"></div>
              <div class="result-notes" id="resultNotes"></div>
            </div>
            <a id="downloadLink" class="download-button" href="#" download>Download resized image</a>
          </div>
        </div>
      </div>
    </form>
  </div>

  <style>
    .tool-section .tool-grid{display:grid;grid-template-columns:1.1fr .9fr;gap:1rem;align-items:start}
    .tool-section .tool-panel{display:grid;gap:1rem}
    .tool-section .upload-area{border:1px dashed var(--input-border);border-radius:12px;padding:1rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;background:var(--input-bg);cursor:pointer}
    .tool-section .upload-copy{display:grid;gap:.25rem}
    .tool-section .upload-copy span,.tool-section .file-meta,.tool-section .result-details,.tool-section .result-notes,.tool-section .result-placeholder,.tool-section .error-message{font-size:.95rem;color:var(--text-color)}
    .tool-section .secondary-button,.tool-section .download-button{display:inline-flex;align-items:center;justify-content:center;padding:.75rem 1rem;border-radius:8px;text-decoration:none;border:1px solid var(--input-border);background:var(--input-bg);color:var(--text-color);cursor:pointer}
    .tool-section .secondary-button:hover,.tool-section .download-button:hover{border-color:var(--accent)}
    .tool-section .field-group{display:grid;gap:.35rem}
    .tool-section .dimension-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
    .tool-section .action-row{display:flex;gap:.75rem;flex-wrap:wrap}
    .tool-section .action-row button{flex:1 1 180px}
    .tool-section .error-message{min-height:1.2em;color:#d64545}
    .tool-section .file-meta{padding:.75rem 1rem;border-radius:10px;background:rgba(0,0,0,.03)}
    .tool-section .result-panel{min-height:100%}
    .tool-section .result-placeholder{padding:1.25rem;border:1px dashed var(--input-border);border-radius:12px;text-align:center}
    .tool-section .result-content{display:grid;gap:1rem}
    .tool-section .preview-wrap{border:1px solid var(--input-border);border-radius:12px;overflow:hidden;background:var(--input-bg);display:flex;align-items:center;justify-content:center;min-height:240px}
    .tool-section .preview-wrap img{max-width:100%;height:auto;display:block}
    .tool-section .result-summary{display:grid;gap:.5rem;text-align:left}
    .tool-section .result-main{font-weight:600;font-size:1.05rem}
    .tool-section .result-details,.tool-section .result-notes{line-height:1.5}
    .tool-section .result-notes{color:#8a5a00}
    .tool-section [hidden]{display:none !important}
    .tool-section .upload-area.dragging{border-color:var(--accent);background:rgba(0,0,0,.03)}
    .tool-section input:disabled,.tool-section select:disabled{opacity:.65;cursor:not-allowed}
    @media (max-width: 900px){.tool-section .tool-grid{grid-template-columns:1fr}}
    @media (max-width: 480px){.tool-section .dimension-grid{grid-template-columns:1fr}.tool-section .upload-area{flex-direction:column;align-items:flex-start}}
  </style>

  <script>
    (function () {
      const form = document.getElementById('imageResizerForm');
      const fileInput = document.getElementById('imageFile');
      const chooseFileBtn = document.getElementById('chooseFileBtn');
      const dropZone = document.getElementById('dropZone');
      const fileMeta = document.getElementById('fileMeta');
      const presetSize = document.getElementById('presetSize');
      const widthInput = document.getElementById('widthInput');
      const heightInput = document.getElementById('heightInput');
      const keepRatio = document.getElementById('keepRatio');
      const resizeMode = document.getElementById('resizeMode');
      const outputFormat = document.getElementById('outputFormat');
      const qualityGroup = document.getElementById('qualityGroup');
      const qualityInput = document.getElementById('qualityInput');
      const qualityValue = document.getElementById('qualityValue');
      const resizeBtn = document.getElementById('resizeBtn');
      const resetBtn = document.getElementById('resetBtn');
      const errorMessage = document.getElementById('errorMessage');
      const resultPlaceholder = document.getElementById('resultPlaceholder');
      const resultContent = document.getElementById('resultContent');
      const resultPreview = document.getElementById('resultPreview');
      const resultMain = document.getElementById('resultMain');
      const resultDetails = document.getElementById('resultDetails');
      const resultNotes = document.getElementById('resultNotes');
      const downloadLink = document.getElementById('downloadLink');

      let state = {
        file: null,
        originalWidth: 0,
        originalHeight: 0,
        originalType: '',
        originalSize: 0,
        objectUrl: '',
        outputUrl: ''
      };

      const presets = {
        '1080x1080': { w: 1080, h: 1080 },
        '1080x1350': { w: 1080, h: 1350 },
        '1200x628': { w: 1200, h: 628 },
        '1280x720': { w: 1280, h: 720 },
        '1920x1080': { w: 1920, h: 1080 },
        '800x800': { w: 800, h: 800 },
        '1600x900': { w: 1600, h: 900 }
      };

      function setError(msg) {
        errorMessage.textContent = msg || '';
      }

      function formatBytes(bytes) {
        if (!Number.isFinite(bytes) || bytes <= 0) return 'Unknown';
        const units = ['B', 'KB', 'MB', 'GB'];
        let i = 0;
        let value = bytes;
        while (value >= 1024 && i < units.length - 1) {
          value /= 1024;
          i++;
        }
        return `${value.toFixed(value < 10 && i > 0 ? 1 : 0)} ${units[i]}`;
      }

      function isLossy(type) {
        return type === 'image/jpeg' || type === 'image/webp';
      }

      function getSelectedFormat() {
        const val = outputFormat.value;
        if (val === 'original') return state.originalType || 'image/png';
        return val;
      }

      function updateQualityVisibility() {
        qualityGroup.hidden = !isLossy(getSelectedFormat());
      }

      function resetResult() {
        resultPlaceholder.hidden = false;
        resultContent.hidden = true;
        resultPreview.removeAttribute('src');
        resultMain.textContent = '';
        resultDetails.textContent = '';
        resultNotes.textContent = '';
        if (state.outputUrl) URL.revokeObjectURL(state.outputUrl);
        state.outputUrl = '';
      }

      function updateFileMeta() {
        if (!state.file) {
          fileMeta.textContent = '';
          return;
        }
        fileMeta.textContent = `Selected: ${state.file.name} · ${state.originalWidth} × ${state.originalHeight}px · ${formatBytes(state.originalSize)}`;
      }

      function setDimensions(w, h) {
        widthInput.value = Number.isFinite(w) ? Math.round(w) : '';
        heightInput.value = Number.isFinite(h) ? Math.round(h) : '';
      }

      function syncFromPreset() {
        const val = presetSize.value;
        if (val === 'custom') return;
        const preset = presets[val];
        if (!preset) return;
        setDimensions(preset.w, preset.h);
      }

      function validateDimension(value) {
        if (value === '') return null;
        const n = Number(value);
        if (!Number.isInteger(n) || n <= 0) return false;
        if (n > 20000) return false;
        return n;
      }

      function updateAspectFromInput(changed) {
        if (!state.originalWidth || !state.originalHeight || !keepRatio.checked) return;
        const ratio = state.originalWidth / state.originalHeight;
        const w = validateDimension(widthInput.value);
        const h = validateDimension(heightInput.value);
        if (changed === 'width' && typeof w === 'number') {
          heightInput.value = Math.max(1, Math.round(w / ratio));
        } else if (changed === 'height' && typeof h === 'number') {
          widthInput.value = Math.max(1, Math.round(h * ratio));
        }
      }

      function loadImage(file) {
        setError('');
        if (!file || !file.type || !file.type.startsWith('image/')) {
          setError('Please upload a valid image file.');
          return;
        }
        if (state.objectUrl) URL.revokeObjectURL(state.objectUrl);
        state.file = file;
        state.originalSize = file.size;
        state.originalType = file.type;
        state.objectUrl = URL.createObjectURL(file);
        const img = new Image();
        img.onload = function () {
          state.originalWidth = img.naturalWidth;
          state.originalHeight = img.naturalHeight;
          setDimensions(state.originalWidth, state.originalHeight);
          presetSize.value = 'custom';
          keepRatio.checked = true;
          resizeBtn.disabled = false;
          widthInput.disabled = false;
          heightInput.disabled = false;
          updateFileMeta();
          resetResult();
          updateQualityVisibility();
        };
        img.onerror = function () {
          setError('This image could not be read. Please try another file.');
          clearFile();
        };
        img.src = state.objectUrl;
      }

      function clearFile() {
        state.file = null;
        state.originalWidth = 0;
        state.originalHeight = 0;
        state.originalType = '';
        state.originalSize = 0;
        if (state.objectUrl) URL.revokeObjectURL(state.objectUrl);
        state.objectUrl = '';
        widthInput.value = '';
        heightInput.value = '';
        widthInput.disabled = true;
        heightInput.disabled = true;
        fileInput.value = '';
        presetSize.value = 'custom';
        resizeBtn.disabled = true;
        fileMeta.textContent = '';
        resetResult();
        updateQualityVisibility();
      }

      function processImage() {
        setError('');
        if (!state.file || !state.objectUrl) {
          setError('Please upload an image first.');
          return;
        }
        const widthVal = validateDimension(widthInput.value);
        const heightVal = validateDimension(heightInput.value);
        const ratioLocked = keepRatio.checked;
        const mode = resizeMode.value;

        if (widthVal === false || heightVal === false) {
          setError('Enter a positive whole number.');
          return;
        }

        if (widthVal === null && heightVal === null) {
          setError(ratioLocked ? 'Enter at least one dimension.' : 'Enter both width and height.');
          return;
        }

        let targetW = typeof widthVal === 'number' ? widthVal : null;
        let targetH = typeof heightVal === 'number' ? heightVal : null;

        if (ratioLocked) {
          const ratio = state.originalWidth / state.originalHeight;
          if (targetW && !targetH) targetH = Math.max(1, Math.round(targetW / ratio));
          if (targetH && !targetW) targetW = Math.max(1, Math.round(targetH * ratio));
        } else if (targetW === null || targetH === null) {
          setError('Enter both width and height when aspect ratio is unlocked.');
          return;
        }

        if (mode === 'fit') {
          const scale = Math.min(targetW / state.originalWidth, targetH / state.originalHeight);
          targetW = Math.max(1, Math.round(state.originalWidth * scale));
          targetH = Math.max(1, Math.round(state.originalHeight * scale));
        } else {
          targetW = Math.round(targetW);
          targetH = Math.round(targetH);
        }

        if (targetW > 20000 || targetH > 20000) {
          setError('The requested size is too large for the browser to process safely.');
          return;
        }

        const img = new Image();
        img.onload = function () {
          try {
            const canvas = document.createElement('canvas');
            canvas.width = targetW;
            canvas.height = targetH;
            const ctx = canvas.getContext('2d');
            if (!ctx) {
              setError('Unable to process the image in this browser.');
              return;
            }
            ctx.drawImage(img, 0, 0, targetW, targetH);
            const format = getSelectedFormat();
            const quality = Math.max(1, Math.min(100, Number(qualityInput.value) || 85)) / 100;
            canvas.toBlob(function (blob) {
              if (!blob) {
                setError('Unable to create the resized image. Please try again.');
                return;
              }
              if (state.outputUrl) URL.revokeObjectURL(state.outputUrl);
              state.outputUrl = URL.createObjectURL(blob);
              resultPreview.src = state.outputUrl;
              downloadLink.href = state.outputUrl;
              const ext = format === 'image/jpeg' ? 'jpg' : format === 'image/png' ? 'png' : 'webp';
              downloadLink.download = `resized-${targetW}x${targetH}.${ext}`;
              resultMain.textContent = `Resized image: ${targetW} × ${targetH}px`;
              resultDetails.textContent = `Original: ${state.originalWidth} × ${state.originalHeight}px · Output: ${format.replace('image/', '').toUpperCase()} · File size: ${formatBytes(state.originalSize)} → ${formatBytes(blob.size)}`;
              const notes = [];
              if (mode === 'fit') notes.push('Fit mode preserved the image proportions within the selected bounds.');
              if (!ratioLocked && (targetW !== state.originalWidth || targetH !== state.originalHeight)) notes.push('Aspect ratio was not locked, so the image may be stretched.');
              if (format === 'image/jpeg' && /png|webp|gif/i.test(state.originalType)) notes.push('Transparency will be flattened in JPG output.');
              if (qualityGroup.hidden === false) notes.push(`Quality used: ${Math.round(quality * 100)}.`);
              resultNotes.textContent = notes.join(' ');
              resultPlaceholder.hidden = true;
              resultContent.hidden = false;
            }, format, isLossy(format) ? quality : undefined);
          } catch (e) {
            setError('An error occurred while resizing the image.');
          }
        };
        img.onerror = function () {
          setError('This image could not be processed. Please try another file.');
        };
        img.src = state.objectUrl;
      }

      chooseFileBtn.addEventListener('click', () => fileInput.click());
      dropZone.addEventListener('click', (e) => {
        if (e.target !== chooseFileBtn) fileInput.click();
      });
      dropZone.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          fileInput.click();
        }
      });
      fileInput.addEventListener('change', (e) => loadImage(e.target.files[0]));
      dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragging');
      });
      dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragging'));
      dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragging');
        const file = e.dataTransfer.files && e.dataTransfer.files[0];
        if (file) loadImage(file);
      });
      presetSize.addEventListener('change', syncFromPreset);
      widthInput.addEventListener('input', () => {
        if (presetSize.value !== 'custom') presetSize.value = 'custom';
        updateAspectFromInput('width');
      });
      heightInput.addEventListener('input', () => {
        if (presetSize.value !== 'custom') presetSize.value = 'custom';
        updateAspectFromInput('height');
      });
      outputFormat.addEventListener('change', updateQualityVisibility);
      qualityInput.addEventListener('input', () => {
        qualityValue.textContent = qualityInput.value;
      });
      keepRatio.addEventListener('change', () => {
        if (keepRatio.checked) updateAspectFromInput('width');
      });
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        processImage();
      });
      resetBtn.addEventListener('click', () => {
        setError('');
        clearFile();
        qualityInput.value = '85';
        qualityValue.textContent = '85';
        resizeMode.value = 'fit';
        outputFormat.value = 'original';
        keepRatio.checked = true;
        updateQualityVisibility();
      });

      updateQualityVisibility();
      clearFile();
    })();
  </script>
</section>