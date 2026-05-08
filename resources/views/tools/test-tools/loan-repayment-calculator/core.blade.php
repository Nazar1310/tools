<section class="tool-section">
  <div class="tool-card">
    <form class="tool-form" id="loanRepaymentForm">
      <label for="loanAmount">
        Loan Amount
        <input type="number" id="loanAmount" name="loanAmount" min="0" step="0.01" placeholder="e.g. 25000" required>
      </label>

      <label for="interestRate">
        Interest Rate (% per year)
        <input type="number" id="interestRate" name="interestRate" min="0" step="0.01" placeholder="e.g. 6.5" required>
      </label>

      <label for="loanTerm">
        Loan Term
        <input type="number" id="loanTerm" name="loanTerm" min="0" step="0.01" placeholder="e.g. 5" required>
      </label>

      <label for="termUnit">
        Loan Term Unit
        <select id="termUnit" name="termUnit" required>
          <option value="months">Months</option>
          <option value="years">Years</option>
        </select>
      </label>

      <label for="paymentFrequency">
        Payment Frequency
        <select id="paymentFrequency" name="paymentFrequency">
          <option value="monthly" selected>Monthly</option>
          <option value="biweekly">Biweekly</option>
          <option value="weekly">Weekly</option>
        </select>
      </label>

      <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0.75rem;">
        <button type="submit">Calculate</button>
        <button type="reset" id="resetBtn">Reset</button>
      </div>
    </form>

    <div class="tool-result" id="loanResult" aria-live="polite">
      <p>Enter your loan details and click Calculate to see repayment estimates.</p>
    </div>
  </div>

  <script>
    (function () {
      const form = document.getElementById('loanRepaymentForm');
      const result = document.getElementById('loanResult');
      const loanAmount = document.getElementById('loanAmount');
      const interestRate = document.getElementById('interestRate');
      const loanTerm = document.getElementById('loanTerm');
      const termUnit = document.getElementById('termUnit');
      const paymentFrequency = document.getElementById('paymentFrequency');

      const frequencyMap = {
        monthly: { paymentsPerYear: 12, label: 'monthly payment', periodLabel: 'month' },
        biweekly: { paymentsPerYear: 26, label: 'biweekly payment', periodLabel: 'biweekly period' },
        weekly: { paymentsPerYear: 52, label: 'weekly payment', periodLabel: 'week' }
      };

      function formatCurrency(value) {
        return new Intl.NumberFormat(undefined, {
          style: 'currency',
          currency: 'USD',
          maximumFractionDigits: 2
        }).format(value);
      }

      function formatNumber(value) {
        return new Intl.NumberFormat(undefined, {
          maximumFractionDigits: 2
        }).format(value);
      }

      function showError(message) {
        result.innerHTML = '<p>' + message + '</p>';
      }

      function calculatePayment(principal, annualRate, totalMonths, paymentsPerYear) {
        const totalPayments = Math.round((totalMonths / 12) * paymentsPerYear);
        if (totalPayments <= 0) {
          return null;
        }

        const periodicRate = annualRate / 100 / paymentsPerYear;
        let payment = 0;

        if (periodicRate === 0) {
          payment = principal / totalPayments;
        } else {
          payment = principal * (periodicRate / (1 - Math.pow(1 + periodicRate, -totalPayments)));
        }

        const totalPaid = payment * totalPayments;
        const totalInterest = totalPaid - principal;

        return { payment, totalPaid, totalInterest, totalPayments, periodicRate };
      }

      form.addEventListener('submit', function (e) {
        e.preventDefault();

        const principal = Number(loanAmount.value);
        const rate = Number(interestRate.value);
        const termValue = Number(loanTerm.value);
        const unit = termUnit.value;
        const frequency = paymentFrequency.value || 'monthly';
        const frequencyInfo = frequencyMap[frequency];

        if (!loanAmount.value.trim()) {
          showError('Please enter a loan amount.');
          return;
        }
        if (!interestRate.value.trim()) {
          showError('Please enter an interest rate.');
          return;
        }
        if (!loanTerm.value.trim()) {
          showError('Please enter a loan term.');
          return;
        }

        if (!Number.isFinite(principal) || principal <= 0) {
          showError('Please enter a valid loan amount greater than zero.');
          return;
        }
        if (!Number.isFinite(rate) || rate < 0) {
          showError('Please enter a valid interest rate of zero or more.');
          return;
        }
        if (!Number.isFinite(termValue) || termValue <= 0) {
          showError('Please enter a valid loan term greater than zero.');
          return;
        }

        const totalMonths = unit === 'years' ? termValue * 12 : termValue;
        if (!Number.isFinite(totalMonths) || totalMonths <= 0) {
          showError('Please enter a valid loan term.');
          return;
        }

        const calculation = calculatePayment(principal, rate, totalMonths, frequencyInfo.paymentsPerYear);
        if (!calculation) {
          showError('Unable to calculate repayment details with the provided values.');
          return;
        }

        const equivalentMonthly = calculation.totalPaid / (totalMonths / 1);
        const monthlyEquivalent = calculation.totalPaid / (totalMonths / 1);

        result.innerHTML = `
          <div style="display:grid;gap:1rem;text-align:left;">
            <div>
              <div style="font-size:0.95rem;opacity:0.8;margin-bottom:0.25rem;">${frequencyInfo.label.charAt(0).toUpperCase() + frequencyInfo.label.slice(1)}</div>
              <div style="font-size:1.8rem;font-weight:700;line-height:1.2;">${formatCurrency(calculation.payment)}</div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0.75rem;">
              <div>
                <div style="font-size:0.9rem;opacity:0.8;">Total repaid</div>
                <div style="font-size:1.1rem;font-weight:600;">${formatCurrency(calculation.totalPaid)}</div>
              </div>
              <div>
                <div style="font-size:0.9rem;opacity:0.8;">Total interest</div>
                <div style="font-size:1.1rem;font-weight:600;">${formatCurrency(calculation.totalInterest)}</div>
              </div>
            </div>
            <div style="font-size:0.95rem;opacity:0.9;">
              <div>Number of payments: ${formatNumber(calculation.totalPayments)}</div>
              <div>Loan term: ${formatNumber(termValue)} ${unit}</div>
              <div>Payment schedule: ${frequencyInfo.periodLabel}</div>
              ${frequency !== 'monthly' ? `<div>Equivalent monthly estimate: ${formatCurrency(monthlyEquivalent)}</div>` : ''}
            </div>
          </div>
        `;
      });

      form.addEventListener('reset', function () {
        setTimeout(function () {
          paymentFrequency.value = 'monthly';
          result.innerHTML = '<p>Enter your loan details and click Calculate to see repayment estimates.</p>';
        }, 0);
      });
    })();
  </script>
</section>