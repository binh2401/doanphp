 <?php
    require_once "../config/database.php"; // Kết nối CSDL
    require_once "../public/session.php"; // Quản lý phiên
    checkAdmin();

    // Truy vấn tổng số đơn hàng
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_orders FROM orders");
    $stmt->execute();
    $totalOrders = $stmt->fetch(PDO::FETCH_ASSOC)['total_orders'];
    // Truy vấn tổng số người dùng
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_users FROM users");
    $stmt->execute();
    $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

    // Truy vấn tổng doanh thu
    $stmt = $conn->prepare("
    SELECT SUM(od.price * od.quantity) AS total_sales
    FROM orders o
    JOIN order_detail od ON o.id = od.order_id
");
    $stmt->execute();
    $totalSales = $stmt->fetch(PDO::FETCH_ASSOC)['total_sales'];

    // Truy vấn thu nhập trong tuần
    $stmt = $conn->prepare("
    SELECT SUM(od.price * od.quantity) AS weekly_income
    FROM orders o
    JOIN order_detail od ON o.id = od.order_id
    WHERE o.order_date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)
");
    $stmt->execute();
    $weeklyIncome = $stmt->fetch(PDO::FETCH_ASSOC)['weekly_income'];


    // Truy vấn các đơn hàng gần đây
    $stmt = $conn->prepare("
    SELECT o.id AS order_id, o.order_date, p.name AS product_name, od.quantity, od.price
    FROM orders o
    JOIN order_detail od ON o.id = od.order_id
    JOIN products p ON od.product_id = p.id
    ORDER BY o.order_date DESC
    LIMIT 10
");
    $stmt->execute();
    $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);


    include 'header_admin.php';
    ?>
 <div class="pc-container">
     <div class="pc-content">
         <!-- [ breadcrumb ] start -->
         <div class="page-header">
             <div class="page-block">
                 <div class="row align-items-center">
                     <div class="col-md-12">
                         <div class="page-header-title">
                             <h5 class="m-b-10">Home</h5>
                         </div>
                         <ul class="breadcrumb">
                             <li class="breadcrumb-item"><a href="../dashboard/index.html">Home</a></li>
                             <li class="breadcrumb-item"><a href="javascript: void(0)">Dashboard</a></li>
                             <li class="breadcrumb-item" aria-current="page">Home</li>
                         </ul>
                     </div>
                 </div>
             </div>
         </div>
         <!-- [ breadcrumb ] end -->
         <!-- [ Main Content ] start -->
         <div class="row">
             <!-- [ sample-page ] start -->

             <div class="col-md-6 col-xl-3">
                 <div class="card">
                     <div class="card-body">
                         <h6 class="mb-2 f-w-400 text-muted">Total Users</h6>
                         <h4 class="mb-3"><?= number_format($totalUsers) ?> <span class="badge bg-light-success border border-success"><i class="ti ti-trending-up"></i> 70.5%</span></h4>
                         <p class="mb-0 text-muted text-sm">You made an extra <span class="text-success">8,900</span> this year</p>
                     </div>
                 </div>
             </div>
             <div class="col-md-6 col-xl-3">
                 <div class="card">
                     <div class="card-body">
                         <h6 class="mb-2 f-w-400 text-muted">Total Orders</h6>
                         <h4 class="mb-3"><?= number_format($totalOrders) ?> <span class="badge bg-light-warning border border-warning"><i class="ti ti-trending-down"></i> 27.4%</span></h4>
                         <p class="mb-0 text-muted text-sm">You made an extra <span class="text-warning">1,943</span> this year</p>
                     </div>
                 </div>
             </div>
             <div class="col-md-6 col-xl-3">
                 <div class="card">
                     <div class="card-body">
                         <h6 class="mb-2 f-w-400 text-muted">Total Sales</h6>
                         <h4 class="mb-3"><?= number_format($totalSales, 0, ',', '.') ?> VND <span class="badge bg-light-danger border border-danger"><i class="ti ti-trending-down"></i> 27.4%</span></h4>
                         <p class="mb-0 text-muted text-sm">You made an extra <span class="text-danger">$20,395</span> this year</p>
                     </div>
                 </div>
             </div>



             <div class="col-md-12 col-xl-8">
                 <h5 class="mb-3">Recent Orders</h5>
                 <div class="card tbl-card">
                     <div class="card-body">
                         <div class="table-responsive">
                             <table class="table table-hover table-borderless mb-0">
                                 <thead>
                                     <tr>
                                         <th>TRACKING NO.</th>
                                         <th>PRODUCT NAME</th>
                                         <th>TOTAL ORDER</th>

                                         <th class="text-end">TOTAL AMOUNT</th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                     <?php foreach ($recentOrders as $order): ?>
                                         <tr>
                                             <td><a href="#" class="text-muted"><?= htmlspecialchars($order['order_id']) ?></a></td>
                                             <td><?= htmlspecialchars($order['product_name']) ?></td>
                                             <td><?= htmlspecialchars($order['quantity']) ?></td>

                                             <td class="text-end"><?= number_format($order['price'] * $order['quantity'], 0, ',', '.') ?> VND</td>
                                         </tr>
                                     <?php endforeach; ?>
                                 </tbody>
                             </table>
                         </div>
                     </div>
                 </div>
             </div>

         </div>
     </div>
 </div>
 <?php include 'footer_admin.php'; ?>