CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "associations"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "phone" varchar,
  "address" varchar,
  "description" text,
  "creation_date" date not null,
  "logo_url" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "associations_email_unique" on "associations"("email");
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "first_name" varchar not null,
  "last_name" varchar not null,
  "email" varchar not null,
  "phone" varchar,
  "address" varchar,
  "user_type" varchar check("user_type" in('individual', 'association', 'admin')) not null,
  "email_verified_at" datetime,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "password" varchar not null
);
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");

INSERT INTO migrations VALUES(1,'2025_04_24_170337_create_associations_table',1);
INSERT INTO migrations VALUES(2,'2025_04_24_170622_create_users_table',1);
INSERT INTO migrations VALUES(3,'2025_04_24_171629_remove_bio_from_users_table',2);
INSERT INTO migrations VALUES(4,'2025_04_24_171923_remove_profile_picture_from_users_table',3);
INSERT INTO migrations VALUES(5,'2025_04_24_172409_create_sessions_table',4);
INSERT INTO migrations VALUES(6,'2025_04_24_173357_add_password_to_users_table',5);
INSERT INTO migrations VALUES(7,'2025_04_24_173530_remove_password_hash_from_users_table',6);
