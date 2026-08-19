const cart = new Map();
const currencies = { PKR: ['Rs', 'Pakistani Rupee'], USD: ['$', 'US Dollar'], EUR: ['€', 'Euro'], GBP: ['£', 'British Pound'], AED: ['د.إ', 'UAE Dirham'], SAR: ['﷼', 'Saudi Riyal'], QAR: ['﷼', 'Qatari Riyal'], KWD: ['د.ك', 'Kuwaiti Dinar'], BHD: ['د.ب', 'Bahraini Dinar'], OMR: ['﷼', 'Omani Rial'], INR: ['₹', 'Indian Rupee'], BDT: ['৳', 'Bangladeshi Taka'], LKR: ['Rs', 'Sri Lankan Rupee'], NPR: ['रू', 'Nepalese Rupee'], CNY: ['¥', 'Chinese Yuan'], JPY: ['¥', 'Japanese Yen'], CAD: ['C$', 'Canadian Dollar'], AUD: ['A$', 'Australian Dollar'], NZD: ['NZ$', 'New Zealand Dollar'], ZAR: ['R', 'South African Rand'], TRY: ['₺', 'Turkish Lira'], MYR: ['RM', 'Malaysian Ringgit'], IDR: ['Rp', 'Indonesian Rupiah'], THB: ['฿', 'Thai Baht'], CHF: ['CHF', 'Swiss Franc'] };
const printers = { browser: 'Normal printer / A4', thermal58: 'Thermal printer 58mm', thermal80: 'Thermal printer 80mm' };
const languages = { en: 'English', 'ur-pk': 'Roman Urdu', ur: 'Urdu', hi: 'Hindi', ar: 'Arabic', bn: 'Bangla', pa: 'Punjabi', sd: 'Sindhi', fa: 'Persian', tr: 'Turkish', id: 'Indonesian', ms: 'Malay', zh: 'Chinese', ja: 'Japanese', ko: 'Korean', es: 'Spanish', fr: 'French', de: 'German', it: 'Italian', pt: 'Portuguese', ru: 'Russian', nl: 'Dutch', pl: 'Polish', th: 'Thai', vi: 'Vietnamese', sw: 'Swahili' };
const money = value => `${currencies[settings.currency]?.[0] || settings.currency || 'Rs'} ${value.toLocaleString('en-PK')}`;
const productById = id => window.POS_PRODUCTS.find(product => product.id === Number(id));
const grid = document.querySelector('#productGrid');
const search = document.querySelector('#search');
const items = document.querySelector('#cartItems');
const toast = document.querySelector('#toast');
const defaultSettings = { company: 'PaK Sale App', tax: 5, currency: 'PKR', printer: 'browser', language: 'en', logo: '', ...(window.POS_INSTALL_SETTINGS || {}) };
let settings = { ...defaultSettings, ...JSON.parse(localStorage.getItem('posSettings') || '{}') };
if (!currencies[settings.currency]) settings.currency = 'PKR';
let drawerBalance = Number(localStorage.getItem('drawerBalance') || 0);
let saleHistory = JSON.parse(localStorage.getItem('saleHistory') || '[]');
let drawerAction = 'open';
let cashFlowAction = 'in';
const taxValue = () => Math.round([...cart].reduce((sum, [id, quantity]) => sum + productById(id).price * quantity, 0) * Number(settings.tax) / 100);
function renderProducts() {
  const query = search.value.trim().toLowerCase();
  const category = document.querySelector('.category.selected').dataset.category;
  document.querySelectorAll('.product-card').forEach(card => {
    const matchesQuery = card.dataset.name.includes(query);
    const matchesCategory = category === 'Sab' || card.dataset.category === category;
    card.style.display = matchesQuery && matchesCategory ? 'flex' : 'none';
  });
}
function renderCart() {
  if (!cart.size) { items.innerHTML = '<div class="empty-cart">Product select karein<br><small>Order yahan nazar aayega</small></div>'; }
  else { items.innerHTML = [...cart].map(([id, quantity]) => { const product = productById(id); return `<div class="cart-item"><span class="item-name">${product.name}</span><span class="qty"><button data-action="minus" data-id="${id}">−</button><span>${quantity}</span><button data-action="plus" data-id="${id}">+</button></span><b class="item-price">${money(product.price * quantity)}</b></div>`; }).join(''); }
  const subtotal = [...cart].reduce((sum, [id, quantity]) => sum + productById(id).price * quantity, 0);
  const tax = taxValue();
  document.querySelector('#subtotal').textContent = money(subtotal);
  document.querySelector('#tax').textContent = money(tax);
  document.querySelector('#total').textContent = money(subtotal + tax);
}
function renderSettings() {
  document.querySelector('#companyName').value = settings.company;
  document.querySelector('#taxRate').value = settings.tax;
  document.querySelector('#currencyCode').value = settings.currency;
  document.querySelector('#printerType').value = settings.printer;
  document.querySelector('#languageCode').value = settings.language;
  document.querySelector('#taxLabel').firstChild.textContent = `Tax (${settings.tax}%) `;
  document.querySelector('#companyPreview').textContent = settings.company;
  document.querySelector('#receiptPreview').textContent = `Tax ${settings.tax}% · Receipt ready`;
  document.querySelector('#drawerBalance').textContent = money(drawerBalance);
  document.querySelector('#logoPreview').innerHTML = settings.logo ? `<img src="${settings.logo}" alt="Company logo">` : 'Logo preview';
}
function renderHistory() {
  const history = document.querySelector('#saleHistory');
  if (!saleHistory.length) { history.innerHTML = '<div class="empty-history">Abhi koi sale record nahi hai.</div>'; return; }
  history.innerHTML = saleHistory.slice().reverse().map(sale => `<div class="history-row"><div><strong>${sale.number}</strong><small>${sale.date} · ${sale.payment} · ${sale.items} items</small></div><b>${money(sale.total)}</b></div>`).join('');
}
function openModal(id) { document.querySelector(`#${id}`).classList.add('visible'); }
function closeModal(id) { document.querySelector(`#${id}`).classList.remove('visible'); }
function printReceipt() {
  const subtotal = [...cart].reduce((sum, [id, quantity]) => sum + productById(id).price * quantity, 0);
  const tax = taxValue();
  const rows = [...cart].map(([id, quantity]) => `<tr><td>${productById(id).name} x ${quantity}</td><td>${money(productById(id).price * quantity)}</td></tr>`).join('');
  const receipt = window.open('', '_blank', 'width=420,height=650');
  if (!receipt) return showToast('Receipt print ke liye popup allow karein');
  const width = settings.printer === 'thermal58' ? '58mm' : settings.printer === 'thermal80' ? '80mm' : '420px';
  receipt.document.write(`<html><head><title>${settings.company} Receipt</title><style>@page{size:${width} auto;margin:0}body{font:14px Arial;padding:12px;color:#222;max-width:${width};margin:auto}h1{text-align:center;font-size:20px}p{text-align:center;color:#666}table{width:100%;border-collapse:collapse;margin:20px 0}td{padding:7px 0;border-bottom:1px solid #ddd}td:last-child{text-align:right}strong{display:flex;justify-content:space-between;font-size:18px}</style></head><body>${settings.logo ? `<p><img src="${settings.logo}" style="max-width:90px;max-height:60px"></p>` : ''}<h1>${settings.company}</h1><p>Sale receipt · ${new Date().toLocaleString()}</p><table>${rows}</table><p>Subtotal: ${money(subtotal)}<br>Tax (${settings.tax}%): ${money(tax)}</p><strong><span>Total</span><span>${money(subtotal + tax)}</span></strong><p>Thank you for shopping with us</p><script>window.print();</script></body></html>`);
  receipt.document.close();
}
function showToast(message) { toast.textContent = message; toast.classList.add('show'); window.setTimeout(() => toast.classList.remove('show'), 2200); }
grid.addEventListener('click', event => { const card = event.target.closest('.product-card'); if (!card) return; const id = Number(card.dataset.id); cart.set(id, (cart.get(id) || 0) + 1); renderCart(); showToast(`${productById(id).name} order mein add ho gaya`); });
items.addEventListener('click', event => { const button = event.target.closest('button'); if (!button) return; const id = Number(button.dataset.id); const next = (cart.get(id) || 0) + (button.dataset.action === 'plus' ? 1 : -1); next > 0 ? cart.set(id, next) : cart.delete(id); renderCart(); });
document.querySelector('#clearCart').addEventListener('click', () => { cart.clear(); renderCart(); });
document.querySelector('#checkout').addEventListener('click', () => { if (!cart.size) return showToast('Pehle product select karein'); const payment = document.querySelector('.payment.active').textContent.trim(); const total = [...cart].reduce((sum, [id, quantity]) => sum + productById(id).price * quantity, 0) + taxValue(); saleHistory.push({ number: `#${String(10249 + saleHistory.length).padStart(5, '0')}`, date: new Date().toLocaleString(), payment, items: [...cart.values()].reduce((sum, quantity) => sum + quantity, 0), total }); localStorage.setItem('saleHistory', JSON.stringify(saleHistory)); if (payment === 'Cash') { drawerBalance += total; localStorage.setItem('drawerBalance', drawerBalance); } printReceipt(); cart.clear(); renderCart(); renderSettings(); renderHistory(); showToast('Payment complete. Receipt tayyar hai.'); });
document.querySelectorAll('.category').forEach(button => button.addEventListener('click', () => { document.querySelector('.category.selected').classList.remove('selected'); button.classList.add('selected'); renderProducts(); }));
search.addEventListener('input', renderProducts);
document.querySelectorAll('.payment').forEach(button => button.addEventListener('click', () => { document.querySelector('.payment.active').classList.remove('active'); button.classList.add('active'); }));
document.querySelector('#settingsLink').addEventListener('click', event => { event.preventDefault(); openModal('settingsModal'); });
document.querySelector('#receiptSettings').addEventListener('click', () => openModal('settingsModal'));
document.querySelectorAll('[data-close]').forEach(button => button.addEventListener('click', () => closeModal(button.dataset.close)));
document.querySelector('#settingsForm').addEventListener('submit', event => { event.preventDefault(); settings.company = document.querySelector('#companyName').value.trim() || defaultSettings.company; settings.tax = Number(document.querySelector('#taxRate').value) || 0; settings.currency = document.querySelector('#currencyCode').value; settings.printer = document.querySelector('#printerType').value; settings.language = document.querySelector('#languageCode').value; localStorage.setItem('posSettings', JSON.stringify(settings)); closeModal('settingsModal'); renderSettings(); showToast('Language aur settings save ho gayi'); });
document.querySelector('#companyLogo').addEventListener('change', event => { const file = event.target.files[0]; if (!file) return; const reader = new FileReader(); reader.onload = () => { settings.logo = reader.result; document.querySelector('#logoPreview').innerHTML = `<img src="${reader.result}" alt="Company logo">`; }; reader.readAsDataURL(file); });
document.querySelector('#openDrawer').addEventListener('click', () => { drawerAction = 'open'; document.querySelector('#drawerHeading').textContent = 'Open cash drawer'; document.querySelector('#drawerAmount').value = ''; openModal('drawerModal'); });
document.querySelector('#closeDrawer').addEventListener('click', () => { drawerAction = 'close'; document.querySelector('#drawerHeading').textContent = 'Close & count drawer'; document.querySelector('#drawerAmount').value = drawerBalance; openModal('drawerModal'); });
document.querySelector('#drawerForm').addEventListener('submit', event => { event.preventDefault(); drawerBalance = Number(document.querySelector('#drawerAmount').value) || 0; localStorage.setItem('drawerBalance', drawerBalance); closeModal('drawerModal'); renderSettings(); showToast(drawerAction === 'open' ? 'Cash drawer open ho gaya' : 'Cash drawer close aur count ho gaya'); });
document.querySelector('#cashIn').addEventListener('click', () => { cashFlowAction = 'in'; document.querySelector('#cashFlowHeading').textContent = 'Cash in'; document.querySelector('#cashFlowAmount').value = ''; openModal('cashFlowModal'); });
document.querySelector('#cashOut').addEventListener('click', () => { cashFlowAction = 'out'; document.querySelector('#cashFlowHeading').textContent = 'Cash out'; document.querySelector('#cashFlowAmount').value = ''; openModal('cashFlowModal'); });
document.querySelector('#cashFlowForm').addEventListener('submit', event => { event.preventDefault(); const amount = Number(document.querySelector('#cashFlowAmount').value) || 0; if (cashFlowAction === 'out' && amount > drawerBalance) return showToast('Drawer mein itna cash nahi hai'); drawerBalance += cashFlowAction === 'in' ? amount : -amount; localStorage.setItem('drawerBalance', drawerBalance); closeModal('cashFlowModal'); renderSettings(); showToast(cashFlowAction === 'in' ? 'Cash drawer mein add ho gaya' : 'Cash drawer se cash nikal gaya'); });
document.querySelector('#clearHistory').addEventListener('click', () => { saleHistory = []; localStorage.removeItem('saleHistory'); renderHistory(); showToast('Sale history clear ho gayi'); });
const currencyField = document.createElement('label'); currencyField.textContent = 'Currency'; currencyField.innerHTML += `<select id="currencyCode">${Object.entries(currencies).map(([code, data]) => `<option value="${code}">${code} - ${data[1]} (${data[0]})</option>`).join('')}</select>`; document.querySelector('#companyLogo').parentElement.before(currencyField);
const printerField = document.createElement('label'); printerField.textContent = 'Receipt printer'; printerField.innerHTML += `<select id="printerType">${Object.entries(printers).map(([code, name]) => `<option value="${code}">${name}</option>`).join('')}</select>`; document.querySelector('#companyLogo').parentElement.before(printerField);
const languageField = document.createElement('label'); languageField.textContent = 'Language'; languageField.innerHTML += `<select id="languageCode">${Object.entries(languages).map(([code, name]) => `<option value="${code}">${name}</option>`).join('')}</select>`; document.querySelector('#companyLogo').parentElement.before(languageField);
renderSettings();
renderHistory();
renderCart();
