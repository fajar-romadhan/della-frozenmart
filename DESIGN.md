# 🎨 DESIGN SYSTEM — della-frozenmart

> Panduan desain visual untuk seluruh antarmuka web **della-frozenmart**.  
> Dokumen ini menjadi acuan utama untuk konsistensi warna, tipografi, komponen, dan layout di seluruh halaman.

---

## 📐 Design Philosophy

**Aesthetic Direction:** Soft Glassmorphism + Clean Dashboard  
**Mood:** Modern, Profesional, Bersih, Tepercaya  
**Inspirasi:** Gradient pastel lembut dengan kartu putih transparan, sidebar ikon minimalis, dan hierarki informasi yang jelas.

> Setiap elemen harus terasa **ringan namun fungsional** — tidak berlebihan, tidak terlalu kosong.

---

## 🎨 Color Palette

### Background Gradient (Global)
```css
/* Gunakan sebagai background utama halaman */
background: linear-gradient(135deg, #c8b8e8 0%, #b8cde8 40%, #a8d8e8 100%);
/* Lavender → Soft Blue → Cyan */
```

### CSS Variables (tambahkan di `app.css` atau `<style>` global)
```css
:root {
  /* Brand Colors */
  --color-primary:       #5b8dee;   /* Biru utama (aksi, tombol) */
  --color-primary-light: #e8f0fe;   /* Biru muda (badge, hover) */
  --color-accent:        #7c6fe0;   /* Ungu aksen */
  --color-accent-light:  #ede9fb;   /* Ungu muda */
  --color-danger:        #f87171;   /* Merah (URGENT, error) */
  --color-success:       #34d399;   /* Hijau (aktif, sukses) */
  --color-warning:       #fbbf24;   /* Kuning (peringatan) */

  /* Background */
  --bg-gradient-start:   #c8b8e8;
  --bg-gradient-mid:     #b8cde8;
  --bg-gradient-end:     #a8d8e8;
  --bg-card:             rgba(255, 255, 255, 0.85);
  --bg-card-dark:        #2d3a4e;   /* Card gelap (featured banner) */
  --bg-sidebar:          rgba(255, 255, 255, 0.60);

  /* Text */
  --text-primary:        #1e293b;   /* Judul utama */
  --text-secondary:      #64748b;   /* Label, deskripsi */
  --text-muted:          #94a3b8;   /* Teks redup */
  --text-on-dark:        #ffffff;   /* Teks di kartu gelap */
  --text-label:          #8fa3c0;   /* Label kecil (uppercase) */

  /* Border & Shadow */
  --border-color:        rgba(255, 255, 255, 0.60);
  --shadow-card:         0 4px 24px rgba(100, 120, 180, 0.10);
  --shadow-card-hover:   0 8px 32px rgba(100, 120, 180, 0.18);

  /* Border Radius */
  --radius-sm:    8px;
  --radius-md:    16px;
  --radius-lg:    24px;
  --radius-xl:    32px;
  --radius-full:  9999px;

  /* Spacing */
  --space-1: 4px;
  --space-2: 8px;
  --space-3: 12px;
  --space-4: 16px;
  --space-5: 20px;
  --space-6: 24px;
  --space-8: 32px;
}
```

---

## 🔤 Typography

### Font Stack
```css
/* Import di <head> atau app.css */
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Sans:wght@300;400;500&display=swap');

:root {
  --font-display: 'Plus Jakarta Sans', sans-serif;  /* Judul, heading */
  --font-body:    'DM Sans', sans-serif;             /* Teks biasa, label */
}
```

### Type Scale
| Token          | Size    | Weight | Penggunaan                        |
|----------------|---------|--------|-----------------------------------|
| `--text-xs`    | 10px    | 500    | Badge, label uppercase kecil      |
| `--text-sm`    | 12px    | 400    | Timestamp, deskripsi redup        |
| `--text-base`  | 14px    | 400    | Body teks, list item              |
| `--text-md`    | 16px    | 500    | Label form, subtitle kartu        |
| `--text-lg`    | 20px    | 600    | Judul section/kartu               |
| `--text-xl`    | 24px    | 700    | Angka statistik besar             |
| `--text-2xl`   | 32px    | 800    | Nama produk featured, page title  |
| `--text-3xl`   | 40px+   | 800    | Hero / banner besar               |

```css
:root {
  --text-xs:   0.625rem;
  --text-sm:   0.75rem;
  --text-base: 0.875rem;
  --text-md:   1rem;
  --text-lg:   1.25rem;
  --text-xl:   1.5rem;
  --text-2xl:  2rem;
  --text-3xl:  2.5rem;
}
```

---

## 🧩 Layout

### Grid System
```
┌─────────────────────────────────────────────────────┐
│  SIDEBAR (64px)  │         MAIN CONTENT              │
│  (fixed, icons)  │  Topbar + Content Area            │
└─────────────────────────────────────────────────────┘
```

```css
.app-shell {
  display: grid;
  grid-template-columns: 64px 1fr;
  min-height: 100vh;
  background: linear-gradient(135deg, var(--bg-gradient-start), var(--bg-gradient-mid), var(--bg-gradient-end));
}

.main-content {
  display: flex;
  flex-direction: column;
  padding: var(--space-6);
  gap: var(--space-6);
}

/* Grid konten utama: 2 kolom (70% / 30%) */
.content-grid {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: var(--space-6);
}

/* Grid 2 kolom sejajar */
.two-col-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: var(--space-4);
}
```

### Breakpoints
```css
/* Mobile */
@media (max-width: 640px)  { /* stack semua kolom */ }
/* Tablet */
@media (max-width: 1024px) { /* sidebar collapse, 1 kolom */ }
/* Desktop */
@media (min-width: 1025px) { /* layout penuh */ }
```

---

## 🗂 Komponen UI

### 1. Card (Kartu Utama)
```css
.card {
  background: var(--bg-card);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid var(--border-color);
  border-radius: var(--radius-lg);
  padding: var(--space-6);
  box-shadow: var(--shadow-card);
  transition: box-shadow 0.2s ease;
}

.card:hover {
  box-shadow: var(--shadow-card-hover);
}
```

### 2. Sidebar
```css
.sidebar {
  width: 64px;
  background: var(--bg-sidebar);
  backdrop-filter: blur(20px);
  border-right: 1px solid var(--border-color);
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: var(--space-4) 0;
  gap: var(--space-2);
  position: fixed;
  top: 0; left: 0;
  height: 100vh;
  z-index: 100;
}

.sidebar-icon {
  width: 40px; height: 40px;
  border-radius: var(--radius-sm);
  display: flex; align-items: center; justify-content: center;
  color: var(--text-secondary);
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
}

.sidebar-icon:hover,
.sidebar-icon.active {
  background: var(--color-primary-light);
  color: var(--color-primary);
}
```

### 3. Topbar / Navbar
```css
.topbar {
  background: var(--bg-card);
  backdrop-filter: blur(12px);
  border-radius: var(--radius-lg);
  padding: var(--space-3) var(--space-6);
  display: flex;
  align-items: center;
  gap: var(--space-6);
  box-shadow: var(--shadow-card);
}

.topbar-brand {
  font-family: var(--font-display);
  font-weight: 700;
  font-size: var(--text-md);
  color: var(--text-primary);
}

.topbar-nav a {
  font-size: var(--text-base);
  color: var(--text-secondary);
  text-decoration: none;
  padding-bottom: 2px;
}

.topbar-nav a.active {
  color: var(--text-primary);
  border-bottom: 2px solid var(--text-primary);
  font-weight: 600;
}

.topbar-search {
  flex: 1;
  background: #f1f5f9;
  border: none;
  border-radius: var(--radius-full);
  padding: var(--space-2) var(--space-4);
  font-size: var(--text-base);
  color: var(--text-primary);
  outline: none;
}
```

### 4. Stat Card (Kartu Statistik)
```css
.stat-card {
  background: rgba(255,255,255,0.6);
  border-radius: var(--radius-md);
  padding: var(--space-4) var(--space-5);
}

.stat-label {
  font-family: var(--font-body);
  font-size: var(--text-xs);
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--text-label);
  margin-bottom: var(--space-1);
}

.stat-value {
  font-family: var(--font-display);
  font-size: var(--text-xl);
  font-weight: 700;
  color: var(--text-primary);
}

.stat-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: var(--text-sm);
  font-weight: 500;
  color: var(--color-success);
  margin-top: var(--space-1);
}
```

### 5. Badge / Tag
```css
.badge {
  display: inline-flex;
  align-items: center;
  padding: 2px 10px;
  border-radius: var(--radius-full);
  font-size: var(--text-xs);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.badge-urgent  { background: #fee2e2; color: var(--color-danger); }
.badge-today   { background: #e0f2fe; color: #0284c7; }
.badge-success { background: #d1fae5; color: #059669; }
.badge-muted   { background: #f1f5f9; color: var(--text-secondary); }
```

### 6. Button
```css
/* Primary */
.btn-primary {
  background: var(--color-primary);
  color: #fff;
  border: none;
  border-radius: var(--radius-full);
  padding: var(--space-2) var(--space-5);
  font-family: var(--font-display);
  font-size: var(--text-base);
  font-weight: 600;
  cursor: pointer;
  transition: opacity 0.15s, transform 0.1s;
}
.btn-primary:hover { opacity: 0.90; transform: translateY(-1px); }

/* Ghost / Outline */
.btn-ghost {
  background: transparent;
  border: 1.5px solid var(--border-color);
  border-radius: var(--radius-full);
  padding: var(--space-2) var(--space-5);
  font-size: var(--text-base);
  font-weight: 500;
  color: var(--text-primary);
  cursor: pointer;
  transition: background 0.15s;
}
.btn-ghost:hover { background: var(--color-primary-light); }

/* CTA Terang (di banner gelap) */
.btn-light {
  background: #ffffff;
  color: var(--text-primary);
  border: none;
  border-radius: var(--radius-full);
  padding: var(--space-3) var(--space-6);
  font-weight: 700;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  font-size: var(--text-sm);
  cursor: pointer;
}
```

### 7. Notification Item
```css
.notif-item {
  display: flex;
  align-items: flex-start;
  gap: var(--space-3);
  padding: var(--space-3) 0;
  border-bottom: 1px solid #f1f5f9;
}

.notif-icon {
  width: 36px; height: 36px;
  border-radius: 50%;
  background: var(--color-accent-light);
  display: flex; align-items: center; justify-content: center;
  color: var(--color-accent);
  flex-shrink: 0;
}

.notif-title {
  font-size: var(--text-base);
  font-weight: 600;
  color: var(--text-primary);
}

.notif-desc {
  font-size: var(--text-sm);
  color: var(--text-muted);
}
```

### 8. Task Item
```css
.task-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-3);
  padding: var(--space-3) var(--space-4);
  background: #f8fafc;
  border-radius: var(--radius-md);
}

.task-title {
  font-size: var(--text-base);
  font-weight: 500;
  color: var(--text-primary);
}
```

### 9. Featured Banner (Dark Card)
```css
.banner-dark {
  background: var(--bg-card-dark);
  border-radius: var(--radius-xl);
  padding: var(--space-8);
  display: grid;
  grid-template-columns: 1fr auto;
  align-items: center;
  gap: var(--space-6);
  position: relative;
  overflow: hidden;
  color: var(--text-on-dark);
}

.banner-label {
  font-size: var(--text-xs);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: rgba(255,255,255,0.55);
  margin-bottom: var(--space-2);
}

.banner-title {
  font-family: var(--font-display);
  font-size: var(--text-2xl);
  font-weight: 800;
  margin-bottom: var(--space-3);
}

.banner-desc {
  font-size: var(--text-base);
  color: rgba(255,255,255,0.65);
  max-width: 420px;
  margin-bottom: var(--space-5);
  line-height: 1.6;
}
```

---

## 📊 Chart / Grafik

Gunakan **Chart.js** atau **ApexCharts** untuk grafik.  
Style grafik harus sesuai tema:

```javascript
// Contoh config Chart.js — Bar Chart
const chartConfig = {
  type: 'bar',
  data: { /* ... */ },
  options: {
    plugins: {
      legend: { display: false },
      tooltip: {
        backgroundColor: '#fff',
        titleColor: '#1e293b',
        bodyColor: '#64748b',
        borderColor: '#e2e8f0',
        borderWidth: 1,
        borderRadius: 8,
      }
    },
    scales: {
      x: { grid: { display: false }, border: { display: false } },
      y: { grid: { color: '#f1f5f9' }, border: { display: false } }
    },
    borderRadius: 12,    /* Bar rounded */
    barPercentage: 0.6,
  }
}
```

**Warna bar chart:**
- Aktif / fokus: `#5b8dee`
- Tidak aktif: `#cbd5e1`

---

## 🗓 Kalender Widget

```css
.calendar-widget {
  /* Gunakan class .card sebagai wrapper */
}

.calendar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--space-4);
}

.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: var(--space-1);
  text-align: center;
}

.cal-day {
  width: 32px; height: 32px;
  display: flex; align-items: center; justify-content: center;
  border-radius: 50%;
  font-size: var(--text-sm);
  cursor: pointer;
  transition: background 0.1s;
}

.cal-day:hover      { background: var(--color-primary-light); }
.cal-day.today      { background: var(--color-primary); color: #fff; font-weight: 700; }
.cal-day.other-month { color: var(--text-muted); }
```

---

## ✨ Micro-interactions & Animasi

```css
/* Fade-in kartu saat halaman load */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}

.card {
  animation: fadeInUp 0.4s ease both;
}

/* Stagger delay untuk beberapa kartu */
.card:nth-child(1) { animation-delay: 0.05s; }
.card:nth-child(2) { animation-delay: 0.10s; }
.card:nth-child(3) { animation-delay: 0.15s; }
.card:nth-child(4) { animation-delay: 0.20s; }

/* Hover lift efek */
.card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.card:hover {
  transform: translateY(-2px);
}
```

---

## 📁 Struktur File CSS (Blade/Laravel)

```
resources/
└── css/
    ├── app.css           ← Import semua, definisi :root variabel
    ├── layout.css        ← Sidebar, topbar, grid shell
    ├── components.css    ← Card, badge, button, notif, task
    ├── charts.css        ← Styling chart/grafik
    └── pages/
        ├── dashboard.css
        ├── produk.css
        ├── transaksi.css
        └── laporan.css
```

---

## 📄 Halaman & Komponen

| Halaman            | Komponen Utama                                      |
|--------------------|-----------------------------------------------------|
| `/dashboard`       | Stat cards, chart penjualan, notifikasi, tasks      |
| `/produk`          | Table produk, card grid, badge stok                 |
| `/transaksi`       | List transaksi, badge status, filter date           |
| `/laporan`         | Chart bar/line, export button, date range picker    |
| `/stok-opname`     | Table stok, badge level, progress bar               |
| `/notifikasi`      | List notif, ikon, timestamp, badge unread           |
| `/analisis`        | Chart multi, card KPI, heatmap                      |
| `/barang-keluar`   | Form transaksi, list riwayat                        |

---

## 🌐 Iconography

Gunakan **Heroicons** (sudah tersedia di banyak setup Laravel) atau **Phosphor Icons**:

```html
<!-- Contoh via CDN Phosphor -->
<script src="https://unpkg.com/@phosphor-icons/web"></script>
<i class="ph ph-house"></i>
<i class="ph ph-chart-bar"></i>
<i class="ph ph-bell"></i>
<i class="ph ph-gear"></i>
```

**Ukuran ikon standar:**
- Sidebar: `20px`
- Inline teks: `16px`
- Banner / hero: `24–32px`

---

## ♿ Aksesibilitas

- Semua tombol punya `:focus-visible` outline
- Kontras teks minimum **4.5:1** (WCAG AA)
- Gunakan tag semantik: `<nav>`, `<main>`, `<section>`, `<aside>`
- Tambahkan `aria-label` pada ikon tanpa teks

```css
:focus-visible {
  outline: 2px solid var(--color-primary);
  outline-offset: 3px;
  border-radius: var(--radius-sm);
}
```

---

## 🚀 Quick Start

1. Tambahkan variabel CSS di `resources/css/app.css`
2. Import font Google di `resources/views/layouts/app.blade.php`
3. Terapkan `class="card"` pada setiap panel/kotak
4. Gunakan grid layout `.content-grid` di halaman utama
5. Terapkan `.badge-urgent`, `.badge-today`, dll. pada status

---

> **Versi:** 1.0.0 — della-frozenmart Design System  
> **Maintainer:** della-frozenmart dev team  
> **Terakhir diupdate:** Mei 2026
