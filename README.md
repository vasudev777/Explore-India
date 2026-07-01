# 🇮🇳 Explore India – Premium Travel Planner & Admin Directory

Explore India is a full-featured, database-driven travel planning web application. It allows travelers to book predefined packages, build custom multi-hotel itineraries, hire verified local guides, and book flights, trains, and cabs. The project includes a sleek, responsive administrative dashboard for platform-wide auditing, revenue tracking, and guide verifications.

---

## 🚀 Key Features

### 1. Traveler Console (Client Side)
* **Custom Itinerary Planner**: Select a state, view available hotels grouped by cities, specify trip duration, and dynamically construct a custom itinerary.
* **Special Tour Packages**: View pre-planned curated packages featuring custom hotel integrations and pricing.
* **Unified Transport Hub**: Real-time mock booking interface for flights, trains, and cabs from city to city.
* **Local Guide Directory**: Browse registered guides, filtered by operating state and languages spoken.
* **Responsive 5-Image Sliders**: State package pages feature beautiful, fluid, mobile-responsive 5-image animation loops (Uttarakhand, Kerala, Tamil Nadu, Gujarat, Rajasthan, Madhya Pradesh, J&K, Sikkim, and West Bengal) optimized for mobile aspect ratios and touch devices.

### 2. Local Guide Portal
* **Verified Registration**: Standard registration with email activation guards.
* **Dashboard Control**: Active profile dashboard displaying assigned bookings and active requests.
* **Direct Profile Editing**: Edit name, mobile, languages, and passwords directly inside the dashboard modal.
* **SMTP Password Reset**: Secure password recovery with unique activation token reset links sent via SMTP email.

### 3. Admin Control Console (Dashboard)
* **Real-time Analytics**: Dynamic count cards, revenue summation, and interactive booking charts (Chart.js).
* **Location & Route Manager**: CRUD panels for States, Cities, and Route station coordinates.
* **Customer Audit System**: Block/Unblock users, view detailed bookings, and monitor feedback.
* **Local Guide Verification Panel**: Multi-stage approval interface (Email Verification check -> Admin Review -> Approval email notification -> Rejection mail with deletion).

### 4. Integrated Utilities & Security
* **Auto-Detect Database Connections**: Smart environment configurations allow the app to run locally on XAMPP and live on InfinityFree without swapping connection strings.
* **Auto-Password Migration (Bcrypt)**: The admin gateway dynamically verifies standard Bcrypt hashes and older MD5 passwords, automatically upgrading MD5 hashes to Bcrypt on successful login.
* **SQL Injection Prevention**: Active database queries are fully secured using Prepared Statements and string escaping.
* **Session Guards**: Centralized session check routing prevents direct URL bypass to admin and guide endpoints.
* **Directory Listing Blocks**: Custom `.htaccess` rules prevent directory listing and protect database config files from direct URL access.

---

## 🛠️ Technology Stack

* **Backend**: PHP 7+ (OOP and procedural architecture)
* **Database**: MySQL (relational structure with left-joins)
* **Frontend**: HTML5, Vanilla CSS3, Javascript, jQuery, Bootstrap 4
* **Payment Integration**: Razorpay API Integration
* **APIs & Libraries**: 
  * [Leaflet JS](https://leafletjs.com/) & [OpenStreetMap](https://www.openstreetmap.org/) (Interactive maps)
  * [Chart.js](https://www.chartjs.org/) (Data visualizations)
  * [PHPMailer](https://github.com/PHPMailer/PHPMailer) (SMTP Email notifications)

---

## 💻 Local Installation Guide

### Prerequisites
* [XAMPP](https://www.apachefriends.org/) (Apache & MySQL server)
* Git installed locally

### Step-by-Step Setup
1. Clone this repository into your XAMPP `htdocs` directory:
   ```bash
   cd C:\xampp\htdocs
   git clone https://github.com/YOUR_USERNAME/explore-india-travel.git Explore_India
   ```
2. Import the database schema:
   * Open browser and go to `http://localhost/phpmyadmin/`.
   * Create a new database named **`exploreindia`**.
   * Click **Import** and upload the SQL backup file located in `database/if0_42189423_exploreindia.sql`.
3. Set up database credentials:
   * Rename `db.php.example` to `db.php`.
   * Verify your local credentials match the default XAMPP details inside:
     ```php
     $servername = "localhost";
     $username = "root";
     $password = "";
     $dbname = "exploreindia";
     ```
4. Run the project:
   * Start Apache and MySQL in the XAMPP Control Panel.
   * Visit `http://localhost/Explore_India/` in your browser.
   * Access Admin Panel at `http://localhost/Explore_India/admin/login.php`.

---

## 🛡️ License

Distributed under the MIT License. See `LICENSE` for more information.
