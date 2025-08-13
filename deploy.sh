#!/bin/bash

# Deployment script for syncing local project to server via SFTP

# Load configuration
source deploy.config 2>/dev/null || {
    echo "❌ deploy.config file not found. Please create it with your server settings."
    exit 1
}

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Functions
log_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

log_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Function to upload files via SFTP
upload_files() {
    log_info "Uploading files via SFTP..."

    # Create SFTP batch commands
    cat > sftp_commands.tmp << EOF
cd $SERVER_PATH
put -r database/
put -r src/
put -r public/
put -r includes/
put env.php
put .htaccess
put composer.json
put composer.lock
put README.md
quit
EOF

    # Execute SFTP commands
    sftp -P $SERVER_PORT -b sftp_commands.tmp $SERVER_USER@$SERVER_HOST

    if [ $? -eq 0 ]; then
        log_success "Files uploaded successfully"
    else
        log_error "File upload failed"
        cleanup_temp_files
        exit 1
    fi

    # Clean up
    rm sftp_commands.tmp
}

# Function to run database migrations on server
run_migrations() {
    log_info "Running database migrations on server..."

    ssh -p $SERVER_PORT $SERVER_USER@$SERVER_HOST << EOF
cd $SERVER_PATH
php database/migrate.php migrate
EOF

    if [ $? -eq 0 ]; then
        log_success "Database migrations completed"
    else
        log_error "Database migrations failed"
        exit 1
    fi
}

# Function to run database seeding on server
run_seeding() {
    log_info "Seeding database on server..."

    ssh -p $SERVER_PORT $SERVER_USER@$SERVER_HOST << EOF
cd $SERVER_PATH
php database/migrate.php seed
EOF

    if [ $? -eq 0 ]; then
        log_success "Database seeding completed"
    else
        log_warning "Database seeding failed (this might be normal if data already exists)"
    fi
}

# Function to check database status on server
check_database_status() {
    log_info "Checking database status on server..."

    ssh -p $SERVER_PORT $SERVER_USER@$SERVER_HOST << EOF
cd $SERVER_PATH
php database/migrate.php status
EOF

    if [ $? -eq 0 ]; then
        log_success "Database status check completed"
    else
        log_error "Database status check failed"
    fi
}

# Function to set proper permissions
set_permissions() {
    log_info "Setting file permissions..."

    ssh -p $SERVER_PORT $SERVER_USER@$SERVER_HOST << EOF
cd $SERVER_PATH
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod 600 env.php
chmod +x database/migrate.php
EOF

    if [ $? -eq 0 ]; then
        log_success "Permissions set successfully"
    else
        log_error "Failed to set permissions"
    fi
}

# Function to backup database before deployment
backup_database() {
    log_info "Creating database backup..."

    BACKUP_NAME="backup_before_deploy_$(date +%Y%m%d_%H%M%S).sql"

    ssh -p $SERVER_PORT $SERVER_USER@$SERVER_HOST << EOF
cd $SERVER_PATH
php database/migrate.php backup $BACKUP_NAME
EOF

    if [ $? -eq 0 ]; then
        log_success "Database backup created: $BACKUP_NAME"
    else
        log_warning "Database backup failed (continuing anyway)"
    fi
}

# Function to test local environment before deployment
test_local() {
    log_info "Testing local environment..."

    # Check if required files exist
    local files_to_check=("database/Database.php" "database/Migration.php" "database/migrate.php" "env.php")

    for file in "${files_to_check[@]}"; do
        if [ ! -f "$file" ]; then
            log_error "Required file missing: $file"
            exit 1
        fi
    done

    # Test database connection locally
    php -r "
    try {
        require_once 'database/Database.php';
        \$db = Database::getInstance();
        echo 'Local database connection: OK\n';
    } catch (Exception \$e) {
        echo 'Local database connection failed: ' . \$e->getMessage() . '\n';
        exit(1);
    }
    "

    if [ $? -eq 0 ]; then
        log_success "Local environment test passed"
    else
        log_error "Local environment test failed"
        exit 1
    fi
}

# Function to cleanup temporary files
cleanup_temp_files() {
    [ -f "sftp_commands.tmp" ] && rm sftp_commands.tmp
}

# Function to show deployment summary
show_summary() {
    echo ""
    log_info "Deployment Summary"
    echo "======================="
    echo "Server: $SERVER_USER@$SERVER_HOST:$SERVER_PORT"
    echo "Path: $SERVER_PATH"
    echo "Time: $(date)"
    echo "======================="
    echo ""
}

# Main deployment process
main() {
    local deployment_type=$1

    echo -e "${BLUE}🚀 Starting deployment to server...${NC}"
    echo "===================================="

    # Always test local environment first
    test_local

    case "$deployment_type" in
        "full")
            log_info "Full deployment (backup + files + database + permissions)"
            backup_database
            upload_files
            run_migrations
            run_seeding
            set_permissions
            check_database_status
            ;;
        "files")
            log_info "Files only deployment"
            upload_files
            set_permissions
            ;;
        "db")
            log_info "Database only deployment"
            backup_database
            run_migrations
            check_database_status
            ;;
        "migrate")
            log_info "Migration only deployment"
            run_migrations
            check_database_status
            ;;
        "seed")
            log_info "Seeding only deployment"
            run_seeding
            ;;
        "backup")
            log_info "Database backup only"
            backup_database
            ;;
        "status")
            log_info "Database status check only"
            check_database_status
            ;;
        "fresh")
            log_info "Fresh deployment (backup + files + fresh database)"
            log_warning "This will drop all existing data!"
            read -p "Are you sure? (y/N): " -n 1 -r
            echo
            if [[ $REPLY =~ ^[Yy]$ ]]; then
                backup_database
                upload_files
                ssh -p $SERVER_PORT $SERVER_USER@$SERVER_HOST "cd $SERVER_PATH && php database/migrate.php fresh"
                run_seeding
                set_permissions
                check_database_status
            else
                log_info "Fresh deployment cancelled"
                exit 0
            fi
            ;;
        "test")
            log_info "Testing deployment (dry run)"
            log_success "Local tests passed"
            log_info "Would upload files to: $SERVER_USER@$SERVER_HOST:$SERVER_PATH"
            log_info "Would run migrations on server"
            log_info "Would set permissions"
            ;;
        *)
            echo -e "${BLUE}📋 Usage: ./deploy.sh [command]${NC}"
            echo ""
            echo "Commands:"
            echo "  full     - Complete deployment (backup + files + migrations + permissions)"
            echo "  files    - Upload files and set permissions only"
            echo "  db       - Run database migrations only"
            echo "  migrate  - Run migrations only (no backup)"
            echo "  seed     - Run database seeding only"
            echo "  backup   - Create database backup only"
            echo "  status   - Check database status only"
            echo "  fresh    - Fresh install (drops all data!)"
            echo "  test     - Test local environment (dry run)"
            echo ""
            echo "Examples:"
            echo "  ./deploy.sh full"
            echo "  ./deploy.sh files"
            echo "  ./deploy.sh migrate"
            echo "  ./deploy.sh status"
            echo ""
            echo "Default: full deployment"
            echo ""
            # Run full deployment as default
            backup_database
            upload_files
            run_migrations
            run_seeding
            set_permissions
            check_database_status
            ;;
    esac

    # Cleanup
    cleanup_temp_files

    # Show summary
    show_summary

    log_success "Deployment completed successfully!"
    echo "===================================="
}

# Trap to cleanup on exit
trap cleanup_temp_files EXIT

# Run main function with all arguments
main "$@"
