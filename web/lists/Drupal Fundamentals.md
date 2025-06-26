Drupal Fundamentals: Step-by-Step Topics
1. Drupal Architecture Overview

Understanding Drupal as a CMS=> Exploring Drupal’s role as a content management system  
Learning Drupal’s Modular Structure=> Understanding modules, themes, and core components  
Exploring Drupal’s File System=> Navigating directories (e.g., modules, themes, sites)  
Understanding Drupal’s Request-Response Flow=> Learning how Drupal processes HTTP requests  
Differentiating Core vs. Contrib Modules=> Recognizing Drupal core and contributed modules  
Exploring Drupal’s Entity System=> Understanding entities (nodes, users, taxonomy terms)  
Learning Drupal’s Configuration System=> Overview of configuration vs. content storage

2. Installation and Setup

Installing Drupal via Composer=> Setting up Drupal 10 with Composer  
Installing Drupal Manually=> Downloading and configuring Drupal via tarball  
Configuring Database Settings=> Setting up settings.php for database connection  
Running the Drupal Installer=> Completing the web-based installation process  
Setting Up a Local Development Environment=> Using DDEV or Lando for local Drupal sites  
Configuring Site Settings=> Setting site name, email, and timezone  
Securing Drupal Installation=> Applying basic security settings post-install

3. Site Building Basics

Creating Content Types=> Defining custom content types via UI  
Adding Fields to Content Types=> Configuring text, image, or reference fields  
Managing Field Settings=> Customizing field properties (e.g., required, default values)  
Configuring Display Modes=> Setting up view modes (e.g., full, teaser)  
Configuring Form Displays=> Customizing form modes for content entry  
Creating Taxonomy Vocabularies=> Setting up vocabularies for categorization  
Adding Taxonomy Terms=> Creating terms within vocabularies  
Managing Menus=> Creating and organizing menu links  
Placing Blocks=> Assigning blocks to regions via UI

4. Content Management

Creating Content (Nodes)=> Adding nodes via the content creation interface  
Editing Content=> Modifying existing nodes via UI  
Deleting Content=> Removing nodes and managing deletion permissions  
Publishing and Unpublishing Content=> Toggling content status (published/unpublished)  
Managing Content Revisions=> Creating and viewing node revisions  
Filtering Content Lists=> Using admin content views to filter nodes  
Tagging Content with Taxonomy=> Applying taxonomy terms to nodes  
Managing Bulk Operations=> Performing bulk actions (e.g., publish, delete)

5. User and Permission Management

Creating User Accounts=> Adding users via the admin interface  
Defining User Roles=> Creating roles for different user types  
Assigning Permissions to Roles=> Configuring permissions for content and admin access  
Managing User Permissions=> Fine-tuning permissions via the permissions UI  
Editing User Profiles=> Updating user details and fields  
Blocking/Unblocking Users=> Managing user account status  
Configuring Account Settings=> Setting up registration and password policies  
Managing Anonymous User Access=> Controlling access for unauthenticated users

6. Blocks and Layouts

Understanding Blocks=> Learning block types (custom, system, contrib)  
Creating Custom Blocks=> Adding custom blocks via the block UI  
Configuring Block Visibility=> Setting visibility by path, role, or content type  
Placing Blocks in Regions=> Assigning blocks to theme regions  
Exploring Layout Builder=> Using Layout Builder for page layouts  
Configuring Layout Builder Sections=> Adding sections and blocks in Layout Builder  
Managing Block Contexts=> Setting block visibility based on conditions

7. Views

Understanding Views Module=> Exploring Views for custom content listings  
Creating a View=> Building a view via the Views UI  
Configuring View Displays=> Setting up page, block, or REST displays  
Adding Fields to Views=> Including entity fields in view output  
Filtering Views Results=> Applying filters (e.g., content type, status)  
Sorting Views Results=> Configuring sort criteria for views  
Paginating Views=> Adding pagers to view displays  
Exposing Views Filters=> Creating exposed filters for user input  
Creating Contextual Filters=> Using contextual filters for dynamic views

8. Themes and Appearance

Understanding Drupal Themes=> Learning theme structure and purpose  
Installing a Theme=> Adding themes via UI or Composer  
Enabling a Theme=> Setting a default or admin theme  
Configuring Theme Settings=> Customizing theme options (e.g., logo, favicon)  
Exploring Core Themes=> Understanding Claro, Olivero, and Stable themes  
Switching Between Themes=> Managing theme usage for different roles

9. Modules

Understanding Drupal Modules=> Learning module types (core, contrib, custom)  
Installing Contrib Modules=> Adding modules via Composer or UI  
Enabling/Disabling Modules=> Managing module status via UI or Drush  
Configuring Module Settings=> Customizing module options via admin UI  
Exploring Core Modules=> Understanding modules like Node, User, and Views  
Finding Contrib Modules=> Searching Drupal.org for contrib modules

10. Configuration Management

Understanding Configuration vs. Content=> Differentiating config (settings) from content (nodes)  
Exporting Configuration=> Using drush config:export or UI to export config  
Importing Configuration=> Using drush config:import or UI to import config  
Managing Configuration Files=> Storing config in config/sync directory  
Handling Configuration Conflicts=> Resolving conflicts during config import  
Using Configuration Split=> Managing environment-specific configurations

11. Multilingual Basics

Enabling Multilingual Support=> Installing Language and translation modules  
Adding Languages=> Configuring site languages via UI  
Translating Interface Strings=> Managing UI translations via admin interface  
Translating Content=> Enabling content translation for nodes  
Configuring Language Detection=> Setting up URL or browser-based language detection

12. Cron and Maintenance

Understanding Cron in Drupal=> Learning cron for automated tasks  
Configuring Cron Jobs=> Setting cron intervals via UI or Drush  
Running Cron Manually=> Triggering cron with drush core:cron  
Enabling Maintenance Mode=> Putting site in maintenance mode via UI or Drush  
Disabling Maintenance Mode=> Restoring site access post-maintenance  
Monitoring Site Status=> Checking status reports for issues

13. Security Basics

Understanding Drupal Security=> Learning Drupal’s security model  
Applying Security Updates=> Updating core and modules for security patches  
Configuring Trusted Host Settings=> Setting up trusted_host_patterns in settings.php  
Managing File Permissions=> Securing file directories (e.g., sites/default/files)  
Reviewing Security Reports=> Checking admin reports for security issues

14. Backup and Restore

Backing Up Drupal Database=> Using drush sql:dump for database backups  
Backing Up Drupal Files=> Archiving sites/default/files directory  
Restoring Drupal Database=> Importing backups with drush sql-cli  
Restoring Drupal Files=> Copying files back to the server  
Using Backup and Migrate Module=> Configuring automated backups with contrib module

15. Drupal Community and Resources

Exploring Drupal.org=> Navigating Drupal.org for modules and documentation  
Joining Drupal Community=> Participating in forums, Slack, or local events  
Understanding Drupal Coding Standards=> Learning basic standards for contributions  
Finding Drupal Documentation=> Accessing official guides and API references  
Following Drupal Updates=> Subscribing to security advisories and news
