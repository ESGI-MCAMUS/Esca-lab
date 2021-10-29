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
  "price" double
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
  "opened" tinyint,
  "gym" int,
  "opener" int
);

ALTER TABLE "Role" ADD FOREIGN KEY ("id") REFERENCES "User" ("role");

ALTER TABLE "User" ADD FOREIGN KEY ("id") REFERENCES "Reaction" ("user");

ALTER TABLE "Comment" ADD FOREIGN KEY ("id") REFERENCES "Reaction" ("comment");

ALTER TABLE "Comment" ADD FOREIGN KEY ("id") REFERENCES "Media" ("comment");

ALTER TABLE "User" ADD FOREIGN KEY ("id") REFERENCES "Session" ("user");

ALTER TABLE "Gym" ADD FOREIGN KEY ("id") REFERENCES "Session" ("gym");

ALTER TABLE "User" ADD FOREIGN KEY ("id") REFERENCES "Franchise" ("admin");

ALTER TABLE "Gym" ADD FOREIGN KEY ("id") REFERENCES "Event" ("gym");

ALTER TABLE "Gym" ADD FOREIGN KEY ("id") REFERENCES "Route" ("gym");

ALTER TABLE "Bill" ADD FOREIGN KEY ("id") REFERENCES "Gym_Bill" ("bill");

ALTER TABLE "Gym" ADD FOREIGN KEY ("id") REFERENCES "Gym_Bill" ("gym");

ALTER TABLE "Franchise" ADD FOREIGN KEY ("id") REFERENCES "Franchise_Bill" ("franchise");

ALTER TABLE "Bill" ADD FOREIGN KEY ("id") REFERENCES "Franchise_Bill" ("bill");

ALTER TABLE "User" ADD FOREIGN KEY ("id") REFERENCES "Route" ("opener");

ALTER TABLE "User" ADD FOREIGN KEY ("id") REFERENCES "Gym" ("admin");

ALTER TABLE "User" ADD FOREIGN KEY ("id") REFERENCES "Comment" ("user");

