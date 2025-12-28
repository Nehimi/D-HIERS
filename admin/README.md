# D-HEIRS Admin Portal

## Project Overview
The **D-HEIRS (Digital Health Extension Integrated Reporting System) Admin Portal** is the centralized control hub for managing the digital health reporting ecosystem. It provides system administrators with powerful tools to oversee user access, monitor system health, and configure administrative units (Kebeles), ensuring the smooth operation of health extension services.

## Technology Stack
*   **Backend**: PHP (Native)
*   **Database**: MySQL
*   **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
*   **Styling**: Custom CSS (Responsive Design)

## Functional Requirements
This module implements the following 5 key functional requirements:

### 1. Interactive Dashboard & Analytics
**File:** `dashboard.php`
*   Provides a comprehensive real-time overview of the system status.
*   Displays critical metrics such as **Total Users**, **Active HEWs** (Health Extension Workers), and **Daily Report Counts**.
*   Visualizes system health and storage usage to ensure operational stability.

### 2. User Management System
**File:** `user_management.php`
*   Allows administrators to view all registered users in a tabular format.
*   Displays user details including **Role** (HEW, Coordinator), **Kebele**, and current **Status**.
*   Includes filtering and search capabilities to quickly locate specific staff members.

### 3. Secure Account Creation
**File:** `create_account.php`
*   Provides a standardized form for registering new system users.
*   Enforces role creation (e.g., assigning a user as an HEW or Supervisor) and mapping them to specific Kebeles.
*   Ensures secure onboarding of personnel into the D-HEIRS ecosystem.

### 4. Audit Logging System
**File:** `audit_logs.php`
*   Maintains a secure, immutable record of critical system actions.
*   Tracks login events, data modifications, and setting changes for security and accountability.
*   Helps in monitoring suspicious activities and verifying user actions.

### 5. Administrative Configuration
**Files:** `kebele_config.php`, `system_reports.php`
*   **Kebele Config**: Manages the list of Kebeles (administrative units), ensuring accurate geographical mapping for reports.
*   **System Reports**: Generates high-level summaries of system usage and health data for administrative decision-making.

## API Architecture
The `api/` folder is critical for the application's responsiveness. It serves as the middleware that handles asynchronous requests (AJAX) from the frontend, allowing the dashboard to update data without requiring a full page reload.

### **how to use it**
These endpoints are typically consumed via JavaScript using the `fetch` API.
**Example Usage:**
```javascript
fetch('../../api/dashboard_stats.php')
  .then(response => response.json())
  .then(data => updateDashboard(data));
```

### **Key Endpoints**
*   **`dashboard_stats.php`**: Returns JSON data for real-time counters (active users, total reports).
*   **`user_status.php`**: Handles toggle requests to change a user's status (Active/Inactive) securely.
*   **`search_users.php`**: Processes dynamic search queries for the user management table.
*   **`reset_password.php`**: Manages the logic for secure password recovery.

## Setup & Installation
1.  Ensure **XAMPP** or **WAMP** is installed and running.
2.  Import the database schema (located in `sql/`) into your MySQL server.
3.  Configure `../../dataBaseConnection.php` with your local database credentials.
4.  Navigate to `admin/php/dashboard.php` in your browser.
5.  Log in using administrative credentials.

---