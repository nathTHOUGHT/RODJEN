# Frontend file map

```text
resources/
├── css/
│   └── app.css                         # Tailwind setup and shared styles
├── js/
│   ├── app.js                          # Main JavaScript entry point
│   └── passkeys.js                     # Passkey integration
└── views/
    ├── home/
    │   ├── index.blade.php             # Home page content
    │   └── partials/
    │       └── reminders.blade.php     # Home reminder list
    ├── dashboard.blade.php             # Dashboard page content
    ├── layouts/
    │   ├── app.blade.php               # Standard page-content wrapper
    │   └── app/
    │       ├── admin-shell.blade.php   # Header + sidebar + main content
    │       ├── admin-header.blade.php  # Administration toolbar
    │       └── top-navigation-shell.blade.php
    │                                      # Alternative layout; currently unused
    ├── components/                     # Reusable UI components
    ├── partials/                       # Small shared view fragments
    ├── admin/                          # Administration pages
    └── pages/                          # Authentication and settings pages

public/
├── brand/                              # Logos and brand images
└── icons/                              # Sidebar icons
```

## Main frontend flow

```text
home/index.blade.php
└── layouts/app/admin-shell.blade.php
    ├── layouts/app/admin-header.blade.php
    ├── Sidebar navigation
    └── Main content slot
```
