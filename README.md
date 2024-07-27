# bankSystem-PHP
Bank System
## Description
This project is a banking system developed using PHP and SQL, adhering to the Model-View-Controller (MVC) architectural pattern. It provides functionalities such as user registration, login, and transaction management. The application is designed to handle concurrency using transactions, ensuring data integrity and consistency across simultaneous user interactions. Indexes are utilized to enhance the performance of database queries.

## Features
* User Registration and Login: Secure authentication system allowing users to register and log in to access their accounts.
* Transaction Management: Supports various banking transactions, ensuring all operations are atomic, consistent, isolated, and durable (ACID).
* Concurrency Handling: Implements SQL transactions to manage concurrency, ensuring that the database state is always consistent.
* Performance Optimization: Uses SQL indexes to speed up data retrieval processes, making the system efficient even with large volumes of data.
* Session Management: Leverages PHP sessions to maintain user state and session information securely.
* Responsive UI: The system uses a dynamic MVC architecture to separate concerns and enhance user experience.

## Technologies Used
* PHP: Server-side scripting language used for backend development.
* MySQL: Relational database management system used for storing all application data.
* HTML/CSS: Frontend technologies used for designing the user interface.
