Drupal APIs: Step-by-Step Topics
1. Form API
Basic
Creating a Basic Form=> Building a form class extending FormBase  
Defining Form Elements=> Adding fields (text, select, checkbox, etc.) to forms  
Validating Form Input=> Implementing validateForm() for input validation  
Handling Form Submission=> Processing data in submitForm()  
Advance
Adding AJAX to Forms=> Implementing AJAX callbacks with #ajax properties  
Altering Existing Forms=> Using hook_form_alter() to modify forms  
Creating Config Forms=> Building configuration forms with ConfigFormBase  
Rendering Forms Programmatically=> Using formBuilder service to render forms  
Managing Form States=> Using #states for dynamic form behavior  
Customizing Form Element Render=> Overriding form element templates

2. Cache API

Understanding Cache Bins=> Exploring Drupal’s cache bins (e.g., render, data)  
Adding Cache Tags=> Applying cache tags to render arrays  
Defining Cache Contexts=> Using cache contexts for dynamic caching  
Setting Cache Max-Age=> Controlling cache expiration with max-age  
Clearing Cache Programmatically=> Invalidating cache tags or bins with cache.invalidate  
Implementing Cacheable Dependencies=> Adding cache metadata to controllers or render arrays  
Using Cache Backends=> Configuring custom cache backends (e.g., Memcache, Redis)  
Debugging Cache Issues=> Using cache debugging tools to inspect cache metadata

3. Database API

Connecting to the Database=> Using Database::getConnection() for queries  
Writing Static Queries=> Executing simple SQL queries safely  
Building Dynamic Queries=> Using Select, Insert, Update, Delete query builders  
Adding Query Conditions=> Applying conditions (e.g., WHERE, AND, OR) to queries  
Using Query Joins=> Implementing joins in dynamic queries  
Managing Database Transactions=> Using transactions for data integrity  
Defining Custom Tables=> Creating tables with hook_schema()  
Altering Database Schema=> Modifying tables in hook_schema_alter()  
Querying with Entity Query API=> Using entity.query for entity-based queries

4. Entity API

Loading Entities=> Using entity_type.manager to load entities  
Creating Entities Programmatically=> Creating new entities with Entity::create()  
Updating Entities=> Modifying entity fields and saving changes  
Deleting Entities=> Removing entities with Entity::delete()  
Defining Custom Content Entities=> Creating entities with annotations and handlers  
Defining Entity Handlers=> Adding storage, access, and form handlers for entities  
Creating Entity Fields=> Adding fields to custom entities programmatically  
Implementing Entity Access=> Defining access control with hook_entity_access()  
Altering Entity Definitions=> Using hook_entity_type_alter() for customization  
Querying Entities=> Building entity queries with conditions and sorting

5. Token API

Using Core Tokens=> Accessing built-in tokens (e.g., [node:title], [user:name])  
Creating Custom Tokens=> Defining tokens with hook_token_info()  
Providing Token Values=> Implementing hook_tokens() for token replacements  
Replacing Tokens Programmatically=> Using token service to replace tokens  
Altering Token Data=> Using hook_tokens_alter() to modify token output  
Integrating Tokens in Forms=> Using tokens in form elements or defaults

6. Queue API

Creating a Queue=> Defining a queue with QueueFactory  
Adding Items to a Queue=> Enqueuing items with QueueInterface::createItem()  
Processing Queue Items=> Creating a queue worker plugin with QueueWorker  
Configuring Queue Execution=> Setting up queues for cron or manual processing  
Deleting Queue Items=> Removing items with QueueInterface::deleteItem()  
Managing Queue Plugins=> Defining queue plugins with annotations

7. REST API

Creating a REST Resource=> Defining a custom REST resource plugin  
Configuring REST Endpoints=> Setting up routes and methods in .routing.yml  
Handling REST Authentication=> Using OAuth, JWT, or cookie-based authentication  
Returning JSON Responses=> Formatting REST output with JsonResponse  
Supporting REST Formats=> Enabling XML or other formats for REST output  
Altering REST Resources=> Using hook_rest_resource_alter() for customization  
Using JSON:API=> Leveraging JSON:API for entity-based REST endpoints

8. Views API

Creating Custom Views Plugins=> Defining custom Views plugins (e.g., style, field)  
Creating Custom Views Handlers=> Building handlers for fields, filters, or sorts  
Altering Views Queries=> Using hook_views_query_alter() to modify queries  
Adding Views Data=> Defining custom data with hook_views_data()  
Altering Views Data=> Using hook_views_data_alter() for customization  
Creating Views Programmatically=> Generating Views configurations in code  
Theming Views Output=> Customizing Views templates via the Views API

9. Migration API

Writing Migration Scripts=> Creating migration YAML files in config/install  
Defining Source Plugins=> Building custom source plugins for migrations  
Defining Destination Plugins=> Creating custom destination plugins for entities  
Mapping Fields in Migrations=> Using process pipelines for field mapping  
Handling Migration Dependencies=> Specifying dependencies in migration YAML  
Running Migrations Programmatically=> Using migrate.executable service to run migrations  
Rolling Back Migrations=> Reverting migrations with Drush or code  
Altering Migrations=> Using hook_migration_plugins_alter() for customization

10. Batch API

Creating a Batch Process=> Defining batch operations with batch_set()  
Defining Batch Operations=> Writing callback functions for batch tasks  
Handling Batch Progress=> Displaying progress messages to users  
Finishing Batch Processes=> Implementing finished callback for completion  
Running Batches Programmatically=> Triggering batches outside of forms  
Altering Batch Operations=> Using hook_batch_alter() to modify batches

11. File API

Managing File Entities=> Creating and saving file entities with file_save()  
Uploading Files Programmatically=> Handling file uploads with file_managed  
Validating File Uploads=> Using file validators for size, type, etc.  
Moving Files to Destinations=> Using file_copy() or file_move()  
Creating Temporary Files=> Managing temporary files with file_save_data()  
Accessing File Metadata=> Retrieving file properties (e.g., URI, MIME type)

12. Mail API

Sending Emails=> Using mail.manager to send emails  
Defining Custom Mail Plugins=> Creating mail plugins with MailInterface  
Formatting Email Content=> Building email bodies and subjects  
Altering Email Content=> Using hook_mail_alter() to modify emails  
Configuring Mail Backends=> Setting up SMTP or other mail systems

13. Ajax API

Implementing AJAX Callbacks=> Adding #ajax properties to form elements  
Returning AJAX Commands=> Using AjaxResponse with commands (e.g., ReplaceCommand)  
Handling AJAX Errors=> Managing errors in AJAX responses  
Triggering AJAX Programmatically=> Using Drupal.ajax for custom AJAX calls  
Altering AJAX Requests=> Using hook_ajax_render_alter() for customization

14. User API

Loading User Entities=> Using entity_type.manager to load users  
Creating Users Programmatically=> Creating user accounts with User::create()  
Managing User Roles=> Adding or removing roles with user_role_grant_permissions()  
Checking User Permissions=> Using user_access() or AccessResult for access checks  
Altering User Data=> Using hook_user_load() or hook_user_presave()  
Handling User Login/Logout=> Triggering login/logout programmatically

15. Menu API

Defining Menu Links=> Creating links in .links.menu.yml  
Defining Local Tasks=> Adding tabs with .links.task.yml  
Defining Action Links=> Creating action links in .links.action.yml  
Altering Menu Links=> Using hook_menu_links_discovered_alter()  
Managing Menu Trees=> Using menu.tree service to load menus  
Rendering Menus Programmatically=> Rendering menu items in code or templates

16. Block API

Creating Custom Blocks=> Defining block plugins with BlockBase  
Configuring Block Settings=> Adding configuration forms to blocks  
Rendering Blocks Programmatically=> Using block.repository to render blocks  
Altering Block Output=> Using hook_block_view_alter() for customization

17. Theme API

Defining Theme Hooks=> Creating custom hooks with hook_theme()  
Adding Theme Suggestions=> Using hook_theme_suggestions_HOOK()  
Preprocessing Theme Variables=> Using hook_preprocess_HOOK() for template data  
Altering Theme Output=> Using hook_preprocess() to modify render arrays  
Rendering Theme Elements=> Using theme() function for custom rendering

18. Plugin API

Understanding Plugin System=> Exploring Drupal’s plugin architecture  
Creating Custom Plugin Types=> Defining new plugin types with annotations  
Implementing Plugin Annotations=> Adding metadata for plugins  
Using Core Plugins=> Leveraging existing plugins (e.g., field, block)  
Altering Plugin Definitions=> Using hook_plugin_definition_alter()

19. Logging API

Logging Messages=> Using logger.factory for custom logs  
Defining Custom Log Channels=> Creating module-specific log channels  
Integrating with Watchdog=> Logging to Drupal’s watchdog system  
Altering Log Messages=> Using hook_watchdog() to modify logs

20. Config API

Reading Configuration=> Using config.factory to retrieve config data  
Saving Configuration=> Storing data with Config::save()  
Defining Config Schema=> Creating .schema.yml for custom configuration  
Providing Default Configuration=> Including config in config/install  
Altering Configuration=> Using hook_config_schema_info_alter()

21. Translation API

Translating Strings=> Using t() for translatable strings  
Creating Translation Files=> Generating .po files for translations  
Managing Config Translations=> Translating configuration with config.translation  
Altering Translations=> Using hook_string_translation_alter()

22. Path API

Creating Path Aliases=> Using path_alias.manager to create aliases  
Loading Path Aliases=> Retrieving aliases with path_alias.repository  
Altering Path Aliases=> Using hook_path_alias_alter()  
Generating URLs from Paths=> Using Url::fromRoute() or path()

23. Field API

Creating Custom Field Types=> Defining field types with FieldType plugins  
Creating Field Widgets=> Building custom widgets with WidgetBase  
Creating Field Formatters=> Defining formatters with FormatterBase  
Accessing Field Data=> Using entity.field to retrieve field values  
Altering Field Definitions=> Using hook_field_info_alter()

24. Event API

Subscribing to Core Events=> Listening to events with EventSubscriberInterface  
Creating Custom Events=> Defining custom event classes  
Dispatching Events=> Using event_dispatcher to trigger events  
Setting Event Priorities=> Configuring priorities for event subscribers
