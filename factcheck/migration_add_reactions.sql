-- Run this ONLY if you already imported database.sql before (existing database).
-- phpMyAdmin > factcheck_db > SQL tab > paste this > Go
-- This just adds one new column for emoji reactions on comments, nothing else changes.

ALTER TABLE COMMENT ADD COLUMN reactions VARCHAR(255) DEFAULT '{}';
