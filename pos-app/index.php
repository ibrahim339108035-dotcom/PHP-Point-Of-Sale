<?php
session_start();
$assetPrefix = defined('POS_ASSET_PREFIX') ? POS_ASSET_PREFIX : '';
$configFile = __DIR__ . '/storage/config.json';
$installedSettings = is_file($configFile) ? json_decode(file_get_contents($configFile), true) : [];
$installedSettings = is_array($installedSettings) ? $installedSettings : [];
$defaultProducts = [];
$productsFile = __DIR__ . '/storage/products.json';
$products = is_file($productsFile) ? json_decode(file_get_contents($productsFile), true) : $defaultProducts;
$products = is_array($products) && $products ? $products : $defaultProducts;
$categories = ['Sab', 'Pharmacy', 'Grocery', 'Restaurant', 'Retail'];
$currency = $installedSettings['currency'] ?? 'Rs';
$currencySymbol = $currency === 'PKR' ? 'Rs' : $currency;
$companyName = $installedSettings['company'] ?? 'PaK Sale App';
$taxRate = (float) ($installedSettings['tax'] ?? 5);
$companyLogo = !empty($installedSettings['logo']) ? $assetPrefix . $installedSettings['logo'] : '';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= $assetPrefix ?>style.css">
    <link rel="stylesheet" href="<?= $assetPrefix ?>operations.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand"><span class="brand-mark">R</span><span><?= htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8') ?><small>business POS</small></span></div>
        <nav>
            <a class="nav-item active" href="#sale"><span>▦</span> Sale counter</a>
            <a class="nav-item" href="#inventory"><span>□</span> Inventory</a>
            <a class="nav-item" href="#reports"><span>↗</span> Reports</a>
            <a class="nav-item" id="settingsLink" href="#settings"><span>⚙</span> Settings</a>
        </nav>
        <div class="sidebar-note"><span class="pulse"></span><div><strong>Register khula hai</strong><small>Counter 01 · Aaj 19 Aug</small></div></div>
        <div class="user"><div class="avatar">AK</div><div><strong>Ali Khan</strong><small>Administrator</small></div><span class="more">···</span></div>
    </aside>
    <main class="main-content">
        <header class="topbar"><div><p class="eyebrow">WEDNESDAY, 19 AUGUST 2026</p><h1>Assalam-o-alaikum, Ali</h1></div><div class="header-actions"><button class="icon-btn" title="Notifications">♢<i></i></button><button class="outline-btn">⌕ <span>Global search</span></button><button class="user-mobile">AK</button></div></header>
        <section class="quick-stats">
            <div class="stat"><div class="stat-icon coral">₨</div><div><small>Aaj ki sales</small><strong>Rs 84,250</strong><em class="up">↑ 12.8%</em></div></div>
            <div class="stat"><div class="stat-icon mint">↗</div><div><small>Orders</small><strong>126</strong><em class="up">↑ 8.4%</em></div></div>
            <div class="stat"><div class="stat-icon yellow">□</div><div><small>Low stock items</small><strong>08</strong><em class="warning">Check now</em></div></div>
            <div class="stat"><div class="stat-icon blue">◷</div><div><small>Open tables</small><strong>06 <span class="muted">/ 24</span></strong><em>Restaurant mode</em></div></div>
        </section>
        <section class="workspace" id="sale">
            <div class="products-panel">
                <div class="section-heading"><div><p class="eyebrow">FAST CHECKOUT</p><h2>Sale counter</h2></div><button class="filter-btn" id="addProduct">＋ Add product</button></div>
                <div class="search-box"><span>⌕</span><input id="search" placeholder="Product naam ya barcode search karein..." autocomplete="off"><button type="button" class="scan-button" id="scanBarcode">▣ Scan</button><kbd>⌘ K</kbd></div>
                <div class="category-tabs" id="categories"><?php foreach ($categories as $category): ?><button class="category <?= $category === 'Sab' ? 'selected' : '' ?>" data-category="<?= $category ?>"><?= $category ?></button><?php endforeach; ?></div>
                <div class="product-grid" id="productGrid">
                    <?php foreach ($products as $product): ?><button class="product-card" data-name="<?= strtolower($product['name']) ?>" data-category="<?= $product['category'] ?>" data-id="<?= $product['id'] ?>" data-price="<?= $product['price'] ?>"><span class="product-image <?= strtolower($product['category']) ?>"><?= $product['category'] === 'Pharmacy' ? '✚' : ($product['category'] === 'Restaurant' ? '♨' : ($product['category'] === 'Retail' ? '◉' : '◆')) ?></span><span class="product-info"><strong><?= $product['name'] ?></strong><small><?= $product['stock'] ?> <?= $product['unit'] ?> available</small><b><?= htmlspecialchars($currencySymbol, ENT_QUOTES, 'UTF-8') ?> <?= number_format($product['price']) ?></b></span><span class="add">+</span></button><?php endforeach; ?>
                </div>
            </div>
            <div class="cart-panel"><div class="cart-header"><div><p class="eyebrow">CURRENT ORDER</p><h2>Order #10248</h2></div><button class="clear-btn" id="clearCart">Clear all</button></div><div class="customer-select">○ <span>Walk-in customer</span><button>⌄</button></div><div class="cart-items" id="cartItems"><div class="empty-cart">Product select karein<br><small>Order yahan nazar aayega</small></div></div><div class="cart-footer"><div class="totals"><span>Subtotal <b id="subtotal">Rs 0</b></span><span id="taxLabel">Tax (5%) <b id="tax">Rs 0</b></span><span class="grand-total">Total <b id="total">Rs 0</b></span></div><div class="payment-row" id="paymentMethods"></div><div class="checkout-actions"><button class="checkout" id="checkout">Paid & receipt <span>→</span></button><button class="unpaid-button" id="saveUnpaid">Save unpaid</button></div></div></div>
        </section>
        <section class="operations" id="inventory"><div class="operation-card"><div><p class="eyebrow">CASH MANAGEMENT</p><h3>Cash drawer</h3><strong id="drawerBalance">Rs 0</strong><small>Current drawer balance</small></div><div class="operation-actions"><button id="openDrawer">＋ Open drawer</button><button id="closeDrawer">▣ Close & count</button><button id="cashIn">＋ Cash in</button><button id="cashOut">− Cash out</button></div></div><div class="operation-card"><div><p class="eyebrow">RECEIPT SETTINGS</p><h3 id="companyPreview">PaK Sale App</h3><small id="receiptPreview">Tax 5% · Receipt ready</small></div><button class="settings-button" id="receiptSettings">⚙ Configure receipt</button></div></section>
    <footer class="app-credit">PaK Sale App · Developed by <strong>Jibran Khan Malap</strong></footer>
        <section class="history-panel" id="reports"><div class="section-heading"><div><p class="eyebrow">TRANSACTIONS</p><h2>Sale history</h2></div><div class="history-actions"><select id="historyFilter"><option value="all">All list</option><option value="paid">Paid only</option><option value="unpaid">Unpaid only</option></select><button class="filter-btn" id="printHistory">▣ Print list</button><button class="filter-btn" id="clearHistory">Clear history</button></div></div><div id="saleHistory"><div class="empty-history">Abhi koi sale record nahi hai.</div></div></section>
    </main>
</div>
<div class="modal-backdrop" id="settingsModal"><form class="modal" id="settingsForm"><button type="button" class="modal-close" data-close="settingsModal">×</button><p class="eyebrow">BUSINESS SETTINGS</p><h2>Receipt aur company</h2><label>Company name<input id="companyName" required placeholder="Aapki company ka naam"></label><label>Tax rate (%)<input id="taxRate" type="number" min="0" max="100" step="0.01"></label><label>Custom payment method<input id="customPayment" placeholder="e.g. JazzCash"><button type="button" class="mini-button" id="addPaymentMethod">＋ Add method</button></label><div class="custom-methods" id="customMethods"></div><label>Company logo<input id="companyLogo" type="file" accept="image/*"></label><div class="logo-preview" id="logoPreview">Logo preview</div><button class="save-button" type="submit">Save settings</button></form></div>
<div class="modal-backdrop" id="drawerModal"><form class="modal small-modal" id="drawerForm"><button type="button" class="modal-close" data-close="drawerModal">×</button><p class="eyebrow" id="drawerTitle">CASH DRAWER</p><h2 id="drawerHeading">Open cash drawer</h2><label>Amount (Rs)<input id="drawerAmount" type="number" min="0" step="1" required placeholder="0"></label><button class="save-button" type="submit">Save amount</button></form></div>
<div class="modal-backdrop" id="cashFlowModal"><form class="modal small-modal" id="cashFlowForm"><button type="button" class="modal-close" data-close="cashFlowModal">×</button><p class="eyebrow">CASH MOVEMENT</p><h2 id="cashFlowHeading">Cash in</h2><label>Amount (Rs)<input id="cashFlowAmount" type="number" min="0" step="1" required placeholder="0"></label><label>Reason<input id="cashFlowReason" maxlength="80" placeholder="e.g. petty cash"></label><button class="save-button" type="submit">Save movement</button></form></div>
<div class="modal-backdrop" id="scannerModal"><div class="modal scanner-modal"><button type="button" class="modal-close" data-close="scannerModal">×</button><p class="eyebrow">BARCODE SCANNER</p><h2>Item scan karein</h2><video id="scannerVideo" autoplay muted playsinline></video><label>USB scanner code<input id="scannerInput" autocomplete="off" placeholder="Scanner se barcode scan karein"></label><p class="scanner-note" id="scannerNote">Camera start ho raha hai...</p><button type="button" class="save-button" id="closeScanner">Close scanner</button></div></div>
<div class="modal-backdrop" id="productModal"><form class="modal" id="productForm"><button type="button" class="modal-close" data-close="productModal">×</button><p class="eyebrow">PRODUCT CATALOG</p><h2 id="productModalTitle">Add product</h2><input type="hidden" id="productId"><label>Product icon<select id="productIcon"><option value="💊">💊 Medicine</option><option value="🛒">🛒 Grocery</option><option value="🍔">🍔 Burger</option><option value="🍛">🍛 Food</option><option value="👕">👕 Clothing</option><option value="📦">📦 General item</option><option value="🥤">🥤 Drink</option><option value="✏️">✏️ Custom/other</option></select></label><label>Product name<input id="productName" required placeholder="Product name"></label><label>Category<select id="productCategory"><option>Pharmacy</option><option>Grocery</option><option>Restaurant</option><option>Retail</option></select></label><label>Barcode<input id="productBarcode" placeholder="Auto generate hoga"></label><label>Price<input id="productPrice" type="number" min="0" step="0.01" required placeholder="0"></label><label>Stock<input id="productStock" type="number" min="0" step="1" required placeholder="0"></label><label>Unit<input id="productUnit" required value="item"></label><button class="save-button" type="submit">Save product</button></form></div>
<div class="toast" id="toast"></div>
<script>window.POS_PRODUCTS = <?= json_encode($products, JSON_UNESCAPED_UNICODE) ?>; window.POS_API_URL = <?= json_encode($assetPrefix . 'api/products.php') ?>; window.POS_INSTALL_SETTINGS = <?= json_encode(['company' => $companyName, 'tax' => $taxRate, 'currency' => $currency === 'Rs' ? 'PKR' : $currency, 'printer' => $installedSettings['printer'] ?? 'browser', 'logo' => $companyLogo]) ?>;</script><script src="<?= $assetPrefix ?>app.js"></script>
</body>
</html>
