How to Install
-------------------------
1. Create/locate a new mysql database to install php point of sale into
2. Execute the file database/database.sql to create the tables needed
3. unzip and upload PHP Point Of Sale files to web server
4. Copy application/config/database.php.tmpl to application/config/database.php
5. Modify application/config/database.php to connect to your database
6. Go to your point of sale install via the browser
7. LOGIN using
username: admin 
password:pointofsale
8. Enjoy

Credit: PaK Sale App was developed by Jibran Khan Malap.

New standalone POS preview
--------------------------
The new multi-business POS interface is available in the `pos-app` folder.
It currently supports Pharmacy, Grocery/Shop, Retail and Restaurant products,
with product search, category filters, cart quantities and checkout.

To run it locally with PHP 8:
1. Open a terminal in the project root
2. Run: `php -S 0.0.0.0:8080 -t pos-app`
3. Open: http://localhost:8080

Installer
---------
For a fresh deployment, open the project root in your browser. The installer
will appear automatically when no setup exists. You can also open
`/pos-app/install.php` directly. Enter the company name, tax rate, currency and
optional logo; the setup is saved in `pos-app/storage/config.json`.

The original CodeIgniter application and the Laravel NexoPOS application are
kept unchanged while the new POS is built in small, testable milestones.

Currency and printer settings
-----------------------------
Settings mein international currency select kar sakte hain, including PKR,
USD, EUR, GBP, AED, SAR, QAR, INR, BDT, CNY, JPY, CAD, AUD and more. Receipt
printer options are Normal/A4, Thermal 58mm and Thermal 80mm. Thermal choice
receipt print window ka paper width set karti hai.

Sales and cash movements
------------------------
Completed payments `Sale history` mein browser ke local storage mein save hoti
hain. Cash drawer se custom `Cash in` aur `Cash out` entries bhi add ki ja
sakti hain; cash out current drawer balance se zyada nahi ho sakta.

Product management
------------------
Sale counter par `Add product` se product, category, price, stock aur unit add
kar sakte hain. Har product card mein `Edit` se price/tax-related sale settings
update kar sakte hain aur `Delete` se product remove kar sakte hain. Tax rate
receipt settings mein change hota hai.

Payment and sale lists
----------------------
Settings mein custom payment methods add/remove kar sakte hain. Sale ko
`Paid & receipt` ya `Save unpaid` ke taur par save kiya ja sakta hai. Sale
history mein All, Paid only aur Unpaid only list filter karke `Print list` se
print ki ja sakti hai.

Item barcodes
-------------
Products ko auto barcode milta hai. Product card ke `Barcode` action se label
print kar sakte hain, aur search box mein barcode number se item dhoond sakte
hain. Product edit form se barcode manually change bhi kiya ja sakta hai.

Deployment troubleshooting
---------------------------
The new POS requires a PHP hosting/runtime because the entry point is
`index.php`. A static sandbox import can show `403 Unable to import sandbox`;
that is a hosting preview error, not a POS response. Configure the deployment
root as this repository root, enable PHP 8.0 or newer, and redeploy. The local
root health check should return the `PaK Sale App` page with HTTP 200.