#!/bin/bash
# GitHub Repository Initialization Script
# Run this after creating the repository on GitHub

echo "🚀 Initializing n8n AWS Integration Repository"
echo ""

# Check if git is installed
if ! command -v git &> /dev/null; then
    echo "❌ Git is not installed. Please install git first."
    exit 1
fi

# Check if we're in the right directory
if [ ! -f "README.md" ]; then
    echo "❌ README.md not found. Please run this script from the repository root."
    exit 1
fi

# Initialize git if not already initialized
if [ ! -d ".git" ]; then
    echo "📦 Initializing git repository..."
    git init
else
    echo "✅ Git repository already initialized"
fi

# Configure git user if not set
if [ -z "$(git config user.name)" ]; then
    echo "Please enter your name for git commits:"
    read git_name
    git config user.name "$git_name"
fi

if [ -z "$(git config user.email)" ]; then
    echo "Please enter your email for git commits:"
    read git_email
    git config user.email "$git_email"
fi

# Add all files
echo "📝 Adding files to git..."
git add .

# Create initial commit
echo "💾 Creating initial commit..."
git commit -m "Initial commit: n8n AWS integration setup

- Complete n8n Docker setup
- Daily joke workflow
- File upload webhook workflow
- PHP upload integration
- Comprehensive documentation"

# Add remote (user needs to provide this)
echo ""
echo "📡 To push to GitHub, run these commands:"
echo ""
echo "  git remote add origin https://github.com/NAVEEN21-C/n8n-aws-integration.git"
echo "  git branch -M main"
echo "  git push -u origin main"
echo ""
echo "Or if using SSH:"
echo ""
echo "  git remote add origin git@github.com:NAVEEN21-C/n8n-aws-integration.git"
echo "  git branch -M main"
echo "  git push -u origin main"
echo ""
echo "✅ Repository initialized! Create the repository on GitHub and run the commands above."
