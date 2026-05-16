<section class="tool-section">
  <style>
    .tool-section .image-resizer-grid{display:grid;gap:1rem}
    .tool-section .image-resizer-columns{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
    .tool-section .image-resizer-actions{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
    .tool-section .image-dropzone{border:2px dashed var(--input-border);border-radius:12px;padding:1rem;text-align:center;background:var(--input-bg);cursor:pointer;transition:border-color .2s,background .2s;position:relative}
    .tool-section .image-dropzone.dragover{border-color:var(--accent);background:rgba(0,123,255,0.06)}
    .tool-section .image-dropzone strong{display:block;margin-bottom:.35rem}
    .tool-section .muted{opacity:.8;font-size:.92rem}
    .tool-section .info-grid,.tool-section .result-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.75rem 1rem}
    .tool-section .info-item,.tool-section .result-item{background:var(--input-bg);border:1px solid var(--input-border);border-radius:10px;padding:.75rem}
    .tool-section .info-item span,.tool-section .result-item span{display:block;font-size:.85rem;opacity:.75}
    .tool-section .preview-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
    .tool-section .preview-card{border:1px solid var(--input-border);border-radius:12px;padding:1rem;background:var(--input-bg)}
    .tool-section .preview-card h3{margin:0 0 .75rem 0;font-size:1rem}
    .tool-section .preview-box{display:flex;align-items:center;justify-content:center;min-height:220px;border:1px solid var(--input-border);border-radius:10px;background:rgba(0,0,0,0.03);overflow:hidden}
    .tool-section .preview-box img{max-width:100%;max-height:320px;display:block}
    .tool-section .status-message{padding:.85rem 1rem;border-radius:10px;font-size:.95rem}
    .tool-section .status-error{background:rgba(220,53,69,.1);border:1px solid rgba(220,53,69,.25)}
    .tool-section .status-warning{background:rgba(255,193,7,.12);border:1px solid rgba(255,193,7,.3)}
    .tool-section .status-success{background:rgba(25,135,84,.1);border:1px solid rgba(25,135,84,.25)}
    .tool-section .hidden{display:none!important}
    .tool-section .quality-wrap{display:grid;gap:.35rem}
    .tool-section .quality-value{font-size:.9rem;opacity:.8}
    .tool-section .download-link{display:inline-block;padding:.75rem 1rem;font-size:1rem;font-weight:500;background-color:var(--accent);color:#fff;border:none;border-radius:8px;cursor:pointer;text-decoration:none}
    .tool-section .download-link:hover{background-color:var(--accent-hover)}
    .tool-section .inline-note{font-size:.9rem;opacity:.8}
    .tool-section .preset-row{display:grid;grid-template-columns:1fr;gap:.5rem}
    .tool-section .preset-row select{width:100%}
    .tool-section input[type="range"]{padding:0}
    .tool-section .file-input-hidden{position:absolute;left:-9999px}
    .tool-section .tool-result{text-align:left}
    .tool-section .result-actions{display:flex;justify-content:center;margin-top:1rem}
    .tool-section .center-text{text-align:center}
    .tool-section .result-grid-top{margin-top:1rem}
    @media (max-width:768px){
      .tool-section .image-resizer-columns,
      .tool-section .preview-grid,
      .tool-section .image-resizer-actions,
      .tool-section .info-grid,
      .tool-section .result-grid{grid-template-columns:1fr}
    }
  </style>

  <div class="tool-card">
    <form class="tool-form image-resizer-grid" id="imageResizerForm" novalidate>
      <div class="image-dropzone" id="dropzone" tabindex="0" role="button" aria-label="Upload image">
        <strong>Upload an image</strong>
        <div class="muted">Click to choose a file or drag and drop an image here</div>
        <div class="muted">Supported formats: JPEG, PNG, WebP, GIF, BMP</div>
        <input class="file-input-hidden" type="file" id="imageInput" accept="image/*">
      </div>

      <div id="messageArea" class="hidden" aria-live="polite"></div>

      <div class="tool-card hidden" id="imageInfoCard">
        <div class="info-grid">
          <div class="info-item"><span>Filename</span><strong id="infoName">—</strong></div>
          <div class="info-item"><span>Original dimensions</span><strong id="infoDimensions">—</strong></div>
          <div class="info-item"><span>File size</span><strong id="infoSize">—</strong></div>
          <div class="info-item"><span>Format</span><strong id="infoFormat">—</strong></div>
        </div>
      </div>

      <div class="tool-card hidden" id="controlsCard">
        <div class="image-resizer-columns">
          <label>
            <span>Width (px)</span>
            <input type="number" id="widthInput" min="1" step="1" placeholder="Enter width">
          </label>
          <label>
            <span>Height (px)</span>
            <input type="number" id="heightInput" min="1" step="1" placeholder="Enter height">
          </label>
        </div>

        <label class="checkbox-label">
          <input class="checkbox-input" type="checkbox" id="lockAspect" checked>
          <span class="checkbox-box"></span>
          <span>Lock aspect ratio</span>
        </label>

        <div class="image-resizer-columns">
          <label>
            <span>Resize mode</span>
            <select id="resizeMode">
              <option value="fit">Fit within dimensions</option>
              <option value="exact">Exact dimensions</option>
            </select>
          </label>

          <label>
            <span>Output format</span>
            <select id="outputFormat">
              <option value="original">Original format</option>
              <option value="image/jpeg">JPEG</option>
              <option value="image/png">PNG</option>
              <option value="image/webp">WebP</option>
            </select>
          </label>
        </div>

        <div class="preset-row">
          <label>
            <span>Preset size</span>
            <select id="presetSize">
              <option value="">Custom size</option>
              <option value="1080x1080">Social post — 1080 × 1080</option>
              <option value="1200x628">Social link preview — 1200 × 628</option>
              <option value="820x312">Cover/banner — 820 × 312</option>
              <option value="400x400">Profile image — 400 × 400</option>
              <option value="600x200">Email header — 600 × 200</option>
              <option value="1920x1080">HD — 1920 × 1080</option>
            </select>
          </label>
        </div>

        <div class="quality-wrap" id="qualityWrap">
          <label for="qualityInput">
            <span>Quality</span>
            <input type="range" id="qualityInput" min="1" max="100" step="1" value="92">
          </label>
          <div class="quality-value">Current quality: <strong id="qualityValue">92%</strong></div>
          <div class="inline-note">Used for JPEG and WebP output.</div>
        </div>

        <div class="tool-card">
          <div class="result-grid">
            <div class="result-item"><span>Planned output size</span><strong id="plannedDimensions">—</strong></div>
            <div class="result-item"><span>Resize note</span><strong id="plannedNote">Upload an image to begin</strong></div>
          </div>
        </div>

        <div class="image-resizer-actions">
          <button type="submit" id="resizeBtn">Resize Image</button>
          <button type="button" id="resetBtn">Reset</button>
        </div>
      </div>
    </form>
  </div>

  <div class="tool-card hidden" id="previewCard">
    <div class="preview-grid">
      <div class="preview-card">
        <h3>Original Preview</h3>
        <div class="preview-box">
          <img id="originalPreview" alt="Original preview">
        </div>
      </div>
      <div class="preview-card">
        <h3>Resized Preview</h3>
        <div class="preview-box">
          <img id="resizedPreview" alt="Resized preview">
        </div>
      </div>
    </div>
  </div>

  <div class="tool-card tool-result hidden" id="resultCard">
    <div class="center-text"><strong>Resized image ready</strong></div>
    <div class="result-grid result-grid-top">
      <div class="result-item"><span>Final dimensions</span><strong id="resultDimensions">—</strong></div>
      <div class="result-item"><span>Output format</span><strong id="resultFormat">—</strong></div>
      <div class="result-item"><span>Output file size</span><strong id="resultSize">—</strong></div>
      <div class="result-item"><span>Dimension change</span><strong id="resultDimensionChange">—</strong></div>
      <div class="result-item"><span>Original vs output size</span><strong id="resultSizeChange">—</strong></div>
      <div class="result-item"><span>Status</span><strong id="resultStatus">—</strong></div>
    </div>
    <div class="result-actions">
      <a id="downloadBtn" class="download-link" href="#" download="resized-image">Download Resized Image</a>
    </div>
  </div>

  <script>
    (function(){
      const form = document.getElementById('imageResizerForm');
      const imageInput = document.getElementById('imageInput');
      const dropzone = document.getElementById('dropzone');
      const messageArea = document.getElementById('messageArea');
      const imageInfoCard = document.getElementById('imageInfoCard');
      const controlsCard = document.getElementById('controlsCard');
      const previewCard = document.getElementById('previewCard');
      const resultCard = document.getElementById('resultCard');

      const widthInput = document.getElementById('widthInput');
      const heightInput = document.getElementById('heightInput');
      const lockAspect = document.getElementById('lockAspect');
      const resizeMode = document.getElementById('resizeMode');
      const outputFormat = document.getElementById('outputFormat');
      const qualityWrap = document.getElementById('qualityWrap');
      const qualityInput = document.getElementById('qualityInput');
      const qualityValue = document.getElementById('qualityValue');
      const presetSize = document.getElementById('presetSize');
      const resetBtn = document.getElementById('resetBtn');

      const infoName = document.getElementById('infoName');
      const infoDimensions = document.getElementById('infoDimensions');
      const infoSize = document.getElementById('infoSize');
      const infoFormat = document.getElementById('infoFormat');

      const plannedDimensions = document.getElementById('plannedDimensions');
      const plannedNote = document.getElementById('plannedNote');

      const originalPreview = document.getElementById('originalPreview');
      const resizedPreview = document.getElementById('resizedPreview');

      const resultDimensions = document.getElementById('resultDimensions');
      const resultFormat = document.getElementById('resultFormat');
      const resultSize = document.getElementById('resultSize');
      const resultDimensionChange = document.getElementById('resultDimensionChange');
      const resultSizeChange = document.getElementById('resultSizeChange');
      const resultStatus = document.getElementById('resultStatus');
      const downloadBtn = document.getElementById('downloadBtn');

      let state = {
        file: null,
        image: null,
        originalUrl: '',
        outputUrl: '',
        originalWidth: 0,
        originalHeight: 0,
        originalType: '',
        originalName: '',
        originalSize: 0,
        aspectRatio: 1
      };

      function showMessage(text, type) {
        messageArea.className = '';
        messageArea.classList.add('status-message', type === 'error' ? 'status-error' : type === 'warning' ? 'status-warning' : 'status-success');
        messageArea.textContent = text;
      }

      function hideMessage() {
        messageArea.className = 'hidden';
        messageArea.textContent = '';
      }

      function formatBytes(bytes) {
        if (!Number.isFinite(bytes) || bytes < 0) return '—';
        if (bytes === 0) return '0 Bytes';
        const units = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
        const value = bytes / Math.pow(1024, i);
        return value.toFixed(value >= 100 || i === 0 ? 0 : value >= 10 ? 1 : 2) + ' ' + units[i];
      }

      function formatMime(type) {
        const map = {
          'image/jpeg': 'JPEG',
          'image/png': 'PNG',
          'image/webp': 'WebP',
          'image/gif': 'GIF',
          'image/bmp': 'BMP'
        };
        return map[type] || (type ? type.replace('image/', '').toUpperCase() : 'Unknown');
      }

      function getExtensionFromMime(type) {
        const map = {
          'image/jpeg': 'jpg',
          'image/png': 'png',
          'image/webp': 'webp',
          'image/gif': 'gif',
          'image/bmp': 'bmp'
        };
        return map[type] || 'png';
      }

      function getSafeOriginalOutputMime() {
        const original = state.originalType;
        if (original === 'image/jpeg' || original === 'image/png' || original === 'image/webp') {
          return original;
        }
        return 'image/png';
      }

      function getSelectedOutputMime() {
        return outputFormat.value === 'original' ? getSafeOriginalOutputMime() : outputFormat.value;
      }

      function updateQualityVisibility() {
        const selected = getSelectedOutputMime();
        const show = selected === 'image/jpeg' || selected === 'image/webp';
        qualityWrap.classList.toggle('hidden', !show);
      }

      function sanitizePositiveInt(value) {
        if (value === '' || value === null || typeof value === 'undefined') return null;
        const num = Number(value);
        if (!Number.isFinite(num)) return null;
        const int = Math.round(num);
        return int > 0 ? int : null;
      }

      function updateQualityLabel() {
        qualityValue.textContent = qualityInput.value + '%';
      }

      function revokeOutputUrl() {
        if (state.outputUrl) {
          URL.revokeObjectURL(state.outputUrl);
          state.outputUrl = '';
        }
      }

      function resetResult() {
        revokeOutputUrl();
        resultCard.classList.add('hidden');
        previewCard.classList.add('hidden');
        resizedPreview.removeAttribute('src');
        downloadBtn.removeAttribute('href');
      }

      function resetAll() {
        hideMessage();
        resetResult();
        if (state.originalUrl) {
          URL.revokeObjectURL(state.originalUrl);
        }
        state = {
          file: null,
          image: null,
          originalUrl: '',
          outputUrl: '',
          originalWidth: 0,
          originalHeight: 0,
          originalType: '',
          originalName: '',
          originalSize: 0,
          aspectRatio: 1
        };
        form.reset();
        lockAspect.checked = true;
        qualityInput.value = 92;
        updateQualityLabel();
        imageInfoCard.classList.add('hidden');
        controlsCard.classList.add('hidden');
        previewCard.classList.add('hidden');
        resultCard.classList.add('hidden');
        infoName.textContent = '—';
        infoDimensions.textContent = '—';
        infoSize.textContent = '—';
        infoFormat.textContent = '—';
        plannedDimensions.textContent = '—';
        plannedNote.textContent = 'Upload an image to begin';
        originalPreview.removeAttribute('src');
        resizedPreview.removeAttribute('src');
        imageInput.value = '';
        outputFormat.value = 'original';
        presetSize.value = '';
        updateQualityVisibility();
      }

      function setOriginalInfo() {
        infoName.textContent = state.originalName;
        infoDimensions.textContent = state.originalWidth + ' × ' + state.originalHeight + ' px';
        infoSize.textContent = formatBytes(state.originalSize);
        infoFormat.textContent = formatMime(state.originalType);
        imageInfoCard.classList.remove('hidden');
        controlsCard.classList.remove('hidden');
        originalPreview.src = state.originalUrl;
      }

      function applyAspectFromWidth() {
        if (!state.image || !lockAspect.checked) return;
        const width = sanitizePositiveInt(widthInput.value);
        if (!width) return;
        const height = Math.max(1, Math.round(width / state.aspectRatio));
        heightInput.value = height;
      }

      function applyAspectFromHeight() {
        if (!state.image || !lockAspect.checked) return;
        const height = sanitizePositiveInt(heightInput.value);
        if (!height) return;
        const width = Math.max(1, Math.round(height * state.aspectRatio));
        widthInput.value = width;
      }

      function calculatePlannedDimensions() {
        if (!state.image) {
          plannedDimensions.textContent = '—';
          plannedNote.textContent = 'Upload an image to begin';
          return;
        }

        const mode = resizeMode.value;
        let width = sanitizePositiveInt(widthInput.value);
        let height = sanitizePositiveInt(heightInput.value);

        if (lockAspect.checked) {
          if (width && !height) {
            height = Math.max(1, Math.round(width / state.aspectRatio));
          } else if (height && !width) {
            width = Math.max(1, Math.round(height * state.aspectRatio));
          }
        }

        if (!width && !height) {
          plannedDimensions.textContent = '—';
          plannedNote.textContent = mode === 'exact' ? 'Enter both width and height' : 'Enter at least one dimension';
          return;
        }

        if (mode === 'fit') {
          if (!width) width = state.originalWidth;
          if (!height) height = state.originalHeight;
          const scale = Math.min(width / state.originalWidth, height / state.originalHeight);
          const outWidth = Math.max(1, Math.round(state.originalWidth * scale));
          const outHeight = Math.max(1, Math.round(state.originalHeight * scale));
          plannedDimensions.textContent = outWidth + ' × ' + outHeight + ' px';
          plannedNote.textContent = 'Image will fit inside the target box without cropping';
        } else {
          if (!width || !height) {
            plannedDimensions.textContent = '—';
            plannedNote.textContent = 'Exact mode requires both width and height';
            return;
          }
          plannedDimensions.textContent = width + ' × ' + height + ' px';
          plannedNote.textContent = Math.abs((width / height) - state.aspectRatio) > 0.0001 ? 'Exact mode may stretch the image' : 'Exact dimensions will be used';
        }
      }

      function loadImageFile(file) {
        hideMessage();
        resetResult();

        if (!file) return;
        if (!file.type || !file.type.startsWith('image/')) {
          showMessage('Unsupported file type. Please upload a valid image file such as JPEG, PNG, WebP, GIF, or BMP.', 'error');
          return;
        }

        const objectUrl = URL.createObjectURL(file);
        const img = new Image();

        img.onload = function() {
          if (state.originalUrl) {
            URL.revokeObjectURL(state.originalUrl);
          }
          state.file = file;
          state.image = img;
          state.originalUrl = objectUrl;
          state.originalWidth = img.naturalWidth || img.width;
          state.originalHeight = img.naturalHeight || img.height;
          state.originalType = file.type || 'image/png';
          state.originalName = file.name || 'image';
          state.originalSize = file.size || 0;
          state.aspectRatio = state.originalWidth / state.originalHeight;

          widthInput.value = state.originalWidth;
          heightInput.value = state.originalHeight;
          outputFormat.value = 'original';
          presetSize.value = '';
          setOriginalInfo();
          updateQualityVisibility();
          calculatePlannedDimensions();
          showMessage('Image loaded successfully. Adjust the settings and click Resize Image.', 'success');
        };

        img.onerror = function() {
          URL.revokeObjectURL(objectUrl);
          showMessage('The image could not be loaded. The file may be corrupted or unreadable.', 'error');
        };

        img.src = objectUrl;
      }

      function handleFiles(files) {
        if (!files || !files.length) return;
        loadImageFile(files[0]);
      }

      function validateBeforeResize() {
        if (!state.image) {
          showMessage('Please upload an image before resizing.', 'error');
          return null;
        }

        let width = sanitizePositiveInt(widthInput.value);
        let height = sanitizePositiveInt(heightInput.value);
        const mode = resizeMode.value;

        if ((widthInput.value !== '' && !width) || (heightInput.value !== '' && !height)) {
          showMessage('Width and height must be positive whole numbers.', 'error');
          return null;
        }

        if (lockAspect.checked) {
          if (width && !height) {
            height = Math.max(1, Math.round(width / state.aspectRatio));
            heightInput.value = height;
          } else if (height && !width) {
            width = Math.max(1, Math.round(height * state.aspectRatio));
            widthInput.value = width;
          }
        }

        if (mode === 'exact') {
          if (!width || !height) {
            showMessage('Exact dimensions mode requires both width and height.', 'error');
            return null;
          }
        } else {
          if (!width && !height) {
            showMessage('Please enter at least one valid dimension.', 'error');
            return null;
          }
          if (!width) width = state.originalWidth;
          if (!height) height = state.originalHeight;
        }

        if (width <= 0 || height <= 0) {
          showMessage('Width and height must be positive whole numbers.', 'error');
          return null;
        }

        if (width > 12000 || height > 12000 || (width * height) > 40000000) {
          showMessage('The requested dimensions are very large and may fail or freeze your browser. Please choose smaller dimensions.', 'warning');
          return null;
        }

        return { width, height, mode };
      }

      function getOutputName(mime) {
        const base = (state.originalName || 'image').replace(/\.[^.]+$/, '');
        return base + '-resized.' + getExtensionFromMime(mime);
      }

      function canvasToBlob(canvas, mime, quality) {
        return new Promise(function(resolve, reject) {
          canvas.toBlob(function(blob) {
            if (blob) resolve(blob);
            else reject(new Error('Export failed'));
          }, mime, quality);
        });
      }

      async function resizeImage() {
        const validated = validateBeforeResize();
        if (!validated) return;

        hideMessage();
        resetResult();

        const selectedMime = getSelectedOutputMime();
        const quality = (selectedMime === 'image/jpeg' || selectedMime === 'image/webp') ? Number(qualityInput.value) / 100 : undefined;

        let outWidth;
        let outHeight;

        if (validated.mode === 'fit') {
          const scale = Math.min(validated.width / state.originalWidth, validated.height / state.originalHeight);
          outWidth = Math.max(1, Math.round(state.originalWidth * scale));
          outHeight = Math.max(1, Math.round(state.originalHeight * scale));
        } else {
          outWidth = validated.width;
          outHeight = validated.height;
        }

        const canvas = document.createElement('canvas');
        canvas.width = outWidth;
        canvas.height = outHeight;
        const ctx = canvas.getContext('2d');

        if (!ctx) {
          showMessage('Your browser does not support canvas image processing.', 'error');
          return;
        }

        if (selectedMime === 'image/jpeg') {
          ctx.fillStyle = '#ffffff';
          ctx.fillRect(0, 0, outWidth, outHeight);
        }

        ctx.drawImage(state.image, 0, 0, outWidth, outHeight);

        try {
          const blob = await canvasToBlob(canvas, selectedMime, quality);
          const outputUrl = URL.createObjectURL(blob);
          state.outputUrl = outputUrl;

          resizedPreview.src = outputUrl;
          previewCard.classList.remove('hidden');

          const dimensionChangeW = ((outWidth - state.originalWidth) / state.originalWidth) * 100;
          const dimensionChangeH = ((outHeight - state.originalHeight) / state.originalHeight) * 100;
          const sizeChange = state.originalSize ? ((blob.size - state.originalSize) / state.originalSize) * 100 : 0;

          resultDimensions.textContent = outWidth + ' × ' + outHeight + ' px';
          resultFormat.textContent = formatMime(selectedMime);
          resultSize.textContent = formatBytes(blob.size);
          resultDimensionChange.textContent = 'Width ' + (dimensionChangeW >= 0 ? '+' : '') + dimensionChangeW.toFixed(1) + '%, Height ' + (dimensionChangeH >= 0 ? '+' : '') + dimensionChangeH.toFixed(1) + '%';
          resultSizeChange.textContent = formatBytes(state.originalSize) + ' → ' + formatBytes(blob.size) + ' (' + (sizeChange >= 0 ? '+' : '') + sizeChange.toFixed(1) + '%)';
          resultStatus.textContent = (outWidth === state.originalWidth && outHeight === state.originalHeight) ? 'Exported with the same dimensions as the original' : 'Resize completed successfully';

          downloadBtn.href = outputUrl;
          downloadBtn.download = getOutputName(selectedMime);

          resultCard.classList.remove('hidden');

          if (selectedMime === 'image/jpeg' && (state.originalType === 'image/png' || state.originalType === 'image/webp' || state.originalType === 'image/gif')) {
            showMessage('Transparency will be replaced with a white background when exporting to JPEG.', 'warning');
          } else if (resizeMode.value === 'exact' && Math.abs((outWidth / outHeight) - state.aspectRatio) > 0.01) {
            showMessage('The resized image was created with exact dimensions and may appear stretched.', 'warning');
          } else if (outWidth === state.originalWidth && outHeight === state.originalHeight) {
            showMessage('The output dimensions match the original image. The file was still exported successfully.', 'success');
          } else {
            showMessage('Your image has been resized and is ready to download.', 'success');
          }
        } catch (error) {
          showMessage('The image could not be exported. Please try different settings or a smaller size.', 'error');
        }
      }

      dropzone.addEventListener('click', function() {
        imageInput.click();
      });

      dropzone.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          imageInput.click();
        }
      });

      ['dragenter', 'dragover'].forEach(function(eventName) {
        dropzone.addEventListener(eventName, function(e) {
          e.preventDefault();
          e.stopPropagation();
          dropzone.classList.add('dragover');
        });
      });

      ['dragleave', 'drop'].forEach(function(eventName) {
        dropzone.addEventListener(eventName, function(e) {
          e.preventDefault();
          e.stopPropagation();
          dropzone.classList.remove('dragover');
        });
      });

      dropzone.addEventListener('drop', function(e) {
        handleFiles(e.dataTransfer.files);
      });

      imageInput.addEventListener('change', function(e) {
        handleFiles(e.target.files);
      });

      widthInput.addEventListener('input', function() {
        if (lockAspect.checked) applyAspectFromWidth();
        calculatePlannedDimensions();
      });

      heightInput.addEventListener('input', function() {
        if (lockAspect.checked) applyAspectFromHeight();
        calculatePlannedDimensions();
      });

      lockAspect.addEventListener('change', function() {
        if (lockAspect.checked) {
          if (document.activeElement === heightInput) {
            applyAspectFromHeight();
          } else {
            applyAspectFromWidth();
          }
        }
        calculatePlannedDimensions();
      });

      resizeMode.addEventListener('change', function() {
        calculatePlannedDimensions();
      });

      outputFormat.addEventListener('change', function() {
        updateQualityVisibility();
      });

      qualityInput.addEventListener('input', updateQualityLabel);

      presetSize.addEventListener('change', function() {
        if (!presetSize.value) return;
        const parts = presetSize.value.split('x');
        if (parts.length !== 2) return;
        widthInput.value = parts[0];
        heightInput.value = parts[1];
        calculatePlannedDimensions();
      });

      form.addEventListener('submit', function(e) {
        e.preventDefault();
        resizeImage();
      });

      resetBtn.addEventListener('click', function() {
        resetAll();
      });

      updateQualityLabel();
      updateQualityVisibility();
      calculatePlannedDimensions();
    })();
  </script>
</section>