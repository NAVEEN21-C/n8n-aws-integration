# Push to GitHub - Quick Guide

## Step 1: Create Repository on GitHub

1. Go to https://github.com/NAVEEN21-C
2. Click **"New"** (green button)
3. Repository name: `n8n-aws-integration`
4. Description: `n8n automation server on AWS with file upload integration`
5. **Public** or **Private** (your choice)
6. **DO NOT** initialize with README (we already have one)
7. Click **"Create repository"**

## Step 2: Push Your Code

### Option A: Using HTTPS (Recommended for beginners)

```bash
cd C:\Users\asus\Desktop\n8n-aws-integration

# Initialize git
git init

# Configure git
git config user.name "NAVEEN21-C"
git config user.email "your-email@example.com"

# Add all files
git add .

# Create initial commit
git commit -m "Initial commit: n8n AWS integration"

# Add GitHub remote
git remote add origin https://github.com/NAVEEN21-C/n8n-aws-integration.git

# Push to GitHub
git branch -M main
git push -u origin main
```

**Note:** You'll be asked for your GitHub username and password. For password, use a **Personal Access Token** (not your GitHub password).

### Option B: Using SSH (More secure)

First, set up SSH key (one-time setup):

```bash
# Generate SSH key
ssh-keygen -t ed25519 -C "your-email@example.com"
# Press Enter to accept default location
# Enter passphrase (or leave empty)

# Copy public key
cat ~/.ssh/id_ed25519.pub
```

Add this public key to GitHub:
1. Go to GitHub → Settings → SSH and GPG keys
2. Click "New SSH key"
3. Paste the public key
4. Click "Add SSH key"

Then push:

```bash
cd C:\Users\asus\Desktop\n8n-aws-integration

git init
git config user.name "NAVEEN21-C"
git config user.email "your-email@example.com"
git add .
git commit -m "Initial commit: n8n AWS integration"

# Add GitHub remote (SSH)
git remote add origin git@github.com:NAVEEN21-C/n8n-aws-integration.git

git branch -M main
git push -u origin main
```

## Step 3: Verify

Go to https://github.com/NAVEEN21-C/n8n-aws-integration

You should see all your files!

## Step 4: Future Updates

After making changes:

```bash
git add .
git commit -m "Description of changes"
git push
```

## Creating a Personal Access Token (for HTTPS)

1. Go to GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic)
2. Click "Generate new token" → "Generate new token (classic)"
3. Note: "n8n-aws-integration"
4. Expiration: Choose duration
5. Select scopes: 
   - ✅ `repo` (Full control of private repositories)
6. Click "Generate token"
7. **COPY THE TOKEN IMMEDIATELY** (you won't see it again)
8. Use this token as your password when pushing

## Quick Commands Reference

```bash
# Check status
git status

# View commit history
git log --oneline

# Create a new branch
git checkout -b feature-name

# Switch branches
git checkout main

# Pull latest changes
git pull

# Clone on another machine
git clone https://github.com/NAVEEN21-C/n8n-aws-integration.git
```

## 🎉 Done!

Your n8n setup is now version-controlled and shareable!
