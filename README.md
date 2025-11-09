# 🧠 Talenavi Backend Test – ToDo API

Project ini dibuat untuk **Technical Test Backend Developer – Talenavi** menggunakan **Laravel 12**.
Aplikasi menyediakan API untuk mengelola *To-Do List* dengan fitur:

* ✅ Create To-Do
* ✅ Export To-Do ke Excel (dengan filter & summary)
* ✅ Get Chart Data (status, priority, assignee)

---

## ⚙️ Setup

```bash
git clone https://github.com/username/talenavi-backend-test.git
cd talenavi-backend-test
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

API URL: `http://127.0.0.1:8000/api`

---

## 🚀 API Endpoints

| Method | Endpoint                          | Description                    |
| :----- | :-------------------------------- | :----------------------------- |
| POST   | `/to-do-list`                     | Create new to-do               |
| GET    | `/to-do-list/export`              | Export to Excel (with filters) |
| GET    | `/to-do-list/chart?type=status`   | Chart by status                |
| GET    | `/to-do-list/chart?type=priority` | Chart by priority              |
| GET    | `/to-do-list/chart?type=assignee` | Chart by assignee              |

---

## 👨‍💻 Author

**Nama:** Salwa Labibah
**Email:** canorasalwa@gmail.com
**GitHub:** (https://github.com/Salwalabibah)

