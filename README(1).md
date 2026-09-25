
<div align="center">

# 🔍 TruthCheck

### Collaborative Fact-Checking Platform — CSE370 Database Project

**Claims Today, Clarity Tomorrow.**

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![PDO](https://img.shields.io/badge/PDO-Prepared_Statements-3776AB?style=for-the-badge)
![XAMPP](https://img.shields.io/badge/XAMPP-FB7A24?style=for-the-badge&logo=xampp&logoColor=white)

</div>

---

## About

**TruthCheck** is a full-stack fact-checking web app where users submit claims, attach evidence and sources, and verified fact-checkers review them and issue a verdict. Built for **CSE370 (Database Systems)** with a normalized relational schema and a PDO-based PHP backend.

---

## Features

- 🔐 **Authentication** — Sign In / Register in one tabbed card
- 📋 **Claim CRUD** — submit, browse, edit, delete claims (with optional screenshot at submission)
- 🖼️ **Media Uploads** — attach images/videos to a claim
- 🔖 **Source Management** — independent CRUD module
- 📄 **Evidence** — attach evidence to a claim, optionally linked to a source
- ✅ **Fact-Check Verdicts** — `fact_checker` / `admin` issue verdicts, claim status auto-updates
- 💬 **Comments & Reactions** — discuss claims with 👍 ❤️ 😂 😮 😢 reactions
- 🚩 **Admin Flagging** — admins mark claims as reviewed
- 📊 **Dashboard** — claim stats + recent activity
- 🌐 **Public Browsing** — view all claims without submitting one

---

## Tech Stack

- **Backend:** PHP (vanilla)
- **Database:** MySQL (via phpMyAdmin)
- **Data Access:** PDO with prepared statements
- **Server:** Apache (via XAMPP)
- **Frontend:** HTML, CSS, vanilla JS

---

## Database Schema (ERD)

<div align="center">
<img src="SCHEMA_DIAGRAM.png" alt="TruthCheck Entity Relationship Diagram" width="100%">
</div>

- **USER** 1─∞ **CLAIM**
- **CLAIM** 1─∞ **MEDIA / EVIDENCE / COMMENT**
- **CLAIM** 1─1 **FACT_CHECK**
- **SOURCE** 1─∞ **EVIDENCE**
- **USER** 1─∞ **COMMENT / FACT_CHECK** (as verifier)

---

## Getting Started

1. Install **[XAMPP](https://www.apachefriends.org)** and start **Apache** + **MySQL**
2. Copy the `factcheck` folder into `htdocs` (`C:\xampp\htdocs\factcheck` on Windows)
3. Go to `http://localhost/phpmyadmin` → **Import** → select `database.sql` → **Go**
4. Visit `http://localhost/factcheck` → Register → Login → submit a claim

---

## User Roles

| Role | Permissions |
|---|---|
| **user** | Submit claims, browse, comment, add evidence/media |
| **fact_checker** | Above + issue verdicts |
| **admin** | Above + flag claims (set manually via phpMyAdmin) |

---

## Database Migrations

If you already imported `database.sql` before, run these in phpMyAdmin's SQL tab:

```sql
ALTER TABLE COMMENT ADD COLUMN reactions VARCHAR(255) DEFAULT '{}';
ALTER TABLE CLAIM ADD COLUMN flagged TINYINT(1) NOT NULL DEFAULT 0;
```

---

<div align="center">

*⭐ Star this repo if you found it helpful!*

</div>
