<section class="tool-section">
    <div class="tool-card">
        <div class="tool-form" id="imageResizerForm">
            <div id="uploadArea" style="border:2px dashed var(--border);border-radius:12px;padding:2rem;text-align:center;cursor:pointer;transition:all 0.3s;background:var(--input-bg);">
                <div style="font-size:2.5rem;margin-bottom:0.5rem;">📁</div>
                <p style="margin:0 0 0.5rem 0;font-weight:500;">Drag & drop an image here</p>
                <p style="margin:0 0 1rem 0;font-size:0.85rem;color:var(--text-muted);">or click to browse</p>
                <input type="file" id="imageInput" accept="image/jpeg,image/png,image/webp,image/gif" style="display:none;">
                <button type="button" id="browseBtn" style="display:inline-block;width:auto;padding:0.5rem 1.5rem;">Choose Image</button>
                <p style="margin:0.75rem 0 0 0;font-size:0.8rem;color:var(--text-muted);">Supported: JPG, PNG, WebP, GIF</p>
            </div>

            <div id="originalInfo" style="display:none;padding:0.75rem;background:var(--input-bg);border-radius:8px;border:1px solid var(--border);">
                <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:0.5rem;font-size:0.9rem;">
                    <span><strong>File:</strong> <span id="fileName">-</span></span>
                    <span><strong>Dimensions:</strong> <span id="origDimensions">-</span></span>
                    <span><strong>Size:</strong> <span id="origFileSize">-</span></span>
                </div>
            </div>

            <div>
                <label>Preset Size
                    <select id="presetSelect">
                        <option value="custom">Custom</option>
                        <option value="instagram">Instagram Post (1080×1080)</option>
                        <option value="facebook">Facebook Cover (1640×624)</option>
                        <option value="thumbnail">Thumbnail (150×150)</option>
                        <option value="email">Email Banner (600×200)</option>
                        <option value="twitter">Twitter Header (1500×500)</option>
                        <option value="linkedin">LinkedIn Banner (1584×396)</option>
                    </select>
                </label>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                <label>Resize Mode
                    <select id="resizeMode">
                        <option value="fit">Fit within dimensions</option>
                        <option value="exact">Exact dimensions</option>
                        <option value="percent">Scale by percentage</option>
                    </select>
                </label>
                <label>Output Format
                    <select id="outputFormat">
                        <option value="original">Original format</option>
                        <option value="image/jpeg">JPG</option>
                        <option value="image/png">PNG</option>
                        <option value="image/webp">WebP</option>
                    </select>
                </label>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;" id="dimensionInputs">
                <label>Width (px)
                    <input type="number" id="widthInput" min="1" max="10000" placeholder="e.g. 800">
                </label>
                <label>Height (px)
                    <input type="number" id="heightInput" min="1" max="10000" placeholder="e.g. 600">
                </label>
            </div>

            <div id="percentInputContainer" style="display:none;">
                <label>Scale Percentage (%)
                    <input type="number" id="percentInput" min="1" max="500" value="100" step="1">
                </label>
            </div>

            <label class="checkbox-label">
                <input type="checkbox" class="checkbox-input" id="lockAspectRatio" checked>
                <span class="checkbox-box"></span>
                Lock aspect ratio
            </label>

            <details style="border:1px solid var(--border);border-radius:8px;padding:0.5rem 0.75rem;">
                <summary style="cursor:pointer;font-weight:500;font-size:0.95rem;">Export Settings</summary>
                <div style="margin-top:0.75rem;display:grid;gap:0.75rem;">
                    <div id="qualityContainer">
                        <label>Quality: <span id="qualityValue">85</span>%
                            <input type="range" id="qualitySlider" min="1" max="100" value="85" style="padding:0;">
                        </label>
                    </div>
                    <label class="checkbox-label" id="transparencyContainer">
                        <input type="checkbox" class="checkbox-input" id="preserveTransparency" checked>
                        <span class="checkbox-box"></span>
                        Preserve transparency
                    </label>
                </div>
            </details>

            <div style="display:grid;grid-template-columns:1fr auto;gap:0.75rem;">
                <button type="button" id="resizeBtn" disabled>Resize Image</button>
                <button type="button" id="resetBtn" style="background:var(--input-bg);color:var(--text-color);border:1px solid var(--border);width:auto;padding:0.75rem 1.5rem;">Reset</button>
            </div>

            <div id="errorMessages" style="color:#dc3545;font-size:0.9rem;display:none;"></div>
        </div>

        <div id="resultArea" style="display:none;margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid var(--border);">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                <div style="text-align:center;">
                    <p style="font-size:0.85rem;color:var(--text-muted);margin:0 0 0.5rem 0;">Original</p>
                    <img id="originalPreview" style="max-width:100%;max-height:200px;border-radius:8px;border:1px solid var(--border);object-fit:contain;">
                    <p id="origPreviewDims" style="font-size:0.8rem;color:var(--text-muted);margin:0.25rem 0 0 0;"></p>
                </div>
                <div style="text-align:center;">
                    <p style="font-size:0.85rem;color:var(--text-muted);margin:0 0 0.5rem 0;">Resized</p>
                    <img id="resizedPreview" style="max-width:100%;max-height:200px;border-radius:8px;border:1px solid var(--border);object-fit:contain;">
                    <p id="resizedPreviewDims" style="font-size:0.8rem;color:var(--text-muted);margin:0.25rem 0 0 0;"></p>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:0.5rem;text-align:center;font-size:0.9rem;padding:0.75rem;background:var(--input-bg);border-radius:8px;border:1px solid var(--border);margin-bottom:1rem;">
                <div><strong>Format</strong><br><span id="resultFormat">-</span></div>
                <div><strong>New Size</strong><br><span id="resultFileSize">-</span></div>
                <div><strong>Change</strong><br><span id="resultChange">-</span></div>
            </div>

            <button type="button" id="downloadBtn" style="width:100%;">Download Resized Image</button>
        </div>
    </div>

    <script>
        (function() {
            const form = document.getElementById('imageResizerForm');
            const uploadArea = document.getElementById('uploadArea');
            const imageInput = document.getElementById('imageInput');
            const browseBtn = document.getElementById('browseBtn');
            const originalInfo = document.getElementById('originalInfo');
            const fileName = document.getElementById('fileName');
            const origDimensions = document.getElementById('origDimensions');
            const origFileSize = document.getElementById('origFileSize');
            const presetSelect = document.getElementById('presetSelect');
            const resizeMode = document.getElementById('resizeMode');
            const widthInput = document.getElementById('widthInput');
            const heightInput = document.getElementById('heightInput');
            const percentInput = document.getElementById('percentInput');
            const percentInputContainer = document.getElementById('percentInputContainer');
            const dimensionInputs = document.getElementById('dimensionInputs');
            const lockAspectRatio = document.getElementById('lockAspectRatio');
            const outputFormat = document.getElementById('outputFormat');
            const qualitySlider = document.getElementById('qualitySlider');
            const qualityValue = document.getElementById('qualityValue');
            const qualityContainer = document.getElementById('qualityContainer');
            const preserveTransparency = document.getElementById('preserveTransparency');
            const transparencyContainer = document.getElementById('transparencyContainer');
            const resizeBtn = document.getElementById('resizeBtn');
            const resetBtn = document.getElementById('resetBtn');
            const errorMessages = document.getElementById('errorMessages');
            const resultArea = document.getElementById('resultArea');
            const originalPreview = document.getElementById('originalPreview');
            const resizedPreview = document.getElementById('resizedPreview');
            const origPreviewDims = document.getElementById('origPreviewDims');
            const resizedPreviewDims = document.getElementById('resizedPreviewDims');
            const resultFormat = document.getElementById('resultFormat');
            const resultFileSize = document.getElementById('resultFileSize');
            const resultChange = document.getElementById('resultChange');
            const downloadBtn = document.getElementById('downloadBtn');

            let originalImage = null;
            let originalWidth = 0;
            let originalHeight = 0;
            let originalFormat = '';
            let originalFileSizeBytes = 0;
            let originalFile = null;
            let resizedBlob = null;
            let resizedWidth = 0;
            let resizedHeight = 0;

            const presets = {
                custom: { w: '', h: '' },
                instagram: { w: 1080, h: 1080 },
                facebook: { w: 1640, h: 624 },
                thumbnail: { w: 150, h: 150 },
                email: { w: 600, h: 200 },
                twitter: { w: 1500, h: 500 },
                linkedin: { w: 1584, h: 396 }
            };

            function showError(msg) {
                errorMessages.textContent = msg;
                errorMessages.style.display = 'block';
            }

            function clearError() {
                errorMessages.style.display = 'none';
                errorMessages.textContent = '';
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 B';
                const k = 1024;
                const sizes = ['B', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            function getOriginalFormatFromMime(mime) {
                const map = {
                    'image/jpeg': 'JPEG',
                    'image/png': 'PNG',
                    'image/webp': 'WebP',
                    'image/gif': 'GIF'
                };
                return map[mime] || mime.split('/')[1].toUpperCase();
            }

            function loadImage(file) {
                clearError();
                if (!file) {
                    showError('Please select an image file.');
                    return;
                }

                const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    showError('Unsupported file format. Please upload JPG, PNG, WebP, or GIF.');
                    return;
                }

                if (file.size > 50 * 1024 * 1024) {
                    showError('File is too large. Maximum size is 50 MB.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        originalImage = img;
                        originalWidth = img.naturalWidth;
                        originalHeight = img.naturalHeight;
                        originalFormat = getOriginalFormatFromMime(file.type);
                        originalFileSizeBytes = file.size;
                        originalFile = file;

                        fileName.textContent = file.name;
                        origDimensions.textContent = originalWidth + ' × ' + originalHeight + ' px';
                        origFileSize.textContent = formatFileSize(file.size);
                        originalInfo.style.display = 'block';

                        originalPreview.src = e.target.result;
                        origPreviewDims.textContent = originalWidth + ' × ' + originalHeight + ' px';

                        resizeBtn.disabled = false;

                        if (resizeMode.value === 'percent') {
                            percentInput.value = 100;
                        } else {
                            widthInput.value = originalWidth;
                            heightInput.value = originalHeight;
                        }

                        clearError();
                    };
                    img.onerror = function() {
                        showError('Failed to load image. The file may be corrupted.');
                    };
                    img.src = e.target.result;
                };
                reader.onerror = function() {
                    showError('Failed to read file.');
                };
                reader.readAsDataURL(file);
            }

            function handleFileSelect(files) {
                if (files && files.length > 0) {
                    loadImage(files[0]);
                }
            }

            uploadArea.addEventListener('click', function(e) {
                if (e.target !== browseBtn && !browseBtn.contains(e.target)) {
                    imageInput.click();
                }
            });

            browseBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                imageInput.click();
            });

            imageInput.addEventListener('change', function() {
                handleFileSelect(this.files);
            });

            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.style.borderColor = 'var(--accent)';
                this.style.backgroundColor = 'rgba(0,123,255,0.05)';
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.style.borderColor = 'var(--border)';
                this.style.backgroundColor = 'var(--input-bg)';
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.style.borderColor = 'var(--border)';
                this.style.backgroundColor = 'var(--input-bg)';
                const files = e.dataTransfer.files;
                if (files && files.length > 0) {
                    imageInput.files = files;
                    handleFileSelect(files);
                }
            });

            presetSelect.addEventListener('change', function() {
                const preset = presets[this.value];
                if (this.value !== 'custom') {
                    widthInput.value = preset.w;
                    heightInput.value = preset.h;
                    if (resizeMode.value === 'percent') {
                        resizeMode.value = 'fit';
                        togglePercentMode(false);
                    }
                }
            });

            resizeMode.addEventListener('change', function() {
                const isPercent = this.value === 'percent';
                togglePercentMode(isPercent);
                if (!isPercent && originalImage) {
                    widthInput.value = originalWidth;
                    heightInput.value = originalHeight;
                }
            });

            function togglePercentMode(isPercent) {
                if (isPercent) {
                    dimensionInputs.style.display = 'none';
                    percentInputContainer.style.display = 'block';
                } else {
                    dimensionInputs.style.display = 'grid';
                    percentInputContainer.style.display = 'none';
                }
            }

            let updatingDimensions = false;

            widthInput.addEventListener('input', function() {
                if (updatingDimensions) return;
                if (lockAspectRatio.checked && originalWidth > 0 && originalHeight > 0 && this.value) {
                    const w = parseInt(this.value);
                    if (w > 0) {
                        updatingDimensions = true;
                        heightInput.value = Math.round(w * (originalHeight / originalWidth));
                        updatingDimensions = false;
                    }
                }
                if (presetSelect.value !== 'custom') {
                    presetSelect.value = 'custom';
                }
            });

            heightInput.addEventListener('input', function() {
                if (updatingDimensions) return;
                if (lockAspectRatio.checked && originalWidth > 0 && originalHeight > 0 && this.value) {
                    const h = parseInt(this.value);
                    if (h > 0) {
                        updatingDimensions = true;
                        widthInput.value = Math.round(h * (originalWidth / originalHeight));
                        updatingDimensions = false;
                    }
                }
                if (presetSelect.value !== 'custom') {
                    presetSelect.value = 'custom';
                }
            });

            qualitySlider.addEventListener('input', function() {
                qualityValue.textContent = this.value;
            });

            outputFormat.addEventListener('change', function() {
                const fmt = this.value;
                const isLossy = fmt === 'image/jpeg' || fmt === 'image/webp';
                qualityContainer.style.display = isLossy ? 'block' : 'none';
                transparencyContainer.style.display = (fmt === 'image/png' || fmt === 'image/webp') ? 'block' : 'none';
                if (fmt === 'image/jpeg') {
                    preserveTransparency.checked = false;
                }
            });

            qualityContainer.style.display = 'block';
            transparencyContainer.style.display = 'block';

            resizeBtn.addEventListener('click', function() {
                if (!originalImage) {
                    showError('Please upload an image first.');
                    return;
                }

                clearError();

                let targetW, targetH;

                if (resizeMode.value === 'percent') {
                    const pct = parseFloat(percentInput.value);
                    if (!pct || pct < 1 || pct > 500) {
                        showError('Percentage must be between 1 and 500.');
                        return;
                    }
                    targetW = Math.round(originalWidth * pct / 100);
                    targetH = Math.round(originalHeight * pct / 100);
                } else {
                    const w = parseInt(widthInput.value);
                    const h = parseInt(heightInput.value);

                    if ((!w || w < 1) && (!h || h < 1)) {
                        showError('Please enter at least one valid dimension (width or height).');
                        return;
                    }

                    if (w && (w < 1 || w > 10000)) {
                        showError('Width must be between 1 and 10000 pixels.');
                        return;
                    }
                    if (h && (h < 1 || h > 10000)) {
                        showError('Height must be between 1 and 10000 pixels.');
                        return;
                    }

                    if (resizeMode.value === 'fit') {
                        if (w && h) {
                            const scale = Math.min(w / originalWidth, h / originalHeight);
                            targetW = Math.round(originalWidth * scale);
                            targetH = Math.round(originalHeight * scale);
                        } else if (w) {
                            targetW = w;
                            targetH = Math.round(w * (originalHeight / originalWidth));
                        } else {
                            targetH = h;
                            targetW = Math.round(h * (originalWidth / originalHeight));
                        }
                    } else {
                        if (w && h) {
                            targetW = w;
                            targetH = h;
                        } else if (w) {
                            targetW = w;
                            targetH = lockAspectRatio.checked ? Math.round(w * (originalHeight / originalWidth)) : originalHeight;
                        } else {
                            targetH = h;
                            targetW = lockAspectRatio.checked ? Math.round(h * (originalWidth / originalHeight)) : originalWidth;
                        }
                    }
                }

                if (targetW < 1 || targetH < 1) {
                    showError('Resulting dimensions are too small.');
                    return;
                }

                if (targetW > 10000 || targetH > 10000) {
                    showError('Resulting dimensions exceed maximum allowed (10000 px).');
                    return;
                }

                if (resizeMode.value === 'exact' && lockAspectRatio.checked === false && originalImage) {
                    const ratio = originalWidth / originalHeight;
                    const newRatio = targetW / targetH;
                    if (Math.abs(ratio - newRatio) > 0.01) {
                        if (!confirm('The image will be distorted because aspect ratio is not locked. Continue?')) {
                            return;
                        }
                    }
                }

                const fmt = outputFormat.value;
                if (fmt === 'image/jpeg' && preserveTransparency.checked) {
                    preserveTransparency.checked = false;
                }

                const canvas = document.createElement('canvas');
                canvas.width = targetW;
                canvas.height = targetH;
                const ctx = canvas.getContext('2d');

                if (fmt === 'image/jpeg') {
                    ctx.fillStyle = '#FFFFFF';
                    ctx.fillRect(0, 0, targetW, targetH);
                }

                ctx.drawImage(originalImage, 0, 0, targetW, targetH);

                const quality = parseInt(qualitySlider.value) / 100;
                let mimeType = fmt === 'original' ? originalFile.type : fmt;
                if (mimeType === 'image/gif') mimeType = 'image/png';

                canvas.toBlob(function(blob) {
                    if (!blob) {
                        showError('Failed to generate resized image.');
                        return;
                    }

                    resizedBlob = blob;
                    resizedWidth = targetW;
                    resizedHeight = targetH;

                    const url = URL.createObjectURL(blob);
                    resizedPreview.src = url;
                    resizedPreviewDims.textContent = targetW + ' × ' + targetH + ' px';

                    const fmtName = mimeType.split('/')[1].toUpperCase();
                    resultFormat.textContent = fmtName;
                    resultFileSize.textContent = formatFileSize(blob.size);

                    const origArea = originalWidth * originalHeight;
                    const newArea = targetW * targetH;
                    const change = ((newArea - origArea) / origArea * 100);
                    const sign = change >= 0 ? '+' : '';
                    resultChange.textContent = sign + change.toFixed(1) + '%';

                    resultArea.style.display = 'block';
                    clearError();
                }, mimeType, quality);
            });

            downloadBtn.addEventListener('click', function() {
                if (!resizedBlob) {
                    showError('No resized image to download. Please resize first.');
                    return;
                }

                const fmt = outputFormat.value;
                let ext = 'png';
                if (fmt === 'image/jpeg') ext = 'jpg';
                else if (fmt === 'image/webp') ext = 'webp';
                else if (fmt === 'original') {
                    const origExt = originalFile.name.split('.').pop().toLowerCase();
                    ext = origExt === 'gif' ? 'png' : origExt;
                }

                const a = document.createElement('a');
                a.href = URL.createObjectURL(resizedBlob);
                const baseName = originalFile ? originalFile.name.replace(/\.[^/.]+$/, '') : 'resized';
                a.download = baseName + '_resized_' + resizedWidth + 'x' + resizedHeight + '.' + ext;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(a.href);
            });

            resetBtn.addEventListener('click', function() {
                originalImage = null;
                originalWidth = 0;
                originalHeight = 0;
                originalFormat = '';
                originalFileSizeBytes = 0;
                originalFile = null;
                resizedBlob = null;
                resizedWidth = 0;
                resizedHeight = 0;

                imageInput.value = '';
                originalInfo.style.display = 'none';
                fileName.textContent = '-';
                origDimensions.textContent = '-';
                origFileSize.textContent = '-';
                originalPreview.src = '';
                origPreviewDims.textContent = '';
                resizedPreview.src = '';
                resizedPreviewDims.textContent = '';
                widthInput.value = '';
                heightInput.value = '';
                percentInput.value = '100';
                presetSelect.value = 'custom';
                resizeMode.value = 'fit';
                lockAspectRatio.checked = true;
                outputFormat.value = 'original';
                qualitySlider.value = '85';
                qualityValue.textContent = '85';
                preserveTransparency.checked = true;
                resultArea.style.display = 'none';
                resizeBtn.disabled = true;
                clearError();
                togglePercentMode(false);
                dimensionInputs.style.display = 'grid';
                percentInputContainer.style.display = 'none';
                qualityContainer.style.display = 'block';
                transparencyContainer.style.display = 'block';
            });

            togglePercentMode(false);
            resizeBtn.disabled = true;
        })();
    </script>
</section>