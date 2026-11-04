# TalentFlow ERP &mdash; Celebrity & Talent Management Enterprise Platform

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12" />
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2" />
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL 8" />
  <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap 5" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-CDN-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" />
  <img src="https://img.shields.io/badge/Tests-58%20Assertions%20Passing-brightgreen?style=for-the-badge" alt="Tests" />
  <img src="https://img.shields.io/badge/License-Proprietary-blue?style=for-the-badge" alt="License" />
</p>

---

## 🌟 Executive Summary

**TalentFlow ERP** is an enterprise-grade Celebrity, Artist, and Talent Management Enterprise Resource Planning (ERP) platform designed for premier talent agencies, artist management firms, casting houses, and entertainment networks.

Built from the ground up as a **Modular Monolith** on **Laravel 12 (PHP 8.2+)**, the system automates the complete operational lifecycle: from lead acquisition, client proposals, and dynamic multi-artist rate cards, to conflict-free booking calendars, campaign production milestones, legal contracts with automated expiry detection, GST tax invoicing, and 20% agency commission calculations with TDS deductions.

---

## 🏛️ System Architecture & Blueprint

```
+----------------------------------------------------------------------------------------------------+
|                                     TALENTFLOW ERP ECOSYSTEM                                       |
+----------------------------------------------------------------------------------------------------+
|                                                                                                    |
|  [ CRM & PIPELINE ]          [ TALENT ROSTER ]           [ COMMERCIALS ]          [ WORKFLOWS ]    |
|  * Brands & Clients          * Artist Master Profiles    * Quotation Builder      * Projects & PM  |
|  * Sales Pipeline            * Rate Cards & Deliverables * GST Tax Engine (18%)   * Milestones     |
|  * Deal Qualification        * Social Reach & Metrics    * PDF Quotation Export   * Tasks Allocation|
|  * Activity Logs             * Manager Contacts          * 1-Click Conversion     * Booking Calendar|
|                                                                                                    |
|  [ LEGAL & DMS ]             [ FINANCE & EXPENSES ]      [ ANALYTICS & BI ]       [ RBAC & ADMIN ] |
|  * Digital Contracts         * GST Invoicing (INV-...)   * Project Margin Report  * Spatie 8 Roles |
|  * 30-Day Expiry Alerts      * Partial Payment Receipts  * Gross Margin Warnings  * Departments    |
|  * Secure Document Vault     * Expense Reimbursements    * Revenue Forecasting    * 1-Click Demo   |
|  * Audit Access Logs         * 20% Commission Deductions * Celebrity Demand Index * System Config  |
|                              * TDS Withholding & Payouts * General Ledger Ledger                   |
+----------------------------------------------------------------------------------------------------+
```

### Key Architectural Metrics:
- **38 Database Migrations** with strict foreign key constraints and indexed lookup codes.
- **32 Eloquent Models** with virtual accessors, relationship graphs, and scopes.
- **19 Database Seeders** generating realistic enterprise talent, campaigns, quotations, and bookings.
- **10 Application Domain Services** encapsulating business algorithms and decoupling domain logic from controllers.
- **117 RESTful HTTP Routes** guarded by enterprise role-based access control.
- **11 Automated Test Suites (58 Assertions)** covering financial calculators, conflict detectors, and HTTP endpoints.

---

## 🚀 Key Modules & Capabilities

### 1. 👥 CRM & Pipeline Management
- **Brand & Client Directory**: Manage client corporations, corporate billing addresses, and GSTIN tax numbers.
- **Lead Funnel Tracking**: Stage leads through `new` $\to$ `contacted` $\to$ `qualified` $\to$ `proposal` $\to$ `won`/`lost`.
- **CRM Activity Timeline**: Log calls, brand briefings, client meetings, follow-ups, and negotiation notes.

### 2. 🎭 Celebrity & Talent Roster
- **Artist Profiles**: Stage names, legal names, biographies, base cities, and categories (Actors, Playback Singers, Musicians, Athletes, Creators).
- **Multi-Rate Cards**: Standard day-rate fee structures for photo shoots, endorsements, video commercials, and stage appearances.
- **Social Reach Metrics**: Track follower counts and engagement rates across Instagram, YouTube, and digital platforms.
- **Confidential Management Data**: Secure storage of direct manager contacts, PAN numbers, and talent contracts.

### 3. 📑 Commercial Quotations & Proposals
- **Dynamic Proposal Builder**: Multi-artist line-item breakdown with real-time tax calculation.
- **Automated Tax Engine**: Subtotal, agency fee, logistical expenses, CGST (9%), SGST (9%), or IGST (18%).
- **PDF Proposal Export**: Clean printable PDF proposals via Barryvdh DomPDF.
- **1-Click Project Conversion**: Convert accepted commercial quotations into active production projects seamlessly with duplicate prevention.

### 4. 📅 Master Booking Calendar & Scheduling
- **Conflict Detection Engine**: Analyzes overlapping dates, shoot schedules, and tentative reservations to prevent double-booking.
- **FullCalendar Integration**: Interactive month, week, and day grid views with real-time talent filtering.
- **Independent Layout Scrolling**: The fixed sidebar stays stationary while the calendar and schedule canvas scroll independently.

### 5. 🎬 Campaigns & Project Management
- **Milestone & Task Tracking**: Assign tasks, monitor due dates, assignees, and urgent/high/medium priorities.
- **Financial KPI Dashboard**: Real-time project budget vs. invoiced revenue vs. expenses vs. net margin.
- **15% Margin Risk Flag**: Projects with gross profit margins below 15% automatically receive high-visibility alerts.

### 6. 📜 Legal, Compliance & Secure DMS
- **Digital Contracts**: Store contract code, start/end dates, scope, and validity status (`active`, `expired`, `terminated`).
- **30-Day Automated Expiry Detection**: Identifies expiring agreements and notifies agency managers proactively.
- **Document Management System (DMS)**: Encrypted file repository categorized by identity, financial, legal, and operational documents.

### 7. 💰 Finance, Invoices & Talent Payouts
- **GST Tax Invoices**: Sequential invoice numbering (`INV-YYYY-XXXX`), partial payment receipts, and balance due tracking.
- **Production Claims & Expenses**: Expense reimbursement workflow with double-posting protection.
- **20% Agency Commission Engine**: Automatically calculates the agency's 20% commission cut, calculates TDS withholdings, and stages net payable balances.
- **Bank Disbursement Logging**: Records bank UTR transaction references upon final settlement.

### 8. 📊 Executive Analytics & Forecasts
- **Revenue Pipeline Forecasting**: Evaluates weighted deal stages to project expected quarterly cashflow.
- **Artist Demand Heatmap**: Ranks talent based on quotation frequency, bookings, and revenue generation.
- **Financial General Ledger**: Aggregates all project incomes and expenditures into an executive audit log.

---

## 🛠️ Technology Stack

| Layer | Technology |
|---|---|
| **Backend Framework** | Laravel 12.71.0 (PHP 8.2+) |
| **Relational Database** | MySQL 8.0+ (InnoDB, UTF8mb4) |
| **Authentication & RBAC**| Spatie Laravel-Permission 6.25.0 |
| **PDF Rendering** | Barryvdh Laravel-DomPDF 3.1.2 |
| **Frontend Foundation** | Bootstrap 5.3.3 + Tailwind CSS CDN (`corePlugins: { preflight: false }`) |
| **Reactivity & Components**| Alpine.js 3.14.8 |
| **Calendar Engine** | FullCalendar 6.1.15 |
| **Charts & Visualizations** | Chart.js 4.4.2 |

---

## 💻 Local Installation & Setup

### Prerequisites
* PHP >= 8.2 (with `pdo_mysql`, `curl`, `mbstring`, `openssl`, `xml`, `zip` extensions)
* Composer 2.x
* MySQL 8.x or MariaDB
* Apache / Nginx / XAMPP / Laravel Herd

### Step-by-Step Installation

```bash
# 1. Clone the repository
git clone https://github.com/mesourish/talentflow-erp.git
cd talentflow-erp

# 2. Install composer dependencies
composer install

# 3. Configure environment file
cp .env.example .env

# 4. Generate application encryption key
php artisan key:generate

# 5. Configure your database in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=talentflow_erp
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Run database migrations with comprehensive seed data
php artisan migrate:fresh --seed

# 7. Create storage symbolic link
php artisan storage:link

# 8. Start development server (or run under XAMPP / Herd)
php artisan serve
```

---

## 🔑 Demo User Accounts & Roles

The seed data provides 8 pre-configured enterprise roles. You can log in using the credentials below, or click any of the **1-Click Quick Demo Login** buttons on the login page:

| Role | Email | Password | Access Scope |
|---|---|---|---|
| **Super Admin** | `admin@demo.test` | `password` | Full root system access, user management, ledger |
| **Management** | `management@demo.test` | `password` | Executive revenue forecast, talent demand, margins |
| **Finance Manager** | `finance@demo.test` | `password` | Client invoices, claims approval, payouts, TDS |
| **Booking Coordinator** | `booking@demo.test` | `password` | Full calendar scheduling, venue call sheets |
| **Project Manager** | `pm@demo.test` | `password` | Campaign deliverables, tasks, milestones, expenses |
| **Sales Manager** | `sales@demo.test` | `password` | CRM pipeline, leads, quotation approvals |
| **CRM Executive** | `crm@demo.test` | `password` | Client onboarding, communication activity logs |
| **Document Manager** | `legal@demo.test` | `password` | Digital contracts, DMS archive, audit access |

---

## 🧪 Automated Testing Suite

TalentFlow ERP includes feature and unit test suites:
```bash
php artisan test
```

### Test Coverage Highlights:
- ✅ `QuotationCalculator`: Accurate deliverable subtotal, tax calculations (18% GST), and total pricing.
- ✅ `CommissionCalculator`: Automatic 20% agency commission deduction and TDS tax withholding.
- ✅ `BookingConflictDetector`: Precise identification of scheduling collisions.
- ✅ `InvoicePartialPayment`: Accurate decrement of `balance_due` and receipt recording.
- ✅ `QuotationConversionService`: Safe migration into active project without duplicate conversion.
- ✅ `ClaimApprovalService`: Expense posting with double-posting safeguards.
- ✅ `ContractExpiryService`: Accurate identification of expiring agreements within 30 days.
- ✅ `HttpEndpointsTest`: Complete routing verification across all 26 core admin modules.

---

## 🌿 Branch Management Strategy

This repository follows a structured Git branching model:

```
main (Production / Stable Releases - v1.0.0)
 │
 └── develop (Active Development & Staging)
      │
      ├── feature/crm-pipeline
      ├── feature/booking-calendar
      ├── feature/finance-commissions
      └── feature/dms-security
```

* `main`: Protected production branch containing stable, fully tested, tagged release code.
* `develop`: Integration branch for active features before production deployment.
* `feature/*`: Dedicated branches for distinct modules or enhancements.

---

## 📄 License & Author

* **Project**: TalentFlow ERP &mdash; Hybrid Celebrity & Talent Management System
* **Author**: **Sourish Mondal** ([@mesourish](https://github.com/mesourish))
* **Version**: `1.0.0`
* **Release Date**: November 2026
* **License**: Open Source for portfolio & enterprise demonstration.
