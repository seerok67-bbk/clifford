# SineSine — Movie Rating and Review Website
**Tagline:** *"Discover. Watch. Rate."*

SineSine is a modern, responsive, and cinematic movie rating and review platform inspired by IMDb. Built specifically as a clean, modular 3rd-year BSIT capstone project using native PHP, MySQL, Bootstrap 5, Font Awesome, and jQuery.

---

## 🛠️ Technology Stack
- **Backend:** Native PHP (PDO with prepared statements, session-based authentication)
- **Database:** MySQL (`sinesine_db`, InnoDB, foreign key constraints)
- **Frontend:** HTML5, CSS3, Vanilla JS & jQuery 3.7
- **CSS Framework:** Bootstrap 5.3 (Dark theme, responsive flexbox & grid system)
- **Icons:** Font Awesome 6 (Consistent UI icons, no emojis)
- **Local Server:** XAMPP (Apache + MySQL)

---

## 📁 Project Folder Structure
```text
Sinesine/
├── config/
│   └── database.php             # PDO database connection & error handling
├── includes/
│   ├── auth.php                 # Role-based auth & access restriction
│   ├── functions.php            # Sanitization, CSRF tokens, SQL queries
│   ├── header.php               # HTML <head>, Bootstrap, FontAwesome & Navbar
│   ├── navbar.php               # Responsive navigation & user dropdown
│   ├── footer.php               # Footer & script links
│   └── movie-card.php           # Responsive reusable movie card partial
├── admin/
│   ├── header.php               # Admin sidebar & topbar
│   ├── footer.php               # Admin scripts & closing tags
│   ├── index.php                # Admin dashboard & live metrics
│   ├── movies.php               # Catalog management table
│   ├── movie-add.php            # Add movie form + poster upload
│   ├── movie-edit.php           # Edit movie info & poster replacement
│   ├── movie-delete.php         # Secure delete with CSRF protection
│   ├── users.php                # User management & status toggle
│   └── reviews.php              # Inappropriate review moderation
├── assets/
│   ├── css/
│   │   ├── style.css            # Cinematic dark theme styling
│   │   └── admin.css            # Admin control panel layout & cards
│   ├── js/
│   │   └── script.js            # Interactive star rating & image previews
│   └── images/
│       ├── movies/              # 15 high-res portrait movie posters
│       ├── avatars/             # User avatar uploads
│       └── default-avatar.png   # Default user avatar placeholder
├── database/
│   └── sinesine_db.sql          # Database schema + 15 seed movies
├── index.php                    # Cinematic homepage (hero, top rated, popular)
├── movies.php                   # Full catalog, genre filter, search, & sorting
├── movie-details.php            # Movie synopsis, cast, 10-star rating & reviews
├── login.php                    # Secure user & admin login
├── register.php                 # User registration form
├── logout.php                   # Session termination
├── profile.php                  # User profile & list of user reviews
├── edit-profile.php             # Edit profile info, password & upload avatar
├── submit-review.php            # Handle submit / update rating & review
├── delete-review.php            # Handle review deletion
├── setup-admin.php              # One-click administrator account setup tool
└── README.md                    # Setup and presentation documentation
```

---

## 🚀 Beginner-Friendly XAMPP Setup Instructions

### 1. Project Location
Verify that this `Sinesine` folder is located inside XAMPP's `htdocs` directory:
```
C:\xampp\htdocs\Sinesine
```

### 2. Start Apache and MySQL
1. Open the **XAMPP Control Panel**.
2. Click **Start** for **Apache**.
3. Click **Start** for **MySQL**.
*(Both modules should turn green).*

### 3. Import the Database in phpMyAdmin
1. Open your web browser and go to:
   ```
   http://localhost/phpmyadmin/
   ```
2. Click on the **Databases** tab.
3. In the "Create database" field, type:
   ```
   sinesine_db
   ```
   and select `utf8mb4_unicode_ci`, then click **Create**.
4. With `sinesine_db` selected in the left sidebar, click the **Import** tab in the top navigation.
5. Click **Choose File** (Browse) and select:
   ```
   C:\xampp\htdocs\Sinesine\database\sinesine_db.sql
   ```
6. Scroll down and click **Import** (or **Go**).
*(You should see a green success message confirming that the tables `users`, `movies`, and `reviews` alongside the 15 initial movies have been imported).*

### 4. Initialize the Administrator Account
1. Open your browser and navigate to:
   ```
   http://localhost/sinesine/setup-admin.php
   ```
2. Fill in the desired administrator credentials or use the defaults:
   - **Username:** `admin`
   - **Email:** `admin@sinesine.com`
   - **Password:** `admin123`
3. Click **Create Admin Account**.

### 5. Access and Test the Website
1. Visit the main website:
   ```
   http://localhost/sinesine/
   ```
2. **Testing as Guest:**
   - Browse the homepage spotlight hero, featured movies, and most discussed sections.
   - Use the search bar to search for movies or directors (e.g., "Nolan", "Inception").
   - Click **Genres** dropdown or go to **Movies** to filter by genre (Action, Sci-Fi, Drama, etc.) and sort by release year or rating.
   - Click **Details** on any movie. Notice that ratings are visible, but the rating submission form prompts you to log in.
3. **Testing User Registration & Rating:**
   - Click **Register** in the navbar and create a new account (e.g. `student1` / `password123`).
   - Log in. Notice your username and avatar appear in the top-right navbar.
   - Visit any movie details page, choose your star rating (1–10 stars), type your review, and click **Submit Review**.
   - Notice the community score and average rating calculate automatically in real time!
   - Visit **My Profile** from the navbar dropdown to view your submitted reviews.
   - Go to **Account Settings** to test changing your name, updating your password, and uploading a custom avatar image.
4. **Testing Admin Panel:**
   - Log out, then log in using your admin credentials (`admin` / `admin123`).
   - Notice the golden **Admin Panel** button in the navbar. Click it or visit `http://localhost/sinesine/admin/`.
   - View live dashboard metrics (Total Movies, Registered Users, Total Reviews, and Average Website Rating).
   - Test adding a new movie via **Add New Movie** with poster upload.
   - Test editing and deleting movie entries.
   - Go to **Manage Users** to view all registered users and test enabling or disabling accounts.
   - Go to **Manage Reviews** to moderate and delete inappropriate reviews.

---

## 🔒 Security Measures Implemented
- **Password Security:** Passwords hashed with `PASSWORD_BCRYPT` via `password_hash()` and verified via `password_verify()`.
- **Session Protection:** `session_regenerate_id(true)` called upon login to prevent session fixation.
- **SQL Injection Prevention:** 100% prepared PDO statements with bound parameters across all database queries.
- **Cross-Site Scripting (XSS):** Universal HTML escaping via `sanitize()` (`htmlspecialchars`) across all templates.
- **CSRF Protection:** Cryptographic tokens generated per session and verified on all data-altering POST requests.
- **Role-Based Access Control:** Strict server-side route guards (`requireLogin()`, `requireAdmin()`) preventing unauthorized elevation.
- **Avatar Upload Validation:** MIME-type validation via `getimagesize()`, 2MB size limit, randomized file names, and segregated storage to prevent file execution exploits.

---

## 🎓 Presentation Talking Points
- **Why Native PHP?** Lightweight, beginner-friendly, zero external package overhead (no npm or Composer required), native to XAMPP.
- **Dynamic Average Rating:** No fake ratings. When no reviews exist, movies display "No Ratings" or "Not Yet Rated"; as users submit 1–10 star scores, SQL `AVG()` recalculates the average score in real time.
- **Database Integrity:** Foreign keys enforce `ON DELETE CASCADE` so deleting a movie or a disabled user cleanly purges associated reviews without orphaned records.

