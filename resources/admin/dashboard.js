
import './dashboard.css'; 

const colorPalette = ['#b8e3e9', '#93b1b5', '#4f7c82'];

// Fetch the data from the API endpoint
fetch('/api/complaints-data')
  .then(response => response.json())
  .then(({ labels, data }) => {
    const complaintStatusLabels = labels;
    const complaintStatusData = data;

    // Calculate total for center text
    const total = complaintStatusData.reduce((acc, val) => acc + val, 0);

    new Chart(document.getElementById('complaintStatusChart').getContext('2d'), {
      type: 'doughnut',
      data: {
          labels: complaintStatusLabels.map(label => label.replace('_', ' ').toUpperCase()),
          datasets: [{
              data: complaintStatusData,
              backgroundColor: colorPalette.slice(0, complaintStatusLabels.length),
              borderWidth: 0,
              hoverOffset: 6,
              borderRadius: 10,
              borderSkipped: false,
          }]
      },
      options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '75%',
          plugins: { 
              legend: { display: false },
              tooltip: {
                  backgroundColor: '#ffffff',
                  titleColor: '#333',
                  bodyColor: '#333',
                  borderColor: '#ddd',
                  borderWidth: 1,
                  cornerRadius: 4,
                  padding: 10,
                  displayColors: false,
              },
          },
          animation: {
              animateScale: true,
              animateRotate: true
          }
      }
    });

    // Optional: Add center text (requires additional CSS)
    document.getElementById('chartCenterText').innerText = total;
  })
  .catch(error => console.error('Error fetching complaints data:', error));
