-- Run this if you already ran migration_add_reactions.sql before (so you don't get
-- a "column already exists" error by re-running the whole old file).
-- phpMyAdmin > factcheck_db > SQL tab > paste this > Go

ALTER TABLE CLAIM ADD COLUMN flagged TINYINT(1) NOT NULL DEFAULT 0;
