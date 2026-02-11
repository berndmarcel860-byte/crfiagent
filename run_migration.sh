#!/bin/bash
# Quick Migration Script
# This script helps automate the database migration process with safety checks

set -e  # Exit on any error

# Configuration
DB_NAME="tradevcrypto"
BACKUP_DIR="./db_backups"
MIGRATION_FILE="migration_tradevcrypto_to_kryptox.sql"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "=================================="
echo "Database Migration Helper Script"
echo "=================================="
echo ""

# Check if migration file exists
if [ ! -f "$MIGRATION_FILE" ]; then
    echo -e "${RED}ERROR: Migration file not found: $MIGRATION_FILE${NC}"
    exit 1
fi

# Get database credentials
echo "Enter MySQL username:"
read -r DB_USER

echo "Enter MySQL password:"
read -s DB_PASS
echo ""

# Test database connection
echo -e "${YELLOW}Testing database connection...${NC}"
mysql -u "$DB_USER" -p"$DB_PASS" -e "SELECT 1;" > /dev/null 2>&1
if [ $? -ne 0 ]; then
    echo -e "${RED}ERROR: Failed to connect to database. Check credentials.${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Database connection successful${NC}"
echo ""

# Check if database exists
echo -e "${YELLOW}Checking if database '$DB_NAME' exists...${NC}"
DB_EXISTS=$(mysql -u "$DB_USER" -p"$DB_PASS" -e "SHOW DATABASES LIKE '$DB_NAME';" | grep -c "$DB_NAME" || true)
if [ "$DB_EXISTS" -eq 0 ]; then
    echo -e "${RED}ERROR: Database '$DB_NAME' does not exist.${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Database '$DB_NAME' found${NC}"
echo ""

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_DIR"

# Generate backup filename with timestamp
BACKUP_FILE="$BACKUP_DIR/tradevcrypto_backup_$(date +%Y%m%d_%H%M%S).sql"

# Create backup
echo -e "${YELLOW}Creating database backup...${NC}"
echo "Backup location: $BACKUP_FILE"
mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_FILE"
if [ $? -ne 0 ]; then
    echo -e "${RED}ERROR: Backup failed!${NC}"
    exit 1
fi

# Verify backup file size
BACKUP_SIZE=$(stat -f%z "$BACKUP_FILE" 2>/dev/null || stat -c%s "$BACKUP_FILE" 2>/dev/null)
if [ "$BACKUP_SIZE" -lt 1000 ]; then
    echo -e "${RED}ERROR: Backup file seems too small ($BACKUP_SIZE bytes). Migration aborted.${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Backup created successfully ($(numfmt --to=iec-i --suffix=B $BACKUP_SIZE))${NC}"
echo ""

# Run validation script if available
if [ -f "validate_migration.py" ]; then
    echo -e "${YELLOW}Running migration validation...${NC}"
    python3 validate_migration.py
    if [ $? -ne 0 ]; then
        echo -e "${RED}WARNING: Validation found issues. Review carefully before proceeding.${NC}"
    fi
    echo ""
fi

# Confirm before running migration
echo -e "${YELLOW}=================================="
echo "READY TO RUN MIGRATION"
echo "==================================${NC}"
echo "Database: $DB_NAME"
echo "Backup: $BACKUP_FILE"
echo "Migration: $MIGRATION_FILE"
echo ""
echo -e "${RED}WARNING: This will modify your database schema.${NC}"
echo -e "${GREEN}NOTE: Your backup is safe at: $BACKUP_FILE${NC}"
echo ""
echo "Do you want to proceed with the migration? (yes/no)"
read -r CONFIRM

if [ "$CONFIRM" != "yes" ]; then
    echo -e "${YELLOW}Migration cancelled by user.${NC}"
    echo "Your backup is available at: $BACKUP_FILE"
    exit 0
fi

# Run the migration
echo ""
echo -e "${YELLOW}Running migration...${NC}"
mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$MIGRATION_FILE"

if [ $? -ne 0 ]; then
    echo -e "${RED}ERROR: Migration failed!${NC}"
    echo "Your database may be in an inconsistent state."
    echo "Restore from backup: $BACKUP_FILE"
    echo ""
    echo "To restore, run:"
    echo "mysql -u $DB_USER -p $DB_NAME < $BACKUP_FILE"
    exit 1
fi

echo -e "${GREEN}✓ Migration completed successfully!${NC}"
echo ""

# Verify new tables
echo -e "${YELLOW}Verifying new tables...${NC}"
NEW_TABLES=$(mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "
    SELECT TABLE_NAME 
    FROM information_schema.TABLES 
    WHERE TABLE_SCHEMA = '$DB_NAME' 
    AND TABLE_NAME IN ('email_templates_backup', 'email_templates_backup1', 'user_notifications');
" -sN)

if [ -z "$NEW_TABLES" ]; then
    echo -e "${RED}WARNING: New tables not found. Migration may have failed.${NC}"
else
    echo -e "${GREEN}✓ New tables verified:${NC}"
    echo "$NEW_TABLES" | while read -r table; do
        echo "  - $table"
    done
fi

echo ""
echo -e "${GREEN}=================================="
echo "MIGRATION COMPLETE"
echo "==================================${NC}"
echo ""
echo "Next steps:"
echo "1. Test your application thoroughly"
echo "2. Monitor application logs for errors"
echo "3. Keep backup safe: $BACKUP_FILE"
echo "4. Update application code if needed to use new columns"
echo ""
echo -e "${GREEN}Backup location: $BACKUP_FILE${NC}"
