<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

function getMonthlySalesData($pdo) {
    $stmt = $pdo->prepare("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            DATE_FORMAT(created_at, '%M %Y') as month_name,
            SUM(total_amount) as total_sales,
            COUNT(*) as order_count
        FROM orders 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            AND order_status NOT IN ('cancelled', 'cancel_requested')
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get Top 5 Selling Products
function getTopSellingProducts($pdo) {
    $stmt = $pdo->prepare("
        SELECT 
            p.name as product_name,
            p.product_id,
            SUM(oi.quantity) as total_quantity,
            SUM(oi.quantity * oi.unit_price) as total_revenue,
            COUNT(DISTINCT o.order_id) as order_count
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        JOIN orders o ON oi.order_id = o.order_id
        WHERE o.order_status NOT IN ('cancelled', 'cancel_requested')
            AND o.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY p.product_id, p.name
        ORDER BY total_quantity DESC
        LIMIT 5
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get Sales Summary Statistics
function getSalesSummary($pdo) {
    $stmt = $pdo->prepare("
        SELECT 
            SUM(total_amount) as total_revenue,
            COUNT(*) as total_orders,
            AVG(total_amount) as avg_order_value,
            MAX(total_amount) as highest_order,
            MIN(total_amount) as lowest_order
        FROM orders 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            AND order_status NOT IN ('cancelled', 'cancel_requested')
    ");
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch the data
$monthlySalesData = getMonthlySalesData($pdo);
$topProductsData = getTopSellingProducts($pdo);
$salesSummary = getSalesSummary($pdo);

// Prepare data for Chart.js
$monthlyLabels = [];
$monthlySales = [];
$monthlyOrders = [];

// Fill in missing months with zero values
$currentDate = new DateTime();
$currentDate->sub(new DateInterval('P11M')); // Go back 11 months

for ($i = 0; $i < 12; $i++) {
    $monthKey = $currentDate->format('Y-m');
    $monthLabel = $currentDate->format('M Y');
    
    // Find matching data
    $found = false;
    foreach ($monthlySalesData as $data) {
        if ($data['month'] === $monthKey) {
            $monthlyLabels[] = $monthLabel;
            $monthlySales[] = (float)$data['total_sales'];
            $monthlyOrders[] = (int)$data['order_count'];
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        $monthlyLabels[] = $monthLabel;
        $monthlySales[] = 0;
        $monthlyOrders[] = 0;
    }
    
    $currentDate->add(new DateInterval('P1M'));
}

// Prepare product data
$productLabels = [];
$productQuantities = [];
$productRevenue = [];

foreach ($topProductsData as $product) {
    $productLabels[] = $product['product_name'];
    $productQuantities[] = (int)$product['total_quantity'];
    $productRevenue[] = (float)$product['total_revenue'];
}

// Convert to JSON for JavaScript
$chartData = [
    'monthly' => [
        'labels' => $monthlyLabels,
        'sales' => $monthlySales,
        'orders' => $monthlyOrders
    ],
    'products' => [
        'labels' => $productLabels,
        'quantities' => $productQuantities,
        'revenue' => $productRevenue
    ],
    'summary' => $salesSummary
];

include '../includes/admin_header.php';
?>

<section class="analytics-page">
    <div class="container">
        <div class="page-header">
            <h2>Sales Analytics</h2>
            <p>Track your business performance with detailed insights</p>
        </div>
        
        <div class="charts-grid">
            <!-- Monthly Sales Chart -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3>Monthly Sales Performance</h3>
                    <div class="chart-controls">
                        <select id="monthlyChartType">
                            <option value="bar">Bar Chart</option>
                            <option value="line">Line Chart</option>
                        </select>
                    </div>
                </div>
                <div class="chart-wrapper">
                    <canvas id="monthlySalesChart"></canvas>
                </div>
            </div>
            
            <!-- Top Products Chart -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3>Top 5 Selling Products</h3>
                    <div class="chart-controls">
                        <select id="topProductsChartType">
                            <option value="pie">Pie Chart</option>
                            <option value="doughnut">Doughnut Chart</option>
                            <option value="bar">Bar Chart</option>
                        </select>
                    </div>
                </div>
                <div class="chart-wrapper">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card">
                <h4>Total Revenue (12 months)</h4>
                <p class="summary-value">RM<?= number_format($salesSummary['total_revenue'] ?? 0, 2) ?></p>
            </div>
            <div class="summary-card">
                <h4>Total Orders</h4>
                <p class="summary-value"><?= number_format($salesSummary['total_orders'] ?? 0) ?></p>
            </div>
            <div class="summary-card">
                <h4>Average Order Value</h4>
                <p class="summary-value">RM<?= number_format($salesSummary['avg_order_value'] ?? 0, 2) ?></p>
            </div>
            <div class="summary-card">
                <h4>Highest Order</h4>
                <p class="summary-value">RM<?= number_format($salesSummary['highest_order'] ?? 0, 2) ?></p>
            </div>
        </div>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
// JavaScript code for the charts
const chartData = <?= json_encode($chartData) ?>;

let monthlySalesChart;
let topProductsChart;

document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    setupChartControls();
});

function initializeCharts() {
    // Monthly Sales Chart
    const monthlyCtx = document.getElementById('monthlySalesChart').getContext('2d');
    monthlySalesChart = new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: chartData.monthly.labels,
            datasets: [{
                label: 'Sales (RM)',
                data: chartData.monthly.sales,
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'Orders Count',
                data: chartData.monthly.orders,
                type: 'line',
                yAxisID: 'y1',
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'RM' + value.toLocaleString();
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 0) {
                                return 'Sales: RM' + context.parsed.y.toLocaleString();
                            } else {
                                return 'Orders: ' + context.parsed.y;
                            }
                        }
                    }
                }
            }
        }
    });

    // Top Products Chart
    const productsCtx = document.getElementById('topProductsChart').getContext('2d');
    topProductsChart = new Chart(productsCtx, {
        type: 'pie',
        data: {
            labels: chartData.products.labels,
            datasets: [{
                data: chartData.products.quantities,
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB', 
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' units (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

function setupChartControls() {
    // Monthly chart type switcher
    document.getElementById('monthlyChartType').addEventListener('change', function(e) {
        const newType = e.target.value;
        monthlySalesChart.destroy();
        
        const ctx = document.getElementById('monthlySalesChart').getContext('2d');
        monthlySalesChart = new Chart(ctx, {
            type: newType,
            data: {
                labels: chartData.monthly.labels,
                datasets: [{
                    label: 'Sales (RM)',
                    data: chartData.monthly.sales,
                    backgroundColor: newType === 'line' ? 'rgba(54, 162, 235, 0.2)' : 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    tension: newType === 'line' ? 0.4 : 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'RM' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Sales: RM' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    });

    // Top products chart type switcher
    document.getElementById('topProductsChartType').addEventListener('change', function(e) {
        const newType = e.target.value;
        topProductsChart.destroy();
        
        const ctx = document.getElementById('topProductsChart').getContext('2d');
        let options = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: newType === 'bar' ? 'top' : 'right'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (newType === 'bar') {
                                return context.label + ': ' + context.parsed.y + ' units';
                            } else {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' units (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        };

        if (newType === 'bar') {
            options.scales = {
                y: {
                    beginAtZero: true
                }
            };
        }

        topProductsChart = new Chart(ctx, {
            type: newType,
            data: {
                labels: chartData.products.labels,
                datasets: [{
                    data: chartData.products.quantities,
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB', 
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF'
                    ],
                    borderWidth: 1
                }]
            },
            options: options
        });
    });
}
</script>

<?php include '../includes/admin_footer.php'; ?>