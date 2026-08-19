<?php
session_start();
$assetPrefix = defined('POS_ASSET_PREFIX') ? POS_ASSET_PREFIX : '';
$configFile = __DIR__ . '/storage/config.json';
$installedSettings = is_file($configFile) ? json_decode(file_get_contents($configFile), true) : [];
$installedSettings = is_array($installedSettings) ? $installedSettings : [];
$products = [
    ['id' => 1, 'name' => 'Paracetamol 500mg', 'category' => 'Pharmacy', 'price' => 120, 'stock' => 48, 'unit' => 'box'],
    ['id' => 2, 'name' => 'Fresh Milk 1L', 'category' => 'Grocery', 'price' => 185, 'stock' => 24, 'unit' => 'pack'],
    ['id' => 3, 'name' => 'Classic Burger', 'category' => 'Restaurant', 'price' => 650, 'stock' => 18, 'unit' => 'item'],
    ['id' => 4, 'name' => 'Black T-Shirt', 'category' => 'Retail', 'price' => 1450, 'stock' => 12, 'unit' => 'item'],
    ['id' => 5, 'name' => 'Vitamin C 1000mg', 'category' => 'Pharmacy', 'price' => 890, 'stock' => 31, 'unit' => 'bottle'],
    ['id' => 6, 'name' => 'Mineral Water 1.5L', 'category' => 'Grocery', 'price' => 90, 'stock' => 66, 'unit' => 'bottle'],
    ['id' => 7, 'name' => 'Chicken Biryani', 'category' => 'Restaurant', 'price' => 480, 'stock' => 25, 'unit' => 'plate'],
    ['id' => 8, 'name' => 'Denim Jeans', 'category' => 'Retail', 'price' => 3200, 'stock' => 9, 'unit' => 'item'],
];
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
                <div class="section-heading"><div><p class="eyebrow">FAST CHECKOUT</p><h2>Sale counter</h2></div><button class="filter-btn">☷ Filter</button></div>
                <div class="search-box"><span>⌕</span><input id="search" placeholder="Product naam ya barcode search karein..." autocomplete="off"><kbd>⌘ K</kbd></div>
                <div class="category-tabs" id="categories"><?php foreach ($categories as $category): ?><button class="category <?= $category === 'Sab' ? 'selected' : '' ?>" data-category="<?= $category ?>"><?= $category ?></button><?php endforeach; ?></div>
                <div class="product-grid" id="productGrid">
                    <?php foreach ($products as $product): ?><button class="product-card" data-name="<?= strtolower($product['name']) ?>" data-category="<?= $product['category'] ?>" data-id="<?= $product['id'] ?>" data-price="<?= $product['price'] ?>"><span class="product-image <?= strtolower($product['category']) ?>"><?= $product['category'] === 'Pharmacy' ? '✚' : ($product['category'] === 'Restaurant' ? '♨' : ($product['category'] === 'Retail' ? '◉' : '◆')) ?></span><span class="product-info"><strong><?= $product['name'] ?></strong><small><?= $product['stock'] ?> <?= $product['unit'] ?> available</small><b><?= htmlspecialchars($currencySymbol, ENT_QUOTES, 'UTF-8') ?> <?= number_format($product['price']) ?></b></span><span class="add">+</span></button><?php endforeach; ?>
                </div>
            </div>
            <div class="cart-panel"><div class="cart-header"><div><p class="eyebrow">CURRENT ORDER</p><h2>Order #10248</h2></div><button class="clear-btn" id="clearCart">Clear all</button></div><div class="customer-select">○ <span>Walk-in customer</span><button>⌄</button></div><div class="cart-items" id="cartItems"><div class="empty-cart">Product select karein<br><small>Order yahan nazar aayega</small></div></div><div class="cart-footer"><div class="totals"><span>Subtotal <b id="subtotal">Rs 0</b></span><span id="taxLabel">Tax (5%) <b id="tax">Rs 0</b></span><span class="grand-total">Total <b id="total">Rs 0</b></span></div><div class="payment-row"><button class="payment active">Cash</button><button class="payment">Card</button><button class="payment">QR</button></div><button class="checkout" id="checkout">Complete payment <span>→</span></button></div></div>
        </section>
        <section class="operations" id="inventory"><div class="operation-card"><div><p class="eyebrow">CASH MANAGEMENT</p><h3>Cash drawer</h3><strong id="drawerBalance">Rs 0</strong><small>Current drawer balance</small></div><div class="operation-actions"><button id="openDrawer">＋ Open drawer</button><button id="closeDrawer">▣ Close & count</button><button id="cashIn">＋ Cash in</button><button id="cashOut">− Cash out</button></div></div><div class="operation-card"><div><p class="eyebrow">RECEIPT SETTINGS</p><h3 id="companyPreview">PaK Sale App</h3><small id="receiptPreview">Tax 5% · Receipt ready</small></div><button class="settings-button" id="receiptSettings">⚙ Configure receipt</button></div></section>
        <section class="history-panel" id="reports"><div class="section-heading"><div><p class="eyebrow">TRANSACTIONS</p><h2>Sale history</h2></div><button class="filter-btn" id="clearHistory">Clear history</button></div><div id="saleHistory"><div class="empty-history">Abhi koi sale record nahi hai.</div></div></section>
    </main>
</div>
<div class="modal-backdrop" id="settingsModal"><form class="modal" id="settingsForm"><button type="button" class="modal-close" data-close="settingsModal">×</button><p class="eyebrow">BUSINESS SETTINGS</p><h2>Receipt aur company</h2><label>Company name<input id="companyName" required placeholder="Aapki company ka naam"></label><label>Tax rate (%)<input id="taxRate" type="number" min="0" max="100" step="0.01"></label><label>Company logo<input id="companyLogo" type="file" accept="image/*"></label><div class="logo-preview" id="logoPreview">Logo preview</div><button class="save-button" type="submit">Save settings</button></form></div>
<div class="modal-backdrop" id="drawerModal"><form class="modal small-modal" id="drawerForm"><button type="button" class="modal-close" data-close="drawerModal">×</button><p class="eyebrow" id="drawerTitle">CASH DRAWER</p><h2 id="drawerHeading">Open cash drawer</h2><label>Amount (Rs)<input id="drawerAmount" type="number" min="0" step="1" required placeholder="0"></label><button class="save-button" type="submit">Save amount</button></form></div>
<div class="modal-backdrop" id="cashFlowModal"><form class="modal small-modal" id="cashFlowForm"><button type="button" class="modal-close" data-close="cashFlowModal">×</button><p class="eyebrow">CASH MOVEMENT</p><h2 id="cashFlowHeading">Cash in</h2><label>Amount (Rs)<input id="cashFlowAmount" type="number" min="0" step="1" required placeholder="0"></label><label>Reason<input id="cashFlowReason" maxlength="80" placeholder="e.g. petty cash"></label><button class="save-button" type="submit">Save movement</button></form></div>
<div class="toast" id="toast"></div>
<script>window.POS_PRODUCTS = <?= json_encode($products) ?>; window.POS_INSTALL_SETTINGS = <?= json_encode(['company' => $companyName, 'tax' => $taxRate, 'currency' => $currency === 'Rs' ? 'PKR' : $currency, 'printer' => $installedSettings['printer'] ?? 'browser', 'logo' => $companyLogo]) ?>;</script><script src="<?= $assetPrefix ?>app.js"></script>
</body>
</html>
