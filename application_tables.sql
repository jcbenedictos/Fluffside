-- ============================================================
-- FLUFFSIDE — Application Detail Tables
-- Paste this into phpMyAdmin > fluffside_db > SQL tab
-- Run AFTER fluffside_schema.sql (tbl_applications must exist)
-- ============================================================

USE fluffside_db;

-- ── Shared applicant info (same fields on both forms) ─────────
-- tbl_applications already stores the high-level tracking row.
-- tbl_app_applicant stores the personal details submitted with the form.

CREATE TABLE IF NOT EXISTS tbl_app_applicant (
    app_id          VARCHAR(20)  PRIMARY KEY,
    -- Personal
    first_name      VARCHAR(60)  NOT NULL,
    last_name       VARCHAR(60)  NOT NULL,
    birthdate       DATE         DEFAULT NULL,
    pronouns        VARCHAR(40)  DEFAULT NULL,   -- he/him, she/her, they/them, others
    pronouns_other  VARCHAR(80)  DEFAULT NULL,
    -- Contact
    email           VARCHAR(100) NOT NULL,
    phone           VARCHAR(20)  NOT NULL,
    address         TEXT         NOT NULL,
    social_media    VARCHAR(200) DEFAULT NULL,
    -- Employment
    occupation      VARCHAR(100) DEFAULT NULL,
    company         VARCHAR(150) DEFAULT NULL,
    civil_status    VARCHAR(40)  DEFAULT NULL,   -- single, married, etc.
    civil_status_other VARCHAR(80) DEFAULT NULL,
    -- Background
    prompt_src      VARCHAR(60)  DEFAULT NULL,   -- how they found Fluffside
    prompt_src_other VARCHAR(80) DEFAULT NULL,
    adopted_before  VARCHAR(10)  DEFAULT NULL,   -- yes / no
    -- Alternate / emergency contact
    alt_first_name  VARCHAR(60)  DEFAULT NULL,
    alt_last_name   VARCHAR(60)  DEFAULT NULL,
    alt_relationship VARCHAR(60) DEFAULT NULL,
    alt_phone       VARCHAR(20)  DEFAULT NULL,
    alt_email       VARCHAR(100) DEFAULT NULL,
    -- Housing
    building_type   VARCHAR(60)  DEFAULT NULL,   -- house, condo, apartment, etc.
    building_type_other VARCHAR(80) DEFAULT NULL,
    do_rent         VARCHAR(10)  DEFAULT NULL,   -- yes / no
    live_with       VARCHAR(200) DEFAULT NULL,   -- comma-separated: alone, parents, partner, etc.
    live_with_other VARCHAR(100) DEFAULT NULL,
    -- Household
    allergic        VARCHAR(10)  DEFAULT NULL,   -- yes / no
    household_support VARCHAR(10) DEFAULT NULL,  -- yes / no
    support_explain TEXT         DEFAULT NULL,
    other_pets      VARCHAR(10)  DEFAULT NULL,   -- yes / no
    past_pets       VARCHAR(10)  DEFAULT NULL,   -- yes / no
    near_road       VARCHAR(10)  DEFAULT NULL,   -- yes / no
    -- Open-ended care plan answers
    move_plan       TEXT         DEFAULT NULL,
    care_plan       TEXT         DEFAULT NULL,
    financial_plan  TEXT         DEFAULT NULL,
    emergency_plan  TEXT         DEFAULT NULL,
    hours_alone     TEXT         DEFAULT NULL,
    -- File uploads (stored path or filename)
    valid_id_file   VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (app_id) REFERENCES tbl_applications(app_id) ON DELETE CASCADE
);

-- ── Adoption-only fields ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS tbl_app_adoption (
    app_id              VARCHAR(20) PRIMARY KEY,
    -- Preferred interview schedule
    interview_date      DATE        DEFAULT NULL,
    interview_time      TIME        DEFAULT NULL,
    same_month          VARCHAR(10) DEFAULT NULL,  -- yes / no
    FOREIGN KEY (app_id) REFERENCES tbl_applications(app_id) ON DELETE CASCADE
);

-- ── Foster-only fields ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS tbl_app_foster (
    app_id              VARCHAR(20) PRIMARY KEY,
    foster_duration     VARCHAR(60) DEFAULT NULL,  -- short-term, long-term, etc.
    shelter_visit       VARCHAR(10) DEFAULT NULL,  -- yes / no (visited shelter before)
    FOREIGN KEY (app_id) REFERENCES tbl_applications(app_id) ON DELETE CASCADE
);

-- ── Indexes ───────────────────────────────────────────────────
CREATE INDEX IF NOT EXISTS idx_app_applicant_email ON tbl_app_applicant(email);

<!-- for clean comments -->