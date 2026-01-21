<?php
// File: admin/analytics.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

Auth::requireAdmin();

$db = Database::getInstance();

// Lấy tham số lọc
$groupBy = $_GET['group_by'] ?? 'day'; // day, month, year
$startDate = $_GET['start_date'] ?? date('Y-m-01'); // Mặc định: Đầu tháng này
$endDate = $_GET['end_date'] ?? date('Y-m-d'); // Mặc định: Hôm nay

// Xác định định dạng ngày cho SQL và hiển thị
switch ($groupBy) {
    case 'month':
        $sqlDateFormat = '%Y-%m';
        $chartLabel = 'Monthly Revenue';
        break;
    case 'year':
        $sqlDateFormat = '%Y';
        $chartLabel = 'Yearly Revenue';
        break;
    case 'day':
    default:
        $sqlDateFormat = '%Y-%m-%d';
        $chartLabel = 'Daily Revenue';
        break;
}

// Chuẩn bị thời gian truy vấn (bao gồm cả giờ phút giây)
$queryStartDate = $startDate . ' 00:00:00';
$queryEndDate = $endDate . ' 23:59:59';

try {
    // Truy vấn thống kê doanh thu
    // Chỉ tính các đơn hàng đã xác nhận, đã giao hoặc đang giao (loại bỏ pending/cancelled nếu cần thiết)
    $query = "
        SELECT 
            DATE_FORMAT(created_at, ?) as date_group,
            SUM(total) as revenue,
            COUNT(*) as order_count,
            AVG(total) as avg_order_value
        FROM orders 
        WHERE status IN ('confirmed', 'shipped', 'delivered') 
        AND created_at >= ? AND created_at <= ?
        GROUP BY date_group 
        ORDER BY date_group ASC
    ";

    $stmt = $db->getConnection()->prepare($query);
    $stmt->execute([$sqlDateFormat, $queryStartDate, $queryEndDate]);
    $statsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Chuẩn bị dữ liệu cho biểu đồ Chart.js
    $labels = [];
    $revenueData = [];
    $orderData = [];
    
    $totalRevenue = 0;
    $totalOrders = 0;

    foreach ($statsData as $row) {
        $labels[] = $row['date_group'];
        $revenueData[] = $row['revenue'];
        $orderData[] = $row['order_count'];
        
        $totalRevenue += $row['revenue'];
        $totalOrders += $row['order_count'];
    }
    
    $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .analytics-filters {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin: 0 30px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            gap: 20px;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .filter-group label {
            font-size: 14px;
            font-weight: 500;
            color: var(--gray);
        }
        .filter-group input, .filter-group select {
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 5px;
            font-size: 14px;
        }
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 0 30px 20px;
        }
        .summary-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
        }
        .summary-card h3 {
            font-size: 28px;
            color: var(--primary);
            margin-bottom: 5px;
        }
        .summary-card p {
            color: var(--gray);
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-header">
                <h2>Revenue Analytics</h2>
            </div>
            
            <!-- Filters -->
            <form method="GET" class="analytics-filters">
                <div class="filter-group">
                    <label>Date Range</label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="date" name="start_date" value="<?php echo $startDate; ?>">
                        <span>to</span>
                        <input type="date" name="end_date" value="<?php echo $endDate; ?>">
                    </div>
                </div>
                
                <div class="filter-group">
                    <label>Group By</label>
                    <select name="group_by">
                        <option value="day" <?php echo $groupBy == 'day' ? 'selected' : ''; ?>>Day</option>
                        <option value="month" <?php echo $groupBy == 'month' ? 'selected' : ''; ?>>Month</option>
                        <option value="year" <?php echo $groupBy == 'year' ? 'selected' : ''; ?>>Year</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Apply Filter</button>
            </form>
            
            <!-- Summary Cards -->
            <div class="summary-cards">
                <div class="summary-card">
                    <h3><?php echo number_format($totalRevenue, 0, ',', '.'); ?>₫</h3>
                    <p>Total Revenue</p>
                </div>
                <div class="summary-card">
                    <h3><?php echo number_format($totalOrders); ?></h3>
                    <p>Total Orders</p>
                </div>
                <div class="summary-card">
                    <h3><?php echo number_format($avgOrderValue, 0, ',', '.'); ?>₫</h3>
                    <p>Avg. Order Value</p>
                </div>
            </div>
            
            <!-- Chart -->
            <div class="chart-container" style="margin: 0 30px 30px;">
                <h3>Revenue Chart</h3>
                <canvas id="revenueChart" height="100"></canvas>
            </div>
            
            <!-- Data Table -->
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date / Period</th>
                            <th>Orders</th>
                            <th>Revenue</th>
                            <th>Avg. Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($statsData as $row): ?>
                        <tr>
                            <td><?php echo $row['date_group']; ?></td>
                            <td><?php echo number_format($row['order_count']); ?></td>
                            <td><?php echo number_format($row['revenue'], 0, ',', '.'); ?>₫</td>
                            <td><?php echo number_format($row['avg_order_value'], 0, ',', '.'); ?>₫</td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($statsData)): ?>
                            <tr><td colspan="4" style="text-align:center; padding: 20px;">No data found for this period.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [
                    {
                        label: 'Revenue (VND)',
                        data: <?php echo json_encode($revenueData); ?>,
                        backgroundColor: 'rgba(67, 97, 238, 0.5)',
                        borderColor: 'rgba(67, 97, 238, 1)',
                        borderWidth: 1,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Orders',
                        data: <?php echo json_encode($orderData); ?>,
                        type: 'line',
                        borderColor: '#f72585',
                        backgroundColor: '#f72585',
                        borderWidth: 2,
                        tension: 0.3,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Revenue' }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { display: true, text: 'Orders' },
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                }
            }
        });
    </script>
</body>
</html>