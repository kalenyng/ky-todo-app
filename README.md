# KY To-Do App

A simple and intuitive To-Do list application built with PHP and MySQL. This app allows users to add, view, and manage their daily tasks efficiently.

# Live-preview
https://todo-app.infinityfreeapp.com/index.php

## 📝 Features

- Add new tasks to your to-do list
- View all existing tasks
- Mark tasks as completed
- Delete tasks that are no longer needed
- Responsive design for both desktop and mobile devices

## 🚀 Getting Started

### Prerequisites

- PHP 7.0 or higher
- MySQL database
- Web server (e.g., Apache, Nginx)

### Installation

1. **Clone the repository:**

   ```bash
   git clone https://github.com/kalenyng/ky-todo-app.git
   ```

2. **Navigate to the project directory:**

   ```bash
   cd ky-todo-app
   ```

3. **Set up the database:**

   - Create a new MySQL database (e.g., `todo_app`)
   - Run the following SQL command to create the required table:

   ```sql
   CREATE TABLE tasks (
       id INT AUTO_INCREMENT PRIMARY KEY,
       title VARCHAR(255) NOT NULL,
       is_done BOOLEAN DEFAULT FALSE,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   ```

4. **Configure the database connection:**

   - Open the `config.php` file
   - Update the database credentials (`host`, `username`, `password`, `database_name`) as per your setup

5. **Run the application:**

   - Place the project folder in your web server's root directory (e.g., `htdocs` for XAMPP)
   - Start your web server and navigate to `http://localhost/ky-todo-app/index.php` in your browser

## 🤝 Contributing

Contributions are welcome! Please fork the repository and submit a pull request for any enhancements or bug fixes.

