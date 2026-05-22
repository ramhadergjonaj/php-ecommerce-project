USE ecommerce_db;

ALTER TABLE contact_messages
  ADD COLUMN status ENUM('new', 'replied') NOT NULL DEFAULT 'new' AFTER message;

ALTER TABLE contact_messages
  ADD COLUMN admin_reply TEXT NULL AFTER status;

ALTER TABLE contact_messages
  ADD COLUMN replied_at DATETIME NULL AFTER admin_reply;
