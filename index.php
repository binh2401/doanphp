<?php
require_once "models/Product.php";
require_once "config/database.php"; // Kết nối CSDL
require_once "public/session.php"; // Quản lý phiên

checkLogin(); // Kiểm tra xem người dùng đã đăng nhập hay chưa


$productModel = new Product($conn);
$products = $productModel->getProducts();
$totalCartAmount = getTotalCartAmount(); // Tính tổng tiền trong giỏ hàng
$stmt = $conn->prepare("SELECT id, name, image FROM categories"); // Thêm 'id' vào truy vấn
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Lấy danh sách sản phẩm yêu thích của người dùng
$userId = $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
$stmt->execute([$userId]);
$wishlist = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<?php include 'header.php'; ?>
<section style="background-image: url('uploads/1.png'); background-repeat: no-repeat; background-size: contain; height: 60vh; display: flex; justify-content: center; align-items: center;     background-position-x: center;">
</section>

<body>


    <section class="py-5 overflow-hidden">
        <div class="container-lg">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-header d-flex flex-wrap justify-content-between mb-5">
                        <h2 class="section-title">Category</h2>
                        <div class="d-flex align-items-center">
                            <a href="#" class="btn btn-primary me-2">View All</a>
                            <div class="swiper-buttons">
                                <button class="swiper-prev category-carousel-prev btn btn-yellow">❮</button>
                                <button class="swiper-next category-carousel-next btn btn-yellow">❯</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="category-carousel swiper">
                        <div class="swiper-wrapper">
                            <?php foreach ($categories as $category): ?>
                                <a href="category.php?id=<?= $category['id'] ?>" class="nav-link swiper-slide text-center">
                                    <img src="uploads/<?= $category['image'] ?>" class="rounded-circle" alt="Category Thumbnail">
                                    <h4 class="fs-6 mt-3 fw-normal category-title"><?= $category['name'] ?></h4>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="pb-5">
        <div class="container-lg">

            <div class="row">
                <div class="col-md-12">

                    <div class="section-header d-flex flex-wrap justify-content-between my-4">

                        <h2 class="section-title">Best selling products</h2>

                        <div class="d-flex align-items-center">
                            <a href="all_products.php" class="btn btn-primary rounded-1">View All</a>
                        </div>
                    </div>

                </div>
            </div>

            <div class="row">
                <div class="col-md-12">

                    <div class="product-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5">
                        <?php foreach ($products as $product): ?>
                            <?php
                            $ratingData = $productModel->getProductRating($product["id"]);
                            $averageRating = round($ratingData["average_rating"], 1);
                            $totalReviews = $ratingData["total_reviews"];
                            $isInWishlist = in_array($product["id"], $wishlist);
                            ?>
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
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <svg width="18" height="18" class="text-warning">
                                                        <use xlink:href="#<?= $i <= $averageRating ? 'star-full' : 'star-empty' ?>"></use>
                                                    </svg>
                                                <?php endfor; ?>
                                            </span>
                                            <span>(<?= $totalReviews ?> reviews)</span>
                                        </div>
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            <span class="text-dark fw-semibold">$<?= $product["price"] ?></span>
                                        </div>
                                        <div class="button-area p-3 pt-0">
                                            <div class="row g-1 mt-2">
                                                <div class="col-3">
                                                    <input type="number" name="quantity" class="form-control border-dark-subtle input-number quantity" value="1">
                                                </div>
                                                <div class="col-7">
                                                    <a href="views/cart.php?action=add&id=<?= $product["id"] ?>" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart">
                                                        <svg width="18" height="18">
                                                            <use xlink:href="#cart"></use>
                                                        </svg> Add to Cart
                                                    </a>
                                                </div>
                                                <div class="col-2">
                                                    <button class="btn <?= $isInWishlist ? 'btn-danger' : 'btn-outline-dark' ?> rounded-1 p-2 fs-6 btn-heart" data-product-id="<?= $product["id"] ?>">
                                                        <svg width="18" height="18">
                                                            <use xlink:href="#heart"></use>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- / product-grid -->
                </div>
            </div>
        </div>
    </section>

    <section class="py-3">
        <div class="container-lg">
            <div class="row">
                <div class="col-md-12">

                    <div class="banner-blocks">

                        <div class="banner-ad d-flex align-items-center large bg-info block-1"
                            style="background: url('uploads/2.png') no-repeat; background-size: cover;">
                            <div class="banner-content p-5">
                                <div class="content-wrapper text-light">
                                    <h3 class="banner-title text-light"></h3>
                                    <p></p>
                                    <a href="#" class="btn-link text-white"></a>
                                </div>
                            </div>
                        </div>

                        <div class="banner-ad bg-success-subtle block-2"
                            style="background:url('uploads/4.png') no-repeat;background-size: cover">
                            <div class="banner-content align-items-center p-5">
                                <div class="content-wrapper text-light">
                                    <h3 class="banner-title text-light"></h3>
                                    <p></p>
                                    <a href="#" class="btn-link text-white"></a>
                                </div>
                            </div>
                        </div>

                        <div class="banner-ad bg-danger block-3"
                            style="background:url('uploads/3.png') no-repeat;background-size: cover">
                            <div class="banner-content align-items-center p-5">
                                <div class="content-wrapper text-light">
                                    <h3 class="banner-title text-light">Discount Coupons</h3>
                                    <p>Discounts up to 40%</p>
                                    <a href="#" class="btn-link text-white">Shop Now</a>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- / Banner Blocks -->

                </div>
            </div>
        </div>
    </section>

    <section id="featured-products" class="products-carousel">
        <div class="container-lg overflow-hidden py-5">
            <div class="row">
                <div class="col-md-12">

                    <div class="section-header d-flex flex-wrap justify-content-between my-4">

                        <h2 class="section-title">Featured products</h2>

                        <div class="d-flex align-items-center">
                            <a href="#" class="btn btn-primary me-2">View All</a>
                            <div class="swiper-buttons">
                                <button class="swiper-prev products-carousel-prev btn btn-primary">❮</button>
                                <button class="swiper-next products-carousel-next btn btn-primary">❯</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="row">
                <div class="col-md-12">

                    <div class="swiper">
                        <div class="swiper-wrapper">

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-10.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Greek Style Plain Yogurt</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-11.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Pure Squeezed No Pulp Orange Juice</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-12.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Fresh Oranges</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-13.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Gourmet Dark Chocolate Bars</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-14.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Fresh Green Celery</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-15.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Sandwich Bread</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-16.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Honeycrisp Apples</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-17.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Whole Wheat Sandwich Bread</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-18.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Honeycrisp Apples</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                    <!-- / products-carousel -->

                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="container-lg">

            <div class="bg-secondary text-light py-5 my-5"
                style="background: url('images/banner-newsletter.jpg') no-repeat; background-size: cover;">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-5 p-3">
                            <div class="section-header">
                                <h2 class="section-title display-5 text-light">Get 25% Discount on your first purchase</h2>
                            </div>
                            <p>Just Sign Up & Register it now to become member.</p>
                        </div>
                        <div class="col-md-5 p-3">
                            <form>
                                <div class="mb-3">
                                    <label for="name" class="form-label d-none">Name</label>
                                    <input type="text" class="form-control form-control-md rounded-0" name="name" id="name"
                                        placeholder="Name">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label d-none">Email</label>
                                    <input type="email" class="form-control form-control-md rounded-0" name="email" id="email"
                                        placeholder="Email Address">
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-dark btn-md rounded-0">Submit</button>
                                </div>
                            </form>

                        </div>

                    </div>

                </div>
            </div>

        </div>
    </section>

    <section id="popular-products" class="products-carousel">
        <div class="container-lg overflow-hidden py-5">
            <div class="row">
                <div class="col-md-12">

                    <div class="section-header d-flex justify-content-between my-4">

                        <h2 class="section-title">Most popular products</h2>

                        <div class="d-flex align-items-center">
                            <a href="#" class="btn btn-primary me-2">View All</a>
                            <div class="swiper-buttons">
                                <button class="swiper-prev products-carousel-prev btn btn-primary">❮</button>
                                <button class="swiper-next products-carousel-next btn btn-primary">❯</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="row">
                <div class="col-md-12">

                    <div class="swiper">
                        <div class="swiper-wrapper">

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-15.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Sandwich Bread</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-16.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Honeycrisp Apples</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-17.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Whole Wheat Sandwich Bread</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-18.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Honeycrisp Apples</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-19.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Sunstar Fresh Melon Juice</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-10.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Greek Style Plain Yogurt</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-11.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Pure Squeezed No Pulp Orange Juice</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-12.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Fresh Oranges</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-13.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Gourmet Dark Chocolate Bars</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                    <!-- / products-carousel -->

                </div>
            </div>
        </div>
    </section>

    <section id="latest-products" class="products-carousel">
        <div class="container-lg overflow-hidden pb-5">
            <div class="row">
                <div class="col-md-12">

                    <div class="section-header d-flex justify-content-between my-4">

                        <h2 class="section-title">Just arrived</h2>

                        <div class="d-flex align-items-center">
                            <a href="#" class="btn btn-primary me-2">View All</a>
                            <div class="swiper-buttons">
                                <button class="swiper-prev products-carousel-prev btn btn-primary">❮</button>
                                <button class="swiper-next products-carousel-next btn btn-primary">❯</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="row">
                <div class="col-md-12">

                    <div class="swiper">
                        <div class="swiper-wrapper">

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-20.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Sunstar Fresh Melon Juice</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-1.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Whole Wheat Sandwich Bread</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-21.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Sunstar Fresh Melon Juice</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-22.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Gourmet Dark Chocolate</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-23.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Sunstar Fresh Melon Juice</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-10.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Greek Style Plain Yogurt</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-11.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Pure Squeezed No Pulp Orange Juice</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-12.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Fresh Oranges</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-item swiper-slide">
                                <figure>
                                    <a href="index.html" title="Product Title">
                                        <img src="images/product-thumb-13.png" alt="Product Thumbnail" class="tab-image">
                                    </a>
                                </figure>
                                <div class="d-flex flex-column text-center">
                                    <h3 class="fs-6 fw-normal">Gourmet Dark Chocolate Bars</h3>
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
                                        <span>(222)</span>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <del>$24.00</del>
                                        <span class="text-dark fw-semibold">$18.00</span>
                                        <span
                                            class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">10%
                                            OFF</span>
                                    </div>
                                    <div class="button-area p-3 pt-0">
                                        <div class="row g-1 mt-2">
                                            <div class="col-3"><input type="number" name="quantity"
                                                    class="form-control border-dark-subtle input-number quantity" value="1"></div>
                                            <div class="col-7"><a href="#" class="btn btn-primary rounded-1 p-2 fs-7 btn-cart"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#cart"></use>
                                                    </svg> Add to Cart</a></div>
                                            <div class="col-2"><a href="#" class="btn btn-outline-dark rounded-1 p-2 fs-6"><svg width="18"
                                                        height="18">
                                                        <use xlink:href="#heart"></use>
                                                    </svg></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- / products-carousel -->

                </div>
            </div>
        </div>
    </section>
    <?php include 'flooter.php'; ?>



</body>

</html>