## Weather App overview
Introduction to Drupal custom modules (Weather App overview)
Creating a basic custom module (weather_app)
Displaying "Hello World" from a custom controller
Adding routes and menu links
Creating a configuration form (API key input)
Using the Form API (city input form)
Fetching weather data from an external API
Creating a custom service for API handling
Displaying API data on a custom page
Building a custom block to show weather
Adding permissions and access control
Theming output with Twig templates
Caching API responses in Drupal
Logging and debugging with Drupal logger
Translating strings for multilingual support
Exporting configuration for deployment
Packaging and sharing the custom module

## Book Review overview
Introduction to Drupal custom modules (Book Review overview)
Creating the basic book_review module
Showing "Hello World" on a custom page
Defining routes and creating a controller
Creating a custom content entity: Book Review
Adding fields to the custom entity programmatically
Creating a custom permissions system
Building an admin settings form
Using Form API to submit book reviews
Saving data to the custom entity
Listing all book reviews on a custom page
Creating a view programmatically for reviews
Adding user access restrictions (only reviewers can submit)
Displaying reviews with Twig templates
Creating a custom block to show latest reviews
Writing a service for calculating average ratings
Using dependency injection in services and controllers
Caching entity queries and rendered output
Implementing hooks (hook_entity_insert, hook_form_alter)
Creating custom validation for the review form
Adding a tab or local task (e.g. "My Reviews")
Creating custom menu items in the admin toolbar
Logging user activity (when reviews are submitted or edited)
Creating a custom REST endpoint for fetching reviews
Making a Drush command to bulk publish pending reviews
Translating book review fields and messages
Creating kernel tests for your module
Adding configuration schema for your settings
Packaging the module with README and proper naming
Submitting your custom module to Drupal.org (optional)



## 
📚 Drupal Custom Module Topics – Full Learning Path
🔰 Beginner Level
Introduction to Drupal custom modules
Creating your first custom module (.info.yml, .module)
Routing system and creating custom pages
Creating controllers to return output
Using Form API to build custom forms
Module configuration using ConfigFormBase
Adding menu links and local tasks
Setting permissions and controlling access
Implementing basic hooks (hook_help, hook_menu, hook_form_alter)

⚙️ Intermediate Level
Custom Block => Creating custom blocks (Block Plugin API)
Service & Dependency Injection => Using services and dependency injection
Custom Service => Creating a custom service
Custom Events and Subscribers => Creating and using custom events and subscribers
Drupal logger => Logging messages using Drupal logger
Custom Configuration => Working with custom configuration (*.schema.yml)
Custom Tokens => Creating custom tokens
Creating Custom Drush Command => Creating simple Drush commands

🏗️ Entity and Field Level
Creating custom content entities
Adding base fields and field UI support
Creating entity forms (add/edit/delete)
Creating view builders and access handlers
Creating entity routes and links
Creating custom views data for your entity

🧩 Advanced Features
Writing automated tests (unit, kernel, functional)
Creating custom RESTful endpoints
Integrating with JSON:API or GraphQL
Creating background tasks with batch API
Creating scheduled tasks with cron
Making your module multilingual
Using plugin systems (FieldFormatter, FieldWidget, FieldType)
Altering Views, forms, entities, and render arrays

📦 Finishing Touches
Exporting/importing configuration (Config management)
Writing a module README and documentation
Packaging your module for distribution
Following Drupal coding standards and best practices
Contributing your module to Drupal.org