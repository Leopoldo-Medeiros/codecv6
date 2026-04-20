# DDEV Configuration for 100MB File Uploads

This directory contains custom DDEV configurations to support uploading files up to 100MB.

## Files Modified/Created

### 1. Nginx Configuration
**File:** `.ddev/nginx_full/nginx-site.conf`
- Added: `client_max_body_size 100M;`
- Removed `#ddev-generated` to prevent DDEV from overwriting

### 2. PHP Configuration
**File:** `.ddev/php/upload-limits.ini`
- `upload_max_filesize = 100M`
- `post_max_size = 110M`
- `max_execution_time = 300`
- `max_input_time = 300`
- `memory_limit = 256M`

## How to Apply Changes

After modifying these configuration files, restart DDEV:

```bash
# Restart DDEV to apply changes
ddev restart

# Verify PHP configuration
ddev exec php -i | grep -E 'upload_max_filesize|post_max_size|max_execution_time'

# Expected output:
# upload_max_filesize => 100M => 100M
# post_max_size => 110M => 110M
# max_execution_time => 300 => 300
```

## Verify Nginx Configuration

```bash
# Check Nginx configuration
ddev exec nginx -t

# Expected output:
# nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
# nginx: configuration file /etc/nginx/nginx.conf test is successful
```

## Testing File Upload

You can test the configuration by uploading a file through the application or using curl:

```bash
# Test with a 50MB file
ddev exec dd if=/dev/zero of=/tmp/test-50mb.mp4 bs=1M count=50

# Upload via API (adjust endpoint and auth as needed)
curl -X POST https://uat.aviationhub.ie/api/attachments \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "files[]=@/tmp/test-50mb.mp4" \
  -F "attachable_type=App\Models\Defect" \
  -F "attachable_id=1"
```

## Troubleshooting

### Changes not applied?

1. Make sure you restarted DDEV: `ddev restart`
2. Check if files exist:
   ```bash
   ls -la .ddev/php/upload-limits.ini
   ls -la .ddev/nginx_full/nginx-site.conf
   ```

### Still getting errors?

1. Check DDEV logs:
   ```bash
   ddev logs
   ```

2. Check Nginx error logs:
   ```bash
   ddev exec tail -f /var/log/nginx/error.log
   ```

3. Verify PHP-FPM is reading the custom ini:
   ```bash
   ddev exec php --ini
   ```

## Rollback

To revert changes:

1. **Nginx**: Restore the `#ddev-generated` line and remove custom config
2. **PHP**: Delete `.ddev/php/upload-limits.ini`
3. **Restart**: `ddev restart`

## Production Deployment

These settings are for **development only**. For production servers, configure:

- **PHP** (`/etc/php/8.x/fpm/php.ini` and `/etc/php/8.x/cli/php.ini`)
- **Nginx** (`/etc/nginx/sites-available/your-site.conf`)
- **Restart services** after changes

See `.docs/backend/attachment-video-upload.md` for production configuration details.

## Related Files

- Application validation: `app/Http/Requests/StoreAttachmentRequest.php`
- Application validation: `app/Http/Requests/ReplaceAttachmentRequest.php`
- Documentation: `.docs/backend/attachment-video-upload.md`
