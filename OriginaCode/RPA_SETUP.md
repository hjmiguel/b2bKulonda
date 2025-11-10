# RPA Excel Upload System - Setup Complete

## Overview
The RPA Excel Upload system has been successfully installed in the Fornecedores application.

## User Credentials
- **Email:** rpa@kulonda.ao
- **Password:** RPA@Kulonda2024

## Access URL
After logging in with the RPA user credentials, access the upload page at:
```
https://your-domain.com/rpa
```

## Features
1. **Drag & Drop Upload**: Simply drag and drop Excel files (.xlsx, .xls, .csv) onto the upload zone
2. **File Management**: View all uploaded files with upload date and size
3. **Download**: Download any previously uploaded file
4. **Delete**: Remove files that are no longer needed
5. **File Validation**: Maximum file size of 10MB, only Excel formats accepted

## File Storage
All uploaded files are stored in:
```
public/RPA/
```

## Technical Details

### Controller
- **Location:** app/Http/Controllers/RPAExcelController.php
- **Methods:**
  - index() - Display upload form and file list
  - upload() - Handle file upload
  - download() - Download a file
  - delete() - Delete a file

### Routes
All routes are protected by authentication middleware:
- GET /rpa - Upload page
- POST /rpa/upload - Handle upload
- GET /rpa/download/{filename} - Download file
- DELETE /rpa/delete/{filename} - Delete file

### View
- **Location:** resources/views/rpa/upload.blade.php
- **Features:** Bootstrap 5, drag-and-drop, responsive design

## Security
- Authentication required for all RPA routes
- File type validation (only Excel files)
- File size limit (10MB maximum)
- CSRF protection on all forms

## How to Use

1. **Login to the system** using the RPA user credentials
2. **Navigate to /rpa** route
3. **Upload files** by either:
   - Dragging and dropping files onto the upload zone
   - Clicking the upload zone to browse for files
4. **Manage files** from the uploaded files table:
   - Click the download icon to download a file
   - Click the delete icon to remove a file

## Troubleshooting

If you encounter any issues:
1. Clear application cache:
   ```bash
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

2. Check file permissions on the public/RPA directory:
   ```bash
   chmod 755 public/RPA
   ```

3. Ensure the database connection is properly configured in the .env file

## Support
For technical support, contact the development team.

---
**Installation Date:** $(date)
**Version:** 1.0.0
