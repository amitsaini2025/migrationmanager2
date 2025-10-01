# Hubdoc Email Configuration

## Overview
The Hubdoc email functionality has been configured to use an environment variable for the email address where invoices are sent for processing.

## Environment Variable Setup

### 1. Add to .env file
Add the following line to your `.env` file:

```env
HUBDOC_EMAIL=easyvisa.1ae4@app.hubdoc.com
```

### 2. Usage in Code
The email address is accessed directly from the environment variable in the `ClientsController`:

```php
Mail::to(env('HUBDOC_EMAIL', 'easyvisa.1ae4@app.hubdoc.com'))->send(new \App\Mail\HubdocInvoiceMail($invoiceData));
```

## Benefits
- **Environment-specific**: Different email addresses for different environments (development, staging, production)
- **Security**: Email address is not hardcoded in the source code
- **Flexibility**: Easy to change without modifying code
- **Fallback**: Default value provided if environment variable is not set

## Example .env Entries
```env
# Development
HUBDOC_EMAIL=dev-test@hubdoc.com

# Staging
HUBDOC_EMAIL=staging@hubdoc.com

# Production
HUBDOC_EMAIL=easyvisa.1ae4@app.hubdoc.com
```

## Verification
To verify the configuration is working:
1. Check that the environment variable is set: `echo $HUBDOC_EMAIL`
2. Test the "Sent To Hubdoc" button functionality
3. Verify emails are sent to the correct address
