# Inventory Tesseract OCR System - Project Overview

## Project Description
This is a Laravel-based inventory management system with OCR (Optical Character Recognition) capabilities for processing receipt images. The system uses Tesseract OCR to extract text from receipt images and automatically updates inventory levels.

## Key Features
- Role-Based Access Control (users can be 'user' or 'admin')
- Inventory Management with automatic stock updates
- OCR Processing using ddeboer/tesseract package
- Data Validation workflow for corrected OCR data
- Debt Tracking for hutang (credit) purchases
- File Storage for receipt images

## Database Schema
The system includes the following tables:
1. **users** - Extended Laravel users table with role field
2. **products** - Inventory items with name, SKU, prices, and stock
3. **stores** - Store/supplier information
4. **receipts** - Uploaded receipt data including OCR results
5. **receipt_items** - Line items from receipts
6. **debts** - Accounts payable tracking
7. **debt_payments** - Payment tracking for debts

## Main Components
### Controllers
- **Admin\ReceiptController**: Handles receipt upload, OCR processing, validation, and inventory updates
- **ProductsController**: Standard CRUD operations for product management

### Models
Eloquent models for all tables with appropriate relationships

### Views
Blade templates for admin interface:
- Base layout with Bootstrap 5
- Receipt upload form
- Validation form with dynamic item rows

### Routes
Web routes including:
- Public welcome route
- Admin receipts routes (namespaced)
- Resource routes for products

## Packages Installed
- ddeboer/tesseract - Tesseract OCR wrapper
- intervention/image - Image manipulation for OCR preprocessing

## How to Use
1. Register users (first user can be made admin by setting role='admin')
2. Log in as admin
3. Navigate to admin/receipts/upload to upload receipt images
4. System processes image with Tesseract OCR
5. Admin validates and corrects extracted data
6. Validated data updates inventory and creates debt records if applicable
7. Manage products via /products route
8. View and pay debts through debt management

## Next Steps / Future Enhancements
1. Implement debt payment interface
2. Add authentication middleware to protect admin routes
3. Create debt listing and payment views
4. Add reporting features
5. Implement user management for role assignment

## Claude Code Integration
This project includes Claude Code plugins with:
- Specialized agents for delegation
- Skills/workflow definitions
- Slash commands (/tdd, /plan, /e2e, etc.)
- Trigger-based automations (hooks)
- Always-follow guidelines (rules)
- MCP server configurations
- Cross-platform Node.js utilities
- Test suite

## Development Guidelines
- File naming: lowercase with hyphens
- PHP code follows Laravel conventions
- Use relative imports and mixed exports
- Prefer const over let; never var
- Keep hook scripts under 200 lines
- All hooks must exit 0 on non-critical errors