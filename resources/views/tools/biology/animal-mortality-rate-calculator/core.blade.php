<section class="tool-section">
    <div class="tool-card">
        <p style="margin:0 0 1rem 0;font-size:0.95rem;color:var(--text-secondary, #666);">Calculate mortality rate from total population and death count.</p>
        
        <form class="tool-form" id="mortalityForm">
            <label>
                Total Population
                <input type="number" id="totalPopulation" min="1" step="1" placeholder="e.g. 500" required>
                <span class="error-message" id="populationError" style="color:#dc3545;font-size:0.85rem;display:none;"></span>
            </label>
            
            <label>
                Number of Deaths
                <input type="number" id="numberDeaths" min="0" step="1" placeholder="e.g. 25" required>
                <span class="error-message" id="deathsError" style="color:#dc3545;font-size:0.85rem;display:none;"></span>
            </label>

            <details style="margin-top:0.5rem;">
                <summary style="cursor:pointer;font-size:0.9rem;color:var(--accent);">More options</summary>
                <div style="margin-top:0.75rem;display:grid;gap:1rem;">
                    <label>
                        Time Period
                        <input type="text" id="timePeriod" placeholder="e.g. Annual, Monthly, Winter">
                    </label>
                    <label>
                        Population Label / Group Name
                        <input type="text" id="groupLabel" placeholder="e.g. Cattle Group A">
                    </label>
                    <label>
                        Decimal Precision
                        <select id="decimalPrecision">
                            <option value="0">0</option>
                            <option value="1">1</option>
                            <option value="2" selected>2</option>
                            <option value="3">3</option>
                        </select>
                    </label>
                </div>
            </details>

            <div style="display:flex;gap:0.75rem;margin-top:0.5rem;">
                <button type="submit" id="calculateBtn">Calculate</button>
                <button type="button" id="resetBtn" style="background-color:var(--input-border, #ccc);color:var(--text-color, #333);">Reset</button>
                <button type="button" id="exampleBtn" style="background-color:transparent;color:var(--accent);border:1px solid var(--accent);">Load Example</button>
            </div>
        </form>

        <div id="resultSection" style="display:none;margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid var(--border);">
            <div id="mainResult" style="font-size:1.8rem;font-weight:700;text-align:center;margin-bottom:0.75rem;"></div>
            
            <div id="calculationDetails" style="text-align:center;font-size:0.95rem;margin-bottom:1rem;color:var(--text-secondary, #555);"></div>
            
            <div id="interpretation" style="text-align:center;font-size:0.9rem;margin-bottom:1rem;font-style:italic;color:var(--text-secondary, #555);"></div>
            
            <div id="inputSummary" style="font-size:0.9rem;display:grid;grid-template-columns:1fr 1fr;gap:0.3rem;max-width:400px;margin:0 auto;"></div>
            
            <div style="text-align:center;margin-top:1rem;">
                <button id="copyResultBtn" style="background-color:transparent;color:var(--accent);border:1px solid var(--accent);padding:0.5rem 1rem;font-size:0.85rem;">Copy Result</button>
            </div>
        </div>

        <div style="margin-top:1.5rem;padding-top:1rem;border-top:1px solid var(--border);font-size:0.85rem;color:var(--text-secondary, #666);">
            <p style="margin:0 0 0.3rem 0;"><strong>Formula:</strong> Mortality Rate (%) = (Number of Deaths ÷ Total Population) × 100</p>
            <p style="margin:0;">This measures the proportion of animals that died during the measured period.</p>
        </div>
    </div>

    <script>
        (function() {
            const form = document.getElementById('mortalityForm');
            const populationInput = document.getElementById('totalPopulation');
            const deathsInput = document.getElementById('numberDeaths');
            const populationError = document.getElementById('populationError');
            const deathsError = document.getElementById('deathsError');
            const timePeriodInput = document.getElementById('timePeriod');
            const groupLabelInput = document.getElementById('groupLabel');
            const precisionSelect = document.getElementById('decimalPrecision');
            const calculateBtn = document.getElementById('calculateBtn');
            const resetBtn = document.getElementById('resetBtn');
            const exampleBtn = document.getElementById('exampleBtn');
            const resultSection = document.getElementById('resultSection');
            const mainResult = document.getElementById('mainResult');
            const calculationDetails = document.getElementById('calculationDetails');
            const interpretation = document.getElementById('interpretation');
            const inputSummary = document.getElementById('inputSummary');
            const copyResultBtn = document.getElementById('copyResultBtn');

            function clearErrors() {
                populationError.style.display = 'none';
                populationError.textContent = '';
                deathsError.style.display = 'none';
                deathsError.textContent = '';
                populationInput.style.borderColor = '';
                deathsInput.style.borderColor = '';
            }

            function showError(input, errorSpan, message) {
                errorSpan.textContent = message;
                errorSpan.style.display = 'block';
                input.style.borderColor = '#dc3545';
            }

            function validateInputs() {
                clearErrors();
                let valid = true;

                const popStr = populationInput.value.trim();
                const deathsStr = deathsInput.value.trim();

                if (popStr === '') {
                    showError(populationInput, populationError, 'Total population is required.');
                    valid = false;
                } else {
                    const pop = Number(popStr);
                    if (isNaN(pop) || !Number.isFinite(pop)) {
                        showError(populationInput, populationError, 'Please enter a valid number.');
                        valid = false;
                    } else if (!Number.isInteger(pop)) {
                        showError(populationInput, populationError, 'Population must be a whole number.');
                        valid = false;
                    } else if (pop <= 0) {
                        showError(populationInput, populationError, 'Population must be greater than 0.');
                        valid = false;
                    }
                }

                if (deathsStr === '') {
                    showError(deathsInput, deathsError, 'Number of deaths is required.');
                    valid = false;
                } else {
                    const deaths = Number(deathsStr);
                    if (isNaN(deaths) || !Number.isFinite(deaths)) {
                        showError(deathsInput, deathsError, 'Please enter a valid number.');
                        valid = false;
                    } else if (!Number.isInteger(deaths)) {
                        showError(deathsInput, deathsError, 'Deaths must be a whole number.');
                        valid = false;
                    } else if (deaths < 0) {
                        showError(deathsInput, deathsError, 'Deaths cannot be negative.');
                        valid = false;
                    } else {
                        const pop = Number(populationInput.value.trim());
                        if (!isNaN(pop) && Number.isFinite(pop) && deaths > pop) {
                            showError(deathsInput, deathsError, 'Deaths cannot exceed total population.');
                            valid = false;
                        }
                    }
                }

                return valid;
            }

            function formatNumber(num) {
                return num.toLocaleString('en-US');
            }

            function calculate() {
                if (!validateInputs()) {
                    resultSection.style.display = 'none';
                    return;
                }

                const population = Number(populationInput.value.trim());
                const deaths = Number(deathsInput.value.trim());
                const precision = Number(precisionSelect.value);
                const timePeriod = timePeriodInput.value.trim();
                const groupLabel = groupLabelInput.value.trim();

                const mortalityRate = (deaths / population) * 100;
                const formattedRate = mortalityRate.toFixed(precision);

                const popFormatted = formatNumber(population);
                const deathsFormatted = formatNumber(deaths);

                mainResult.textContent = `Mortality Rate: ${formattedRate}%`;

                calculationDetails.textContent = `Calculation: (${deathsFormatted} ÷ ${popFormatted}) × 100 = ${formattedRate}%`;

                const perX = mortalityRate > 0 ? Math.round(100 / (100 / mortalityRate)) : 0;
                if (mortalityRate > 0) {
                    interpretation.textContent = `This means approximately ${perX} out of every 100 animals died during the measured period.`;
                } else {
                    interpretation.textContent = 'No deaths recorded — mortality rate is 0%.';
                }

                let summaryHTML = '';
                summaryHTML += `<div><strong>Population:</strong> ${popFormatted}</div>`;
                summaryHTML += `<div><strong>Deaths:</strong> ${deathsFormatted}</div>`;
                if (timePeriod) {
                    summaryHTML += `<div><strong>Period:</strong> ${timePeriod}</div>`;
                }
                if (groupLabel) {
                    summaryHTML += `<div><strong>Group:</strong> ${groupLabel}</div>`;
                }
                inputSummary.innerHTML = summaryHTML;

                resultSection.style.display = 'block';
            }

            function resetForm() {
                form.reset();
                clearErrors();
                resultSection.style.display = 'none';
                populationInput.value = '';
                deathsInput.value = '';
                timePeriodInput.value = '';
                groupLabelInput.value = '';
                precisionSelect.value = '2';
            }

            function loadExample() {
                populationInput.value = '500';
                deathsInput.value = '25';
                timePeriodInput.value = 'Annual';
                groupLabelInput.value = 'Cattle Group A';
                precisionSelect.value = '2';
                clearErrors();
                resultSection.style.display = 'none';
            }

            function copyResult() {
                const pop = populationInput.value.trim();
                const deaths = deathsInput.value.trim();
                if (!pop || !deaths) return;

                const population = Number(pop);
                const deathCount = Number(deaths);
                const precision = Number(precisionSelect.value);
                const mortalityRate = (deathCount / population) * 100;
                const formattedRate = mortalityRate.toFixed(precision);
                const timePeriod = timePeriodInput.value.trim();
                const groupLabel = groupLabelInput.value.trim();

                let text = `Mortality Rate: ${formattedRate}%\n`;
                text += `Calculation: (${formatNumber(deathCount)} ÷ ${formatNumber(population)}) × 100 = ${formattedRate}%\n`;
                text += `Population: ${formatNumber(population)}\n`;
                text += `Deaths: ${formatNumber(deathCount)}\n`;
                if (timePeriod) text += `Period: ${timePeriod}\n`;
                if (groupLabel) text += `Group: ${groupLabel}\n`;

                navigator.clipboard.writeText(text).catch(() => {
                    const textarea = document.createElement('textarea');
                    textarea.value = text;
                    document.body.appendChild(textarea);
                    textarea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textarea);
                });
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                calculate();
            });

            resetBtn.addEventListener('click', resetForm);
            exampleBtn.addEventListener('click', loadExample);
            copyResultBtn.addEventListener('click', copyResult);

            populationInput.addEventListener('input', function() {
                if (populationError.style.display === 'block') {
                    clearErrors();
                }
            });

            deathsInput.addEventListener('input', function() {
                if (deathsError.style.display === 'block') {
                    clearErrors();
                }
            });
        })();
    </script>
</section>