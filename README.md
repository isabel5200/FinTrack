## FinTrack

## ✨ Personal Finance Tracker
FinTrack is a lightweight personal finance management application that helps users track transactions, organize categories, and manage monthly budgets with clarity and efficiency. Built with a clean structure and modern stack, the project serves as a solid foundation for learning or expanding financial tools.

## 🚀 Features
- 📒 Create, edit, and delete transactions
- 💸 Income and expense tracking
- 🗂️ Custom categories
- 🎯 Monthly budgets
- 📊 Dashboard with essential insights
- 📱 Responsive UI
- 🔐 Backend and frontend validation
- 🧩 Modular and extendable structure

## 🛠️ Tech Stack

# Backend
- 🐘 PHP 8.4.5
- 🧱 Laravel
- 📦 Composer 2.8.6
- 🗄️ MySQL 8.0

# Frontend
- 🌿 Vue
- 🔗 Inertia.js
- 💠 PrimeVue 4.0
- 🎨 Tailwind CSS 4.0.14
- 📡 Axios
- 🟢 Node 22.14
- 🔵 npm 10.8.3

# Other
- 🧭 Git version control

## 📋 Requirements
- PHP 8.4+
- Composer 2.8+
- Node 22+
- npm 10+
- MySQL 8+

## ⚙️ Installation
    ````sh 
    git clone https://github.com/isabel5200/FinTrack
    ````

# Backend setup
    ````sh 
    composer install
    cp .env.example .env
    php artisan key:generate
    ````

Configure your .env:
    ````sh 
    DB_DATABASE=fintrack
    DB_USERNAME=your_user
    DB_PASSWORD=your_password
    ````

Run migrations:
    ````sh 
    php artisan migrate
    ````

(Optional seed):
    ````sh 
    php artisan db:seed
    ````

Start the backend server:
    ````sh 
    php artisan serve
    ````

# Frontend setup
    ````sh 
    npm install
    npm run dev
    ````

## 📂 Project Structure
- app/ Core backend logic
- resources/js/ Vue components and Inertia pages
- resources/views/ Blade templates
- routes/ Routing layer
- database/ Migrations and seeders

## ⚠️ Note
This project is for learning and portfolio purposes only.
________________________________________
Made with ❤️ by Isabel Lovera

