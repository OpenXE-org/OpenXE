# Office365 OAuth2 Integration

This document describes how to set up and use Microsoft Office365 OAuth2 authentication for email accounts in OpenXE, and explains the underlying implementation.

The integration provides:
- OAuth2 authorization code grant flow with automatic token refresh
- Secure credential storage (no passwords required)
- A custom SMTP client supporting Office365's case-sensitive `XOAUTH2` mechanism
- Full support for HTML, plain text and attachments via MIME

---

## Part 1 — Setup Guide

### Prerequisites

- Microsoft Azure account with admin access
- Running OpenXE installation with database access

### Step 1: Azure App Registration

#### 1.1 Register an Application

1. Go to the [Azure Portal](https://portal.azure.com/)
2. Navigate to **Azure Active Directory → App registrations → New registration**
3. Enter:
   - **Name**: `OpenXE Office365 Integration` (or similar)
   - **Supported account types**: *Accounts in any organizational directory and personal Microsoft accounts*
4. Click **Register**

#### 1.2 Create Client Secret

1. Open **Certificates & secrets → New client secret**
2. Description: `OpenXE Integration`
3. Expiry: 24 months (or as desired)
4. **Copy the Value** (not the Secret ID) — this is your `CLIENT_SECRET`

#### 1.3 Configure API Permissions

1. Open **API permissions → Add a permission → Microsoft Graph → Delegated permissions**
2. Add:
   - `Mail.Read`
   - `Mail.ReadWrite`
   - `Mail.Send`
   - `offline_access` (required for refresh tokens)
3. Click **Grant admin consent for [Your Organization]**

#### 1.4 Configure Redirect URI

1. Open **Authentication → Add URI**
2. Enter your callback URL:
   ```
   https://your-openxe-domain.com/index.php?module=emailbackup&action=oauth_callback&provider=office365
   ```
3. Save

#### 1.5 Collect Required Values

From the **Overview** page note:
- **Application (client) ID** → `CLIENT_ID`
- **Directory (tenant) ID** → `TENANT_ID`

### Step 2: Database Migration

Run the migration to create the necessary Office365 tables (see [Database Schema](#database-schema)).

```bash
cd /path/to/OpenXE
php migrations/run_migration.php
```

Alternatively:

```bash
mysql -u openxe -p openxe < migrations/office365_oauth_tables.sql
```

### Step 3: Store OAuth Credentials in OpenXE

Login to OpenXE as admin, open the Office365 OAuth Configuration in the system settings, and enter:
- **Client ID**: from step 1.5
- **Client Secret**: from step 1.2
- **Redirect URI**: from step 1.4
- **Tenant ID**: from step 1.5

The values are stored in the `konfiguration` table under the keys
`office365_client_id`, `office365_client_secret`, `office365_redirect_uri`, `office365_tenant_id`.

### Step 4: Configure an Email Account

1. Open **E-Mail Accounts** (E-Mail Backup) and create a new account
2. Fill in basic information (email address, display name)
3. In the **SMTP** section:
   - Enable **SMTP benutzen** (Use SMTP)
   - **Server**: `smtp.office365.com`
   - **Encryption**: TLS
   - **Port**: `587`
   - **Auth Type**: `Microsoft Office365 OAuth2`
4. Save

### Step 5: Authorize the Account

1. Click the **Office365 Authorize** button on the account
2. Sign in with the matching Office365 account at Microsoft
3. Grant the requested permissions
4. You are redirected back to OpenXE; the account is now ready

After authorization the database holds:
- `office365_account.refresh_token`
- `office365_access_token.token` and `expires`

### Step 6: Send a Test Email

Use the **Test Mail Sending** action on the account. On success, the access token is fetched (and refreshed if needed) and the mail is sent through the custom SMTP client.

---

## Part 2 — Architecture & Implementation

### Module Structure

The Office365Api module mirrors the existing GoogleApi module:

```
classes/Modules/Office365Api/
├── Bootstrap.php                          # DI container registration
├── Service/
│   ├── Office365AccountGateway.php       # Database access layer
│   ├── Office365AuthorizationService.php # OAuth2 flow handler
│   └── Office365CredentialsService.php   # App credentials management
├── Data/
│   ├── Office365AccountData.php
│   ├── Office365AccessTokenData.php
│   ├── Office365TokenResponseData.php
│   ├── Office365CredentialsData.php
│   ├── Office365AccountPropertyValue.php
│   └── Office365AccountPropertyCollection.php
└── Exception/
    ├── Office365AccountException.php
    ├── Office365OAuthException.php
    ├── AuthorizationExpiredException.php
    ├── NoAccessTokenException.php
    └── NoRefreshTokenException.php
```

### Key Components

- **Office365CredentialsService** — Manages OAuth app credentials stored in `konfiguration`.
- **Office365AuthorizationService** — Handles the authorization code grant flow.
  - Authorization endpoint: `https://login.microsoftonline.com/{tenant}/oauth2/v2.0/authorize`
  - Token endpoint: `https://login.microsoftonline.com/{tenant}/oauth2/v2.0/token`
  - Scopes requested:
    - `https://outlook.office.com/SMTP.Send`
    - `https://outlook.office.com/IMAP.AccessAsUser.All`
    - `https://outlook.office.com/POP.AccessAsUser.All`
    - `offline_access`
- **Office365AccountGateway** — Database access for accounts, tokens, scopes and properties.
- **PhpMailerOffice365Authentification** — Provides XOAUTH2 tokens at SMTP authentication time, refreshing the access token when its TTL drops below 30 seconds.

### Integration with SystemMailer

- `EmailBackupAccount::AUTH_OFFICE365 = 'oauth_office365'`
- `MailerTransportFactory` handles `AUTH_OFFICE365` and returns an `Office365SmtpTransport`.
- `SystemMailer::composeAndSendEmail()` accepts the Office365 auth type alongside SMTP and Gmail.
- `emailbackup.php` exposes the OAuth authorize/callback actions; `emailbackup_edit.tpl` renders the dropdown entry and the Authorize button.

### OAuth2 Flow

1. User selects Office365 auth type for an email account.
2. User clicks **Office365 Authorize** → `emailbackup_office365_authorize()` builds the Microsoft login URL.
3. User authenticates with Microsoft and grants consent.
4. Microsoft redirects back to the callback (`emailbackup_office365_callback()`).
5. The callback exchanges the authorization code for `access_token` and `refresh_token` and stores them in the database.

### Token Refresh

Access tokens expire after about an hour. `PhpMailerOffice365Authentification::getOauth64()` checks the TTL on every send and, if less than 30 seconds remain, calls `Office365AuthorizationService::refreshAccessToken()` (POST to the token endpoint with the stored refresh token) and updates `office365_access_token`. Refresh is fully transparent to the caller.

### Custom SMTP Client (`Office365SmtpTransport`)

Located at `classes/Components/Mailer/Transport/Office365SmtpTransport.php`.

**Why a custom client?** Office365 advertises `AUTH LOGIN XOAUTH2` (uppercase) and only accepts the uppercase mechanism name. PHPMailer sends `AUTH xoauth2` (lowercase) and is rejected with *"Requested auth method not available: xoauth2"*.

**What it does:**
- Implements `MailerTransportInterface` so it integrates transparently into the existing mail pipeline.
- Opens an SMTP connection via `stream_socket_client()` and negotiates `STARTTLS`.
- Sends `AUTH XOAUTH2` (uppercase) followed by the base64-encoded XOAUTH2 token (`user={email}\x01auth=Bearer {token}\x01\x01`).
- Builds RFC 2822 / MIME multipart messages with full attachment support.
- Refreshes OAuth tokens via the existing `Office365AuthorizationService` infrastructure.

**Typical SMTP exchange:**

```
1. Connect smtp.office365.com:587  → 220 service ready
2. EHLO hostname                   → 250-AUTH LOGIN XOAUTH2 …
3. STARTTLS                        → 220 Ready, upgrade to TLS
4. AUTH XOAUTH2                    → 334 (server prompt)
5. <base64 XOAUTH2 token>          → 235 2.7.0 Authentication successful
6. MAIL FROM / RCPT TO / DATA / .  → 250 Message queued
7. QUIT
```

### Database Schema

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
  KEY `office365_account_id` (`office365_account_id`),
  FOREIGN KEY (`office365_account_id`)
    REFERENCES `office365_account` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;
```

### Modified Files

- `classes/Modules/SystemMailer/Data/EmailBackupAccount.php` — added `AUTH_OFFICE365` constant
- `classes/Modules/SystemMailer/Service/MailerTransportFactory.php` — added Office365 transport creation
- `classes/Modules/SystemMailer/SystemMailer.php` — allow `AUTH_OFFICE365` in `composeAndSendEmail()`
- `www/pages/emailbackup.php` — added `oauth_authorize` / `oauth_callback` action handlers
- `www/pages/content/emailbackup_edit.tpl` — added dropdown option and Authorize button

---

## Part 3 — Troubleshooting

### "Authtype error." when sending

`SystemMailer::composeAndSendEmail()` rejects accounts whose `smtp_authtype` is not in the allowed list, or whose `smtp_extra` (SMTP active flag) is `0`.

```sql
SELECT id, email, smtp_extra, smtp_authtype
FROM emailbackup
WHERE email = 'user@example.com';
```

Expected: `smtp_extra = 1`, `smtp_authtype = 'oauth_office365'`.

### "No Office365 account configured"

- Ensure the Office365 account was authorized after creation.
- The account's email address must match the Office365 account that granted consent.

### "Token refresh failed" / "Authorization expired"

```sql
SELECT refresh_token FROM office365_account WHERE id = ?;
```

If `refresh_token` is empty, re-authorize via the Office365 Authorize button. Also verify the configured `office365_client_secret` is current (Azure secrets expire).

### Check token validity

```sql
SELECT oat.token, oat.expires, TIMEDIFF(oat.expires, NOW()) AS ttl
FROM office365_account oa
LEFT JOIN office365_access_token oat ON oa.id = oat.office365_account_id
WHERE oa.id = ?;
```

### Check granted scopes

```sql
SELECT scope FROM office365_account_scope WHERE office365_account_id = ?;
```

Expected: `SMTP.Send`, `IMAP.AccessAsUser.All`, `POP.AccessAsUser.All`, `offline_access`.

### "AUTH XOAUTH2" still rejected

- Confirm the account uses `Office365SmtpTransport` (i.e. `smtp_authtype = 'oauth_office365'`). The transport sends uppercase `AUTH XOAUTH2`; PHPMailer's lowercase variant would be rejected by Office365.
- Verify the access token is current (see token validity query above).

### OAuth authorization fails

- The Redirect URI in Azure must match exactly (scheme, host, query parameters).
- Verify Client ID and Client Secret are correct.
- Confirm admin consent has been granted for the requested permissions.

### Connection refused / timeout

- Outbound TCP 587 must be allowed by the firewall.
- DNS must resolve `smtp.office365.com`.
- The PHP environment must support TLS streams.

### Logs

```bash
tail -f /path/to/OpenXE/userdata/logs/mail.log
```

Enable verbose SMTP debug output by setting the account's "SMTP Debug" flag, which raises the log level to include the full SMTP conversation.

---

## Security Notes

- **Client Secret**: never share or commit; rotate periodically.
- **Refresh Tokens**: stored in the database; treat as secrets.
- **Access Tokens**: cached short-term, transmitted only over TLS.
- **No passwords**: OAuth2 only — the user's Microsoft password is never seen by OpenXE.
- **Scope Limitation**: only the requested mail scopes are granted.

## References

- [Microsoft identity platform — OAuth2 authorization code flow](https://learn.microsoft.com/azure/active-directory/develop/v2-oauth2-auth-code-flow)
- [SMTP AUTH XOAUTH2 mechanism](https://learn.microsoft.com/exchange/client-developer/legacy-protocols/how-to-authenticate-an-imap-pop-smtp-application-by-using-oauth)
