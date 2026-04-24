# Brand Management User Guide

## Overview

The Brand Management system allows tenants to create, manage, and organize their brands while assigning specific modules to each brand. This enables multi-brand organizations to have different module access for different brands.

## Features

- ✅ **Brand Creation & Management**: Create, edit, and delete brands
- ✅ **Module Assignment**: Assign specific modules to each brand
- ✅ **Cross-Database Integration**: Access modules from the landlord database
- ✅ **Brand Dashboard**: View brands with their assigned modules
- ✅ **Module Navigation**: Navigate to specific module dashboards
- ✅ **Brand Switching**: Switch between different brands

## Getting Started

### Accessing Brand Management

1. **Navigate to Brands**: Go to the tenant dashboard and click on "Brands" in the navigation menu
2. **View Brands List**: See all your brands in a data table with their details
3. **Create New Brand**: Click the "Add Brand" button to create a new brand

### Creating a Brand

1. **Click "Add Brand"**: This opens a modal dialog
2. **Fill Brand Details**:
   - **Name**: Enter the brand name (required)
   - **Description**: Add a description for the brand
   - **Logo**: Upload a brand logo (optional)
   - **Website**: Enter the brand's website URL
   - **Email**: Enter contact email
   - **Phone**: Enter contact phone number
   - **Address**: Enter the brand's address
   - **Status**: Set to Active or Inactive

3. **Save Brand**: Click "Save" to create the brand

### Editing a Brand

1. **Find the Brand**: Locate the brand in the brands list
2. **Click Edit**: Click the edit button (pencil icon) for the brand
3. **Modify Details**: Update any brand information
4. **Save Changes**: Click "Save" to update the brand

### Assigning Modules to Brands

1. **Access Brand Modules**: Click on a brand name or use the "View Modules" action
2. **Select Modules**: Choose which modules this brand should have access to
3. **Save Assignment**: Confirm the module assignment

## Brand Dashboard

### Viewing Brands

The brand dashboard shows:
- **Brand Cards**: Visual cards showing each brand with logo and name
- **Module Count**: Number of modules assigned to each brand
- **Quick Actions**: Buttons to view modules or manage the brand

### Brand Card Information

Each brand card displays:
- **Brand Logo**: Visual representation of the brand
- **Brand Name**: The name of the brand
- **Module Count**: Number of assigned modules
- **Status**: Active or Inactive status

### Interacting with Brands

1. **Click Brand Card**: Opens a modal showing assigned modules
2. **View Modules**: See all modules assigned to the brand
3. **Navigate to Module**: Click on a module to go to its dashboard
4. **Add Brand**: Click the "Add Brand" button to create a new brand

## Module Assignment

### Understanding Modules

Modules are system features that can be assigned to brands:
- **HR Module**: Human Resources management
- **CRM Module**: Customer Relationship Management
- **POS Module**: Point of Sale system
- **Accounting Module**: Financial management
- **Sales Module**: Sales management
- **Inventory Module**: Inventory tracking

### Assigning Modules

1. **Select Brand**: Choose the brand you want to assign modules to
2. **Choose Modules**: Select which modules the brand should have access to
3. **Confirm Assignment**: Save the module assignments

### Module Access

- **Active Modules**: Brands can access and use these modules
- **Inactive Modules**: Brands cannot access these modules
- **Module Dashboards**: Each module has its own dashboard interface

## Brand Switching

### Using Brand Switcher

1. **Access Switcher**: Click the "Switch Brand" dropdown in the header
2. **Select Brand**: Choose from available brands
3. **View Modules**: See modules available for the selected brand
4. **Navigate**: Click on a module to go to its dashboard

### Brand Context

- **Current Brand**: The currently selected brand is highlighted
- **Available Modules**: Only modules assigned to the current brand are shown
- **Module Access**: Users can only access modules assigned to the current brand

## Best Practices

### Brand Organization

1. **Clear Naming**: Use descriptive names for brands
2. **Consistent Logos**: Use high-quality, consistent logos
3. **Complete Information**: Fill in all relevant brand details
4. **Status Management**: Keep brand status up to date

### Module Assignment

1. **Minimal Access**: Only assign modules that the brand actually needs
2. **Regular Review**: Periodically review module assignments
3. **User Training**: Ensure users understand which modules are available
4. **Documentation**: Keep track of why modules were assigned

### Security Considerations

1. **Access Control**: Only authorized users can manage brands
2. **Module Permissions**: Users can only access assigned modules
3. **Data Isolation**: Brand data is properly isolated between tenants
4. **Audit Trail**: All brand changes are logged and auditable

## Troubleshooting

### Common Issues

#### Brand Not Appearing
- **Check Status**: Ensure the brand is set to "Active"
- **Refresh Page**: Try refreshing the browser page
- **Check Permissions**: Verify you have permission to view brands

#### Modules Not Loading
- **Check Assignment**: Verify modules are assigned to the brand
- **Check Status**: Ensure modules are active in the system
- **Clear Cache**: Try clearing browser cache

#### Cannot Edit Brand
- **Check Permissions**: Verify you have edit permissions
- **Check Status**: Ensure the brand is not locked
- **Contact Admin**: Contact system administrator if issues persist

#### Module Dashboard Not Accessible
- **Check Assignment**: Verify the module is assigned to the current brand
- **Check Permissions**: Ensure you have access to the module
- **Try Different Brand**: Switch to a different brand to test

### Getting Help

1. **Documentation**: Check this user guide for detailed information
2. **Support Team**: Contact the support team for technical issues
3. **System Admin**: Contact your system administrator for permission issues
4. **Training**: Request additional training if needed

## Advanced Features

### Bulk Operations

- **Bulk Module Assignment**: Assign the same modules to multiple brands
- **Bulk Status Changes**: Change status for multiple brands at once
- **Export Data**: Export brand and module data for reporting

### Reporting

- **Brand Reports**: Generate reports on brand usage and activity
- **Module Usage**: Track which modules are most used
- **Performance Metrics**: Monitor brand and module performance

### Integration

- **API Access**: Use the Cross-Database API for custom integrations
- **Webhook Support**: Set up webhooks for brand changes
- **Third-Party Tools**: Integrate with external tools and services

## Frequently Asked Questions

### Q: How many brands can I create?
A: There's no limit on the number of brands you can create per tenant.

### Q: Can I assign the same module to multiple brands?
A: Yes, modules can be assigned to multiple brands simultaneously.

### Q: What happens if I delete a brand?
A: Deleting a brand removes all module assignments and brand data. This action cannot be undone.

### Q: Can I change module assignments after creating a brand?
A: Yes, you can modify module assignments at any time through the brand management interface.

### Q: How do I know which modules are available?
A: Available modules are shown in the module assignment interface and are managed by the system administrator.

### Q: Can I have different users for different brands?
A: User management is separate from brand management. Users can access multiple brands based on their permissions.

### Q: What's the difference between Active and Inactive brands?
A: Active brands are visible and accessible, while inactive brands are hidden from the interface but retain their data.

### Q: How do I upload a brand logo?
A: Use the file upload field in the brand creation/editing form. Supported formats include PNG, JPG, and SVG.

### Q: Can I export brand data?
A: Yes, brand data can be exported through the reporting features or API endpoints.

### Q: What happens if a module is removed from the system?
A: If a module is removed from the system, it will no longer be available for assignment to brands, but existing assignments will remain until manually removed.
