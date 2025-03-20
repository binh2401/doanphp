<?php
require_once "config/database.php";
require_once "public/session.php"; // Quản lý phiên
checkLogin();
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

if ($query) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE :query OR description LIKE :query");
    $stmt->execute(['query' => '%' . $query . '%']);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $products = [];
}
?>

<?php include 'header.php'; ?>

<body>
    <div class="container-lg">
        <h1>Search Results for "<?= htmlspecialchars($query) ?>"</h1>
        <?php if ($products): ?>
            <div class="product-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5">
                <?php foreach ($products as $product): ?>
                    <div class="col">
                        <div class="product-item">
                            <figure>
                                <a href="product_detail.php?id=<?= $product["id"] ?>" title="Product Title">
                                    <img src="uploads/product/<?= $product["image"] ?>" alt="Product Thumbnail" class="tab-image">
                                </a>
                            </figure>
                            <div class="d-flex flex-column text-center">
                                <h3 class="fs-6 fw-normal"><?= $product["name"] ?></h3>
                                <div>
                                    <span class="rating">
                                        <svg width="18" height="18" class="text-warning">
                                            <use xlink:href="#star-full"></use>
                                        </svg>
                                        <svg width="18" height="18" class="text-warning">
                                            <use xlink:href="#star-full"></use>
                                        </svg>
                                        <svg width="18" height="18" class="text-warning">
                                            <use xlink:href="#star-full"></use>
                                        </svg>
                                        <svg width="18" height="18" class="text-warning">
                                            <use xlink:href="#star-full"></use>
                                        </svg>
                                        <svg width="18" height="18" class="text-warning">
                                            <use xlink:href="#star-half"></use>
                                        </svg>
                                    </span>
                                    <span>(<?= isset($product["sold_quantity"]) ? $product["sold_quantity"] : 0 ?> sold)</span>
                                </div>
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <span class="text-dark fw-semibold">$<?= $product["price"] ?></span>
                                </div>
                                <div class="button-area p-3 pt-0">
                                    <div class="row g-1 mt-2">
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            <span class="text-dark fw-semibold"><?= number_format($product["price"], 0, ',', '.') ?> VNĐ</span>
                                        </div>
                                        <div class="col-7">
                                            <a href="views/cart.php?action=add&id=<?= $product["id"] ?>" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart">
                                                <svg width="18" height="18">
                                                    <use xlink:href="#cart"></use>
                                                </svg> Add to Cart
                                            </a>
                                        </div>
                                        <div class="col-2">
                                            <a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6">
                                                <svg width="18" height="18">
                                                    <use xlink:href="#heart"></use>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No products found for "<?= htmlspecialchars($query) ?>"</p>
        <?php endif; ?>
    </div>
</body>

<?php include 'flooter.php'; ?>