# Admin Panel Setup - AB Bau

## ✅ Instalimi i Plotësuar

Admin panel është krijuar dhe gati për përdorim!

## 📁 Struktura e Projektit

```
ab-bau/
├── admin/                    # Admin Panel
│   ├── login.php            # Faqja e login
│   ├── dashboard.php        # Dashboard kryesor
│   ├── gallery.php          # Menaxhimi i galerisë
│   ├── services.php         # Menaxhimi i shërbimeve
│   ├── reviews.php          # Menaxhimi i reviews
│   ├── change-password.php  # Ndryshimi i fjalëkalimit
│   ├── functions.php        # Funksione helper
│   ├── logout.php           # Logout
│   └── README.md            # Dokumentacion
│
├── api/                      # API Endpoints
│   ├── get-data.php         # Lexim të dhënash
│   └── submit-review.php    # Dërgim reviews
│
├── data/                     # Të dhënat (JSON)
│   ├── config.json          # Konfigurimi
│   ├── gallery.json         # Galeria
│   ├── services.json        # Shërbimet
│   └── reviews.json         # Reviews
│
├── uploads/                  # Fotot e uploaduara
│
└── js/
    └── admin-api.js         # JavaScript për integrim
```

## 🚀 Si të Përdoret

### 1. Hyrje në Admin Panel

Shko te: `http://localhost/ab-bau/admin/login.php`

**Fjalëkalimi Default:** `admin123`

⚠️ **IMPORTANTE:** Ndrysho fjalëkalimin pas hyrjes së parë!

### 2. Funksionalitetet

#### Dashboard
- Statistikat e përgjithshme
- Quick access te të gjitha seksionet

#### Menaxhimi i Galerisë
- Shto fotot e reja
- Cakto nëse do të shfaqen në faqen kryesore ose portfolio
- Zhvendos fotot midis kategorive
- Fshi fotot

#### Menaxhimi i Shërbimeve
- Shto shërbime të reja
- Ndrysho shërbimet ekzistuese
- Aktivizo/Deaktivizo shërbimet
- Fshi shërbimet

#### Menaxhimi i Reviews
- Shiko reviews në pritje
- Aprovo ose refuzo reviews
- Fshi reviews të aprovuara

### 3. Integrimi me Faqen Publike

Faqja publike mund të lexojë të dhënat nga API:

```javascript
// Për galerinë
fetch('api/get-data.php?type=gallery')
  .then(res => res.json())
  .then(data => {
    // data.home - fotot kryesore
    // data.portfolio - fotot portfolio
  });

// Për shërbimet
fetch('api/get-data.php?type=services')
  .then(res => res.json())
  .then(data => {
    // Vetëm shërbimet aktive
  });

// Për reviews
fetch('api/get-data.php?type=reviews')
  .then(res => res.json())
  .then(data => {
    // Vetëm reviews të aprovuara
  });
```

## 🔒 Siguria

- ✅ Fjalëkalimi hash me bcrypt
- ✅ Session management
- ✅ Sanitizim i të dhënave
- ✅ Validim i inputeve
- ✅ Mbrojtje e folderit `data/` me `.htaccess`

## 📝 Shënime

1. **Backup:** Bëj backup të folderit `data/` para ndryshimeve të mëdha
2. **Permissions:** Sigurohu që folderi `uploads/` ka permissions për shkrim
3. **Fjalëkalimi:** Ndrysho fjalëkalimin default menjëherë pas instalimit
4. **JSON Files:** Mos fshi ose modifiko manualisht skedarët JSON

## 🐛 Troubleshooting

### Problem: Nuk mund të uploadoj fotot
**Zgjidhje:** Kontrollo permissions për folderin `uploads/`

### Problem: Nuk mund të hyj në admin panel
**Zgjidhje:** Kontrollo që PHP session funksionon dhe që folderi `data/` ekziston

### Problem: Reviews nuk shfaqen
**Zgjidhje:** Sigurohu që reviews janë aprovuar nga admin panel

## 📞 Mbështetje

Për çdo problem ose pyetje, kontrollo dokumentacionin në `admin/README.md`

