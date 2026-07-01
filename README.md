# 🇮🇳 Explore India – Live Travel Planner & Platform Directory

Explore India is a production-ready, database-driven travel planning web application deployed live on the cloud. The platform enables travelers to customize multi-hotel itineraries, book predefined packages, hire verified local guides, and manage transportation bookings. It features a robust administration console and a secure portal for local guides, all integrated with automated SMTP mailers and secure checkout processors.

---

## 🌐 Live Platform Architecture

The platform is optimized for production hosting environments (e.g., InfinityFree Cloud Hosting) and implements a series of enterprise-grade security and configuration patterns:

* **Auto-Detect Environment Engine**: Seamlessly transitions database connection strings between development sandboxes and live production servers without manual file updates.
* **Production Security Shields (.htaccess)**: Blocks open directory browsing across assets (`/css/`, `/images/`, `/uploads/`) and restricts direct URL access to configuration files (`db.php`).
* **Bcrypt Password Migration**: Dynamically validates BCrypt and legacy MD5 passwords on the Admin Gateway, automatically upgrading hashes to Bcrypt on successful authentication.
* **Secure Database Access**: All live queries are hardened using prepared statements and advanced sanitization filters to block SQL Injection (SQLi) attempts.

---

## 🚀 Key Functional Modules

### 1. Traveler Console (Client Side)
* **Custom Itinerary Planner**: Choose a state, select hotels from multiple cities, specify trip duration, and dynamically compile custom travel itineraries.
* **Special Tour Packages**: Browse curated holiday packages with pre-integrated hotel bookings and pricing.
* **Responsive 5-Image Sliders**: Fluid, responsive animation sliders across J&K, Uttarakhand, Kerala, Tamil Nadu, Gujarat, Rajasthan, Madhya Pradesh, Sikkim, and West Bengal package pages.
* **Unified Transport Hub**: Interactive mock interface for booking flights, trains, and cabs.

### 2. Local Guide Portal
* **Verified Registrations**: Multi-step registration secured with OTP verification via transactional emails.
* **Guide Dashboard**: Access assigned bookings, edit profile details, languages, and set custom passwords in a single-page modal experience.
* **SMTP Recovery**: Secure password reset links sent via SMTP to registered guides using custom activation tokens.

### 3. Admin Control Console
* **Real-time Analytics**: Summary dashboard displaying live traveler counts, active guides, hotel directories, and package bookings with Chart.js visualization.
* **Location & Route Manager**: CRUD control panels for configuring states, cities, and station nodes.
* **Auditing System**: Manage customers (block/unblock), inspect booking logs, and verify local guide applications.

---

## 🛠️ Technology Stack

* **Backend**: PHP 7.4+ (Structured MVC and procedural elements)
* **Database**: MySQL (Relational schema with optimized join queries)
* **Frontend**: HTML5, Vanilla CSS3, Javascript (ES6), jQuery, Bootstrap 4
* **APIs & Integrations**:
  * **Razorpay / Instamojo**: Payment gateway checkout integration.
  * **PHPMailer (SMTP)**: Transactional email notifications (OTPs, activations, resets).
  * **Leaflet JS & OpenStreetMap**: Interactive mapping and branch locator.

---

## 📦 Deployment & Setup

### Database Configuration
To deploy the platform in a fresh database environment:
1. Create a MySQL database (e.g., `exploreindia`).
2. Import the latest relational schema from `database/if0_42189423_exploreindia.sql`.
3. Configure database credentials in `db.php` corresponding to your production or local connection environment.

### SMTP Mailer Setup
The system uses PHPMailer for sending transaction-related emails. Ensure global credentials are set in mail-sending utilities (`send_otp.php`, `forgot_password.php`, etc.) using your verified SMTP account details.
