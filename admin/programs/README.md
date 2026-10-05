# Program Management Module

This folder provides administrative management for ESAHub Africa programs:
- `index.php`: Programs listing with category, status, active toggles, edit and delete actions.
- `create.php`: Program creation form supporting categories, delivery modes, pricing, and image upload.
- `edit.php`: Full program editing including category reassignment, delivery mode, duration, templates, and image replacement.
- `toggle.php`: Fast POST-based active/inactive toggle with CSRF verification.
- `delete.php`: Program deletion handler with CSRF protection and orphaned image cleanup.
