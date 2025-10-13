# Equipment Maintenance Tracker

A PHP web application to track equipment maintenance, with login system, maintenance records, and archive/unarchive functionality.

## Features
- User login/register/logout
- Add, edit, archive/unarchive equipment
- View and add maintenance records per equipment
- Admin configuration for theme and columns
- **Uses [Smarty](https://www.smarty.net/) template engine for theming**
- **Client-side table sorting with [tablesorter](https://mottie.github.io/tablesorter/)**
- Dockerized setup for easy deployment

## Setup

1. **Clone the repository**
   ```sh
   git clone <repo-url>
   cd maintenance_tracker
   ```

2. **Build and start with Docker Compose**
   ```sh
   docker compose build
   docker compose up -d
   ```

3. **Database Initialization**
   - The database is automatically created and initialized on first run.
   - An admin user is created with:
     - **Username:** `admin`
     - **Password:** `changeme`

4. **Access the Application**
   - Open your browser and go to [http://localhost:8080](http://localhost:8080) (or your configured port).

5. **Configure Your Questions**
   - After setup, you need to build the questions you want to ask for trailers and refrigeration units via the admin interface.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---