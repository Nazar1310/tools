<section class="tool-section">
  <style>
    .tool-section .image-resizer-grid{display:grid;grid-template-columns:1.1fr 0.9fr;gap:1rem;align-items:start}
    .tool-section .tool-muted{font-size:.9rem;opacity:.8}
    .tool-section .tool-small{font-size:.85rem;opacity:.8}
    .tool-section .tool-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
    .tool-section .tool-actions{display:flex;gap:.75rem;flex-wrap:wrap}
    .tool-section .tool-actions button[type="button"]{background-color:var(--input-bg);color:var(--text-color);border:1px solid var(--input-border)}
    .tool-section .tool-actions button[type="button"]:hover{background-color:var(--card-bg)}
    .tool-section .mode-group{display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem}
    .tool-section .mode-option{display:flex;align-items:center;gap:.5rem;border:1px solid var(--input-border);border-radius:10px;padding:.75rem;cursor:pointer;background:var(--input-bg)}
    .tool-section .mode-option input{width:auto;margin:0;padding:0}
    .tool-section .upload-zone{border:2px dashed var(--input-border);border-radius:12px;padding:1rem;text-align:center;background:var(--input-bg);transition:border-color .2s, background .2s}
    .tool-section .upload-zone.dragover{border-color:var(--accent);background:rgba(0,123,255,0.06)}
    .tool-section .upload-zone input[type="file"]{display:none}
    .tool-section .upload-zone button{margin-top:.75rem}
    .tool-section .panel{border:1px solid var(--border);border-radius:12px;padding:1rem;background:rgba(255,255,255,0.02)}
    .tool-section .preview-box{display:grid;gap:1rem}
    .tool-section .preview-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
    .tool-section .preview-card{border:1px solid var(--input-border);border-radius:12px;padding:.75rem;background:var(--input-bg)}
    .tool-section .preview-card h3{margin:0 0 .5rem 0;font-size:1rem}
    .tool-section .preview-image-wrap{aspect-ratio:4/3;display:flex;align-items:center;justify-content:center;border:1px solid var(--input-border);border-radius:10px;overflow:hidden;background-image:linear-gradient(45deg, rgba(0,0,0,.04) 25%, transparent 25%), linear-gradient(-45deg, rgba(0,0,0,.04) 25%, transparent 25%), linear-gradient(45deg, transparent 75%, rgba(0,0,0,.04) 75%), linear-gradient(-45deg, transparent 75%, rgba(0,0,0,.04) 75%);background-size:20px 20px;background-position:0 0, 0 10px, 10px -10px, -10px 0}
    .tool-section .preview-image-wrap img{max-width:100%;max-height:100%;display:block}
    .tool-section .meta-list{display:grid;gap:.35rem;text-align:left}
    .tool-section .meta-item{display:flex;justify-content:space-between;gap:1rem;font-size:.92rem}
    .tool-section .meta-item span:last-child{text-align:right;word-break:break-word}
    .tool-section .error-text{color:#d93025;font-size:.88rem}
    .tool-section .success-box{margin-top:1rem}
    .tool-section .hidden{display:none!important}
    .tool-section .range-row{display:grid;grid-template-columns:1fr auto;gap:.75rem;align-items:center}
    .tool-section .range-value{min-width:3rem;text-align:right;font-size:.95rem}
    .tool-section .status-box{padding:.75rem 1rem;border-radius:10px;background:var(--input-bg);border:1px solid var(--input-border);text-align:left}
    .tool-section .download-btn{display:inline-block;text-decoration:none;padding:.75rem 1rem;font-size:1rem;font-weight:500;background-color:var(--accent);color:#fff;border:none;border-radius:8px;cursor:pointer}
    .tool-section .download-btn:hover{background-color:var(--accent-hover)}
    @media (max-width:900px){
      .tool-section .image-resizer-grid{grid-template-columns:1fr}
    }
    @media (max-width:640px){
      .tool-section .tool-row,.tool-section .preview-grid,.tool-section .mode-group{grid-template-columns:1fr}
    }
  </style>

  <div class="tool-card">
    <div class="image-resizer-grid">
      <div>
        <form class="tool-form" id="imageResizerForm" novalidate>
          <div class="panel">
            <div class="tool-form">
              <div>
                <h2 style="margin:0 0 .35rem 0;">Image Resizer</h2>
                <div class="tool-muted">Resize images online quickly for web, social, email, or print while keeping the dimensions you need.</div>
              </div>

              <div>
                <label for="imageFile">Image file</label>
                <div class="upload-zone" id="uploadZone" tabindex="0" role="button" aria-label="Upload image">
                  <div id="uploadText">Drag and drop an image here or choose a file</div>
                  <div class="tool-small">Supported: JPG, JPEG, PNG, WebP, GIF, BMP</div>
                  <button type="button" id="browseBtn">Choose Image</button>
                  <input type="file" id="imageFile" accept="image/*">
                </div>
                <div class="error-text hidden" id="fileError"></div>
              </div>

              <div id="originalInfo" class="status-box hidden">
                <div class="meta-list">
                  <div class="meta-item"><span>Original dimensions</span><span id="origDimensions">—</span></div>
                  <div class="meta-item"><span>Original file size</span><span id="origSize">—</span></div>
                  <div class="meta-item"><span>Original format</span><span id="origFormat">—</span></div>
                  <div class="meta-item"><span>Suggested filename</span><span id="suggestedName">—</span></div>
                </div>
              </div>
            </div>
          </div>

          <div class="panel" id="controlsPanel">
            <div class="tool-form">
              <div>
                <label>Resize method</label>
                <div class="mode-group">
                  <label class="mode-option">
                    <input type="radio" name="resizeMode" value="custom" checked>
                    <span>Custom dimensions</span>
                  </label>
                  <label class="mode-option">
                    <input type="radio" name="resizeMode" value="percentage">
                    <span>Scale by percentage</span>
                  </label>
                  <label class="mode-option">
                    <input type="radio" name="resizeMode" value="preset">
                    <span>Preset sizes</span>
                  </label>
                </div>
              </div>

              <div id="customControls">
                <div class="tool-row">
                  <div>
                    <label for="widthInput">Width (px)</label>
                    <input type="number" id="widthInput" min="1" step="1" inputmode="numeric" placeholder="e.g. 1200">
                  </div>
                  <div>
                    <label for="heightInput">Height (px)</label>
                    <input type="number" id="heightInput" min="1" step="1" inputmode="numeric" placeholder="e.g. 800">
                  </div>
                </div>
                <label class="checkbox-label">
                  <input class="checkbox-input" type="checkbox" id="aspectLock" checked>
                  <span class="checkbox-box"></span>
                  <span>Keep aspect ratio</span>
                </label>
                <div class="tool-small">When aspect ratio is locked, the last edited dimension updates the other automatically.</div>
                <div class="error-text hidden" id="customError"></div>
              </div>

              <div id="percentageControls" class="hidden">
                <label for="percentageInput">Scale percentage</label>
                <input type="number" id="percentageInput" min="1" max="500" step="1" value="100" inputmode="numeric">
                <div class="tool-small">Allowed range: 1% to 500%.</div>
                <div class="error-text hidden" id="percentageError"></div>
              </div>

              <div id="presetControls" class="hidden">
                <label for="presetSelect">Preset target</label>
                <select id="presetSelect">
                  <option value="">Select a preset</option>
                  <option value="1080x1080">Instagram Square — 1080 × 1080</option>
                  <option value="1200x630">Facebook Post — 1200 × 630</option>
                  <option value="820x312">Facebook Cover — 820 × 312</option>
                  <option value="1280x720">YouTube Thumbnail — 1280 × 720</option>
                  <option value="1920x1080">Website Banner — 1920 × 1080</option>
                  <option value="600x315">Email Header — 600 × 315</option>
                  <option value="1200x800">Blog Featured Image — 1200 × 800</option>
                  <option value="800x800">Web Medium Square — 800 × 800</option>
                </select>
                <div class="tool-small">Preset mode resizes to the exact selected dimensions.</div>
                <div class="error-text hidden" id="presetError"></div>
              </div>

              <div class="status-box">
                <div class="meta-list">
                  <div class="meta-item"><span>Live output dimensions</span><span id="liveDimensions">—</span></div>
                  <div class="meta-item"><span>Resize method</span><span id="liveMethod">Custom dimensions</span></div>
                </div>
              </div>
            </div>
          </div>

          <div class="panel" id="outputPanel">
            <div class="tool-form">
              <div class="tool-row">
                <div>
                  <label for="formatSelect">Output format</label>
                  <select id="formatSelect">
                    <option value="original">Keep original format</option>
                    <option value="jpeg">JPG</option>
                    <option value="png">PNG</option>
                    <option value="webp">WebP</option>
                  </select>
                </div>
                <div>
                  <label for="fileNameInput">File name</label>
                  <input type="text" id="fileNameInput" placeholder="resized-image">
                </div>
              </div>

              <div id="qualityWrap" class="hidden">
                <label for="qualityInput">Quality</label>
                <div class="range-row">
                  <input type="range" id="qualityInput" min="1" max="100" value="90">
                  <div class="range-value"><span id="qualityValue">90</span>%</div>
                </div>
                <div class="tool-small">Used for JPG and WebP output.</div>
              </div>

              <div id="backgroundWrap" class="hidden">
                <label for="backgroundColor">Background fill color</label>
                <input type="color" id="backgroundColor" value="#ffffff">
                <div class="tool-small">Transparent areas will be flattened when exporting to JPG.</div>
              </div>

              <div class="tool-actions">
                <button type="submit" id="resizeBtn" disabled>Resize Image</button>
                <button type="button" id="replaceBtn" disabled>Replace Image</button>
                <button type="button" id="resetBtn">Reset</button>
              </div>
              <div class="error-text hidden" id="generalError"></div>
            </div>
          </div>
        </form>
      </div>

      <div>
        <div class="preview-box">
          <div class="panel">
            <div class="preview-grid">
              <div class="preview-card">
                <h3>Original preview</h3>
                <div class="preview-image-wrap">
                  <img id="originalPreview" alt="Original preview" class="hidden">
                </div>
              </div>
              <div class="preview-card">
                <h3>Resized preview</h3>
                <div class="preview-image-wrap">
                  <img id="resultPreview" alt="Resized preview" class="hidden">
                </div>
              </div>
            </div>
          </div>

          <div class="tool-result panel" id="resultPanel">
            <div id="resultEmpty" class="tool-muted">Upload an image to begin. Your resized result will appear here.</div>
            <div id="resultContent" class="hidden">
              <div class="success-box">
                <a id="downloadBtn" class="download-btn" download>Download Resized Image</a>
              </div>
              <div class="meta-list" style="margin-top:1rem">
                <div class="meta-item"><span>Original</span><span id="resultOriginalDims">—</span></div>
                <div class="meta-item"><span>Resized</span><span id="resultNewDims">—</span></div>
                <div class="meta-item"><span>Original format</span><span id="resultOriginalFormat">—</span></div>
                <div class="meta-item"><span>Output format</span><span id="resultOutputFormat">—</span></div>
                <div class="meta-item"><span>Original file size</span><span id="resultOriginalSize">—</span></div>
                <div class="meta-item"><span>New file size</span><span id="resultNewSize">—</span></div>
                <div class="meta-item"><span>Method used</span><span id="resultMethod">—</span></div>
                <div class="meta-item"><span>Filename</span><span id="resultFilename">—</span></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <canvas id="workCanvas" class="hidden"></canvas>
  </div>

  <script>
    (function(){
      const MAX_DIMENSION = 16384;
      const MAX_PIXELS = 268435456;

      const form = document.getElementById('imageResizerForm');
      const uploadZone = document.getElementById('uploadZone');
      const browseBtn = document.getElementById('browseBtn');
      const imageFile = document.getElementById('imageFile');
      const fileError = document.getElementById('fileError');
      const originalInfo = document.getElementById('originalInfo');
      const origDimensions = document.getElementById('origDimensions');
      const origSize = document.getElementById('origSize');
      const origFormat = document.getElementById('origFormat');
      const suggestedName = document.getElementById('suggestedName');

      const widthInput = document.getElementById('widthInput');
      const heightInput = document.getElementById('heightInput');
      const aspectLock = document.getElementById('aspectLock');
      const percentageInput = document.getElementById('percentageInput');
      const presetSelect = document.getElementById('presetSelect');
      const formatSelect = document.getElementById('formatSelect');
      const fileNameInput = document.getElementById('fileNameInput');
      const qualityInput = document.getElementById('qualityInput');
      const qualityValue = document.getElementById('qualityValue');
      const backgroundColor = document.getElementById('backgroundColor');

      const customControls = document.getElementById('customControls');
      const percentageControls = document.getElementById('percentageControls');
      const presetControls = document.getElementById('presetControls');
      const qualityWrap = document.getElementById('qualityWrap');
      const backgroundWrap = document.getElementById('backgroundWrap');

      const customError = document.getElementById('customError');
      const percentageError = document.getElementById('percentageError');
      const presetError = document.getElementById('presetError');
      const generalError = document.getElementById('generalError');

      const liveDimensions = document.getElementById('liveDimensions');
      const liveMethod = document.getElementById('liveMethod');

      const resizeBtn = document.getElementById('resizeBtn');
      const replaceBtn = document.getElementById('replaceBtn');
      const resetBtn = document.getElementById('resetBtn');

      const originalPreview = document.getElementById('originalPreview');
      const resultPreview = document.getElementById('resultPreview');
      const resultEmpty = document.getElementById('resultEmpty');
      const resultContent = document.getElementById('resultContent');
      const downloadBtn = document.getElementById('downloadBtn');

      const resultOriginalDims = document.getElementById('resultOriginalDims');
      const resultNewDims = document.getElementById('resultNewDims');
      const resultOriginalFormat = document.getElementById('resultOriginalFormat');
      const resultOutputFormat = document.getElementById('resultOutputFormat');
      const resultOriginalSize = document.getElementById('resultOriginalSize');
      const resultNewSize = document.getElementById('resultNewSize');
      const resultMethod = document.getElementById('resultMethod');
      const resultFilename = document.getElementById('resultFilename');

      const canvas = document.getElementById('workCanvas');
      const ctx = canvas.getContext('2d');

      let state = {
        file: null,
        image: null,
        objectUrl: '',
        resultUrl: '',
        originalWidth: 0,
        originalHeight: 0,
        originalFormat: '',
        originalMime: '',
        originalSize: 0,
        hasTransparency: false,
        lastEdited: 'width'
      };

      function formatBytes(bytes){
        if (!Number.isFinite(bytes) || bytes < 0) return '—';
        if (bytes === 0) return '0 B';
        const units = ['B','KB','MB','GB'];
        const i = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
        const value = bytes / Math.pow(1024, i);
        return (value >= 100 || i === 0 ? value.toFixed(0) : value.toFixed(1)) + ' ' + units[i];
      }

      function sanitizeFileName(name){
        const cleaned = String(name || '')
          .replace(/\.[^.]+$/, '')
          .replace(/[\\/:*?"<>|]+/g, '')
          .replace(/\s+/g, '-')
          .replace(/-+/g, '-')
          .replace(/^-|-$/g, '')
          .trim();
        return cleaned || 'image-resized';
      }

      function getExtensionFromFormat(format){
        if (format === 'jpeg') return 'jpg';
        if (format === 'png') return 'png';
        if (format === 'webp') return 'webp';
        return 'png';
      }

      function mimeToFormat(mime){
        if (!mime) return 'png';
        if (mime.includes('jpeg') || mime.includes('jpg')) return 'jpeg';
        if (mime.includes('png')) return 'png';
        if (mime.includes('webp')) return 'webp';
        if (mime.includes('gif')) return 'png';
        if (mime.includes('bmp')) return 'png';
        return 'png';
      }

      function formatLabel(format){
        if (format === 'jpeg') return 'JPG';
        if (format === 'png') return 'PNG';
        if (format === 'webp') return 'WebP';
        return String(format || '').toUpperCase();
      }

      function getSelectedMode(){
        const checked = form.querySelector('input[name="resizeMode"]:checked');
        return checked ? checked.value : 'custom';
      }

      function clearErrors(){
        [fileError, customError, percentageError, presetError, generalError].forEach(function(el){
          el.textContent = '';
          el.classList.add('hidden');
        });
      }

      function showError(el, message){
        el.textContent = message;
        el.classList.remove('hidden');
      }

      function setControlsEnabled(enabled){
        const controls = [
          widthInput, heightInput, aspectLock, percentageInput, presetSelect,
          formatSelect, fileNameInput, qualityInput, backgroundColor
        ];
        controls.forEach(function(el){
          el.disabled = !enabled;
        });
        resizeBtn.disabled = !enabled;
        replaceBtn.disabled = !enabled;
      }

      function revokeResultUrl(){
        if (state.resultUrl) {
          URL.revokeObjectURL(state.resultUrl);
          state.resultUrl = '';
        }
      }

      function resetResult(){
        revokeResultUrl();
        resultPreview.removeAttribute('src');
        resultPreview.classList.add('hidden');
        resultEmpty.classList.remove('hidden');
        resultContent.classList.add('hidden');
        downloadBtn.removeAttribute('href');
        downloadBtn.removeAttribute('download');
        resultOriginalDims.textContent = '—';
        resultNewDims.textContent = '—';
        resultOriginalFormat.textContent = '—';
        resultOutputFormat.textContent = '—';
        resultOriginalSize.textContent = '—';
        resultNewSize.textContent = '—';
        resultMethod.textContent = '—';
        resultFilename.textContent = '—';
      }

      function resetAll(){
        clearErrors();
        revokeResultUrl();
        if (state.objectUrl) {
          URL.revokeObjectURL(state.objectUrl);
        }
        state = {
          file: null,
          image: null,
          objectUrl: '',
          resultUrl: '',
          originalWidth: 0,
          originalHeight: 0,
          originalFormat: '',
          originalMime: '',
          originalSize: 0,
          hasTransparency: false,
          lastEdited: 'width'
        };
        form.reset();
        aspectLock.checked = true;
        percentageInput.value = '100';
        qualityInput.value = '90';
        qualityValue.textContent = '90';
        formatSelect.value = 'original';
        backgroundColor.value = '#ffffff';
        originalPreview.removeAttribute('src');
        originalPreview.classList.add('hidden');
        originalInfo.classList.add('hidden');
        origDimensions.textContent = '—';
        origSize.textContent = '—';
        origFormat.textContent = '—';
        suggestedName.textContent = '—';
        widthInput.value = '';
        heightInput.value = '';
        fileNameInput.value = '';
        imageFile.value = '';
        document.querySelector('input[name="resizeMode"][value="custom"]').checked = true;
        updateModeUI();
        updateOutputUI();
        updateLivePreview();
        resetResult();
        setControlsEnabled(false);
      }

      function updateModeUI(){
        const mode = getSelectedMode();
        customControls.classList.toggle('hidden', mode !== 'custom');
        percentageControls.classList.toggle('hidden', mode !== 'percentage');
        presetControls.classList.toggle('hidden', mode !== 'preset');
        liveMethod.textContent = mode === 'custom' ? 'Custom dimensions' : mode === 'percentage' ? 'Scale by percentage' : 'Preset sizes';
        updateLivePreview();
      }

      function updateOutputUI(){
        const selected = formatSelect.value === 'original' ? state.originalFormat : formatSelect.value;
        const showQuality = selected === 'jpeg' || selected === 'webp';
        qualityWrap.classList.toggle('hidden', !showQuality);
        const showBackground = selected === 'jpeg' && state.hasTransparency;
        backgroundWrap.classList.toggle('hidden', !showBackground);
      }

      function detectTransparency(img){
        try {
          const testCanvas = document.createElement('canvas');
          const w = Math.min(img.naturalWidth || img.width, 64);
          const h = Math.min(img.naturalHeight || img.height, 64);
          testCanvas.width = w;
          testCanvas.height = h;
          const tctx = testCanvas.getContext('2d', { willReadFrequently: true });
          tctx.clearRect(0, 0, w, h);
          tctx.drawImage(img, 0, 0, w, h);
          const data = tctx.getImageData(0, 0, w, h).data;
          for (let i = 3; i < data.length; i += 4) {
            if (data[i] < 255) return true;
          }
        } catch (e) {}
        return false;
      }

      function setOriginalData(file, img, objectUrl){
        if (state.objectUrl) URL.revokeObjectURL(state.objectUrl);
        state.file = file;
        state.image = img;
        state.objectUrl = objectUrl;
        state.originalWidth = img.naturalWidth || img.width;
        state.originalHeight = img.naturalHeight || img.height;
        state.originalMime = file.type || '';
        state.originalFormat = mimeToFormat(file.type);
        state.originalSize = file.size || 0;
        state.hasTransparency = detectTransparency(img);

        originalPreview.src = objectUrl;
        originalPreview.classList.remove('hidden');

        origDimensions.textContent = state.originalWidth + ' × ' + state.originalHeight + ' px';
        origSize.textContent = formatBytes(state.originalSize);
        origFormat.textContent = formatLabel(state.originalFormat);
        const defaultName = sanitizeFileName(file.name) + '-resized';
        suggestedName.textContent = defaultName;
        if (!fileNameInput.value.trim()) fileNameInput.value = defaultName;
        originalInfo.classList.remove('hidden');

        widthInput.value = String(state.originalWidth);
        heightInput.value = String(state.originalHeight);

        setControlsEnabled(true);
        updateOutputUI();
        updateLivePreview();
        resetResult();
      }

      function loadFile(file){
        clearErrors();
        resetResult();

        if (!file) return;
        if (!file.type || !file.type.startsWith('image/')) {
          showError(fileError, 'Please upload a valid image file.');
          return;
        }

        const objectUrl = URL.createObjectURL(file);
        const img = new Image();
        img.onload = function(){
          if (!(img.naturalWidth > 0 && img.naturalHeight > 0)) {
            URL.revokeObjectURL(objectUrl);
            showError(fileError, 'This image could not be read. Please try another file.');
            return;
          }
          setOriginalData(file, img, objectUrl);
        };
        img.onerror = function(){
          URL.revokeObjectURL(objectUrl);
          showError(fileError, 'The image is corrupted or unsupported by your browser.');
        };
        img.src = objectUrl;
      }

      function computeTargetDimensions(){
        if (!state.image) return null;
        const mode = getSelectedMode();
        const ow = state.originalWidth;
        const oh = state.originalHeight;

        if (mode === 'custom') {
          const wRaw = widthInput.value.trim();
          const hRaw = heightInput.value.trim();
          if (!wRaw && !hRaw) return { error: 'Enter a width, a height, or both.' };

          let w = wRaw ? parseInt(wRaw, 10) : null;
          let h = hRaw ? parseInt(hRaw, 10) : null;

          if ((wRaw && (!Number.isFinite(w) || w < 1)) || (hRaw && (!Number.isFinite(h) || h < 1))) {
            return { error: 'Width and height must be positive whole numbers.' };
          }

          if (aspectLock.checked) {
            const ratio = ow / oh;
            if (w && !h) {
              h = Math.max(1, Math.round(w / ratio));
            } else if (!w && h) {
              w = Math.max(1, Math.round(h * ratio));
            } else if (w && h) {
              if (state.lastEdited === 'width') {
                h = Math.max(1, Math.round(w / ratio));
              } else {
                w = Math.max(1, Math.round(h * ratio));
              }
            }
          } else {
            if (!w || !h) return { error: 'Enter both width and height when aspect ratio is unlocked.' };
          }

          return { width: w, height: h, method: 'Custom dimensions' };
        }

        if (mode === 'percentage') {
          const pct = parseFloat(percentageInput.value);
          if (!Number.isFinite(pct)) return { error: 'Enter a resize percentage.' };
          if (pct <= 0 || pct > 500) return { error: 'Percentage must be between 1 and 500.' };
          return {
            width: Math.max(1, Math.round(ow * pct / 100)),
            height: Math.max(1, Math.round(oh * pct / 100)),
            method: 'Scale by percentage'
          };
        }

        if (mode === 'preset') {
          const value = presetSelect.value;
          if (!value) return { error: 'Select a preset size.' };
          const parts = value.split('x');
          const w = parseInt(parts[0], 10);
          const h = parseInt(parts[1], 10);
          if (!Number.isFinite(w) || !Number.isFinite(h) || w < 1 || h < 1) {
            return { error: 'The selected preset is invalid.' };
          }
          return { width: w, height: h, method: 'Preset sizes' };
        }

        return null;
      }

      function validateCanvasSize(width, height){
        if (width < 1 || height < 1) return 'Width and height must be at least 1 pixel.';
        if (width > MAX_DIMENSION || height > MAX_DIMENSION) {
          return 'The requested dimensions are too large for safe browser processing.';
        }
        if ((width * height) > MAX_PIXELS) {
          return 'The requested image size is too large and may exceed browser memory limits.';
        }
        return '';
      }

      function updateLivePreview(){
        if (!state.image) {
          liveDimensions.textContent = '—';
          updateOutputUI();
          return;
        }
        const result = computeTargetDimensions();
        if (!result || result.error) {
          liveDimensions.textContent = '—';
        } else {
          liveDimensions.textContent = result.width + ' × ' + result.height + ' px';
        }
        updateOutputUI();
      }

      function getOutputFormat(){
        let format = formatSelect.value === 'original' ? state.originalFormat : formatSelect.value;
        if (!['jpeg','png','webp'].includes(format)) {
          format = 'png';
        }
        return format;
      }

      function getMimeForFormat(format){
        if (format === 'jpeg') return 'image/jpeg';
        if (format === 'png') return 'image/png';
        if (format === 'webp') return 'image/webp';
        return 'image/png';
      }

      function buildOutputFilename(format){
        const fallbackBase = sanitizeFileName(state.file ? state.file.name : 'image') + '-resized';
        const base = sanitizeFileName(fileNameInput.value || fallbackBase);
        return base + '.' + getExtensionFromFormat(format);
      }

      function canvasToBlobSafe(canvasEl, mime, quality){
        return new Promise(function(resolve, reject){
          try {
            canvasEl.toBlob(function(blob){
              if (blob) resolve(blob);
              else reject(new Error('Export failed.'));
            }, mime, quality);
          } catch (e) {
            reject(e);
          }
        });
      }

      browseBtn.addEventListener('click', function(){
        imageFile.click();
      });

      replaceBtn.addEventListener('click', function(){
        imageFile.click();
      });

      imageFile.addEventListener('change', function(e){
        const file = e.target.files && e.target.files[0];
        loadFile(file);
      });

      ['dragenter','dragover'].forEach(function(type){
        uploadZone.addEventListener(type, function(e){
          e.preventDefault();
          e.stopPropagation();
          uploadZone.classList.add('dragover');
        });
      });

      ['dragleave','drop'].forEach(function(type){
        uploadZone.addEventListener(type, function(e){
          e.preventDefault();
          e.stopPropagation();
          uploadZone.classList.remove('dragover');
        });
      });

      uploadZone.addEventListener('drop', function(e){
        const file = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
        loadFile(file);
      });

      uploadZone.addEventListener('click', function(e){
        if (e.target !== browseBtn) imageFile.click();
      });

      uploadZone.addEventListener('keydown', function(e){
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          imageFile.click();
        }
      });

      form.querySelectorAll('input[name="resizeMode"]').forEach(function(radio){
        radio.addEventListener('change', updateModeUI);
      });

      widthInput.addEventListener('input', function(){
        state.lastEdited = 'width';
        if (state.image && aspectLock.checked && widthInput.value.trim()) {
          const w = parseInt(widthInput.value, 10);
          if (Number.isFinite(w) && w > 0) {
            heightInput.value = String(Math.max(1, Math.round(w * state.originalHeight / state.originalWidth)));
          }
        }
        updateLivePreview();
      });

      heightInput.addEventListener('input', function(){
        state.lastEdited = 'height';
        if (state.image && aspectLock.checked && heightInput.value.trim()) {
          const h = parseInt(heightInput.value, 10);
          if (Number.isFinite(h) && h > 0) {
            widthInput.value = String(Math.max(1, Math.round(h * state.originalWidth / state.originalHeight)));
          }
        }
        updateLivePreview();
      });

      aspectLock.addEventListener('change', function(){
        if (state.image && aspectLock.checked) {
          if (state.lastEdited === 'width' && widthInput.value.trim()) {
            const w = parseInt(widthInput.value, 10);
            if (Number.isFinite(w) && w > 0) {
              heightInput.value = String(Math.max(1, Math.round(w * state.originalHeight / state.originalWidth)));
            }
          } else if (heightInput.value.trim()) {
            const h = parseInt(heightInput.value, 10);
            if (Number.isFinite(h) && h > 0) {
              widthInput.value = String(Math.max(1, Math.round(h * state.originalWidth / state.originalHeight)));
            }
          }
        }
        updateLivePreview();
      });

      percentageInput.addEventListener('input', updateLivePreview);
      presetSelect.addEventListener('change', updateLivePreview);
      formatSelect.addEventListener('change', updateOutputUI);
      qualityInput.addEventListener('input', function(){
        qualityValue.textContent = qualityInput.value;
      });

      resetBtn.addEventListener('click', resetAll);

      form.addEventListener('submit', async function(e){
        e.preventDefault();
        clearErrors();

        if (!state.image || !state.file) {
          showError(fileError, 'Upload an image before resizing.');
          return;
        }

        const dims = computeTargetDimensions();
        if (!dims || dims.error) {
          const mode = getSelectedMode();
          if (mode === 'custom') showError(customError, dims && dims.error ? dims.error : 'Enter valid dimensions.');
          if (mode === 'percentage') showError(percentageError, dims && dims.error ? dims.error : 'Enter a valid percentage.');
          if (mode === 'preset') showError(presetError, dims && dims.error ? dims.error : 'Select a preset.');
          return;
        }

        const sizeError = validateCanvasSize(dims.width, dims.height);
        if (sizeError) {
          showError(generalError, sizeError);
          return;
        }

        let outputFormat = getOutputFormat();
        let mime = getMimeForFormat(outputFormat);
        const quality = (outputFormat === 'jpeg' || outputFormat === 'webp') ? Math.min(1, Math.max(0.01, parseInt(qualityInput.value, 10) / 100)) : undefined;

        resizeBtn.disabled = true;
        resizeBtn.textContent = 'Processing...';

        try {
          canvas.width = dims.width;
          canvas.height = dims.height;

          ctx.clearRect(0, 0, canvas.width, canvas.height);

          if (outputFormat === 'jpeg') {
            ctx.fillStyle = backgroundColor.value || '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
          }

          ctx.imageSmoothingEnabled = true;
          ctx.imageSmoothingQuality = 'high';
          ctx.drawImage(state.image, 0, 0, dims.width, dims.height);

          let blob;
          try {
            blob = await canvasToBlobSafe(canvas, mime, quality);
          } catch (err) {
            if (outputFormat === 'webp') {
              outputFormat = 'png';
              mime = 'image/png';
              blob = await canvasToBlobSafe(canvas, mime);
              showError(generalError, 'WebP export is not supported in this browser. PNG was used instead.');
            } else {
              throw err;
            }
          }

          revokeResultUrl();
          state.resultUrl = URL.createObjectURL(blob);
          const filename = buildOutputFilename(outputFormat);

          resultPreview.src = state.resultUrl;
          resultPreview.classList.remove('hidden');
          resultEmpty.classList.add('hidden');
          resultContent.classList.remove('hidden');

          downloadBtn.href = state.resultUrl;
          downloadBtn.download = filename;

          resultOriginalDims.textContent = state.originalWidth + ' × ' + state.originalHeight + ' px';
          resultNewDims.textContent = dims.width + ' × ' + dims.height + ' px';
          resultOriginalFormat.textContent = formatLabel(state.originalFormat);
          resultOutputFormat.textContent = formatLabel(outputFormat);
          resultOriginalSize.textContent = formatBytes(state.originalSize);
          resultNewSize.textContent = formatBytes(blob.size);
          resultMethod.textContent = dims.method;
          resultFilename.textContent = filename;
        } catch (err) {
          showError(generalError, 'The image could not be resized. Try smaller dimensions or a different format.');
        } finally {
          resizeBtn.disabled = false;
          resizeBtn.textContent = 'Resize Image';
        }
      });

      setControlsEnabled(false);
      updateModeUI();
      updateOutputUI();
      updateLivePreview();
    })();
  </script>
</section>