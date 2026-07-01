# 🇮🇳 Explore India – Premium Travel Planner & Live Platform Console

[![Live Platform](https://img.shields.io/badge/Live-exploreindia.rf.gd-FF5722?style=for-the-badge&logo=google-chrome&logoColor=white)](http://exploreindia.rf.gd/)
[![Tech Stack](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![Database](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)

Explore India is a premium, live-deployed travel planning and booking web application. It offers an end-to-end traveler portal for custom multi-hotel itinerary plotting, predefined package checking, transportation bookings, and local guide hirings. The platform is supported by an interactive Admin Console for platform-wide audits and verified Guide Portals.

> [!NOTE]
> **Production URL:** The application is live at **[exploreindia.rf.gd](http://exploreindia.rf.gd/)**.

---

## 🚀 Core Modules & Real-World Features

### 1. Traveler Portal (Client Side)
* **Custom Itinerary Planner**: Choose a state, select hotels from multiple cities, set duration, and dynamically compile custom travel schedules.
* **Special Tour Packages**: Browse curated holiday packages with pre-integrated hotel bookings and pricing.
* **Unified Transport Console**: Simulated real-time booking interfaces for flights, trains, and cabs (with seat/coach pickers).
* **Interactive Geo Mapping**: Integration with **Mappls Web SDK (v3.0)** for live branch locating and dynamic mapping markers.

### 2. Verified Local Guide Console
* **Guide Registrations**: Multi-step registration secured with OTP verification via transactional emails.
* **Guide Dashboard**: Displays active bookings, traveler details, and guide requests.
* **Interactive Profile Editing**: Guides can modify name, contact, language settings, and passwords in a single-page modal experience.
* **Password Recovery**: Secure, token-based forgot password flow. Guides receive reset links via SMTP using custom tokens.

### 3. Administrative Control Console
* **Real-time Metrics**: Dynamic counts for customers, guides, hotels, packages, states, cities, trains, and bookings.
* **Interactive Visualizations**: Chart.js charts displaying booking distributions and sales analytics.
* **Location & Route Manager**: CRUD control panels for configuring states, cities, and train coordinates.
* **Auditing System**: Manage customers (block/unblock), inspect booking logs, and verify local guide applications.

---

## 🛡️ Security Hardening (Production Level)

* **SQL Injection (SQLi) Protection**: Hardened database queries using Prepared Statements and structured input sanitization (`mysqli_real_escape_string` / `intval` casting).
* **Bcrypt Password Migration**: The admin gateway dynamically verifies standard Bcrypt hashes and older MD5 passwords, automatically upgrading MD5 hashes to Bcrypt on successful login.
* **Admin & Guide Session Guards**: Custom `check.php` authentication gates prevent direct URL bypass to admin and guide dashboards.
* **Direct Directory Protection (.htaccess)**: Prevents open directory listing across assets folders (`/css/`, `/images/`, `/uploads/`) and blocks direct browser requests to database config files.
* **Auto-Detect Environment Engine**: Dynamically detects the server environment (Localhost vs. Live InfinityFree server) to resolve database connection details.

---

## 🛠️ Technology Stack

* **Backend**: PHP 7.4+ (Structured MVC and procedural elements)
* **Database**: MySQL (Relational schema with optimized join queries)
* **Frontend**: HTML5, Vanilla CSS3, Javascript (ES6), jQuery, Bootstrap 4
* **APIs & Integrations**:
  * **Razorpay**: Payment gateway checkout integration.
  * **PHPMailer (SMTP)**: Transactional email notifications (OTPs, activations, resets).
  * **Mappls SDK**: Interactive mapping and branch locator.

---

## 📦 Deployment & Setup

### Database Configuration
To deploy the platform in a fresh database environment:
1. Create a MySQL database (e.g., `exploreindia`).
2. Import the latest relational schema from `database/if0_42189423_exploreindia.sql`.
3. Configure database credentials in `db.php` corresponding to your production or local connection environment.

### SMTP Mailer Setup
The system uses PHPMailer for sending transaction-related emails. Ensure global credentials are set in mail-sending utilities (`send_otp.php`, `forgot_password.php`, etc.) using your verified SMTP account details.
