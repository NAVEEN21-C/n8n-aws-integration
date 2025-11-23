# n8n AWS Integration

Complete n8n automation server setup for AWS EC2 with file upload integration.

## 🚀 Features

- **n8n Workflow Automation** running in Docker
- **Daily Joke Email** workflow (scheduled automation)
- **File Upload Notifications** via webhook
- **PHP Upload Site Integration** with real-time alerts
- **Production-Ready** configuration for AWS EC2

## 📋 Prerequisites

- AWS EC2 instance (Amazon Linux 2023 recommended)
- Domain name (e.g., DuckDNS)
- Email account for SMTP (Gmail, Outlook, etc.)
- SSH access with `.pem` key

## 🛠️ Quick Start

### 1. Deploy n8n on AWS EC2

```bash
# SSH into your EC2 instance
ssh -i your-key.pem ec2-user@your-ec2-ip

# Install Docker and Docker Compose
sudo dnf install docker -y
sudo systemctl start docker
sudo systemctl enable docker
sudo usermod -aG docker ec2-user

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Clone this repository
git clone https://github.com/NAVEEN21-C/n8n-aws-integration.git
cd n8n-aws-integration

# Start n8n
cd n8n-deployment
docker-compose up -d
```

### 2. Configure Firewall

```bash
# Open required ports
sudo firewall-cmd --permanent --add-port=5678/tcp
sudo firewall-cmd --reload
```

**AWS Security Group:** Add inbound rule for port `5678` (TCP)

### 3. Access n8n

Navigate to: `http://YOUR_EC2_IP:5678`

Create your owner account and import the workflows.

### 4. Import Workflows

1. Login to n8n
2. Click **"Import from File"**
3. Upload workflows from `workflows/` directory:
   - `daily-joke.json` - Scheduled daily joke emails
   - `file-upload-alert.json` - Upload notifications

### 5. Configure Email Credentials

For each workflow:
1. Open the workflow
2. Double-click the **Send Email** node
3. Add your SMTP credentials:
   - **Host:** `smtp.gmail.com` (for Gmail)
   - **Port:** `587`
   - **User:** Your email
   - **Password:** App password (see [Gmail App Passwords](https://support.google.com/accounts/answer/185833))

### 6. Activate Workflows

Toggle the **Active** switch (top-right) for each workflow.

## 🔗 Upload Site Integration

### Deploy the PHP Upload Site

```bash
# Create upload directory
sudo mkdir -p /var/www/html/secondsite
sudo chown -R ec2-user:ec2-user /var/www/html/secondsite

# Copy integration files
cp integrations/upload.php /var/www/html/secondsite/
cp integrations/index.html /var/www/html/secondsite/

# Install PHP
sudo dnf install php php-cli -y

# Run with PM2 (process manager)
npm install -g pm2
pm2 start "php -S 0.0.0.0:8080 -t /var/www/html/secondsite" --name php-site
pm2 save
pm2 startup
```

### Configure NGINX (Optional)

If you want to use a reverse proxy:

```bash
sudo cp nginx/mysite.conf /etc/nginx/conf.d/
sudo systemctl restart nginx
```

## 📁 Repository Structure

```
n8n-aws-integration/
├── README.md                          # This file
├── n8n-deployment/
│   └── docker-compose.yml            # n8n Docker configuration
├── workflows/
│   ├── daily-joke.json               # Daily joke workflow
│   └── file-upload-alert.json        # Upload notification webhook
├── integrations/
│   ├── upload.php                    # Modified upload handler
│   └── index.html                    # Upload page
├── nginx/
│   └── mysite.conf                   # NGINX reverse proxy config
└── docs/
    ├── TROUBLESHOOTING.md            # Common issues
    └── HTTPS_SETUP.md                # SSL certificate guide
```

## 🔧 Configuration

### Environment Variables (docker-compose.yml)

Key settings in `n8n-deployment/docker-compose.yml`:

```yaml
- N8N_HOST=YOUR_EC2_IP          # Change this!
- N8N_BASIC_AUTH_USER=admin
- N8N_BASIC_AUTH_PASSWORD=password  # Change this!
```

### Webhook Integration

The upload site triggers n8n via webhook:

```php
$webhookUrl = 'http://localhost:5678/webhook/file-uploaded';
```

This sends file metadata to n8n, which then emails you.

## 📚 Documentation

- [Troubleshooting Guide](docs/TROUBLESHOOTING.md)
- [HTTPS Setup with Let's Encrypt](docs/HTTPS_SETUP.md)

## 🎯 Workflows Explained

### 1. Daily Joke (Scheduled)

**Trigger:** Every day at 8:00 AM  
**Action:** Fetches a random joke and emails it

### 2. File Upload Alert (Webhook)

**Trigger:** When file uploaded to PHP site  
**Action:** Sends email with filename, size, and link

## 🐛 Troubleshooting

**Webhook not working?**
- Ensure workflow is **Active** (toggle in top-right)
- Check n8n logs: `docker logs n8n-n8n-1`
- Verify PHP `curl` extension: `php -m | grep curl`

**Can't access n8n?**
- Check AWS Security Group (port 5678)
- Verify firewall: `sudo firewall-cmd --list-ports`
- Check container status: `docker ps`

## 📝 License

MIT

## 🤝 Contributing

Created for portable n8n deployment on AWS. Feel free to fork and customize!

## 📧 Support

For issues, check [docs/TROUBLESHOOTING.md](docs/TROUBLESHOOTING.md)
