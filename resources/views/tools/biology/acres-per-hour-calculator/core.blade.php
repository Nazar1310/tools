<section class="tool-section">
    <div class="tool-card">
        <p style="margin-bottom:1rem;font-size:0.95rem;color:var(--text-muted);">Calculate how many acres you can cover per hour based on your work rate and time.</p>
        
        <form class="tool-form" id="acresPerHourForm">
            <label>
                Acres Covered
                <input type="number" id="acres" step="any" min="0" placeholder="e.g. 25" required>
                <span class="error-message" id="acresError" style="color:#dc3545;font-size:0.85rem;display:none;"></span>
            </label>

            <fieldset style="border:none;padding:0;margin:0;">
                <legend style="font-size:0.95rem;margin-bottom:0.3rem;">Time Spent</legend>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                    <label>
                        Hours
                        <input type="number" id="hours" step="any" min="0" placeholder="e.g. 2">
                    </label>
                    <label>
                        Minutes
                        <input type="number" id="minutes" min="0" max="59" placeholder="e.g. 30">
                    </label>
                </div>
                <span class="error-message" id="timeError" style="color:#dc3545;font-size:0.85rem;display:none;"></span>
            </fieldset>

            <label style="flex-direction:row;align-items:center;gap:0.75rem;">
                <span>Decimal places:</span>
                <select id="precision" style="width:auto;min-width:100px;">
                    <option value="1">1 decimal</option>
                    <option value="2" selected>2 decimals</option>
                    <option value="3">3 decimals</option>
                </select>
            </label>

            <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                <button type="submit" id="calculateBtn">Calculate</button>
                <button type="button" id="resetBtn" style="background-color:var(--input-border);color:var(--text-color);">Reset</button>
                <button type="button" id="exampleBtn" style="background-color:transparent;border:1px solid var(--accent);color:var(--accent);">Try Example</button>
            </div>
        </form>

        <div id="resultArea" style="display:none;margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid var(--border);">
            <div class="tool-result">
                <div style="font-size:0.9rem;color:var(--text-muted);margin-bottom:0.25rem;">Acres Per Hour</div>
                <div id="mainResult" style="font-size:2.2rem;font-weight:700;color:var(--accent);"></div>
            </div>

            <div style="margin-top:1.25rem;display:grid;gap:0.5rem;font-size:0.95rem;text-align:left;max-width:400px;margin-left:auto;margin-right:auto;">
                <div style="display:flex;justify-content:space-between;"><span>Acres covered:</span><span id="detailAcres"></span></div>
                <div style="display:flex;justify-content:space-between;"><span>Time spent:</span><span id="detailTime"></span></div>
                <div style="display:flex;justify-content:space-between;"><span>Total time (hours):</span><span id="detailDecimalHours"></span></div>
                <div style="display:flex;justify-content:space-between;"><span>Hours per acre:</span><span id="detailHoursPerAcre"></span></div>
            </div>

            <div id="interpretation" style="margin-top:1rem;font-size:0.9rem;color:var(--text-muted);font-style:italic;"></div>
        </div>

        <div style="margin-top:1.25rem;padding-top:1rem;border-top:1px solid var(--border);font-size:0.85rem;color:var(--text-muted);">
            <strong>Formula:</strong> Acres per hour = Acres covered ÷ Total hours (hours + minutes/60)
        </div>
    </div>

    <script>
        (function() {
            const form = document.getElementById('acresPerHourForm');
            const acresInput = document.getElementById('acres');
            const hoursInput = document.getElementById('hours');
            const minutesInput = document.getElementById('minutes');
            const precisionSelect = document.getElementById('precision');
            const acresError = document.getElementById('acresError');
            const timeError = document.getElementById('timeError');
            const resultArea = document.getElementById('resultArea');
            const mainResult = document.getElementById('mainResult');
            const detailAcres = document.getElementById('detailAcres');
            const detailTime = document.getElementById('detailTime');
            const detailDecimalHours = document.getElementById('detailDecimalHours');
            const detailHoursPerAcre = document.getElementById('detailHoursPerAcre');
            const interpretation = document.getElementById('interpretation');
            const calculateBtn = document.getElementById('calculateBtn');
            const resetBtn = document.getElementById('resetBtn');
            const exampleBtn = document.getElementById('exampleBtn');

            function hideErrors() {
                acresError.style.display = 'none';
                timeError.style.display = 'none';
            }

            function showError(element, message) {
                element.textContent = message;
                element.style.display = 'block';
            }

            function validateInputs() {
                hideErrors();
                let valid = true;

                const acres = parseFloat(acresInput.value);
                if (isNaN(acres) || acres <= 0) {
                    showError(acresError, 'Please enter a number greater than 0 for acres covered.');
                    valid = false;
                }

                const hours = parseFloat(hoursInput.value) || 0;
                const minutes = parseFloat(minutesInput.value) || 0;

                if (hours < 0 || minutes < 0) {
                    showError(timeError, 'Time values cannot be negative.');
                    valid = false;
                }

                if (minutes > 59) {
                    showError(timeError, 'Minutes must be between 0 and 59.');
                    valid = false;
                }

                if (hours === 0 && minutes === 0) {
                    showError(timeError, 'Please enter time spent (hours and/or minutes).');
                    valid = false;
                }

                return valid;
            }

            function calculate() {
                if (!validateInputs()) {
                    resultArea.style.display = 'none';
                    return;
                }

                const acres = parseFloat(acresInput.value);
                const hours = parseFloat(hoursInput.value) || 0;
                const minutes = parseFloat(minutesInput.value) || 0;
                const precision = parseInt(precisionSelect.value, 10);

                const totalHours = hours + (minutes / 60);
                const acresPerHour = acres / totalHours;
                const roundedRate = acresPerHour.toFixed(precision);
                const hoursPerAcre = totalHours / acres;
                const roundedHoursPerAcre = hoursPerAcre.toFixed(precision);

                mainResult.textContent = roundedRate + ' acres/hour';

                detailAcres.textContent = acres;
                detailTime.textContent = hours + 'h ' + minutes + 'm';
                detailDecimalHours.textContent = totalHours.toFixed(4);
                detailHoursPerAcre.textContent = roundedHoursPerAcre + ' hours/acre';

                interpretation.textContent = 'At this pace, you cover about ' + roundedRate + ' acres each hour.';

                resultArea.style.display = 'block';
            }

            function resetForm() {
                form.reset();
                hideErrors();
                resultArea.style.display = 'none';
                acresInput.value = '';
                hoursInput.value = '';
                minutesInput.value = '';
                precisionSelect.value = '2';
            }

            function fillExample() {
                acresInput.value = '25';
                hoursInput.value = '2';
                minutesInput.value = '30';
                precisionSelect.value = '2';
                hideErrors();
                calculate();
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                calculate();
            });

            resetBtn.addEventListener('click', resetForm);
            exampleBtn.addEventListener('click', fillExample);

            [acresInput, hoursInput, minutesInput, precisionSelect].forEach(function(el) {
                el.addEventListener('input', function() {
                    if (resultArea.style.display === 'block') {
                        calculate();
                    }
                });
            });
        })();
    </script>
</section>