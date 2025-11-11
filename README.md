
# WIFT Africa Backend API

This is the backend for the WIFT Africa membership onboarding platform built with **CodeIgniter 4**.

## 🚀 Features
- User registration & onboarding workflow
- Chapters, Roles, and Specializations stored in database
- Profile, Portfolio, Achievement, and Experience management
- API-first design (ready for mobile & web frontend)
- MySQL database with migrations + seeders

## 📦 Requirements
- PHP 8+
- Composer
- MySQL 5.7+ / MariaDB

## ⚙️ Installation
```bash
git clone https://github.com/jimohisraelo/wilfafrica_backend.git
cd wiftafrica-backend
composer install
cp env .env
php spark migrate
php spark db:seed ChaptersSeeder
php spark db:seed RolesSeeder
```

## 🧪 Running Locally
```bash
php spark serve
```
API runs at:
```
http://localhost:8080
```

## 🗂 API Endpoints

### **Auth & Onboarding**
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/auth/register` | Register user |
| POST | `/auth/login` | Login user |
| GET | `/onboarding/chapters` | List chapters |
| GET | `/onboarding/roles` | List roles |
| GET | `/onboarding/specializations` | List specializations |
| PUT | `/onboarding/roles` | Set Role & Specializations |
| PUT | `/onboarding/chapter` | Select Chapter |
| POST | `/onboarding/cv` | Upload Resume |
| PUT | `/onboarding/links` | Add Links |
| POST | `/onboarding/survey/submissions` | Submit Survey |
| POST | `/onboarding/complete` | Complete Onboarding |

---

## 🤝 Contributing
```bash
git checkout -b feature/my-feature
git commit -m "feat: add new feature"
git push origin feature/my-feature
```

---

## 📄 License
Private Repository – Internal Use Only.
