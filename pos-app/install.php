<?php
$configDirectory = __DIR__ . '/storage';
$configFile = $configDirectory . '/config.json';
$installed = is_file($configFile);
$error = '';
$success = '';
$currencyOptions = ['PKR' => 'Rs - Pakistani Rupee', 'USD' => '$ - US Dollar', 'EUR' => '€ - Euro', 'GBP' => '£ - British Pound', 'AED' => 'د.إ - UAE Dirham', 'SAR' => '﷼ - Saudi Riyal', 'QAR' => '﷼ - Qatari Riyal', 'INR' => '₹ - Indian Rupee', 'BDT' => '৳ - Bangladeshi Taka', 'CNY' => '¥ - Chinese Yuan', 'JPY' => '¥ - Japanese Yen', 'CAD' => 'C$ - Canadian Dollar', 'AUD' => 'A$ - Australian Dollar', 'ZAR' => 'R - South African Rand', 'TRY' => '₺ - Turkish Lira', 'MYR' => 'RM - Malaysian Ringgit'];
$currencyMarkup = '<select name="currency">';
foreach ($currencyOptions as $code => $label) $currencyMarkup .= '<option value="' . e($code) . '">' . e($code . ' - ' . $label) . '</option>';
$currencyMarkup .= '</select>';
ob_start(function ($html) use ($currencyMarkup) {
    $html = str_replace('Roznamcha POS', 'PaK Sale App', $html);
    $html = str_replace('<label>Currency<input name="currency" value="Rs" maxlength="8"></label>', '<label>Currency' . $currencyMarkup . '</label><label>Receipt printer<select name="printer"><option value="browser">Normal printer / A4</option><option value="thermal58">Thermal printer 58mm</option><option value="thermal80">Thermal printer 80mm</option></select></label>', $html);
    return str_replace('</main>', '<p class="credit">Developed by Jibran Khan Malap</p></main>', $html);
});

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$installed) {
    $company = trim($_POST['company'] ?? '');
    $tax = (float) ($_POST['tax'] ?? 0);
    $currency = trim($_POST['currency'] ?? 'Rs');
    if ($company === '') {
        $error = 'Company name zaroori hai.';
    } elseif ($tax < 0 || $tax > 100) {
        $error = 'Tax 0 se 100 ke darmiyan hona chahiye.';
    } elseif (!is_dir($configDirectory) && !mkdir($configDirectory, 0755, true)) {
        $error = 'Storage folder create nahi ho saka. Folder permissions check karein.';
    } elseif (!is_writable($configDirectory)) {
        $error = 'Storage folder writable nahi hai.';
    } else {
        $logo = '';
        if (!empty($_FILES['logo']['tmp_name'])) {
            $imageInfo = @getimagesize($_FILES['logo']['tmp_name']);
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            if (!$imageInfo || !isset($allowed[$imageInfo['mime']])) {
                $error = 'Logo sirf JPG, PNG ya WEBP image hona chahiye.';
            } elseif ($_FILES['logo']['size'] > 2 * 1024 * 1024) {
                $error = 'Logo ka size 2 MB se kam hona chahiye.';
            } else {
                $logoName = 'logo.' . $allowed[$imageInfo['mime']];
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $configDirectory . '/' . $logoName)) {
                    $logo = 'storage/' . $logoName;
                } else {
                    $error = 'Logo save nahi ho saka.';
                }
            }
        }
        if ($error === '') {
            $printer = in_array($_POST['printer'] ?? 'browser', ['browser', 'thermal58', 'thermal80'], true) ? $_POST['printer'] : 'browser';
            $config = ['company' => $company, 'tax' => $tax, 'currency' => $currency ?: 'PKR', 'printer' => $printer, 'logo' => $logo, 'installed_at' => date('c')];
            if (file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX) === false) {
                $error = 'Configuration save nahi ho saki.';
            } else {
                $installed = true;
                $success = 'Installation complete ho gayi. POS open karein.';
            }
        }
    }
}
function e($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Roznamcha POS Installer</title><style>
:root{--green:#17342e;--coral:#eb6c55;--line:#e5ebe8}*{box-sizing:border-box}body{margin:0;background:#f6f9f7;color:#1b2630;font:14px Arial,sans-serif;min-height:100vh;display:grid;place-items:center}.installer{width:min(470px,calc(100% - 30px));background:#fff;border:1px solid var(--line);border-radius:12px;padding:30px;box-shadow:0 18px 55px #17342e12}.brand{display:flex;align-items:center;gap:10px;color:var(--green);font-size:19px;font-weight:bold;margin-bottom:25px}.mark{display:grid;place-items:center;width:36px;height:36px;border-radius:9px;background:var(--coral);color:#fff;font-size:22px}.eyebrow{color:#84928d;letter-spacing:1.5px;font-size:10px;font-weight:bold;margin:0 0 8px}.installer h1{font-size:25px;margin:0 0 8px}.intro{color:#71807a;line-height:1.6;margin:0 0 22px}label{display:block;font-size:12px;font-weight:bold;margin:15px 0;color:#52615b}input{display:block;width:100%;margin-top:7px;border:1px solid var(--line);border-radius:6px;padding:11px;font:inherit;outline-color:#83bca1}button,.open{display:block;width:100%;border:0;border-radius:6px;background:var(--coral);color:#fff;padding:12px;font-weight:bold;text-align:center;text-decoration:none;margin-top:22px;cursor:pointer}.notice{padding:12px;border-radius:6px;font-size:12px;margin-bottom:15px}.error{background:#fff0ed;color:#b54837}.success{background:#e8f6ee;color:#287453}.requirements{border-top:1px solid var(--line);padding-top:15px;margin-top:20px;color:#84908b;font-size:11px;line-height:1.7}
</style></head><body><main class="installer"><div class="brand"><span class="mark">R</span> Roznamcha POS</div><?php if ($installed): ?><p class="eyebrow">ALREADY INSTALLED</p><h1>Setup complete</h1><p class="intro">POS pehle se configure hai. Dobara install karne ki zaroorat nahi.</p><?php if ($success): ?><div class="notice success"><?= e($success) ?></div><?php endif; ?><a class="open" href="index.php">POS open karein</a><?php else: ?><p class="eyebrow">WELCOME TO SETUP</p><h1>Apna POS configure karein</h1><p class="intro">Company details set karein. Yeh settings receipts aur tax calculation mein use hongi.</p><?php if ($error): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?><form method="post" enctype="multipart/form-data"><label>Company name<input name="company" required placeholder="Meri Shop / Restaurant"></label><label>Tax rate (%)<input name="tax" type="number" min="0" max="100" step="0.01" value="5"></label><label>Currency<input name="currency" value="Rs" maxlength="8"></label><label>Company logo<input name="logo" type="file" accept="image/jpeg,image/png,image/webp"></label><button type="submit">Install POS</button></form><div class="requirements">Required: PHP 8.0+<br>Optional: JPG, PNG ya WEBP logo (max 2 MB)</div><?php endif; ?></main></body></html>
