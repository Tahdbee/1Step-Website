# Ecosur Website - Database Setup Guide

## Quick Start

Your application is ready to use! However, to enable all features (activity logging, profile statistics, etc.), you need to import the database schema.

## Step 1: Import the SQL File

### Option A: Using phpMyAdmin (Recommended)

1. **Open phpMyAdmin**
   - Go to `http://localhost/phpmyadmin` in your browser
   - Log in with your XAMPP credentials (default: username: `root`, no password)

2. **Import the SQL File**
   - Click on the **"Import"** tab at the top
   - Click **"Choose File"** button
   - Navigate to: `C:\xampp\htdocs\Ecosur_Website\database\1Step.sql`
   - Select it and click **Open**
   - Click the **"Go"** button at the bottom

3. **Verify**
   - You should see a success message
   - The database `1Step` should now be listed in the left sidebar
   - You should see these tables:
     - `users`
     - `review`
     - `user_profile`
     - `user_activity`
     - `user_statistics`

### Option B: Using MySQL Command Line

```bash
mysql -u root -p 1Step < "C:\xampp\htdocs\Ecosur_Website\database\1Step.sql"
```

## What Gets Created

| Table             | Purpose                                               |
| ----------------- | ----------------------------------------------------- |
| `users`           | Stores user login credentials and basic info          |
| `review`          | Stores user reviews with ratings                      |
| `user_profile`    | Extended profile information (bio, preferences, etc.) |
| `user_activity`   | Tracks user actions (login, logout, reviews, etc.)    |
| `user_statistics` | Stores user engagement metrics                        |

## Features Enabled After Import

✅ User registration with automatic profile creation  
✅ User login/logout activity logging  
✅ Review posting with statistics updates  
✅ Profile page displaying real user data  
✅ Activity history on user profile  
✅ User engagement tracking

## Before Import

Your app will still work without these tables, but:

- Activity logging won't work
- Profile statistics won't display
- No activity history

## After Import

All features are fully functional!

## Troubleshooting

**Error: "Table '1step.user_activity' doesn't exist"**

- This means the SQL file hasn't been imported yet
- Follow the import steps above

**Error: "Access Denied"**

- Make sure XAMPP MySQL is running
- Check your phpMyAdmin credentials

**Error: "Database doesn't exist"**

- Make sure you're importing to the `1Step` database, not creating it separately

## Need Help?

The database file is located at:
`C:\xampp\htdocs\Ecosur_Website\database\1Step.sql`

All database operations now gracefully handle missing tables, so the app won't crash if tables are missing.
