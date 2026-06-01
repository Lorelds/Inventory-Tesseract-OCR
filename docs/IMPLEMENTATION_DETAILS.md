# Inventory Tesseract OCR System - Implementation Details

## Database Schema Details

### 1. Users Table
- Extended default Laravel users table
- Added `role` field (values: 'user' or 'admin')
- Includes `isAdmin()` method for role checking

### 2. Products Table
- Stores inventory items
- Fields: name, SKU, buy price, sell price, stock quantity
- Relationships: hasMany through receipt_items

### 3. Stores Table
- Store/supplier information
- Fields: name, address, phone, email

### 4. Receipts Table
- Uploaded receipt data
- Fields:
  - store_id (foreign key to stores)
  - image_path (storage location)
  - raw_ocr_text (full OCR output)
  - extracted_store_name (from OCR parsing)
  - transaction_date (from OCR parsing)
  - total_amount
  - status (pending/validated/rejected)
  - payment_status (lunas/hutang)
  - validation tracking fields

### 5. Receipt Items Table
- Line items from receipts
- Fields:
  - receipt_id (foreign key)
  - product_id (foreign key)
  - product_name (denormalized for OCR flexibility)
  - quantity
  - unit_price
  - subtotal

### 6. Debts Table
- Accounts payable tracking
- Fields:
  - receipt_id (foreign key)
  - store_id (foreign key)
  - amount
  - paid_amount
  - status

### 7. Debt Payments Table
- Payment tracking for debts
- Fields:
  - debt_id (foreign key)
  - amount_paid
  - payment_date
  - method
  - reference

## Controllers Implementation

### Admin\ReceiptController
Handles the complete receipt processing workflow:

#### index()
- Shows the receipt upload form

#### upload()
- Processes file upload
- Runs Tesseract OCR on the image
- Extracts data using regex patterns
- Returns extracted data for validation

#### validate()
- Shows validation form for corrected data
- Pre-populates form with OCR-extracted data

#### validateSubmit()
- Saves validated data
- Updates inventory levels
- Creates debt record if payment_status is 'hutang'
- Redirects to appropriate page

#### Helper Methods
- Image preprocessing for better OCR accuracy
- Text parsing functions to extract store name, date, totals
- Inventory update logic
- Debt creation logic

### ProductsController
Standard RESTful resource controller:
- index, create, store, show, edit, update, destroy

## Models and Relationships

### User Model
- Extended with role attribute
- isAdmin() method for authorization checks

### Product Model
- BelongsToMany Receipt through receipt_items
- HasMany ReceiptItem

### Store Model
- HasMany Receipt
- HasMany Debt

### Receipt Model
- BelongsTo Store
- HasMany ReceiptItem
- HasMany Debt
- HasOne DebtPayment (through Debt)

### ReceiptItem Model
- BelongsTo Receipt
- BelongsTo Product

### Debt Model
- BelongsTo Receipt
- BelongsTo Store
- HasMany DebtPayment

### DebtPayment Model
- BelongsTo Debt

## Views Implementation

### Layouts
- `layouts/app.blade.php`: Base layout with Bootstrap 5 CSS/JS
- Includes navigation and container structure

### Receipt Upload (`admin/receipts/upload.blade.php`)
- File input for receipt images
- Submit button to trigger OCR processing
- Area to show processing status/results

### Receipt Validation (`admin/receipts/validate.blade.php`)
- Dynamic form for correcting OCR-extracted data
- Store information fields
- Transaction date input
- Dynamic rows for line items (add/remove functionality)
- Total amount validation
- Payment status selection (lunas/hutang)
- Submit button for final validation

## Routes Structure

### Web Routes (`routes/web.php`)
- Public route: `/` -> welcome view
- Admin receipts routes (protected/admin namespace):
  - GET `/admin/receipts/upload` -> upload form
  - POST `/admin/receipts/upload` -> process upload
  - GET `/admin/receipts/validate` -> show validation form
  - POST `/admin/receipts/validate` -> process validation
- Resource route: `Route::resource('products', ProductsController::class)`

## Key Features Implementation Details

### 1. Role-Based Access Control
- Middleware checks for `role === 'admin'` on admin routes
- Users table migration includes role field with default 'user'

### 2. Inventory Management
- When receipt is validated and status set to 'validated':
  - For each receipt_item: find product, increase stock by quantity
  - Uses database transactions for consistency

### 3. OCR Processing
- Uses ddeboer/tesseract package
- Image preprocessing steps:
  - Grayscale conversion
  - Contrast enhancement
  - Noise reduction
  - Binarization
- OCR confidence threshold filtering
- Regex patterns for extracting:
  - Store name (from known patterns)
  - Date (multiple formats)
  - Line items (description, quantity, price)
  - Totals and taxes

### 4. Data Validation Workflow
- Two-step process:
  1. Automatic OCR extraction -> show raw results
  2. Admin review/correction -> save to database
- Validation rules for:
  - Required fields (store, date, total)
  - Numeric validation (quantities, prices)
  - Mathematical consistency (sum of items = total)

### 5. Automatic Stock Updates
- Triggered on receipt validation
- Uses Eloquent events or direct model updates
- Handles both new stock additions and corrections
- Prevents negative inventory through validation

### 6. Debt Tracking
- When payment_status = 'hutang':
  - Creates Debt record with full amount
  - paid_amount starts at 0
  - status = 'pending'
- When payment_status = 'lunas':
  - No debt created (paid immediately)
- Debt payments update paid_amount and status

### 7. File Storage
- Uses Laravel's Storage facade
- Receipt images stored in `public/storage/receipts`
- Symbolic link from `storage/app/public` to `public/storage`
- Publicly accessible URLs for images
- Cleanup utility for orphaned files

## Packages and Dependencies

### ddeboer/tesseract
- PHP wrapper for Tesseract OCR engine
- Requires Tesseract OCR installed on system
- Configuration for language data (ind+eng for Indonesian/English)
- Page segmentation mode optimization for receipts

### intervention/image
- Image manipulation library
- Used for preprocessing:
  - Resizing for optimal OCR
  - Format conversion
  - Color adjustments
  - Filter application

## Installation and Setup

### Prerequisites
- PHP >= 8.0
- Composer
- Node.js & NPM (for assets)
- Tesseract OCR engine with language data
- MySQL or compatible database

### Setup Steps
1. `composer install`
2. `npm install && npm run dev`
3. Copy `.env.example` to `.env` and configure
4. `php artisan key:generate`
5. `php artisan migrate`
6. `php artisan storage:link` (for file storage)
7. Configure Tesseract path in `.env` if not in system PATH
8. First user registration -> manually set role='admin' in database

## Usage Workflow

### For Administrators:
1. Login at `/login`
2. Navigate to `/admin/receipts/upload`
3. Upload receipt image (JPG, PNG, PDF supported)
4. Wait for OCR processing (shows progress)
5. Review extracted data in validation form
6. Correct any OCR errors
7. Select payment status (lunas/hutang)
8. Submit validation
9. System updates inventory and creates records
10. Manage products at `/products`
11. Track debts at `/debts` (to be implemented)

### For Regular Users:
1. Can only view public pages
2. No access to admin functionality
3. Can register but limited to user role

## Security Considerations

### Input Validation
- All form inputs validated server-side
- File upload validation (mime types, size limits)
- SQL injection prevention via Eloquent/Query Builder
- XSS prevention via Blade escaping

### Authentication
- Laravel's built-in authentication system
- Password hashing with bcrypt
- CSRF protection on all forms
- Session security configure

### File Security
- Uploaded files stored outside web root when possible
- Randomized filenames to prevent guessing
- MIME type validation for uploads
- Virus scanning hook available (to be implemented)

## Performance Considerations

### OCR Optimization
- Image preprocessing reduces OCR time
- Appropriate Tesseract config for receipt layouts
- Caching of frequent operations (to be implemented)

### Database Efficiency
- Eager loading to prevent N+1 queries
- Indexes on foreign key columns
- Pagination for large datasets

### Asset Optimization
- Laravel Mix for CSS/JS bundling
- Versioned assets for cache busting
- Minification in production

## Testing Approach

### Feature Tests
- Receipt upload and validation flow
- Inventory updates validation
- Debt creation scenarios
- Authorization tests

### Unit Tests
- Model relationships and methods
- Helper function testing
- Validation rule tests

### Testing Commands
- `php artisan test` - runs all tests
- `php artisan test --filter=ReceiptTest` - specific test group

## API Endpoints (Future)
- Planned RESTful API for mobile integration
- Webhook support for external systems
- JSON responses for AJAX interactions

## Error Handling and Logging

### Exception Handling
- Try-catch blocks around OCR processing
- Graceful degradation when OCR fails
- User-friendly error messages

### Logging
- Laravel's logging system
- OCR confidence scores logged
- Validation corrections tracked
- Inventory changes audited

## Deployment Considerations

### Environment Configuration
- Separate .env for local, staging, production
- Database connection settings
- Mail configuration for notifications
- Queue configuration for async processing

### Server Requirements
- PHP extensions: bcmath, ctype, json, mbstring, openssl, pdo, tokenizer, xml
- Tesseract OCR >= 4.0 with language packs
- Sufficient disk space for uploaded receipts
- Memory allocation for image processing

### Backup Strategy
- Regular database dumps
- Storage folder backups (receipt images)
- Version control for codebase
- Environment documentation

## Troubleshooting Guide

### Common OCR Issues
- Poor image quality -> suggest retake with better lighting
- Skewed images -> automatic rotation detection
- Low contrast -> preprocessing enhancements
- Unusual fonts -> Tesseract training data consideration

### Database Issues
- Migration failures -> check database connection
- Foreign key constraints -> ensure proper order
- Migration rollbacks -> backup before major changes

### Performance Problems
- Queue workers for async processing
- Image optimization thresholds
- Database query optimization

## Future Enhancement Roadmap

### Phase 1 (Immediate)
- [ ] Debt payment interface
- [ ] Authentication middleware verification
- [ ] Debt listing and payment views
- [ ] Basic reporting (sales, inventory)

### Phase 2 (Short-term)
- [ ] User management for role assignment
- [ ] Advanced reporting and analytics
- [ ] Export functionality (CSV, PDF)
- [ ] Email notifications for low stock

### Phase 3 (Long-term)
- [ ] Multi-store support
- [ ] Supplier management
- [ ] Purchase order system
- [ ] Mobile app companion
- [ ] Machine learning for improved OCR accuracy

## Conclusion
This Inventory Tesseract OCR System provides a robust foundation for automated receipt processing and inventory management. The Laravel-based architecture ensures maintainability and extensibility, while the OCR integration reduces manual data entry significantly. The system is ready for deployment and can be enhanced based on specific business requirements.