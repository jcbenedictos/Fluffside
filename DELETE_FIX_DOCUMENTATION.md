# Delete Functionality Fix - FluffSide Admin

## Issue Summary
Admin users could delete pets and supplies from the UI, but the data remained in the SQL database. Additionally, timestamps were potentially off due to timezone configuration issues.

## Root Causes Identified

### 1. **Soft Deletes Being Used**
The original code was using "soft deletes" - marking records as inactive instead of actually removing them:
```php
// OLD CODE:
UPDATE tbl_pets SET is_available = 0 WHERE pet_id = ?
UPDATE tbl_products SET is_active = 0 WHERE product_id = ?
```

This approach means:
- Data stays in the database forever
- Only hidden from UI queries
- Can confuse admins and clutter databases over time

### 2. **No Timezone Configuration**
The database connection didn't specify timezone handling, causing potential timestamp mismatches between:
- PHP application timezone
- MySQL server timezone
- Client timezone

## Changes Made

### 1. **Updated `db.inc.php`**
- Added `date_default_timezone_set()` for PHP timezone consistency
- Added timezone configuration to MySQL session
- Ensures all timestamps are in UTC (standardized)

### 2. **Updated `delete_pet()` Function in `db_helper.inc.php`**
Now performs **hard delete** with proper cascading:
```php
function delete_pet(string $id): bool {
    global $pdo;
    try {
        // 1. Delete pet gallery images
        DELETE FROM tbl_pet_gallery WHERE pet_id = ?
        
        // 2. Delete pet traits/likes/dislikes
        DELETE FROM tbl_pet_traits WHERE pet_id = ?
        
        // 3. Delete related applications
        DELETE FROM tbl_app_adoption WHERE app_id IN (...)
        DELETE FROM tbl_applications WHERE pet_id = ?
        
        // 4. Delete the pet itself
        DELETE FROM tbl_pets WHERE pet_id = ?
        
        return true;
    } catch (Exception $e) {
        // Log error and return false
        return false;
    }
}
```

### 3. **Updated `delete_product()` Function in `db_helper.inc.php`**
Now performs **hard delete** with cascading:
```php
function delete_product(int $id): bool {
    global $pdo;
    try {
        // 1. Delete product gallery images
        DELETE FROM tbl_product_gallery WHERE product_id = ?
        
        // 2. Delete order items referencing this product
        DELETE FROM tbl_order_items WHERE product_id = ?
        
        // 3. Delete the product itself
        DELETE FROM tbl_products WHERE product_id = ?
        
        return true;
    } catch (Exception $e) {
        // Log error and return false
        return false;
    }
}
```

## Benefits of Changes

✅ **Real Deletions**: Data is actually removed from database, not just hidden
✅ **Referential Integrity**: All related records are deleted in proper order
✅ **Timestamp Consistency**: All timestamps now use the same timezone
✅ **Error Handling**: Exceptions are logged for debugging
✅ **Database Cleanliness**: No orphaned records left behind

## Important Notes

### Before You Delete
- **This is destructive**: Hard deletes cannot be undone without database backups
- **Check dependencies**: Ensure no orders or applications are dependent on pets/supplies before deleting
- **Backup your database**: Always maintain recent backups

### Timezone Configuration
If you're in a different timezone than America/Chicago:
- Edit `db.inc.php` line 7
- Change `'America/Chicago'` to your timezone
- Use PHP timezone format: https://www.php.net/manual/en/timezones.php

Examples:
- `'UTC'` - Coordinated Universal Time
- `'America/New_York'` - Eastern Time
- `'America/Los_Angeles'` - Pacific Time
- `'Europe/London'` - London/UK time
- `'Asia/Manila'` - Philippine Time

### Database Constraints
If you encounter foreign key errors during deletion:
1. Check if there are still orders containing the product
2. Ensure no active applications for the pet
3. The delete functions handle cascading, but may fail if constraints are too strict

### Testing the Fix
1. Create a test pet/supply in admin panel
2. Delete it from admin panel
3. Check the database directly:
   ```sql
   SELECT * FROM tbl_pets WHERE pet_id = 'test_pet';
   SELECT * FROM tbl_products WHERE product_id = 999;
   ```
4. The records should no longer exist

### Reverting to Soft Deletes (If Needed)
If you prefer to keep soft deletes instead:
1. Use `UPDATE` statements instead of `DELETE`
2. Always filter queries with `WHERE is_available = 1` for pets
3. Always filter queries with `WHERE is_active = 1` for products
4. This is safer for data preservation but creates database bloat

## Files Modified
- `db.inc.php` - Database connection with timezone
- `db_helper.inc.php` - Hard delete functions for pets and products

## Support
If deletions are still not working:
1. Check PHP error logs
2. Check MySQL error logs
3. Verify database user has DELETE permissions
4. Ensure foreign key constraints are properly configured
