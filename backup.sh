#!/bin/bash
DATA=$(date +%Y-%m-%d_%H-%M-%S)
mysqldump -u root crm | zip --password 'SenhaForte2025!' backup_crm_$DATA.zip -
find . -name "backup_crm_*.zip" -mtime +7 -delete # Remove backups antigos (7 dias)
