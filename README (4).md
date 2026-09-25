<div align="center">

# 🔍 TruthCheck

### Collaborative Fact-Checking Platform — CSE370 Database Project

**Claims Today, Clarity Tomorrow.**

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![PDO](https://img.shields.io/badge/PDO-Prepared_Statements-3776AB?style=for-the-badge)
![XAMPP](https://img.shields.io/badge/XAMPP-FB7A24?style=for-the-badge&logo=xampp&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

</div>

---

## 📌 About

**TruthCheck** হলো একটা full-stack fact-checking web application, যেখানে ইউজাররা claim জমা দিতে পারেন, evidence ও source যুক্ত করতে পারেন, এবং verified `fact_checker`-রা সেই claim যাচাই করে verdict দেন। Admin claim গুলোকে flag/review করতে পারেন, আর ইউজাররা একে অপরের সাথে comment ও emoji reaction-এর মাধ্যমে discuss করতে পারেন।

এই প্রজেক্টটা **CSE370 (Database Systems)** কোর্সের জন্য তৈরি — একটা সম্পূর্ণ **normalized relational schema**, **PDO-based PHP backend**, আর role-based access control নিয়ে।

---

## 🗂️ Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Database Schema (ERD)](#-database-schema-erd)
- [Project Structure](#-project-structure)
- [Getting Started](#-getting-started)
- [User Roles](#-user-roles)
- [Testing Guide](#-testing-guide)
- [Database Migrations](#-database-migrations)
- [Contributors](#-contributors)

---

## ✨ Features

| Module | Description |
|---|---|
| 🔐 **Authentication** | Single-card, tab-style Sign In / Register (no page reload) |
| 📋 **Claim Management** | Full CRUD — submit, browse, edit, delete claims, with optional screenshot upload at submission |
| 🖼️ **Media Management** | Upload & link images/videos (jpg, png, gif, webp, mp4) directly to a claim |
| 🔖 **Source Management** | Independent module to manage trusted sources |
| 📄 **Evidence Management** | Attach evidence to a claim, optionally linked to a source |
| ✅ **Fact-Check Verdicts** | `fact_checker` / `admin` roles issue verdicts — claim status auto-updates |
| 💬 **Comments & Reactions** | Discuss claims with threaded comments + 👍 ❤️ 😂 😮 😢 emoji reactions |
| 🚩 **Admin Flagging** | Admins mark claims as reviewed, independent of the fact-check verdict |
| 📊 **Dashboard** | At-a-glance stats — total / pending / verified / rejected claims, your submissions, recent activity |
| 🌐 **Public Browsing** | Anyone can browse all claims without submitting one themselves |

---

## 🛠️ Tech Stack

- **Backend:** PHP (vanilla, no framework)
- **Database:** MySQL (via phpMyAdmin)
- **Data Access:** PDO with prepared statements
- **Server:** Apache (via XAMPP)
- **Frontend:** HTML, CSS (custom, Poppins + Hind Siliguri fonts), vanilla JS

---

## 🗃️ Database Schema (ERD)

<div align="center">
<img src="SCHEMA_DIAGRAM.png" alt="TruthCheck Entity Relationship Diagram" width="100%">
</div>

**6 core entities**, fully normalized:

- **USER** `1 ─── ∞` **CLAIM** — একজন user অনেক claim submit করতে পারেন
- **CLAIM** `1 ─── ∞` **MEDIA / EVIDENCE / COMMENT** — একটা claim-এর অনেক media, evidence, comment থাকতে পারে
- **CLAIM** `1 ─── 1` **FACT_CHECK** — প্রতিটা claim-এর সর্বোচ্চ একটা verdict
- **SOURCE** `1 ─── ∞` **EVIDENCE** — একটা source একাধিক evidence-কে backup করতে পারে
- **USER** `1 ─── ∞` **COMMENT / FACT_CHECK (as verifier)**

> 💡 ERD generated with [drawSQL](https://drawsql.app)

---

## 📁 Project Structure

```
factcheck/
├── index.php              # Entry point — redirects to dashboard or login
├── login.php              # Sign In / Register (tabbed, single card)
├── logout.php             # Session destroy
├── dashboard.php          # Post-login landing page with stats
├── database.sql           # Full schema — run this first
├── migration_add_reactions.sql   # Adds COMMENT.reactions column
├── migration_add_flagged.sql     # Adds CLAIM.flagged column
├── config/
│   └── db.php              # PDO connection
├── includes/
│   ├── auth_check.php      # Session guard
│   ├── header.php
│   └── sidebar.php
├── claims/                 # Claim CRUD + Details page (Evidence/Media/Comment/Fact-check)
├── source/                 # Source CRUD
└── uploads/                 # Uploaded screenshots/media (needs write permission)
```

---

## 🚀 Getting Started

### প্রয়োজনীয়তা
- **[XAMPP](https://www.apachefriends.org)** — PHP + MySQL + phpMyAdmin একসাথে পাওয়া যায়

### ধাপে ধাপে

1. **XAMPP চালু করুন** — Control Panel থেকে `Apache` ও `MySQL` দুটোই Start করুন

2. **প্রজেক্ট কপি করুন** পুরো `factcheck` ফোল্ডার XAMPP-এর `htdocs`-এ রাখুন:
   ```
   Windows: C:\xampp\htdocs\factcheck
   Mac:     /Applications/XAMPP/htdocs/factcheck
   ```

3. **Database তৈরি করুন**
   - ব্রাউজারে যান → `http://localhost/phpmyadmin`
   - **Import** ট্যাবে ক্লিক করে `database.sql` সিলেক্ট করে **Go** চাপুন

4. **অ্যাপ চালান**
   - `http://localhost/factcheck` এ যান
   - Register করে অ্যাকাউন্ট বানান → Login করুন → Claim submit করে দেখুন 🎉

---

## 👥 User Roles

| Role | Permissions |
|---|---|
| **user** | Claim submit, browse, comment, evidence/media যোগ করতে পারবে |
| **fact_checker** | উপরের সবকিছু + verdict দিতে পারবে (claim status বদলে যায়) |
| **admin** | উপরের সবকিছু + claim flag করতে পারবে (phpMyAdmin দিয়ে manually সেট করতে হয়) |

> নতুন signup করা সবাই ডিফল্টভাবে `user` role পায়। `admin` বানাতে হলে phpMyAdmin → `USER` টেবিল → `role` কলাম manually `admin` করে দিন।

---

## 🧪 Testing Guide

1. দুটো account বানান — একটা normal `user`, একটা phpMyAdmin দিয়ে `fact_checker`
2. `user` দিয়ে Login → একটা Claim submit করুন (screenshot সহ) → Details পেজে Evidence/Comment যোগ করুন
3. `fact_checker` দিয়ে Login → একই Claim-এ গিয়ে Verdict দিন → status বদলে যাওয়া দেখুন
4. Comment-এ emoji reaction ক্লিক করে দেখুন
5. `admin` role সেট করে Claim flag করে দেখুন → Sidebar-এর "Flagged Claims" এ চেক করুন

---

## 🔄 Database Migrations

আগে থেকে `database.sql` import করা থাকলে, নতুন ফিচারের জন্য দুইটা migration লাগবে (phpMyAdmin → `factcheck_db` → **SQL** ট্যাবে paste করে Go):

```sql
-- 1) Emoji reactions on comments
ALTER TABLE COMMENT ADD COLUMN reactions VARCHAR(255) DEFAULT '{}';

-- 2) Admin flag/review system
ALTER TABLE CLAIM ADD COLUMN flagged TINYINT(1) NOT NULL DEFAULT 0;
```

(অথবা `migration_add_reactions.sql` ও `migration_add_flagged.sql` ফাইল দুইটা সরাসরি রান করুন)

---

## 🙌 Contributors

Built for **CSE370 — Database Systems** coursework.

<div align="center">

*⭐ Star this repo if you found it helpful!*

</div>
