const lineConfig = {
    type: 'line',
    data: lineData,
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        stacked: false,
        plugins: {
            title: {
                display: false
            }
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                grid: {
                    drawOnChartArea: false,
                },
            },
        }
    },
};

const pieConfig = {
    type: 'pie',
    data: pieData,
    options: {
        responsive: true,
        plugins: {
            title: {
                display: false
            }
        }
    }
};


       
       
