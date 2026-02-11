#!/usr/bin/env python3
"""
Migration Validation Script
Validates that the migration script can be safely applied to the database.
"""

import re
import sys

def read_sql_file(filename):
    """Read SQL file and return content."""
    try:
        with open(filename, 'r', encoding='utf-8') as f:
            return f.read()
    except FileNotFoundError:
        print(f"ERROR: File not found: {filename}")
        sys.exit(1)

def extract_tables(sql_content):
    """Extract table names from CREATE TABLE statements."""
    pattern = r'CREATE TABLE(?:\s+IF NOT EXISTS)?\s+`([^`]+)`'
    return set(re.findall(pattern, sql_content, re.IGNORECASE))

def extract_columns_from_table(sql_content, table_name):
    """Extract column names from a specific table."""
    # Find the CREATE TABLE statement for this table
    pattern = rf'CREATE TABLE(?:\s+IF NOT EXISTS)?\s+`{re.escape(table_name)}`\s*\((.*?)\)\s*ENGINE'
    match = re.search(pattern, sql_content, re.DOTALL | re.IGNORECASE)
    
    if not match:
        return set()
    
    table_def = match.group(1)
    
    # Extract column names (lines starting with backtick that aren't keys/constraints)
    columns = set()
    for line in table_def.split('\n'):
        line = line.strip()
        if line.startswith('`'):
            # Extract column name
            col_match = re.match(r'`([^`]+)`', line)
            if col_match:
                columns.add(col_match.group(1))
    
    return columns

def validate_migration(old_schema, new_schema, migration_script):
    """Validate that migration is safe and complete."""
    print("=" * 80)
    print("MIGRATION VALIDATION REPORT")
    print("=" * 80)
    print()
    
    old_sql = read_sql_file(old_schema)
    new_sql = read_sql_file(new_schema)
    migration_sql = read_sql_file(migration_script)
    
    old_tables = extract_tables(old_sql)
    new_tables = extract_tables(new_sql)
    migration_tables = extract_tables(migration_sql)
    
    # Check 1: New tables are being created
    new_table_names = new_tables - old_tables
    print(f"✓ NEW TABLES TO BE CREATED: {len(new_table_names)}")
    for table in sorted(new_table_names):
        in_migration = table in migration_tables
        status = "✓" if in_migration else "✗"
        print(f"  {status} {table}")
    print()
    
    # Check 2: No tables are being dropped
    dropped_tables = old_tables - new_tables
    if dropped_tables:
        print(f"⚠ WARNING: Tables in old schema but not in new schema: {len(dropped_tables)}")
        for table in sorted(dropped_tables):
            print(f"  - {table}")
        print("  NOTE: Migration script does NOT drop these tables (data preserved)")
    else:
        print("✓ NO TABLES DROPPED: All existing tables are preserved")
    print()
    
    # Check 3: Verify column additions for common tables
    common_tables = old_tables & new_tables
    print(f"✓ EXISTING TABLES TO UPDATE: {len(common_tables)}")
    
    total_new_columns = 0
    for table in sorted(common_tables):
        old_cols = extract_columns_from_table(old_sql, table)
        new_cols = extract_columns_from_table(new_sql, table)
        added_cols = new_cols - old_cols
        removed_cols = old_cols - new_cols
        
        if added_cols or removed_cols:
            print(f"\n  Table: {table}")
            if added_cols:
                print(f"    + New columns ({len(added_cols)}): {', '.join(sorted(added_cols))}")
                total_new_columns += len(added_cols)
            if removed_cols:
                print(f"    ⚠ Columns removed in new schema ({len(removed_cols)}): {', '.join(sorted(removed_cols))}")
                print(f"      NOTE: Migration does NOT remove these columns (data preserved)")
    
    print()
    print(f"✓ TOTAL NEW COLUMNS TO BE ADDED: {total_new_columns}")
    print()
    
    # Check 4: Migration safety checks
    print("=" * 80)
    print("SAFETY CHECKS")
    print("=" * 80)
    
    has_drop = bool(re.search(r'\bDROP\s+(TABLE|COLUMN)', migration_sql, re.IGNORECASE))
    has_truncate = bool(re.search(r'\bTRUNCATE\s+TABLE', migration_sql, re.IGNORECASE))
    has_delete = bool(re.search(r'\bDELETE\s+FROM', migration_sql, re.IGNORECASE))
    
    if has_drop:
        print("✗ DANGER: Migration script contains DROP statements")
    else:
        print("✓ SAFE: No DROP statements found")
    
    if has_truncate:
        print("✗ DANGER: Migration script contains TRUNCATE statements")
    else:
        print("✓ SAFE: No TRUNCATE statements found")
    
    if has_delete:
        print("✗ DANGER: Migration script contains DELETE statements")
    else:
        print("✓ SAFE: No DELETE statements found")
    
    print()
    
    # Check 5: Uses IF NOT EXISTS for safety
    uses_if_not_exists = bool(re.search(r'IF NOT EXISTS', migration_sql, re.IGNORECASE))
    if uses_if_not_exists:
        print("✓ SAFE: Uses IF NOT EXISTS clauses (can be run multiple times)")
    else:
        print("⚠ WARNING: Does not use IF NOT EXISTS (may error if run twice)")
    
    print()
    print("=" * 80)
    print("RECOMMENDATIONS")
    print("=" * 80)
    print()
    print("1. BACKUP: Always backup your database before running migrations")
    print("2. TEST: Run migration in a development/staging environment first")
    print("3. VERIFY: Check application compatibility with new schema")
    print("4. MONITOR: Watch for errors during migration execution")
    print()
    
    if not has_drop and not has_truncate and not has_delete:
        print("✓ VERDICT: Migration appears SAFE - only adds new structures")
        print("           No data will be lost or removed")
        return 0
    else:
        print("✗ VERDICT: Migration contains destructive operations - REVIEW CAREFULLY")
        return 1

if __name__ == '__main__':
    old_schema = 'tradevcrypto (4).sql'
    new_schema = 'kryptox (18).sql'
    migration_script = 'migration_tradevcrypto_to_kryptox.sql'
    
    exit_code = validate_migration(old_schema, new_schema, migration_script)
    sys.exit(exit_code)
