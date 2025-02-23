![GitHub release (latest by date)](https://img.shields.io/github/v/release/jkm96/expense-tracker?display_name=tag&color=blue)
![GitHub license](https://img.shields.io/github/license/jkm96/expense-tracker?color=green)

# Expense Tracker

## Project Overview
The **Expense Tracker** is a simple web application to track and categorize daily expenses. Users can manage their expenses with CRUD operations, view detailed analytics, and filter expenses by monthly and yearly summaries. The app provides a dynamic dashboard with charts for better visualization of spending habits.

## Tech Stack
- **Framework:** Laravel 11.32
- **Database:** MySQL
- **Frontend:** Blade templates, Livewire, Alpine.js

## Features

### User Authentication:
- Email registration and login
- Email verification for new users

### Expense Management:
- Create, read, update, and delete (CRUD) operations for expenses
- Grouping expenses by year

### Dashboard with Visual Insights:
- Expense tracking by category
- Dynamic charts: Pie, Bar, and Line
- Monthly and yearly summaries

## Setup & Installation

### Prerequisites
Ensure you have the following installed:
- PHP (>=8.2)
- Composer
- MySQL
- Node.js (for frontend assets)

### Installation Steps

1. Clone the repository:

```bash
git clone <repository_url>
cd expense-tracker
```

2. Install dependencies:

```bash
composer install
npm install
```

3. Set up environment variables:

```bash
cp .env.example .env
php artisan key:generate
```

4. Configure database connection in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=expense_tracker
DB_USERNAME=root
DB_PASSWORD=your_password
```

5. Run database migrations:

```bash
php artisan migrate
```

6. Start the development servers:

```bash
php artisan serve
npm run dev
```

Access the app at: `http://localhost:8000`

## Usage Instructions
1. Register a new account or log in.
2. Navigate to the expense page to manage expenses and use the dashboard page to view expense analytics.
3. Use the filters to switch between monthly and yearly views.

### Dashboard Filter Options:
- **Monthly View:** Select a month to view weekly expenses.
- **Yearly View:** Select a year for a comprehensive breakdown of monthly expenses.

## Contribution Guidelines
1. Fork the repository and create a feature branch.
2. Commit changes with clear and concise messages.
3. Submit a pull request for review.

## Troubleshooting
1. Ensure database credentials are correctly set in `.env`.
2. Clear cache if issues arise:

```bash
php artisan config:cache
php artisan cache:clear
```

3. Verify that required PHP extensions (e.g., mbstring, pdo) are enabled.

## Future Enhancements
- Expense categories customization
- Exporting expense reports (CSV, PDF)

## Contact
For questions or support, open an issue in the repository or reach out via email **jkm96.dev@gmail.com**.



