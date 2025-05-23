Project Handover Guide
Online Art Store
GitHub: Download From Here

---

1. Project Overview
   This project is an Online Art Store where users can:
   • Browse and select art products
   • Checkout by submitting their order form
   • Submit testimonials from the homepage
   • Review products by selecting them
   • View the latest news updates
   The admin can manage everything via a secured admin panel.

---

2. Features
   User Side:
   • Select products and add to cart
   • Checkout with form submission (name, email, phone, address)
   • Receive order confirmation emails
   • Submit testimonials from homepage
   • Review products (ratings and comments)
   • View latest news
   Admin Side:
   • Session-restricted admin panel — login required (check admin.php for credentials)
   • Dashboard for managing:
   o Products
   o News
   o Testimonials (approve/reject)
   o Orders (view and update status)
   • Automatic emails sent to both user and business on new orders
   • SMTP email sending configured in order.php (needs setup)

---

3. Setup Instructions
   3.1 Prerequisites
   • PHP 7.4+ installed
   • MySQL or MariaDB installed
   • Web server like Apache or Nginx (XAMPP recommended for beginners)
   • Composer installed (for PHPMailer dependencies if needed)
   • Internet connection for SMTP email sending and TailwindCSS CDN

---

3.2 Database Setup

1. Open phpMyAdmin (usually at http://localhost/phpmyadmin if using XAMPP).
2. Create a new database called art_store_db (or any name you want).
3. In the main project directory, find the file art_store_db.sql.
4. Import this file via phpMyAdmin:
   o Select the database
   o Click Import tab
   o Choose art_store_db.sql file
   o Click Go to import tables and data

---

3.3 Configure Database Connection (db.php)

1. Open the db.php file in your project root directory.
2. Update the database connection parameters to match your setup:

---

3.4 Admin Credentials (admin.php)
• Admin login is restricted by session authentication.
• Open admin.php to find or update admin username and password.
• Default login credentials are stored there.
• Only admins can access the dashboard to manage products, orders, news, and testimonials.

---

3.5 SMTP Setup for Order Confirmation Emails (order.php)
This project uses PHPMailer to send order confirmation emails to both the customer and store owner.
Follow these steps to configure SMTP:

1. Open order.php.
2. Find the SMTP settings section (look for $mail->Host, $mail->Username, etc.).
3. Use your Gmail account for SMTP (recommended):
   o $mail->Host = 'smtp.gmail.com';
   o $mail->SMTPAuth = true;
   o $mail->Username = 'your-email@gmail.com'; // replace with your email
   o $mail->Password = 'your-app-password'; // use App Password if 2FA enabled
   o $mail->SMTPSecure = 'tls';
   o $mail->Port = 587;
4. Important:
   If your Gmail has 2-Step Verification enabled, you must generate an App Password and use that here instead of your normal password.
5. If you don’t have 2FA enabled, you might need to allow Less Secure Apps access for SMTP to work.
6. Save the changes.

---

4. How to Run the Project
1. Make sure you have a working web server with PHP and MySQL.
1. Import the database with art_store_db.sql.
1. Configure your DB connection in db.php.
1. Set up your SMTP credentials in order.php.
1. Place the entire project folder inside your web server's root directory (htdocs for XAMPP).
1. Open your browser and go to the homepage (index.php).
1. To access admin panel: open admin.php in the browser and login with credentials from admin.php file.

---

5. Additional Notes
   • Always clear browser cache if you update code and don’t see changes.
   • For security, change admin credentials before going live.
   • Emails rely on proper SMTP setup, so double-check those details if emails fail.
   • Sessions are used for cart and admin authentication — ensure PHP sessions are enabled on your server.
   • Testimonials and news can be managed by the admin dashboard after login.

---

6. Contact & Support
   For questions or issues, reach out to:
   Supto (Developer)
   Email: supto.cse.vu@gmail.com

---

END OF DOCUMENT
