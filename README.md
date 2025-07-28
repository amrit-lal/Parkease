
# 🅿️ Paspark – Parking Management System

Paspark is a complete web-based Parking Management System built using **PHP**, **MySQL**, and **Bootstrap**. It offers separate modules for **Users**, **Admins**, and **Guards**, ensuring smooth parking slot booking, management, and verification.


## ✅ Features

### 👤 User Panel
- User Registration & Login
- View available parking slots
- Book a parking slot (date, time, category)
- View Booking Status: Pending / Approved / Rejected
- Unique Booking ID after Admin Approval

### 🛠️ Admin Panel
- Admin Login
- Create/Edit/Delete Parking Slots (with date, time, location)
- View and Manage Booking Requests
- Approve/Reject Bookings
- View Booking Stats: Total, Pending, Approved, Rejected
- Auto-update slot availability

### 🛡️ Guard Panel
- Guard Login
- Scan/Enter Booking ID
- Verify Booking and Mark Slot as Occupied

---

## 🗂️ Project Structure

```bash
/ (root)
├── index.php               # Home / Landing Page
├── dashboard.php           # User Dashboard
├── admin_dashboard.php     # Admin Dashboard
├── guard_login.php         # Guard Login
├── config.php              # Database Connection (assumed)
├── css/                    # Stylesheets
├── js/                     # JavaScript Files
├── images/                 # Project Images
└── sql/                    # Database SQL Dump (if any)
```

---

## 🛠️ Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/amrit-lal/parkease.git
   ```

2. **Setup Environment**
   - Install [XAMPP](https://www.apachefriends.org/index.html) / [WAMP](https://www.wampserver.com/) / LAMP
   - Copy the project folder to `htdocs` (XAMPP) or `www` (WAMP)

3. **Database Configuration**
   - Import the SQL file (if provided) into **phpMyAdmin**
   - Update your database credentials in `config.php`:
     ```php
     $host = "localhost";
     $user = "root";
     $pass = "";
     $db   = "parkease";
     ```

4. **Run the Project**
   - Open your browser and go to:
     ```
     http://localhost/paspark/index.php
     ```

---

## ▶️ Usage

- **Users** can register/login and book slots
- **Admins** manage slot availability and booking approvals
- **Guards** validate booking IDs on arrival

---

## 💻 Technologies Used

| Technology     | Purpose                          |
|----------------|----------------------------------|
| PHP            | Backend Scripting                |
| MySQL          | Database                         |
| HTML/CSS       | UI Layout & Styling              |
| Bootstrap 5    | Responsive Design                |
| JavaScript     | Frontend Interactions            |
| Font Awesome   | Icons                            |

---

## 👨‍💻 Author

**Amrit Lal**  
🔗 GitHub: [github.com/amrit-lal](https://github.com/amrit-lal)  

---

## 📄 License

This project is licensed under the [MIT License](LICENSE).
