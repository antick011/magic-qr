# 🌟 Magic QR - Smart Review Filter System

Magic QR is a Laravel-based web application designed to improve customer feedback quality by offering a **rating form** through a QR code. Customers can scan the QR code to leave a review, which is **filtered** based on rating before redirecting to Google Review. This ensures **positive reviews** are promoted and **critical feedback** is handled privately.

---

## 🔧 Features

- 📱 QR code generation for each rating form.
- 🌐 Custom form UI to collect ratings and comments.
- ⭐ Automatically redirects high ratings (4 & 5 stars) to Google Review page.
- 🔒 Filters lower ratings (1–3 stars) for internal feedback handling.
- 📊 Admin dashboard to view and analyze responses.
- 📥 Stores feedback for internal analysis before publishing.

---

## 🚀 Use Case

Businesses use **Magic QR** to:
- Encourage more **positive reviews** on Google.
- Collect **genuine negative feedback** privately.
- Enhance **online reputation** and **customer trust**.

---

## ⚙️ Tech Stack

- **Back-end**: PHP (MVC)
- **Database**: MySQL
- **Frontend**: Bootstrap, Blade templates
- **QR Code**: Simple QR generation libraries
- **Hosting**: XAMPP / Apache or Render / Netlify for frontend

---

## 📸 How It Works

1. 📲 Customer scans QR code → lands on feedback form.
2. 📝 Customer selects rating (1–5 stars) & writes comment.
3. 🎯
   - If ⭐⭐⭐⭐ or ⭐⭐⭐⭐⭐ → Redirects to your business's **Google Review page**.
   - If ⭐ to ⭐⭐⭐ → Saves feedback to admin panel for offline review.

---

## License

This project is **not open source**.  
**All rights reserved © Antick**  
**Cloning, downloading, or redistributing this repository is prohibited**.
