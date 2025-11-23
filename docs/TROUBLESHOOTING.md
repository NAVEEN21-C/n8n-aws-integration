# Troubleshooting Guide

## Common Issues and Solutions

### 🔴 Webhook Returns 404

**Symptom:** `{"code":404,"message":"The requested webhook is not registered"}`

**Solutions:**

1. **Workflow Not Active**
   ```bash
   # Login to n8n UI
   # Open the workflow
   # Toggle the Active switch (top-right)
   ```

2. **Check Workflow Status**
   ```bash
   ssh into-ec2
   docker logs n8n-n8n-1 --tail 50
   # Look for: "Activated workflow 'File Upload Alert'"
   ```

3. **Database Issues After Version Change**
   ```bash
   # If you upgraded/downgraded n8n, reset the database
   cd n8n-deployment
   docker-compose down
   rm -rf n8n_data/database.sqlite*
   docker-compose up -d
   # Re-import workflows and re-enter credentials
   ```

### 🔴 Can't Access n8n UI

**Symptom:** Browser can't connect to `http://YOUR_IP:5678`

**Solutions:**

1. **Check AWS Security Group**
   - Go to EC2 → Security Groups
   - Add inbound rule: Port `5678`, Protocol `TCP`, Source `0.0.0.0/0`

2. **Check Firewall**
   ```bash
   sudo firewall-cmd --list-ports
   # Should show 5678/tcp
   
   # If not:
   sudo firewall-cmd --permanent --add-port=5678/tcp
   sudo firewall-cmd --reload
   ```

3. **Check Container Status**
   ```bash
   docker ps
   # Should show n8n-n8n-1 running
   
   # If not running:
   docker logs n8n-n8n-1
   ```

### 🔴 Email Not Sending

**Symptom:** Workflow executes but no email arrives

**Solutions:**

1. **Check Credentials**
   - Open workflow in n8n
   - Double-click Send Email node
   - Re-enter SMTP password
   - For Gmail: Use [App Password](https://support.google.com/accounts/answer/185833), not regular password

2. **Test SMTP Manually**
   ```bash
   # From EC2, test connection
   telnet smtp.gmail.com 587
   # Should connect
   ```

3. **Check Execution Log**
   - In n8n, click "Executions" (left sidebar)
   - Click failed execution
   - Read error message

### 🔴 Upload Site Can't Trigger Webhook

**Symptom:** File uploads successfully but workflow doesn't execute

**Solutions:**

1. **Check PHP curl Extension**
   ```bash
   php -m | grep curl
   # Should output: curl
   
   # If not installed:
   sudo dnf install php-curl -y
   pm2 restart php-site
   ```

2. **Test Webhook Manually**
   ```bash
   curl -X POST http://localhost:5678/webhook/file-uploaded \
     -H 'Content-Type: application/json' \
     -d '{"filename":"test.jpg","size":123,"url":"http://example.com"}'
   
   # Should NOT return 404
   ```

3. **Check upload.php Configuration**
   ```php
   // In upload.php, verify:
   $webhookUrl = 'http://localhost:5678/webhook/file-uploaded';
   
   // NOT http://13.49.46.92:5678 (use localhost from same server)
   ```

### 🔴 n8n Version Mismatch / Database Error

**Symptom:** `SQLITE_ERROR: no such column`

**Cause:** You downgraded n8n version (not supported)

**Solution:**
```bash
# ALWAYS move forward with versions, never backward
cd n8n-deployment

# Backup workflows first (export from UI)

# Reset database
docker-compose down
rm -rf n8n_data/
docker-compose up -d

# Re-import workflows
# Re-enter credentials
```

### 🔴 Container Keeps Restarting

**Symptom:** `docker ps` shows container restarting

**Solutions:**

1. **Check Logs**
   ```bash
   docker logs n8n-n8n-1 --tail 100
   ```

2. **Common Causes:**
   - Port 5678 already in use
   - Insufficient memory (add swap)
   - Corrupted database (delete and restart)

3. **Add Swap (if low memory)**
   ```bash
   sudo fallocate -l 2G /swapfile
   sudo chmod 600 /swapfile
   sudo mkswap /swapfile
   sudo swapon /swapfile
   echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
   ```

### 🔴 Workflows Disappeared After Restart

**Symptom:** Workflows gone after `docker-compose restart`

**Solution:**

1. **Check Volume Mount**
   ```bash
   ls -la n8n-deployment/n8n_data/
   # Should see database.sqlite
   ```

2. **Ensure Correct docker-compose.yml**
   ```yaml
   volumes:
     - ./n8n_data:/home/node/.n8n  # Relative path
   ```

## 🐛 Debug Commands

```bash
# View n8n logs
docker logs n8n-n8n-1 -f

# Check all containers
docker ps -a

# Restart n8n
cd n8n-deployment
docker-compose restart

# Full reset
docker-compose down
docker-compose up -d

# Check disk space
df -h

# Check memory
free -h

# Test network from EC2
curl -I http://localhost:5678
```

## 📞 Still Stuck?

1. Export your workflow as JSON
2. Check the n8n [Community Forum](https://community.n8n.io/)
3. Review n8n [Documentation](https://docs.n8n.io/)
