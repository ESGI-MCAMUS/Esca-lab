CREATE TABLE "User" (
  "id" SERIAL PRIMARY KEY,
  "username" varchar,
  "email" varchar,
  "password" varchar,
  "firstname" varchar,
  "lastname" varchar,
  "created_at" timestamp,
  "birthdate" date,
  "role" int
);

CREATE TABLE "Role" (
  "id" int PRIMARY KEY,
  "name" varchar
);

CREATE TABLE "Franchise" (
  "id" SERIAL PRIMARY KEY,
  "admin" int
);

CREATE TABLE "Gym" (
  "id" SERIAL PRIMARY KEY,
  "name" varchar,
  "size" bigint,
  "pc" varchar,
  "address" varchar,
  "city" varchar,
  "franchise" int,
  "admin" int
);

CREATE TABLE "Event" (
  "id" SERIAL PRIMARY KEY,
  "gym" int,
  "happen_at" timestamp,
  "description" varchar
);

CREATE TABLE "Comment" (
  "id" SERIAL PRIMARY KEY,
  "report" int,
  "message" varchar,
  "user" int
);

CREATE TABLE "Media" (
  "id" SERIAL PRIMARY KEY,
  "url" varchar,
  "comment" int
);

CREATE TABLE "Reaction" (
  "user" int,
  "comment" int,
  PRIMARY KEY ("user", "comment")
);

CREATE TABLE "Session" (
  "id" SERIAL PRIMARY KEY,
  "user" int,
  "gym" int,
  "happen_at" timestamp
);

CREATE TABLE "Bill" (
  "id" SERIAL PRIMARY KEY,
  "label" varchar,
  "state" bigint,
  "created_at" timestamp,
  "updated_at" timestamp,
  "file" varchar,
  "price" decimal(6,2)
);

CREATE TABLE "Franchise_Bill" (
  "franchise" int,
  "bill" int,
  PRIMARY KEY ("franchise", "bill")
);

CREATE TABLE "Gym_Bill" (
  "gym" int,
  "bill" int,
  PRIMARY KEY ("gym", "bill")
);

CREATE TABLE "Route" (
  "id" SERIAL PRIMARY KEY,
  "opened" smallint,
  "gym" int,
  "opener" int
);

ALTER TABLE "User" ADD FOREIGN KEY ("role") REFERENCES "Role" ("id");

ALTER TABLE "Reaction" ADD FOREIGN KEY ("user") REFERENCES "User" ("id");

ALTER TABLE "Reaction" ADD FOREIGN KEY ("comment") REFERENCES "Comment" ("id");

ALTER TABLE "Media" ADD FOREIGN KEY ("comment") REFERENCES "Comment" ("id");

ALTER TABLE "Session" ADD FOREIGN KEY ("user") REFERENCES "User" ("id");

ALTER TABLE "Session" ADD FOREIGN KEY ("gym") REFERENCES "Gym" ("id");

ALTER TABLE "Franchise" ADD FOREIGN KEY ("admin") REFERENCES "User" ("id");

ALTER TABLE "Event" ADD FOREIGN KEY ("gym") REFERENCES "Gym" ("id");

ALTER TABLE "Route" ADD FOREIGN KEY ("gym") REFERENCES "Gym" ("id");

ALTER TABLE "Gym_Bill" ADD FOREIGN KEY ("bill") REFERENCES "Bill" ("id");

ALTER TABLE "Gym_Bill" ADD FOREIGN KEY ("gym") REFERENCES "Gym" ("id");

ALTER TABLE "Franchise_Bill" ADD FOREIGN KEY ("franchise") REFERENCES "Franchise" ("id");

ALTER TABLE "Franchise_Bill" ADD FOREIGN KEY ("bill") REFERENCES "Bill" ("id");

ALTER TABLE "Route" ADD FOREIGN KEY ("opener") REFERENCES "User" ("id");

ALTER TABLE "Gym" ADD FOREIGN KEY ("admin") REFERENCES "User" ("id");

ALTER TABLE "Comment" ADD FOREIGN KEY ("user") REFERENCES "User" ("id");

