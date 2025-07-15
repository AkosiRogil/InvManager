// Define the number_format function first
function number_format(number, decimals = 0, dec_point = '.', thousands_sep = ',') {
  number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
  const n = !isFinite(+number) ? 0 : +number;
  const prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
  let s = '';

  const toFixedFix = function(n, prec) {
    const k = Math.pow(10, prec);
    return '' + Math.round(n * k) / k;
  };

  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');

  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, thousands_sep);
  }

  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }

  return s.join(dec_point);
}

// Fetch chart data from PHP backend
fetch('http://localhost/InvManager/js/demo/get_chart_data.php')
  .then(async res => {
    const text = await res.text();
    console.log('Raw response:', text); // Show HTML if it's an error page
    return JSON.parse(text); // Try parsing manually to catch errors
  })
  .then(data => {
    console.log('Parsed JSON:', data);

    const labels = data.map(row => row.date);
    const excellentData = data.map(row => row.Excellent);
    const brokenData = data.map(row => row.Broken);
    const borrowedData = data.map(row => row.Borrowed);

    const ctx = document.getElementById("myAreaChart").getContext('2d');
    const myLineChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          {
            label: "Excellent",
            lineTension: 0.3,
            backgroundColor: "rgba(28, 200, 138, 0.05)",
            borderColor: "rgba(28, 200, 138, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(28, 200, 138, 1)",
            pointBorderColor: "rgba(28, 200, 138, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(28, 200, 138, 1)",
            pointHoverBorderColor: "rgba(28, 200, 138, 1)",
            data: excellentData,
          },
          {
            label: "Broken",
            lineTension: 0.3,
            backgroundColor: "rgba(231, 74, 59, 0.05)",
            borderColor: "rgba(231, 74, 59, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(231, 74, 59, 1)",
            pointBorderColor: "rgba(231, 74, 59, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(231, 74, 59, 1)",
            pointHoverBorderColor: "rgba(231, 74, 59, 1)",
            data: brokenData,
          },
          {
            label: "Borrowed",
            lineTension: 0.3,
            backgroundColor: "rgba(78, 115, 223, 0.05)",
            borderColor: "rgba(78, 115, 223, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(78, 115, 223, 1)",
            pointBorderColor: "rgba(78, 115, 223, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
            data: borrowedData,
          }
        ]
      },
      options: {
        maintainAspectRatio: false,
        scales: {
          xAxes: [{
            // Removed the time configuration that was causing the error
            gridLines: { display: false },
            ticks: { maxTicksLimit: 7 }
          }],
          yAxes: [{
            ticks: {
              beginAtZero: true,
              callback: function(value) {
                return number_format(value); // Now works correctly
              }
            },
            gridLines: {
              color: "rgb(234, 236, 244)",
              zeroLineColor: "rgb(234, 236, 244)",
              drawBorder: false,
              borderDash: [2],
              zeroLineBorderDash: [2]
            }
          }]
        },
        tooltips: {
          mode: 'index',
          intersect: false
        },
        legend: {
          display: true
        }
      }
    });
  })
  .catch(err => {
    console.error('Fetch or JSON error:', err);
  });