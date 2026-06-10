<section class="tool-section">
    <div class="tool-card">
        <p class="tool-intro">Enter genotype counts to calculate allele frequencies for a two-allele system.</p>
        
        <form class="tool-form" id="alleleFrequencyForm">
            <div class="genotype-inputs">
                <label>
                    Genotype AA count
                    <input type="number" id="aaCount" min="0" step="1" value="0" required>
                </label>
                <label>
                    Genotype Aa count
                    <input type="number" id="aaCountHet" min="0" step="1" value="0" required>
                </label>
                <label>
                    Genotype aa count
                    <input type="number" id="aaCountRec" min="0" step="1" value="0" required>
                </label>
            </div>

            <details class="options-details">
                <summary>Options</summary>
                <div class="options-grid">
                    <label>
                        Allele 1 label
                        <input type="text" id="allele1Label" value="A" maxlength="10">
                    </label>
                    <label>
                        Allele 2 label
                        <input type="text" id="allele2Label" value="a" maxlength="10">
                    </label>
                    <label>
                        Decimal precision
                        <select id="decimalPrecision">
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4" selected>4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                        </select>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" class="checkbox-input" id="showPercentages" checked>
                        <span class="checkbox-box"></span>
                        Show percentages
                    </label>
                </div>
            </details>

            <div class="button-group">
                <button type="submit" id="calculateBtn">Calculate</button>
                <button type="button" id="resetBtn" class="btn-secondary">Reset</button>
                <button type="button" id="exampleBtn" class="btn-secondary">Load Example</button>
            </div>
        </form>

        <div id="resultArea" class="tool-result" style="display:none;">
            <div id="errorMessages" class="error-messages" style="display:none;"></div>
            
            <div id="mainResults">
                <h3>Allele Frequencies</h3>
                <div id="frequencyDisplay" class="frequency-display"></div>
            </div>

            <div id="supportingDetails" class="supporting-details" style="margin-top:1.5rem;">
                <h4>Supporting Calculations</h4>
                <div id="detailsContent"></div>
            </div>

            <div id="formulaBreakdown" class="formula-breakdown" style="margin-top:1rem;">
                <h4>Formula</h4>
                <div id="formulaContent"></div>
            </div>

            <div id="frequencyCheck" class="frequency-check" style="margin-top:0.5rem;"></div>

            <button type="button" id="copyResultsBtn" class="btn-secondary" style="margin-top:1rem;">Copy Results</button>
        </div>

        <details class="help-details" style="margin-top:1rem;">
            <summary>How is this calculated?</summary>
            <div class="help-content">
                <p>Allele frequencies are calculated from genotype counts:</p>
                <ul>
                    <li>Total individuals = AA + Aa + aa</li>
                    <li>Total alleles = Total individuals × 2</li>
                    <li>Allele 1 count = (2 × AA) + Aa</li>
                    <li>Allele 2 count = (2 × aa) + Aa</li>
                    <li>Frequency = Allele count / Total alleles</li>
                </ul>
                <p>This assumes a two-allele system with genotypes AA, Aa, and aa.</p>
            </div>
        </details>
    </div>

    <style>
        .tool-section .genotype-inputs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }
        .tool-section .options-details {
            margin-top: 0.5rem;
        }
        .tool-section .options-details summary {
            cursor: pointer;
            font-weight: 500;
            color: var(--accent);
            padding: 0.3rem 0;
        }
        .tool-section .options-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: 0.8rem;
            padding: 0.8rem;
            background: var(--input-bg);
            border-radius: 8px;
        }
        .tool-section .button-group {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        .tool-section .btn-secondary {
            background-color: var(--input-bg);
            color: var(--text-color);
            border: 1px solid var(--input-border);
        }
        .tool-section .btn-secondary:hover {
            background-color: var(--border);
        }
        .tool-section .frequency-display {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 0.8rem;
        }
        .tool-section .frequency-item {
            padding: 1rem;
            background: var(--input-bg);
            border-radius: 8px;
            border: 1px solid var(--border);
        }
        .tool-section .frequency-item .allele-label {
            font-weight: 600;
            font-size: 1.1rem;
        }
        .tool-section .frequency-item .frequency-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--accent);
            margin-top: 0.3rem;
        }
        .tool-section .frequency-item .percentage-value {
            font-size: 0.9rem;
            color: var(--text-color);
            opacity: 0.8;
        }
        .tool-section .supporting-details,
        .tool-section .formula-breakdown {
            text-align: left;
            padding: 0.8rem;
            background: var(--input-bg);
            border-radius: 8px;
            border: 1px solid var(--border);
        }
        .tool-section .supporting-details h4,
        .tool-section .formula-breakdown h4 {
            margin: 0 0 0.5rem 0;
            font-size: 0.95rem;
        }
        .tool-section .error-messages {
            color: #dc3545;
            padding: 0.8rem;
            background: rgba(220,53,69,0.1);
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .tool-section .frequency-check {
            font-size: 0.9rem;
            color: var(--text-color);
            opacity: 0.8;
            padding: 0.5rem;
            background: var(--input-bg);
            border-radius: 8px;
            border: 1px solid var(--border);
        }
        .tool-section .help-details summary {
            cursor: pointer;
            font-weight: 500;
            color: var(--accent);
        }
        .tool-section .help-content {
            margin-top: 0.5rem;
            padding: 0.8rem;
            background: var(--input-bg);
            border-radius: 8px;
            font-size: 0.9rem;
        }
        .tool-section .help-content ul {
            margin: 0.5rem 0;
            padding-left: 1.5rem;
        }
        .tool-section .tool-intro {
            margin-bottom: 1rem;
            font-size: 0.95rem;
            color: var(--text-color);
            opacity: 0.9;
        }
        @media (max-width: 600px) {
            .tool-section .genotype-inputs {
                grid-template-columns: 1fr;
            }
            .tool-section .options-grid {
                grid-template-columns: 1fr;
            }
            .tool-section .frequency-display {
                grid-template-columns: 1fr;
            }
            .tool-section .button-group {
                flex-direction: column;
            }
            .tool-section .button-group button {
                width: 100%;
            }
        }
    </style>

    <script>
        (function() {
            const form = document.getElementById('alleleFrequencyForm');
            const aaInput = document.getElementById('aaCount');
            const aaHetInput = document.getElementById('aaCountHet');
            const aaRecInput = document.getElementById('aaCountRec');
            const allele1LabelInput = document.getElementById('allele1Label');
            const allele2LabelInput = document.getElementById('allele2Label');
            const precisionSelect = document.getElementById('decimalPrecision');
            const showPercentagesCheck = document.getElementById('showPercentages');
            const resultArea = document.getElementById('resultArea');
            const errorMessages = document.getElementById('errorMessages');
            const frequencyDisplay = document.getElementById('frequencyDisplay');
            const detailsContent = document.getElementById('detailsContent');
            const formulaContent = document.getElementById('formulaContent');
            const frequencyCheck = document.getElementById('frequencyCheck');
            const calculateBtn = document.getElementById('calculateBtn');
            const resetBtn = document.getElementById('resetBtn');
            const exampleBtn = document.getElementById('exampleBtn');
            const copyBtn = document.getElementById('copyResultsBtn');

            function getDefaults() {
                return {
                    aa: 0,
                    aaHet: 0,
                    aaRec: 0,
                    allele1Label: 'A',
                    allele2Label: 'a',
                    precision: 4,
                    showPercentages: true
                };
            }

            function validateInputs() {
                const errors = [];
                const aa = aaInput.value.trim();
                const aaHet = aaHetInput.value.trim();
                const aaRec = aaRecInput.value.trim();

                if (aa === '' || aaHet === '' || aaRec === '') {
                    errors.push('All genotype count fields are required.');
                    return { valid: false, errors };
                }

                const aaNum = Number(aa);
                const aaHetNum = Number(aaHet);
                const aaRecNum = Number(aaRec);

                if (isNaN(aaNum) || isNaN(aaHetNum) || isNaN(aaRecNum)) {
                    errors.push('Genotype counts must be valid numbers.');
                    return { valid: false, errors };
                }

                if (!Number.isInteger(aaNum) || !Number.isInteger(aaHetNum) || !Number.isInteger(aaRecNum)) {
                    errors.push('Genotype counts must be whole numbers.');
                    return { valid: false, errors };
                }

                if (aaNum < 0 || aaHetNum < 0 || aaRecNum < 0) {
                    errors.push('Genotype counts cannot be negative.');
                    return { valid: false, errors };
                }

                const total = aaNum + aaHetNum + aaRecNum;
                if (total === 0) {
                    errors.push('Total sample size must be greater than zero.');
                    return { valid: false, errors };
                }

                return { valid: true, errors: [], aa: aaNum, aaHet: aaHetNum, aaRec: aaRecNum, total };
            }

            function getLabels() {
                let label1 = allele1LabelInput.value.trim();
                let label2 = allele2LabelInput.value.trim();
                const defaults = getDefaults();

                if (!label1) label1 = defaults.allele1Label;
                if (!label2) label2 = defaults.allele2Label;

                if (label1 === label2) {
                    label1 = defaults.allele1Label;
                    label2 = defaults.allele2Label;
                }

                return { label1, label2 };
            }

            function calculate() {
                const validation = validateInputs();
                if (!validation.valid) {
                    resultArea.style.display = 'block';
                    errorMessages.style.display = 'block';
                    errorMessages.innerHTML = validation.errors.join('<br>');
                    frequencyDisplay.innerHTML = '';
                    detailsContent.innerHTML = '';
                    formulaContent.innerHTML = '';
                    frequencyCheck.innerHTML = '';
                    return;
                }

                errorMessages.style.display = 'none';
                errorMessages.innerHTML = '';

                const { aa, aaHet, aaRec, total } = validation;
                const { label1, label2 } = getLabels();
                const precision = parseInt(precisionSelect.value, 10);
                const showPercentages = showPercentagesCheck.checked;

                const totalAlleles = total * 2;
                const allele1Count = (2 * aa) + aaHet;
                const allele2Count = (2 * aaRec) + aaHet;

                const allele1Freq = allele1Count / totalAlleles;
                const allele2Freq = allele2Count / totalAlleles;

                const freq1Formatted = allele1Freq.toFixed(precision);
                const freq2Formatted = allele2Freq.toFixed(precision);

                let freq1Percent = '';
                let freq2Percent = '';
                if (showPercentages) {
                    freq1Percent = (allele1Freq * 100).toFixed(precision - 2 >= 0 ? precision - 2 : 0);
                    freq2Percent = (allele2Freq * 100).toFixed(precision - 2 >= 0 ? precision - 2 : 0);
                }

                frequencyDisplay.innerHTML = `
                    <div class="frequency-item">
                        <div class="allele-label">Allele ${label1}</div>
                        <div class="frequency-value">${freq1Formatted}</div>
                        ${showPercentages ? `<div class="percentage-value">${freq1Percent}%</div>` : ''}
                    </div>
                    <div class="frequency-item">
                        <div class="allele-label">Allele ${label2}</div>
                        <div class="frequency-value">${freq2Formatted}</div>
                        ${showPercentages ? `<div class="percentage-value">${freq2Percent}%</div>` : ''}
                    </div>
                `;

                detailsContent.innerHTML = `
                    <p><strong>Total individuals:</strong> ${total}</p>
                    <p><strong>Total alleles counted:</strong> ${totalAlleles}</p>
                    <p><strong>Count of allele ${label1}:</strong> ${allele1Count}</p>
                    <p><strong>Count of allele ${label2}:</strong> ${allele2Count}</p>
                `;

                formulaContent.innerHTML = `
                    <p>${label1} count = 2 × AA + Aa = 2 × ${aa} + ${aaHet} = ${allele1Count}</p>
                    <p>${label2} count = 2 × aa + Aa = 2 × ${aaRec} + ${aaHet} = ${allele2Count}</p>
                    <p>Frequency = allele count / total alleles</p>
                `;

                const sumFreq = (allele1Freq + allele2Freq).toFixed(precision);
                let checkText = `Sum of frequencies: ${sumFreq}`;
                if (showPercentages) {
                    const sumPercent = (parseFloat(freq1Percent) + parseFloat(freq2Percent)).toFixed(precision - 2 >= 0 ? precision - 2 : 0);
                    checkText += ` (${sumPercent}%)`;
                }
                frequencyCheck.innerHTML = checkText;

                resultArea.style.display = 'block';
            }

            function resetForm() {
                const defaults = getDefaults();
                aaInput.value = defaults.aa;
                aaHetInput.value = defaults.aaHet;
                aaRecInput.value = defaults.aaRec;
                allele1LabelInput.value = defaults.allele1Label;
                allele2LabelInput.value = defaults.allele2Label;
                precisionSelect.value = defaults.precision;
                showPercentagesCheck.checked = defaults.showPercentages;
                resultArea.style.display = 'none';
                errorMessages.style.display = 'none';
                errorMessages.innerHTML = '';
                frequencyDisplay.innerHTML = '';
                detailsContent.innerHTML = '';
                formulaContent.innerHTML = '';
                frequencyCheck.innerHTML = '';
            }

            function loadExample() {
                aaInput.value = 50;
                aaHetInput.value = 30;
                aaRecInput.value = 20;
                allele1LabelInput.value = 'A';
                allele2LabelInput.value = 'a';
                precisionSelect.value = 4;
                showPercentagesCheck.checked = true;
                calculate();
            }

            function copyResults() {
                const validation = validateInputs();
                if (!validation.valid) return;

                const { aa, aaHet, aaRec, total } = validation;
                const { label1, label2 } = getLabels();
                const precision = parseInt(precisionSelect.value, 10);
                const showPercentages = showPercentagesCheck.checked;

                const totalAlleles = total * 2;
                const allele1Count = (2 * aa) + aaHet;
                const allele2Count = (2 * aaRec) + aaHet;
                const allele1Freq = allele1Count / totalAlleles;
                const allele2Freq = allele2Count / totalAlleles;

                let text = `Allele Frequency Results\n`;
                text += `========================\n\n`;
                text += `Allele ${label1} frequency: ${allele1Freq.toFixed(precision)}`;
                if (showPercentages) text += ` (${(allele1Freq * 100).toFixed(precision - 2 >= 0 ? precision - 2 : 0)}%)`;
                text += `\n`;
                text += `Allele ${label2} frequency: ${allele2Freq.toFixed(precision)}`;
                if (showPercentages) text += ` (${(allele2Freq * 100).toFixed(precision - 2 >= 0 ? precision - 2 : 0)}%)`;
                text += `\n\n`;
                text += `Total individuals: ${total}\n`;
                text += `Total alleles: ${totalAlleles}\n`;
                text += `Count of allele ${label1}: ${allele1Count}\n`;
                text += `Count of allele ${label2}: ${allele2Count}\n`;

                navigator.clipboard.writeText(text).then(() => {
                    const originalText = copyBtn.textContent;
                    copyBtn.textContent = 'Copied!';
                    setTimeout(() => {
                        copyBtn.textContent = originalText;
                    }, 2000);
                }).catch(() => {
                    alert('Failed to copy results.');
                });
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                calculate();
            });

            resetBtn.addEventListener('click', resetForm);
            exampleBtn.addEventListener('click', loadExample);
            copyBtn.addEventListener('click', copyResults);

            form.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    calculate();
                }
            });
        })();
    </script>
</section>