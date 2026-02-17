# Message of the Day — Zabbix 6.4 Frontend Module

A frontend module for **Zabbix 6.4** that displays a configurable banner message to users on every page of the Zabbix UI.

---

## Features

- 📢 Displays a banner at the top of every page
- 🎨 Four banner types: **Info** (blue), **Warning** (yellow), **Critical** (red), **Success** (green)
- 👁 Show to **all users** or **administrators only**
- ✖ Optional **dismiss button** (dismissal is per-session)
- ⚙️ Admin settings page under **Administration → Message of the Day**
- 🌙 Dark theme compatible

---

## Requirements

- Zabbix **6.4.x**
- PHP 7.4+ with `json` extension
- Web server must have **write permission** to `config.json` inside the module directory

---

## Installation

1. Copy the `MessageOfTheDay` folder into your Zabbix frontend `modules` directory:

   ```
   /usr/share/zabbix/modules/MessageOfTheDay/
   ```

2. Make `config.json` writable by the web server:

   ```bash
   chown www-data:www-data /usr/share/zabbix/modules/MessageOfTheDay/config.json
   chmod 664 /usr/share/zabbix/modules/MessageOfTheDay/config.json
   ```

   Or simply make it world-writable (less secure):
   ```bash
   chmod 666 /usr/share/zabbix/modules/MessageOfTheDay/config.json
   ```

3. In Zabbix, go to **Administration → General → Modules** and click **Scan directory**.

4. Find **Message of the Day** in the list and click **Disabled** to enable it.

5. Refresh the page. A new entry **Administration → Message of the Day** will appear.

---

## Configuration

Go to **Administration → Message of the Day** to configure the banner:

| Setting | Description |
|---------|-------------|
| **Enabled** | Turn the banner on or off |
| **Message** | The text to display (up to 2048 characters) |
| **Banner type** | Visual style: info, warning, danger, or success |
| **Dismissible** | Whether users can close the banner (per session) |
| **Show to** | All users, or administrators only |

Settings are stored in `config.json` within the module directory.

---

## File Structure

```
MessageOfTheDay/
├── manifest.json              # Module metadata and action registration
├── Module.php                 # Injects menu item
├── config.json                # Stored banner configuration (must be writable)
├── README.md
├── actions/
│   ├── MessageOfTheDayConfig.php  # JSON API endpoint (used by JS)
│   ├── MessageOfTheDayEdit.php    # Settings page controller
│   └── MessageOfTheDaySave.php    # Save settings controller
├── views/
│   ├── messageoftheday.edit.php   # Settings form view
│   └── messageoftheday.config.php # JSON response view
└── assets/
    ├── css/
    │   └── motd.banner.css        # Banner styles (loaded on all pages)
    └── js/
        └── motd.banner.js         # Banner logic (loaded on all pages)
```

---

## Differences from the 7.0 version

This module is adapted for **Zabbix 6.4** and uses:
- `manifest_version: 2.0` (vs 2.1 in 7.0)
- `CFormGrid` + `CFormField` instead of newer form components
- `CFormActions` for the submit button row
- No `APP::ModuleManager()` calls (not available in 6.4)
- Session-based dismiss (using `sessionStorage`) instead of any server-side tracking

---

## License

GNU AGPLv3 — see LICENSE for details.
