# Office365 OAuth2 Integration Setup Guide

## Overview

This document explains how to set up and configure Microsoft Office365 OAuth2 authentication for email accounts in OpenXE.

## Prerequisites

1. Microsoft Azure Account with admin access
2. OpenXE system running
3. Database access to OpenXE

## Step 1: Create Azure App Registration

### 1.1 Register an Application

1. Go to [Azure Portal](https://portal.azure.com/)
2. Navigate to **Azure Active Directory** → **App registrations** → **New registration**
3. Enter the following details:
   - **Name**: OpenXE Office365 Integration (or your preferred name)
   - **Supported account types**: Accounts in any organizational directory and personal Microsoft accounts
4. Click **Register**

### 1.2 Configure Application Credentials

1. In the app registration, go to **Certificates & secrets**
2. Click **New client secret**
3. Enter a description (e.g., "OpenXE Integration")
4. Select **Expires**: 24 months (or your preference)
5. Click **Add**
6. **Copy the Value** (not the ID) - this is your `CLIENT_SECRET` - save it securely

### 1.3 Configure API Permissions

1. In the app registration, go to **API permissions**
2. Click **Add a permission**
3. Select **Microsoft Graph**
4. Choose **Delegated permissions**
5. Search for and add:
   - `Mail.Read`
   - `Mail.ReadWrite`
   - `Mail.Send`
   - `offline_access` (for refresh tokens)
6. Click **Grant admin consent for [Your Organization]**

### 1.4 Configure Redirect URI

1. In the app registration, go to **Authentication**
2. Under **Redirect URIs**, click **Add URI**
3. Enter your callback URL (example format):
   ```
   https://your-openxe-domain.com/index.php?module=emailbackup&action=oauth_callback&provider=office365
   ```
4. Save changes

### 1.5 Get Required Information

From the **Overview** page, copy:
- **Application (client) ID** - this is your `CLIENT_ID`
- **Directory (tenant) ID** - this is your `TENANT_ID`

## Step 2: Database Migration

Run the database migration to create the necessary Office365 tables:

### Option A: Using PHP Script (Recommended)

```bash
cd /path/to/OpenXE
php migrations/run_migration.php
```

### Option B: Using MySQL CLI

```bash
mysql -u openxe -p openxe < migrations/office365_oauth_tables.sql
```

## Step 3: Configure OpenXE

### 3.1 Save Office365 Credentials in System

Login to OpenXE as admin and navigate to System Settings:

1. Go to **Einstellungen** (Settings)
2. Find the **Office365 OAuth Configuration** section
3. Enter the following information:
   - **Client ID**: `[Your CLIENT_ID from Step 1.5]`
   - **Client Secret**: `[Your CLIENT_SECRET from Step 1.2]`
   - **Redirect URI**: `[Your redirect URI from Step 1.4]`
   - **Tenant ID**: `[Your TENANT_ID from Step 1.5]`
4. Click **Save**

Alternatively, these values can be saved directly to the database:

```sql
INSERT INTO `xentral_config` (varname, value) VALUES
('office365_client_id', 'YOUR_CLIENT_ID'),
('office365_client_secret', 'YOUR_CLIENT_SECRET'),
('office365_redirect_uri', 'https://your-domain.com/callback'),
('office365_tenant_id', 'YOUR_TENANT_ID');
```

## Step 4: Configure Email Account

### 4.1 Add New Email Account

1. Go to **E-Mail Accounts** (E-Mail Backup)
2. Click **Create New** account
3. Fill in the basic information:
   - **E-Mail Address**: your.office365@company.com
   - **Display Name**: Your Name
   - **Description**: Office365 Account
4. In the **SMTP** section:
   - Enable **SMTP benutzen** (Use SMTP)
   - **Server**: `smtp.office365.com` (auto-filled)
   - **Encryption**: Select **TLS**
   - **Port**: `587`
   - **Auth Type**: Select **Office365 OAuth2**
5. Save the account

### 4.2 Authorize Office365 Account

After saving, an authorization option should appear. Click to authorize your Office365 account:

1. You'll be redirected to Microsoft login
2. Sign in with your Office365 credentials
3. Grant the requested permissions
4. You'll be redirected back to OpenXE
5. The account is now configured and ready to use

## Step 5: Verify Configuration

### Test Mail Sending

1. Go to your new Office365 email account in OpenXE
2. Click **Test Mail Sending** button
3. A test email will be sent
4. If successful, your Office365 OAuth2 integration is working!

### Check Logs

Monitor logs for any authentication errors:

```bash
tail -f /path/to/OpenXE/userdata/logs/mail.log
```

## Troubleshooting

### Error: "No Office365 account configured"

- Ensure the Office365 account was properly authorized
- Check that the email address in the account matches the authorized Office365 email
- Verify the credentials are saved in the system

### Error: "Token refresh failed"

- Check that the refresh token is properly stored in the database
- Verify the Client Secret is correct
- Check network connectivity to Microsoft endpoints

### Error: "Invalid Tenant ID"

- Verify the Tenant ID in system settings
- Ensure it's the Directory (tenant) ID, not the Application ID
- Check the Azure app registration

### OAuth Authorization Fails

- Verify the Redirect URI in Azure matches exactly
- Check that the Client ID and Secret are correct
- Ensure API permissions are granted
- Clear browser cache and try again

## Technical Architecture

The Office365 OAuth2 implementation follows these components:

### Database Schema

- **office365_account**: Stores account information and refresh tokens
- **office365_access_token**: Caches access tokens with expiration
- **office365_account_scope**: Tracks granted OAuth scopes
- **office365_account_property**: Stores account metadata (email address, etc.)

### Code Structure

```
classes/Modules/Office365Api/
├── Bootstrap.php                          # DI Container registration
├── Service/
│   ├── Office365AccountGateway.php       # Database access layer
│   ├── Office365AuthorizationService.php # OAuth2 flow handler
│   └── Office365CredentialsService.php   # Credentials management
├── Data/
│   ├── Office365AccountData.php
│   ├── Office365AccessTokenData.php
│   ├── Office365TokenResponseData.php
│   ├── Office365CredentialsData.php
│   └── Office365AccountProperty*.php
├── Exception/
│   ├── Office365OAuthException.php
│   ├── AuthorizationExpiredException.php
│   └── NoAccessTokenException.php
└── Wrapper/
    └── CompanyConfigWrapper.php
```

### Token Management

- Access tokens are cached with 1-hour expiration
- Tokens are automatically refreshed 30 seconds before expiration
- Refresh tokens are stored securely in the database
- No user interaction required for token refresh

## Security Notes

1. **Client Secret**: Never share your Client Secret - it's sensitive!
2. **Refresh Tokens**: Stored encrypted in the database
3. **Access Tokens**: Cached but not visible to users
4. **SSL/TLS**: All OAuth communications use encrypted connections
5. **Scope Limitation**: Only requested permissions are granted

## Support

For issues or questions, please refer to:
- Microsoft documentation: https://docs.microsoft.com/azure/
- OpenXE documentation
- Check system logs for detailed error messages
