## Project
[CVer]: [Meant to be a user-friendly CV creator to appeal to younger generations of future workers through an interactive and simple UI. The application will also allow users to share their CVs and to post templates they created so others may use them as well].
Roles: [admin, user]. Entities: [users, admins, styles, posts, friendships].
 
## Technology stack
- Frontend: HTML, CSS, JavaScript, jQuery, Bootstrap 5, single-page application (SPA)
- Backend: PHP with FlightPHP (REST API), PDO, MySQL
- Local server: XAMPP
 
## Folder structure
- frontend/: css/, js/, views/ (one HTML file per view), assets/
- backend/: routes/, services/, dao/, middleware/, config/, logs/
- docs/: AI_LOG.md and other documentation
 
## Frontend rules
- Each view is in its own file in frontend/views/.
- Use Bootstrap 5 classes; avoid inline styles.
 
## Backend rules
- Requests go: route -> service -> DAO -> database.
- Only services call DAO classes. Routes never call DAO classes directly.
- All SQL uses PDO prepared statements.
- Routes return JSON with correct HTTP status codes.
 
## Conventions
- PHP classes: PascalCase (UserService, UserDao). Files are named after the class.
- JavaScript and PHP variables: camelCase. Database tables and columns: snake_case.
 
## Rules for AI assistants
- Do not add new libraries or frameworks without asking me first.
- Do not change the database schema without asking me first.
- Never put passwords, keys or secrets in code; they belong in backend/config/config.php,
  which is not committed. Update config.example.php instead.
- Do not edit files in backend/logs/.
- Make small changes and explain what you changed and why.
