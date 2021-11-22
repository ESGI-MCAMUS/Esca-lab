# Esca'Lab

## Specification
The spec sheet can be found on this [Google Docs](https://docs.google.com/document/d/1JHE1V04u1KaTrA9mP1qEWvUhGMF9QsCDrCYC4eKbeVU/edit?usp=sharing)

## Start the project

```bash
docker-compose build --pull --no-cache # Start for the first time
docker-compose up -d
```

## Accessing the project

http://127.0.0.1

## Database Env

```
DATABASE_URL="postgresql://postgres:password@db:5432/db?serverVersion=13&charset=utf8"
```

## Open Docker in terminal 

```bash
docker exec -it docker_php_1 bash
```
