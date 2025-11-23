# HTTPS Setup with Let's Encrypt

## 🔒 Secure Your n8n Instance with SSL

### Prerequisites

- Domain name pointing to your EC2 IP (e.g., `n8n.yourdomain.com`)
- Ports 80 and 443 open in AWS Security Group
- NGINX installed

### Step 1: Install Certbot

```bash
# Install Certbot for Amazon Linux 2023
sudo dnf install certbot python3-certbot-nginx -y
```

### Step 2: Configure DNS

Point your domain to your EC2 public IP:

```
Type: A Record
Name: n8n (or @ for root domain)
Value: YOUR_EC2_PUBLIC_IP
TTL: 300
```

**For DuckDNS:**
```bash
# Update DuckDNS
curl "https://www.duckdns.org/update?domains=YOUR_SUBDOMAIN&token=YOUR_TOKEN&ip=YOUR_EC2_IP"
```

### Step 3: Install NGINX

```bash
sudo dnf install nginx -y
sudo systemctl start nginx
sudo systemctl enable nginx
```

### Step 4: Configure NGINX for n8n

Create `/etc/nginx/conf.d/n8n.conf`:

```nginx
server {
    listen 80;
    server_name n8n.yourdomain.com;

    location / {
        proxy_pass http://localhost:5678;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
        
        # WebSocket support
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

Test and reload:
```bash
sudo nginx -t
sudo systemctl reload nginx
```

### Step 5: Open Firewall Ports

```bash
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --reload
```

**AWS Security Group:**
- Port 80 (HTTP)
- Port 443 (HTTPS)

### Step 6: Obtain SSL Certificate

```bash
sudo certbot --nginx -d n8n.yourdomain.com
```

Follow the prompts:
- Enter email
- Agree to terms
- Choose whether to redirect HTTP to HTTPS (recommended: Yes)

### Step 7: Update n8n Configuration

Edit `n8n-deployment/docker-compose.yml`:

```yaml
environment:
  - N8N_HOST=n8n.yourdomain.com
  - N8N_PROTOCOL=https
  - N8N_PORT=443
  # Remove N8N_SECURE_COOKIE=false
```

Restart n8n:
```bash
cd n8n-deployment
docker-compose down
docker-compose up -d
```

### Step 8: Verify

Visit: `https://n8n.yourdomain.com`

You should see:
- 🔒 Padlock in browser
- No security warnings
- n8n login page

### Auto-Renewal

Certbot automatically adds a renewal cron job. Verify:

```bash
sudo systemctl list-timers | grep certbot
```

Test renewal:
```bash
sudo certbot renew --dry-run
```

## 🔐 Update Webhook URLs

After HTTPS setup, update your PHP script:

```php
// In integrations/upload.php
$webhookUrl = 'https://n8n.yourdomain.com/webhook/file-uploaded';
```

## 📋 Final NGINX Configuration

After Certbot, `/etc/nginx/conf.d/n8n.conf` should look like:

```nginx
server {
    server_name n8n.yourdomain.com;

    location / {
        proxy_pass http://localhost:5678;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    listen 443 ssl;
    ssl_certificate /etc/letsencrypt/live/n8n.yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/n8n.yourdomain.com/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;
}

server {
    if ($host = n8n.yourdomain.com) {
        return 301 https://$host$request_uri;
    }

    listen 80;
    server_name n8n.yourdomain.com;
    return 404;
}
```

## 🎯 Benefits of HTTPS

- ✅ Secure credentials transmission
- ✅ Browser security features enabled
- ✅ SEO benefits
- ✅ Professional appearance
- ✅ Required for some integrations

## ⚠️ Important Notes

1. **Certificate expires in 90 days** (auto-renews)
2. **Keep port 80 open** (for renewal challenges)
3. **Backup before changes**
4. **Test renewal process** regularly
