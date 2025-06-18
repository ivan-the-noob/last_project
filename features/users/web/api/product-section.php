<?php
session_start();
if (isset($_SESSION['email']) && isset($_SESSION['profile_picture'])) {
    $email = $_SESSION['email'];
    $profile_picture = $_SESSION['profile_picture'];
} else {
    header("Location: login.php");
    exit();
}

require '../../../../db.php';

$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchCondition = $searchQuery ? "WHERE product_name LIKE ?" : "";

$sql = "SELECT * FROM product $searchCondition";
$stmt = $conn->prepare($sql);
if ($searchQuery) {
    $stmt->bind_param("s", $searchLike);
    $searchLike = "%$searchQuery%";
}
$stmt->execute();
$result = $stmt->get_result();
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PRODUCT | DIGITAL PAWS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.js"></script>
  <link rel="stylesheet" href="../../css/products.css">
    <link rel="icon" href="../../../../assets/img/logo.png" type="image/x-icon">

</head>

<body>
<div class="navbar-container">
<nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
            <a class="navbar-brand d-none d-lg-block" href="../../../../index.php">
                    <img src="../../../../assets/img/logo.png" alt="Logo" width="30" height="30">
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        style="stroke: black; fill: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>

                <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="../../../../index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Products</a>
                        </li>
                       
                    </ul>
                    <div class="d-flex ml-auto">
                        <?php if ($email): ?>
                            <!-- Profile Dropdown -->
                            <div class="dropdown second-dropdown">
                                <button class="btn" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="../../../../assets/img/<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile Image" class="profile">
                                </button>
                                <ul class="dropdown-menu custom-center-dropdown" aria-labelledby="dropdownMenuButton2">
                                    <li><a class="dropdown-item" href="features/users/web/api/dashboard.php">Profile</a></li>
                                    <li><a class="dropdown-item" href="features/users/function/authentication/logout.php">Logout</a></li>
                                </ul>
                            </div>
                          <?php
                            include '../../function/php/count_cart.php';
                          ?>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <a href="../../function/php/update_cart_status.php" class="header-cart">
                            <span class="material-symbols-outlined">
                                shopping_cart
                            </span>

                            <?php if ($newCartData > 0): ?>
                                <span class="badge"><?= $newCartData ?></span>
                            <?php endif; ?>
                        </a>
                                <a href="my-orders.php" class="header-cart">
                                    <span class="material-symbols-outlined">
                                        local_shipping
                                    </span>
                                </a>
                                 <div class="dropdown">
                                    <a href="#" class="header-cart " data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="material-symbols-outlined">
                                        notifications
                                        </span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end"  style="width: 300px; height: 400px; overflow-y: auto;">
                                    <?php
                                        include '../../../../db.php';
                                       

                                        $email = $_SESSION['email'] ?? '';

                                        if ($email) {
                                            $query = "SELECT message, created_at FROM notification WHERE email = ? ORDER BY id DESC";
                                            $stmt = $conn->prepare($query);
                                            $stmt->bind_param("s", $email);
                                            $stmt->execute();
                                            $result = $stmt->get_result();

                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $message = $row['message'];
                                                    $created_at = $row['created_at'];

                                                    // Format the created_at date as "April 4, 5:00 PM"
                                                    $formatted_date = date('F j, g:i a', strtotime($created_at));

                                                    // Apply styles for the message
                                                    $classes = 'dropdown-item bg-white shadow-sm px-3 py-2 rounded';
                                                    $style = 'box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);';

                                                    if (trim($message) == "Your appointment has been approved!") {
                                                        $classes .= ' text-success';
                                                    } else if (trim($message) == "Your checkout has been approved") {
                                                        $classes .= ' text-success';
                                                    } else if (trim($message) == "Your item has been picked up by courier. Please ready payment for COD.") {
                                                        $classes .= ' text-info';
                                                    } else if (trim($message) == "Your profile info has been updated.") {
                                                        $classes .= ' text-info';
                                                    } else if (trim($message) == "New services offered! Check it now!") {
                                                        $classes .= ' text-success';
                                                    } else if (trim($message) == "New product has been arrived! Check it now!") {
                                                        $classes .= ' text-success';
                                                    }

                                                    // Display the message with the date below
                                                    echo "<li><a class=\"$classes d-flex flex-column mx-auto\" href=\"#\" style=\"$style\">";
                                                    echo "<span>$message</span>";
                                                    echo "<div style=\"font-size: 0.9em; color: black; margin-top: 5px;\">$formatted_date</div></a></li>";
                                                    echo "<li><hr class=\"dropdown-divider\"></li>";
                                                }
                                            } else {
                                                echo "<li><a class=\"dropdown-item bg-white shadow-sm\" href=\"#\">No notifications</a></li>";
                                            }

                                            $stmt->close();
                                        } else {
                                            echo "<li><a class=\"dropdown-item bg-white shadow-sm\" href=\"#\">Please log in to see notifications</a></li>";
                                        }

                                 
                                        ?>
                                    </ul>

                                </div>
                            </div>
                            </div>


                        <?php else: ?>
                            <a href="features/users/web/api/login.php" class="btn-theme" type="button">Login</a>
                        <?php endif; ?>
                    </div>

        </nav>
    </div>

    <section class="essentials py-5">
    <div class="d-flex">
        <div class="how-headings col-8 text-center mt-4">
            <p class="mb-0">Explore pet care</p>
            <h2 class="mb-4">Essentials</h2>
        </div>
        <div class="col-md-4 d-flex align-items-center">
            <form method="GET" class="w-100">
                <input type="search" name="search" id="search-product" class="search-product" placeholder="Search products..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <button type="submit" class="product-button">Search</button>
            </form>
        </div>
    </div>
    <div class="container">
        <div class="row align-items-start justify-content-center">
            <div class="col-lg-3 col-md-4 col-12 mb-3">
                <div class="essentials-button d-flex flex-column align-items-start">
                    <button onclick="filterProducts('petfood')">Pet Food</button>
                    <button onclick="filterProducts('pettoys')">Pet Toys</button>
                    <button onclick="filterProducts('supplements')">Supplements</button>
                    <button onclick="filterProducts('all')">Show All</button>
                </div>
            </div>

            <div class="col-lg-9 col-md-8 col-12">
                <div class="row" id="product-list">
                <?php
                    if ($result->num_rows > 0):
                        while ($product = $result->fetch_assoc()): ?>
                           <div class="col-lg-4 col-md-6 col-12 mb-4 product-item" data-type="<?= strtolower($product['type']) ?>">
                                <div class="product">
                                    <div class="product-itemss" style="height: 36vh;">
                                        <img src="../../../../assets/img/product/<?= $product['product_img'] ?>" alt="Product Image">
                                        <h5 class="mt-4 mb-0 product_name"><?= htmlspecialchars($product['product_name']) ?></h5>
                                        <p class="mt-0 mb-0 product_name"><?= htmlspecialchars($product['quantity']) ?>x</p>
                                    </div>
                                    <div class="d-flex prices">
                                        <p class="tag align-items-center mb-0 d-flex">PHP</p>
                                        <p class="price mb-0"><?= htmlspecialchars(number_format($product['cost'], 2)) ?></p>
                                    </div>
                                    <?php if ($product['quantity'] > 0): ?>
                                    <div class="d-flex justify-content-between item-btn">
                                        <a href="../../../../features/users/web/api/buy-now.php?id=<?= $product['id'] ?>&type=<?= htmlspecialchars($product['type']) ?>" class="btn buy-now">BUY NOW!</a>
                                        <a href="../../../../features/users/web/api/buy-now.php?id=<?= $product['id'] ?>&type=<?= htmlspecialchars($product['type']) ?>&triggerModal=true" class="btn add-to-cart">
                                            <span class="material-symbols-outlined">shopping_cart</span>
                                        </a>
                                    </div>
                                    <?php else: ?>
                                    <button class="buy-now">Out Of Stock!</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile;
                    else: ?>
                        <p>No products found matching your search criteria.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
   

      <!--Chat Bot-->
      <button id="chat-bot-button" onclick="toggleChat()">
        <i class="fa-solid fa-headset"></i>
    </button>

    <div id="chat-interface" class="hidden">
    <div id="chat-header">
        <p>Amazing Day! How may I help you?</p>
        <button onclick="toggleChat()">X</button>
    </div>
    <div id="chat-body">
    <div class="button-bot">
            <button onclick="sendResponse('How to log in?')">How to log in?</button>
            <button onclick="sendResponse('How to book?')">How to book?</button>
            <button onclick="sendResponse('What are the services?')">What are the services?</button>
            <button onclick="sendResponse('Contact information?')">Contact information?</button>
        </div>
        
        <div class="admin mt-3">
            <div class="admin-chat">
                <img src="../../../../assets/img/logo.png" alt="Admin">
                <p>Admin</p>
            </div>
            <p class="text" id="typing-text">Hello, I am Chat Bot. Please Ask me a question just by pressing the question buttons.</p>
        </div>
      
    </div>
    <div class="line"></div>
</div>
    <!--Chat Bot End-->



</body>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Initialize product variables
    let allProducts = [];
    let currentCategory = 'all'; // Default to showing all
    
    // Load all products when page loads
    function loadAllProducts() {
        allProducts = Array.from(document.querySelectorAll('.product-item'));
        console.log("Total products found:", allProducts.length); // Debug log
        
        if (allProducts.length > 0) {
            // Initially show all products
            showAllProducts();
        } else {
            console.log("No products found in the DOM");
        }
    }
    
    // Show all products
    function showAllProducts() {
        allProducts.forEach(product => {
            product.style.display = 'block';
        });
    }
    
    // Filter products by type
    function filterProducts(type) {
        currentCategory = type;
        console.log(`Filtering by: ${type}`); // Debug log
        
        if (type === 'all') {
            showAllProducts();
            return;
        }
        
        allProducts.forEach(product => {
            const productType = product.dataset.type.toLowerCase();
            if (productType === type.toLowerCase()) {
                product.style.display = 'block';
            } else {
                product.style.display = 'none';
            }
        });
    }
    
    // Initialize
    loadAllProducts();
    
    // Make filterProducts available globally for button clicks
    window.filterProducts = filterProducts;
});
</script>
<script src="../../function/script/chat-bot_product.js"></script>
<script src="../../function/script/chatbot_questionslide.js"></script>
<script src="../../function/script/chatbot-toggle.js"></script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

</html>