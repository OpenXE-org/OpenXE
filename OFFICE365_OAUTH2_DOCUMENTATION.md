# Office365 OAuth2 Integration - Complete Implementation

## Overview

This document describes the Office365 OAuth2 implementation for OpenXE's mail system, which enables users to configure Office365 email accounts with OAuth2-based SMTP authentication instead of traditional passwords.

The implementation includes:
- Complete OAuth2 authorization code grant flow integration
- Automatic access token refresh with database persistence
- Secure credential storage in the OpenXE configuration system
- Custom SMTP client with direct uppercase XOAUTH2 authentication support
- Full MIME support for attachments and HTML emails
- Comprehensive error handling and logging

**Status**: ✅ Implementation complete and fully functional. Office365 email sending now works directly without external proxies.

---

## Architecture

### Module Structure: `classes/Modules/Office365Api/`

The Office365 module mirrors the GoogleApi module structure:

```
Office365Api/
├── Bootstrap.php                          # Service registration
├── Service/
│   ├── Office365AccountGateway.php       # Database access layer
│   ├── Office365AuthorizationService.php # OAuth2 flow handler
│   └── Office365CredentialsService.php   # App credentials management
├── Data/
│   ├── Office365AccountData.php          # Account DTO
│   ├── Office365AccessTokenData.php      # Token with expiration
│   ├── Office365TokenResponseData.php    # Token response parser
│   ├── Office365CredentialsData.php      # OAuth app credentials
│   ├── Office365AccountPropertyValue.php # Property storage
│   └── Office365AccountPropertyCollection.php
└── Exception/
    ├── Office365AccountException.php
    ├── Office365OAuthException.php
    ├── AuthorizationExpiredException.php
    ├── NoAccessTokenException.php
    └── NoRefreshTokenException.php
```

### Key Components

#### 1. Office365CredentialsService
Manages OAuth app credentials (client_id, client_secret, redirect_uri, tenant_id) stored in the `konfiguration` table.

**Config keys**:
- `office365_client_id`
- `office365_client_secret`
- `office365_redirect_uri`
- `office365_tenant_id`

#### 2. Office365AuthorizationService
Handles the OAuth2 authorization code grant flow:

**Authorization endpoints**:
- Authorization: `https://login.microsoftonline.com/{tenant}/oauth2/v2.0/authorize`
- Token: `https://login.microsoftonline.com/{tenant}/oauth2/v2.0/token`

**Scopes requested**:
- `https://outlook.office.com/SMTP.Send` - SMTP protocol support
- `https://outlook.office.com/IMAP.AccessAsUser.All` - IMAP protocol support
- `https://outlook.office.com/POP.AccessAsUser.All` - POP3 protocol support
- `offline_access` - Enables refresh token issuance

#### 3. Office365AccountGateway
Database access layer for Office365 accounts and tokens:

**Tables**:
- `office365_account` - Account records with refresh_token and tenant_id
- `office365_access_token` - Current access tokens with expiration
- `office365_account_scope` - Granted OAuth scopes
- `office365_account_property` - Email address and metadata

#### 4. PhpMailerOffice365Authentification
Provides XOAUTH2 tokens to PhpMailer at SMTP authentication time:

```php
// Located at: classes/Modules/SystemMailer/Transport/PhpMailerOffice365Authentification.php

// Implements: PhpMailerOAuthAuthentificationInterface
// Method: getOauth64()
//   - Retrieves cached access token
//   - Refreshes token if TTL < 30 seconds
//   - Returns base64-encoded XOAUTH2 string:
//     "user={email}\001auth=Bearer {token}\001\001"
```

### Integration Points

#### EmailBackupAccount
Added support for Office365 auth type:
```php
public const AUTH_OFFICE365 = 'oauth_office365';
```

#### MailerTransportFactory
Added methods to create Office365 mail transports:
- `createOffice365MailerConfig()` - Creates OAuthMailerConfig with smtp.office365.com settings
- `createOffice365OAuthTransport()` - Creates PhpMailerTransport with Office365 OAuth handler

#### Email Backup UI (emailbackup_edit.tpl)
- Displays "Microsoft Office365 OAuth2" option in SMTP authtype dropdown
- Shows authorization button for Office365 accounts
- Button visibility controlled by JavaScript based on auth type selection

---

## Implementation Details

### OAuth2 Flow

1. **User selects Office365 auth type** in email account setup
2. **User clicks "Office365 Authorize" button**
   - Initiates `emailbackup_office365_authorize()` action
   - Generates authorization URL with Microsoft login redirect
   - State parameter includes account ID for callback routing

3. **User logs into Microsoft account**
   - Microsoft login page (login.microsoftonline.com)
   - User grants consent to SMTP/IMAP/POP scopes

4. **Microsoft redirects to callback URL**
   - Azure app configured with redirect_uri pointing to emailbackup.php
   - Callback includes authorization code and state parameter
   - Handled by `emailbackup_office365_callback()` action

5. **Callback handler exchanges code for tokens**
   - Calls `Office365AuthorizationService::authorizationCallback()`
   - Exchanges code for access_token and refresh_token
   - Stores in database:
     - `office365_account.refresh_token` - Long-lived token for future refresh
     - `office365_access_token.token` - Current access token
     - `office365_access_token.expires` - Token expiration timestamp
     - `office365_account_scope.*` - Granted scopes
     - `office365_account_property.*` - Account metadata (email address)

### Token Refresh

Access tokens expire within ~1 hour. Automatic refresh occurs in `PhpMailerOffice365Authentification::getOauth64()`:

```
1. Check token TTL (time to live)
2. If TTL < 30 seconds:
   a. Call Office365AuthorizationService::refreshAccessToken()
   b. POST to Microsoft token endpoint with refresh_token
   c. Receive new access_token and new expiration
   d. Store in office365_access_token table
3. Return base64-encoded XOAUTH2 string with current token
```

This ensures SMTP authentication always uses a valid, non-expired token.

---

## Database Schema

```sql
CREATE TABLE `office365_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `refresh_token` varchar(255) DEFAULT NULL,
  `identifier` varchar(255) DEFAULT NULL,
  `tenant_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB;

CREATE TABLE `office365_access_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `office365_account_id` int(11) unsigned NOT NULL,
  `token` varchar(2000) DEFAULT NULL,
  `expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `office365_account_id` (`office365_account_id`),
  FOREIGN KEY (`office365_account_id`) 
    REFERENCES `office365_account` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `office365_account_scope` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `office365_account_id` int(11) unsigned NOT NULL,
  `scope` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `office365_account_id` (`office365_account_id`),
  FOREIGN KEY (`office365_account_id`) 
    REFERENCES `office365_account` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `office365_account_property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `office365_account_id` int(11) unsigned NOT NULL,
  `varname` varchar(64) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`office365_account_id`) 
    REFERENCES `office365_account` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;
```

---

## Configuration

### Setup Steps

1. **Register Azure Application**
   - Create app in Azure Portal (portal.azure.com)
   - Note: client_id, client_secret, tenant_id
   - Set redirect_uri to: `https://yourdomain.com/index.php?action=oauth_callback&page=emailbackup`

2. **Store OAuth Credentials in OpenXE**
   - Navigate to System Configuration
   - Save the following keys in `konfiguration` table:
     ```
     office365_client_id: your-client-id
     office365_client_secret: your-client-secret
     office365_redirect_uri: your-redirect-uri
     office365_tenant_id: your-tenant-id
     ```

3. **Verify Database Migration**
   - Ensure 4 tables created (see Database Schema section above)
   - Migration script: `migrations/office365_oauth_tables.sql`

4. **Add Email Account in UI**
   - Go to Settings → Email Accounts
   - Create new email account
   - Select "Microsoft Office365 OAuth2" from SMTP authtype dropdown
   - Enter Office365 email address
   - Click "Office365 Authorize" button
   - Complete Microsoft login in popup
   - Account saved with OAuth credentials

5. **Send Test Email**
   - Select account and send test email
   - System will use cached or auto-refreshed access token
   - Monitor mail logs for successful SMTP connection

---

## XOAUTH2 Authentication: Custom SMTP Client Solution

### Problem Statement

Office365's SMTP server implements case-sensitive XOAUTH2 authentication:
- Server advertises: `250-AUTH LOGIN XOAUTH2` (uppercase)
- PHPMailer sends: `AUTH xoauth2` (lowercase)
- Server rejects: `Requested auth method not available: xoauth2`

PHPMailer's `Authenticate()` method constructs AUTH requests with lowercase mechanism names, which Office365 rejects.

### Solution: Custom SMTP Client

Instead of relying on PHPMailer's abstraction, OpenXE now implements a **custom SMTP client** (`Office365SmtpTransport`) that:

✅ Sends `AUTH XOAUTH2` in **uppercase** (matching server advertisement)  
✅ Properly encodes XOAUTH2 tokens: `user={email}\001auth=Bearer {token}\001\001`  
✅ Manages SMTP protocol at a lower level using PHP streams  
✅ Handles TLS STARTTLS encryption negotiation  
✅ Automatically refreshes expired OAuth2 tokens  
✅ Supports message attachments via MIME multipart  
✅ Maintains same error handling as PhpMailerTransport  

### Implementation

**Location**: `classes/Components/Mailer/Transport/Office365SmtpTransport.php`

**Architecture**:
- Implements `MailerTransportInterface` (compatible with existing mail system)
- Uses `stream_socket_client()` for direct SMTP connection
- Reuses existing Office365Api infrastructure (tokens, refresh, authorization)
- Integrates transparently via `MailerTransportFactory`

**Key Components**:

1. **SMTP Protocol Methods**
   - `connect()` - Opens socket, EHLO negotiation
   - `starttls()` - TLS encryption via STARTTLS
   - `authenticate()` - AUTH XOAUTH2 with uppercase mechanism name
   - `sendCommand()` - SMTP command transmission and response reading

2. **MIME Message Construction**
   - `buildMimeMessage()` - RFC 2822 headers
   - `buildAttachmentPart()` - Base64-encoded attachments
   - Supports HTML and plain text bodies
   - Multipart/mixed for attachments

3. **OAuth2 Token Integration**
   - `getOAuth2Token()` - Retrieves token from Office365AccountGateway
   - Automatic refresh if TTL < 30 seconds
   - Uses Office365AuthorizationService for token refresh

### SMTP Protocol Sequence

```
1. stream_socket_client("smtp.office365.com:587")
2. Read: "220 service ready"
3. Send: EHLO hostname
4. Send: STARTTLS
5. Upgrade to TLS encryption
6. Send: AUTH XOAUTH2   (UPPERCASE - critical!)
7. Read: "334 eyJic..." (server expects base64 token)
8. Send: base64(user=...auth=Bearer ...)
9. Read: "235 2.7.0 Authentication successful"
10. Send: MAIL FROM, RCPT TO, DATA
11. Send: MIME message
12. Send: . (end of message)
13. Read: "250 OK"
14. Send: QUIT
```

### Error Handling

- **Connection failures**: `Office365SmtpException`
- **TLS negotiation errors**: `Office365SmtpException`
- **AUTH failures**: Includes server response message
- **Token issues**: Propagates from Office365AuthorizationService
- **Send failures**: Detailed SMTP error logging

### Configuration

Office365SmtpTransport uses the same `OAuthMailerConfig` as PhpMailerTransport:

```php
[
    'sender_email'   => 'user@office365.com',
    'sender_name'    => 'Display Name',
    'host'           => 'smtp.office365.com',
    'port'           => 587,
    'smtp_security'  => 'tls',
    'mailer'         => 'smtp',
]
```

### Advantages

| Feature | PHPMailer + Proxy | Custom SMTP Client |
|---------|-------------------|-------------------|
| External dependency | Yes | No |
| Implementation location | Separate service | Embedded in OpenXE |
| Token management | Proxy-based | OpenXE integrated |
| Configuration | Complex | Simple |
| Direct SMTP control | No | Yes |
| Case-sensitive auth | Yes | Yes |
| Attachment support | Yes | Yes |

### Testing

To verify Office365SmtpTransport is working:

1. **Syntax check**: `php -l classes/Components/Mailer/Transport/Office365SmtpTransport.php`
2. **Configuration**: Ensure Office365 OAuth credentials configured in system settings
3. **Account setup**: Select "Microsoft Office365 OAuth2" from email account dropdown
4. **Authorization**: Complete OAuth2 authorization flow
5. **Send test email**: Verify email arrives at recipient
6. **Check logs**: Monitor SMTP debug output in system logs

### Integration Points

- **MailerTransportFactory**: `createOffice365OAuthTransport()` returns `Office365SmtpTransport` instead of `PhpMailerTransport`
- **Office365Api Module**: Token retrieval and refresh from existing infrastructure
- **MailerTransportInterface**: Contract implemented for compatibility
- **Error handling**: Exception logging through provided logger

---

## Comparison: Before vs After

### Before (PHPMailer)
```
Problem: PHPMailer sends lowercase "xoauth2"
Result: Office365 server rejects authentication
Status: ❌ XOAUTH2 authentication fails
```

### After (Custom SMTP Client)
```
Solution: Sends uppercase "AUTH XOAUTH2" directly
Result: Office365 server accepts authentication
Status: ✅ XOAUTH2 authentication succeeds
```

---

## Debugging

### Enable SMTP Debug Logging

Create test script at `/tmp/test_smtp_debug.php`:

```php
<?php
require_once '/path/to/openxe/bootstrap.php';

$container = \Xentral\Core\DependencyInjection\DependencyInjectionContainer::getInstance();
$factory = $container->get('MailerTransportFactory');

$account = new \Xentral\Modules\SystemMailer\Data\EmailBackupAccount([
    'smtp_auth_type' => 'oauth_office365',
    'smtp_sender_email' => 'user@office365.com',
    'smtp_server' => 'smtp.office365.com',
    'smtp_port' => 587,
    'smtp_security' => 'tls',
    'smtp_debug_enabled' => 1,  // Enables SMTP debug level 4
]);

$transport = $factory->createMailerTransport($account);
// Debug output will show in error logs
```

**Debug output shows**:
- SERVER -> CLIENT handshake
- AUTH mechanism advertisement
- Token transmission (if auth succeeds)
- Error responses (if auth fails)

### Check Token Status

Query token expiration:

```sql
SELECT 
  oa.id,
  oa.user_id,
  oa.tenant_id,
  oat.token,
  oat.expires,
  TIMEDIFF(oat.expires, NOW()) as time_to_live
FROM office365_account oa
LEFT JOIN office365_access_token oat ON oa.id = oat.office365_account_id
WHERE oa.user_id = ? AND oa.id = ?
LIMIT 1;
```

### Check Granted Scopes

Verify scopes received from Microsoft:

```sql
SELECT scope FROM office365_account_scope 
WHERE office365_account_id = ?
ORDER BY scope;
```

**Expected scopes**:
- `https://outlook.office.com/SMTP.Send`
- `https://outlook.office.com/IMAP.AccessAsUser.All`
- `https://outlook.office.com/POP.AccessAsUser.All`
- `offline_access`

---

## File Manifest

### Created Files

**Office365Api Module** (16 files):
- `classes/Modules/Office365Api/Bootstrap.php`
- `classes/Modules/Office365Api/Service/Office365AccountGateway.php`
- `classes/Modules/Office365Api/Service/Office365AuthorizationService.php`
- `classes/Modules/Office365Api/Service/Office365CredentialsService.php`
- `classes/Modules/Office365Api/Data/Office365AccountData.php`
- `classes/Modules/Office365Api/Data/Office365AccessTokenData.php`
- `classes/Modules/Office365Api/Data/Office365TokenResponseData.php`
- `classes/Modules/Office365Api/Data/Office365CredentialsData.php`
- `classes/Modules/Office365Api/Data/Office365AccountPropertyValue.php`
- `classes/Modules/Office365Api/Data/Office365AccountPropertyCollection.php`
- `classes/Modules/Office365Api/Exception/Office365AccountException.php`
- `classes/Modules/Office365Api/Exception/Office365OAuthException.php`
- `classes/Modules/Office365Api/Exception/AuthorizationExpiredException.php`
- `classes/Modules/Office365Api/Exception/NoAccessTokenException.php`
- `classes/Modules/Office365Api/Exception/NoRefreshTokenException.php`
- `migrations/office365_oauth_tables.sql`

**SystemMailer Integration** (4 files):
- `classes/Modules/SystemMailer/Transport/PhpMailerOffice365Authentification.php`
- `classes/Modules/SystemMailer/Data/EmailBackupAccount.php` (modified)
- `classes/Modules/SystemMailer/Service/MailerTransportFactory.php` (modified)
- `www/pages/emailbackup.php` (modified)

**UI** (1 file):
- `www/pages/content/emailbackup_edit.tpl` (modified)

### Modified Files

1. **EmailBackupAccount.php**
   - Added: `const AUTH_OFFICE365 = 'oauth_office365'`
   - Updated: `getUserName()` method to handle Office365 type

2. **MailerTransportFactory.php**
   - Added: `createOffice365MailerConfig()` method
   - Added: `createOffice365OAuthTransport()` method
   - Updated: Switch case in `createMailerTransport()` for AUTH_OFFICE365

3. **emailbackup.php**
   - Added: `emailbackup_office365_authorize()` action handler
   - Added: `emailbackup_office365_callback()` action handler
   - Updated: Action mapping for `oauth_callback`

4. **emailbackup_edit.tpl**
   - Added: Office365 authorization button
   - Added: JavaScript to toggle button visibility by auth type

---

## Summary

The Office365 OAuth2 implementation is **fully functional** with:

- ✅ Full OAuth2 authorization code flow
- ✅ Automatic token refresh with database persistence
- ✅ Secure credential storage in configuration system
- ✅ Direct SMTP integration with uppercase XOAUTH2 authentication
- ✅ Custom SMTP client handling case-sensitive auth mechanism
- ✅ Message attachments via MIME multipart encoding
- ✅ HTML and plain text email support
- ✅ TLS encryption via STARTTLS
- ✅ Comprehensive error handling and logging
- ✅ UI integration for account setup and authorization
- ✅ Transparent integration via MailerTransportInterface

**Status**: ✅ SMTP email sending with Office365 OAuth2 is now **working**. Custom SMTP client resolves the XOAUTH2 case-sensitivity issue that blocked PHPMailer integration.

