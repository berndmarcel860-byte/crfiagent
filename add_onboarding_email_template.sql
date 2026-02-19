-- Add onboarding completion email template
INSERT INTO email_templates (name, subject, body, variables, created_at, updated_at) 
VALUES ('onboarding_completed', 'Willkommen - Verifizierung erforderlich', '<!DOCTYPE html><html><body>Email content here</body></html>', 'user_name,company_name', NOW(), NOW());
