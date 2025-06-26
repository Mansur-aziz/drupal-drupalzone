Drush and Console Commands: Step-by-Step Topics
1. Drush Setup and Basics

Installing Drush Globally=> Installing Drush via Composer for system-wide use  
Installing Drush Locally=> Adding Drush as a project dependency in Drupal  
Configuring Drush=> Setting up drush.yml for site aliases and settings  
Verifying Drush Installation=> Running drush --version to check installation  
Updating Drush=> Upgrading Drush to the latest version  
Setting Up Drush Aliases=> Defining site aliases for remote or local sites  
Running Drush Commands=> Executing basic commands via terminal

2. Core Drush Commands

Clearing Drupal Cache=> Using drush cache:rebuild to clear caches  
Managing Configuration=> Exporting/importing config with drush config:export/import  
Running Cron Jobs=> Triggering cron with drush core:cron  
Updating Drupal Core=> Using drush pm:up for core updates  
Managing Modules=> Enabling/disabling modules with drush pm:enable/disable  
Installing Modules=> Downloading modules with drush pm:install  
Updating Modules=> Updating contrib modules with drush pm:up  
Managing Themes=> Enabling/disabling themes with drush theme:enable/disable  
Running Database Updates=> Executing drush updatedb for schema updates  
Managing Users=> Creating users with drush user:create  
Resetting User Passwords=> Using drush user:password to reset passwords  
Blocking/Unblocking Users=> Managing user status with drush user:block/unblock  
Viewing Site Status=> Checking site details with drush core:status  
Executing SQL Queries=> Running queries with drush sql:query  
Dumping Database=> Exporting database with drush sql:dump  
Importing Database=> Importing database with drush sql-cli

3. Drush Migration Commands

Running Migration Discovery=> Using drush migrate:upgrade for Drupal 7 migrations  
Executing Migrations=> Running drush migrate:import for content migrations  
Rolling Back Migrations=> Using drush migrate:rollback to revert migrations  
Viewing Migration Status=> Checking status with drush migrate:status  
Debugging Migration Messages=> Inspecting errors with drush migrate:messages  
Resetting Migration Status=> Using drush migrate:reset-status for troubleshooting

4. Drush Queue and Batch Commands

Processing Queues=> Running queues with drush queue:run  
Listing Available Queues=> Viewing queues with drush queue:list  
Deleting Queue Items=> Clearing queues with drush queue:delete  
Running Batch Processes=> Triggering batches with drush core:batch-process  
Monitoring Batch Progress=> Checking batch status during execution

5. Custom Drush Commands

Creating a Custom Drush Command=> Defining commands in a .drush.yml or PHP file  
Annotating Drush Commands=> Using annotations for command metadata  
Defining Command Arguments=> Adding positional arguments to commands  
Defining Command Options=> Adding optional flags to commands  
Injecting Services in Commands=> Using dependency injection for services  
Handling Command Input/Output=> Using InputInterface and OutputInterface  
Validating Command Input=> Adding input validation logic  
Testing Custom Drush Commands=> Running and debugging custom commands  
Registering Commands in Modules=> Including commands in custom modules  
Altering Existing Drush Commands=> Using hook_drush_command_alter()

6. Drupal Console Setup and Basics

Installing Drupal Console=> Adding Drupal Console via Composer  
Configuring Drupal Console=> Setting up console.config.yml for preferences  
Verifying Drupal Console Installation=> Running drupal --version to check installation  
Updating Drupal Console=> Upgrading to the latest version  
Running Drupal Console Commands=> Executing commands via terminal

7. Core Drupal Console Commands

Generating Modules=> Creating modules with drupal generate:module  
Generating Content Types=> Using drupal generate:content-type for content types  
Generating Custom Entities=> Creating entities with drupal generate:entity:content  
Generating Forms=> Building forms with drupal generate:form  
Generating Controllers=> Creating controllers with drupal generate:controller  
Generating Plugins=> Using drupal generate:plugin:block for plugins  
Generating Services=> Creating services with drupal generate:service  
Generating Routes=> Defining routes with drupal generate:route  
Generating Theme Files=> Creating themes with drupal generate:theme  
Debugging Site Configuration=> Inspecting config with drupal debug:config  
Listing Available Commands=> Viewing commands with drupal list

8. Custom Drupal Console Commands

Creating a Custom Console Command=> Defining commands in a module’s Command directory  
Annotating Console Commands=> Using annotations for command metadata  
Defining Console Command Arguments=> Adding arguments to custom commands  
Defining Console Command Options=> Adding optional flags to commands  
Injecting Services in Console Commands=> Using dependency injection for services  
Handling Console Input/Output=> Using Symfony’s InputInterface and OutputInterface  
Validating Console Input=> Adding validation for command inputs  
Testing Custom Console Commands=> Running and debugging custom commands  
Registering Console Commands in Modules=> Including commands in custom modules  
Extending Existing Console Commands=> Using hook_console_command_alter()

9. Drush and Console Integration

Combining Drush and Console Workflows=> Using both tools in development processes  
Automating Tasks with Scripts=> Writing shell scripts to chain commands  
Using Drush with Console-Generated Code=> Managing generated modules with Drush  
Sharing Site Aliases=> Configuring aliases for both Drush and Console  
Debugging Command Conflicts=> Resolving issues between Drush and Console

10. Performance Optimization for Commands

Optimizing Drush Command Execution=> Reducing memory usage for large operations  
Caching Drush Command Output=> Using cache to speed up repetitive commands  
Optimizing Console Command Execution=> Streamlining generated code for performance  
Running Commands in Parallel=> Using tools like GNU Parallel for batch tasks  
Monitoring Command Performance=> Analyzing execution time with --debug

11. Drush and Console for Migration

Generating Migration Configurations=> Using Console to create migration YAML files  
Running Migration Commands with Drush=> Executing migrations with drush migrate:import  
Debugging Migration Commands=> Inspecting migration issues with Drush logs  
Automating Migration Workflows=> Scripting migration tasks with Drush and Console

12. Testing and Debugging Commands

Testing Drush Commands=> Writing PHPUnit tests for custom Drush commands  
Testing Console Commands=> Writing tests for custom Console commands  
Debugging Drush Commands=> Using --debug or --verbose for Drush output  
Debugging Console Commands=> Using Console’s debug mode for troubleshooting  
Logging Command Execution=> Capturing command output to log files

13. Deployment and Automation

Using Drush in CI/CD Pipelines=> Integrating Drush commands in GitHub Actions or Jenkins  
Using Console in CI/CD Pipelines=> Automating code generation in CI/CD  
Automating Site Deployment=> Scripting Drush commands for deployments  
Running Post-Deployment Commands=> Executing updatedb or config:import after deployment  
Managing Remote Sites with Drush=> Using site aliases for remote command execution

14. Documentation and Maintenance

Documenting Custom Drush Commands=> Adding help text and examples in annotations  
Documenting Custom Console Commands=> Including usage details in command metadata  
Maintaining Drush Configurations=> Updating drush.yml for new environments  
Maintaining Console Configurations=> Updating console.config.yml for new projects  
Monitoring Command Usage=> Tracking command execution for optimization
