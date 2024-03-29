# Forummie 
Forummie is a user-friendly platform to provide meaningful community interactions and discussions.

## Tech stack 
- Backend service with `Laravel` (version 10)
- Client side with `Vue 3` (composition api with script setup)
- `Inertia js` as adapter
  
![index page of forum](/public/screenshot-1.png)

![single thread](/public/screenshot-2.png)

## Features 
- markdown support
- syntax highlighting
- database notifications
- 1 level of nested reply, like/unlike functionality
- admin panel
- and more


## Installation and Setup

- clone this repo with:

```bash
git clone "https://github.com/kgsint/forum.git"
```

- install PHP dependencies:

```bash
composer install
```

- Set up local database called `forummie`
- To setup the application (setup `env` file and install frontend dependencies) by running:
```
composer setup
```
## Commands

Command | Description
--- | ---
`php artisan serve` | start local development server
`npm run dev` | Build and watch for changes, hot reload and bundle CSS and JS files
`php artisan test` | Run the entire test suite
`php artisan migrate:fresh --seed` | Refresh the database and seed

For Admin login,
```
email - admin@gmail.com
password - password
```

## License 
Forummie is under [MIT license](/LICENSE.md)
