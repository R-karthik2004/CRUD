# Role-Based CRUD Management System  

A complete role-based web application built using **PHP, MySQL, HTML, CSS, Bootstrap, and SQL**, designed for managing users, products, and communication workflows with different access permissions. The system includes secure login/register functionality, admin-level controls, and a user-friendly interactive UI.

---

## 🧩 Project Overview

This project provides a foundational structure for applications requiring user authentication, multiple roles, and CRUD operations. It contains two modules: **User Module** and **Admin Module**, each with dedicated access and functionality based on assigned roles.

---

## 🔐 Authentication & User Access

- Secure Login and Registration system.
- New user account details are stored in the database.
- Users are redirected based on their role:
  - **Admin** ➝ Admin Dashboard
  - **User** ➝ E-commerce UI Page
- Session-based login and logout.
- Role change feature available only for admin.

---

## 👤 User Module (Features)

Once a user logs in, they are redirected to a simple e-commerce styled interface. The functionalities include:

- View available products.
- Auto-display logged-in user details.
- Update profile information.
- Access a **Contact Us** form.
- Submit queries, which are emailed directly to the admin using PHP Mail.

> This module acts as a front-facing demo UI for e-commerce functionality (view only).

---

## 🛠️ Admin Module (Features)

Admin has full access to manage the entire system. Available features include:

### **User Management**
- View all registered users.
- Update user details.
- Delete users from the system permanently.
- Change user roles (User → Admin).

### **Product Management**
- View all available products.
- Insert new products using form-based input.
- Update product details.
- Delete products.

### **Contact Management**
- View all messages submitted by users.
- Reply to users via email through the admin dashboard.

---

## 📧 Email Integration

The system uses **PHP mail() function** to send:

- User queries from the Contact Form
- Admin reply messages

---

## 🏗️ Technologies Used

| Category        | Technologies |
|----------------|-------------|
| Frontend       | HTML, CSS, Bootstrap |
| Backend        | PHP |
| Database       | MySQL |
| CRUD System    | SQL Queries |
| Email System   | PHP Mail() |
| Authentication | PHP Sessions |

---



