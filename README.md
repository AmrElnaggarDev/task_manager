# Task Manager

Task Manager is a Laravel + Blade web application for managing projects, tasks, project members, task assignment, and task comments.

This project was built as a backend-focused learning project to practice Laravel fundamentals such as MVC, authentication, authorization, Eloquent relationships, validation, policies, and GitHub release workflow.

---

## Features

- User authentication
- Dashboard overview
- Project CRUD
- Task CRUD
- Project members management
- Task assignment restricted to project owner or project members
- Task comments
- Task status, priority, and deadline support
- Authorization policies
- Bootstrap-based Blade UI

---

## Tech Stack

- PHP
- Laravel
- Blade
- Bootstrap
- MySQL
- Laravel Breeze
- Eloquent ORM
- Git & GitHub

---

## Screenshots

### Dashboard

![Dashboard](public/screenshots/TaskManagerDashboard.PNG)

### Projects

![Projects](public/screenshots/TaskManagerProjects.PNG)

### Project Details with Members

![Project Details](public/screenshots/TaskManagerProjectShowWithMembers.PNG)

### Tasks Index

![Tasks Index](public/screenshots/TaskManagerTasksIndex.PNG)

### Task Details with Comments

![Task Details](public/screenshots/TaskManagerShowTaskWithComments.png)

### Edit Task

![Edit Task](public/screenshots/TaskManagerEditTask.PNG)

---

## Core Relationships

### User

A user can:

- Own many projects
- Belong to many projects as a member
- Create tasks
- Be assigned to tasks
- Write comments

### Project

A project:

- Belongs to an owner
- Has many members
- Has many tasks

### Task

A task:

- Belongs to a project
- Belongs to a creator
- May have an assignee
- Has many comments

### Comment

A comment:

- Belongs to a task
- Belongs to a user

---

## Authorization Rules

- Only project owners can update or delete their projects.
- Project owners can add and remove project members.
- Project owners and project members can view project tasks.
- Tasks can only be assigned to the project owner or project members.
- Users who can view a task can add comments.
- Comment owners and project owners can delete comments.

---

## Installation

Clone the repository:

```bash
git clone https://github.com/AmrElnaggarDev/task_manager.git
cd task_manager
```

Install dependencies:

```bash
composer install
npm install
```

Create the environment file:

```bash
copy .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Configure your database in `.env`, then run migrations:

```bash
php artisan migrate
```

Run the development server:

```bash
php artisan serve
```

Compile frontend assets:

```bash
npm run dev
```

---

## Usage Flow

1. Register or log in.
2. Create a project.
3. Add project members by email.
4. Create tasks inside the project.
5. Assign tasks to the project owner or project members.
6. Track task status, priority, and deadline.
7. Add comments to discuss task updates.

---

## Release History

- `v0.6.0` Dashboard Overview
- `v0.7.0` Project Members Management
- `v0.8.0` Task Comments
- `v0.9.0` Project Team Task Assignment
- `v1.0.0` First Stable Release

---

## Future Improvements

- Deadline alerts
- Project statistics
- Advanced task filters
- Activity logs
- User roles inside projects
- Comment editing
- Notifications
- Automated tests
- API endpoints

---

## Author

Built by [Amr Elnaggar](https://github.com/AmrElnaggarDev)
