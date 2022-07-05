## Requirements

Make sure you have `sqlite` and `mailhog` on your system

## For Development
### Commands 

symfony:

```shell
symfony server:start
```

#### Optionally you can run the encore service (using tailwindcss)

encore:

```shell
yarn encore dev-server
```

tailwindcss:

```shell
npx tailwindcss -i ./assets/styles/app.css -o ./public/build/app.css --watch
```

### Running

The app should be running on `https://localhost:8000`

Mailhog should run on `http://localhost:8025/`
