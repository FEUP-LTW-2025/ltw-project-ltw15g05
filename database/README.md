## How to Create the Service Images Table

If you're facing issues with the service_images table not existing, you can run the migration script to create it:

```bash
php database/migrate_service_images.php
```

This script will check if the service_images table exists and create it if it doesn't.

Alternatively, you can add the following line to your database schema:

```sql
CREATE TABLE service_images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    service_id INTEGER NOT NULL,
    image_path TEXT NOT NULL,
    is_primary BOOLEAN DEFAULT 0,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);
```

This table is needed for storing images associated with services.
